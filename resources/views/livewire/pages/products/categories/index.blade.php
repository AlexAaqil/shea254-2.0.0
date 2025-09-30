<div class="Products">
    <div class="container ProductCategories">
        <div class="breadcrumbs">
            <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}" wire:navigate>Products</a>
            <a href="{{ Route::has('product-measurements.index') ? route('product-measurements.index') : '#' }}" wire:navigate>Measurements</a>
            <a href="{{ Route::has('product-reviews.index') ? route('product-reviews.index') : '#' }}" wire:navigate>Reviews</a>
            <span>Categories</span>
        </div>

        <div class="app_header">
            <div class="info">
                <h2>Product Categories</h2>
                <div class="stats">
                    <span>{{ $count_categories }} {{ Str::plural('category', $count_categories) }}</span>
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
                <a href="{{ Route::has('product-categories.create') ? route('product-categories.create') : '#' }}" class="btn">New Product Category</a>
            </div>
        </div>

        <div class="categories_list small_cards">
            @forelse($categories as $category)
                <div class="card">
                    <div class="details">
                        <div class="image">
                            <span class="bg-gray-200 text-lg text-gray-700 rounded-full w-20 h-20 flex items-center justify-center font-semibold uppercase">{{ substr($category->title, 0, 1) }}</span>
                        </div>

                        <div class="info">
                            <h3>{{ $category->title }}</h3>
                            <span>{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span>
                        </div>
                    </div>

                    <div class="actions">
                        <div class="others">

                        </div>

                        <div class="crud">
                            <a href="{{ Route::has('product-categories.edit') ? route('product-categories.edit', $category->id) : '#' }}" class="edit">
                                <x-svgs.edit />
                            </a>
                            <button x-data
                                x-on:click.prevent="$wire.set('delete_category_id', {{ $category->id }}); $dispatch('open-modal', 'confirm-category-deletion')"
                                class="delete">
                                <x-svgs.trash />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No categories found.</p>
            @endforelse
        </div>
    </div>

    <x-modal name="confirm-category-deletion" :show="$delete_category_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteCategory" @submit="$dispatch('close-modal', 'confirm-category-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">Confirm Deletion</h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">Are you sure you want to permanently delete this category?</p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-category-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Category
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
