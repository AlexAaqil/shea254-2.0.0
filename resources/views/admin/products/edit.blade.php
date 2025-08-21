<x-admin>
    <x-slot name='extra_head'>
        <script src="https://cdn.tiny.cloud/1/44zgxk9rangxvjd8no219bfrmnon247y18bl2fylec4zpa55/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    </x-slot>

    <div class="container">
        <div class="custom_form">
            <h1>Update Product</h1>
            <form action="{{ route('products.update', ['product' => $product->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row_input_group_3">
                    <div class="input_group">
                        <label for="title">Title<span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $product->title) }}" placeholder="Title" />
                        <span class="inline_alert">{{ $errors->first('title') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="product_code">Product Code</label>
                        <input type="number" name="product_code" id="product_code" placeholder="Product Code" value="{{ old('product_code', $product->product_code) }}">
                    </div>

                    <div class="input_group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id">
                            <option value="">select</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
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
                                <input class="option_radio" type="radio" name="featured" id="featured" value="1" {{ $product->featured ==1 ? 'checked' : '' }}>
                                <span>Yes</span>
                            </label>

                            <label>
                                <input class="option_radio" type="radio" name="featured" id="not_featured" value="0" {{ $product->featured == 0 ? 'checked' : '' }}>
                                <span>No</span>
                            </label>
                        </div>
                        <span class="inline_alert">{{ $errors->first('featured') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="is_visible">Is Visible?</label>
                        <div class="custom_radio_buttons">
                            <label>
                                <input class="option_radio" type="radio" name="is_visible" id="is_visible" value="1" {{ $product->is_visible ==1 ? 'checked' : '' }}>
                                <span>Yes</span>
                            </label>

                            <label>
                                <input class="option_radio" type="radio" name="is_visible" id="not_visible" value="0" {{ $product->is_visible == 0 ? 'checked' : '' }}>
                                <span>No</span>
                            </label>
                        </div>
                        <span class="inline_alert">{{ $errors->first('is_visible') }}</span>
                    </div>
                </div>

                <div class="row_input_group">
                    <div class="input_group">
                        <label for="stock_count">Stock Count</label>
                        <input type="number" name="stock_count" id="stock_count" placeholder="Stock in hand" value="{{ old('stock_count', $product->stock_count) }}" />
                        <span class="inline_alert">{{ $errors->first('stock_count') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="safety_stock">Safety Stock</label>
                        <input type="number" name="safety_stock" id="safety_stock" placeholder="Safety Stock" value="{{ old('safety_stock', $product->safety_stock) }}" />
                        <span class="inline_alert">{{ $errors->first('stock_stock') }}</span>
                    </div>
                </div>

                <div class="row_input_group_3">
                    <div class="input_group">
                        <label for="buying_price">Buying Price</label>
                        <input type="number" step="0.01" name="buying_price" id="buying_price" value="{{ old('buying_price', $product->buying_price) }}" placeholder="Enter the Buying Price eg. 500.00" />
                        <span class="inline_alert">{{ $errors->first('buying_price') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="selling_price">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" placeholder="Enter the Selling Price eg. 700.00." />
                        <span class="inline_alert">{{ $errors->first('selling_price') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="discount_price">Discount Price (New Price)</label>
                        <input type="number" step="0.01" name="discount_price" id="discount_price" value="{{ old('discount_price', $product->discount_price) }}" placeholder="Enter the discount_price." />
                        <span class="inline_alert">{{ $errors->first('discount_price') }}</span>
                    </div>
                </div>

                <div class="row_input_group_3">
                    <div class="input_group">
                        <label for="product_measurement">Product Measurement</label>
                        <input type="number" name="product_measurement" id="product_measurement" value="{{ old('product_measurement', $product->product_measurement) }}" placeholder="Eg. 200">
                        <span class="inline_alert">{{ $errors->first('product_measurement') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="measurement_id">Measurement Unit</label>
                        <select name="measurement_id" id="measurement_id">
                            <option value="">Select Measurement Unit</option>
                            @foreach($measurement_units as $measurement_unit)
                                <option value="{{ $measurement_unit->id }}" {{ old('measurement_id', $product->measurement_id) == $measurement_unit->id ? 'selected' : '' }}>{{ $measurement_unit->measurement_name }}</option>
                            @endforeach
                        </select>
                        <span class="inline_alert">{{ $errors->first('measurement_id') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="product_order">Sort Order</label>
                        <input type="number" name="product_order" id="product_order" min="1" value={{ old('product_order', $product->product_order) }}>
                        <span class="inline_alert">{{ $errors->first('product_order') }}</span>
                    </div>
                </div>

                <div class="input_group">
                    <label>Wholesale Price Tiers</label>
                    <div id="price-tiers-wrapper">
                        @forelse(old('price_tiers', $product->priceTiers->toArray()) as $index => $tier)
                            <div class="price-tier-row row_input_group_3">
                                <div class="input_group">
                                    <input type="number" name="price_tiers[{{ $index }}][min_quantity]" placeholder="Min Qty" value="{{ $tier['min_quantity'] ?? '' }}" />
                                </div>

                                <div class="input_group">
                                    <input type="number" step="0.01" name="price_tiers[{{ $index }}][price]" placeholder="Unit Price" value="{{ $tier['price'] ?? '' }}" />
                                </div>
                                <button type="button" class="remove-tier">−</button>
                            </div>
                        @empty
                            <div class="price-tier-row row_input_group_3">
                                <div class="input_group">
                                    <input type="number" name="price_tiers[0][min_quantity]" placeholder="Min Qty" />
                                </div>
                                <div class="input_group">
                                    <input type="number" step="0.01" name="price_tiers[0][price]" placeholder="Unit Price" />
                                </div>
                                <button type="button" class="remove-tier">−</button>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" id="add-price-tier">+ Add Tier</button>
                    <span class="inline_alert">{{ $errors->first('price_tiers.*') }}</span>
                </div>

                <div class="input_group">
                    <label for="images">Images (Maximum allowed images is 5)</label>
                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg" multiple />
                    <span class="inline_alert">{{ session('error') ? session('error') : ($errors->has('images') ? $errors->first('images') : '') }}</span>
                </div>

                @if(!empty(session('success')))
                    <span class="inline_alert_success">{{ session('success')['message'] }}</span>
                @endif

                <div class="product_images" id="sortable">
                    @if(!empty($product_images->count()))
                        @foreach ($product_images as $image)
                            @if(!empty($image->getProductImageURL()))
                                <div class="product_image sortable_images" id={{ $image->id }}>
                                    <img src="{{ $image->getProductImageURL() }}" alt="{{ $image->image_name }}" />
                                    <a href="{{ route('delete_product_image', $image->id) }}" >
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="input_group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="7" placeholder="Enter a Description" class="tinymiced">{{ old('description', $product->description) }}</textarea>
                    <span class="inline_alert">{{ $errors->first('description') }}</span>
                </div>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>

    <x-slot name='javascript'>
        <script src="{{ asset('assets/js/tinymce.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.js') }}"></script>
        <script src="{{ asset('assets/js/jquery_ui.js') }}"></script>
        <script>
            $(document).ready(function() {
            $("#sortable").sortable({
                update : function(event, ui) {
                    var photo_id = new Array();
                    $('.sortable_images').each(function() {
                        var id = $(this).attr('id');
                        photo_id.push(id);
                    });

                    $.ajax({
                        type : "POST",
                        url : "{{ url('admin/product/product_images_sort') }}",
                        data : {
                            "photo_id" : photo_id,
                            "_token" : "{{ csrf_token() }}"
                        },
                        dataType : "json",
                        success : function(data) {

                        },
                        error : function (data) {

                        }
                    });
                }
            });
        });
        </script>
        <script>
            $(document).ready(function () {
                let tierIndex = {{ count(old('price_tiers', $product->priceTiers ?? [])) ?: 1 }};

                $('#add-price-tier').click(function () {
                    $('#price-tiers-wrapper').append(`
                        <div class="price-tier-row">
                            <input type="number" name="price_tiers[${tierIndex}][min_quantity]" placeholder="Min Qty" required />
                            <input type="number" step="0.01" name="price_tiers[${tierIndex}][price]" placeholder="Unit Price" required />
                            <button type="button" class="remove-tier">−</button>
                        </div>
                    `);
                    tierIndex++;
                });

                $(document).on('click', '.remove-tier', function () {
                    $(this).closest('.price-tier-row').remove();
                });
            });
        </script>
    </x-slot>
</x-admin>
