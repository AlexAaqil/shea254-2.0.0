<?php

namespace App\Livewire\Pages\General\Blogs;

use Livewire\Component;
use App\Models\Blogs\Blog;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $blogs = Blog::latest()->paginate(30);

        return view('livewire.pages.general.blogs.index', compact('blogs'));
    }
}
