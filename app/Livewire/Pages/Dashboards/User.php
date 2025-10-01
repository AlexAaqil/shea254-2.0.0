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

        $orders = Sale::with(['payment', 'order_delivery', 'order_items'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('order_delivery', function ($q2) use ($user) {
                      $q2->where('email', $user->email);
                  });
            })
            ->latest()
            ->take(10)
            ->get();

        $count_paid = $orders->filter(function ($order) {
            return optional($order->payment)->status === 'paid';
        })->count();

        $count_unpaid = $orders->count() - $count_paid;

        $reviews = ProductReview::where('user_id', $user->id)->take(10)->get();
        $count_reviews = ProductReview::where('user_id', $user->id)->count();

        return view('livewire.pages.dashboards.user', [
            'orders' => $orders,
            'count_paid' => $count_paid,
            'count_unpaid' => $count_unpaid,
            'reviews' => $reviews,
            'count_reviews' => $count_reviews,
        ]);
    }
}
