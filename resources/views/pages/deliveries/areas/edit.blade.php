<x-app-layout>
    <div class="custom_form py-4 max-w-4xl mx-auto">
        <div class="header">
            <a href="{{ Route::has('delivery-areas.index') ? route('delivery-areas.index') : '#' }}" wire:navigate>
                <x-svgs.arrow-left class="w-5 h-5" />
            </a>
            <h2>Update Delivery Area</h2>
        </div>

        <form action="{{ route('delivery-areas.update', $delivery_area->id) }}" method="post">
            @csrf
            @method('PATCH')

            <div class="inputs">
                <label for="delivery_location_id">Delivery Location</label>
                <select name="delivery_location_id" id="delivery_location_id">
                    <option value="">Select Delivery Location</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ old('delivery_location_id', $delivery_area->delivery_location_id) == $location->id ? 'selected' : '' }}>{{ $location->location_name }}</option>
                    @endforeach
                </select>
                <x-form-input-error field="delivery_location_id" />
            </div>

            <div class="inputs">
                <label for="area_name" class="required">Area Name</label>
                <input type="text" name="area_name" id="area_name" autocomplete="area_name" value="{{ old('area_name', $delivery_area->area_name) }}" autofocus>
                <x-form-input-error field="area_name" />
            </div>

            <div class="inputs">
                <label for="price">Delivery Price</label>
                <input type="number" name="price" id="price" value="{{ old('price', $delivery_area->price) }}" />
                <x-form-input-error field="price" />
            </div>

            <div class="buttons_group">
                <button type="submit">Update Area</button>
                <a href="{{ Route::has('delivery-areas.index') ? route('delivery-areas.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>


