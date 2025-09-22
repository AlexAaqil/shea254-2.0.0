<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\Products\Product;
use App\Models\Sales\Sale;
use App\Models\Sales\OrderItem;
use App\Models\Sales\OrderDelivery;
use Illuminate\Support\Str;

class CartService
{
    protected $cachedItems = null;

    public function add(int $product_id, int $quantity = 1): void
    {
        $cart = Session::get('cart', []);
        $cart[$product_id] = ($cart[$product_id] ?? 0) + $quantity;
        Session::put('cart', $cart);
        $this->cachedItems = null; // Clear cache on modification
    }

    public function update(int $product_id, int $quantity): void
    {
        $cart = Session::get('cart', []);
        
        if ($quantity <= 0) {
            unset($cart[$product_id]);
        } else {
            $cart[$product_id] = $quantity;
        }
        
        Session::put('cart', $cart);
        $this->cachedItems = null; // Clear cache on modification
    }

    public function remove(int $product_id): void
    {
        $cart = Session::get('cart', []);
        unset($cart[$product_id]);
        Session::put('cart', $cart);
        $this->cachedItems = null; // Clear cache on modification
    }

    public function clear(): void
    {
        Session::forget('cart');
        $this->cachedItems = null; // Clear cache on modification
    }

    public function count(): int
    {
        return array_sum(Session::get('cart', []));
    }

    public function getItems()
    {
        // Return cached items if available
        if ($this->cachedItems !== null) {
            return $this->cachedItems;
        }

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            $this->cachedItems = collect();
            return $this->cachedItems;
        }

        $products = Product::whereIn('id', array_keys($cart))
            ->select('id', 'title', 'selling_price', 'discount_price', 'buying_price')
            ->get()
            ->keyBy('id');

        $this->cachedItems = collect($cart)->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);
            
            if (!$product) {
                $this->remove($productId); // Clean up invalid products
                return null;
            }

            $effective_price = $product->discount_price > 0 && $product->discount_price < $product->selling_price
                ? $product->discount_price
                : $product->selling_price;

            return (object) [
                'product'  => $product,
                'quantity' => $quantity,
                'subtotal' => $quantity * $effective_price,
            ];
        })->filter();

        return $this->cachedItems;
    }

    public function getSubtotal(): float
    {
        return $this->getItems()->sum('subtotal');
    }

    public function getTotal(float $shipping = 0.0): float
    {
        return $this->getSubtotal() + (float)$shipping;
    }
}