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
        if ($order->getAttribute('user_id') !== Auth::id())  {
            return redirect()->route('menu');
        }
        
        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.order-success');
    }
}