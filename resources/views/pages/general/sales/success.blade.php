<x-guest-layout>
    <div class="OrderSuccess">
        <div class="container">
            <h1>Success</h1>
            <p>Your order (<strong>{{ $order_number }}</strong>) has been placed successfully!</p>
            <p>Please enter your MPesa PIN to complete this order.</p>
            <p>We will contact you in case we need any clarification.</p>

            <div class="actions">
                <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}" class="btn">Continue Shopping</a>
            </div>
        </div>
    </div>
</x-guest-layout>
