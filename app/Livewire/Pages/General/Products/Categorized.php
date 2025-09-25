<?php

namespace App\Livewire\Pages\General\Products;

use Livewire\Component;
use App\Models\Products\ProductCategory;
use App\Services\CartService;

class Categorized extends Component
{
    public $category;
    public $categories;

    public function mount(string $category)
    {
        $this->category = ProductCategory::where('slug', $category)->firstOrFail();
        $this->categories = ProductCategory::where('slug', '!=', $category)->get();
    }

    public function addToCart(int $product_id): void
    {
        app(CartService::class)->add($product_id);

        $this->dispatch('cart-updated');

        $this->dispatch('notify', 'Added to cart', 'success');
    }

    public function render()
    {
        $products = $this->category->products()
            ->with(['product_category', 'product_images'])
            ->where('is_visible', true)
            ->orderBy('title')
            ->get();
        
        return view('livewire.pages.general.products.categorized', compact('products'))->layout('layouts.guest');
    }
}
