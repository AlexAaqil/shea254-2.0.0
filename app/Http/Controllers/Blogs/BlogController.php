<?php

namespace App\Http\Controllers\Blogs;

use App\Http\Controllers\Controller;
use App\Models\Blogs\Blog;
use App\Models\Blogs\BlogCategory;
use App\Http\Requests\Blogs\BlogRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function create()
    {
        $categories = BlogCategory::orderBy('title')->get();

        return view('pages.blogs.blogs.create', compact('categories'));
    }

    public function store(BlogRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = uniqid('blog_') . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('blogs', $filename, 'public');

                $validated['image'] = $filename;
            }

            Blog::create($validated);
        });

        session()->flash('notify', [
            'message' => 'Blog added successfully',
            'type' => 'success'
        ]);

        return redirect()->route('blogs.index');
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::orderBy('title')->get();

        return view('pages.blogs.blogs.edit', compact('blog', 'categories'));
    }

    public function update(BlogRequest $request, Blog $blog)
    {
        DB::transaction(function () use ($request, $blog) {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                if ($blog->image) {
                    Storage::disk('public')->delete($blog->image);
                }

                $file = $request->file('image');
                $filename = uniqid('blog_') . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('blogs', $filename, 'public');

                $validated['image'] = $filename;
            }

            $blog->update($validated);
        });

        session()->flash('notify', [
            'message' => 'Blog updated successfully',
            'type' => 'success'
        ]);

        return redirect()->route('blogs.index');
    }
}
