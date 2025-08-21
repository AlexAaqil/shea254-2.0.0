<x-admin>
    <div class="container orders">
        <div class="header">
            <h1>Product Reviews <span>({{ $reviews->count() }})</span></h1>
            @include('partials.js_search')
        </div>

        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Is Visible</th>
                        <th>Ordering</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>                    
                    @if ($reviews->count() > 0)
                        @php $id = 1 @endphp
                        @foreach($reviews as $review)
                        <tr class="searchable">
                            <td class="edit">
                                <a href="{{ route('product-reviews.edit', ['product_review'=>$review->id]) }}">
                                    {{ $id++ }}
                                </a>
                            </td>
                            <td>{{ $review->user->first_name . ' ' . $review->user->last_name }}</td>
                            <td>{{ $review->user->email }}</td>
                            <td><i class="fa fa-star"></i> {{ $review->rating }}</td>
                            <td>
                                {{ Illuminate\Support\Str::limit($review->review, 40, ' ...') }}
                            </td>
                            <td>{{ $review->is_visible == 1 ? 'Yes' : 'No' }}</td>
                            <td>{{ $review->ordering }}</td>
                            <td class="actions">
                                <div class="action">
                                    <form id="deleteForm_{{ $review->id }}" action="{{ route('product-reviews.destroy', ['product_review' => $review->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <a href="javascript:void(0);" onclick="deleteItem({{ $review->id }}, 'product review');">
                                            <i class="fas fa-trash-alt delete"></i>
                                        </a>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">No reviews yet</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <x-sweetalert></x-sweetalert>
</x-admin>
