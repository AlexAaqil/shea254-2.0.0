<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductPriceTier extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
