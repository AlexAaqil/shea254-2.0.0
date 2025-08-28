<?php

namespace App\Livewire\Pages\Deliveries\Areas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Deliveries\DeliveryLocation;
use App\Models\Deliveries\DeliveryArea;

class Index extends Component
{
    use WithPagination;

    public ?int $delete_area_id = null;
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

    public function deleteArea()
    {
        if ($this->delete_area_id) {
            $area = DeliveryArea::findOrFail($this->delete_area_id);
            $area->delete();

            $this->delete_area_id = null;
            $this->dispatch('close-modal', 'confirm-area-deletion');
            $this->dispatch('notify', type: 'success', message: 'Area deleted successfully');
        }
    }

    public function render()
    {
        $locations = DeliveryLocation::query()
            ->with(['delivery_areas' => function ($query) {
                $query->when($this->search && $this->search_performed, function ($q) {
                    $q->where('area_name', 'like', '%' . $this->search . '%');
                })
                ->orderBy('area_name');
            }])
            ->orderBy('location_name')
            ->paginate(20)
            ->withQueryString();

        $count_areas = DeliveryArea::count();

        return view('livewire.pages.deliveries.areas.index', compact('locations', 'count_areas'));
    }
}

