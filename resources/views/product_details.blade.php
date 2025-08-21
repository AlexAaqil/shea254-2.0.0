<x-app-layout>
    @include('partials.navbar')
    <main class="Product_details" style="background: linear-gradient(135deg, #f8fafc, #ffffff); padding: 4em 1em;">
        @include('partials.messages')
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <div class="wrapper" style="display: grid; grid-template-columns: 1fr; gap: 3em;">
                <div class="images" style="display: flex; flex-direction: column; align-items: center;">
                    <div class="main_product_image"
                        style="border-radius: 24px; overflow: hidden; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); width: 100%; max-width: 500px;">
                        <img src="{{ $product->getFirstImage() }}" alt="{{ $product->title }}"
                            style="width: 100%; height: auto;">
                    </div>
                    <div class="other_images" style="display: flex; gap: 1em; margin-top: 1.5em;">
                        @foreach($product_images as $image)
                            <img src={{ $image->getProductImageURL() }} alt="Other Image"
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; cursor: pointer; transition: transform 0.3s ease;">
                        @endforeach
                    </div>
                </div>
                <div class="details"
                    style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 2em;">
                    <h1
                        style="font-size: clamp(1.6rem, 5vw, 2.2rem); color: #1a237e; font-weight: 800; margin-bottom: 0.5em;">
                        {{ $product->title }}</h1>
                    <p class="price"
                        style="font-size: clamp(1rem, 4.5vw, 1.5rem); color: #1a237e; font-weight: 600; margin-bottom: 1.5em;">
                        @if($product->discount_price != 0.00 && $product->discount_price < $product->selling_price)
                            <span class="price">
                                <span class="amount" style="color: #1a237e;">Ksh. {{ $product->discount_price }}</span>
                                <span class="discount_price"
                                    style="text-decoration: line-through; color: #64748b; font-size: 0.875rem; margin-left: 0.5rem;">
                                    {{ $product->selling_price }}
                                </span>
                                <span class="discount_percentage"
                                    style="background: #1a237e; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">
                                    {{ round($product->calculateDiscount()) }}% off
                                </span>
                            </span>
                        @else
                            <span class="price">
                                <span class="currency">Ksh.</span>
                                <span class="price_amount">{{ $product->selling_price }}</span>
                            </span>
                        @endif
                    </p>
                    <div class="action" style="display: flex; flex-direction: column; gap: 1em; margin-bottom: 2em;">
                        @if ($product->stock_count > 0)
                            <form action="{{ route('cart.store', $product->id) }}" method="post">
                                @csrf
                                <button type="submit"
                                    style="display: block; width: 100%; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); border: none; cursor: pointer; transition: background 0.3s ease;">
                                    <i class="fas fa-cart-plus add_to_cart_btn" style="margin-right: 0.5rem;"></i> Add to
                                    Cart
                                </button>
                            </form>
                        @else
                            <span class="danger"
                                style="color: #ef4444; text-align: center; font-size: clamp(0.9rem, 4vw, 1rem);"><b>Out of
                                    Stock</b></span>
                        @endif
                        @if(auth()->user() && !auth()->user()->hasReviewedProduct($product->id))
                            <a href="{{ route('product-reviews.create', $product->slug) }}"
                                style="display: block; background: white; color: #1a237e; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); border: 1px solid #1a237e; text-align: center; transition: background 0.3s ease, color 0.3s ease;">Review
                                Product</a>
                        @endif
                    </div>
                    <div class="extras"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1em; font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b;">
                        @if($product->product_reviews->isNotEmpty())
                            <p style="display: flex; justify-content: space-between;">
                                <span>Rating</span>
                                <span><i class="fas fa-star" style="color: #34d399; margin-right: 0.25rem;"></i>
                                    {{ number_format($product->product_reviews->avg('rating'), 1) }} / 5</span>
                            </p>
                        @endif
                        @if($product->product_measurement && $product->measurement_id)
                            <p style="display: flex; justify-content: space-between;">
                                <span>Size</span>
                                <span>{{ $product->product_measurement . ' ' . $product->measurement_unit->measurement_name }}</span>
                            </p>
                        @endif
                        @if($product->category_id != null)
                            <p style="display: flex; justify-content: space-between;">
                                <span>Category</span>
                                <span>
                                    <a href="{{ route('products.categorized', $product->product_category->slug) }}"
                                        style="color: #64748b; text-decoration: none; transition: color 0.3s ease;">
                                        {{ $product->product_category->title }}
                                    </a>
                                </span>
                            </p>
                        @endif
                    </div>
                    <div class="description" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b; line-height: 1.6; margin-top: 2em;">
    <div id="short-description" style="transition: max-height 0.3s ease-out;">
        {!! Illuminate\Support\Str::limit($product->description, 500) !!}
    </div>
    
    <div id="full-description" style="display: none; transition: max-height 0.3s ease-in;">
        {!! $product->description !!}
    </div>
    
    <button id="see-more-btn" 
        style="display: inline-block; 
               background: transparent; 
               color: #1a237e; 
               border: none; 
               padding: 0.5em 0; 
               margin-top: 1em; 
               font-size: 0.9rem; 
               font-weight: 600; 
               cursor: pointer; 
               transition: color 0.3s ease;"
        onclick="toggleDescription()">
        See More <i class="fas fa-chevron-down" style="margin-left: 0.3em; transition: transform 0.3s ease;"></i>
    </button>
