<?php

namespace App\Livewire\Pages\Deliveries\Locations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Deliveries\DeliveryLocation;

class Index extends Component
{
    use WithPagination;

    public $confirm_location_deletion = false;
    public ?int $delete_location_id = null;

    public $search = '';
    public bool $search_performed = false;

    // Include search in URL query string
    protected $queryString = ['search'];

    // Reset page when search input changes
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

    protected $listeners = [
        'confirm-location-deletion' => 'confirmLocationDeletion',
    ];

    public function confirmLocationDeletion($data)
    {
        $this->delete_location_id = $data['location_id'];
        $this->dispatch('open-modal', 'confirm-location-deletion');
    }

    public function deleteLocation()
    {
        if ($this->delete_location_id) {
            $location = DeliveryLocation::findOrFail($this->delete_location_id);
            $location->delete();

            $this->delete_location_id = null;
            $this->dispatch('close-modal', 'confirm-location-deletion');
            $this->dispatch('notify', type: 'success', message: 'location deleted successfully');
        }
    }

    public function render()
    {
        $locations = DeliveryLocation::query()
            ->withCount('delivery_areas')
            ->when($this->search && $this->search_performed, function ($query) {
                $query->where(function($q) {
                    $q->where('location_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('location_name')
            ->paginate(50)
            ->withQueryString();

        $count_locations = $locations->total();

        return view('livewire.pages.deliveries.locations.index', compact('locations', 'count_locations'));
    }
}
