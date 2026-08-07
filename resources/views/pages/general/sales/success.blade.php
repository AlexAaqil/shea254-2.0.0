<x-guest-layout>
    <div class="OrderSuccess">
        <div class="container">
            <div class="icon">
                <x-svgs.check />
            </div>

            <h1>Success</h1>
            <p>Your order (<span class="order_number">{{ $order_number }}</span>) has been placed successfully!</p>
            <p>Please enter your MPesa PIN to pay for this order.</p>
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
                        // Extract event_id
                        const eventId = purchaseData.event_id;
                        delete purchaseData.event_id; // remove from custom data

                        // pass event id for deduplication
                        fbq('track', 'Purchase', purchaseData, {eventID: eventId});
                        console.log('Meta Pixel: Purchase tracked with event_id:', eventId);
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
