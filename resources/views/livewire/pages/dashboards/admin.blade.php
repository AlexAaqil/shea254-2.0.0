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
                    <p>xxx</p>
                    <p>Locations & xxx Areas</p>
                </div>

                <div class="stat">
                    <p>xxx</p>
                    <p>Blogs</p>
                </div>

                <div class="stat">
                    <p>{{ $count_messages }}</p>
                    <p>{{ Str::plural('Message', $count_messages) }}</p>
                </div>
            </div>
        </div>
    </section>
</div>
