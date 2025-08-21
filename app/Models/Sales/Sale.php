<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sales\OrderItem;
use App\Models\User;
use App\Models\Payments\Payment;

class Sale extends Model
{
        protected $guarded = [];

    public function order_delivery()
    {
        return $this->hasOne(OrderDelivery::class, 'order_id');
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    static public function getOrders()
    {
        return self::select('sales.*')
        ->where('order_type', 1)
        ->orderBy('id','desc')
        ->get();
    }
}
