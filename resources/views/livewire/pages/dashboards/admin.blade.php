<div class="AdminDashboard">
    <section class="Statistics">
        <div class="container">
            <h2>Platform Statistics</h2>
            <div class="stats">
                <div class="stat">
                    <p>{{ $count_users }}</p>
                    <p>{{ Str::plural('Customer', $count_users) }}</p>
                    <p class="extras">
                        <span>
                            @if(auth()->user()->isSuperAdmin())
                                {{ $count_super_admins }} {{ Str::plural('super admin', $count_super_admins) }}  & 
                            @endif
                            {{ $count_admins }} {{ Str::plural('admin', $count_admins) }}
                        </span>
                    </p>
                </div>

                <div class="stat">
                    <p>{{ $count_orders }}</p>
                    <p>{{ Str::plural('Order', $count_orders) }}</p>
                    <p class="extras">
                        <span class="{{ $order_trend >= 0 ? 'success' : 'danger' }}">
                            {{ $order_trend >= 0 ? '+' : '' }} {{ number_format($order_trend, 1) }}% vs last year
                        </span>
                    </p>
                </div>

                <div class="stat">
                    <p>{{ $count_products }}</p>
                    <p>{{ Str::plural('Product', $count_products) }}</p>
                    <p class="extras">
                        <span>{{ $count_product_categories }} {{ Str::plural('Category', $count_product_categories) }}</span>
                    </p>
                </div>

                <div class="stat">
                    <p>{{ $count_delivery_locations }}</p>
                    <p>{{ Str::plural('Location', $count_delivery_locations) }}</p>
                    <p class="extras">
                        <span>{{ $count_delivery_areas }} {{ Str::plural('Area', $count_delivery_areas) }}</span>
                    </p>
                </div>

                <div class="stat">
                    <p>{{ $count_blogs }}</p>
                    <p>{{ Str::plural('Blog', $count_blogs) }}</p>
                    <p class="extras">
                        <span>{{ $count_blog_categories }} {{ Str::plural('Category', $count_blog_categories) }}</span>
                    </p>
                </div>

                <div class="stat">
                    <p>{{ $count_messages }}</p>
                    <p>{{ Str::plural('Message', $count_messages) }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="SalesStatistics">
        <div class="container">
            <h2>Financial Overview</h2>
            <div class="stats">
                <div class="stat">
                    <p>{{ number_format($gross_sales, 2) }}</p>
                    <p>Gross Sales</p>
                    <p class="extras">
                        <span class="{{ $gross_sales_trend >= 0 ? 'success' : 'danger' }}">
                            {{ $gross_sales_trend >= 0 ? '+' : '' }} {{ number_format(abs($gross_sales_trend), 1) }}% vs last year
                        </span>
                        <span class="text-gray-500 text-xs block mt-1">All sales at full price</span>
                    </p>
                </div>

                <div class="stat">
                    <p>{{ number_format($net_sales, 2) }}</p>
                    <p>Net Sales</p>
                    <p class="extras">
                        <span class="{{ $net_sales_trend >= 0 ? 'success' : 'danger' }}">
                            {{ $net_sales_trend >= 0 ? '+' : '' }} {{ number_format(abs($net_sales_trend), 1) }}% vs last year
                        </span>
                        <span class="text-gray-500 text-xs block mt-1">All sales minus discounts</span>
                    </p>
                </div>

                <div class="stat">
                    <p class="text-red-600">{{ number_format($cost_of_sales, 2) }}</p>
                    <p>Cost of Sales</p>
                </div>

                <div class="stat">
                    <p class="text-green-600">{{ number_format($gross_profit, 2) }}</p>
                    <p>Gross Profit</p>
                    <p class="extras">
                        @php
                            $profit_margin = $net_sales > 0 ? ($gross_profit / $net_sales) * 100 : 0;
                        @endphp
                        <span class="{{ $gross_profit_trend >= 0 ? 'success' : 'danger' }}">
                            {{ $gross_profit_trend >= 0 ? '+' : '' }} {{ number_format(abs($gross_profit_trend), 1) }}% vs last year
                        </span>
                        <span class="text-gray-500 text-xs block mt-1">
                            {{ number_format($profit_margin, 1) }}% margin
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="Charts">
        <div class="container">
            <div class="charts_wrapper">
                <div class="chart sales">
                    <h2>Sales Performance ({{ now()->year }})</h2>
                    <div class="chart_container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="chart orders">
                    <h2>Orders by Location</h2>
                    <div class="chart_container">
                        <canvas id="citiesChart"></canvas>
                    </div>
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

@push("scripts")
    <script src="{{ asset('assets/js/chart.js') }}"></script>
    <script>
        let salesChart = null;
        let citiesChart = null;

        function renderSalesChart(data) {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;
            
            // Destroy existing chart if it exists
            if (salesChart) {
                salesChart.destroy();
            }
            
            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'],
                    datasets: [{
                        label: 'Monthly Sales',
                        data: data,
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        function renderCitiesChart(labels, data) {
            const ctx = document.getElementById('citiesChart');
            if (!ctx) return;
            
            // Destroy existing chart if it exists
            if (citiesChart) {
                citiesChart.destroy();
            }
            
            citiesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Orders',
                        data: data,
                        backgroundColor: [
                            '#3b82f6', '#6366f1', '#10b981', '#f59e0b',
                            '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'
                        ],
                        borderWidth: 2,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', function () {
            renderSalesChart(@json($sales_data));
            renderCitiesChart(@json($locations_labels), @json($locations_orders));
        });

        // Listen for Livewire updates
        document.addEventListener('livewire:load', function () {
            renderSalesChart(@json($sales_data));
            renderCitiesChart(@json($locations_labels), @json($locations_orders));
        });

        document.addEventListener('livewire:update', function () {
            // Small delay to ensure DOM is updated
            setTimeout(() => {
                renderSalesChart(@json($sales_data));
                renderCitiesChart(@json($locations_labels), @json($locations_orders));
            }, 100);
        });
    </script>
@endpush
