<?php

namespace App\Http\Livewire;

use App\Models\Stall;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Menu extends Component
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
        $stalls = Stall::with(['products' => function($query) {
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

        return view('livewire.menu', [
            'stalls' => $stalls // Make sure this is being passed
        ]);
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
        $this->emit('cartUpdated');

        $this->dispatchBrowserEvent('notify', [
            'message' => 'Item added to cart!',
            'type' => 'success'
        ]);
    }
}