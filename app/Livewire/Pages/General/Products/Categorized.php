<?php

namespace App\Livewire\Pages\General\Products;

use Livewire\Component;
use App\Models\Products\ProductCategory;
use App\Services\CartService;
use App\Services\MetaConversionsApiService;
use Illuminate\Support\Facades\Log;
use App\Models\Products\Product;
use Exception;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Categorized extends Component
{
    public $category;
    public $categories;

    protected MetaConversionsApiService $capi;

    public function boot(MetaConversionsApiService $capi)
    {
        $this->capi = $capi;
    }

    public function mount(string $category)
    {
        $this->category = ProductCategory::where('slug', $category)->firstOrFail();
        $this->categories = ProductCategory::where('slug', '!=', $category)->get();
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

        // CAPI: Send AddToCart from server
        try {
            $price = $product->discount_price ?? $product->selling_price;
            $this->capi->trackAddToCart($product, 1, $price);
            Log::info('CAPI AddToCart sent for product ' . $product->id . ' from Category page');
        } catch (Exception $e) {
            Log::error('CAPI AddToCart failed from Category page: ' . $e->getMessage());
        }

        // Client-side tracking (backup)
        $this->dispatch('track-add-to-cart', [
            'content_name' => $product->title,
            'content_ids' => [(string) $product->id],
            'content_type' => 'product',
            'value' => $price,
            'currency' => 'KES',
            'quantity' => 1
        ]);

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
        
        return view('livewire.pages.general.products.categorized', compact('products'));
    }
}
