<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MetaConversionsApiService
{
    protected string $pixelId;
    protected string $accessToken;
    protected bool $enabled;
    protected string $apiVersion;

    public function __construct()
    {
        $this->pixelId = config('meta-pixel.pixel_id');
        $this->accessToken = config('meta-pixel.access_token');
        $this->enabled = config('meta-pixel.capi_enabled', false);
        $this->apiVersion = config('meta-pixel.api_version', 'v18.0');
    }

    /**
     * Send an event to Meta Conversions API
     */
    public function sendEvent(string $eventName, array $customData = [], ?array $userData = null, ?string $eventId = null): bool
    {
        if (!$this->enabled || !$this->accessToken) {
            Log::info('CAPI disabled or missing token', [
                'enabled' => $this->enabled,
                'has_token' => !empty($this->accessToken)
            ]);
            return false;
        }

        try {
            $eventId = $eventId ?? Str::uuid()->toString();
            
            // Get user data from request
            $userData = $userData ?? $this->prepareUserData();
            
            // Prepare event data
            $payload = [
                'data' => [
                    [
                        'event_name' => $eventName,
                        'event_time' => time(),
                        'event_id' => $eventId,
                        'event_source_url' => url()->current(),
                        'action_source' => 'website',
                        'user_data' => $userData,
                        'custom_data' => $customData,
                    ]
                ],
                'access_token' => $this->accessToken,
            ];

            Log::info('Sending CAPI event', [
                'event_name' => $eventName,
                'event_id' => $eventId,
                'payload' => $payload
            ]);

            // Send to Meta
            $response = Http::post(
                "https://graph.facebook.com/{$this->apiVersion}/{$this->pixelId}/events",
                $payload
            );

            if ($response->successful()) {
                $result = $response->json();
                Log::info('CAPI event sent successfully', [
                    'event' => $eventName,
                    'result' => $result
                ]);
                return true;
            }

            Log::error('CAPI event failed', [
                'event' => $eventName,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return false;

        } catch (\Exception $e) {
            Log::error('CAPI exception', [
                'event' => $eventName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Prepare user data for CAPI (hashed)
     */
    protected function prepareUserData(): array
    {
        $userData = [
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->header('User-Agent'),
        ];

        // Get user if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->email) {
                $userData['em'] = hash('sha256', strtolower($user->email));
            }
            
            if ($user->phone) {
                $userData['ph'] = hash('sha256', $this->normalizePhone($user->phone));
            }
            
            if ($user->first_name) {
                $userData['fn'] = hash('sha256', strtolower($user->first_name));
            }
            
            if ($user->last_name) {
                $userData['ln'] = hash('sha256', strtolower($user->last_name));
            }
        }

        // Get Facebook click ID from URL
        if (request()->has('fbclid')) {
            $userData['fbclid'] = request()->input('fbclid');
        }

        // Get Facebook browser ID from cookie
        if (isset($_COOKIE['_fbp'])) {
            $userData['fbp'] = $_COOKIE['_fbp'];
        }

        return $userData;
    }

    /**
     * Normalize phone number (remove +, spaces, etc.)
     */
    protected function normalizePhone(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If phone starts with 0, replace with 254 (Kenya)
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }
        
        // If phone starts with 254, keep it
        if (str_starts_with($phone, '254')) {
            return $phone;
        }
        
        // Otherwise, assume it's a local number
        return $phone;
    }

    /**
     * Public method to normalize phone number
     * (Wrapper for protected normalizePhone)
     */
    public function normalizePhoneNumber(string $phone): string
    {
        return $this->normalizePhone($phone);
    }

    /**
     * Track PageView event
     */
    public function trackPageView(): bool
    {
        return $this->sendEvent('PageView', [
            'url' => url()->current(),
        ]);
    }

    /**
     * Track ViewContent event
     */
    public function trackViewContent($product, $price, ?string $eventId = null): bool
    {
        $eventId = $eventId ?? 'view_content_' . $product->id . '_' . time();

        // Get product data safely
        $productId = null;
        $productName = 'Product';
        
        if (is_object($product)) {
            $productId = $product->id ?? null;
            $productName = $product->title ?? $product->name ?? 'Product';
        } elseif (is_array($product)) {
            $productId = $product['id'] ?? null;
            $productName = $product['title'] ?? $product['name'] ?? 'Product';
        }
        
        if (!$productId) {
            Log::error('trackViewContent: Invalid product data');
            return false;
        }

        return $this->sendEvent('ViewContent', [
            'content_name' => $productName,
            'content_ids' => [(string) $productId],
            'content_type' => 'product',
            'value' => (float) $price,
            'currency' => 'KES',
        ], null, $eventId);
    }

    /**
     * Track AddToCart event
     */
    public function trackAddToCart($product, int $quantity, float $total, ?string $eventId = null): bool
    {
        $eventId = $eventId ?? 'add_to_cart_' . $product->id . '_' . time();
        
        // Get product data safely
        $productId = null;
        $productName = 'Product';
        
        if (is_object($product)) {
            $productId = $product->id ?? null;
            $productName = $product->title ?? $product->name ?? 'Product';
        } elseif (is_array($product)) {
            $productId = $product['id'] ?? null;
            $productName = $product['title'] ?? $product['name'] ?? 'Product';
        }
        
        if (!$productId) {
            Log::error('trackAddToCart: Invalid product data');
            return false;
        }

        return $this->sendEvent('AddToCart', [
            'content_name' => $productName,
            'content_ids' => [(string) $productId],
            'content_type' => 'product',
            'value' => (float) $total,
            'currency' => 'KES',
            'quantity' => (int) $quantity,
        ], null, $eventId);
    }

    /**
     * Track InitiateCheckout event
     */
    public function trackInitiateCheckout($cartItems, float $total, ?array $userData = null, ?string $eventId = null): bool
    {
        // Use provided event_id or generate one
        $eventId = $eventId ?? 'initiate_checkout_' . md5(json_encode($cartItems) . time());

        // Convert to array if it's a Collection
        if ($cartItems instanceof \Illuminate\Support\Collection) {
            $cartItems = $cartItems->toArray();
        }
        
        // If it's not an array, try to convert it
        if (!is_array($cartItems)) {
            Log::error('trackInitiateCheckout: cartItems is not an array or Collection', [
                'type' => gettype($cartItems)
            ]);
            return false;
        }
        
        $productIds = [];
        foreach ($cartItems as $item) {
            // Handle different item types
            if (is_object($item) && isset($item->product) && isset($item->product->id)) {
                // It's a CartItem object with product relationship
                $productIds[] = (string) $item->product->id;
            } elseif (is_array($item) && isset($item['product_id'])) {
                // It's an array with product_id
                $productIds[] = (string) $item['product_id'];
            } elseif (is_array($item) && isset($item['id'])) {
                // It's an array with id
                $productIds[] = (string) $item['id'];
            } elseif (is_object($item) && isset($item->id)) {
                // It's an object with id
                $productIds[] = (string) $item->id;
            }
        }

        if (empty($productIds)) {
            Log::warning('trackInitiateCheckout: No product IDs found', [
                'cart_items' => $cartItems
            ]);
            return false;
        }

        return $this->sendEvent('InitiateCheckout', [
            'value' => $total,
            'currency' => 'KES',
            'content_ids' => $productIds,
            'num_items' => count($cartItems),
        ], $userData, $eventId);
    }

    /**
     * Track Purchase event (MOST IMPORTANT)
     */
    public function trackPurchase($order, array $productIds, ?array $userData = null, ?string $eventId = null): bool
    {
        $eventId = 'purchase_' . $order->id . '_' . time();

        $finalUserData = $userData ?? $this->prepareUserData();
        
        return $this->sendEvent('Purchase', [
            'value' => (float) $order->total_amount,
            'currency' => 'KES',
            'content_ids' => $productIds,
            'content_type' => 'product',
            'num_items' => count($productIds),
            'order_id' => $order->order_number,
        ], $finalUserData, $eventId);
    }
}