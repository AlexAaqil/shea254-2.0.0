<div class="Blogs">
    <div class="container BlogCategories">
        <div class="breadcrumbs">
            <a href="{{ Route::has('blogs.index') ? route('blogs.index') : '#' }}" wire:navigate>Blogs</a>
            <span>Categories</span>
        </div>

        <div class="app_header">
            <div class="info">
                <h2>Categories</h2>
                <div class="stats">
                    <span>{{ $count_categories }} {{ Str::plural('category', $count_categories) }}</span>
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by category title..."
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
                <a href="{{ Route::has('blog-categories.create') ? route('blog-categories.create') : '#' }}" class="btn">New Blog Category</a>
            </div>
        </div>

        <div class="blog_categories_list small_cards">
            @forelse($categories as $category)
                <div class="card">
                    <div class="details">
                        <div class="info">
                            <h3>{{ $category->title }}</h3>
                            <span>{{ $category->blogs_count }} {{ Str::plural('blog', $category->blogs_count) }}</span>
                        </div>
                    </div>

                    <div class="actions">
                        <div class="crud">
                            <a href="{{ Route::has('blog-categories.edit') ? route('blog-categories.edit', $category->id) : '#' }}" class="edit">
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
