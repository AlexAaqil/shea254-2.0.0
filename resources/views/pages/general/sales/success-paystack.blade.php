<x-guest-layout>
    <div class="OrderSuccess">
        <div class="container">
            <div class="icon">
                <x-svgs.check />
            </div>

            <h1>Payment Successful</h1>
            <p>Your order (<span class="order_number">{{ $order_number }}</span>) has been placed successfully!</p>
            <p>Your payment has been confirmed. Thank you for your purchase!</p>
            <p>We will contact you in case we need any clarification.</p>

            <div class="actions">
                <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}" class="btn">Continue Shopping</a>
            </div>
        </div>
    </div>

    @push('scripts')
        @if(session()->has('meta_purchase'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const purchaseData = @json(session('meta_purchase'));
                    
                    if (typeof fbq !== 'undefined') {
                        fbq('track', 'Purchase', purchaseData);
                        console.log('Meta Pixel: Purchase tracked', purchaseData);
                    } else {
                        console.warn('Meta Pixel not loaded for Purchase');
                    }
                });
            </script>
            @php
                session()->forget('meta_purchase');
            @endphp
        @endif
    @endpush
</x-guest-layout>
