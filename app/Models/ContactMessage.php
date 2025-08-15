<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (ContactMessage $message) {
            if (empty($message->uuid)) {
                $message->uuid = (string) Str::uuid();
            }
        });
    }

    protected $casts = [
        'is_read' => 'boolean',
        'is_important' => 'boolean',
    ];

    public function getIsReadAttribute(): bool
    {
        return (bool) $this->attributes['is_read'];
    }

    public function getIsNotReadAttribute(): bool
    {
        return !$this->is_read;
    }

    public function markAsRead(): void
    {
        if(!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }
}
