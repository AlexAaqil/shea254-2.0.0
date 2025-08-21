<?php

namespace App\Models\Blogs;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $guarded = [];

    public function blog_category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function getImageUrl()
    {
        if($this->image) {
            return asset($this->image);
        } else {
            return asset('assets/images/default_image.jpg');
        }
    }
}
