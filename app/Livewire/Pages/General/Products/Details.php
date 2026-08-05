<?php

namespace App\Livewire\Pages\General\Products;

use Livewire\Component;
use App\Models\Products\Product;
use App\Services\CartService;

class Details extends Component
{
    public $productId;
    public $product;

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
        $this->product = $product;

        $price = $product->discount_price ?? $product->selling_price;

        // DISPATCH VIEWCONTENT EVENT
        // This sends an event to JavaScript which will trigger the Pixel
        $this->dispatch('track-view-content', [
            'content_name' => $this->product->title,           // Product name
            'content_ids' => [(string) $this->product->id],   // Product ID as string
            'content_type' => 'product',                      // Type of content
            'value' => $price,         // Product price
            'currency' => 'KES'                                // Your currency
        ]);
    }

    public function addToCart(int $product_id, int $quantity = 1): void
    {
        $cart_service = app(CartService::class);

        // Add to cart
        $cart_service->add($product_id, $quantity);

        // Get the product with price tiers for accurate pricing
        $product = Product::with('priceTiers')->find($product_id);
        $unit_price = $product->getEffectivePriceForQuantity($quantity);
        $total_value = $unit_price * $quantity;

        // DISPATCH ADDTOCART EVENT
        // This tells Meta someone added a product to cart
        $this->dispatch('track-add-to-cart', [
            'content_name' => $this->product->title,
            'content_ids' => [(string) $this->product->id],
            'content_type' => 'product',
            'value' => $total_value,  // Total value of items added
            'currency' => 'KES',
            'quantity' => $this->quantity
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
