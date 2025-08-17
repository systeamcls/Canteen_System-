<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tenant_id',
        'location',
        'description',
        'rental_fee',
        'is_active',
        'logo',
        'contact_number',
        'opening_time',
        'closing_time',
    ];

    protected function casts(): array
    {
        return [
            'rental_fee' => 'decimal:2',
            'is_active' => 'boolean',
            'opening_time' => 'datetime:H:i',
            'closing_time' => 'datetime:H:i',
        ];
    }

    /**
     * Relationship: Stall belongs to a tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Relationship: Stall has many products
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relationship: Stall has many rental payments
     */
    public function rentalPayments(): HasMany
    {
        return $this->hasMany(RentalPayment::class);
    }

    /**
     * Relationship: Get current rental payment
     */
    public function currentRentalPayment()
    {
        return $this->rentalPayments()
                   ->where('period_start', '<=', now())
                   ->where('period_end', '>=', now())
                   ->first();
    }

    /**
     * Relationship: Get overdue payments
     */
    public function overduePayments(): HasMany
    {
        return $this->rentalPayments()->overdue();
    }
}