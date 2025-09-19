<?php

namespace App\Livewire\Pages\Orders;

use Livewire\Component;
use App\Models\Sales\Sale;

class Edit extends Component
{
    public $order;
    public $additional_information, $delivery_status;

    public $confirm_order_deletion = false;
    public ?int $delete_order_id = null;

    protected $listeners = [
        'confirm-order-deletion' => 'confirmOrderDeletion',
    ];

    public function confirmOrderDeletion($data)
    {
        $this->delete_order_id = $data['order_id'];
        $this->dispatch('open-modal', 'confirm-order-deletion');
    }

    public function deleteOrder()
    {
        if ($this->delete_order_id) {
            $sale = Sale::findOrFail($this->delete_order_id);
            $sale->delete();

            $this->delete_order_id = null;
            $this->dispatch('close-modal', 'confirm-order-deletion');
            session()->flash('notify', ['message' => 'order deleted successfully', 'type' => 'success']);
            return redirect()->route('orders.index');
        }
    }

    public function mount($order)
    {
        $this->order = Sale::with('order_delivery', 'order_items', 'payment')->where('id', $order)->firstOrFail();
        $this->additional_information = $this->order->order_delivery->additional_information ?? '';
        $this->delivery_status = $this->order->order_delivery->delivery_status ?? 'pending';
    }

    public function rules()
    {
        return [
            'additional_information' => 'nullable|string|max:255',
            'delivery_status' => 'required|in:pending,processed',
        ];
    }

    public function updateOrder()
    {
        $this->validate();

        $this->order->order_delivery->update([
            'additional_information' => $this->additional_information,
            'delivery_status' => $this->delivery_status,
        ]);

        $this->dispatch('notify', 'order updated successfully', 'success');

        $this->redirectRoute('orders.index');
    }

    public function render()
    {
        return view('livewire.pages.orders.edit');
    }
}

