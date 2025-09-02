<?php

namespace App\Livewire\Pages\Dashboards;

use Livewire\Component;
use App\Models\Sales\Sale;
use App\Models\Products\ProductReview;
use Illuminate\Support\Facades\Auth;

class User extends Component
{
    public function render()
    {
        $user = Auth::user();

        $orders = Sale::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('order_delivery', function ($q2) use ($user) {
                  $q2->where('email', $user->email);
              });
        });

        $count_paid = (clone $orders)->whereHas('payment', function ($q) {
            $q->where('status', 'paid');
        })->count();

        $count_unpaid = (clone $orders)->whereDoesntHave('payment', function ($q) {
            $q->where('status', 'paid');
        })->count();

        $count_reviews = ProductReview::where('user_id', $user->id)->count();

        return view('livewire.pages.dashboards.user', compact('count_paid', 'count_unpaid', 'count_reviews'));
    }
}
