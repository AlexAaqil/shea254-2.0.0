<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sales\OrderItem;
use App\Models\User;
use App\Models\Products\Product;
use App\Models\Payments\Payment;
use Carbon\Carbon;

class Sale extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($sale) {
            $sale->order_items()->delete();

            $sale->order_delivery()->delete();

            $sale->payment()->delete();
        });
    }

    public function order_delivery()
    {
        return $this->hasOne(OrderDelivery::class, 'order_id');
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    static public function getOrders()
    {
        return self::select('sales.*')
        ->where('order_type', 1)
        ->orderBy('id','desc')
        ->get();
    }

    public function scopeToday($query) {
        return $query->whereDate('sales.created_at', Carbon::today());
    }

    public function scopeYesterday($query) {
        return $query->whereDate('sales.created_at', Carbon::yesterday());
    }

    public function scopeThisWeek($query) {
        return $query->whereBetween('sales.created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek(),
        ]);
    }

    public function scopeThisMonth($query) {
        return $query->whereMonth('sales.created_at', Carbon::now()->month)
            ->whereYear('sales.created_at', Carbon::now()->year);
    }
}
