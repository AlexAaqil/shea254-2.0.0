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

        // DISPATCH VIEWCONTENT EVENT
        // This sends an event to JavaScript which will trigger the Pixel
        $this->dispatch('track-view-content', [
            'content_name' => $this->product->title,           // Product name
            'content_ids' => [(string) $this->product->id],   // Product ID as string
            'content_type' => 'product',                      // Type of content
            'value' => $this->product->selling_price,         // Product price
            'currency' => 'KES'                                // Your currency
        ]);
    }

    public function addToCart(int $product_id): void
    {
        app(CartService::class)->add($product_id);

        // DISPATCH ADDTOCART EVENT
        // This tells Meta someone added a product to cart
        $this->dispatch('track-add-to-cart', [
            'content_name' => $this->product->name,
            'content_ids' => [(string) $this->product->id],
            'content_type' => 'product',
            'value' => $this->product->price * $this->quantity,  // Total value of items added
            'currency' => 'KES',
            'quantity' => $this->quantity                         // Number of items
        ]);

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
