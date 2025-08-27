<?php

namespace App\Livewire\Pages\Products\Measurements;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Products\ProductMeasurement;

class Index extends Component
{
    use WithPagination;

    public $confirm_measurement_deletion = false;
    public ?int $delete_measurement_id = null;

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
        'confirm-measurement-deletion' => 'confirmMeasurementDeletion',
    ];

    public function confirmMeasurementDeletion($data)
    {
        $this->delete_measurement_id = $data['measurement_id'];
        $this->dispatch('open-modal', 'confirm-measurement-deletion');
    }

    public function deleteMeasurement()
    {
        if ($this->delete_measurement_id) {
            $user = ProductMeasurement::findOrFail($this->delete_measurement_id);
            $user->delete();

            $this->delete_measurement_id = null;
            $this->dispatch('close-modal', 'confirm-measurement-deletion');
            $this->dispatch('notify', type: 'success', message: 'measurement deleted successfully');
        }
    }

    public function render()
    {
        $measurements = ProductMeasurement::query()
            ->when($this->search && $this->search_performed, function ($query) {
                $query->where(function($q) {
                    $q->where('measurement_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('measurement_name')
            ->paginate(50)
            ->withQueryString();

        $count_measurements = ProductMeasurement::count();

        return view('livewire.pages.products.measurements.index', compact('measurements', 'count_measurements'));
    }
}
