<div class="CashierDashboard">
    <div class="filters">
        <select wire:model.live="period">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="this_week">This Week</option>
            <option value="this_month">This Month</option>
        </select>
    </div>

    <!-- Debug information -->
    <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; font-size: 12px;">
        <strong>Debug Info:</strong><br>
        Period: <strong>{{ $period }}</strong><br>
        Date Range: {{ $date_range['start']->format('Y-m-d H:i:s') }} to {{ $date_range['end']->format('Y-m-d H:i:s') }}<br>
        Orders: {{ $orders }} | Revenue: KSh {{ number_format($revenue, 2) }} | Units: {{ $units_sold }}
    </div>

    <div class="stats">
        <div class="stat">
            <p>{{ $orders }}</p>
            <p>Orders</p>
        </div>
        <div class="stat">
            <p>KSh {{ number_format($revenue, 2) }}</p>
            <p>Revenue</p>
        </div>
        <div class="stat">
            <p>{{ $units_sold }}</p>
            <p>Units Sold</p>
        </div>
    </div>
</div>
