<?php

namespace App\Livewire\Pages\Dashboards;

use Livewire\Component;
use App\Models\Sales\Sale;
use App\Models\Sales\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Cashier extends Component
{
    public string $period = 'today';

    protected function getDateRange(): array
    {
        $now = Carbon::now();

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

    public function getTopProducts($limit = 5)
    {
        return OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->product->title ?? 'Unknown Product',
                    'sold' => $item->total_sold,
                    'revenue' => $item->total_sold * ($item->product->selling_price ?? 0)
                ];
            });
    }

    public function getRecentOrders($limit = 5)
    {
        return Sale::with(['user', 'order_delivery'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'customer' => $order->user->full_name ?? 'Guest',
                    'amount' => $order->total_amount,
                    'status' => $order->status,
                    'order_number' => $order->order_number ?? 'Unknown Order No.',
                    'date' => $order->created_at->format('M j, Y')
                ];
            });
    }

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

            'top_products' => $this->getTopProducts(),
            'recent_orders' => $this->getRecentOrders(),
        ]);
    }
}
