<?php

namespace App\Livewire\Pages\Blogs\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Blogs\BlogCategory;

class Index extends Component
{
    use WithPagination;

    public $confirm_category_deletion = false;
    public ?int $delete_category_id = null;

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
        'confirm-category-deletion' => 'confirmCategoryDeletion',
    ];

    public function confirmCategoryDeletion($data)
    {
        $this->delete_category_id = $data['category_id'];
        $this->dispatch('open-modal', 'confirm-category-deletion');
    }

    public function deleteCategory()
    {
        if ($this->delete_category_id) {
            $category = BlogCategory::findOrFail($this->delete_category_id);
            $category->delete();

            $this->delete_category_id = null;
            $this->dispatch('close-modal', 'confirm-category-deletion');
            $this->dispatch('notify', type: 'success', message: 'category deleted successfully');
        }
    }

    public function render()
    {
        $categories = BlogCategory::query()
            ->withCount('blogs')
            ->when($this->search && $this->search_performed, function ($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('title')
            ->paginate(40)
            ->withQueryString();

        $count_categories = BlogCategory::count();

        return view('livewire.pages.blogs.categories.index', compact('categories', 'count_categories'));
    }
}
