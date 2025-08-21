@if($product->product_reviews->isNotEmpty())
    <p>
        <span>Rating</span>
        <span>{{ number_format($product->product_reviews->avg('rating'), 1) }} / 5</span>
    </p>
@else
    <p>
        <span>Rating</span>
        <span>No reviews yet</span>
    </p>
@endif