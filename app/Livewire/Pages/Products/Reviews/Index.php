<?php

namespace App\Livewire\Pages\Products\Reviews;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Products\ProductReview;

class Index extends Component
{
    use WithPagination;

    public $confirm_review_deletion = false;
    public ?int $delete_review_id = null;

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

    protected $listeners = [
        'confirm-review-deletion' => 'confirmReviewDeletion',
    ];

    public function confirmReviewDeletion($data)
    {
        $this->delete_review_id = $data['review_id'];
        $this->dispatch('open-modal', 'confirm-review-deletion');
    }

    public function deleteReview()
    {
        if ($this->delete_review_id) {
            $review = ProductReview::findOrFail($this->delete_review_id);
            $review->delete();

            $this->delete_review_id = null;
            $this->dispatch('close-modal', 'confirm-review-deletion');
            $this->dispatch('notify', type: 'success', message: 'review deleted successfully');
        }
    }

    public function render()
    {
        $reviews = ProductReview::query()
            ->with('user')
            ->when($this->search && $this->search_performed, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('email', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $count_reviews = ProductReview::count();

        return view('livewire.pages.products.reviews.index', compact('reviews', 'count_reviews'));
    }
}
