<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;

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
                    'stall' => $product->stall ? $product->stall->name : 'Unknown Stall',
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
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.cart');
    }
}