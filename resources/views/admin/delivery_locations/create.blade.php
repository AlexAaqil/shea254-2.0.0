<x-admin>
    <div class="container locations">
        <div class="custom_form">
            <h1>New Location</h1>
            <form action="{{ route('locations.store') }}" method="post">
                @csrf

                <div class="input_group">
                    <label for="location_name">Location Name</label>
                    <input type="text" name="location_name" id="location_name" value="{{ old('location_name') }}" required autofocus />
                    <span class="inline_alert">{{ $errors->first('location_name') }}</span>
                </div>

                <button type="submit">Save</button>
            </form>
        </div>
    </div>
</x-admin>
