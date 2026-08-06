<?php

namespace App\Livewire\Pages\General\Blogs;

use Livewire\Component;
use App\Models\Blogs\Blog;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Details extends Component
{
    public $blog;

    public function mount($blog)
    {
        $this->blog = Blog::where('slug', $blog)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.pages.general.blogs.details');
    }
}
