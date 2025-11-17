<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Menu;
use App\Http\Livewire\Cart;
use App\Http\Livewire\Checkout;
use App\Http\Livewire\OrderSuccess;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\StallController;
use Illuminate\Support\Facades\Auth;
use App\Filament\Admin\Pages\TwoFactorChallenge; // <-- add this
use Illuminate\Http\Request;
use App\Livewire\CheckoutForm;
use App\Http\Controllers\CheckoutController;
use App\Livewire\TestComponent;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\LegalController;
use App\Models\OrderGroup;
use Illuminate\Support\Facades\Log;

// Welcome page - first entry point
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 🔥 DISABLE /login - redirect to welcome page with modal
Route::get('/login', function () {
    session()->flash('showModal', true);
    session()->flash('loginMessage', 'Please use the login modal to access your account.');
    return redirect()->route('welcome');
})->name('login');

// 🔥 SECURITY: Override /register route - redirect to WelcomeModal
Route::get('/register', function () {
    session()->flash('showModal', true);
    return redirect()->route('welcome');
})->name('register');


// Public routes accessible to both guests and employees
Route::middleware(['web', 'redirect.admin'])->group(function () {
    // Search functionality
    Route::get('/search', [HomeController::class, 'search'])->name('search');

    // Menu browsing (requires user type selection)
    Route::middleware(['checkusertype'])->group(function () {
        // ADD THIS LINE - Home route for post-authentication
        Route::get('/home', [HomeController::class, 'index'])->name('home.index');
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/{product}', [MenuController::class, 'show'])->name('menu.show');
        Route::get('/stalls', [StallController::class, 'index'])->name('stalls.index');
        Route::get('/stalls/{stall}', [StallController::class, 'show'])->name('stalls.show');

        // 👇 ADD THIS EMPLOYEE LOGOUT ROUTE HERE
    Route::post('/employee/logout', function () {
        Auth::logout();
        session()->forget('user_type');
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect()->route('home.index')->with('success', 'Logged out successfully!');
    })->name('employee.logout');
    // 👆 END OF NEW ROUTE


        Route::get('/cart', function() {
            return redirect()->route('checkout.index');
        })->name('cart');

        Route::prefix('checkout')->name('checkout.')->middleware(['verified'])->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('/success/{orderGroup}', [CheckoutController::class, 'success'])->name('success');
        Route::get('/track/{orderGroup}', [CheckoutController::class, 'track'])->name('track');
        });

        // Convenience route (redirects to checkout.index)
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

        // Test route
        Route::get('/test-livewire', TestComponent::class);

        // Cart API routes
        Route::post('/api/add-to-cart', function (Request $request) {
            $cart = session()->get('cart', []);
            $productId = $request->product_id;
    
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity']++;
            } else {
                $cart[$productId] = [
                    'name' => $request->name,
                    'price' => $request->price,
                    'image' => $request->image,
                    'quantity' => 1
                ];
            }
    
            session()->put('cart', $cart);
            return response()->json(['success' => true]);
        });

        Route::get('/api/cart-count', function () {
            $cart = session()->get('cart', []);
            $count = array_sum(array_column($cart, 'quantity'));
            return response()->json(['count' => $count]);
        });
    });
});

// Employee-only routes (require authentication)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // optional dashboard redirect to keep legacy links working
    Route::get('/dashboard', fn() => redirect()->route('profile.show'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('user.profile.show');

    // Accept both PUT and PATCH so forms (PUT or PATCH) both work
    Route::match(['put','patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/picture', [ProfileController::class, 'uploadProfilePicture'])->name('profile.picture');
    Route::get('/profile/orders/{order}', [ProfileController::class, 'showOrder'])->name('order.detail');
    Route::match(['put','patch'], '/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::match(['put','patch'], '/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings');
    Route::post('/profile/orders/{order}/cancel', [ProfileController::class, 'cancelOrder'])->name('order.cancel');
    Route::get('/profile/orders/{order}/status', [ProfileController::class, 'checkOrderStatus'])->name('order.status');

});

// ✅ Verification link (NO auth required - user clicks from email)
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// ✅ Other routes NEED auth
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/email/verify-status', [EmailVerificationController::class, 'checkStatus'])
        ->name('verification.status');

    Route::get('/email/verified', [EmailVerificationController::class, 'success'])
        ->name('verification.success');
});

