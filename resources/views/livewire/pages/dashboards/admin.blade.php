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
                    <p>{{ $count_admins }}</p>
                    <p>{{ Str::plural('Admin', $count_admins) }}</p>
                </div>

                <div class="stat">
                    <p>{{ $count_users }}</p>
                    <p>{{ Str::plural('User', $count_users) }}</p>
                </div>

                <div class="stat">
                    <p>{{ $count_messages }}</p>
                    <p>{{ Str::plural('Message', $count_messages) }}</p>
                </div>
            </div>
        </div>
    </section>
</div>
