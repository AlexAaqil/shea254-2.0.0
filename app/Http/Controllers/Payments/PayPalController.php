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
use App\Services\CurrencyExchangeService;
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
    protected $exchange_service;

    public function __construct(CurrencyExchangeService $exchange_service)
    {
        // PayPal Credentials
        $this->client_id = env('PAYPAL_CLIENT_ID');
        $this->client_secret = env('PAYPAL_CLIENT_SECRET');
        $this->mode = env('PAYPAL_MODE', 'sandbox');

        // Set base URL based on mode (sandbox or live)
        $this->base_url = $this->mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $this->logger = Log::channel('paypal');
        $this->inventory_logger = Log::channel('inventory_management');

        $this->exchange_service = $exchange_service;

        $this->logger->info('PayPalController initialized', [
            'mode' => $this->mode,
            'base_url' => $this->base_url
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
                ->post("{$this->base_url}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            $this->logger->info('Access token response', [
                'status' => $response->status(),
                // 'body' => $response->json(),
                // 'headers' => $response->headers(),
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
    public function initializePayment($order, $total_amount_kes)
    {
        try {
            // Generate unique transaction ID for rate locking
            $transactionId = $order->order_number . '_' . uniqid();
            
            // Get locked rate for this transaction
            $conversionData = $this->exchange_service->convertForTransaction($transactionId, $total_amount_kes);

            $this->logger->info('Currency conversion for transaction', $conversionData);

            $access_token = $this->getAccessToken();

            // Format items with PayPal currency USING THE LOCKED RATE
            $items = $this->formatOrderItems($order, 'USD', $conversionData['rate_used']);

            // Calculate totals from items
            $item_total = 0;
            foreach ($items as $item) {
                $item_total += (float)$item['unit_amount']['value'] * $item['quantity'];
            }

            // Calculate shipping cost in USD
            $shipping_cost_kes = $order->shipping_cost ?? 0;
            $shipping_cost_usd = $this->formatAmountForPayPal($shipping_cost_kes * $conversionData['rate_used'], 'USD');

            // Calculate total amount (items + shipping)
            $total_amount_usd = $item_total + (float)$shipping_cost_usd;

            // Handle rounding mismatches without touching shipping
            $difference = $total_amount_usd - $conversionData['usd_amount'];

            // Verify total matches converson data
            if (abs($difference) > 0.01) {
                // Large mismatch - Log warning but use conversion total as source of truth
                $this->logger->warning('Total mismatch detected', [
                    'item_total' => $item_total,
                    'shipping_usd' => $shipping_cost_usd,
                    'calculated_total' => $total_amount_usd,
                    'expected_total' => $conversionData['usd_amount'],
                    'difference' => $difference,
                    'order_number' => $order->order_number,
                ]);

                // Use calculated total as source of truth
                $total_amount_usd = $conversionData['usd_amount'];

                // Recalculate shipping to absort any rounding differences
                // $shipping_cost_usd = $this->formatAmountForPayPal($total_amount_usd - $item_total, 'USD');
            }

            $shipping_address = $this->formatShippingAddress($order->order_delivery);

            // Prepare order data for PayPal
            $order_data = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $order->order_number,
                        'description' => "Order #{$order->order_number}",
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => sprintf('%.2f', $total_amount_usd),
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => 'USD',
                                    'value' => sprintf('%.2f', $item_total)
                                ],
                                // Add shipping and tax breakdown
                                // 'shipping' => [...],
                                // 'tax_total' => [...]
                                'shipping' => [
                                    'currency_code' => 'USD',
                                    'value' => sprintf('%.2f', $shipping_cost_usd)
                                ]
                            ]
                        ],
                        
                        // Store ALL conversion data in custom_id for audit
                        'custom_id' => json_encode([
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'kes' => $conversionData['kes_amount'],
                            'usd' => $conversionData['usd_amount'],
                            'rate' => $conversionData['rate_used'],
                            'rate_source' => $conversionData['rate_source'],
                            'rate_timestamp' => $conversionData['rate_timestamp']->format('d-m-Y H:i:s'),
                            'transaction_id' => $conversionData['transaction_id'],
                        ]),

                        'items' => $items,
                        'shipping' => $this->formatShippingAddress($order->order_delivery)
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

            // Log of the breakdown for debugging
            $this->logger->info('PayPal amount breakdown', [
                'order_number' => $order->order_number,
                'item_total_usd' => $item_total,
                'shipping_usd' => $shipping_cost_usd,
                'total_usd' => $total_amount_usd,
                'total_kes' => $total_amount_kes,
                'shipping_kes' => $shipping_cost_kes
            ]);

            $this->logger->info('Initializing PayPal payment', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount_usd' => $total_amount_usd,
                'amount_kes' => $total_amount_kes,
            ]);

            // Create order in PayPal
            $response = Http::withToken($access_token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->base_url}/v2/checkout/orders", $order_data);

            $response_data = $response->json();

            // Check if order was created successfully
            if ($response->successful() && isset($response_data['id'])) {
                // Store payment record
                $this->createPaymentRecord($order, $response_data, $conversionData);

                // Find approval URL and redirect user
                foreach ($response_data['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }

                throw new Exception('No approval link found in PayPal response');
            }

            // Error logging for debugging
            $this->logger->error('PayPal order creation failed', [
                'status' => $response->status(),
                'response' => $response_data,
                'request_data' => [
                    'total_usd' => $total_amount_usd,
                    'item_total' => $item_total,
                    'shipping_usd' => $shipping_cost_usd,
                    'items_count' => count($items)
                ]
            ]);

            session()->flash('notify', [
                'message' => 'Payment initialization failed. Please try again.',
                'type' => 'error'
            ]);

            return redirect()->route('checkout-page');

        } catch (Throwable $e) {
            $this->logger->error('PayPal initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order' => $order->order_number
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
        $payerId = $request->PayerID; // PayPal also returns PayerID

        $this->logger->info('PayPal capture callback received', [
            'token' => $token,
            'payer_id' => $payerId,
            'all_params' => $request->all()
        ]);

        if (!$token) {
            $this->logger->error('No token in capture request');
            session()->flash('notify', [
                'message' => 'Invalid payment session. Please try again.',
                'type' => 'error'
            ]);
            return redirect()->route('checkout-page');
        }

        try {
            $access_token = $this->getAccessToken();

            $this->logger->info('Capturing PayPal payment', [
                'order_id' => $token
            ]);

            // Send an empty JSON object in the body
            $response = Http::withToken($access_token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->send('POST', "{$this->base_url}/v2/checkout/orders/{$token}/capture", [
                    'body' => '{}'
            ]);

            $capture_data = $response->json();

            $this->logger->info('PayPal capture response', [
                'status' => $response->status(),
                'data' => $capture_data
            ]);

            if (!$response->successful()) {
                $this->logger->error('PayPal capture failed', [
                    'status' => $response->status(),
                    'data' => $capture_data
                ]);
                
                session()->flash('notify', [
                    'message' => 'Payment capture failed: ' . ($capture_data['message'] ?? 'Unknown error'),
                    'type' => 'error'
                ]);
                return redirect()->route('checkout-page');
            }

            // Find payment record by PayPal order ID
            $payment = Payment::where('merchant_request_id', $token)->first();

            if (!$payment) {
                $this->logger->error('Payment not found for PayPal order', ['token' => $token]);
                
                // Try to find by transaction_reference as fallback
                $payment = Payment::where('transaction_reference', $token)->first();
                
                if (!$payment) {
                    session()->flash('notify', [
                        'message' => 'Payment record not found. Please contact support.',
                        'type' => 'error'
                    ]);
                    return redirect()->route('shop-page');
                }
            }

            $order = Sale::find($payment->order_id);

            if (!$order) {
                $this->logger->error('Order not found for payment', ['payment_id' => $payment->id]);
                session()->flash('notify', [
                    'message' => 'Order not found. Please contact support.',
                    'type' => 'error'
                ]);
                return redirect()->route('shop-page');
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
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

            // Extract custom_id data if available
            $custom_data = [];
            if (isset($capture_data['purchase_units'][0]['custom_id'])) {
                $custom_data = json_decode($capture_data['purchase_units'][0]['custom_id'], true);
            }

            // Update payment record
            $payment->update([
                'status' => 'paid',
                'transaction_reference' => $capture['id'] ?? $payment->transaction_reference,
                'response_code' => $capture['status'] ?? 'COMPLETED',
                'response_description' => json_encode([
                    'paypal_order_id' => $capture_data['id'] ?? null,
                    'capture_id' => $capture['id'] ?? null,
                    'usd_amount' => $capture['amount']['value'] ?? null,
                    'kes_amount' => $custom_data['kes_amount'] ?? $order->total_amount,
                    'exchange_rate' => $custom_data['exchange_rate'] ?? null,
                    'currency' => $capture['amount']['currency_code'] ?? 'USD',
                    'create_time' => $capture['create_time'] ?? null,
                    'update_time' => $capture['update_time'] ?? null,
                    'final_capture' => $capture['final_capture'] ?? true,
                    'full_response' => $capture_data,
                ]),
                'customer_message' => 'Payment completed successfully via PayPal',
            ]);

            // Update order - KES amount remains unchanged
            $order->update([
                'status' => 'paid',
                // 'amount_paid' already has KES amount from order creation
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

        // 🔥 FIX: Clear stored form data on successful payment
        session()->forget('checkout_form_data');
        session()->forget('checkout_selected_location');
        session()->forget('checkout_selected_area');
        session()->forget('pending_paystack_order_id');
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
    private function createPaymentRecord($order, $paypal_response, $conversionData)
    {
        $this->logger->info('Creating payment record', [
            'paypal_order_id' => $paypal_response['id'],
            'order_id' => $order->id
        ]);

        return $order->payment()->create([
            'payment_gateway' => 'paypal',
            'merchant_request_id' => $paypal_response['id'], // This should match the token
            'checkout_request_id' => 'PAYPAL_' . $paypal_response['id'],
            'transaction_reference' => $paypal_response['id'],
            'response_code' => $paypal_response['status'],
            'response_description' => json_encode([
                'paypal_response' => $paypal_response,
                'conversion_data' => $conversionData
            ]),
            'status' => 'pending',
            'order_id' => $order->id,
            'customer_message' => 'PayPal payment initiated',
        ]);
    }

    /**
     * Format order items for PayPal
     */
    private function formatOrderItems($order, $paypal_currency, $exchange_rate)
    {
        $items = [];
        $total_amount_cents = (int)round($order->total_amount * $exchange_rate * 100);

        // Calculate each item's raw cent value
        $item_cents = [];
        $sum_cents = 0;
        
        foreach ($order->order_items as $item) {
            $raw_cents = (int)round($item->selling_price * $exchange_rate * 100 * $item->quantity);
            $item_cents[] = $raw_cents;
            $sum_cents += $raw_cents;
        }

        // Adjust last item to match the total exactly
        $difference = $total_amount_cents - $sum_cents;
        if ($difference != 0) {
            $item_cents[count($item_cents) - 1] += $difference;
        }

        // Format for PayPal
        foreach($order->order_items as $index => $item) {
            $unit_price_cents = (int)round($item_cents[$index] / $item->quantity);
            $unit_price_usd = number_format($unit_price_cents / 100, 2, '.', '');

            $items[] = [
                'name' => substr($item->title, 0, 127),
                'unit_amount' => [
                    'currency_code' => 'USD',
                    'value' => $unit_price_usd
                ],
                'quantity' => $item->quantity,
                'category' => 'PHYSICAL_GOODS'
            ];
        }

        return $items;
    }

    /**
     * Format shipping address for PayPal
     */
    private function formatShippingAddress($orderDelivery)
    {
        // For shop pickup, use a default address or handle differently
        if ($orderDelivery->location === 'Shop') {
            return [
                'address_line_1' => 'Shop Pickup',
                'admin_area_2' => 'Nairobi', // City
                'admin_area_1' => 'Nairobi', // State/Region
                'postal_code' => '00100',
                'country_code' => 'KE',
            ];
        }

        // For delivery, use the provided address
        $address = [
            'address_line_1' => $orderDelivery->address,
            'admin_area_2' => $orderDelivery->area, // City/Area
            'admin_area_1' => $orderDelivery->location, // State/Region (if applicable)
            'postal_code' => '00100', // You might want to add postal_code to your orders table
            'country_code' => $orderDelivery->area,
        ];

        // Remove empty fields
        return array_filter($address);
    }

    /**
     * Format amount according to PayPal's currency rules
     * 
     * @param float $amount
     * @param string $paypal_currency
     * @return string
     */
    private function formatAmountForPayPal($amount, $paypal_currency)
    {
        // List of currencies that don't support decimals (from PayPal docs)
        // Source: https://developer.paypal.com/api/rest/reference/currency-codes/
        $non_decimal_currencies = ['HUF', 'JPY', 'TWD'];
        
        if (in_array($paypal_currency, $non_decimal_currencies)) {
            // For non-decimal currencies: round to integer and return as whole number
            return (string) round($amount, 0);
        }
        
        // Use proper rounding to avoid floating point issues
        // Round to 2 decimal places and ensure sting format without trailing zeros issues
        return number_format(round($amount, 2, PHP_ROUND_HALF_UP), 2, '.', '');
    }

    /**
     * Helper for consistent error redirects
     */
    private function redirectWithError($message)
    {
        session()->flash('notify', [
            'message' => $message . ' Please try again or contact support.',
            'type' => 'error'
        ]);

        return redirect()->route('checkout-page');
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhookSignature(Request $request)
    {
        // TODO: Implement proper PayPal webhook verification
        return true;
    }
}
