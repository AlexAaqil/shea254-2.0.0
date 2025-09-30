<div class="Orders">
    <div class="container Orders EditOrder">
        <div class="custom_form order_details_form">
            <div class="header">
                <a href="{{ Route::has('orders.index') ? route('orders.index') : '#' }}" wire:navigate>
                    <x-svgs.arrow-left class="w-5 h-5" />
                </a>
                <h2>Order Details</h2>
            </div>

            <div class="content">
                <div class="order_details">
                    <p>
                        <span>Order Number</span>
                        <span class="text-bold text-green-600">{{ $order->order_number }}</span>
                    </p>
                    <p>
                        <span>Names</span>
                        <span>{{ $order->order_delivery->full_name }}</span>
                    </p>
                    <p>
                        <span>Email Address</span>
                        <span>{{ $order->order_delivery->email }}</span>
                    </p>

                    <p>
                        <span>Phone Number</span>
                        <span>+{{ $order->order_delivery->phone_number }}</span>
                    </p>

                    <p>
                        <span>Address</span>
                        <span>{{ $order->order_delivery->address }}</span>
                    </p>
                    <p>
                        <span>Location</span>
                        <span>{{ $order->order_delivery->location }}</span>
                    </p>
                    <p>
                        <span>Area</span>
                        <span>{{ $order->order_delivery->area }}</span>
                    </p>
                    <p>
                        <span>Order Date</span>
                        <span>{{ $order->created_at->format('d M Y \a\t h:i A') }}</span>
                    </p>
                </div>

                <div class="order_items">
                    <p class="custom_title">Items Ordered</p>

                    <ul>
                        @foreach($order->order_items as $product)
                        <li>
                            <span>{{ $product['title'] }}</span>
                            <span>{{ $product['quantity'] }} @ {{ $product['selling_price'] }}</span>
                            <span>= Ksh. {{ number_format($product['selling_price'] * $product['quantity'], 2) }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <p>
                        <span>Shipping Cost : </span>
                        <span>Ksh. {{ $order->order_delivery->shipping_cost }}</span>
                    </p>
                    <p class="text-green-600">
                        <span>Total Amount : </span>
                        <span>Ksh. {{ number_format($order->total_amount, 2) }}</span>
                    </p>

                    <div class="payment_details">
                        @php
                            $payment_status = optional($order->payment)->status;
                            $payment_description = optional($order->payment)->response_description;
                            $status_class = match($payment_status) {
                                'paid' => 'text-green-600',
                                'success' => 'text-green-600',
                                'pending' => 'text-yellow-700',
                                'failed' => 'text-red-600',
                                default => ''
                            };

                            // Decode the JSON payment description
                            $payment_info = json_decode($payment_description, true) ?? [];
                        @endphp

                        <div class="payment_info">
                            <h4 class="{{ $status_class }}">Payment Status: {{ ucfirst($payment_status ?? 'unknown') }}</h4>
                            @if($payment_status == 'failed' && isset($payment_description))
                                <p class="text-danger"><strong>Reason:</strong> {{ $payment_description }}</p>
                            @endif
                            @if(!empty($payment_info))
                                <div class="payment_details_grid">
                                    @if(isset($payment_info['mpesa_receipt']))
                                        <p>
                                            <span>M-Pesa Receipt:</span>
                                            <span>{{ $payment_info['mpesa_receipt'] }}</span>
                                        </p>
                                    @endif
                                    @if(isset($payment_info['amount']))
                                        <p>
                                            <span>Amount Paid:</span>
                                            <span>KES {{ number_format($payment_info['amount'], 2) }}</span>
                                        </p>
                                    @endif
                                    @if(isset($payment_info['phone_number']))
                                        <p>
                                            <span>Phone Number:</span>
                                            <span>+{{ $payment_info['phone_number'] }}</span>
                                        </p>
                                    @endif
                                    @if(isset($payment_info['transaction_date']))
                                        <p>
                                            <span>Transaction Date:</span>
                                            <span>{{ \Carbon\Carbon::createFromFormat('YmdHis', $payment_info['transaction_date'])->format('d M Y \a\t h:i A') }}</span>
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($payment_status == 'failed' || $payment_status == 'pending')
                            <form action="{{ Route::has('orders.request_stkpush') ? route('orders.request_stkpush', $order->order_number) : '#' }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-primary">Request STK Push</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="custom_form">
            <div class="header">
                <a href="{{ Route::has('orders.index') ? route('orders.index') : '#' }}" wire:navigate>
                    <x-svgs.arrow-left class="w-5 h-5" />
                </a>
                <h2>Edit Order</h2>
            </div>

            <form wire:submit.prevent="updateOrder">
                <div class="inputs_group">
                    <div class="inputs">
                        <label for="additional_information">Additional Information</label>
                        <input type="text" wire:model="additional_information" id="additional_information" placeholder="Extra Information... (e.g) Specific Location">
                        <x-form-input-error field="additional_information" />
                    </div>

                    <div class="inputs">
                        <label class="block font-semibold mb-2">Delivery Status</label>

                        <label class="flex items-center space-x-2 mb-2">
                            <input type="radio" wire:model="delivery_status" value="pending">
                            <span>Pending</span>
                        </label>

                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model="delivery_status" value="processed">
                            <span>Processed</span>
                        </label>

                        <x-form-input-error field="delivery_status" />
                    </div>
                </div>

                <div class="buttons_group">
                    <button type="submit" wire:loading.attr="disabled" wire:target="updateOrder">
                        <span wire:loading.remove wire:target="updateOrder">Update</span>
                        <span wire:loading wire:target="updateOrder">Updating Order...</span>
                    </button>

                    <a href="{{ route('orders.index') }}" class="btn btn_danger">Cancel</a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <button x-data x-on:click.prevent="$wire.set('delete_order_id', {{ $order->id }}); $dispatch('open-modal', 'confirm-order-deletion')" class="btn btn_danger">Delete</button>
                        @endif
                    @endauth
                </div>
            </form>
        </div>
    </div>

    <x-modal name="confirm-order-deletion" :show="$delete_order_id !== null" focusable>
        <div class="custom_form">
            <form wire:submit="deleteOrder" @submit="$dispatch('close-modal', 'confirm-order-deletion')" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">Confirm Deletion</h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">Are you sure you want to permanently delete this order and associated details?</p>

                <div class="mt-6 flex justify-start">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-order-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Order
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
