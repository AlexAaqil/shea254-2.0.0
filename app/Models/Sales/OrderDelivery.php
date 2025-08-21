<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    protected $guarded =[];

    public function order()
    {
        return $this->belongsTo(Sale::class, 'order_id');
    }
}
