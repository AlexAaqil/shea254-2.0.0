<div class="CategorizedProductsPage">
    <section class="Hero">
        <div class="container">
            <div class="breadcrumbs">
                <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}">Shop</a>
                <span>{{ Str::title($category->title) }}</span>
            </div>
            <h1>{{ Str::title($category->title) }}</h1>
            <p>{{ $products->count() }} {{ Str::plural('product', $products->count()) }} found in the {{ $category->title }} category</p>
        </div>
    </section>

    <section class="Categories">
        <div class="container">
            @forelse($categories as $category)
                <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', $category->slug) : '#' }}" wire:navigate>{{ $category->title }}</a>
            @empty
                <p>No other categories found.</p>
            @endforelse
        </div>
    </section>

    <section class="Products">
        <div class="container">
            <div class="products_list custom_cards">
                @forelse($products as $product)
                    @include('livewire.pages.general.products.card')
                @empty
                    <p>No products were found in this category.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
