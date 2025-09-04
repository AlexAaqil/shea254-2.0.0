<x-app-layout>
    <div class="ProductReviews">
        <div class="custom_form py-4 max-w-4xl mx-auto">
            <div class="header">
                <a href="{{ Route::has('product-reviews.index') ? route('product-reviews.index') : '#' }}" wire:navigate>
                    <x-svgs.arrow-left class="w-5 h-5" />
                </a>
                <h2>Update Product Review</h2>
            </div>

            <form action="{{ route('product-reviews.update', $product_review->id) }}" method="post">
                @csrf
                @method('PATCH')

                <div class="review_details">
                    <p><strong>Product:</strong> {{ $product_review->product->title }}</p>
                    <p><strong>User:</strong> {{ $product_review->user->full_name }} ({{ $product_review->user->email }})</p>
                    <p><strong>Current Rating:</strong> {{ $product_review->rating }} / 5</p>
                    <p><strong>Current Review:</strong> {{ $product_review->review }}</p>
                </div>

                <div class="inputs_group">
                    <div class="inputs">
                        <label for="is_visible">
                            <input type="checkbox" name="is_visible" id="is_visible" value="1" {{ old('is_visible', $product_review->is_visible) ? 'checked' : '' }}>
                            Is Visible
                        </label>
                        <x-form-input-error field="is_visible" />
                    </div>

                    <div class="inputs">
                        <label for="ordering">Sort Order</label>
                        <input type="number" name="ordering" id="ordering" value="{{ old('ordering', $product_review->ordering) }}">
                    </div>
                </div>

                <div class="buttons_group">
                    <button type="submit">Update Product Review</button>
                    <a href="{{ Route::has('product-reviews.index') ? route('product-reviews.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

