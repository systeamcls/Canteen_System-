<?php

// 📁 app/Models/Stall.php (UPDATED - Safe merge with your existing model)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stall extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Your existing fields
        'name',
        'tenant_id',
        'user_id',
        'location',
        'description',
        'rental_fee',
        'is_active',
        'logo',
        'contact_number',
        'opening_time',
        'closing_time',

        // New fields for canteen system (will be added via migration)
        'owner_id',          // Alias for tenant_id for consistency
        'commission_rate',   // Commission percentage for canteen
        'logo_url',         // URL version of logo field
    ];

    protected function casts(): array
    {
        return [
            // Your existing casts
            'rental_fee' => 'decimal:2',
            'is_active' => 'boolean',
            'opening_time' => 'datetime:H:i',
            'closing_time' => 'datetime:H:i',

            // New casts
            'tenant_id' => 'integer',
            'user_id' => 'integer',
            'owner_id' => 'integer',
            'commission_rate' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships (Your existing + new ones)
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship: Stall belongs to a tenant (your existing)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * NEW: Owner relationship (alias for tenant for canteen system)
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relationship: Stall has many products (your existing)
     */ 
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relationship: Stall has many rental payments (your existing)
     */
    public function rentalPayments(): HasMany
    {
        return $this->hasMany(RentalPayment::class);
    }

    /**
     * NEW: Orders relationship for canteen system
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'vendor_id', 'owner_id');
    }

    /**
     * NEW: Stall reports relationship
     */
    public function reports(): HasMany
    {
        return $this->hasMany(StallReport::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Your Existing Methods (preserved)
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship: Get current rental payment (your existing)
     */
    public function currentRentalPayment()
    {
        return $this->rentalPayments()
                   ->where('period_start', '<=', now())
                   ->where('period_end', '>=', now())
                   ->first();
    }

    /**
     * Relationship: Get overdue payments (your existing)
     */
    public function overduePayments(): HasMany
    {
        return $this->rentalPayments()->overdue();
    }

    /*
    |--------------------------------------------------------------------------
    | New Methods for Canteen System
    |--------------------------------------------------------------------------
    */

    /**
     * Get the stall owner (works with both tenant_id and owner_id)
     */
    public function getOwner(): ?User
    {
        return $this->owner_id ? $this->owner : $this->tenant;
    }

    /**
     * Get owner ID (works with both fields)
     */
    public function getOwnerId(): ?int
    {
        return $this->owner_id ?? $this->tenant_id;
    }

    /**
     * Scope: Active stalls
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get available products
     */
    public function getAvailableProducts()
    {
        return $this->products()->where('is_available', true);
    }

    /**
     * Toggle stall status
     */
    public function toggleStatus(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Get today's sales for canteen system
     */
    public function getTodaysSales(): int
    {
        return $this->orders()
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('amount_total') ?? 0;
    }

    /**
     * Get commission rate (fallback to default)
     */
    public function getCommissionRate(): float
    {
        return $this->commission_rate ?? 15.00; // Default 15%
    }

    /**
     * Check if stall is open now
     */
    public function isOpenNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->opening_time || !$this->closing_time) {
            return true; // Assume open if no times set
        }

        $now = now()->format('H:i');
        $opening = $this->opening_time->format('H:i');
        $closing = $this->closing_time->format('H:i');

        return $now >= $opening && $now <= $closing;
    }

    /**
     * Get logo URL (works with both logo and logo_url fields)
     */
    public function getLogoUrl(): ?string
    {
        return $this->logo_url ?? $this->logo;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors for compatibility
    |--------------------------------------------------------------------------
    */

    /**
     * Ensure owner_id syncs with tenant_id if not set
     */
    public function getOwnerIdAttribute(): ?int
    {
        return $this->attributes['owner_id'] ?? $this->tenant_id;
    }

    /**
     * Get logo URL attribute
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->attributes['logo_url'] ?? $this->logo;
    }

    public function getCurrentRentalStatus(): string
{
    if (!$this->tenant_id) {
        return 'vacant';
    }
    
    $currentPayment = $this->rentalPayments()
        ->whereMonth('period_start', now()->month)
        ->whereYear('period_start', now()->year)
        ->first();
        
    if (!$currentPayment) {
        return 'no_payment';
    }
    
    return $currentPayment->status;
}
}
