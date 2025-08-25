<div class="product_card card">
    <div class="image">
        <img src="{{ $product->image_url }}" alt="{{ $product->slug }}">

        @if ($product->stock_count > 0)
            <div class="cart_btn">
                <button wire:click="addToCart({{ $product->id }})" title="Add to Cart">
                    <x-svgs.add-to-shopping-cart />
                </button>
            </div>
        @endif
    </div>

    <div class="content">
        <div class="extras">
            <span>
                @if($product->category_slug)
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', $product->category_slug) : '#' }}" wire:navigate>
                        {{ $product->category_title }}
                    </a>
                @else
                    Uncategorized
                @endif
            </span>

            @if($product->stock_count <= 0)
                <span class="danger">out of stock</span>
            @endif
        </div>

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
    </div>
</div>
