<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Order Received',
            'message' => "New order #{$this->order->order_number} for ₱{$this->order->total_amount}",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
            'customer_name' => $this->order->user ? $this->order->user->name : 'Guest Customer',
            'action_url' => route('filament.admin.resources.orders.view', ['record' => $this->order]),
        ];
    }

    public function toBroadcast(object $notifiable): array
    {
        return [
            'title' => 'New Order Received',
            'message' => "Order #{$this->order->order_number} for ₱{$this->order->total_amount}",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
        ];
    }
}