<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Throwable;

class PaystackController extends Controller
{
    protected $secret_key;
    protected $base_url;
    protected $logger;

    public function __construct()
    {
        $this->secret_key = env('PAYSTACK_SECRET_KEY');
        $this->base_url = env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co');
        $this->logger = Log::channel('paystack');
    }

    public function initializeTransaction($order, $email, $amount)
    {
        try {
            $amount_kobo = (int)($amount * 100);

            $reference = $this->generateReference($order->order_number);

            $data = [
                'amount' => $amount_kobo,
                'email' => $email,
                'reference' => $reference,
                'currency' => 'KES',
                'callback_url' => route('paystack.callback'),
                'metadata' => json_encode([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->order_delivery->full_name,
                    'phone_number' => $order->order_delivery->phone_number,
                ]),
            ];

            $this->logger->info('Initializing Paystack transaction', [
                'order_id' => $order->id,
                'reference' => $reference,
            ]);

            // Make the API request to Paystack
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secret_key,
                'content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->base_url . '/transaction/initialize', $data);

            $response_data = $response->json();

            $this->logger->info('Paystack initialization response', [
                'response' => $response_data
            ]);

            // Check if request was successful
            if ($response->successful() && $response_data['status']) {
                // Store payment record
                $this->createPaymentRecord($order, $reference, $response_data);

                return Redirect::away($response_data['data']['authorization_url']);
            }

            $this->logger->error('Paystack initialization failed', [
                'response' => $response_data
            ]);

            session()->flash('notify', ['message' => 'Payment initialization failed. Please try again.', 'type' => 'error']);

            return redirect()->route('checkout-page');
        } catch (Throwable $e) {
            $this->logger->error('Paystack initialization exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('notify', ['message' => 'Payment system error. Please try again.', 'type' => 'error']);

            return redirect()->route('checkout-page');
        }
    }

    /**
     * Handle Paystack callback
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');
        
        if (!$reference) {
            $this->logger->error('No reference in callback');
            return redirect()->route('checkout-page')->with('error', 'Invalid payment reference');
        }

        try {
            $this->logger->info('Processing callback for reference: ' . $reference);

            // Verify transaction with Paystack
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secret_key,
                'Content-Type' => 'application/json',
            ])->get($this->base_url . '/transaction/verify/' . $reference);

            $paymentData = $response->json();

            $this->logger->info('Payment verification response', [
                'reference' => $reference,
                'status' => $paymentData['status'] ?? false,
                'data' => $paymentData['data'] ?? null
            ]);

            if (!$response->successful() || !$paymentData['status']) {
                throw new \Exception('Payment verification failed: ' . ($paymentData['message'] ?? 'Unknown error'));
            }

            $data = $paymentData['data'];
            
            // Find the payment record
            $payment = Payment::where('transaction_reference', $reference)->first();
            
            if (!$payment) {
                $this->logger->error('Payment not found for reference: ' . $reference);
                return redirect()->route('checkout-page')->with('error', 'Payment record not found');
            }

            $order = Sale::find($payment->order_id);

            if (!$order) {
                $this->logger->error('Order not found for payment: ' . $payment->id);
                return redirect()->route('checkout-page')->with('error', 'Order not found');
            }

            // Check if already processed
            if ($payment->status === 'paid') {
                $this->logger->warning('Payment already processed: ' . $reference);
                return $this->redirectBasedOnStatus($order, 'success');
            }

            // Process based on payment status
            if ($data['status'] === 'success') {
                return $this->processSuccessfulPayment($order, $payment, $data);
            } else {
                return $this->processFailedPayment($order, $payment, $data);
            }

        } catch (Throwable $e) {
            $this->logger->error('Callback handling failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            session()->flash('notify', [
                'message' => 'Payment verification failed. Please contact support.',
                'type' => 'error'
            ]);

            return redirect()->route('shop-page');
        }
    }

    /**
     * Handle Paystack webhook
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Get the payload
            $payload = $request->getContent();
            
            // Verify webhook signature (optional but recommended)
            if (!$this->verifyWebhookSignature($request)) {
                $this->logger->warning('Invalid webhook signature');
                return response()->json(['status' => 'error'], 401);
            }

            $event = json_decode($payload, true);
            
            $this->logger->info('Webhook received', [
                'event' => $event['event'] ?? 'unknown'
            ]);

            // Handle charge.success event
            if (($event['event'] ?? '') === 'charge.success') {
                $data = $event['data'];
                $reference = $data['reference'] ?? null;

                if ($reference) {
                    $payment = Payment::where('transaction_reference', $reference)->first();
                    
                    if ($payment && $payment->status !== 'paid') {
                        $order = Sale::find($payment->order_id);
                        
                        if ($order) {
                            $this->processSuccessfulPayment($order, $payment, $data);
                        }
                    }
                }
            }

            return response()->json(['status' => 'success'], 200);

        } catch (Throwable $e) {
            $this->logger->error('Webhook handling failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Process successful payment
     */
    private function processSuccessfulPayment($order, $payment, $data)
    {
        // Update payment record
        $payment->update([
            'status' => 'paid',
            'response_code' => $data['gateway_response'] ?? '00',
            'response_description' => json_encode([
                'amount' => $data['amount'] / 100,
                'currency' => $data['currency'],
                'transaction_date' => $data['transaction_date'] ?? now(),
                'payment_method' => 'paystack',
                'authorization' => $data['authorization'] ?? null,
                'full_response' => $data
            ]),
            'customer_message' => 'Payment completed successfully',
        ]);

        // Update order
        $order->update([
            'status' => 'paid',
            'amount_paid' => $data['amount'] / 100,
        ]);

        // Clear cart session
        session()->forget(['cart', 'cart_count']);
        
        // Store order number in session for success page
        session()->put('order_number', $order->order_number);

        $this->logger->info('Payment successful', [
            'order_number' => $order->order_number,
            'reference' => $data['reference']
        ]);

        return $this->redirectBasedOnStatus($order, 'success');
    }

    /**
     * Process failed payment
     */
    private function processFailedPayment($order, $payment, $data)
    {
        $payment->update([
            'status' => 'failed',
            'response_code' => '99',
            'response_description' => $data['gateway_response'] ?? 'Payment failed',
            'customer_message' => 'Payment failed: ' . ($data['gateway_response'] ?? 'Unknown error'),
        ]);

        $this->logger->warning('Payment failed', [
            'order_number' => $order->order_number,
            'reference' => $data['reference'],
            'reason' => $data['gateway_response'] ?? 'Unknown'
        ]);

        return $this->redirectBasedOnStatus($order, 'failed');
    }

    /**
     * Create initial payment record
     */
    private function createPaymentRecord($order, $reference, $responseData)
    {
        return $order->payment()->create([
            'payment_gateway' => 'paystack',
            'merchant_request_id' => $responseData['data']['reference'] ?? null,
            'checkout_request_id' => null,
            'transaction_reference' => $reference,
            'response_code' => $responseData['data']['reference'] ?? null,
            'response_description' => json_encode($responseData),
            'status' => 'pending',
            'order_id' => $order->id,
            'customer_message' => $responseData['message'] ?? 'Payment initialization started',
        ]);
    }

    /**
     * Generate unique reference for transaction
     */
    private function generateReference($orderNumber)
    {
        return 'PS_' . $orderNumber . '_' . time() . '_' . uniqid();
    }

    /**
     * Verify webhook signature (optional but recommended)
     */
    private function verifyWebhookSignature(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        
        if (!$signature) {
            return false;
        }

        $payload = $request->getContent();
        $secret = env('PAYSTACK_SECRET_KEY');
        $computedSignature = hash_hmac('sha512', $payload, $secret);
        
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Redirect based on payment status
     */
    private function redirectBasedOnStatus($order, $status)
    {
        if ($status === 'success') {
            session()->flash('notify', [
                'message' => "Payment successful! Your order {$order->order_number} has been confirmed.",
                'type' => 'success'
            ]);
            return redirect()->route('checkout.success');
        } else {
            session()->flash('notify', [
                'message' => "Payment failed. Please try again or contact support.",
                'type' => 'error'
            ]);
            return redirect()->route('checkout-page');
        }
    }
}
