<div class="card product_card" style="border: 1px solid #e0e0e0; border-radius: 10px;  background-color: #fff; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease-in-out;">
    <div class="image" style="position: relative; overflow: hidden; border-bottom: 2px solid #f0f0f0;">
        <a href="{{ Route::has('products.details') ? route('products.details', ['slug' => $product->slug]) : '#' }}">
            <img src="{{ $product->getFirstImage() }}" alt="{{ $product->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease-in-out;">
        </a>

        @if($product->discount_price != 0.00 && $product->discount_price < $product->selling_price)
            <span class="percentage_discount" style="position: absolute; top: 10px; left: 10px; background-color: #1a237e; color: #fff; padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 14px; z-index: 10;">
                {{ round($product->calculateDiscount()) }}% off
            </span>
        @endif

        @if ($product->stock_count > 0)
        <div class="actions" style="position: absolute; bottom: 10px; right: 10px;">
            <div class="action">
                <form action="{{ Route::has('cart.store') ? route('cart.store', $product->id) : '#' }}" method="POST" onsubmit="fbq('track', 'AddToCart', { content_name: '{{ $product->title }}', content_ids: ['{{ $product->id }}'], content_type: 'product', value: {{ $product->discount_price != 0.00 && $product->discount_price < $product->selling_price ? $product->discount_price : $product->selling_price }}, currency: 'KES', content_category: '{{ $product->product_category ? $product->product_category->title : "" }}', });">
                    @csrf
                    <button type="submit" title="Add to Cart" style="background-color: #1a237e; border: none; padding: 10px 15px; color: white; font-size: 16px; border-radius: 5px; cursor: pointer; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); transition: background-color 0.3s ease;">
                        <i class="fa fa-cart-plus" style="font-size: 18px;"></i>
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="details" style="padding: 15px; background-color: #f9f9f9;">
        <div class="extra_details" style="display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #555;">
            @if($product->product_category)
                <span>
                    <a href="{{ Route::has('products.categorized') ? route('products.categorized', $product->product_category->slug) : '#' }}" style="text-decoration: none; color: #1a237e; font-weight: bold;">
                        {{ $product->product_category->title }}
                    </a>
                </span>
            @endif

            @if($product->stock_count <= 0)
                <span class="danger" style="color: #e53935; font-weight: bold; font-size: 14px;">out of stock</span>
            @endif
        </div>

        <div class="content" style="margin-top: 10px;">
            <div class="info">
                <div class="title" style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 5px; display: flex; align-items: center; gap: 8px;">
                    <a href="{{ Route::has('products.details') ? route('products.details', ['slug' => $product->slug]) : '#' }}" style="text-decoration: none; color: #333; transition: color 0.3s ease;">{{ $product->title }}</a>
                    <div class="tooltip-container">
                        <i class="fas fa-info-circle info-icon" style="color: #1a237e; cursor: pointer; font-size: 16px;"></i>
                        <div class="tooltip-content description-tooltip">
                            {{-- Use a helper function to clean and format the HTML --}}
                            @php
                                $description = preg_replace('/<\/?(?:p|li|ol|ul)[^>]*>/', '', $product->description);
                                $description = preg_replace('/data-sourcepos="[^"]*"/', '', $description);
                                $description = preg_replace('/<\/strong>\s*/', '</strong>: ', $description);

                                // Split into ingredients
                                $ingredients = explode('<strong>', $description);
                                array_shift($ingredients); // Remove empty first element
                            @endphp

                            <ul class="ingredients-list">
                                @foreach($ingredients as $ingredient)
                                    <li>
                                        <p><strong>{!! explode('</strong>', $ingredient)[0] !!}</strong>
                                        {!! explode('</strong>', $ingredient)[1] ?? '' !!}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="price_rating" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="price" style="font-size: 18px; font-weight: bold; color: #333;">
                        @if($product->discount_price != 0.00 && $product->discount_price < $product->selling_price)
                            <span class="new_price" style="color: #1a237e;">Ksh. {{ $product->discount_price }}</span>
                            <span class="old_price" style="color: #bbb; text-decoration: line-through; margin-left: 5px;">Ksh. {{ $product->selling_price }}</span>
                        @else
                            <span class="new_price" style="color: #1a237e;">Ksh. {{ $product->selling_price }}</span>
                        @endif
                    </span>

                    @if($product->product_reviews->count() > 5)
                        <span class="rating" style="font-size: 14px; color: #1a237e;">
                            <span><i class="fas fa-star" style="color: #1a237e;"></i> {{ number_format($product->average_rating(), 1) }} ({{ $product->product_reviews->count() }} reviews)</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .product_card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .product_card .image img:hover {
        transform: scale(1.1);
    }

    .product_card .title a:hover {
        color: #1a237e;
    }

    .product_card .percentage_discount {
        font-size: 14px;
    }

    .actions button:hover {
        background-color: #0d1a56;
    }

    /* New styles for tooltip */
    .tooltip-container {
        position: relative;
        display: inline-block;
    }

    .tooltip-content {
        visibility: hidden;
        position: absolute;
        z-index: 9999999999;
        background-color: #fff;
        color: #333;
        padding: 15px;
        border-radius: 8px;
        width: 300px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.15);
        font-size: 14px;
        font-weight: normal;
        line-height: 1.5;
        left: 50%;
        transform: translateX(-50%);
        bottom: 100%;
        margin-bottom: 12px;
        opacity: 0;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .description-tooltip {
        max-height: 300px;
        overflow-y: auto;
        z-index: 9999;
    }

    .ingredients-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ingredients-list li {
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .ingredients-list li:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .ingredients-list p {
        margin: 0;
    }

    .ingredients-list strong {
        color: #1a237e;
        display: inline-block;
        margin-right: 4px;
    }

    .tooltip-content::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -8px;
        border-width: 8px;
        border-style: solid;
        border-color: #fff transparent transparent transparent;
        filter: drop-shadow(0 3px 3px rgba(0, 0, 0, 0.1));
    }

    .tooltip-container:hover .tooltip-content {
        visibility: visible;
        opacity: 1;
    }

    .info-icon:hover {
        color: #0d1a56;
        transform: scale(1.1);
        transition: all 0.2s ease;
    }
</style>
