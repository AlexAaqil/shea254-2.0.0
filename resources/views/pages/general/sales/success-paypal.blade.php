<x-guest-layout>
    <div class="OrderSuccess">
        <div class="container">
            <div class="icon">
                <x-svgs.check />
            </div>

            <h1>Payment Successful</h1>
            <p>Your order (<span class="order_number">{{ $order_number }}</span>) has been placed successfully!</p>
            <p>Your PayPal payment has been confirmed. Thank you for your purchase!</p>
            <p>We will contact you in case we need any clarification.</p>

            <div class="actions">
                <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}" class="btn">Continue Shopping</a>
            </div>
        </div>
    </div>
</x-guest-layout>
