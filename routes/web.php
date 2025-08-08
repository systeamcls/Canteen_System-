<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Menu;
use App\Http\Livewire\Cart;
use App\Http\Livewire\Checkout;
use App\Http\Livewire\OrderSuccess;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\StallController;


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
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/{product}', [MenuController::class, 'show'])->name('menu.show');
        Route::get('/stalls', [StallController::class, 'index'])->name('stalls.index');
        Route::get('/stalls/{stall}', [StallController::class, 'show'])->name('stalls.show');

        // Cart and checkout (available to both guests and employees)
        Route::view('/cart', 'livewire.cart')->name('cart');
        Route::view('/checkout', 'livewire.checkout')->name('checkout');
        Route::view('/order/success/{order}', 'livewire.order-success')->name('order.success');
    });
});

// Employee-only routes (require authentication)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'user.type:employee'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
// Additional employee-only routes can be added here
});

// Admin and Tenant routes (require specific roles)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Admin/Tenant specific routes will go here
});