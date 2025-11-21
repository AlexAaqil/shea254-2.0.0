<x-app-layout>
    <div class="custom_form py-4 max-w-8xl mx-auto">
        <div class="header">
            <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}" wire:navigate>
                <x-svgs.arrow-left class="w-5 h-5" />
            </a>
            <h2>Create New Product</h2>
        </div>

        <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="title" class="required">Title</label>
                    <input type="text" name="title" id="title" autocomplete="title" value="{{ old('title') }}" autofocus>
                    <x-form-input-error field="title" />
                </div>

                <div class="inputs">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                        @endforeach
                    </select>
                    <x-form-input-error field="category_id" />
                </div>

                <div class="inputs">
                    <label for="product_code">Product Code</label>
                    <input type="text" name="product_code" id="product_code" autocomplete="product_code" value="{{ old('product_code', 0) }}">
                    <x-form-input-error field="product_code" />
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="featured">
                        <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                        Featured Product
                    </label>
                    <x-form-input-error field="featured" />
                </div>

                <div class="inputs">
                    <label for="is_visible">
                        <input type="checkbox" name="is_visible" id="is_visible" value="1" {{ old('is_visible', true) ? 'checked' : '' }}>
                        Visible to Customers
                    </label>
                    <x-form-input-error field="is_visible" />
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="stock_count">Stock Count</label>
                    <input type="number" name="stock_count" id="stock_count" placeholder="Stock in hand" value="{{ old('stock_count', 0) }}" />
                    <x-form-input-error field="stock_count" />
                </div>

                <div class="inputs">
                    <label for="safety_stock">Safety Stock Count</label>
                    <input type="number" name="safety_stock" id="safety_stock" placeholder="Safety Stock Count" value="{{ old('safety_stock', 0) }}" />
                    <x-form-input-error field="safety_stock" />
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="buying_price" class="required">Buying Price</label>
                    <input type="number" step="0.01" name="buying_price" id="buying_price" value="{{ old('buying_price', 0.00) }}" placeholder="Enter the Buying Price eg. 300.00" />
                    <x-form-input-error field="buying_price" />
                </div>

                <div class="inputs">
                    <label for="selling_price" class="required">Selling Price</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', 0.00) }}" placeholder="Enter the Buying Price eg. 500.00" />
                    <x-form-input-error field="selling_price" />
                </div>

                <div class="inputs">
                    <label for="discount_price">Discount Price (Price after discount)</label>
                    <input type="number" step="0.01" name="discount_price" id="discount_price" value="{{ old('discount_price', 0.00) }}" placeholder="Enter the Price after discount eg. 200.00" />
                    <x-form-input-error field="discount_price" />
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="product_measurement">Product Measurement</label>
                    <input type="number" name="product_measurement" id="product_measurement" value="{{ old('product_measurement') }}" placeholder="Eg. 200">
                    <span class="inline_alert">{{ $errors->first('product_measurement') }}</span>
                </div>

                <div class="inputs">
                    <label for="measurement_unit">Measurement Unit</label>
                    <select name="measurement_unit" id="measurement_unit">
                        <option value="">Select Measurement Unit</option>
                        @foreach($measurements as $measurement)
                            <option value="{{ $measurement->measurement_name }}" {{ old('measurement_unit') == $measurement->measurement_name ? 'selected' : '' }}>{{ $measurement->measurement_name }}</option>
                        @endforeach
                    </select>
                    <span class="inline_alert">{{ $errors->first('measurement_unit') }}</span>
                </div>

                <div class="inputs">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" min="1" value={{ old('sort_order') }}>
                    <span class="inline_alert">{{ $errors->first('sort_order') }}</span>
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="image">Images (Max allowed images is 5 and < 2MB)</label>
                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg, .webp, .svg" multiple>
                    <x-form-input-error field="images.*" />
                </div>
            </div>

            <div class="inputs">
                <label for="description">Description</label>
                <textarea name="description" id="ckeditor" cols="30" rows="10">{{ old('description') }}</textarea>
                <x-form-input-error field="description" />
            </div>

            <div class="buttons_group">
                <button type="submit">Save Product</button>
                <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
        <x-ckeditor />
    @endpush
</x-app-layout>

