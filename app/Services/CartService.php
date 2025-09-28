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
        $this->cachedItems = null;
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
        $this->cachedItems = null;
    }

    public function remove(int $product_id): void
    {
        $cart = Session::get('cart', []);
        unset($cart[$product_id]);
        Session::put('cart', $cart);
        $this->cachedItems = null;
    }

    public function clear(): void
    {
        Session::forget('cart');
        $this->cachedItems = null;
    }

    public function count(): int
    {
        return array_sum(Session::get('cart', []));
    }

    public function getItems()
    {
        if ($this->cachedItems !== null) {
            return $this->cachedItems;
        }

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return $this->cachedItems = collect();
        }

        $products = Product::with('priceTiers')
            ->whereIn('id', array_keys($cart))
            ->select('id', 'title', 'slug', 'buying_price', 'selling_price', 'discount_price')
            ->get()
            ->keyBy('id');

        $this->cachedItems = collect($cart)->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);
            
            if (!$product) {
                $this->remove($productId);
                return null;
            }

            $unit_price = $product->getEffectivePriceForQuantity($quantity);

            return (object) [
                'product'  => $product,
                'quantity' => $quantity,
                'unit_price' => $unit_price,
                'subtotal' => $unit_price * $quantity,
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