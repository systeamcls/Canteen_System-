<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;

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

        static::created(function ($order) {
            // Fire event for real-time updates
            event(new OrderCreated($order));
        });

        static::updating(function ($order) {
            // Check if status is being updated
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');

                // We'll fire the event after update in the updated event
                $order->_oldStatus = $oldStatus;
            }
        });

        static::updated(function ($order) {
            // Fire event if status was updated
            if (isset($order->_oldStatus)) {
                event(new OrderStatusUpdated($order, $order->_oldStatus));
            }
        });
    }
}