<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use App\Models\Products\ProductCategory;

class Footer extends Component
{
    public function render()
    {
        $categories = ProductCategory::take(6)->get();

        return view('livewire.partials.footer', compact('categories'));
    }
}
