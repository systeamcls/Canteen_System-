<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CheckoutForm extends Component
{
    #[Validate('required|string|max:255')]
    public string $customerName = '';

    #[Validate('required|email|max:255')]
    public string $customerEmail = '';

    #[Validate('required|string|max:20')]
    public string $customerPhone = '';

    #[Validate('required|string|in:gcash,paymaya,card,cash')]
    public string $paymentMethod = 'gcash';

    #[Validate('required|string|in:onsite,online')]
    public string $orderType = 'online';

    #[Validate('nullable|string|max:500')]
    public string $notes = '';

    // Component state
    public array $cartSnapshot = [];
    public float $totalAmount = 0.00;
    public bool $isProcessing = false;
    public string $successMessage = '';
    public string $errorMessage = '';

    // Customer type context (passed from controller)
    public string $initialCustomerType = 'guest';
    public ?string $initialUserType = null;
    public array $availablePaymentMethods = [];
    public array $availableOrderTypes = [];

    protected CartService $cartService;
    protected PaymentService $paymentService;

    public function boot(CartService $cartService, PaymentService $paymentService): void
    {
        $this->cartService = $cartService;
        $this->paymentService = $paymentService;
    }

    public function mount(
        string $initialCustomerType = 'guest',
        ?string $initialUserType = null,
        array $availablePaymentMethods = [],
        array $availableOrderTypes = []
    ): void {
        $this->initialCustomerType = $initialCustomerType;
        $this->initialUserType = $initialUserType;
        $this->availablePaymentMethods = $availablePaymentMethods;
        $this->availableOrderTypes = $availableOrderTypes;

        $this->ensureGuestToken();
        $this->loadCartSnapshot();
        $this->prefillUserData();
        $this->setDefaultOptions();
    }

    public function submitOrder(): void
    {
        if ($this->isProcessing) {
            return;
        }

        $this->validate();
        
        if (empty($this->cartSnapshot)) {
            $this->errorMessage = 'Your cart is empty. Please add items before checkout.';
            return;
        }

        $this->isProcessing = true;
        $this->errorMessage = '';
        $this->successMessage = '';

        try {
            DB::transaction(function () {
                // Create order group
                $orderGroup = $this->createOrderGroup();
                
                // Create individual orders per vendor
                $this->createOrdersFromSnapshot($orderGroup);
                
                // Process payment
                $paymentResult = $this->processPayment($orderGroup);
                
                if (!$paymentResult['success']) {
                    throw new \Exception($paymentResult['error'] ?? 'Payment processing failed');
                }
                
                // Handle different payment flows
                if ($this->paymentMethod === 'cash') {
                    // Cash payment - redirect to success page
                    $this->handleCashPayment($orderGroup);
                } else {
                    // Online payment - redirect to payment gateway
                    $this->handleOnlinePayment($paymentResult);
                }
            });

        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to process order: ' . $e->getMessage();
            $this->dispatch('checkout-error', ['message' => $e->getMessage()]);
            
            logger()->error('Checkout failed', [
                'user_id' => Auth::id(),
                'guest_token' => Session::get('guest_cart_token'),
                'customer_type' => $this->initialCustomerType,
                'user_type' => $this->initialUserType,
                'cart_snapshot' => $this->cartSnapshot,
                'payment_method' => $this->paymentMethod,
                'exception' => $e->getTraceAsString()
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    #[Computed]
    public function paymentMethodOptions(): array
    {
        // FIX: Return properly structured array with 'label' keys
        $methods = [
            'gcash' => [
                'value' => 'gcash',
                'label' => 'GCash',
                'description' => 'Pay with GCash e-wallet',
                'icon' => 'payment-icon',
                'available' => true,
            ],
            'paymaya' => [
                'value' => 'paymaya',
                'label' => 'PayMaya',
                'description' => 'Pay with PayMaya e-wallet',
                'icon' => 'payment-icon',
                'available' => true,
            ],
            'card' => [
                'value' => 'card',
                'label' => 'Credit/Debit Card',
                'description' => 'Pay with credit or debit card',
                'icon' => 'card-icon',
                'available' => true,
            ],
        ];

        // Only add cash payment for logged-in users (not guests)
        if ($this->initialCustomerType === 'logged_in') {
            $methods['cash'] = [
                'value' => 'cash',
                'label' => 'Cash Payment',
                'description' => 'Pay when you collect your order',
                'icon' => 'cash-icon',
                'available' => true,
            ];
        }

        // Filter based on available payment methods if provided
        if (!empty($this->availablePaymentMethods)) {
            $allowedMethods = [];
        
        foreach ($this->availablePaymentMethods as $key => $value) {
            if (is_string($value)) {
                // If it's just a list of method names: ['gcash', 'paymaya']
                $allowedMethods[] = $value;
            } elseif (is_string($key)) {
                // If it's associative: ['gcash' => [...], 'paymaya' => [...]]
                $allowedMethods[] = $key;
            }
        }
        
        // Only return methods that are in the allowed list
        return array_filter($methods, function($key) use ($allowedMethods) {
            return in_array($key, $allowedMethods);
        }, ARRAY_FILTER_USE_KEY);
        }

        return $methods;
    }

    #[Computed]
    public function orderTypeOptions(): array
    {
        return $this->availableOrderTypes;
    }

    #[Computed]
    public function showOrderTypeSelection(): bool
    {
        return $this->initialCustomerType === 'logged_in' && 
               $this->initialUserType === 'employee' &&
               count($this->availableOrderTypes) > 1;
    }

    #[Computed]
    public function customerTypeLabel(): string
    {
        if ($this->initialCustomerType === 'guest') {
            return 'Guest Customer';
        }
        
        if ($this->initialUserType === 'employee') {
            return 'Employee';
        }
        
        return 'Registered User';
    }

    private function ensureGuestToken(): void
    {
        if ($this->initialCustomerType === 'guest' && !Session::has('guest_cart_token')) {
            Session::put('guest_cart_token', \Illuminate\Support\Str::random(32));
        }
    }

    private function loadCartSnapshot(): void
    {
        try {
            $cartTotals = $this->cartService->getCartTotals();
            
            $this->cartSnapshot = [];
            $this->totalAmount = 0.00;

            if (!empty($cartTotals['items'])) {
                foreach ($cartTotals['items'] as $item) {
                    $this->cartSnapshot[] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Unknown Product',
                        'vendor_id' => $item->vendor_id,
                        'vendor_name' => $item->vendor->name ?? 'Unknown Vendor',
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'line_total' => $item->line_total,
                    ];
                }
                
                $this->totalAmount = $cartTotals['total'];
            }
        } catch (\Exception $e) {
            logger()->error('Failed to load cart snapshot: ' . $e->getMessage());
            $this->cartSnapshot = [];
            $this->totalAmount = 0.00;
        }
    }

    private function prefillUserData(): void
    {
        if ($this->initialCustomerType === 'logged_in' && Auth::check()) {
            $user = Auth::user();
            $this->customerName = $user->name ?? '';
            $this->customerEmail = $user->email ?? '';
            $this->customerPhone = $user->phone ?? '';
        }
    }

    private function setDefaultOptions(): void
    {
        // Set default payment method based on available options
        $availableMethods = array_keys($this->availablePaymentMethods);
        if (!empty($availableMethods)) {
            $this->paymentMethod = $availableMethods[0];
        }

        // Set default order type
        if ($this->initialUserType === 'employee') {
            $this->orderType = 'onsite';
        } else {
            $this->orderType = 'online';
        }
    }

    private function createOrderGroup(): OrderGroup
    {
        return OrderGroup::create([
             'payer_type' => session('user_type', 'guest'),
            'user_id' => Auth::id(),
            'guest_token' => $this->initialCustomerType === 'guest' ? Session::get('guest_cart_token') : null,
            'payment_method' => $this->paymentMethod === 'cash' ? 'onsite' : 'online',
            'payment_status' => 'pending',
            'amount_total' => (int)($this->totalAmount * 100), // Convert to centavos
            'currency' => 'PHP',
            'billing_contact' => [
                'name' => $this->customerName,
                'email' => $this->customerEmail,
                'phone' => $this->customerPhone,
            ],
            'cart_snapshot' => $this->cartSnapshot,
        ]);
    }

    private function createOrdersFromSnapshot(OrderGroup $orderGroup): void
    {
        $itemsByVendor = collect($this->cartSnapshot)->groupBy('vendor_id');

        foreach ($itemsByVendor as $vendorId => $vendorItems) {
            $vendorTotal = $vendorItems->sum('line_total');
            
            $order = Order::create([
                'order_group_id' => $orderGroup->id,
                'user_id' => Auth::id(),
                'guest_token' => $this->initialCustomerType === 'guest' ? Session::get('guest_cart_token') : null,
                'vendor_id' => $vendorId,
                'total_amount' => $vendorTotal,
                'status' => 'pending',
                'payment_method' => $this->paymentMethod === 'cash' ? 'onsite' : 'online',
                'order_type' => $this->orderType,
                'customer_name' => $this->customerName,
                'customer_email' => $this->customerEmail,
                'customer_phone' => $this->customerPhone,
            ]);

            foreach ($vendorItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'subtotal' => $item['line_total'],
                ]);
            }
        }
    }

    private function processPayment(OrderGroup $orderGroup): array
    {
        $customerData = [
            'name' => $this->customerName,
            'email' => $this->customerEmail,
            'phone' => $this->customerPhone,
        ];

        return $this->paymentService->processPayment(
            $orderGroup,
            $this->paymentMethod,
            $customerData
        );
    }

    private function handleCashPayment(OrderGroup $orderGroup): void
    {
        $this->clearCart();
        
        $this->successMessage = "Order placed successfully! You can pay cash when you collect your order.";
        
        // Debug the order group
        logger()->info('HandleCashPayment called', [
            'order_group_id' => $orderGroup->id,
            'order_group_exists' => $orderGroup->exists,
            'order_group_attributes' => $orderGroup->getAttributes()
        ]);
    
    // Ensure we have a valid order group ID
    if (!$orderGroup->id) {
        logger()->error('Order group has no ID!');
        throw new \Exception('Order group was not saved properly');
    }

        // Redirect to success page after a short delay
        $this->dispatch('order-completed', [
            'orderGroupId' => $orderGroup->id,
            'paymentMethod' => 'cash'
        ]);
        logger()->info('Event dispatched', [
        'orderGroupId' => (int) $orderGroup->id,
        'paymentMethod' => 'cash'
        ]);
    }

   private function handleOnlinePayment(array $paymentResult): void
{
    logger()->info('Payment result in handleOnlinePayment: ', $paymentResult);
    
    $this->clearCart();
    
    if (isset($paymentResult['checkout_url'])) {
        logger()->info('Checkout URL found, dispatching redirect', [
            'url' => $paymentResult['checkout_url'],
            'paymentId' => $paymentResult['payment_id']
        ]);
        
        // Redirect to PayMongo checkout
        $this->dispatch('redirect-to-payment', [
            'url' => $paymentResult['checkout_url'],
            'paymentId' => $paymentResult['payment_id']
        ]);
    } else {
        logger()->error('No checkout_url in payment result: ', $paymentResult);
        throw new \Exception('No checkout URL received from payment gateway');
    }
}

    private function clearCart(): void
    {
        try {
            $this->cartService->clearCart();
            $this->cartSnapshot = [];
            $this->totalAmount = 0.00;
        } catch (\Exception $e) {
            logger()->error('Failed to clear cart: ' . $e->getMessage());
        }
    }

    public function refreshCart(): void
    {
        $this->loadCartSnapshot();
    }

    public function render()
    {
        return view('livewire.checkout-form');
    }

}