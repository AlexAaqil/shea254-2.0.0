<?php

namespace App\Models\Blogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->title);
        });

        static::updating(function ($category) {
            if ($category->isDirty('title')) {
                $category->slug = Str::slug($category->title);
            }
        });
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
