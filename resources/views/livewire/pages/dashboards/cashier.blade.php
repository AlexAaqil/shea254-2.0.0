<div class="CashierDashboard">
    <section class="Statistics mb-6">
        <div class="container">
            <div class="stats_header">
                <h2>Financial Overview</h2>

                <div class="filters">
                    <select wire:model.live="period">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                    </select>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat">
                    <p>{{ $orders }}</p>
                    <p>Orders</p>
                </div>
                
                <div class="stat">
                    <p>{{ number_format($revenue, 2) }}</p>
                    <p>Revenue</p>
                </div>

                <div class="stat">
                    <p>{{ $units_sold }}</p>
                    <p>Units Sold</p>
                </div>
            </div>
        </div>
    </section>

    <section class="RecentActivity">
        <div class="container">
            <div class="activities_wrapper">
                <div class="activity">
                    <div class="activity_header">
                        <h3>Top Selling Products</h3>
                        <span>Last 30 days</span>
                    </div>
                    <div class="activity_content">
                        @forelse($top_products as $product)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product['name'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ number_format($product['revenue'], 2) }} revenue</p>
                                </div>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $product['sold'] }} sold
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm text-center py-4">No sales data available</p>
                        @endforelse
                    </div>
                </div>

                <div class="activity">
                    <div class="activity_header">
                        <h3>Recent Orders</h3>
                        <a href="{{ route('orders.index') }}" wire:navigate>View All</a>
                    </div>
                    <div class="activity_content">
                        @forelse($recent_orders as $order)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">#{{ $order['order_number'] }}</p>
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $order['status'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $order['customer'] }} • {{ $order['date'] }}</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($order['amount'], 2) }}</span>
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
