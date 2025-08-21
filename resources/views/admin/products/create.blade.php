<x-admin>
    <x-slot name='extra_head'>
        <script src="https://cdn.tiny.cloud/1/44zgxk9rangxvjd8no219bfrmnon247y18bl2fylec4zpa55/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    </x-slot>

    <div class="container">
        <div class="custom_form">
            <h1>Add Product</h1>
            <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row_input_group_3">
                    <div class="input_group">
                        <label for="title">Title<span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Title" autofocus />
                        <span class="inline_alert">{{ $errors->first('title') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="product_code">Product Code</label>
                        <input type="number" name="product_code" id="product_code" placeholder="Product Code" value="{{ old('product_code', 0) }}">
                    </div>

                    <div class="input_group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id">
                            <option value="">select</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                            @endforeach
                        </select>
                        <span class="inline_alert">{{ $errors->first('category_id') }}</span>
                    </div>
                </div>

                <div class="row_input_group_3">
                    <div class="input_group">
                        <label for="featured">Featured</label>
                        <div class="custom_radio_buttons">
                            <label>
                                <input class="option_radio" type="radio" name="featured" id="featured" value="1" {{ old('featured') == '1' ? 'checked' : '' }}>
                                <span>Yes</span>
                            </label>

                            <label>
                                <input class="option_radio" type="radio" name="featured" id="not_featured" value="0" {{ old('featured', 0) == '0' ? 'checked' : '' }}>
                                <span>No</span>
                            </label>
                        </div>
                        <span class="inline_alert">{{ $errors->first('featured') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="is_visible">Is Visible?</label>
                        <div class="custom_radio_buttons">
                            <label>
                                <input class="option_radio" type="radio" name="is_visible" id="is_visible" value="1" {{ old('is_visible', 1) == '1' ? 'checked' : '' }}>
                                <span>Yes</span>
                            </label>

                            <label>
                                <input class="option_radio" type="radio" name="is_visible" id="not_visible" value="0" {{ old('is_visible') == '0' ? 'checked' : '' }}>
                                <span>No</span>
                            </label>
                        </div>
                        <span class="inline_alert">{{ $errors->first('is_visible') }}</span>
                    </div>
                </div>

                <div class="row_input_group">
                    <div class="input_group">
                        <label for="stock_count">Stock Count</label>
                        <input type="number" name="stock_count" id="stock_count" placeholder="Stock in hand" value="{{ old('stock_count', 0) }}" />
                        <span class="inline_alert">{{ $errors->first('stock_count') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="safety_stock">Safety Stock</label>
                        <input type="number" name="safety_stock" id="safety_stock" placeholder="Safety Stock" value="{{ old('safety_stock', 0) }}" />
                        <span class="inline_alert">{{ $errors->first('stock_count') }}</span>
                    </div>
                </div>

                <div class="row_input_group_3">
                    <div class="input_group">
                        <label for="buying_price">Buying Price</label>
                        <input type="number" step="0.01" name="buying_price" id="buying_price" value="{{ old('buying_price', 0.00) }}" placeholder="Enter the Buying Price eg. 500.00" />
                        <span class="inline_alert">{{ $errors->first('buying_price') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="selling_price">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', 0.00) }}" placeholder="Enter the Selling Price eg. 700.00." />
                        <span class="inline_alert">{{ $errors->first('selling_price') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="discount_price">Discount Price (New Price)</label>
                        <input type="number" step="0.01" name="discount_price" id="discount_price" value="{{ old('discount_price', 0.00) }}" placeholder="Enter the discount_price." />
                        <span class="inline_alert">{{ $errors->first('discount_price') }}</span>
                    </div>
                </div>

                <div class="row_input_group_3">
                    <div class="input_group">
                        <label for="product_measurement">Product Measurement</label>
                        <input type="number" name="product_measurement" id="product_measurement" value="{{ old('product_measurement') }}" placeholder="Eg. 200">
                        <span class="inline_alert">{{ $errors->first('product_measurement') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="measurement_id">Measurement Unit</label>
                        <select name="measurement_id" id="measurement_id">
                            <option value="">Select Measurement Unit</option>
                            @foreach($measurement_units as $measurement_unit)
                                <option value="{{ $measurement_unit->id }}" {{ old('measurement_id') == $measurement_unit->id ? 'selected' : '' }}>{{ $measurement_unit->measurement_name }}</option>
                            @endforeach
                        </select>
                        <span class="inline_alert">{{ $errors->first('measurement_id') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="product_order">Order</label>
                        <input type="number" name="product_order" id="product_order" min="1" value={{ old('product_order', 200) }}>
                        <span class="inline_alert">{{ $errors->first('product_order') }}</span>
                    </div>
                </div>

                <div class="input_group">
                    <label for="images">Images (Maximum allowed images is 5)</label>
                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg" multiple />
                    <span class="inline_alert">{{ $errors->first('images') }}</span>
                </div>

                <div class="input_group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="7" placeholder="Enter a Description" class="tinymiced">{{ old('description') }}</textarea>
                    <span class="inline_alert">{{ $errors->first('description') }}</span>
                </div>

                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <x-slot name='javascript'>
        <script src="{{ asset('assets/js/tinymce.js') }}"></script>
    </x-slot>
</x-admin>
