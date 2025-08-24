<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Cart extends Component
{   
    public $cartItems = [];
    public $total = 0;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $cart = session()->get('cart', []);
        $this->cartItems = [];
        $this->total = 0;

        foreach ($cart as $productId => $item) {
    /** @var Product|null $product */
    $product = Product::with('stall')->find($productId); // Eager load stall
            
            if ($product) {
                $this->cartItems[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'quantity' => $item['quantity'],
                    'stall' => $product->stall?->name ?? 'Unknown Stall',
                    'subtotal' => $product->price * $item['quantity']
                ];
                $this->total += $product->price * $item['quantity'];
            }
        }
    }

    public function incrementQuantity($productId)
    {
        $cart = session()->get('cart', []);
        $cart[$productId]['quantity']++;
        session()->put('cart', $cart);
        $this->loadCart();
    }

    public function decrementQuantity($productId)
    {
        $cart = session()->get('cart', []);
        if ($cart[$productId]['quantity'] > 1) {
            $cart[$productId]['quantity']--;
            session()->put('cart', $cart);
        } else {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
        $this->loadCart();
    }

    public function removeItem($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        $this->loadCart();
    }

    public function checkout()
    {
        // Check if user type is set
        $userType = session('user_type');
        if (!$userType) {
            return redirect('/')->with('showModal', true);
        }

        // For guests, no auth required, for employees, auth required
        if ($userType === 'employee' && !Auth::check()) {
            return redirect('/')->with('showModal', true);
        }

        return redirect()->route('checkout');
    }

    public function render()
    {
        // Defensive programming: ensure cartItems is always an array
        if (!is_array($this->cartItems)) {
            $this->cartItems = [];
        }
        
        return view('livewire.cart');
    }
}