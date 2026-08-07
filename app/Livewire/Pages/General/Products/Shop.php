<?php

namespace App\Livewire\Pages\General\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Services\CartService;
use App\Services\MetaConversionsApiService;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Shop extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $search_performed = false;
    protected $queryString = ['search'];

    protected MetaConversionsApiService $capi;

    public function boot(MetaConversionsApiService $capi)
    {
        $this->capi = $capi;
    }

    // Reset page when search input changes
    public function performSearch()
    {
        $this->search_performed = true;
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->search_performed = false;
        $this->resetPage();
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

        $price = $product->discount_price ?? $product->selling_price;

        $eventId = 'add_to_cart_' . $product->id . '_' . time();

        // CAPI: Send AddToCart from server
        try {
            $this->capi->trackAddToCart($product, 1, $price, $eventId);
            Log::info('CAPI AddToCart sent for product ' . $product->id);
        } catch (Exception $e) {
            Log::error('CAPI AddToCart failed: ' . $e->getMessage());
        }

        // Client-side tracking (backup)
        $this->dispatch('track-add-to-cart', [
            'content_name' => $product->title,
            'content_ids' => [(string) $product->id],
            'content_type' => 'product',
            'value' => $price,
            'currency' => 'KES',
            'quantity' => 1,
            'event_id' => $eventId
        ]);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', 'Added to cart', 'success');
    }

    public function render()
    {
        $categories = ProductCategory::orderBy('title')->get();

        $products = Product::query()
            ->select('id', 'title', 'slug', 'discount_price', 'selling_price', 'stock_count', 'category_id')
            ->with(['product_category', 'product_images'])
            ->when($this->search && $this->search_performed, function ($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->where('is_visible', true)
            ->paginate(30);

        $count_products = Product::where('is_visible', true)->count();

        return view('livewire.pages.general.products.shop', compact('categories', 'products', 'count_products'));
    }
}
