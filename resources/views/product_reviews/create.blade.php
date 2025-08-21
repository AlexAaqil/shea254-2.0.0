<x-app-layout>
    @include('partials.navbar')
    <main class="Product_details" style="background: linear-gradient(135deg, #f8fafc, #ffffff); padding: 4em 1em;">
    @include('partials.messages')

    <div class="container product_review_form" style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 3em;">
        <div class="product" style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 2em; text-align: center;">
            <h1 style="font-size: clamp(1.8rem, 5vw, 2.2rem); color: #1a237e; font-weight: 800; margin-bottom: 0.5em;">Product Review</h1>
            <p style="font-size: clamp(1rem, 4.5vw, 1.2rem); color: #1a237e; margin-bottom: 1em;">{{ $product->title }}</p>
            <img src="{{ $product->getFirstImage() }}" alt="{{ $product->title }}" style="max-width: 300px; height: auto; margin-bottom: 1em;">
            <p class="rating" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b;">
                <span>Rating : </span>
                @if($product->product_reviews->isNotEmpty())
                    </span>
                        {{ number_format($product->product_reviews->avg('rating'), 1) }} / 5
                    <span>
                @else
                    <span>No ratings yet</span>
                @endif
            </p>
        </div>

        <div class="custom_form" style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 2em;">
            <form action="{{ route('product-reviews.store', ['product' => $product->id]) }}" method="post">
                @csrf

                <div class="input_group" style="margin-bottom: 1.5em;">
                    <label for="rating" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Rating out of 5</label>
                    <div class="rating" style="font-size: 1.5em; color: #64748b;"> 
                        <input type="radio" name="rating" value="5" id="5" {{ old('rating') == '5' ? 'checked' : '' }} style="display: none;">
                        <label for="5" style="cursor: pointer;">☆</label> 
                        
                        <input type="radio" name="rating" value="4" id="4" {{ old('rating') == '4' ? 'checked' : '' }} style="display: none;">
                        <label for="4" style="cursor: pointer;">☆</label> 
                        
                        <input type="radio" name="rating" value="3" id="3" {{ old('rating') == '3' ? 'checked' : '' }} style="display: none;">
                        <label for="3" style="cursor: pointer;">☆</label> 
                        
                        <input type="radio" name="rating" value="2" id="2" {{ old('rating') == '2' ? 'checked' : '' }} style="display: none;">
                        <label for="2" style="cursor: pointer;">☆</label> 
                        
                        <input type="radio" name="rating" value="1" id="1" {{ old('rating') == '1' ? 'checked' : '' }} style="display: none;">
                        <label for="1" style="cursor: pointer;">☆</label>
                    </div>
                    <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('rating') }}</span>
                </div>

                <div class="input_group" style="margin-bottom: 1.5em;">
                    <label for="review" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #1a237e; font-weight: 600;">Review</label>
                    <textarea name="review" id="review" cols="30" rows="7" placeholder="Your thoughts about this product" style="padding: 0.75em; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #e2e8f0; border-radius: 12px;">{{ old('review') }}</textarea>
                    <span class="inline_alert" style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #ef4444;">{{ $errors->first('review') }}</span>
                </div>

                <button type="submit" style="display: inline-block; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); border: none; cursor: pointer; transition: background 0.3s ease;">Send Review</button>
            </form>
        </div>
    </div>
</main>
</x-app-layout>