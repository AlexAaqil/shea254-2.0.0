<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductMeasurement;
use App\Http\Requests\Products\ProductMeasurementRequest;

class ProductMeasurementController extends Controller
{
    public function create()
    {
        return view('pages.products.measurements.create');
    }

    public function store(ProductMeasurementRequest $request)
    {
        $validated_data = $request->validated();

        ProductMeasurement::create($validated_data);

        session()->flash('notify', ['message' => 'measurement added successfully', 'type' => 'success']);

        return redirect()->route('product-measurements.index');
    }

    public function edit(ProductMeasurement $product_measurement)
    {
        return view('pages.products.measurements.edit', compact('product_measurement'));
    }

    public function update(ProductMeasurementRequest $request, ProductMeasurement $product_measurement)
    {
        $validated_data = $request->validated();

        $product_measurement->update($validated_data);

        session()->flash('notify', ['message' => 'measurement updated successfully', 'type' => 'success']);

        return redirect()->route('product-measurements.index');
    }
}
