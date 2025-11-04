<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Helpers\RecaptchaHelper;
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
    public string $paymentMethod = '';

    #[Validate('required|string|in:onsite,online')]
    public string $orderType = 'online';

    #[Validate('nullable|string|max:500')]
    public string $notes = '';


    public string $customerPhone = '';
    public string $customerEmail = '';
    public string $notificationPreference = '';

    public string $recaptcha_token = '';

    public bool $showPaymentMethods = false;
    public ?string $selectedPaymentMethod = null;
    public bool $showExpandedItems = false;

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


    protected function rules()
{
    $rules = [
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'required|email|max:255',
        'customerPhone' => 'required|string|regex:/^(09\\d{9}|63\\d{10}|\\+63\\d{10})$/',
        'notificationPreference' => 'required|string|in:sms,email,both',
        'paymentMethod' => 'required|string|in:gcash,paymaya,card,cash',
        'notes' => 'nullable|string|max:500',
    ];

    if (in_array($this->notificationPreference, ['sms', 'both'])) {
        $rules['customerPhone'] = [
            'required',
            'string',
            'regex:/^(09\\d{9}|63\\d{10}|\\+63\\d{10})$/',
            'min:11',
            'max:13'
        ];
    }

    if (in_array($this->notificationPreference, ['email', 'both'])) {
        $rules['customerEmail'] = 'required|email|max:255';
    }

    return $rules;
}

    public function togglePaymentMethods()
{
    $this->showPaymentMethods = !$this->showPaymentMethods;
}

public function toggleExpandedItems()
{
    $this->showExpandedItems = !$this->showExpandedItems;
}

public function selectPaymentMethod($method)
{
    $this->selectedPaymentMethod = $method;
    $this->paymentMethod = $method;
    $this->showPaymentMethods = false;

    $this->resetErrorBag('paymentMethod');
}

public function changePaymentMethod()
{
    $this->selectedPaymentMethod = null;
    $this->paymentMethod = '';
    $this->showPaymentMethods = true;
}

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

    if (empty($this->paymentMethod)) {
        $this->addError('paymentMethod', 'Please select a payment method to continue.');
        $this->dispatch('checkout-error');
        return;
    }

    // ✅ ADD: Verify reCAPTCHA FIRST
    $action = Auth::check() ? 'checkout' : 'guest_checkout';
    $minScore = RecaptchaHelper::getScoreThreshold($action);
    
    if (!RecaptchaHelper::verify($this->recaptcha_token, $action, $minScore)) {
        $this->errorMessage = 'Security verification failed. Please try again.';
        $this->dispatch('checkout-error', ['message' => 'Security verification failed']);
        $this->recaptcha_token = ''; // Reset token
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
                        'product_image' => $item->product->image ?? null,
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

        // Set default order type
        if ($this->initialUserType === 'employee') {
            $this->orderType = 'onsite';
        } else {
            $this->orderType = 'online';
        }
        
        $this->notificationPreference = 'sms';
        $this->customerPhone = '09';
    }

    private function createOrderGroup(): OrderGroup
    {

    $billingContact = ['name' => $this->customerName];
    
    // Only include email/phone if they were required and filled
    if (in_array($this->notificationPreference, ['email', 'both'])) {
        $billingContact['email'] = $this->customerEmail;
    }
    
    if (in_array($this->notificationPreference, ['sms', 'both'])) {
        $billingContact['phone'] = $this->customerPhone;
    }

        return OrderGroup::create([
            'payer_type' => session('user_type') === 'employee' ? 'employee' : 'guest',
            'user_id' => Auth::id(),
            'guest_token' => $this->initialCustomerType === 'guest' ? Session::get('guest_cart_token') : null,
            'payment_method' => $this->paymentMethod === 'cash' ? 'onsite' : 'online',
            'payment_status' => 'pending',
            'amount_total' => (int)$this->totalAmount,
            'currency' => 'PHP',
            'billing_contact' => $billingContact,
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
    
    // Ensure order group is saved and has an ID
    if (!$orderGroup->exists || !$orderGroup->id) {
        logger()->error('Order group not saved!', [
            'exists' => $orderGroup->exists,
            'id' => $orderGroup->id,
            'attributes' => $orderGroup->getAttributes()
        ]);
        throw new \Exception('Order was not saved properly. Please try again.');
    }

    // Force refresh from database to ensure ID is set
    $orderGroup->refresh();
    
    $orderGroupId = (int) $orderGroup->id;
    
    logger()->info('Cash payment - redirecting to success', [
        'order_group_id' => $orderGroupId,
        'order_group_exists' => $orderGroup->exists,
    ]);

    // Dispatch event with explicit integer ID
    $this->dispatch('order-completed', 
        orderGroupId: $orderGroupId,
        paymentMethod: 'cash'
    );
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

    public function updateQuantity($index, $newQuantity)
{
    if ($newQuantity < 1) {
        return;
    }
    
    if (isset($this->cartSnapshot[$index])) {
        // Update quantity in cart snapshot
        $this->cartSnapshot[$index]['quantity'] = $newQuantity;
        $this->cartSnapshot[$index]['line_total'] = $this->cartSnapshot[$index]['unit_price'] * $newQuantity;
        
        // Recalculate total
        $this->totalAmount = collect($this->cartSnapshot)->sum('line_total');
        
        // If you have a cart service, also update the actual cart
        // $this->cartService->updateQuantity($cartItemId, $newQuantity);
    }
}

public function removeItem($index)
{
    if (isset($this->cartSnapshot[$index])) {
        // Remove item from snapshot
        unset($this->cartSnapshot[$index]);
        $this->cartSnapshot = array_values($this->cartSnapshot); // Re-index array
        
        // Recalculate total
        $this->totalAmount = collect($this->cartSnapshot)->sum('line_total');
        
        // If you have a cart service, also remove from actual cart
        // $this->cartService->removeFromCart($cartItemId);
        
        // Redirect to menu if cart is empty
        if (empty($this->cartSnapshot)) {
            return redirect()->route('menu.index')->with('info', 'Your cart is now empty.');
        }
    }
}

protected function messages()
{
    return [
        'customerPhone.regex' => 'Please enter a valid Philippine phone number (e.g., 09123456789, 639123456789, or +639123456789)',
        'customerPhone.required' => 'Phone number is required for SMS notifications',
        'customerEmail.required' => 'Email address is required for email notifications',
    ];
}

}