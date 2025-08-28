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
                    <p>xxx</p>
                    <p>Orders</p>
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
                    <p>xxx</p>
                    <p>Blogs & xxx Categories</p>
                </div>

                <div class="stat">
                    <p>{{ $count_messages }}</p>
                    <p>{{ Str::plural('Message', $count_messages) }}</p>
                </div>
            </div>
        </div>
    </section>
</div>
