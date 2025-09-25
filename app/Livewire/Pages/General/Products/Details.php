<?php

namespace App\Livewire\Pages\General\Products;

use Livewire\Component;
use App\Models\Products\Product;
use App\Services\CartService;

class Details extends Component
{
    public $productId;

    public function mount($slug)
    {
        // Store the product ID once (instead of serializing full model)
        $product = Product::query()
            ->select(
                'id',
                'title',
                'slug',
                'featured',
                'is_visible',
                'selling_price',
                'discount_price',
                'stock_count',
                'category_id',
                'description'
            )
            ->where('slug', $slug)
            ->firstOrFail();

        $this->productId = $product->id;
    }

    public function addToCart(int $product_id): void
    {
        app(CartService::class)->add($product_id);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', 'Added to cart', 'success');
    }

    public function render()
    {
        $product = Product::query()
            ->with(['product_images', 'product_category'])
            ->findOrFail($this->productId);

        if ($product->product_category) {
            $related_products = Product::query()
                ->select(
                    'id',
                    'title',
                    'slug',
                    'featured',
                    'is_visible',
                    'selling_price',
                    'discount_price',
                    'stock_count',
                    'category_id'
                )
                ->with(['product_images', 'product_category:id,title,slug'])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_visible', true)
                ->orderBy('title')
                ->limit(6)
                ->get();
        } else {
            $related_products = Product::query()
                ->select(
                    'id',
                    'title',
                    'slug',
                    'featured',
                    'is_visible',
                    'selling_price',
                    'discount_price',
                    'stock_count',
                    'category_id'
                )
                ->with(['product_images', 'product_category:id,title,slug'])
                ->where('id', '!=', $product->id)
                ->where('is_visible', true)
                ->where('featured', true)
                ->orderBy('title')
                ->limit(6)
                ->get();
        }

        return view('livewire.pages.general.products.details', [
            'product' => $product,
            'related_products' => $related_products,
        ])->layout('layouts.guest');
    }
}
