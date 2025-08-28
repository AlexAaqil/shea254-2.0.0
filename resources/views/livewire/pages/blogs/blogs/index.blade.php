<div class="Blogs">
    <div class="container Blogs">
        <div class="breadcrumbs">
            <a href="{{ Route::has('blog-categories.index') ? route('blog-categories.index') : '#' }}" wire:navigate>Categories</a>
            <span>Blogs</span>
        </div>

        <div class="app_header">
            <div class="info">
                <h2>Blogs</h2>
                <div class="stats">
                    <span>{{ $count_blogs }} {{ Str::plural('blog', $count_blogs) }}</span>
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by blog title..."
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
                <a href="{{ Route::has('blogs.create') ? route('blogs.create') : '#' }}" class="btn">New Blog</a>
            </div>
        </div>

        <div class="blogs_list small_cards">
            @forelse($blogs as $blog)
                <div class="blog_card card">
                    <div class="details">
                        <div class="image">
                            @if ($blog->image_url)
                                <img src="{{ $blog->image_url }}" alt="{{ $blog->slug }}" class="rounded-lg w-20 h-20 object-cover">
                            @else
                                <span class="bg-red-200 text-xl text-gray-700 rounded-lg w-20 h-20 flex items-center justify-center font-semibold uppercase">{{ substr($blog->title, 0, 1) }}</span>
                            @endif
                        </div>

                        <div class="info">
                            <h3>{{ $blog->title }}</h3>

                            <div class="extras">
                                <div class="extra">
                                    <span>Category:</span>
                                    <span>{{ $blog->blog_category->title ?? 'Uncategorized' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="actions">
                        <div class="crud">
                            <a href="{{ Route::has('blogs.edit') ? route('blogs.edit', $blog->id) : '#' }}" class="edit">
                                <x-svgs.edit />
                            </a>

                            <button x-data
                                x-on:click.prevent="$wire.set('delete_blog_id', {{ $blog->id }}); $dispatch('open-modal', 'confirm-blog-deletion')"
                                class="delete">
                                <x-svgs.trash />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No blogs found.</p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $blogs->links() }}
        </div>
    </div>

    <x-modal name="confirm-blog-deletion" :show="$delete_blog_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteBlog" @submit="$dispatch('close-modal', 'confirm-blog-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">Confirm Deletion</h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">Are you sure you want to permanently delete this blog?</p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-blog-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Blog
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
