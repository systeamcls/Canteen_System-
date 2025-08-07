<?php

namespace App\Livewire;

use App\Models\Stall;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class MenuComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $stallFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'stallFilter' => ['except' => ''],
    ];

    public function render()
    {
        $stalls = \App\Models\Stall::with(['products' => function($query) {
                $query->where('is_available', true)
                    ->when($this->search, function($query) {
                        $query->where('name', 'like', '%'.$this->search.'%');
                    });
            }])
            ->where('is_active', true)
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->get();
        
        return view('livewire.menu-component', [
            'stalls' => $stalls
        ])->layout('layouts.app');
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);
                
        /** @var Product $product */
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'quantity' => 1,
                /** @suppress PHP6602 */
                'stall_id' => $product->stall_id ?? null,
                'product_name' => $product->name,
                'price' => $product->price,
                'image' => $product->image
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cartUpdated');

        $this->dispatch('notify', [
            'message' => 'Item added to cart!',
            'type' => 'success'
        ]);
    }
}
