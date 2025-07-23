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
    public $showLoginModal = false;

    protected $listeners = ['userTypeUpdated' => 'handleUserTypeUpdate'];

    public function mount()
    {
        // Get user type from session or determine based on auth status
        $this->userType = session('user_type') ?? (Auth::check() ? 'employee' : null);

        // If no user type is set, show login modal
        if (!$this->userType) {
            $this->showLoginModal = true;
            return;
        }

        // Set default payment method based on user type
        $this->paymentMethod = $this->userType === 'guest' ? 'online' : 'cash';
        
        $this->loadCart();
    }

    public function handleUserTypeUpdate($type)
    {
        $this->userType = $type;
        $this->showLoginModal = false;
        $this->paymentMethod = $type === 'guest' ? 'online' : 'cash';
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

        // Only employees can access onsite payment
        if ($this->userType === 'employee') {
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
        // Validate user type is set
        if (!$this->userType) {
            $this->showLoginModal = true;
            return;
        }

        // Validate payment method based on user type
        $allowedPaymentMethods = $this->userType === 'guest' 
            ? ['online'] 
            : ['cash', 'online', 'card', 'e-wallet'];

        $this->validate([
            'paymentMethod' => 'required|in:' . implode(',', $allowedPaymentMethods),
            'orderType' => 'required|in:online,onsite',
            'specialInstructions' => 'nullable|string|max:500',
        ]);

        $order = Order::create([
            'user_id' => Auth::id() ?? null,
            'guest_token' => $this->userType === 'guest' ? Str::random(32) : null,
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
        if ($this->userType === 'guest') {
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