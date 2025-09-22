<?php

namespace App\Livewire\Pages\General\Sales;

use Livewire\Component;
use App\Services\CartService;

class Cart extends Component
{
    public $cart_items;
    public $cart_subtotal;
    public $cart_count = 0;

    public function mount(CartService $cart)
    {
        $this->refreshCart($cart);
    }

    public function increaseQuantity(CartService $cart, $product_id)
    {
        $currentQuantity = $this->getCurrentQuantity($product_id);
        if ($currentQuantity !== null) {
            $cart->update($product_id, $currentQuantity + 1);
            $this->refreshCart($cart);
        }
    }

    public function decreaseQuantity(CartService $cart, $product_id)
    {
        $currentQuantity = $this->getCurrentQuantity($product_id);
        if ($currentQuantity !== null && $currentQuantity > 1) {
            $cart->update($product_id, $currentQuantity - 1);
            $this->refreshCart($cart);
        }
    }

    public function removeItem(CartService $cart, $product_id)
    {
        $cart->remove($product_id);
        $this->refreshCart($cart);
    }

    private function getCurrentQuantity($product_id)
    {
        foreach ($this->cart_items as $item) {
            if ($item->product->id == $product_id) {
                return $item->quantity;
            }
        }
        return null;
    }

    private function refreshCart(CartService $cart)
    {
        // Get all cart data in one go
        $this->cart_items = $cart->getItems();
        $this->cart_subtotal = $cart->getSubtotal();
        $this->cart_count = $cart->count();

        $this->dispatch('cart-updated')->to('partials.navbar');
    }

    public function render()
    {
        return view('livewire.pages.general.sales.cart')->layout('layouts.guest');
    }
}