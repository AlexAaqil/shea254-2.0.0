<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use App\Livewire\Actions\Auth\Logout;
use App\Services\CartService;

class Navbar extends Component
{
    public $cart_items;
    public $cart_count = 0;

    protected $listeners = ['cart-updated' => 'updateCount'];

    public function mount(CartService $cart)
    {
        $this->cart_items = $cart->getItems();
        $this->cart_count = $cart->count();
    }

    public function updateCount(CartService $cart)
    {
        $this->cart_count = $cart->count();
    }

    public function logout(Logout $logout)
    {
        $logout();
        $this->redirect('/', navigate:true);
    }

    public function render()
    {
        return view('livewire.partials.navbar');
    }
}
