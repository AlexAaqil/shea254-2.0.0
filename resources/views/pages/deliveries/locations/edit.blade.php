<x-app-layout>
    <div class="custom_form py-4 max-w-4xl mx-auto">
        <div class="header">
            <a href="{{ Route::has('delivery-locations.index') ? route('delivery-locations.index') : '#' }}" wire:navigate>
                <x-svgs.arrow-left class="w-5 h-5" />
            </a>
            <h2>Update Delivery Location</h2>
        </div>

        <form action="{{ route('delivery-locations.update', $delivery_location->id) }}" method="post">
            @csrf
            @method('PATCH')

            <div class="inputs">
                <label for="location_name" class="required">Location Name</label>
                <input type="text" name="location_name" id="location_name" autocomplete="location_name" value="{{ old('location_name', $delivery_location->location_name) }}" autofocus>
                <x-form-input-error field="location_name" />
            </div>

            <div class="buttons_group">
                <button type="submit">Update Location</button>
                <a href="{{ Route::has('delivery-locations.index') ? route('delivery-locations.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>


