@if(isset($product))
    <script>
        // ViewContent event for product pages
        fbq('track', 'ViewContent', {
            content_name: '{{ $product->title }}',
            content_ids: ['{{ $product->id }}'],
            content_type: 'product',
            value: {{ $product->discount_price != 0.00 && $product->discount_price < $product->selling_price 
                ? $product->discount_price 
                : $product->selling_price }},
            currency: 'KES',
            content_category: '{{ $product->product_category ? $product->product_category->title : "" }}',
        });
    </script>
@endif

@if(session('order_completed'))
    <script>
        // Purchase event for order confirmation
        fbq('track', 'Purchase', {
            content_ids: {!! json_encode(session('order_completed.product_ids', [])) !!},
            content_type: 'product',
            value: {{ session('order_completed.total', 0) }},
            currency: 'KES',
            num_items: {{ session('order_completed.item_count', 0) }},
            transaction_id: '{{ session('order_completed.order_number') }}'
        });
    </script>
@endif