<?php

// 📁 app/Models/User.php (UPDATED - Safe merge with your existing model)

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\User
 *
 * @mixin \Spatie\Permission\Traits\HasRoles
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        // Your existing fields
        'name',
        'email',
        'password',
        'phone',
        'type',
        'is_active',
        'admin_stall_id',

        // New fields for canteen system (will be added via migration)
        'preferred_notification_channel',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = [
        'profile_photo_url',
    ];

    /*
    |--------------------------------------------------------------------------
    | Your Existing Role Methods (preserved)
    |--------------------------------------------------------------------------
    */

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isTenant()
    {
        return $this->hasRole('tenant');
    }

    public function isCashier()
    {
        return $this->hasRole('cashier');
    }

    public function isCustomer()
    {
        return $this->hasRole('customer');
    }

    public function isStallAdmin()
    {
        return $this->hasRole('admin') && $this->admin_stall_id !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Your Existing Relationships (preserved)
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship: Stall assigned to tenant (your existing)
     */
    public function assignedStall()
    {
        return $this->hasOne(Stall::class, 'tenant_id');
    }

    public function stall(): HasOne
    {
        return $this->hasOne(Stall::class);
    }

    public function adminStall()
    {
        return $this->belongsTo(Stall::class, 'admin_stall_id');
    }

    /**
     * Relationship: Rental payments for tenant (your existing)
     */
    public function rentalPayments()
    {
        return $this->hasMany(RentalPayment::class, 'tenant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | New Relationships for Canteen System
    |--------------------------------------------------------------------------
    */

    /**
     * Cart relationship
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Order groups (customer orders)
     */
    public function orderGroups(): HasMany
    {
        return $this->hasMany(OrderGroup::class);
    }

    /**
     * Owned stall (for canteen system)
     */
    public function ownedStall(): HasOne
    {
        return $this->hasOne(Stall::class, 'owner_id');
    }

    /**
     * Vendor orders (orders to fulfill)
     */
    public function vendorOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'vendor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Your Existing Methods (preserved)
    |--------------------------------------------------------------------------
    */

    public function requiresTwoFactor(): bool
    {
        // Check if user has any admin/tenant roles and has 2FA enabled
        try {
            $hasRequiredRole = $this->hasAnyRole(['admin', 'tenant']);
            return $hasRequiredRole && !is_null($this->two_factor_secret);
        } catch (\Exception $e) {
            // Fallback if roles aren't set up yet
            return !is_null($this->two_factor_secret);
        }
    }

    public function canUseTwoFactor(): bool
    {
        try {
            return $this->hasAnyRole(['admin', 'tenant']);
        } catch (\Exception $e) {
            // Fallback if roles aren't set up yet - allow all users
            return true;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | New Methods for Canteen System
    |--------------------------------------------------------------------------
    */

    /**
     * Get notification preferences
     */
    public function prefersEmailNotifications(): bool
    {
        $channel = $this->preferred_notification_channel ?? 'email';
        return in_array($channel, ['email', 'both'], true);
    }

    public function prefersSmsNotifications(): bool
    {
        $channel = $this->preferred_notification_channel ?? 'email';
        return in_array($channel, ['sms', 'both'], true);
    }

    public function getNotificationChannels(): array
    {
        $channels = [];

        if ($this->prefersEmailNotifications() && $this->email) {
            $channels[] = 'mail';
        }

        if ($this->prefersSmsNotifications() && $this->phone) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    /**
     * Get the stall this user owns (works with multiple relationship types)
     */
    public function getOwnedStall(): ?Stall
    {
        // Try ownedStall first (new canteen system)
        $stall = $this->ownedStall;
        
        if (!$stall) {
            // Fallback to assignedStall (your existing system)
            $stall = $this->assignedStall;
        }

        if (!$stall && $this->admin_stall_id) {
            // Fallback to adminStall
            $stall = $this->adminStall;
        }

        return $stall;
    }

    /**
     * Check if user has a stall (any type)
     */
    public function hasStall(): bool
    {
        return $this->getOwnedStall() !== null;
    }

    /**
     * Get user's stall ID (works with multiple relationship types)
     */
    public function getStallId(): ?int
    {
        $stall = $this->getOwnedStall();
        return $stall?->id;
    }
}
