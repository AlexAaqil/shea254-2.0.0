<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductMeasurement extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'measurement_id');
    }
}
