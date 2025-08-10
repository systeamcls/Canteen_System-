<?php

namespace App\Models;

use App\Events\OrderStatusChanged;
use App\Notifications\OrderStatusNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Notification;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'guest_token',
        'status',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_type',
        'special_instructions',
        'user_type',
        'guest_details',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'guest_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . strtoupper(uniqid());
        });

        static::updated(function ($order) {
            // Check if status was changed
            if ($order->wasChanged('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;
                
                // Dispatch broadcast event
                OrderStatusChanged::dispatch($order, $oldStatus, $newStatus);
                
                // Send notification to customer if exists
                if ($order->user) {
                    $order->user->notify(new OrderStatusNotification($order, $oldStatus, $newStatus));
                }
                
                // Send notification to admin users who manage this stall
                $stallOwner = $order->items->first()?->product?->stall?->user;
                if ($stallOwner && $stallOwner->hasRole('admin')) {
                    $stallOwner->notify(new OrderStatusNotification($order, $oldStatus, $newStatus));
                }
            }
        });
    }
}