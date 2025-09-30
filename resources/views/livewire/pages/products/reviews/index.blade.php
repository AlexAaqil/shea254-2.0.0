<div class="ProductReviews">
    <div class="container">
        <div class="breadcrumbs">
            <a href="{{ Route::has('product.index') ? route('products.index') : '#' }}" wire:navigate>Products</a>
            <a href="{{ Route::has('product-categories.index') ? route('product-categories.index') : '#' }}" wire:navigate>Categories</a>
            <a href="{{ Route::has('product-measurements.index') ? route('product-measurements.index') : '#' }}" wire:navigate>Measurements</a>
            <span>Reviews</span>
        </div>

        <div class="app_header">
            <div class="info">
                <h2>Reviews</h2>
                <div class="stats">
                    <span>{{ $count_reviews }} {{ Str::plural('review', $count_reviews) }}</span>
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by name, email"
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

            </div>
        </div>

        <div class="reviews_list">
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Is Visible</th>
                            <th>Ordering</th>
                            <th class="action">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ ($reviews->currentPage() - 1) * $reviews->perPage() + $loop->iteration }}</td>
                                <td>{{ $review->user->full_name }}</td>
                                <td>{{ $review->user->email }}</td>
                                <td>{{ $review->rating }}</td>
                                <td>{!! Illuminate\Support\Str::limit($review->review, 30, ' ...') !!}</td>
                                <td>{{ $review->is_visible_label }}</td>
                                <td>{{ $review->ordering }}</td>
                                <td class="actions">
                                    <div class="action">
                                        <a href="{{ Route::has('product-reviews.edit') ? route('product-reviews.edit', $review->id) : '#' }}" class="edit" title="Edit this review">
                                            <x-svgs.edit class="text-green-600" />
                                        </a>
                                    </div>

                                    <div class="action">
                                        <button x-data
                                            x-on:click.prevent="$wire.set('delete_review_id', {{ $review->id }}); $dispatch('open-modal', 'confirm-review-deletion')"
                                            class="delete">
                                            <x-svgs.trash class="text-red-600" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No reviews found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination mt-4">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>

    <x-modal name="confirm-review-deletion" :show="$delete_review_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteReview" @submit="$dispatch('close-modal', 'confirm-review-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">Confirm Deletion</h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">Are you sure you want to permanently delete this review?</p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-review-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Review
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
