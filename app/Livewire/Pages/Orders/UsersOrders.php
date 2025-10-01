<?php

namespace App\Livewire\Pages\Orders;

use Livewire\Component;
use App\Models\Sales\Sale;
use Illuminate\Support\Facades\Auth;

class UsersOrders extends Component
{
    public function render()
    {
        $user = Auth::user();

        $orders = Sale::with(['payment', 'order_delivery', 'order_items'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('order_delivery', function ($q2) use ($user) {
                      $q2->where('email', $user->email);
                  });
            })
            ->latest()
            ->get();

        $count_paid = $orders->filter(function ($order) {
            return optional($order->payment)->status === 'paid';
        })->count();

        $count_unpaid = $orders->count() - $count_paid;

        $count_orders = $orders->count();
        
        return view('livewire.pages.orders.users-orders', [
            'orders' => $orders,
            'count_paid' => $count_paid,
            'count_unpaid' => $count_unpaid,
            'count_orders' => $count_orders,
        ]);
    }
}
