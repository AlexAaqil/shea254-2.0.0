<?php

namespace App\Models\Blogs;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $guarded = [];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