</div>

                </div>
            </div>
        </div>
        @if($product->product_reviews->isNotEmpty())
            <div class="product_reviews" style="margin-top: 4em;">
                <div class="container" style="max-width: 1200px; margin: 0 auto;">
                    <h1
                        style="font-size: clamp(1.6rem, 5vw, 2.2rem); color: #1a237e; font-weight: 800; margin-bottom: 1.5em;">
                        Reviews</h1>
                    <div class="reviews_wrapper"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2em;">
                        @foreach($product->product_reviews as $product_review)
                            <div class="review"
                                style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 1.5em;">
                                <p style="font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b; line-height: 1.6;">
                                    {{ $product_review->review }}</p>
                                <p class="details"
                                    style="font-size: clamp(0.8rem, 3.5vw, 0.9rem); color: #1a237e; margin-top: 1em;">
                                    <span class="username"
                                        style="font-weight: 600;">{{ $product_review->user->first_name . ' ' . $product_review->user->last_name  }}</span>
                                    <span class="date"
                                        style="color: #64748b;">{{ $product_review->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        @if($related_products->count() > 0)
            <div class="container related_products" style="margin-top: 4em;">
                <h1 style="font-size: clamp(1.6rem, 5vw, 2.2rem); color: #1a237e; font-weight: 800; margin-bottom: 1.5em;">
                    Related Products</h1>
                <div class="card_wrapper products_wrapper"
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2em;">
                    @foreach($related_products as $product)
                        @include('partials.product')
                    @endforeach
                </div>
            </div>
        @endif
    </main>
    <x-slot name="javascript">
        <script>
            function toggleDescription() {
    const shortDesc = document.getElementById('short-description');
    const fullDesc = document.getElementById('full-description');
    const button = document.getElementById('see-more-btn');
    const icon = button.querySelector('i');
    
    if (fullDesc.style.display === 'none') {
        // Show full description
        shortDesc.style.display = 'none';
        fullDesc.style.display = 'block';
        button.innerHTML = `See Less <i class="fas fa-chevron-up" style="margin-left: 0.3em;"></i>`;
    } else {
        // Show short description
        shortDesc.style.display = 'block';
        fullDesc.style.display = 'none';
        button.innerHTML = `See More <i class="fas fa-chevron-down" style="margin-left: 0.3em;"></i>`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const shortDesc = document.getElementById('short-description');
    const fullDesc = document.getElementById('full-description');
    const button = document.getElementById('see-more-btn');
    
    if (shortDesc.innerText.trim() === fullDesc.innerText.trim()) {
        button.style.display = 'none';
    }
});

            document.addEventListener("DOMContentLoaded", function () {
                // Your existing image zoom code...
                const mainProductImage = document.querySelector(".main_product_image img");
                const otherImagesContainer = document.querySelector(".other_images");
                otherImagesContainer.querySelectorAll("img").forEach((thumbnail) => {
                    thumbnail.addEventListener("click", (event) => {
                        // Remove active class from all thumbnails
                        otherImagesContainer.querySelectorAll("img").forEach((img) => {
                            img.classList.remove("active");
                        });
                        // Add active class to the clicked thumbnail
                        event.target.classList.add("active");

                        // Change the source of the main product image with a zoom effect
                        mainProductImage.src = event.target.src;
                    });
                });

                // Add the zoom effect on hover for the main product image
                mainProductImage.addEventListener("mousemove", (e) => {
                    const containerWidth = mainProductImage.offsetWidth;
                    const containerHeight = mainProductImage.offsetHeight;

                    const image = mainProductImage;
                    const imageWidth = image.offsetWidth;
                    const imageHeight = image.offsetHeight;

                    const x = e.pageX - mainProductImage.offsetLeft;
                    const y = e.pageY - mainProductImage.offsetTop;

                    const translateX = (containerWidth / 2 - x) * 2;
                    const translateY = (containerHeight / 2 - y) * 2;

                    const scale = 3;

                    image.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
                });

                mainProductImage.addEventListener("mouseleave", () => {
                    mainProductImage.style.transform = "translate(0%, 0%) scale(1)";
                });

                // Add Meta Pixel ViewContent tracking
                fbq('track', 'ViewContent', {
                    content_name: '{{ $product->title }}',
                    content_ids: ['{{ $product->id }}'],
                    content_type: 'product',
                    value: {{ $product->discount_price != 0.00 && $product->discount_price < $product->selling_price
    ? $product->discount_price
    : $product->selling_price }},
                    currency: 'KES',
                    content_category: '{{ $product->product_category ? $product->product_category->title : "" }}',
                });

                // Track add to cart events
                const addToCartForm = document.querySelector('form[action*="cart/store"]');
                if (addToCartForm) {
                    addToCartForm.addEventListener('submit', function (e) {
                        fbq('track', 'AddToCart', {
                            content_name: '{{ $product->title }}',
                            content_ids: ['{{ $product->id }}'],
                            content_type: 'product',
                            value: {{ $product->discount_price != 0.00 && $product->discount_price < $product->selling_price
    ? $product->discount_price
    : $product->selling_price }},
                            currency: 'KES',
                            content_category: '{{ $product->product_category ? $product->product_category->title : "" }}',
                        });
                    });
                }
            });
        </script>
        <style>
.description button:hover {
    color: #0d1a56;
}

#short-description, #full-description {
    margin-bottom: 0.5em;
}

/* Ensure proper HTML formatting in description */
.description strong {
    font-weight: 600;
    color: #1a237e;
}

.description ul, .description ol {
    padding-left: 1.5em;
    margin: 1em 0;
}

.description li {
    margin-bottom: 0.5em;
}

.description p {
    margin-bottom: 1em;
}
</style>
    </x-slot>
</x-app-layout>