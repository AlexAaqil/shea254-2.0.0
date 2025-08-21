<x-admin>
    <section class="Admin_dashboard">
        <div class="container">
            <div class="hero">
                <p>Hi {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
            </div>
        
            <div class="stats">
                <div class="stat">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="text">
                        <p>
                            <a href="{{ route('users.index') }}">Users</a>
                        </p>
                        <p>{{ $count_users }}</p>
                    </div>
                </div>

                <div class="stat">
                    <div class="icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="text">
                        <p>
                            <a href="{{ route('orders.index') }}">Orders</a>
                        </p>
                        <p>{{ $count_orders }}</p>
                    </div>
                </div>
        
                <div class="stat">
                    <div class="icon">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <div class="text">
                        <p>
                            <a href="{{ route('products.index') }}">Products</a>
                        </p>
                        <p>{{ $count_products }}</p>
                    </div>
                </div>

                <div class="stat">
                    <div class="icon">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <div class="text">
                        <p>
                            <a href="{{ route('locations.index') }}">Areas</a>
                        </p>
                        <p>{{ $count_delivery_areas }}</p>
                    </div>
                </div>
        
                <div class="stat">
                    <div class="icon">
                        <i class="fas fa-blog"></i>
                    </div>
                    <div class="text">
                        <p>
                            <a href="{{ route('blogs.index') }}">Blogs</a>
                        </p>
                        <p>{{ $count_blogs }}</p>
                    </div>
                </div>
        
                <div class="stat">
                    <div class="icon">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="text">
                        <p>
                            <a href="{{ route('comments.index') }}">Comments</a>
                        </p>
                        <p>{{ $count_comments }}</p>
                    </div>
                </div>
            </div>

            <div class="analytics_wrapper">
                <div class="custom_form">
                    <form action="">
                        <div class="input_group">
                            <select name="period" id="period">
                                <option value="">Today</option>
                                <option value="">Yesterday</option>
                                <option value="">This Week</option>
                                <option value="">Last Week</option>
                                <option value="">This Month</option>
                                <option value="">Last Month</option>
                                <option value="">This Year</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="analytics">
                    <div class="analytic">
                        <div class="text">
                            <p>{{ number_format($gross_sales) }}</p>
                            <p>Gross Sales</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
    
                    <div class="analytic">
                        <div class="text">
                            <p>{{ number_format($net_sales) }}</p>
                            <p>Net Sales</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
    
                    <div class="analytic">
                        <div class="text">
                            <p>{{ number_format($cost_of_sales) }}</p>
                            <p>Cost of Sales</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
    
                    <div class="analytic">
                        <div class="text">
                            <p>{{ number_format($gross_profit) }}</p>
                            <p>Gross Profit</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="charts">
                <div class="chart">
                    <h2>Sales</h2>
                    <canvas id="salesChart"></canvas>
                </div>
                <div class="chart">
                    <h2>Order Locations</h2>
                    <canvas id="citiesChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <x-slot name="javascript">
        <script src="{{ asset('assets/js/chart.js') }}"></script>
        <script>
            const ctx = document.getElementById('salesChart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Amount',
                    data: {!! json_encode($sales_data) !!},
                    borderWidth: 1
                }]
                },
                options: {
                    responsive: true,
                }
            });

            const cities = document.getElementById('citiesChart');

                new Chart(cities, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($locations_labels) !!},
                        datasets: [{
                            label: 'Orders',
                            data: {!! json_encode($locations_orders) !!},
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            }
                        }
                }
            });
        </script>
    </x-slot>
</x-admin>