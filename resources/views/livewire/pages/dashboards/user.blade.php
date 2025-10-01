<div class="UserDashboard">
    <section class="Statistics">
        <div class="container">
            <div class="stats">
                <div class="stat">
                    <p>{{ $count_paid }}</p>
                    <p>{{ Str::plural('Purchase', $count_paid) }} & {{ $count_unpaid }} Pending</p>
                </div>

                <div class="stat">
                    <p>{{ $count_reviews }}</p>
                    <p>{{ Str::plural('Review', $count_reviews) }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="RecentActivity">
        <div class="container">
            <div class="activities_wrapper">
                <div class="activity">
                    <div class="activity_header">
                        <h3>Recent Purchases</h3>
                        <a href="{{ Route::has('users-orders.index') ? route('users-orders.index') : '#'}}">View All</a>
                    </div>

                    <div class="activity_content">
                        @forelse($orders as $order)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</p>
                                        <span class="{{ $order->payment->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs px-2 py-1 rounded">{{ $order->payment->status }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->timezone('Africa/Nairobi')->format('d-m-Y H:i') }}</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm text-center py-4">No recent orders</p>
                        @endforelse
                    </div>
                </div>

                <div class="activity">
                    <div class="activity_header">
                        <h3>Your Reviews</h3>
                        <a href="{{ Route::has('users-reviews.index') ? route('users-reviews.index') : '#'}}">View All</a>
                    </div>

                    <div class="activity_content">
                        @forelse($reviews as $review)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">#{{ $review->product->title }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $review->created_at->timezone('Africa/Nairobi')->format('d-m-Y H:i') }}</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 flex items-center gap-1">
                                    {{ $review->rating }}
                                    <x-svgs.star class="fill-yellow-500" />
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm text-center py-4">No recent orders</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
