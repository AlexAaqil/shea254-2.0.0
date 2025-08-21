<x-admin>
    <div class="container measurements">
        <div class="custom_form">
            <h1>Add Product Measurement</h1>
            <form action="{{ route('product-measurements.store') }}" method="post">
                @csrf

                <div class="input_group">
                    <label for="measurement_name">Measurement Name</label>
                    <input type="text" name="measurement_name" id="measurement_name" value="{{ old('measurement_name') }}" required />
                    <span class="inline_alert">{{ $errors->first('measurement_name') }}</span>
                </div>

                <button type="submit">Save</button>
            </form>
        </div>
    </div>
</x-admin>
