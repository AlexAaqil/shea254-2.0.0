<?php

namespace App\Livewire\Pages\Dashboards;

use Livewire\Component;
use App\Models\Sales\Sale;
use Carbon\Carbon;

class Cashier extends Component
{
    public string $period = 'today';

    public function render()
    {
        $dateRange = $this->getDateRange();

        $orders = Sale::whereBetween('sales.created_at', [$dateRange['start'], $dateRange['end']])->count();

        $revenue = Sale::whereBetween('sales.created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('total_amount');

        $units_sold = Sale::whereBetween('sales.created_at', [$dateRange['start'], $dateRange['end']])
            ->join('order_items', 'sales.id', '=', 'order_items.order_id')
            ->sum('order_items.quantity');

        return view('livewire.pages.dashboards.cashier', [
            'revenue'    => $revenue,
            'orders'     => $orders,
            'units_sold' => $units_sold,
            'date_range' => $dateRange,
        ]);
    }

    protected function getDateRange(): array
    {
        $now = Carbon::now(); // always use current system date/time

        switch ($this->period) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end'   => $now->copy()->endOfDay(),
                ];
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDay()->startOfDay(),
                    'end'   => $now->copy()->subDay()->endOfDay(),
                ];
            case 'this_week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end'   => $now->copy()->endOfWeek(),
                ];
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end'   => $now->copy()->endOfMonth(),
                ];
            default:
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end'   => $now->copy()->endOfDay(),
                ];
        }
    }
}
