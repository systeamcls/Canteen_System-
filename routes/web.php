<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Menu;
use App\Http\Livewire\Cart;
use App\Http\Livewire\Checkout;
use App\Http\Livewire\OrderSuccess;

// Guest browsing routes - allow both guests and authenticated users
Route::middleware(['guest.or.auth'])->group(function () {
    Route::get('/menu', \App\Livewire\MenuComponent::class)->name('menu');
    Route::view('/cart', 'livewire.cart')->name('cart');
});

// Checkout routes with payment restrictions
Route::middleware(['guest.or.auth', 'payment.restriction'])->group(function () {
    Route::view('/checkout', 'livewire.checkout')->name('checkout');
});

// Order success - accessible to both guests and authenticated users
Route::middleware(['guest.or.auth'])->group(function () {
    Route::view('/order/success/{order}', 'livewire.order-success')->name('order.success');
});

// Authenticated user routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Additional authenticated routes can go here
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