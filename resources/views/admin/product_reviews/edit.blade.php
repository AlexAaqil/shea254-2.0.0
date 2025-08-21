<x-admin>
    <div class="Product_details">
        <div class="container product_reviews_update">
            <div class="custom_form">
                <div class="header">
                    <h1>Update Product Review</h1>
                    <p>
                        {{ $product_review->user->first_name . ' ' . $product_review->user->last_name }}
                    </p>
                    <p>
                        {{ $product_review->user->email }}
                    </p>
                    <p>
                        {{ $product_review->user->phone_number }}
                    </p>
                </div>
    
                <form action="{{ route('product-reviews.update', ['product_review' => $product_review->id]) }}" method="post">
                    @csrf
                    @method('PATCH')

                    <p class="product_details">
                        {{ $product_review->product->title }}
                    </p>
                    
                    <p class="review">
                        {{ $product_review->review }}
                        <span class="rating">
                            <i class="fas fa-star"></i> 
                            {{ $product_review->rating }}
                        </span>
                    </p>
    
                    <div class="row_input_group">
                        <div class="input_group">
                            <label for="is_visible">Is Visible</label>
                            <div class="custom_radio_buttons">
                                <label>
                                    <input class="option_radio" type="radio" name="is_visible" id="is_visible" value="1" {{ $product_review->is_visible ==1 ? 'checked' : '' }}>
                                    <span>Yes</span>
                                </label>
    
                                <label>
                                    <input class="option_radio" type="radio" name="is_visible" id="not_visible" value="0" {{ $product_review->is_visible == 0 ? 'checked' : '' }}>
                                    <span>No</span>
                                </label>
                            </div>
                            <span class="inline_alert">{{ $errors->first('is_visible') }}</span>
                        </div>
    
                        <div class="input_group">
                            <label for="ordering">Ordering</label>
                            <input type="number" name="ordering" id="ordering" value="{{ old('ordering', $product_review->ordering) }}" />
                            <span class="inline_alert">{{ $errors->first('ordering') }}</span>
                        </div>
                    </div>
    
                    <button type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
</x-admin>
