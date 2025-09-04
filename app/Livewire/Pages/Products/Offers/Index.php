<?php

namespace App\Livewire\Pages\Products\Offers;

use Livewire\Component;
use App\Models\Products\Product;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $discounts = [];

    public $products = [];
    public $count_products = 0;

    protected $rules = [
        'discounts.*' => 'nullable|numeric|min:0'
    ];

    public function saveDiscounts()
    {
        $this->validate();

        DB::transaction(function () {
            foreach ($this->discounts as $productId => $discountPrice) {
                $product = Product::find($productId);

                if ($product) {
                    $finalDiscountPrice = (empty($discountPrice) || !is_numeric($discountPrice))
                        ? $product->selling_price
                        : (float) $discountPrice;

                    $finalDiscountPrice = min($finalDiscountPrice, $product->selling_price);

                    $product->update([
                        'discount_price' => $finalDiscountPrice,
                    ]);
                }
            }
        });

        $this->initializeProducts();

        $this->dispatch('notify', type: 'success', message: 'Discount prices updated successfully');
    }

    public function removeAllDiscounts()
    {
        DB::transaction(function () {
            Product::query()->update(['discount_price' => DB::raw('selling_price')]);
        });

        $this->initializeProducts();

        $this->dispatch('notify', type: 'success', message: 'All discounts removed successfully');
    }

    public function mount()
    {
        $this->initializeProducts();
    }

    protected function initializeProducts()
    {
        $query = Product::query()
            ->select(['id', 'title', 'buying_price', 'selling_price', 'discount_price'])
            ->orderBy('title');

        $this->products = $query->get();
        $this->count_products = $this->products->count();

        $this->initializeDiscounts();
    }

    protected function initializeDiscounts()
    {
        foreach ($this->products as $product) {
            $this->discounts[$product->id] = $product->discount_price ?? $product->selling_price;
        }
    }

    public function render()
    {
        return view('livewire.pages.products.offers.index');
    }
}
