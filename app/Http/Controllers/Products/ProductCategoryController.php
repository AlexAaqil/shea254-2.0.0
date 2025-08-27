<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductCategoryRequest;
use App\Models\Products\ProductCategory;

class ProductCategoryController extends Controller
{
    public function create()
    {
        return view('pages.products.categories.create');
    }

    public function store(ProductCategoryRequest $request)
    {
        $validated_data = $request->validated();

        ProductCategory::create($validated_data);

        session()->flash('notify', ['message' => 'category added successfully', 'type' => 'success']);

        return redirect()->route('product-categories.index');
    }

    public function edit(ProductCategory $product_category)
    {
        return view('pages.products.categories.edit', compact('product_category'));
    }

    public function update(ProductCategoryRequest $request, ProductCategory $product_category)
    {
        $validated_data = $request->validated();

        $product_category->update($validated_data);

        session()->flash('notify', ['message' => 'category updated successfully', 'type' => 'success']);

        return redirect()->route('product-categories.index');
    }
}
