<?php

namespace App\Livewire\Pages\General\Products;

use Livewire\Component;
use App\Models\Products\Product;
use App\Services\CartService;

class Details extends Component
{
    public $product;
    public $related_products;

    public function mount($slug)
    {
        $this->product = Product::query()
            ->select('id', 'title', 'slug', 'featured', 'is_visible', 'selling_price', 'discount_price', 'stock_count', 'category_id', 'description')
            ->with(['product_images', 'product_category'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->related_products = Product::query()
            ->select('id', 'title', 'slug', 'featured', 'is_visible', 'selling_price', 'discount_price', 'stock_count', 'category_id')
            ->with(['product_images', 'product_category:id,title,slug'])
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where('is_visible', true)
            ->OrderBy('title')
            ->limit(6)
            ->get();
    }

    public function addToCart(int $product_id): void
    {
        app(CartService::class)->add($product_id);

        $this->dispatch('cart-updated');

        $this->dispatch('notify', 'Added to cart', 'success');
    }

    public function render()
    {
        return view('livewire.pages.general.products.details')->layout('layouts.guest');
    }
}
