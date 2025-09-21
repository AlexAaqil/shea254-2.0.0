<?php

namespace App\Livewire\Pages\Products\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Products\Product;

class Index extends Component
{
    use WithPagination;

    public $confirm_product_deletion = false;
    public ?int $delete_product_id = null;

    public $filter = null;
    public $search = '';
    public bool $search_performed = false;

    protected $queryString = ['search', 'filter'];

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

    public function setFilter($filter = null)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    protected $listeners = [
        'confirm-product-deletion' => 'confirmProductDeletion',
    ];

    public function confirmProductDeletion($data)
    {
        $this->delete_product_id = $data['product_id'];
        $this->dispatch('open-modal', 'confirm-product-deletion');
    }

    public function deleteProduct()
    {
        if ($this->delete_product_id) {
            $user = Product::findOrFail($this->delete_product_id);
            $user->delete();

            $this->delete_product_id = null;
            $this->dispatch('close-modal', 'confirm-product-deletion');
            $this->dispatch('notify', type: 'success', message: 'product deleted successfully');
        }
    }

    public function toggleVisibility($product_uuid)
    {
        $product = Product::where('id', $product_uuid)->firstOrFail();
        $product->is_visible = !$product->is_visible;
        $product->save();

        $this->dispatch('notify', type: 'success', message: 'visibility updated successfully');
    }

    public function toggleFeatured($product_uuid)
    {
        $product = Product::where('id', $product_uuid)->firstOrFail();
        $product->featured = !$product->featured;
        $product->save();

        $this->dispatch('notify', type: 'success', message: 'featured status updated successfully');
    }

    public function render()
    {
        $products = Product::query()
            ->select(['id', 'title', 'slug', 'selling_price', 'discount_price', 'stock_count', 'is_visible', 'featured', 'category_id'])
            ->with(['product_category', 'coverImage'])
            ->when($this->search && $this->search_performed, function ($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter === 'featured', function ($query) {
                $query->where('featured', 1);
            })
            ->when($this->filter === 'invisible', function ($query) {
                $query->where('is_visible', 0);
            })
            ->when($this->filter === 'out_of_stock', function ($query) {
                $query->where('stock_count', 0);
            })
            ->orderBy('title')
            ->paginate(40)
            ->withQueryString();

        $stats = Product::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_visible = 0 THEN 1 ELSE 0 END) as count_invisible,
            SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as count_featured,
            SUM(CASE WHEN stock_count = 0 THEN 1 ELSE 0 END) as count_out_of_stock
        ")->first();

        return view('livewire.pages.products.products.index', [
            'products' => $products,
            'count_products' => $stats->total,
            'count_invisible' => $stats->count_invisible,
            'count_featured' => $stats->count_featured,
            'count_out_of_stock' => $stats->count_out_of_stock,
        ]);
    }
}
