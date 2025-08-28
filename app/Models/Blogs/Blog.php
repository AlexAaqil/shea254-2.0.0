<?php

namespace App\Models\Blogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($blog) {
            $blog->slug = Str::slug($blog->title);
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title')) {
                $blog->slug = Str::slug($blog->title);
            }
        });

        static::deleting(function ($blog) {
            $path = "blogs/{$blog->image}";

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        });
    }

    public function blog_category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/blogs/' . $this->image)
            : null;
    }
}
