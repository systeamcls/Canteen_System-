<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Menu;
use App\Http\Livewire\Cart;
use App\Http\Livewire\Checkout;
use App\Http\Livewire\OrderSuccess;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Render Livewire components through views
    Route::view('/menu', 'livewire.menu')->name('menu');
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