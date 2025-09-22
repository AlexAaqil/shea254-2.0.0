<?php

namespace App\Livewire\Pages\General\Products;

use Livewire\Component;
use App\Models\Products\ProductCategory;

class Categorized extends Component
{
    public $category;
    public $categories;
    public $products;

    public function mount(string $category)
    {
        $this->category = ProductCategory::where('slug', $category)->firstOrFail();
        $this->categories = ProductCategory::where('slug', '!=', $category)->get();
        $this->products = $this->category->products()
            ->with(['product_category', 'product_images'])
            ->where('is_visible', true)
            ->orderBy('title')
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.general.products.categorized')->layout('layouts.guest');
    }
}
