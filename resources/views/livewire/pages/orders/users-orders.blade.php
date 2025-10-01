<div class="Orders UserOrders">
    <div class="container">
        <div class="app_header">
            <div class="info">
                <h2>Orders</h2>
                <div class="stats">
                    <span>{{ $count_orders }} {{ Str::plural('order', $count_orders) }}</span>
                    <span>{{ $count_unpaid }} unpaid</span>
                </div>
            </div>

            <div class="search">
                
            </div>

            <div class="button">

            </div>
        </div>

        <div class="orders_list">
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Number</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            {{-- <th>Delivery</th> --}}
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->order_delivery->phone_number }}</td>
                                <td>{!! Illuminate\Support\Str::limit($order->order_delivery?->address, 30, ' ...') !!}</td>
                                <td>{{ number_format($order->total_amount) }}</td>
                                @php
                                    $paymentStatus = optional($order->payment)->status;
                                    $statusClass = match($paymentStatus) {
                                        'paid' => 'text-green-600',
                                        'pending' => 'text-yellow-700',
                                        'failed' => 'text-red-600',
                                        default => ''
                                    };
                                @endphp
                                <td class="{{ $statusClass }}">
                                    {{ $paymentStatus ?? 'unknown' }}
                                </td>
                                {{-- <td class="{{ $order->order_delivery->delivery_status == 'pending' ? 'text-red-600' : 'text-green-600'  }}">{{ $order->order_delivery->delivery_status }}</td> --}}
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
