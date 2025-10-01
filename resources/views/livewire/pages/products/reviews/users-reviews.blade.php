<div class="Reviews UserReviews">
    <div class="container">
        <div class="app_header">
            <div class="info">
                <h2>Reviews</h2>
                <div class="stats">
                    <span>{{ $count_reviews }} {{ Str::plural('review', $count_reviews) }}</span>
                </div>
            </div>

            <div class="search">
                
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
                            <th>Product</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $review->product->title }}</td>
                                <td class="flex items-center gap-1">
                                    {{ $review->rating }}
                                    <x-svgs.star class="text-yellow-500" />
                                </td>
                                <td>{{ $review->review }}</td>
                                <td>{{ $review->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No reviews found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
