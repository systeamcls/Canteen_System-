<?php

// 📁 app/Models/Order.php (UPDATED - Safe merge with your existing model)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use InvalidArgumentException;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        // Your existing fields
        'order_number',
        'order_reference',
        'user_id',
        'guest_token',
        'customer_name',
        'customer_phone',
        'customer_email',
        'status',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_type',
        'service_type',
        'special_instructions',
        'notes',
        'user_type',
        'guest_details',
        'estimated_completion',

        // New fields from Module 2 (will be added via migration)
        'order_group_id',
        'vendor_id',
        'amount_subtotal',
        'amount_total',
    ];

    protected $casts = [
        // Your existing casts
        'total_amount' => 'decimal:2',
        'guest_details' => 'array',
        'estimated_completion' => 'integer',
        
        // New casts for new fields
        'order_group_id' => 'integer',
        'vendor_id' => 'integer',
        'amount_subtotal' => 'integer', // in cents
        'amount_total' => 'integer',    // in cents
        'service_type' => 'string',

    ];
    

    /**
     * Status transition rules
     */
    private const STATUS_TRANSITIONS = [
        'placed' => ['accepted', 'cancelled'],
        'accepted' => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships (Your existing + new ones)
    |--------------------------------------------------------------------------
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // NEW: OrderGroup relationship
    public function orderGroup(): BelongsTo
    {
        return $this->belongsTo(OrderGroup::class);
    }

    // NEW: Vendor relationship
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function orderItems()
    {
    return $this->hasMany(OrderItem::class);
    }

    // NEW: Stall relationship
    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class, 'vendor_id', 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Your existing ones preserved)
    |--------------------------------------------------------------------------
    */
    public function getOrderReferenceAttribute(): string
    {
        return $this->order_number;
    }

    public function setOrderReferenceAttribute($value): void
    {
        $this->attributes['order_number'] = $value;
    }

    public function getCustomerNameAttribute(): ?string
    {
        return $this->user?->name ?? $this->guest_details['name'] ?? null;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->user?->phone ?? $this->guest_details['phone'] ?? null;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_details['email'] ?? null;
    }

    public function getNotesAttribute(): ?string
    {
        return $this->special_instructions;
    }

    public function setNotesAttribute($value): void
    {
        $this->attributes['special_instructions'] = $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Domain Methods (Your existing + enhanced)
    |--------------------------------------------------------------------------
    */
    public function transitionTo(string $newStatus): bool
    {
        $allowedTransitions = self::STATUS_TRANSITIONS[$this->status] ?? [];

        if (!in_array($newStatus, $allowedTransitions, true)) {
            throw new InvalidArgumentException(
                "Cannot transition from '{$this->status}' to '{$newStatus}'"
            );
        }

        return $this->update(['status' => $newStatus]);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::STATUS_TRANSITIONS[$this->status] ?? [], true);
    }

    public function getAllowedTransitions(): array
    {
        return self::STATUS_TRANSITIONS[$this->status] ?? [];
    }

    public function isPlaced(): bool
    {
        return $this->status === 'placed';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isPreparing(): bool
    {
        return $this->status === 'preparing';
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // ENHANCED: Handle both old and new amount fields
    public function getFormattedTotal(): string
    {
        // Prioritize new amount_total (in cents), fallback to old total_amount (decimal)
        $amount = $this->amount_total 
            ? $this->amount_total / 100 
            : (float) $this->total_amount;
            
        return '₱' . number_format($amount, 2);
    }

    // NEW: Get total in cents for consistency
    public function getTotalInCents(): int
    {
        if ($this->amount_total) {
            return $this->amount_total;
        }
        
        // Convert old decimal total_amount to cents
        return (int) round((float) $this->total_amount * 100);
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'placed' => 'warning',
            'accepted' => 'info',
            'preparing' => 'primary',
            'ready' => 'success',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    // NEW: Check if this is a multi-vendor order
    public function isPartOfGroup(): bool
    {
        return $this->order_group_id !== null;
    }

    // NEW: Get sibling orders from same order group
    public function getSiblingOrders()
    {
        if (!$this->order_group_id) {
            return collect();
        }

        return static::where('order_group_id', $this->order_group_id)
            ->where('id', '!=', $this->id)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Boot (Events) - Your existing preserved
    |--------------------------------------------------------------------------
    */
    protected static function boot()
{
    parent::boot();

    static::creating(function ($order) {
        if (empty($order->order_number)) {
            $order->order_number = 'ORD-' . strtoupper(uniqid());
        }
    });

    static::created(function ($order) {
        if (class_exists(OrderCreated::class)) {
            event(new OrderCreated($order));
        }
    });

    static::updated(function ($order) {
        if ($order->wasChanged('status') && class_exists(OrderStatusUpdated::class)) {
            $oldStatus = $order->getOriginal('status');
            event(new OrderStatusUpdated($order, $oldStatus));
        }
    });
}
}