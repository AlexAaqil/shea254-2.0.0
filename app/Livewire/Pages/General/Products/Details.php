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
        $this->product = Product::with(['product_images', 'product_category', 'product_reviews'])->where('slug', $slug)->firstOrFail();

        $this->related_products = Product::with('product_images', 'measurement_unit')
            ->where('category_id', $this->product->product_category_id)
            ->where('id', '!=', $this->product->id)
            ->inRandomOrder()
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
