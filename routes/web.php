<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Menu;
use App\Http\Livewire\Cart;
use App\Http\Livewire\Checkout;
use App\Http\Livewire\OrderSuccess;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\StallController;

// Public routes
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{product}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/stalls', [StallController::class, 'index'])->name('stalls.index');
Route::get('/stalls/{stall}', [StallController::class, 'show'])->name('stalls.show');
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    

// Render Livewire components through views
Route::view('/cart', 'livewire.cart')->name('cart');
Route::view('/checkout', 'livewire.checkout')->name('checkout');
Route::view('/order/success/{order}', 'livewire.order-success')->name('order.success');

});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});