<x-app-layout>
    <div class="custom_form py-4 max-w-8xl mx-auto">
        <div class="header">
            <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}" wire:navigate>
                <x-svgs.arrow-left class="w-5 h-5" />
            </a>
            <h2>Update Product</h2>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="title" class="required">Title</label>
                    <input type="text" name="title" id="title" autocomplete="title" value="{{ old('title', $product->title) }}" autofocus>
                    <x-form-input-error field="title" />
                </div>

                <div class="inputs">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                        @endforeach
                    </select>
                    <x-form-input-error field="category_id" />
                </div>

                <div class="inputs">
                    <label for="product_code">Product Code</label>
                    <input type="text" name="product_code" id="product_code" autocomplete="product_code" value="{{ old('product_code', $product->product_code) }}">
                    <x-form-input-error field="product_code" />
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="featured">
                        <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                        Featured Product
                    </label>
                    <x-form-input-error field="featured" />
                </div>

                <div class="inputs">
                    <label for="is_visible">
                        <input type="checkbox" name="is_visible" id="is_visible" value="1" {{ old('is_visible', $product->is_visible) ? 'checked' : '' }}>
                        Visible to Customers
                    </label>
                    <x-form-input-error field="is_visible" />
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="stock_count">Stock Count</label>
                    <input type="number" name="stock_count" id="stock_count" placeholder="Stock in hand" value="{{ old('stock_count', $product->stock_count) }}" />
                    <x-form-input-error field="stock_count" />
                </div>

                <div class="inputs">
                    <label for="safety_stock">Safety Stock Count</label>
                    <input type="number" name="safety_stock" id="safety_stock" placeholder="Safety Stock Count" value="{{ old('safety_stock', $product->safety_stock) }}" />
                    <x-form-input-error field="safety_stock" />
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="buying_price" class="required">Buying Price</label>
                    <input type="number" step="0.01" name="buying_price" id="buying_price" value="{{ old('buying_price', $product->buying_price) }}" placeholder="Enter the Buying Price eg. 300.00" />
                    <x-form-input-error field="buying_price" />
                </div>

                <div class="inputs">
                    <label for="selling_price" class="required">Selling Price</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" placeholder="Enter the Buying Price eg. 500.00" />
                    <x-form-input-error field="selling_price" />
                </div>

                <div class="inputs">
                    <label for="discount_price">Discount Price (Price after discount)</label>
                    <input type="number" step="0.01" name="discount_price" id="discount_price" value="{{ old('discount_price', $product->discount_price) }}" placeholder="Enter the Price after discount eg. 200.00" />
                    <x-form-input-error field="discount_price" />
                </div>
            </div>

            <div class="input_group">
                <h3>Wholesale Price Tiers</h3>

                <div id="price_tiers">
                    @php
                        $tiers = old('price_tiers', $product->priceTiers->toArray() ?? []);
                    @endphp

                    @forelse($tiers as $index => $tier)
                        <div class="tier flex gap-2 mb-2">
                            <input type="number" name="price_tiers[{{ $index }}][min_quantity]" value="{{ $tier['min_quantity'] ?? '' }}" placeholder="Min Quantity">

                            <input type="number" name="price_tiers[{{ $index }}][price]" value="{{ $tier['price'] ?? '' }}" placeholder="Tier Price">

                            <button class="remove_tier">X</button>
                        </div>
                    @empty
                        {{-- No tiers initially --}}
                    @endforelse
                </div>

                <button type="button" id="add_tier" class="btn">+ Add Tier</button>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="product_measurement">Product Measurement</label>
                    <input type="number" name="product_measurement" id="product_measurement" value="{{ old('product_measurement', $product->product_measurement) }}" placeholder="Eg. 200">
                    <span class="inline_alert">{{ $errors->first('product_measurement') }}</span>
                </div>

                <div class="inputs">
                    <label for="measurement_id">Measurement Unit</label>
                    <select name="measurement_id" id="measurement_id">
                        <option value="">Select Measurement Unit</option>
                        @foreach($measurements as $measurement)
                            <option value="{{ $measurement->id }}" {{ old('measurement_id', $product->measurement_id) == $measurement->id ? 'selected' : '' }}>{{ $measurement->measurement_name }}</option>
                        @endforeach
                    </select>
                    <span class="inline_alert">{{ $errors->first('measurement_id') }}</span>
                </div>

                <div class="inputs">
                    <label for="product_order">Sort Order</label>
                    <input type="number" name="product_order" id="product_order" min="1" value={{ old('product_order', $product->product_order) }}>
                    <span class="inline_alert">{{ $errors->first('product_order') }}</span>
                </div>
            </div>

            <div class="inputs_group_3">
                <div class="inputs">
                    <label for="image">Images (Max allowed images is 5 and < 2MB)</label>
                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg, .webp, .svg" multiple>
                    <x-form-input-error field="images.*" />
                </div>
            </div>

            @if($product->product_images->count() > 0)
            <div class="existing_images mt-4">
                <h4 class="text-base font-medium mb-3">Existing Images</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4" id="product_images">
                    @foreach($product->product_images as $image)
                        @php
                            $image_url = Storage::url($image->image);
                        @endphp

                        <div class="product_image relative border rounded-lg p-2" data-image-id="{{ $image->id }}">
                            <img src="{{ $image_url }}" 
                                alt="Product Image" 
                                class="w-full h-32 object-cover rounded">

                            <button type="submit" 
                                form="deleteImageForm"
                                formaction="{{ route('product-images.delete', $image->id) }}"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs hover:bg-red-600 transition-colors btn_danger"
                                onclick="return confirm('Are you sure you want to delete this image?')"
                                title="Delete this image">
                                X
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="inputs">
                <label for="description">Description</label>
                <textarea name="description" id="ckeditor" cols="30" rows="10">{{ old('description', $product->description) }}</textarea>
                <x-form-input-error field="description" />
            </div>

            <div class="buttons_group">
                <button type="submit">Update Product</button>
                <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>

        <form action="" method="POST" id="deleteImageForm">
            @csrf
            @method('DELETE')
            <!-- This form will be dynamically updated -->
        </form>
    </div>

    @push('scripts')
        <x-ckeditor />

        <script>
            document.getElementById('add_tier').addEventListener('click', function () {
                let container = document.getElementById('price_tiers');
                let index = container.children.length;
                let html = `
                    <div class="tier flex gap-2 mb-2">
                        <input type="number" name="price_tiers[${index}][min_quantity]" placeholder="Min Quantity" class="input" />
                        <input type="number" step="0.01" name="price_tiers[${index}][price]" placeholder="Tier Price" class="input" />
                        <button type="button" class="remove_tier">✕</button>
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
            });

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove_tier')) {
                    e.target.closest('.tier').remove();
                }
            });
        </script>
    @endpush
</x-app-layout>

