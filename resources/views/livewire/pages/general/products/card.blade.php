<div class="product_card card">
    <div class="image">
        <div class="skeleton"></div>
        <img 
            src="{{ $product->image_url }}" 
            alt="{{ $product->name }}" 
            loading="lazy"
            onload="this.classList.add('loaded'); this.previousElementSibling.remove();"
        >
        <!-- <img src="{{ $product->image_url }}" alt="{{ $product->slug }}"> -->

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
                <a
                    @if($product->category_slug)
                        href="{{ route('products-categorized-page', $product->category_slug) }}" wire:navigate
                    @else
                        class="disabled-link" aria-disabled="true"
                    @endif
                >
                    {{ $product->category_title }}
                </a>
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
            @if ($product->has_discount)
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
