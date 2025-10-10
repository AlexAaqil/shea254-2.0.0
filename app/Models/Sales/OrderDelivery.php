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

    /**
     * Returns a blurred version of the phone number.
     * e.g. 254746055478 → 0746 *** *78
     */
    public function getBlurredPhoneNumberAttribute(): string
    {
        $phone = $this->phone_number;

        if (!$phone) {
            return '';
        }

        // Normalize
        $phone = str_replace(['+', ' '], '', trim($phone));

        // Convert 254xxxxxxxxx → 07xxxxxxxx
        if (preg_match('/^254(\d{9})$/', $phone, $matches)) {
            $phone = '0' . $matches[1];
        }

        // If invalid format, return as-is
        if (!preg_match('/^0\d{9}$/', $phone)) {
            return $phone;
        }

        // Blur middle section: 0746 *** *78
        return substr($phone, 0, 4) . ' *** *' . substr($phone, -2);
    }
}
