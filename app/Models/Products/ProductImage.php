<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getProductImageURL() {
        if(!empty($this->image)) {
            return Storage::disk('public')->url('products/' . $this->image);
        }
        return asset('assets/images/default_product.jpg');
    }
}
