<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Cart extends Component
{   
    public $cartItems = [];
    public $total = 0;
    public $isOpen = false; // Add this for sidebar toggle

    protected $listeners = [
        'cartUpdated' => 'loadCart',
        'toggleCart' => 'toggleCart',
        'addToCart' => 'addToCart',
    ];

    public function mount()
    {
        $this->loadCart();
    }

    public function toggleCart()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function openCart()
    {
        $this->isOpen = true;
    }

    public function closeCart()
    {
        $this->isOpen = false;
    }

    public function loadCart()
    {
        $cart = session()->get('cart', []);
        $this->cartItems = [];
        $this->total = 0;

        foreach ($cart as $productId => $item) {
            /** @var Product|null $product */
            $product = Product::with('stall')->find($productId);
            
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

        $this->closeCart(); // Close cart before redirecting
        return redirect()->route('checkout');
    }

    public function getCartCountProperty()
    {
        return count($this->cartItems ?? []);
    }

    public function render()
    {
        return view('livewire.cart');
    }
}