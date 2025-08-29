<div class="AdminDashboard">
    <section class="Statistics">
        <div class="container">
            <div class="stats">
                @if(auth()->user()->isSuperAdmin())
                    <div class="stat">
                        <p>{{ $count_super_admins }}</p>
                        <p>{{ Str::plural('Super Admin', $count_super_admins) }}</p>
                    </div>
                @endif

                <div class="stat">
                    <p>{{ $count_users }}</p>
                    <p>{{ Str::plural('User', $count_users) }} & {{ $count_admins }} {{ Str::plural('Admin', $count_admins) }}</p>
                </div>

                <div class="stat">
                    <p>{{ $count_orders }}</p>
                    <p>{{ Str::plural('Order', $count_orders) }}</p>
                </div>

                <div class="stat">
                    <p>{{ $count_products }}</p>
                    <p>{{ Str::plural('Product', $count_products) }} & {{ $count_product_categories }} {{ Str::plural('Category', $count_product_categories) }}</p>
                </div>

                <div class="stat">
                    <p>{{ $count_delivery_locations }}</p>
                    <p>{{ Str::plural('Location', $count_delivery_locations) }} & {{ $count_delivery_areas }} {{ Str::plural('Area', $count_delivery_areas) }}</p>
                </div>

                <div class="stat">
                    <p>{{ $count_blogs }}</p>
                    <p>{{ Str::plural('Blog', $count_blogs) }} & {{ $count_blog_categories }} {{ Str::plural('Category', $count_blog_categories) }}</p>
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
            <div class="stats">
                <div class="stat">
                    <p>{{ number_format($gross_sales, 2) }}</p>
                    <p>Gross</p>
                </div>

                <div class="stat">
                    <p>{{ number_format($net_sales, 2) }}</p>
                    <p>Net</p>
                </div>

                <div class="stat">
                    <p>{{ number_format($cost_of_sales, 2) }}</p>
                    <p>Cost of Sales</p>
                </div>

                <div class="stat">
                    <p>{{ number_format($gross_profit, 2) }}</p>
                    <p>Gross Profit</p>
                </div>
            </div>
        </div>
    </section>

    <section class="Charts">
        <div class="container">
            <div class="chart sales">
                <h2>Sales</h2>
                <canvas id="salesChart"></canvas>
            </div>

            <div class="chart orders">
                <h2>Order Locations</h2>
                <canvas id="citiesChart"></canvas>
            </div>
        </div>
    </section>
</div>

@push("scripts")
    <script src="{{ asset('assets/js/chart.js') }}"></script>
    <script>
        function renderSalesChart(data) {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Sales Amount',
                        data: data,
                        borderWidth: 1,
                        borderRadius: 2,
                        backgroundColor: '#3b82f6',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                }
            });
        }

        function renderCitiesChart(labels, data) {
            const ctx = document.getElementById('citiesChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Orders',
                        data: data,
                        backgroundColor: [
                            '#3b82f6', '#6366f1', '#10b981', '#f59e0b',
                            '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'
                        ],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });
        }

        // Boot the charts initially (for fresh loads)
        document.addEventListener('DOMContentLoaded', function () {
            renderSalesChart(@json($sales_data));
            renderCitiesChart(@json($locations_labels), @json($locations_orders));
        });
    </script>
@endpush
