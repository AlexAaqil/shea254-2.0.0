<x-admin>
    <section class="Products">
        <div class="container products">
            @include('admin.partials.products_navbar')
    
            <div class="header">
                <h1>Products <span>({{ $products->count() }})</span></h1>
                @include('partials.js_search')
                <div class="header_btn">
                    <a href="{{ route('products.create') }}">New</a>
                </div>
            </div>
    
            <div class="body">
                <div class="card_wrapper products_wrapper">
                    @foreach($products as $product)
                    <div class="card product_card searchable {{ $product->is_visible && $product->is_visible == 1 ? '' : 'invisible_product' }}">
                        <div class="image">
                            <a href="{{ route('products.edit', ['product' => $product->id]) }}" class="title">
                                <img src="{{ $product->getFirstImage() }}" alt="Product">
                            </a>

                            @if($product->discount_price != 0.00 && $product->discount_price < $product->selling_price)
                                <span class="percentage_discount">
                                    {{ round($product->calculateDiscount()) }}% off
                                </span>
                            @endif

                            <div class="actions">
                                <form id="deleteForm_{{ $product->id }}" action="{{ route('products.destroy', ['product' => $product->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <a href="javascript:void(0);" onclick="deleteItem({{ $product->id }}, 'product');">
                                        <i class="fas fa-trash-alt danger"></i>
                                    </a>
                                </form>
                            </div>
                        </div>
    
                        <div class="details">
                            <div class="extra_details">
                                <span>
                                    {{ $product->product_code }}
                                </span>
                                <span>
                                    {{ $product->product_category ? $product->product_category->title : 'no category' }}
                                </span>
                                <span class="{{ $product->stock_count == 0 ? 'out_of_stock' : 'in_stock' }}">
                                    {{ $product->stock_count == 0 ? 'out of stock' : 'in stock (' . $product->stock_count . ')' }}
                                </span>
                                <span class="{{ $product->featured == 1 ? 'featured' : 'not_featured'}}">
                                    {{ $product->featured == 1 ? 'featured' : 'not featured'}}
                                </span>
                                <span class="{{ $product->is_visible == 1 ? 'featured' : 'not_featured'}}">
                                    {{ $product->is_visible == 1 ? '' : 'not visible'}}
                                </span>
                            </div>
                            
                            <div class="content">
                                <div class="info">
                                    <div class="title">
                                        <a href="{{ route('products.edit', ['product' => $product->id]) }}">
                                            {{ $product->title }}
                                        </a>
                                    </div>

                                    <div class="price_rating">
                                        <span class="price">
                                            @if($product->discount_price != 0.00 && $product->discount_price < $product->selling_price)
                                                <span class="new_price">Ksh. {{ $product->discount_price }}</span>
                                                <span class="old_price"><del>{{ $product->selling_price }}</del></span>
                                            @else
                                                <span class="new_price">Ksh. {{ $product->selling_price }}</span>
                                            @endif
                                        </span>

                                        @if($product->product_reviews->count() > 0)
                                            <span class="rating">
                                                <span><i class="fas fa-star"></i> {{ number_format($product->average_rating(), 1) }} ({{ $product->product_reviews->count() }} reviews)</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <x-sweetalert></x-sweetalert>
</x-admin>
