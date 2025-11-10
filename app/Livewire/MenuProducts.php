<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class MenuProducts extends Component
{
    use WithPagination;

    public $selectedCategoryId = null;
    public $selectedCategoryName = 'All Menu Items';
    public $search = '';
    public $stall = '';

    protected $queryString = [
        'selectedCategoryId' => ['except' => null, 'as' => 'category_id'],
    ];

    public function mount()
    {
        $this->selectedCategoryId = request('category_id');
        $this->search = request('search', '');
        $this->stall = request('stall', '');
        $this->updateCategoryName();
    }

    public function selectCategory($categoryId)
    {
        // Toggle: if clicking same category, deselect it
        if ($this->selectedCategoryId == $categoryId) {
            $this->selectedCategoryId = null;
        } else {
            $this->selectedCategoryId = $categoryId;
        }
        
        $this->updateCategoryName();
        $this->resetPage();
    }

    private function updateCategoryName()
    {
        if ($this->selectedCategoryId) {
            $category = Category::find($this->selectedCategoryId);
            $this->selectedCategoryName = $category ? $category->name : 'All Menu Items';
        } else {
            $this->selectedCategoryName = 'All Menu Items';
        }
    }

    public function render()
    {
        $query = Product::query()
            ->with(['stall', 'category'])
            ->where('is_available', true)
            ->where('is_published', true);

        // Filter by category
        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        // Filter by search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Filter by stall
        if ($this->stall) {
            $query->where('stall_id', $this->stall);
        }

        $products = $query->latest()->paginate(12);
        
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.menu-products', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}