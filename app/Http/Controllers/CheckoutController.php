<?php

namespace App\Http\Controllers;

use App\Models\OrderGroup;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the checkout page with Livewire component
     */
    public function index()
    {
        
        /** @var \App\Models\User|null $user */
    $user = Auth::user();

    // Restrict unverified employees
    if (
        Auth::check() &&
        session('user_type') === 'employee' &&
        !$user->hasVerifiedEmail()
    ) {
        return redirect()->route('verification.notice')
            ->with('error', 'Please verify your email address to proceed with checkout.');
    }

        // Get cart data for pre-checkout validation
        $cartTotals = $this->cartService->getCartTotals();
        
        // Redirect if cart is empty
        if (empty($cartTotals['items']) || $cartTotals['total'] <= 0) {
            return redirect()->route('menu.index')
                ->with('warning', 'Your cart is empty. Please add items before checkout.');
        }

        // Determine customer type and available options
        $customerType = Auth::check() ? 'logged_in' : 'guest';
        $userType = Auth::check() ? Auth::user()->user_type : null;
        
        // Get available payment methods from PaymentService
        $paymentService = app(\App\Services\PaymentService::class);
        $availablePaymentMethods = $paymentService->getAvailablePaymentMethods($customerType);
        
        // Available order types based on customer type
        $availableOrderTypes = $this->getAvailableOrderTypes($customerType, $userType);

        // Pass context to the wrapper view
        return view('checkout.index', [
            'cartTotal' => $cartTotals['total'],
            'itemCount' => count($cartTotals['items']),
            'customerType' => $customerType,
            'userType' => $userType,
            'availablePaymentMethods' => $availablePaymentMethods,
            'availableOrderTypes' => $availableOrderTypes,
            'isLoggedIn' => Auth::check(),
            'userName' => Auth::check() ? Auth::user()->name : null
        ]);
    }

    /**
     * Display order confirmation/success page
     */
    public function success(OrderGroup $orderGroup)
{
    // Security: Only show order to the person who created it
    if (Auth::check()) {
        if ($orderGroup->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }
    } else {
        if ($orderGroup->guest_token !== session('guest_cart_token')) {
            abort(403, 'Unauthorized access to order');
        }
    }

    // Load the order with all related data for the receipt
    $orderGroup->load([
        'orders.items.product',  // Load order items with product details
        'orders.stall',          // Load stall information if needed
        'user'                   // Load user if authenticated
    ]);

    // Use the new receipt-style success page
    return view('payment.success', compact('orderGroup'));
}

    

    /**
     * Track order status
     */
    public function track(OrderGroup $orderGroup)
    {
        // Same security check as success method
        if (Auth::check()) {
            if ($orderGroup->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to order');
            }
        } else {
            if ($orderGroup->guest_token !== session('guest_cart_token')) {
                abort(403, 'Unauthorized access to order');
            }
        }

        $orderGroup->load(['orders.vendor']);

        return view('checkout.track', compact('orderGroup'));
    }

    /**
     * Get available payment methods based on customer type
     */
    private function getAvailablePaymentMethods(string $customerType): array
    {
        if ($customerType === 'logged_in') {
            return [
                'online' => [
                    'label' => 'Online Payment',
                    'description' => 'Pay securely with your credit/debit card',
                    'icon' => 'credit-card'
                ],
                'onsite' => [
                    'label' => 'Pay on Pickup',
                    'description' => 'Pay when you collect your order',
                    'icon' => 'cash'
                ]
            ];
        }

        // Guests can only pay online
        return [
            'online' => [
                'label' => 'Online Payment',
                'description' => 'Pay securely with your credit/debit card',
                'icon' => 'credit-card'
            ]
        ];
    }

    /**
     * Get available order types based on customer type and user type
     */
    private function getAvailableOrderTypes(string $customerType, ?string $userType): array
    {
        if ($customerType === 'logged_in' && $userType === 'employee') {
            return [
                'onsite' => [
                    'label' => 'Dine In',
                    'description' => 'Eat at the canteen',
                    'icon' => 'utensils'
                ],
                'online' => [
                    'label' => 'Take Out',
                    'description' => 'Take your order to go',
                    'icon' => 'shopping-bag'
                ]
            ];
        }

        // Guests and non-employees default to take out only
        return [
            'onsite' => [
                'label' => 'Take Out',
                'description' => 'Take your order to go',
                'icon' => 'shopping-bag'
            ]
        ];
    }
}