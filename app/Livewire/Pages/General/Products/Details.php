<?php

namespace App\Livewire\Pages\General\Products;

use Livewire\Component;
use App\Models\Products\Product;
use App\Services\CartService;

class Details extends Component
{
    public $product;

    public function mount($slug)
    {
        $this->product = Product::query()
            ->select('id', 'title', 'slug', 'featured', 'is_visible', 'selling_price', 'discount_price', 'stock_count', 'category_id', 'description')
            ->with(['product_images', 'product_category'])
            ->where('slug', $slug)
            ->firstOrFail(); 
    }

    public function addToCart(int $product_id): void
    {
        app(CartService::class)->add($product_id);

        $this->dispatch('cart-updated');

        $this->dispatch('notify', 'Added to cart', 'success');
    }

    public function render()
    {
        if ($this->product->product_category) {
            $related_products = Product::query()
                ->select('id', 'title', 'slug', 'featured', 'is_visible', 'selling_price', 'discount_price', 'stock_count', 'category_id')
                ->with(['product_images', 'product_category:id,title,slug'])
                ->where('category_id', $this->product->category_id)
                ->where('id', '!=', $this->product->id)
                ->where('is_visible', true)
                ->OrderBy('title')
                ->limit(6)
                ->get();
        } else {
            $related_products = Product::query()
                ->select('id', 'title', 'slug', 'featured', 'is_visible', 'selling_price', 'discount_price', 'stock_count', 'category_id')
                ->with(['product_images', 'product_category:id,title,slug'])
                ->where('id', '!=', $this->product->id)
                ->where('is_visible', true)
                ->where('featured', true)
                ->OrderBy('title')
                ->limit(6)
                ->get();
        }  

        return view('livewire.pages.general.products.details', compact('related_products'))->layout('layouts.guest');
    }
}
