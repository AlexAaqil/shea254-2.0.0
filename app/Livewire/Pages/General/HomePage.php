<?php

namespace App\Livewire\Pages\General;
use Illuminate\Support\Facades\Cache;

use Livewire\Component;
use App\Models\Products\Product;
use App\Models\Products\ProductReview;
use App\Services\CartService;
use App\Models\Sales\Sale;

class HomePage extends Component
{
    public $testimonials = [];

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
        app(CartService::class)->add($product_id);

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

        return view('livewire.pages.general.home-page', compact('featured_products', 'latest_orders'))->layout('layouts.guest');
    }
}
