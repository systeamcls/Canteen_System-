<?php

namespace App\Http\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class Checkout extends Component
{
    public $cartItems = [];
    public $total = 0;
    public $paymentMethod = '';
    public $specialInstructions = '';
    public $orderType = 'onsite';
    public $userType = null;

    public function mount()
    {
        // Determine user type based on authentication status
        if (Auth::check()) {
            $this->userType = 'employee';
        } else {
            $this->userType = 'guest';
        }

        // Set default payment method based on user type
        $this->paymentMethod = $this->userType === 'guest' ? 'online' : 'cash';
        
        $this->loadCart();
    }

    public function handleUserTypeUpdate($type)
    {
        // This method is no longer needed since we use authentication
        // but keeping it for backwards compatibility
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

        if (empty($this->cartItems)) {
            return redirect()->route('menu');
        }
    }

    public function getAvailablePaymentMethods()
    {
        $methods = [
            'online' => [
                'name' => 'Online Payment',
                'description' => 'Pay with card or e-wallet',
                'icon' => 'credit-card'
            ]
        ];

        // Only authenticated users (employees) can access onsite payment
        if (Auth::check()) {
            $methods['cash'] = [
                'name' => 'Cash Payment',
                'description' => 'Pay at counter',
                'icon' => 'cash'
            ];
        }

        return $methods;
    }

    public function placeOrder()
    {
        // Validate payment method based on user authentication
        $allowedPaymentMethods = Auth::check() 
            ? ['cash', 'online', 'card', 'e-wallet'] 
            : ['online', 'card', 'e-wallet'];

        $this->validate([
            'paymentMethod' => 'required|in:' . implode(',', $allowedPaymentMethods),
            'orderType' => 'required|in:online,onsite',
            'specialInstructions' => 'nullable|string|max:500',
        ]);

        $order = Order::create([
            'user_id' => Auth::id() ?? null,
            'guest_token' => !Auth::check() ? Str::random(32) : null,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'status' => 'pending',
            'total_amount' => $this->total,
            'payment_method' => $this->paymentMethod,
            'payment_status' => 'pending',
            'order_type' => $this->orderType,
            'special_instructions' => $this->specialInstructions,
            'user_type' => $this->userType
        ]);

        foreach ($this->cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->getKey(),
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        // Store guest token in session if guest order
        if (!Auth::check()) {
            session()->put('guest_token', $order->guest_token);
        }

        session()->forget('cart');
        
        return redirect()->route('order.success', $order);
    }

    public function render()
    {
        return view('livewire.checkout', [
            'availablePaymentMethods' => $this->getAvailablePaymentMethods()
        ]);
    }
}