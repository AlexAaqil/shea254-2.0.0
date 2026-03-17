# PayPal Simplified Code
```php
// PayPal Service Class
<?php

namespace App\Services\Payments;

use App\Models\Sales\Sale;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;
use Throwable;

class PayPalService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $mode;
    protected string $baseUrl;
    protected string $currency;
    protected array $config;
    protected $logger;

    public function __construct()
    {
        $this->clientId = env('PAYPAL_CLIENT_ID');
        $this->clientSecret = env('PAYPAL_CLIENT_SECRET');
        $this->mode = env('PAYPAL_MODE', 'sandbox');
        $this->currency = env('PAYPAL_CURRENCY', 'USD');
        
        $this->baseUrl = $this->mode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
        
        $this->logger = Log::channel('paypal');
        
        $this->config = [
            'timeout' => 30,
            'retry_times' => 3,
            'retry_sleep' => 100,
        ];
    }

    /**
     * Get access token with caching
     */
    public function getAccessToken(): string
    {
        $cacheKey = 'paypal_access_token_' . $this->mode;
        
        return Cache::remember($cacheKey, 3500, function () {
            try {
                $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                    ->asForm()
                    ->timeout($this->config['timeout'])
                    ->retry($this->config['retry_times'], $this->config['retry_sleep'])
                    ->post("{$this->baseUrl}/v1/oauth2/token", [
                        'grant_type' => 'client_credentials',
                    ]);

                if ($response->failed()) {
                    throw new Exception('Failed to get PayPal access token: ' . $response->body());
                }

                $data = $response->json();
                
                $this->logger->info('Access token obtained', [
                    'expires_in' => $data['expires_in'] ?? null
                ]);

                return $data['access_token'];
                
            } catch (Throwable $e) {
                $this->logger->error('Access token error', [
                    'message' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Create an order in PayPal
     */
    public function createOrder(Sale $order, array $conversionData, array $items, float $shippingCostUsd): array
    {
        $itemTotal = array_sum(array_column($items, 'total_usd'));
        $totalAmountUsd = $itemTotal + $shippingCostUsd;

        // Validate totals before sending
        if (abs($totalAmountUsd - $conversionData['usd_amount']) > 0.01) {
            $this->logger->warning('Total mismatch corrected', [
                'order_id' => $order->id,
                'calculated' => $totalAmountUsd,
                'expected' => $conversionData['usd_amount'],
            ]);
            $totalAmountUsd = $conversionData['usd_amount'];
        }

        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $order->order_number,
                    'description' => "Order #{$order->order_number}",
                    'amount' => $this->buildAmountPayload($itemTotal, $shippingCostUsd, $totalAmountUsd),
                    'custom_id' => $this->buildCustomId($order, $conversionData),
                    'items' => $this->formatItemsForApi($items),
                    'shipping' => $this->buildShippingPayload($order->order_delivery),
                ]
            ],
            'application_context' => $this->buildApplicationContext(),
        ];

        $this->logger->debug('PayPal order payload', ['order_data' => $orderData]);

        $response = Http::withToken($this->getAccessToken())
            ->withHeaders(['Content-Type' => 'application/json'])
            ->timeout($this->config['timeout'])
            ->retry($this->config['retry_times'], $this->config['retry_sleep'])
            ->post("{$this->baseUrl}/v2/checkout/orders", $orderData);

        if ($response->failed()) {
            $this->logError('Order creation failed', $response, $orderData);
            throw new Exception('PayPal order creation failed: ' . ($response->json()['message'] ?? 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Capture a payment
     */
    public function capturePayment(string $paypalOrderId): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
            ->timeout($this->config['timeout'])
            ->retry($this->config['retry_times'], $this->config['retry_sleep'])
            ->post("{$this->baseUrl}/v2/checkout/orders/{$paypalOrderId}/capture", [
                'body' => '{}'
            ]);

        if ($response->failed()) {
            $this->logError('Capture failed', $response);
            throw new Exception('PayPal capture failed: ' . ($response->json()['message'] ?? 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Build amount payload
     */
    private function buildAmountPayload(float $itemTotal, float $shipping, float $total): array
    {
        return [
            'currency_code' => $this->currency,
            'value' => sprintf('%.2f', $total),
            'breakdown' => [
                'item_total' => [
                    'currency_code' => $this->currency,
                    'value' => sprintf('%.2f', $itemTotal)
                ],
                'shipping' => [
                    'currency_code' => $this->currency,
                    'value' => sprintf('%.2f', $shipping)
                ]
            ]
        ];
    }

    /**
     * Build minimal custom_id
     */
    private function buildCustomId(Sale $order, array $conversionData): string
    {
        return json_encode([
            'id' => $order->id,
            'ref' => $order->order_number,
            'ks' => $conversionData['kes_amount'],
            'us' => $conversionData['usd_amount'],
            'rt' => $conversionData['rate_used'],
            'tx' => $conversionData['transaction_id'],
        ]);
    }

    /**
     * Format items for API
     */
    private function formatItemsForApi(array $items): array
    {
        return array_map(function ($item) {
            return [
                'name' => substr($item['name'], 0, 127),
                'unit_amount' => [
                    'currency_code' => $this->currency,
                    'value' => sprintf('%.2f', $item['unit_price_usd']),
                ],
                'quantity' => $item['quantity'],
                'description' => substr($item['description'] ?? '', 0, 127),
                'category' => 'PHYSICAL_GOODS',
            ];
        }, $items);
    }

    /**
     * Build shipping payload
     */
    private function buildShippingPayload($delivery): array
    {
        $address = $delivery->location === 'Shop' 
            ? $this->getShopPickupAddress()
            : [
                'address_line_1' => $delivery->address,
                'admin_area_2' => $delivery->area,
                'admin_area_1' => $delivery->location,
                'postal_code' => '00100',
                'country_code' => 'KE',
            ];

        return [
            'name' => ['full_name' => $delivery->full_name],
            'address' => array_filter($address),
        ];
    }

    /**
     * Build application context
     */
    private function buildApplicationContext(): array
    {
        return [
            'brand_name' => config('app.name'),
            'landing_page' => 'BILLING',
            'shipping_preference' => 'SET_PROVIDED_ADDRESS',
            'user_action' => 'PAY_NOW',
            'return_url' => route('paypal.capture'),
            'cancel_url' => route('paypal.cancel'),
        ];
    }

    /**
     * Get shop pickup address
     */
    private function getShopPickupAddress(): array
    {
        return [
            'address_line_1' => 'Shop Pickup',
            'admin_area_2' => 'Nairobi',
            'admin_area_1' => 'Nairobi',
            'postal_code' => '00100',
            'country_code' => 'KE',
        ];
    }

    /**
     * Log API errors
     */
    private function logError(string $message, $response, array $requestData = []): void
    {
        $this->logger->error($message, [
            'status' => $response->status(),
            'response' => $response->json(),
            'request' => $requestData,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2),
        ]);
    }
}



// Paypment Processor Trait (Reusable Logic)
<?php

namespace App\Traits;

use App\Models\Products\Product;
use App\Models\Sales\Sale;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\DB;
use Exception;

trait PaymentProcessorTrait
{
    /**
     * Process successful payment
     */
    protected function processSuccessfulPayment(Sale $order, Payment $payment, array $paymentData, array $customData = [])
    {
        return DB::transaction(function () use ($order, $payment, $paymentData, $customData) {
            // Update payment
            $payment->update([
                'status' => 'paid',
                'transaction_reference' => $paymentData['id'] ?? $payment->transaction_reference,
                'response_code' => $paymentData['status'] ?? 'COMPLETED',
                'response_description' => json_encode([
                    'gateway_response' => $paymentData,
                    'custom_data' => $customData,
                    'processed_at' => now()->toIso8601String(),
                ]),
                'customer_message' => 'Payment completed successfully',
            ]);

            // Update order
            $order->update(['status' => 'paid']);

            // Decrement stock
            $this->decrementStock($order);

            // Clear session data
            $this->clearPaymentSession();

            return $order;
        });
    }

    /**
     * Process failed payment
     */
    protected function processFailedPayment(Payment $payment, array $paymentData, string $message)
    {
        $payment->update([
            'status' => 'failed',
            'response_code' => $paymentData['status'] ?? 'FAILED',
            'response_description' => json_encode([
                'error' => $paymentData,
                'failed_at' => now()->toIso8601String(),
            ]),
            'customer_message' => 'Payment failed: ' . $message,
        ]);

        return $payment;
    }

    /**
     * Decrement product stock
     */
    protected function decrementStock(Sale $order): void
    {
        foreach ($order->order_items as $item) {
            $updated = Product::where('id', $item->product_id)
                ->where('stock_count', '>=', $item->quantity)
                ->decrement('stock_count', $item->quantity);

            if (!$updated) {
                throw new Exception("Insufficient stock for product ID: {$item->product_id}");
            }
        }
    }

    /**
     * Clear payment session data
     */
    protected function clearPaymentSession(): void
    {
        session()->forget([
            'checkout_form_data',
            'checkout_selected_location',
            'checkout_selected_area',
            'pending_paystack_order_id',
            'paypal_order_id',
            'cart',
            'cart_count',
        ]);
    }
}



// PayPal Controller (Clean and Focused)
<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use App\Models\Payments\Payment;
use App\Services\Payments\PayPalService;
use App\Services\CurrencyExchangeService;
use App\Traits\PaymentProcessorTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;

class PayPalController extends Controller
{
    use PaymentProcessorTrait;

    protected PayPalService $paypalService;
    protected CurrencyExchangeService $exchangeService;
    protected $logger;
    protected $inventoryLogger;

    public function __construct(
        PayPalService $paypalService,
        CurrencyExchangeService $exchangeService
    ) {
        $this->paypalService = $paypalService;
        $this->exchangeService = $exchangeService;
        $this->logger = Log::channel('paypal');
        $this->inventoryLogger = Log::channel('inventory_management');

        $this->logger->info('PayPalController initialized');
    }

    /**
     * Initialize PayPal payment
     */
    public function initializePayment(Sale $order, float $totalAmountKes)
    {
        try {
            // Generate transaction ID for rate locking
            $transactionId = $order->order_number . '_' . uniqid();
            
            // Get locked exchange rate
            $conversionData = $this->exchangeService->convertForTransaction(
                $transactionId, 
                $totalAmountKes
            );

            $this->logger->info('Currency conversion', $conversionData);

            // Format items for PayPal
            $items = $this->formatOrderItems($order, $conversionData['rate_used']);

            // Calculate shipping
            $shippingCostUsd = $this->calculateShippingCost(
                $order->shipping_cost ?? 0,
                $conversionData['rate_used']
            );

            // Create PayPal order
            $paypalOrder = $this->paypalService->createOrder(
                $order,
                $conversionData,
                $items,
                $shippingCostUsd
            );

            // Store payment record
            $this->createPaymentRecord($order, $paypalOrder, $conversionData);

            // Find approval URL and redirect
            foreach ($paypalOrder['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }

            throw new Exception('No approval link found in PayPal response');

        } catch (Throwable $e) {
            return $this->handleInitializationError($e, $order);
        }
    }

    /**
     * Handle PayPal capture callback
     */
    public function capturePayment(Request $request)
    {
        $paypalOrderId = $request->token;

        $this->logger->info('Capture callback received', [
            'paypal_order_id' => $paypalOrderId,
            'payer_id' => $request->PayerID
        ]);

        if (!$paypalOrderId) {
            return $this->redirectWithError('Invalid payment session');
        }

        try {
            // Capture the payment
            $captureData = $this->paypalService->capturePayment($paypalOrderId);

            // Find payment record
            $payment = Payment::where('merchant_request_id', $paypalOrderId)->first();

            if (!$payment) {
                throw new Exception('Payment record not found');
            }

            $order = Sale::find($payment->order_id);

            if (!$order) {
                throw new Exception('Order not found');
            }

            // Process based on status
            if ($captureData['status'] === 'COMPLETED') {
                $customData = json_decode($captureData['purchase_units'][0]['custom_id'] ?? '{}', true);
                
                $order = $this->processSuccessfulPayment(
                    $order, 
                    $payment, 
                    $captureData,
                    $customData
                );

                session()->put('order_number', $order->order_number);

                return $this->redirectWithSuccess($order);
            }

            return $this->processFailedPayment(
                $payment,
                $captureData,
                $captureData['message'] ?? 'Payment failed'
            );

        } catch (Throwable $e) {
            return $this->handleCaptureError($e, $paypalOrderId);
        }
    }

    /**
     * Format order items for PayPal
     */
    private function formatOrderItems(Sale $order, float $exchangeRate): array
    {
        $items = [];

        foreach ($order->order_items as $item) {
            $unitPriceUsd = $item->selling_price * $exchangeRate;
            
            $items[] = [
                'name' => $item->title,
                'unit_price_usd' => $unitPriceUsd,
                'quantity' => $item->quantity,
                'total_usd' => $unitPriceUsd * $item->quantity,
                'description' => $item->title . ' (KES ' . number_format($item->selling_price, 2) . ')',
                'sku' => 'PROD-' . $item->product_id,
            ];

            $this->logger->debug('Item formatted', [
                'product_id' => $item->product_id,
                'kes_price' => $item->selling_price,
                'usd_price' => $unitPriceUsd,
                'quantity' => $item->quantity,
            ]);
        }

        return $items;
    }

    /**
     * Calculate shipping cost in USD
     */
    private function calculateShippingCost(float $shippingKes, float $exchangeRate): float
    {
        if ($shippingKes <= 0) {
            return 0.0;
        }

        // Round to 2 decimal places for USD
        return round($shippingKes * $exchangeRate, 2);
    }

    /**
     * Create payment record
     */
    private function createPaymentRecord(Sale $order, array $paypalResponse, array $conversionData): void
    {
        $order->payment()->create([
            'payment_gateway' => 'paypal',
            'merchant_request_id' => $paypalResponse['id'],
            'checkout_request_id' => 'PAYPAL_' . $paypalResponse['id'],
            'transaction_reference' => $paypalResponse['id'],
            'response_code' => $paypalResponse['status'],
            'response_description' => json_encode([
                'paypal_response' => $paypalResponse,
                'conversion_data' => $conversionData,
                'initiated_at' => now()->toIso8601String(),
            ]),
            'status' => 'pending',
            'customer_message' => 'PayPal payment initiated',
        ]);
    }

    /**
     * Handle initialization errors
     */
    private function handleInitializationError(Throwable $e, Sale $order)
    {
        $this->logger->error('PayPal initialization failed', [
            'order_id' => $order->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        session()->flash('notify', [
            'message' => 'Payment system error. Please try again.',
            'type' => 'error'
        ]);

        return redirect()->route('checkout-page');
    }

    /**
     * Handle capture errors
     */
    private function handleCaptureError(Throwable $e, string $paypalOrderId)
    {
        $this->logger->error('Capture handling failed', [
            'paypal_order_id' => $paypalOrderId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        session()->flash('notify', [
            'message' => 'Payment verification failed. Please contact support.',
            'type' => 'error'
        ]);

        return redirect()->route('shop-page');
    }

    /**
     * Redirect with success
     */
    private function redirectWithSuccess(Sale $order)
    {
        session()->flash('notify', [
            'message' => "Payment successful! Your order {$order->order_number} has been confirmed.",
            'type' => 'success'
        ]);

        return redirect()->route('checkout.success');
    }

    /**
     * Redirect with error
     */
    private function redirectWithError(string $message)
    {
        $this->logger->error($message);

        session()->flash('notify', [
            'message' => $message . ' Please try again.',
            'type' => 'error'
        ]);

        return redirect()->route('checkout-page');
    }

    /**
     * Handle PayPal cancel
     */
    public function cancelPayment(Request $request)
    {
        $this->logger->info('PayPal payment cancelled', ['token' => $request->token]);

        session()->flash('notify', [
            'message' => 'You have cancelled the PayPal payment. Your order has not been processed.',
            'type' => 'info'
        ]);

        return redirect()->route('checkout-page');
    }

    /**
     * Handle PayPal webhook
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $event = json_decode($payload, true);

            $this->logger->info('Webhook received', [
                'event_type' => $event['event_type'] ?? 'unknown'
            ]);

            // Handle payment capture completed
            if (($event['event_type'] ?? '') === 'PAYMENT.CAPTURE.COMPLETED') {
                $resource = $event['resource'];
                $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

                if ($paypalOrderId) {
                    $payment = Payment::where('merchant_request_id', $paypalOrderId)->first();
                    
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
            $this->logger->error('Webhook handling failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}



// Currency Exchange Service
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class CurrencyExchangeService
{
    protected $logger;
    protected $fallbackRate;
    protected $cacheTtl;
    protected $apis;

    public function __construct()
    {
        $this->logger = Log::channel('paypal');
        $this->fallbackRate = (float) env('FALLBACK_KES_TO_USD_RATE', 0.0077);
        $this->cacheTtl = (int) env('EXCHANGE_RATE_CACHE_TTL', 300); // 5 minutes
        
        // Multiple reliable API sources
        $this->apis = [
            'primary' => [
                'url' => 'https://api.exchangerate-api.com/v4/latest/KES',
                'path' => 'rates.USD',
                'timeout' => 5,
                'retry' => 2
            ],
            'secondary' => [
                'url' => 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/kes.json',
                'path' => 'kes.usd',
                'timeout' => 5,
                'retry' => 2
            ],
            'tertiary' => [
                'url' => 'https://api.currencyapi.com/v3/latest',
                'params' => ['base_currency' => 'KES', 'currencies' => 'USD'],
                'path' => 'data.USD.value',
                'headers' => ['apikey' => env('CURRENCY_API_KEY', '')],
                'timeout' => 5,
                'retry' => 2
            ]
        ];
    }

    /**
     * Get best available exchange rate
     */
    public function getRate(): array
    {
        // Try cache first
        $cached = $this->getCachedRate();
        if ($cached) {
            return $cached;
        }

        $errors = [];

        // Try each API in sequence
        foreach ($this->apis as $source => $config) {
            try {
                $rate = $this->fetchFromApi($config);
                
                if ($rate && $rate > 0) {
                    $result = $this->buildRateData($rate, $source);
                    $this->cacheRate($result);
                    
                    $this->logger->info('Exchange rate fetched', $result);
                    
                    return $result;
                }
            } catch (Exception $e) {
                $errors[$source] = $e->getMessage();
                $this->logger->warning("Exchange rate API failed: {$source}", [
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        // All APIs failed - use fallback and alert
        $this->triggerAlert('All exchange rate APIs failed', $errors);
        
        $this->logger->critical('All exchange rate APIs failed', ['errors' => $errors]);
        
        return $this->buildRateData($this->fallbackRate, 'fallback', [
            'warning' => 'Using configured fallback rate - APIs unavailable',
            'errors' => $errors
        ]);
    }

    /**
     * Fetch rate from specific API
     */
    protected function fetchFromApi(array $config): ?float
    {
        $response = Http::timeout($config['timeout'] ?? 5)
            ->retry($config['retry'] ?? 2, 100)
            ->withHeaders($config['headers'] ?? [])
            ->get($config['url'], $config['params'] ?? []);

        if (!$response->successful()) {
            throw new Exception("HTTP {$response->status()}: {$response->body()}");
        }

        $data = $response->json();
        
        // Navigate response path
        $value = $data;
        foreach (explode('.', $config['path']) as $key) {
            if (!isset($value[$key])) {
                throw new Exception("Invalid response structure: missing {$key}");
            }
            $value = $value[$key];
        }

        return (float) $value;
    }

    /**
     * Build rate data array
     */
    protected function buildRateData(float $rate, string $source, array $extra = []): array
    {
        return array_merge([
            'rate' => $rate,
            'source' => $source,
            'timestamp' => now(),
            'expires_at' => now()->addSeconds($this->cacheTtl),
        ], $extra);
    }

    /**
     * Get cached rate
     */
    protected function getCachedRate(): ?array
    {
        return Cache::get('exchange_rate');
    }

    /**
     * Cache the rate
     */
    protected function cacheRate(array $rateData): void
    {
        Cache::put('exchange_rate', $rateData, now()->addSeconds($this->cacheTtl));
    }

    /**
     * Trigger alert for monitoring
     */
    protected function triggerAlert(string $message, array $context = []): void
    {
        // Implement your alerting logic here
        // e.g., Send to Slack, email, or monitoring service
        
        if (app()->environment('production')) {
            // Example: Send to Slack
            // Slack::send($message, $context);
        }
    }

    /**
     * Lock rate for transaction
     */
    public function lockRateForTransaction(string $transactionId, array $rateData): void
    {
        Cache::put(
            "locked_rate_{$transactionId}", 
            $rateData, 
            now()->addHours(24)
        );
    }

    /**
     * Get locked rate
     */
    public function getLockedRate(string $transactionId): ?array
    {
        return Cache::get("locked_rate_{$transactionId}");
    }

    /**
     * Convert amount with rate locking
     */
    public function convertForTransaction(string $transactionId, float $amountKES): array
    {
        $rateData = $this->getLockedRate($transactionId);
        
        if (!$rateData) {
            $rateData = $this->getRate();
            $this->lockRateForTransaction($transactionId, $rateData);
        }

        $amountUSD = round($amountKES * $rateData['rate'], 2);

        return [
            'kes_amount' => $amountKES,
            'usd_amount' => $amountUSD,
            'rate_used' => $rateData['rate'],
            'rate_source' => $rateData['source'],
            'rate_timestamp' => $rateData['timestamp'],
            'transaction_id' => $transactionId
        ];
    }
}



// Configuration File
<?php
// config/payments.php

return [
    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'currency' => env('PAYPAL_CURRENCY', 'USD'),
        'timeout' => env('PAYPAL_TIMEOUT', 30),
        'retry' => [
            'times' => env('PAYPAL_RETRY_TIMES', 3),
            'sleep' => env('PAYPAL_RETRY_SLEEP', 100),
        ],
        'endpoints' => [
            'sandbox' => 'https://api-m.sandbox.paypal.com',
            'live' => 'https://api-m.paypal.com',
        ],
    ],
    
    'exchange_rates' => [
        'cache_ttl' => env('EXCHANGE_RATE_CACHE_TTL', 300),
        'fallback_rate' => env('FALLBACK_KES_TO_USD_RATE', 0.0077),
        'alert_on_failure' => env('ALERT_ON_RATE_FAILURE', true),
    ],
];
```
