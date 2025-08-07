<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class CartComponent extends Component
{
    protected $listeners = ['cartUpdated' => 'loadCart'];

    public $cartItems = [];
    public $total = 0;

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
            $product = Product::find($productId);
            if ($product) {
                $this->cartItems[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'stall' => $product->stall?->name ?? 'Unknown Stall',
                    'subtotal' => $product->price * $item['quantity']
                ];
                $this->total += $product->price * $item['quantity'];
            }
        }
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cartUpdated');
        }
    }

    public function render()
    {
        return view('livewire.cart-component')->layout('layouts.app');
    }
}
