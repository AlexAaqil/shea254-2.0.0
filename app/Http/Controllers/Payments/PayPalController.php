<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Products\Product;
use App\Models\Sales\Sale;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\Http;
use Exception;
use Throwable;

class PayPalController extends Controller
{
    protected $client_id;
    protected $client_secret;
    protected $mode;
    protected $base_url;
    protected $logger;
    protected $inventory_logger;

    public function __construct()
    {
        // PayPal Credentials
        $this->client_id = env('PAYPAL_CLIENT_ID');
        $this->client_secret = env('PAYPAL_CLIENT_SECRET');
        $this->mode = env('PAYPAL_MODE', 'sandbox');

        // Set base URL based on mode (sandbox or live)
        $this->base_url = $this->mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $this->logger = Log::channel('paypal');
        $this->inventory_logger = Log::channel('inventory_management');

        $this->logger->info('PayPalController initialized', [
            'mode' => $this->mode,
            'base_url' => $this->base_url,
        ]);
    }

    /**
     * Get PayPal access token
     */
    private function getAccessToken()
    {
        try {
            $response = Http::withBasicAuth($this->client_id, $this->client_secret)
                ->asForm()
                ->post("{$this->base_url}/v1/oauth/token", [
                    'grant_type' => 'client_credentials',
                ]);

            $this->logger->info('Access token response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->failed()) {
                throw new Exception('Failed to get PayPal access token: ' . $response->body());
            }

            return $response->json()['access_token'];
        } catch (Throwable $e) {
            $this->logger->error('Access token error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Initialize PayPal payment
     */
    public function initializePayment($order, $total_amount)
    {
        try {
            $access_token = $this->getAccessToken();

            // Format amount (PayPal uses decimal with 2 places)
            $amount = number_format($total_amount, 2, '.', '');

            // Prepare order data for PayPal
            $order_data = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $order->order_number,
                        'description' => 'Order #' . $order->order_number,
                        'amount' => [
                            'currency_code' => 'USD', // or your currency
                            'value' => $amount,
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => 'USD',
                                    'value' => $amount
                                ]
                            ]
                        ],
                        'items' => $this->formatOrderItems($order)
                    ]
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'landing_page' => 'BILLING',
                    'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('paypal.capture'),
                    'cancel_url' => route('paypal.cancel'),
                ]
            ];

