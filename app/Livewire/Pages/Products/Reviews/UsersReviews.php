<?php

namespace App\Livewire\Pages\Products\Reviews;

use Livewire\Component;
use App\Models\Products\ProductReview;
use Illuminate\Support\Facades\Auth;

class UsersReviews extends Component
{
    public function render()
    {
        $user = Auth::user();

        $reviews = ProductReview::where('user_id', $user->id)->get();
        $count_reviews = ProductReview::where('user_id', $user->id)->count();

        return view('livewire.pages.products.reviews.users-reviews', [
            'reviews' => $reviews,
            'count_reviews' => $count_reviews,
        ]);
    }
}
