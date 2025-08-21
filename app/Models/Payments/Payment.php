<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sales\Sale;

class Payment extends Model
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Sale::class, 'order_id');
    }
}
