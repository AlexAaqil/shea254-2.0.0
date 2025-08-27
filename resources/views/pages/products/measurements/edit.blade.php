<x-app-layout>
    <div class="custom_form py-4 max-w-4xl mx-auto">
        <div class="header">
            <a href="{{ Route::has('product-measurements.index') ? route('product-measurements.index') : '#' }}" wire:navigate>
                <x-svgs.arrow-left class="w-5 h-5" />
            </a>
            <h2>Update Product Measurement</h2>
        </div>

        <form action="{{ route('product-measurements.update', $product_measurement->id) }}" method="post">
            @csrf
            @method('PATCH')

            <div class="inputs">
                <label for="measurement_name" class="required">Title</label>
                <input type="text" name="measurement_name" id="measurement_name" autocomplete="measurement_name" value="{{ old('measurement_name', $product_measurement->measurement_name) }}">
                <x-form-input-error field="measurement_name" />
            </div>

            <div class="buttons_group">
                <button type="submit">Update Product Measurement</button>
                <a href="{{ Route::has('product-measurements.index') ? route('product-measurements.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

