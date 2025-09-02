<?php

namespace App\Livewire\Pages\General\Sales;

use Livewire\Component;
use App\Services\CartService;

class Cart extends Component
{
    public $cart_items;
    public $cart_count = 0;

    public function mount(CartService $cart)
    {
        $this->cart_items = $cart->getItems();
        $this->cart_count = $cart->count();
    }

    public function increaseQuantity(CartService $cart, $product_id)
    {
        foreach($this->cart_items as $item) {
            if ($item->product->id == $product_id) {
                $new_quantity = $item->quantity + 1;
                $cart->update($product_id, $new_quantity);
                break;
            }
        }

        $this->refreshCart($cart);
    }

      public function decreaseQuantity(CartService $cart, $product_id)
    {
        foreach ($this->cart_items as $item) {
            if ($item->product->id == $product_id) {
                if ($item->quantity > 1) {
                    $new_quantity = $item->quantity - 1;
                    $cart->update($product_id, $new_quantity);
                }
                break;
            }
        }
        $this->refreshCart($cart);
    }

    public function removeItem(CartService $cart, $product_id)
    {
        $cart->remove($product_id);
        $this->refreshCart($cart);
    }

    private function refreshCart(CartService $cart)
    {
        $this->cart_items = $cart->getItems();
        $this->cart_count = $cart->count();

        $this->dispatch('cart-updated')->to('partials.navbar');
    }

    public function render()
    {
        return view('livewire.pages.general.sales.cart')->layout('layouts.guest');
    }
}
