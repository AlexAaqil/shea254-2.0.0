<?php

namespace App\Models\Deliveries;

use Illuminate\Database\Eloquent\Model;

class DeliveryArea extends Model
{
    protected $guarded = [];

    public function delivery_location()
    {
        return $this->belongsTo(DeliveryLocation::class, 'delivery_location_id');
    }
}
