<?php

namespace App\Http\Controllers\Deliveries;

use App\Http\Controllers\Controller;
use App\Models\Deliveries\DeliveryArea;
use App\Models\Deliveries\DeliveryLocation;
use App\Http\Requests\Deliveries\AreaRequest;

class DeliveryAreaController extends Controller
{
    public function create()
    {
        $locations = DeliveryLocation::orderBy('location_name')->get();

        return view('pages.deliveries.areas.create', compact('locations'));
    }

    public function store(AreaRequest $request)
    {
        $validated_data = $request->validated();

        DeliveryArea::create($validated_data);

        session()->flash('notify', ['message' => 'area added successfully', 'type' => 'success']);

        return redirect()->route('delivery-areas.index');
    }

    public function edit(DeliveryArea $delivery_area)
    {
        $locations = DeliveryLocation::orderBy('location_name')->get();

        return view('pages.deliveries.areas.edit', compact('delivery_area', 'locations'));
    }

    public function update(AreaRequest $request, DeliveryArea $delivery_area)
    {
        $validated_data = $request->validated();

        $delivery_area->update($validated_data);

        session()->flash('notify', ['message' => 'area updated successfully', 'type' => 'success']);

        return redirect()->route('delivery-areas.index');
    }

    public function areasFetch($location)
    {
        $areas = DeliveryArea::where('delivery_location_id', $location)->get(['id', 'area_name']);

        return response()->json($areas);
    }

    public function areasShippingCost(DeliveryArea $area)
    {
        return response()->json(['price' => (float)$area->price]);
    }
}
