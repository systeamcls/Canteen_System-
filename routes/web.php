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


// Welcome page - first entry point
Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// Public routes accessible to both guests and employees
Route::middleware(['web'])->group(function () {
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

// Email verification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/email/verify-status', [EmailVerificationController::class, 'checkStatus'])
        ->name('verification.status');

    Route::get('/email/verified', [EmailVerificationController::class, 'success'])
        ->name('verification.success');
});

// Admin and Tenant routes (require specific roles)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Admin/Tenant specific routes will go here
});

// Webhook routes (outside other middleware for security)
Route::middleware(['webhook.security'])->group(function () {
    Route::post('/webhook/paymongo', [WebhookController::class, 'paymongo'])
        ->name('webhook.paymongo');
    
    // Test webhook for development
    Route::post('/webhook/test', [WebhookController::class, 'testWebhook'])
        ->name('webhook.test');
});

// Payment redirect routes (accessible to all users)
Route::get('/payment/success', function () {
    // Debug logging
    logger()->info('Payment success route accessed');
    
    // Check if view exists
    if (view()->exists('payment.success')) {
        logger()->info('View exists, rendering...');
        return view('payment.success');
    } else {
        logger()->error('View does not exist at payment.success');
        return response('Payment successful! View file missing.', 200);
    }
})->name('payment.success');

Route::get('/payment/failed', function () {
    return view('payment.failed');
})->name('payment.failed');

Route::get('/payment/cancelled', function () {
    return view('payment.cancelled');
})->name('payment.cancelled');

