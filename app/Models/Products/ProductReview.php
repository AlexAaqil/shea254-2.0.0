<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProductReview extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
