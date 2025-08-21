<x-admin>
    <div class="container orders">
        <div class="custom_form order_details_form">
            <h1>Update Order</h1>
            <div class="order_details">
                <div class="details">
                    <p class="text-success">
                        <span>Order_number</span>
                        <span>{{ $order->order_number }}</span>
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
                        <span>+{{ format_phone_number($order->order_delivery->phone_number) }}</span>
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

                <div class="cart_items">
                    <p class="bold">Items Ordered</p>

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
                    <p class="text-success bold">
                        <span>Total Amount : </span>
                        <span>Ksh. {{ number_format($order->total_amount, 2) }}</span>
                    </p>

                    <div class="payment_details">
                        @php
                            $payment_status = optional($order->payment)->status;
                            $payment_description = optional($order->payment)->response_description;
                            $status_class = match($payment_status) {
                                'paid' => 'success',
                                'success' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                default => ''
                            };

                            // Decode the JSON payment description
                            $payment_info = json_decode($payment_description, true) ?? [];
                        @endphp

                        <div class="payment-info">
                            <h4 class="{{ $status_class }}">Payment Status: {{ ucfirst($payment_status ?? 'unknown') }}</h4>
                            @if($payment_status == 'failed' && isset($payment_description))
                                <p class="text-danger"><strong>Reason:</strong> {{ $payment_description }}</p>
                            @endif
                            @if(!empty($payment_info))
                                <div class="payment-details-grid">
                                    @if(isset($payment_info['mpesa_receipt']))
                                        <p><strong>M-Pesa Receipt:</strong> {{ $payment_info['mpesa_receipt'] }}</p>
                                    @endif
                                    @if(isset($payment_info['amount']))
                                        <p><strong>Amount Paid:</strong> KES {{ number_format($payment_info['amount'], 2) }}</p>
                                    @endif
                                    @if(isset($payment_info['phone_number']))
                                        <p><strong>Phone Number:</strong> +{{ format_phone_number($payment_info['phone_number']) }}</p>
                                    @endif
                                    @if(isset($payment_info['transaction_date']))
                                        <p><strong>Transaction Date:</strong> {{ \Carbon\Carbon::createFromFormat('YmdHis', $payment_info['transaction_date'])->format('d M Y \a\t h:i A') }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($payment_status == 'failed' || $payment_status == 'pending')
                            <form action="{{ route('payment.request_stkPush', $order->order_number) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-primary">Request STK Push</button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>

            <form action="{{ route('orders.update', ['order' => $order->id]) }}" method="post">
                @csrf
                @method('PATCH')

                <div class="row_input_group">
                    <div class="input_group">
                        <label for="additional_information">Additional Information</label>
                        <input type="text" name="additional_information" id="additional_information" placeholder="Extra Information... (e.g) Specific Location" value="{{ $order ? $order->order_delivery->additional_information : old('additional_information') }}">
                        <span class="inline_alert">{{ $errors->first('additional_information') }}</span>
                    </div>

                    <div class="input_group">
                        <label for="delivery_status">Delivery Status</label>
                        <select name="delivery_status" id="delivery_status">
                            <option value="" {{ !$order || $order->order_delivery->delivery_status == '' ? 'selected' : ''}}>Select Order Status</option>
                            <option value="pending" {{ ($order && $order->order_delivery->delivery_status == 'pending') ? 'selected' : '' }}>Pending</option>
                            <option value="processed" {{ ($order && $order->order_delivery->delivery_status == 'processed') ? 'selected' : '' }}>Processed</option>
                        </select>
                        <span class="inline_alert">{{ $errors->first('delivery_status') }}</span>
                    </div>
                </div>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</x-admin>
