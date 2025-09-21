<div class="Products">
    <div class="container Products">
        <div class="breadcrumbs">
            <a href="{{ Route::has('product-categories.index') ? route('product-categories.index') : '#' }}" wire:navigate>Categories</a>
            <a href="{{ Route::has('product-measurements.index') ? route('product-measurements.index') : '#' }}" wire:navigate>Measurements</a>
            <span>Products</span>
        </div>

        <div class="app_header">
            <div class="info">
                <h2>Products</h2>
                <div class="stats">
                    <span 
                        class="{{ $filter === null ? 'font-bold text-blue-600' : 'cursor-pointer hover:underline' }}"
                        wire:click="setFilter">
                        {{ $count_products }} {{ Str::plural('product', $count_products) }}
                    </span>

                    <span 
                        class="{{ $filter === 'featured' ? 'font-bold text-blue-600' : 'cursor-pointer hover:underline' }}"
                        wire:click="setFilter('featured')">
                        {{ $count_featured }} featured
                    </span>

                    <span 
                        class="{{ $filter === 'invisible' ? 'font-bold text-blue-600' : 'cursor-pointer hover:underline' }}"
                        wire:click="setFilter('invisible')">
                        {{ $count_invisible }} invisible
                    </span>

                    <span 
                        class="{{ $filter === 'out_of_stock' ? 'font-bold text-blue-600' : 'cursor-pointer hover:underline' }}"
                        wire:click="setFilter('out_of_stock')">
                        {{ $count_out_of_stock }} out of stock
                    </span>
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by title..."
                        wire:model="search"
                        wire:keydown.enter="performSearch"
                        class="pr-8"
                    >
                    @if($search)
                        <button
                            wire:click="resetSearch"
                            class="absolute right-1 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            X
                        </button>
                    @endif
                </div>
            </div>

            <div class="button">
                <a href="{{ Route::has('products.create') ? route('products.create') : '#' }}" class="btn">New Product</a>
            </div>
        </div>

        <div class="products_list small_cards">
            @forelse($products as $product)
                <div class="product_card card">
                    <div class="details">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->slug }}" class="rounded-lg w-20 h-20 object-cover">
                        @else
                            <span class="bg-red-200 text-xl text-gray-700 rounded-lg w-20 h-20 flex items-center justify-center font-semibold uppercase">{{ substr($product->title, 0, 1) }}</span>
                        @endif

                        <div class="info">
                            <h3>{{ $product->title }}</h3>

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

                            <div class="extras">
                                <div class="extra">
                                    <span>Category:</span>
                                    <span>{{ $product->product_category->title ?? 'Uncategorized' }}</span>
                                </div>

                                <div class="extra">
                                    <span>Code:</span>
                                    <span>{{ $product->product_code ?? 'N/A' }}</span>
                                </div>

                                <div class="extra">
                                    @if($product->stock_count <= 0)
                                        <span class="danger">out of Stock</span>
                                    @elseif($product->stock_count < 5)
                                        <span class="danger">Remaining Items: {{ $product->stock_count }}</span>
                                    @else
                                        <span>In Stock: {{ $product->stock_count }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="actions">
                        <div class="others">
                            <button
                                wire:click="toggleVisibility('{{ $product->id }}')"
                                wire:loading.attr="disabled"
                                wire:target="toggleVisibility"
                                class="{{ $product->is_visible ? 'border border-green-500 bg-green-100 text-green-900 text-xs p-1' : 'border border-red-500 bg-red-100 text-red-900 text-xs p-1' }}">
                                {{ $product->is_visible_label }}
                            </button>

                            <button
                                wire:click="toggleFeatured('{{ $product->id }}')"
                                wire:loading.attr="disabled"
                                wire:target="toggleFeatured"
                                class="{{ $product->featured ? 'border border-green-500 bg-green-100 text-green-900 text-xs p-1' : 'border border-red-500 bg-red-100 text-red-900 text-xs p-1' }}">
                                {{ $product->is_featured_label }}
                            </button>
                        </div>

                        <div class="crud">
                            <a href="{{ Route::has('products.edit') ? route('products.edit', $product->id) : '#' }}" class="edit">
                                <x-svgs.edit />
                            </a>
                            <button x-data
                                x-on:click.prevent="$wire.set('delete_product_id', {{ $product->id }}); $dispatch('open-modal', 'confirm-product-deletion')"
                                class="delete">
                                <x-svgs.trash />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No products found.</p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

    <x-modal name="confirm-product-deletion" :show="$delete_product_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteProduct" @submit="$dispatch('close-modal', 'confirm-product-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">Confirm Deletion</h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">Are you sure you want to permanently delete this product?</p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-product-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Product
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
