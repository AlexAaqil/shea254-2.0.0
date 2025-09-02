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
</div>
