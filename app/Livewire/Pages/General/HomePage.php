<?php

namespace App\Livewire\Pages\General;

use Livewire\Component;
use App\Models\Products\Product;
use App\Models\Products\ProductReview;

class HomePage extends Component
{
    public function render()
    {
        $featured_products = Product::with('product_category')->where('featured', 1)->where('is_visible', 1)->take(12)->get();
        $testimonials = ProductReview::take(3)->get();

        return view('livewire.pages.general.home-page', compact('featured_products', 'testimonials'))->layout('layouts.guest');
    }
}
