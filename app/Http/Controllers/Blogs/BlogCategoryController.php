<?php

namespace App\Http\Controllers\Blogs;

use App\Http\Controllers\Controller;
use App\Models\Blogs\BlogCategory;
use App\Http\Requests\Blogs\BlogCategoryRequest;

class BlogCategoryController extends Controller
{
    public function create()
    {
        return view('pages.blogs.categories.create');
    }

    public function store(BlogCategoryRequest $request)
    {
        $validated_data = $request->validated();

        BlogCategory::create($validated_data);

        session()->flash('notify', ['message' => 'category added successfully', 'type' => 'success']);

        return redirect()->route('blog-categories.index');
    }

    public function edit(BlogCategory $blog_category)
    {
        return view('pages.blogs.categories.edit', compact('blog_category'));
    }

    public function update(BlogCategoryRequest $request, BlogCategory $blog_category)
    {
        $validated_data = $request->validated();

        $blog_category->update($validated_data);

        session()->flash('notify', ['message' => 'category updated successfully', 'type' => 'success']);

        return redirect()->route('blog-categories.index');
    }
}
