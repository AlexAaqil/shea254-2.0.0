<div class="Products">
    <section class="ProductDetails">
        <div class="details container">
            <div class="images" x-data="{ active_image: '{{ $product->image_url ?? asset('assets/images/default-image.jpg') }}' }">
                <div class="main_image">
                    <div class="image">
                        <img :src="active_image" alt="{{ $product->slug }}" id="active_image" />
                    </div>
                </div>

                <div class="other_images">
                    @forelse($product->product_images as $image)
                        @php
                            $imageUrl = Storage::url('products/images/' . $image->image);
                        @endphp

                        <div class="image" @click="active_image = '{{ $imageUrl }}'">
                            <img
                                src="{{ $imageUrl }}"
                                alt="Other Image"
                                :class="{ 'active ring-2 ring-blue-500': active_image === '{{ $imageUrl }}' }"
                                class="transition duration-200"
                            />
                        </div>
                    @empty
                        <p>No other images available</p>
                    @endforelse
                </div>
            </div>

            <div class="content">
                <h3 class="title">
                    @if($product->slug)
                        <a href="{{ Route::has('product-details-page') ? route('product-details-page', $product->slug) : '#' }}" wire:navigate>
                            {{ $product->title }}
                        </a>
                    @else
                        <span>{{ $product->title }} ---</span>
                    @endif
                </h3>

                <p class="product_price">
                    <span class="selling_price">
                        Ksh. {{ number_format($product->effective_price, 2) }}
                    </span>
                    @if ($product->discount_price && $product->discount_price < $product->selling_price)
                        <span class="discount_price">
                            {{ number_format($product->selling_price, 2) }}
                        </span>
                        <span class="discount_percentage">
                            {{ $product->discount_percentage }}% off
                        </span>
                    @endif
                </p>

                <div class="actions">
                    <button class="btn">Add to Cart</button>
                </div>

                <div class="extras">
                    <p>
                        <span>Category</span>
                        <span>
                            : @if($product->category_slug)
                                <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', $product->category_slug) : '#' }}" wire:navigate>
                                    {{ $product->category_title }}
                                </a>
                            @else
                                Uncategorized
                            @endif
                        </span>
                    </p>
                    <p>
                        <span>In stock</span>
                        <span>: {{ $product->stock_count }}</span>
                    </p>
                    <p>
                        <span>Measurement</span>
                        <span>: {{ $product->product_measurement . ' ' . $product->measurement_unit->measurement_name }}</span>
                    </p>
                    <p>
                        <span>Rating</span>
                        <span>: {{ number_format($product->product_reviews->avg('rating'), 1) }} / 5</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="more_details container">
            <div class="description">
                {!! $product->description !!}
            </div>

            <div class="reviews">
                {{-- Reviews will go here --}}
            </div>
        </div>
    </section>

    <section class="RelatedProducts">
        <div class="container">
            <h2 class="related_products_title">You may also like</h2>
            <div class="products_list custom_cards">
                @forelse($related_products as $product)
                    @include('livewire.pages.general.products.card')
                @empty
                    <p>No related products found</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