// ========================================
// 🔒 FILAMENT PANELS (Already Protected)
// ========================================
// Admin Panel: /admin (protected by Filament + FilamentTwoFactorAuth)
// Cashier Panel: /cashier (protected by Filament + CheckCashierAccess)
// Tenant Panel: /tenant (protected by Filament + EnsureTwoFactorAuthenticated)

// Additional role-protected routes (if you need any custom routes outside Filament)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:admin'])
    ->prefix('custom-admin')
    ->name('custom.admin.')
    ->group(function () {
        // Add any custom admin routes here that are NOT in Filament
        // Example: Route::get('/reports', [CustomReportController::class, 'index']);
    });

// Webhook routes (outside other middleware for security)
Route::middleware(['webhook.security'])->group(function () {
    Route::post('/webhook/paymongo', [WebhookController::class, 'paymongo'])
        ->name('webhook.paymongo');
    
    // Test webhook for development
    Route::post('/webhook/test', [WebhookController::class, 'testWebhook'])
        ->name('webhook.test');
});

// PayMongo callback handler - redirects to proper success page with order ID
Route::get('/payment/callback/success', function () {
    // Get the last order from session
    $orderGroupId = session('last_order_group_id');
    
    if (!$orderGroupId) {
        Log::warning('PayMongo callback: No order ID in session');
        return redirect()->route('home.index')->with('error', 'Order not found. Please check your order history.');
    }
    
    Log::info('PayMongo callback: Redirecting to success page', ['order_id' => $orderGroupId]);
    
    // Redirect to the proper success page with order ID
    return redirect()->route('payment.success', ['orderGroup' => $orderGroupId]);
})->name('payment.callback.success');

// Payment redirect routes (accessible to all users)
Route::get('/payment/success/{orderGroup?}', function (OrderGroup $orderGroup = null) {
    // If no order group provided, try to get from session
    if (!$orderGroup) {
        $orderGroupId = session('last_order_group_id');
        
        if (!$orderGroupId) {
            return redirect()->route('home.index')->with('error', 'Order not found. Please check your order history.');
        }
        
        $orderGroup = OrderGroup::find($orderGroupId);
        
        if (!$orderGroup) {
            return redirect()->route('home.index')->with('error', 'Order not found.');
        }
    }
    
    // Security check
    if (Auth::check()) {
        if ($orderGroup->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }
    } else {
        if ($orderGroup->guest_token !== session('guest_cart_token')) {
            abort(403, 'Unauthorized access to order');
        }
    }
    
    // Load relationships - use 'items' not 'orderItems'
    $orderGroup->load([
        'orders.items.product',
        'orders.vendor',
        'user'
    ]);
    
    return view('payment.success', compact('orderGroup'));
})->name('payment.success');

Route::get('/payment/failed', function () {
    return view('payment.failed');
})->name('payment.failed');

Route::get('/payment/cancelled', function () {
    return view('payment.cancelled');
})->name('payment.cancelled');

Route::middleware(['auth'])->prefix('admin/financial-reports')->name('admin.financial.')->group(function () {
    
    // Full Dashboard Export
    Route::get('/export-full', [FinancialReportController::class, 'exportFullDashboard'])
        ->name('export-full');
    
    // Individual Widget Exports
    Route::get('/export-sales-chart', [FinancialReportController::class, 'exportSalesChart'])
        ->name('export-sales-chart');
    
    Route::get('/export-revenue-expense', [FinancialReportController::class, 'exportRevenueExpense'])
        ->name('export-revenue-expense');
    
    Route::get('/export-expense-breakdown', [FinancialReportController::class, 'exportExpenseBreakdown'])
        ->name('export-expense-breakdown');
    
    Route::get('/export-rental-payments', [FinancialReportController::class, 'exportRentalPayments'])
        ->name('export-rental-payments');
});

Route::get('/legal/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/legal/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
