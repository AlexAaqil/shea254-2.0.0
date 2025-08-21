<x-admin>
    <div class="container orders">
        <div class="header">
            <h1>Orders <span>({{ $orders->count() }})</span></h1>
            @include('partials.js_search')
        </div>

        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Delivery</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="searchable {{ $order->order_delivery?->delivery_status == 'processed' ? 'del' : '' }}">
                            <td>
                                <a href="{{ route('orders.edit', ['order'=>$order->id]) }}" class="update_link">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>{{ $order->order_delivery?->full_name }}</td>
                            <td>{{ format_phone_number($order->order_delivery?->phone_number) }}</td>
                            <td>{!! Illuminate\Support\Str::limit($order->order_delivery?->address, 15, ' ...') !!}</td>
                            <td>{{ number_format($order->total_amount) }}</td>

                            @php
                                $paymentStatus = optional($order->payment)->status;
                                $statusClass = match($paymentStatus) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    default => ''
                                };
                            @endphp
                            <td class="{{ $statusClass }}">
                                {{ $paymentStatus ?? 'unknown' }}
                            </td>                  

                            <td class="{{ $order->order_delivery->delivery_status == 'pending' ? 'text-danger' : 'text-success'  }}">{{ $order->order_delivery->delivery_status }}</td>
                            <td class="actions">
                                <div class="action">
                                    <form id="deleteForm_{{ $order->id }}" action="{{ route('orders.destroy', ['order' => $order->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <a href="javascript:void(0);" onclick="deleteItem({{ $order->id }}, 'order');">
                                            <i class="fas fa-trash-alt delete"></i>
                                        </a>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
	    <div>
		<p>Invalid orders</p>
		 @foreach($orders_without_delivery as $other)
                        <tr>
                                <td>{{ $other->order_number }}
                        </tr>
                    @endforeach
	    </div>
        </div>
    </div>
    <x-sweetalert></x-sweetalert>
</x-admin>
