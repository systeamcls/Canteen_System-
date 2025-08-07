<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;


class OrderSuccess extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $userType = session('user_type');
        
        // Validate access to order based on user type
        if ($userType === 'employee') {
            // Employee must be authenticated and own the order
            if (!Auth::check() || $order->user_id !== Auth::id()) {
                return redirect()->route('menu.index');
            }
        } elseif ($userType === 'guest') {
            // Guest must have the order in their session
            $guestOrders = session('guest_orders', []);
            if (!in_array($order->id, $guestOrders)) {
                return redirect()->route('menu.index');
            }
        } else {
            // No user type set, redirect to welcome
            return redirect('/')->with('showModal', true);
        }
        
        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.order-success');
    }
}