<?php

namespace App\Livewire\Pages\General;
use Illuminate\Support\Facades\Cache;

use Livewire\Component;
use App\Models\Sales\Sale;
use App\Models\Products\Product;
use App\Models\Products\ProductReview;
use App\Services\CartService;
use App\Services\MetaConversionsApiService;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class HomePage extends Component
{
    public $testimonials = [];

    protected MetaConversionsApiService $capi;

    public function boot(MetaConversionsApiService $capi)
    {
        $this->capi = $capi;
    }

    public function loadTestimonials()
    {
        $this->testimonials = Cache::remember('homepage_testimonials', 3600, function () {
            return ProductReview::with('user:id,name') // eager load user
                ->latest()
                ->take(3)
                ->get();
        });
    }

    public function addToCart(int $product_id): void
    {
        // Get product details before adding to cart
        $product = Product::find($product_id);
        
        if (!$product) {
            $this->dispatch('notify', 'Product not found', 'error');
            return;
        }

        // Add to cart
        app(CartService::class)->add($product_id);

        $price = $product->discount_price ?? $product->selling_price;

        $eventId = 'add_to_cart_' . $product->id . '_' . time();

        // CAPI: Send AddToCart from server
        // try {
        //     $this->capi->trackAddToCart($product, 1, $price, $eventId);
        //     Log::info('CAPI AddToCart sent for product ' . $product->id);
        // } catch (Exception $e) {
        //     Log::error('CAPI AddToCart failed: ' . $e->getMessage());
        // }

        // Client-side tracking (backup)
        $this->dispatch('track-add-to-cart', [
            'content_name' => $product->title,
            'content_ids' => [(string) $product->id],
            'content_type' => 'product',
            'value' => $price,
            'currency' => 'KES',
            'quantity' => 1,
            'event_id' => $eventId
        ]);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', 'Added to cart', 'success');
    }

    public function render()
    {
        $featured_products = Product::select(['id', 'title', 'slug', 'selling_price', 'discount_price', 'stock_count', 'category_id'])
            ->with(['product_category', 'product_images'])
            ->where('featured', 1)
            ->where('is_visible', 1)
            ->take(12)
            ->get();

        $latest_orders = Sale::query()
            ->select(['id', 'order_number', 'total_amount', 'amount_paid', 'created_at'])
            ->with([
                'order_delivery:id,full_name,phone_number,order_id', 
                'order_items:id,title,quantity,order_id', 
                'payment:id,status,order_id'
            ])
            ->whereHas('payment', function ($query) {
                $query->where('status', 'paid');
            })
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.pages.general.home-page', compact('featured_products', 'latest_orders'));
    }
}