            $this->logger->info('Initializing PayPal payment', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $amount,
                'payload' => $order_data
            ]);

            // Create order in PayPal
            $response = Http::withToken($access_token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->base_url}/v2/checkout/orders", $order_data);

            $response_data = $response->json();

            $this->logger->info('PayPal order creation response', [
                'status' => $response->status(),
                'response' => $response_data
            ]);

            // Check if order was created successfully
            if ($response->successful() && isset($response_data['id'])) {
                // Store payment record
                $this->createPaymentRecord($order, $response_data);

                // Find approval URL and redirect user
                foreach ($response_data['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }

                throw new Exception('No approval link found in PayPal response');
            }

            $this->logger->error('PayPal order creation failed', [
                'response' => $response_data
            ]);

            session()->flash('notify', [
                'message' => 'Payment initialization failed. Please try again.',
                'type' => 'error'
            ]);

            return redirect()->route('checkout-page');

        } catch (Throwable $e) {
            $this->logger->error('PayPal initialization exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('notify', [
                'message' => 'Payment system error. Please try again.',
                'type' => 'error'
            ]);

            return redirect()->route('checkout-page');
        }
    }

    /**
     * Handle PayPal capture (callback after user approves)
     */
    public function capturePayment(Request $request)
    {
        $token = $request->token; // PayPal returns order ID in 'token' parameter

        if (!$token) {
            $this->logger->error('No token in capture request');
            return redirect()->route('checkout-page')->with('error', 'Invalid payment session');
        }

        try {
            $access_token = $this->getAccessToken();

            $this->logger->info('Capturing PayPal payment', [
                'order_id' => $token
            ]);

            // Capture the payment
            $response = Http::withToken($access_token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->base_url}/v2/checkout/orders/{$token}/capture");

            $capture_data = $response->json();

            $this->logger->info('PayPal capture response', [
                'status' => $response->status(),
                'data' => $capture_data
            ]);

            if (!$response->successful()) {
                throw new Exception('Payment capture failed: ' . ($capture_data['message'] ?? 'Unknown error'));
            }

            // Find payment record by PayPal order ID
            $payment = Payment::where('merchant_request_id', $token)->first();

            if (!$payment) {
                $this->logger->error('Payment not found for PayPal order', ['token' => $token]);
                return redirect()->route('checkout-page')->with('error', 'Payment record not found');
            }

            $order = Sale::find($payment->order_id);

            if (!$order) {
                $this->logger->error('Order not found for payment', ['payment_id' => $payment->id]);
                return redirect()->route('checkout-page')->with('error', 'Order not found');
            }

            // Check payment status
            if ($capture_data['status'] === 'COMPLETED') {
                return $this->processSuccessfulPayment($order, $payment, $capture_data);
            } else {
                return $this->processFailedPayment($order, $payment, $capture_data);
            }

        } catch (Throwable $e) {
            $this->logger->error('Capture handling failed', [
                'token' => $token,
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
     * Handle PayPal webhook (for asynchronous updates)
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            
            // Verify webhook signature (optional but recommended)
            if (!$this->verifyWebhookSignature($request)) {
                $this->logger->warning('Invalid PayPal webhook signature');
                return response()->json(['status' => 'error'], 401);
            }

            $event = json_decode($payload, true);
            
            $this->logger->info('PayPal webhook received', [
                'event_type' => $event['event_type'] ?? 'unknown'
            ]);

            // Handle payment capture completed
            if (($event['event_type'] ?? '') === 'PAYMENT.CAPTURE.COMPLETED') {
                $resource = $event['resource'];
                $paypal_order_id = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

                if ($paypal_order_id) {
                    $payment = Payment::where('merchant_request_id', $paypal_order_id)->first();
                    
                    if ($payment && $payment->status !== 'paid') {
                        $order = Sale::find($payment->order_id);
                        
                        if ($order) {
                            $this->processSuccessfulPayment($order, $payment, $resource);
                        }
                    }
                }
            }

            return response()->json(['status' => 'success'], 200);

        } catch (Throwable $e) {
            $this->logger->error('PayPal webhook handling failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Handle PayPal cancel (user cancels payment)
     */
    public function cancelPayment(Request $request)
    {
        $this->logger->info('PayPal payment cancelled', [
            'token' => $request->token
        ]);

        session()->flash('notify', [
            'message' => 'You have cancelled the PayPal payment. Your order has not been processed.',
            'type' => 'info'
        ]);

        return redirect()->route('checkout-page');
    }

    /**
     * Process successful payment
     */
    private function processSuccessfulPayment($order, $payment, $capture_data)
    {
        DB::transaction(function () use ($order, $payment, $capture_data) {
            // Extract capture details
            $capture = $capture_data['purchase_units'][0]['payments']['captures'][0] ?? $capture_data;

            // Update payment record
            $payment->update([
                'status' => 'paid',
                'transaction_reference' => $capture['id'] ?? $payment->transaction_reference,
                'response_code' => $capture['status'] ?? 'COMPLETED',
                'response_description' => json_encode([
                    'paypal_order_id' => $capture_data['id'] ?? null,
                    'capture_id' => $capture['id'] ?? null,
                    'amount' => $capture['amount']['value'] ?? null,
                    'currency' => $capture['amount']['currency_code'] ?? 'USD',
                    'create_time' => $capture['create_time'] ?? null,
                    'update_time' => $capture['update_time'] ?? null,
                    'final_capture' => $capture['final_capture'] ?? true,
                    'full_response' => $capture_data,
                ]),
                'customer_message' => 'Payment completed successfully via PayPal',
            ]);

            // Update order
            $order->update([
                'status' => 'paid',
                'amount_paid' => $capture['amount']['value'] ?? $order->total_amount,
            ]);

            // Decrement product stock (similar to Paystack/M-Pesa)
            if ($order->order_items->isNotEmpty()) {
                foreach ($order->order_items as $item) {
                    $updated = Product::where('id', $item->product_id)
                        ->where('stock_count', '>=', $item->quantity)
                        ->decrement('stock_count', $item->quantity);

                    if ($updated) {
                        $this->inventory_logger->info('Stock decremented', [
                            'product_id' => $item->product_id,
                            'qty' => $item->quantity,
                            'order_id' => $order->id,
                            'payment_id' => $payment->id
                        ]);
                    } else {
                        $this->inventory_logger->error('Stock deduction failed', [
                            'product_id' => $item->product_id,
                            'qty' => $item->quantity,
                            'order_id' => $order->id,
                            'payment_id' => $payment->id
                        ]);
                        throw new Exception("Insufficient stock for product ID: {$item->product_id}");
                    }
                }
            }
        });

        // Clear cart session
        session()->forget(['cart', 'cart_count']);
        
        // Store order number for success page
        session()->put('order_number', $order->order_number);

        $this->logger->info('PayPal payment successful', [
            'order_number' => $order->order_number,
            'order_id' => $order->id
        ]);

        session()->flash('notify', [
            'message' => "Payment successful! Your order {$order->order_number} has been confirmed.",
            'type' => 'success'
        ]);

        return redirect()->route('checkout.success');
    }

    /**
     * Process failed payment
     */
    private function processFailedPayment($order, $payment, $capture_data)
    {
        $payment->update([
            'status' => 'failed',
            'response_code' => $capture_data['status'] ?? 'FAILED',
            'response_description' => json_encode($capture_data),
            'customer_message' => 'Payment failed: ' . ($capture_data['message'] ?? 'Unknown error'),
        ]);

        $this->logger->warning('PayPal payment failed', [
            'order_number' => $order->order_number,
            'reason' => $capture_data['message'] ?? 'Unknown'
        ]);

        session()->flash('notify', [
            'message' => "Payment failed. Please try again or choose another payment method.",
            'type' => 'error'
        ]);

        return redirect()->route('checkout-page');
    }

    /**
     * Create initial payment record
     */
    private function createPaymentRecord($order, $paypal_response)
    {
        return $order->payment()->create([
            'payment_gateway' => 'paypal',
            'merchant_request_id' => $paypal_response['id'], // PayPal order ID
            'checkout_request_id' => 'PAYPAL_' . $paypal_response['id'],
            'transaction_reference' => $paypal_response['id'],
            'response_code' => $paypal_response['status'],
            'response_description' => json_encode($paypal_response),
            'status' => 'pending',
            'order_id' => $order->id,
            'customer_message' => 'PayPal payment initiated',
        ]);
    }

    /**
     * Format order items for PayPal
     */
    private function formatOrderItems($order)
    {
        $items = [];
        
        foreach ($order->order_items as $item) {
            $items[] = [
                'name' => $item->title,
                'unit_amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($item->selling_price, 2, '.', '')
                ],
                'quantity' => $item->quantity,
                'description' => 'Product: ' . $item->title,
                'sku' => 'PROD-' . $item->product_id,
                'category' => 'PHYSICAL_GOODS'
            ];
        }

        return $items;
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhookSignature(Request $request)
    {
        // PayPal webhook verification is more complex
        // For simplicity, we'll skip for now
        // In production, implement proper signature verification
        return true;
    }
}
