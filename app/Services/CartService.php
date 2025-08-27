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
    public function add(int $product_id, int $quantity = 1): void
    {
        $cart = Session::get('cart', []);

        $cart[$product_id] = ($cart[$product_id] ?? 0) + $quantity;

        Session::put('cart', $cart);
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
    }

    public function remove(int $product_id): void
    {
        $cart = Session::get('cart', []);
        unset($cart[$product_id]);
        Session::put('cart', $cart);
    }

    public function clear(): void
    {
        Session::forget('cart');
    }

    public function count(): int
    {
        return collect(Session::get('cart', []))->sum();
    }

    public function getItems()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) return collect();

        $products = Product::whereIn('id', array_keys($cart))->get();

        return $products->map(function ($product) use ($cart) {
            return (object) [
                'product'  => $product,
                'quantity' => $cart[$product->id],
                'subtotal' => $cart[$product->id] * $product->selling_price,
            ];
        });
    }

    public function getTotal(): float
    {
        return $this->getItems()->sum('subtotal');
    }

    /**
     * Checkout: create Sale + OrderItems + OrderDelivery
     */
    public function checkout(array $deliveryData, ?int $user_id = null): Sale
    {
        $items = $this->getItems();

        if ($items->isEmpty()) {
            throw new \Exception("Cart is empty.");
        }

        // 1. Create Sale
        $sale = Sale::create([
            'order_number'   => strtoupper(Str::random(10)),
            'order_type'     => 1, // adjust if you have multiple types
            'total_amount'   => $this->getTotal(),
            'user_id'        => $user_id,
        ]);

        // 2. Create OrderItems
        foreach ($items as $item) {
            OrderItem::create([
                'order_id'       => $sale->id,
                'product_id'     => $item->product->id,
                'title'          => $item->product->title,
                'quantity'       => $item->quantity,
                'buying_price'   => $item->product->buying_price,
                'selling_price'  => $item->product->selling_price,
            ]);
        }

        // 3. Create Delivery
        OrderDelivery::create(array_merge(
            $deliveryData,
            ['order_id' => $sale->id]
        ));

        // 4. Clear cart
        $this->clear();

        return $sale;
    }
}
