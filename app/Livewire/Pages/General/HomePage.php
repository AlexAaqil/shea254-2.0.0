<?php

namespace App\Livewire\Pages\General;
use Illuminate\Support\Facades\Cache;

use Livewire\Component;
use App\Models\Products\Product;
use App\Models\Products\ProductReview;
use App\Services\CartService;

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

        return view('livewire.pages.general.home-page', compact('featured_products'))->layout('layouts.guest');
    }
}
