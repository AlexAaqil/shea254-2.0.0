<?php

namespace App\Http\Controllers\Deliveries;

use App\Http\Controllers\Controller;
use App\Models\Deliveries\DeliveryLocation;
use App\Http\Requests\Deliveries\LocationRequest;

class DeliveryLocationController extends Controller
{
    public function create()
    {
        return view('pages.deliveries.locations.create');
    }

    public function store(LocationRequest $request)
    {
        $validated_data = $request->validated();

        DeliveryLocation::create($validated_data);

        session()->flash('notify', ['message' => 'location added successfully', 'type' => 'success']);

        return redirect()->route('delivery-locations.index');
    }

    public function edit(DeliveryLocation $delivery_location)
    {
        return view('pages.deliveries.locations.edit', compact('delivery_location'));
    }

    public function update(LocationRequest $request, DeliveryLocation $delivery_location)
    {
        $validated_data = $request->validated();

        $delivery_location->update($validated_data);

        session()->flash('notify', ['message' => 'location updated successfully', 'type' => 'success']);

        return redirect()->route('delivery-locations.index');
    }
}
