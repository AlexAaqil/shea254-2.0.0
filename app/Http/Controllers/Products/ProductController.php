<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductMeasurement;
use App\Http\Requests\Products\ProductRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductController extends Controller
{
    public function create()
    {
        $categories = ProductCategory::orderBy('title')->get();
        $measurements = ProductMeasurement::orderBy('measurement_name')->get();

        return view('pages.products.products.create', compact('categories', 'measurements'));
    }

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated_data = $request->validated();

            unset($validated_data['images']);

            $validated_data['featured'] = $request->boolean('featured');
            $validated_data['is_visible'] = $request->has('is_visible');

            $validated_data['stock_count'] = $validated_data['stock_count'] ?? 0;
            $validated_data['safety_stock'] = $validated_data['safety_stock'] ?? 0;
            $validated_data['product_order'] = $validated_data['product_order'] ?? 200;

            $product = Product::create($validated_data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = $product->slug . '-' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('products', $filename, 'public');
                    $product->productImages()->create(['image' => $filename]);
                }
            }

            DB::commit();

            session()->flash('notify', ['message' => 'product created successfully', 'type' => 'success']);

            return redirect()->route('products.index');
        } catch(Throwable $e) {
            DB::rollback();

            if (app()->isLocal()) {
                dd($e->getMessage(), $e->getTraceAsString());
            }

            report($e);

            return back()->withInput()->with('error', 'An error occured while saving the product');
        }
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('title')->get();
        $measurements = ProductMeasurement::orderBy('measurement_name')->get();
        $product->load('product_images');

        return view('pages.products.products.edit', compact('product', 'categories', 'measurements'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::beginTransaction();

        try {
            $validated_data = $request->validated();

            unset($validated_data['images']);

            // Basic fallbacks
            $validated_data['featured'] = $request->boolean('featured');
            $validated_data['is_visible'] = $request->has('is_visible');
            $validated_data['stock_count'] = $validated_data['stock_count'] ?? 0;
            $validated_data['safety_stock'] = $validated_data['safety_stock'] ?? 0;
            $validated_data['product_order'] = $validated_data['product_order'] ?? 200;

            $product->update($validated_data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = $product->slug . '-' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('products', $filename, 'public');
                    $product->productImages()->create(['image' => $filename]);
                }
            }

            DB::commit();

            session()->flash('notify', ['message' => 'product updated successfully', 'type' => 'success']);

            return redirect()->route('products.index');
        } catch(Throwable $e) {
            DB::rollback();

            if (app()->isLocal()) {
                dd($e->getMessage(), $e->getTraceAsString());
            }

            report($e);

            return back()->withInput()->with('error', 'An error occured while updating the product');
        }
    }
}
