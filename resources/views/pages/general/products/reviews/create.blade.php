<x-guest-layout>
    <div class="ProductReviewsPage">
        <div class="container">
            <div class="product">
                <h1>Product Review</h1>
                <p>{{ $product->title }}</p>
                <div class="image">
                    <img src="{{ $product->image_url }}" alt="{{ $product->title }}">
                </div>
                <p>
                    <span>Rating : </span>
                    @if($product->product_reviews->isNotEmpty())
                        {{ number_format($product->product_reviews->avg('rating'), 1) }} / 5
                    @else
                        <span>No ratings yet</span>
                    @endif
                </p>
            </div>

            @auth
                @if(!$alreadyReviewed)
                    <div class="custom_form">
                        <form action="{{ route('product-reviews.store', $product->slug) }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="inputs">
                                <label for="rating">Rating out of 5</label>
                                <div class="rating">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                                        <label for="{{ $i }}">☆</label>
                                    @endfor
                                </div>
                                <x-form-input-error field="rating" />
                            </div>

                            <div class="inputs">
                                <label for="review">Review</label>
                                <textarea name="review" cols="30" rows="7" placeholder="Tell us your thoughts on this product">{{ old('review') }}</textarea>
                                <x-form-input-error field="review" />
                            </div>

                            <div class="actions">
                                <button class="btn">Submit Review</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="custom_form">
                        <p class="text-green-700 font-semibold mt-6 text-center">
                            ✅ You have already submitted a review for this product.
                        </p>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</x-guest-layout>
