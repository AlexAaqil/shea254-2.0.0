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

                    @php
                        use App\Helpers\PhoneHelper;

                        $formatted_phone = PhoneHelper::formatForDisplay($order->order_delivery->phone_number);
                        $phone_country = PhoneHelper::getCountry($order->order_delivery->phone_number);
                    @endphp

                    <p>
                        <span>Phone Number</span>
                        <span>
                            {{ $formatted_phone }}
                            @if($phone_country != 'Kenya')
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $phone_country }}</span>
                            @endif
                        </span>
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

                    @php
                        $items_total = $order->order_items->sum(function ($item) {
                            return $item->selling_price * $item->quantity;
                        });
                    @endphp

                    <p>
                        <span>Items Total : </span>
                        <span>Ksh. {{ number_format($items_total, 2) }}</span>
                    </p>
                    <p>
                        <span>Shipping Cost : </span>
                        <span>Ksh. {{ number_format($order->order_delivery->shipping_cost, 2) }}</span>
                    </p>
                    <p class="font-bold">
                        <span>Total Amount : </span>
                        <span class="text-green-600">Ksh. {{ number_format($order->total_amount, 2) }}</span>
                    </p>

                    <div class="payment_details">
                        @php
                            $payment_status = optional($order->payment)->status;
                            $payment_info = optional($order->payment)->response_description;
                            $payment_gateway = optional($order->payment)->payment_gateway;
                            
                            // Decode payment info if it's JSON
                            if (is_string($payment_info)) {
                                $payment_info = json_decode($payment_info, true);
                            }
                            
                            $status_class = match($payment_status) {
                                'paid' => 'text-green-600',
                                'success' => 'text-green-600',
                                'pending' => 'text-yellow-700',
                                'failed' => 'text-red-600',
                                default => ''
                            };
                        @endphp

                        <div class="payment_info">
                            <h4 class="{{ $status_class }}">Payment Status: {{ ucfirst($payment_status ?? 'unknown') }}</h4>
                            
                            {{-- Payment Gateway Badge --}}
                            @if($payment_gateway)
                                <div class="payment_gateway_badge mt-2 mb-3">
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-sm font-medium">
                                        {{ strtoupper($payment_gateway) }}
                                    </span>
                                </div>
                            @endif

                            {{-- Failed Payment Reason --}}
                            @if(in_array($payment_status, ['failed', 'pending']) && !empty($payment_info))
                                @php
                                    $failure_reason = null;

                                    if ($payment_gateway === 'kcb_mpesa') {
                                        $failure_reason = $payment_info['customer_message'] ?? 
                                                        $payment_info['response_description'] ?? 
                                                        'M-PESA payment failed';
                                    
                                    } elseif ($payment_gateway === 'paystack') {
                                        $failure_reason = $payment_info['message'] ?? 
                                                        ($payment_info['data']['gateway_response'] ?? null) ??
                                                        'PayStack payment failed';
                                    
                                    } elseif ($payment_gateway === 'paypal') {
                                        if (isset($payment_info['paypal_response']['details'][0]['description'])) {
                                            $failure_reason = $payment_info['paypal_response']['details'][0]['description'];
                                        } elseif (isset($payment_info['paypal_response']['message'])) {
                                            $failure_reason = $payment_info['paypal_response']['message'];
                                        } else {
                                            $failure_reason = 'PayPal payment failed';
                                        }
                                    }
                                @endphp

                                @if($failure_reason)
                                    <div class="mt-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Payment Failed Reason</h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <p>{{ $failure_reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- Payment Details Grid --}}
                            @if(!empty($payment_info) && is_array($payment_info))
                                <div class="payment_details_grid mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                                    
                                    {{-- M-PESA Specific Fields --}}
                                    @if($payment_gateway === 'kcb_mpesa')
                                        @if(isset($payment_info['mpesa_receipt']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">M-Pesa Receipt:</span>
                                                <span class="block mt-1 font-mono">{{ $payment_info['mpesa_receipt'] }}</span>
                                            </div>
                                        @endif
                                        
                                        @if(isset($payment_info['amount']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Amount Paid:</span>
                                                <span class="block mt-1">KES {{ number_format($payment_info['amount'], 2) }}</span>
                                            </div>
                                        @endif
                                        
                                        @if(isset($payment_info['phone_number']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Phone Number:</span>
                                                <span class="block mt-1">+{{ $payment_info['phone_number'] }}</span>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- PayStack Specific Fields --}}
                                    @if($payment_gateway === 'paystack')
                                        @if(isset($payment_info['reference']))
                                            <div class="detail-item col-span-2">
                                                <span class="font-semibold text-gray-600">Reference:</span>
                                                <div class="flex items-center mt-1">
                                                    <span class="font-mono text-sm bg-gray-100 px-3 py-2 rounded-l border border-gray-300 flex-1 truncate" 
                                                        title="{{ $payment_info['reference'] }}">
                                                        {{ $payment_info['reference'] }}
                                                    </span>
                                                    <button onclick="copyToClipboard('{{ $payment_info['reference'] }}')" 
                                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-r text-sm flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                                        </svg>
                                                        Copy
                                                    </button>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1 flex items-center">
                                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full mr-2">
                                                        {{ substr($payment_info['reference'], 0, 3) }}...{{ substr($payment_info['reference'], -6) }}
                                                    </span>
                                                    <span>Full reference available - click copy for support queries</span>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(isset($payment_info['authorization']['card_type']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Card Type:</span>
                                                <span class="block mt-1">
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-sm">
                                                        {{ $payment_info['authorization']['card_type'] }}
                                                    </span>
                                                </span>
                                            </div>
                                        @endif
                                        
                                        @if(isset($payment_info['authorization']['last4']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Card Details:</span>
                                                <div class="flex items-center mt-1">
                                                    <span class="font-mono">**** **** **** {{ $payment_info['authorization']['last4'] }}</span>
                                                    @if(isset($payment_info['authorization']['bank']))
                                                        <span class="ml-2 text-sm text-gray-600">({{ $payment_info['authorization']['bank'] }})</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(isset($payment_info['authorization']['country_code']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Country:</span>
                                                <span class="block mt-1">{{ $payment_info['authorization']['country_code'] }}</span>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- PayPal Specific Fields --}}
                                    @if($payment_gateway === 'paypal')
                                        {{-- PayPal Order ID --}}
                                        @if(isset($payment_info['paypal_order_id']))
                                            <div class="detail-item col-span-2 bg-blue-50 p-3 rounded border border-blue-200">
                                                <span class="font-semibold text-blue-800">PayPal Order ID:</span>
                                                <span class="block mt-1 font-mono text-blue-600 break-all">{{ $payment_info['paypal_order_id'] }}</span>
                                            </div>
                                        @endif

                                        {{-- Capture ID --}}
                                        @if(isset($payment_info['capture_id']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Capture ID:</span>
                                                <span class="block mt-1 font-mono text-sm break-all">{{ $payment_info['capture_id'] }}</span>
                                            </div>
                                        @endif

                                        {{-- Currency Conversion Info --}}
                                        <div class="detail-item col-span-2 bg-yellow-50 p-3 rounded border border-yellow-200">
                                            <span class="font-semibold text-yellow-800">Currency Conversion:</span>
                                            <div class="grid grid-cols-2 gap-2 mt-2">
                                                <div>
                                                    <span class="text-sm text-gray-600">KES Amount:</span>
                                                    <span class="block font-bold">KES {{ number_format($payment_info['kes_amount'] ?? $order->total_amount, 2) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-600">USD Amount:</span>
                                                    <span class="block font-bold">${{ number_format($payment_info['usd_amount'] ?? 0, 2) }}</span>
                                                </div>
                                                @if(isset($payment_info['exchange_rate']))
                                                    <div>
                                                        <span class="text-sm text-gray-600">Exchange Rate:</span>
                                                        <span class="block">1 USD = {{ number_format(1 / $payment_info['exchange_rate'], 2) }} KES</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-sm text-gray-600">Rate Source:</span>
                                                        <span class="block capitalize">{{ $payment_info['rate_source'] ?? 'Unknown' }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Transaction Times --}}
                                        @if(isset($payment_info['create_time']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Created (UTC):</span>
                                                <span class="block mt-1">{{ \Carbon\Carbon::parse($payment_info['create_time'])->format('d M Y H:i:s') }}</span>
                                            </div>
                                        @endif
                                        
                                        @if(isset($payment_info['update_time']))
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Updated (UTC):</span>
                                                <span class="block mt-1">{{ \Carbon\Carbon::parse($payment_info['update_time'])->format('d M Y H:i:s') }}</span>
                                            </div>
                                        @endif

                                        {{-- PayPal Fee Information --}}
                                        @if(isset($payment_info['full_response']['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']))
                                            @php
                                                $breakdown = $payment_info['full_response']['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown'];
                                            @endphp
                                            <div class="detail-item col-span-2 bg-gray-100 p-3 rounded">
                                                <span class="font-semibold text-gray-700">Fee Breakdown:</span>
                                                <div class="grid grid-cols-3 gap-2 mt-2 text-sm">
                                                    <div>
                                                        <span class="text-gray-600">Gross:</span>
                                                        <span class="block font-medium">${{ $breakdown['gross_amount']['value'] ?? '0.00' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Fee:</span>
                                                        <span class="block font-medium text-red-600">-${{ $breakdown['paypal_fee']['value'] ?? '0.00' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Net:</span>
                                                        <span class="block font-medium text-green-600">${{ $breakdown['net_amount']['value'] ?? '0.00' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Seller Protection Status --}}
                                        @if(isset($payment_info['full_response']['purchase_units'][0]['payments']['captures'][0]['seller_protection']))
                                            @php
                                                $protection = $payment_info['full_response']['purchase_units'][0]['payments']['captures'][0]['seller_protection'];
                                            @endphp
                                            <div class="detail-item">
                                                <span class="font-semibold text-gray-600">Seller Protection:</span>
                                                <span class="block mt-1">
                                                    <span class="px-2 py-1 {{ $protection['status'] === 'ELIGIBLE' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} rounded text-sm">
                                                        {{ $protection['status'] }}
                                                    </span>
                                                </span>
                                                @if(!empty($protection['dispute_categories']))
                                                    <span class="text-xs text-gray-500 block mt-1">
                                                        Dispute Categories: {{ implode(', ', $protection['dispute_categories']) }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Payer Information --}}
                                        @if(isset($payment_info['full_response']['payer']))
                                            @php
                                                $payer = $payment_info['full_response']['payer'];
                                            @endphp
                                            <div class="detail-item col-span-2">
                                                <span class="font-semibold text-gray-600">Payer Details:</span>
                                                <div class="mt-1 text-sm">
                                                    <div>{{ $payer['name']['given_name'] ?? '' }} {{ $payer['name']['surname'] ?? '' }}</div>
                                                    <div class="text-gray-600">{{ $payer['email_address'] ?? '' }}</div>
                                                    <div class="text-gray-600">Account ID: {{ $payer['payer_id'] ?? '' }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- Common Fields for All Gateways --}}
                                    @if(isset($payment_info['transaction_date']) || isset($payment_info['create_time']) || isset($payment_info['paid_at']))
                                        <div class="detail-item">
                                            <span class="font-semibold text-gray-600">Transaction Date:</span>
                                            <span class="block mt-1">
                                                @php
                                                    $transaction_date = $payment_info['transaction_date'] ?? $payment_info['create_time'] ?? $payment_info['paid_at'] ?? null;
                                                    $formatted_date = 'N/A';
                                                    
                                                    if (!empty($transaction_date)) {
                                                        try {
                                                            // Handle M-Pesa format (YmdHis)
                                                            if (is_string($transaction_date) && preg_match('/^\d{14}$/', $transaction_date)) {
                                                                $formatted_date = \Carbon\Carbon::createFromFormat('YmdHis', $transaction_date)->format('d M Y \a\t h:i A');
                                                            }
                                                            // Handle ISO format
                                                            elseif (is_string($transaction_date)) {
                                                                $formatted_date = \Carbon\Carbon::parse($transaction_date)->format('d M Y \a\t h:i A');
                                                            }
                                                            // Handle timestamp
                                                            elseif (is_numeric($transaction_date)) {
                                                                $formatted_date = \Carbon\Carbon::createFromTimestamp($transaction_date)->format('d M Y \a\t h:i A');
                                                            }
                                                        } catch (\Exception $e) {
                                                            $formatted_date = $transaction_date;
                                                        }
                                                    }
                                                @endphp
                                                {{ $formatted_date }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Raw Response Toggle (for admin use) --}}
                                    @auth
                                        @if(auth()->user()->isAdmin() && isset($payment_info['full_response']))
                                            <div class="detail-item col-span-2 mt-4">
                                                <details class="bg-gray-100 p-3 rounded">
                                                    <summary class="cursor-pointer font-semibold text-gray-700">View Raw Response</summary>
                                                    <pre class="mt-3 text-xs overflow-auto max-h-96 p-3 bg-gray-800 text-white rounded">{{ json_encode($payment_info['full_response'], JSON_PRETTY_PRINT) }}</pre>
                                                </details>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                            @elseif(is_string($payment_info) && !empty($payment_info))
                                <p>{{ $payment_info }}</p>
                            @endif
                        </div>

                        @if(in_array($payment_status, ['failed', 'pending']))
                            {{-- Only show STK Push for M-PESA --}}
                            @if ($payment_gateway === 'kcb_mpesa')
                                <form action="{{ Route::has('orders.request_stkpush') ? route('orders.request_stkpush', $order->order_number) : '#' }}" method="post" class="mt-4">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Request STK Push</button>
                                </form>
                            @else
                                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Note:</strong> This order used <span class="font-bold uppercase">{{ $payment_gateway }}</span>.
                                    </p>
                                </div>
                            @endif
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

{{-- Add this JavaScript at the bottom of your view or in your layout --}}
@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show a temporary notification
        let notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
        notification.textContent = 'Reference copied to clipboard!';
        document.body.appendChild(notification);
        
        setTimeout(function() {
            notification.remove();
        }, 2000);
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endpush
