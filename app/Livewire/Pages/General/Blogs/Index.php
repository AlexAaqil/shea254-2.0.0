<?php

namespace App\Livewire\Pages\General\Blogs;

use Livewire\Component;
use App\Models\Blogs\Blog;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $blogs = Blog::latest()->paginate(30);

        return view('livewire.pages.general.blogs.index', compact('blogs'))->layout('layouts.guest');
    }
}
