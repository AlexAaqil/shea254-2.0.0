<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\Sales\OrderItem;
use App\Models\Products\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function add(int $product_id, int $quantity =1): void
    {
        if (auth()->check()) {
            $item = OrderItem::firstOrCreate(
                ['user_id' => auth()->id(), 'product_id' => $product_id],
                ['quantity' => 0]
            );
            $item->increment('quantity', $quantity);
        }
        else {
            $cart = Session::get('cart', []);
            $cart[$product_id] = ($cart[$product_id] ?? 0) + $quantity;
            Session::put('cart', $cart);
        }
    }

    public function count()
    {
        if (auth()->check()) {
            return OrderItem::where('user_id', auth()->id())->sum('quantity');
        }
        else {
            return collect(Session::get('cart', []))->sum();
        }
    }

    public function getItems()
    {
        if (auth()->check()) {
            return OrderItem::with(['product' => function($query) {
                    $query->where('is_visible', true);
                }])
                ->where('user_id', auth()->id())->get();
        }
        else {
            $productIds = array_keys(Session::get('cart', []));
            $products = Product::whereIn('id', $productIds)->get();
            return $products->map(function ($product) {
                return (object)[
                    'product' => $product,
                    'quantity' => Session::get("cart")[$product->id],
                ];
            });
        }
    }

    public function update(int $product_id, int $quantity): void
    {
        if(auth()->check()) {
            if ($quantity <= 0) {
                $this->remove($product_id);
            }
            else {
                OrderItem::updateOrCreate(
                    ['user_id' => auth()->id(), 'product_id' => $product_id],
                    ['quantity' => $quantity]
                );
            }
        }
        else {
            $cart = Session::get('cart', []);
            if ($quantity <= 0) {
                unset($cart[$product_id]);
            }
            else {
                $cart[$product_id] = $quantity;
            }

            Session::put('cart', $cart);
        }
    }

    public function remove(int $product_id): void
    {
        if (auth()->check()) {
            OrderItem::where('user_id', auth()->id())
                ->where('product_id', $product_id)
                ->delete();
        }
        else {
            $cart = Session::get('cart', []);
            unset($cart[$product_id]);
            Session::put('cart', $cart);
        }
    }

    public function clear(): void
    {
        if (auth()->check()) {
            OrderItem::where('user_id', auth()->id())->delete();
        }
        else {
            Session::forget('cart');
        }
    }

    public function mergeSessionCartIntoDatabase(int $user_id): void
    {
        $session_cart = Session::get('cart', []);

        foreach ($session_cart as $product_id => $quantity) {
            OrderItem::updateOrCreate(
                ['user_id' => $user_id, 'product_id' => $product_id],
                ['quantity' => DB::raw("quantity + {$quantity}")]
            );
        }

        Session::forget('cart');
    }
}
