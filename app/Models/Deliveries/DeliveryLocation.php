<?php

namespace App\Models\Deliveries;

use Illuminate\Database\Eloquent\Model;

class DeliveryLocation extends Model
{
    protected $guarded = [];

    public function delivery_areas()
    {
        return $this->hasMany(DeliveryArea::class);
    }
}
