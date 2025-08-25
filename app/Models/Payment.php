<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_group_id',
        'user_id',
        'stall_id',
        'payment_method',
        'amount',
        'status',
        'provider',
        'provider_payment_id',
        'provider_source_id',
        'receipt_url',
        'provider_response',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'provider_response' => 'array',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function orderGroup(): BelongsTo
    {
        return $this->belongsTo(OrderGroup::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stall(): BelongsTo
    {
       return $this->belongsTo(Stall::class, 'stall_id');
    }

    // Scopes for querying
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'succeeded');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeOnline($query)
    {
        return $query->whereIn('payment_method', ['gcash', 'paymaya', 'card']);
    }

    public function scopeCash($query)
    {
        return $query->where('payment_method', 'cash');
    }

    public function scopeByStall($query, $stallId)
    {
        return $query->where('stall_id', $stallId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    // Helper methods
    public function isSuccessful(): bool
    {
        return $this->status === 'succeeded';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isOnlinePayment(): bool
    {
        return in_array($this->payment_method, ['gcash', 'paymaya', 'card']);
    }

    public function isCashPayment(): bool
    {
        return $this->payment_method === 'cash';
    }

    // Formatted attributes
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'gcash' => 'GCash',
            'paymaya' => 'PayMaya',
            'card' => 'Credit/Debit Card',
            'cash' => 'Cash on Delivery',
            default => ucfirst($this->payment_method),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'succeeded' => 'Successful',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'succeeded' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary',
        };
    }

    // Static methods for analytics
    public static function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = static::successful();
        
        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }
        
        return $query->sum('amount');
    }

    public static function getRevenueByMethod($startDate = null, $endDate = null)
    {
        $query = static::successful();
        
        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }
        
        return $query->selectRaw('payment_method, SUM(amount) as total')
                     ->groupBy('payment_method')
                     ->pluck('total', 'payment_method');
    }

    public static function getRevenueByStall($startDate = null, $endDate = null)
    {
        $query = static::successful()->with('stall');
        
        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }
        
        return $query->selectRaw('stall_id, SUM(amount) as total')
                     ->whereNotNull('stall_id')
                     ->groupBy('stall_id')
                     ->pluck('total', 'stall_id');
    }
}