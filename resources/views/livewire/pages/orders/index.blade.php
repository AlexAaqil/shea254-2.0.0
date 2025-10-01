<div class="Orders">
    <div class="container">
        <div class="app_header">
            <div class="info">
                <h2>Orders</h2>
                <div class="stats">
                    <span>{{ $count_orders }} {{ Str::plural('order', $count_orders) }}</span>
                    <span>{{ $count_unpaid_orders }} unpaid</span>
                    <span>{{ $count_undelivered_orders }} undelivered</span>
                    <span>{{ $count_invalid_orders }} invalid</span>
                </div>
            </div>

            <div class="search">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search by order number, name, email, phone number..."
                        wire:model="search"
                        wire:keydown.enter="performSearch"
                        class="pr-8"
                    >
                    @if($search)
                        <button
                            wire:click="resetSearch"
                            class="absolute right-1 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            X
                        </button>
                    @endif
                </div>
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
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Delivery</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                <td>
                                    <a href="{{ Route::has('orders.edit') ? route('orders.edit', $order->id) : '#' }}" class="text-blue-600" title="Edit this order" wire:navigate>
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $order->order_delivery?->full_name }}</td>
                                <td>{{ $order->order_delivery->phone_number }}</td>
                                <td>{!! Illuminate\Support\Str::limit($order->order_delivery?->address, 15, ' ...') !!}</td>
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
                                <td class="{{ $order->order_delivery->delivery_status == 'pending' ? 'text-red-600' : 'text-green-600'  }}">{{ $order->order_delivery->delivery_status }}</td>
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

            <div class="pagination mt-4">
                {{ $orders->links() }}
            </div>
        </div>

        @if(count($orders_without_delivery) > 0)
            <div class="invalid_orders_list mt-6">
                <h2>Invalid Orders</h2>

                <ul>
                    @forelse($orders_without_delivery as $invalid_order)
                        <li>{{ $invalid_order->order_number }}</li>
                    @empty
                        <li>No invalid orders found</li>
                    @endforelse
                </ul>
            </div>
        @endif
    </div>
</div>
