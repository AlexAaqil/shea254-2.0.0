<?php

namespace App\Livewire\Pages\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sales\Sale;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public bool $search_performed = false;

    protected $queryString = ['search'];

    public function performSearch()
    {
        $this->search_performed = true;
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->search_performed = false;
        $this->resetPage();
    }

    public function render()
    {
        $orders = Sale::query()
            ->with(['order_delivery', 'payment'])
            ->whereHas('order_delivery')
            ->when($this->search && $this->search_performed, function($query) {
                $query->where(function($query) {
                    $query->where('order_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('order_delivery', function($q) {
                            $q->where('full_name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

	    $orders_without_delivery = Sale::with(['order_delivery', 'order_items'])->whereDoesntHave('order_delivery')->orderBy('id', 'desc')->get();

        $stats = DB::table('sales as s')
            ->leftJoin('payments as p', 'p.order_id', '=', 's.id')
            ->leftJoin('order_deliveries as od', 'od.order_id', '=', 's.id')
            ->selectRaw("
                COUNT(DISTINCT s.id) as total_orders,

                SUM(
                    CASE
                        WHEN NOT EXISTS (
                            SELECT 1
                            FROM payments
                            WHERE payments.order_id = s.id
                            AND payments.status = 'paid'
                        ) THEN 1 ELSE 0
                    END
                ) as unpaid_orders,

                SUM(
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM order_deliveries
                            WHERE order_deliveries.order_id = s.id
                            AND order_deliveries.delivery_status <> 'processed'
                        ) THEN 1 ELSE 0
                    END
                ) as undelivered_orders,

                SUM(
                    CASE
                        WHEN NOT EXISTS (
                            SELECT 1
                            FROM order_deliveries
                            WHERE order_deliveries.order_id = s.id
                        ) THEN 1 ELSE 0
                    END
                ) as invalid_orders
            ")
            ->first();

        return view('livewire.pages.orders.index', [
            'orders' => $orders,
            'orders_without_delivery' => $orders_without_delivery,

            'count_orders' => $stats->total_orders,
            'count_unpaid_orders' => $stats->unpaid_orders,
            'count_undelivered_orders' => $stats->undelivered_orders,
            'count_invalid_orders' => $stats->invalid_orders,
        ]);
    }
}
