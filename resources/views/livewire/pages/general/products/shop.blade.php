<div class="ShopPage bg-slate-50">
    <section class="Hero">
        <div class="container flex justify-center">
            <div class="search md:w-[50dvh]">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search {{ $count_products }} {{ Str::plural('product', $count_products) }} by title..."
                        wire:model="search"
                        wire:keydown.enter="performSearch"
                        class="w-full bg-white border border-gray-300 rounded-full py-2 px-4 pr-8"
                    >
                    @if($search)
                        <button
                            wire:click="resetSearch"
                            class="absolute font-bold right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            X
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="Categories">
        <div class="container">
            <div class="categories_list flex gap-4">
                @if($categories->count() > 0)
                    <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}" class="text-sm" wire:navigate>All Categories</a>
                    @foreach ($categories as $category)
                        <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', $category->slug) : '#' }}" class="text-blue-500 text-sm" wire:navigate>
                            {{ $category->title }}
                        </a>
                    @endforeach
                @else
                    <p>No categories yet.</p>
                @endif
            </div>
        </div>
    </section>

    <section class="Products">
        <div class="container">
            <div class="products_list custom_cards">
                @forelse($products as $product)
                    @include('livewire.pages.general.products.card')
                @empty
                    <p>No products yet.</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="pagination mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </section>
</div>
