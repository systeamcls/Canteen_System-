<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'payer_type',
        'user_id',
        'guest_token',
        'payment_method',
        'payment_status',
        'amount_total',
        'currency',
        'billing_contact',
        'payment_provider',
        'provider_intent_id',
        'provider_client_secret',
        'cart_snapshot',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'amount_total' => 'integer',
        'billing_contact' => 'array',
        'cart_snapshot' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Domain Methods - Updated to work with both old and new payment system
    public function isPaid(): bool
    {
        // Check new payment system first (if payments exist)
        if ($this->payments()->exists()) {
            return $this->payments()->successful()->exists();
        }
        
        // Fallback to old payment_status field
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        // Check new payment system first
        if ($this->payments()->exists()) {
            return $this->payments()->pending()->exists() && !$this->payments()->successful()->exists();
        }
        
        // Fallback to old payment_status field
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        // Check new payment system first
        if ($this->payments()->exists()) {
            return $this->payments()->failed()->exists() && !$this->payments()->successful()->exists();
        }
        
        // Fallback to old payment_status field
        return $this->payment_status === 'failed';
    }

    public function isGuest(): bool
    {
        return $this->payer_type === 'guest';
    }

    public function isOnlinePayment(): bool
    {
        return $this->payment_method === 'online';
    }

    public function isOnsitePayment(): bool
    {
        return $this->payment_method === 'onsite';
    }

    public function getCustomer(): ?User
    {
        return $this->user;
    }

    public function getCustomerName(): string
    {
        if ($this->isGuest()) {
            return $this->billing_contact['name'] ?? 'Guest';
        }

        return $this->user->name ?? 'Unknown';
    }

    public function getCustomerEmail(): ?string
    {
        if ($this->isGuest()) {
            return $this->billing_contact['email'] ?? null;
        }

        return $this->user->email ?? null;
    }

    public function getCustomerPhone(): ?string
    {
        if ($this->isGuest()) {
            return $this->billing_contact['phone'] ?? null;
        }

        return $this->user->phone ?? null;
    }

    public function getFormattedTotal(): string
    {
        return '₱' . number_format($this->amount_total / 100, 2);
    }

    public function markAsPaid(): void
    {
        $this->update(['payment_status' => 'paid']);
    }

    public function markAsFailed(): void
    {
        $this->update(['payment_status' => 'failed']);
    }

    // New payment-related methods
    public function getMainPayment(): ?Payment
    {
        return $this->payments()->whereIn('payment_method', ['gcash', 'paymaya', 'card', 'cash'])->first();
    }

    public function getTotalPaidAmount(): float
    {
        // Return amount from new payment system if available
        if ($this->payments()->exists()) {
            return $this->payments()->successful()->sum('amount');
        }
        
        // Fallback to order group total if paid via old system
        if ($this->isPaid()) {
            return $this->amount_total / 100; // Convert from centavos
        }
        
        return 0;
    }

    public function hasSuccessfulPayments(): bool
    {
        return $this->payments()->successful()->exists();
    }

    public function hasPendingPayments(): bool
    {
        return $this->payments()->pending()->exists();
    }

    public function getPaymentMethod(): ?string
    {
        $mainPayment = $this->getMainPayment();
        return $mainPayment ? $mainPayment->payment_method : $this->payment_method;
    }

    public function getPaymentProvider(): ?string
    {
        $mainPayment = $this->getMainPayment();
        return $mainPayment ? $mainPayment->provider : $this->payment_provider;
    }
}