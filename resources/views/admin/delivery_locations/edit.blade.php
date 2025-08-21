<x-admin>
    <div class="container locations">
        <div class="custom_form">
            <h1>Update Location</h1>

            <form action="{{ route('locations.update', ['location' => $location->id]) }}" method="post">
                @csrf
                @method('PATCH')

                <div class="input_group">
                    <label for="location_name">Location</label>
                    <input type="text" name="location_name" id="location_name" value="{{ old('location_name', $location->location_name) }}" />
                    <span class="inline_alert">{{ $errors->first('location_name') }}</span>
                </div>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</x-admin>
