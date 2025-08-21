<x-admin>
    <div class="container locations">
        <div class="custom_form">
            <h1>Update Area</h1>
            <form action="{{ route('areas.update', ['area' => $area->id]) }}" method="post">
                @csrf
                @method('PATCH')

                <div class="input_group">
                    <label for="delivery_location_id">Location</label>
                    <select name="delivery_location_id" id="delivery_location_id">
                        <option value="">Select Location</option>
                        @foreach ($locations as $location)
                            <option value="{{$location->id}}" {{ (old('delivery_location_id', $area->delivery_location_id) == $location->id) ? 'selected' : '' }}>{{$location->location_name}}</option>
                        @endforeach
                    </select>
                    <span class="inline_alert">{{ $errors->first('delivery_location_id') }}</span>
                </div>

                <div class="input_group">
                    <label for="area_name">Area Name</label>
                    <input type="text" name="area_name" id="area_name" value="{{ old('area_name', $area->area_name) }}" />
                    <span class="inline_alert">{{ $errors->first('area_name') }}</span>
                </div>

                <div class="input_group">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $area->price) }}" />
                    <span class="inline_alert">{{ $errors->first('price') }}</span>
                </div>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</x-admin>
