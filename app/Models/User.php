<?php

// 📁 app/Models/User.php (UPDATED - Safe merge with your existing model)

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\FilamentUser;



/**
 * App\Models\User
 *
 * Common Spatie Permission helper methods:
 *
 * @method bool hasRole(string|array $roles, string|null $guard = null)
 * @method bool hasAnyRole(string|array $roles, string|null $guard = null)
 * @method bool hasAllRoles(string|array $roles, string|null $guard = null)
 * @method \Spatie\Permission\Models\Role|array assignRole(...$roles)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @method \Illuminate\Database\Eloquent\Relations\HasMany orders()
 * @method \Spatie\Permission\Models\Role[] syncRoles(...$roles)
 * @method void removeRole(string|\Spatie\Permission\Contracts\Role $role)
 * @method bool hasPermissionTo(string|\Spatie\Permission\Contracts\Permission $permission, string|null $guard = null)
 * @method bool hasAnyPermission(string|array ...$permissions)
 * @method \Spatie\Permission\Models\Permission|array givePermissionTo(...$permissions)
 * @method \Spatie\Permission\Models\Permission[] syncPermissions(...$permissions)
 * @method void revokePermissionTo(string|\Spatie\Permission\Contracts\Permission $permission)
 */
class User extends Authenticatable implements MustVerifyEmail, FilamentUser
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
        'is_staff',
        'admin_stall_id',
        'verification_sent_at',

        // New fields for canteen system (will be added via migration)
        'preferred_notification_channel',
        'profile_photo_path',
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
            'verification_sent_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_staff' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    protected $appends = [
        'profile_photo_url',
    ];

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Determine if two-factor authentication has been enabled.
     */
    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get the QR code URL for two-factor authentication setup.
     */
    public function twoFactorQrCodeUrl(): ?string
    {
        if (!$this->two_factor_secret) {
            return null;
        }

        try {
            $secret = decrypt($this->two_factor_secret);
            $appName = config('app.name', 'Laravel App');
            $qrText = 'otpauth://totp/' . urlencode($appName) . ':' . urlencode($this->email) . '?secret=' . $secret . '&issuer=' . urlencode($appName);
            
            return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrText);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get formatted secret key for manual entry.
     */
    public function twoFactorFormattedSecret(): ?string
    {
        if (!$this->two_factor_secret) {
            return null;
        }

        try {
            $secret = decrypt($this->two_factor_secret);
            $chunks = str_split($secret, 4);
            return strtoupper(implode(' ', $chunks));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get recovery codes as array.
     */
    public function getTwoFactorRecoveryCodes(): array
    {
        if (!$this->two_factor_recovery_codes) {
            return [];
        }

        try {
            $decrypted = decrypt($this->two_factor_recovery_codes);
            $codes = json_decode($decrypted, true);
            
            return is_array($codes) ? array_filter($codes, 'is_string') : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if user has any recovery codes left.
     */
    public function hasRecoveryCodes(): bool
    {
        return count($this->getTwoFactorRecoveryCodes()) > 0;
    }

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

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
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
    | Your Existing Methods (preserved and updated)
    |--------------------------------------------------------------------------
    */

    public function requiresTwoFactor(): bool
    {
        // Check if user has any admin/tenant roles and has 2FA enabled
        try {
            $hasRequiredRole = $this->hasAnyRole(['admin', 'tenant']);
            return $hasRequiredRole && $this->hasEnabledTwoFactorAuthentication();
        } catch (\Exception $e) {
            // Fallback if roles aren't set up yet
            return $this->hasEnabledTwoFactorAuthentication();
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

    /**
     * Check if 2FA is required but not set up.
     */
    public function needsTwoFactorSetup(): bool
    {
        try {
            $hasRequiredRole = $this->hasAnyRole(['admin', 'tenant']);
            return $hasRequiredRole && !$this->hasEnabledTwoFactorAuthentication();
        } catch (\Exception $e) {
            return false;
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

    public function getProfilePictureUrlAttribute()
{
    return $this->profile_photo_path 
        ? Storage::url($this->profile_photo_path)
        : null;
}

public function canResendVerification()
    {
        if (!$this->verification_sent_at) {
            return true;
        }
        
        return $this->verification_sent_at->diffInSeconds(now()) >= 60; // 60 second cooldown
    }

    public function markVerificationSent()
    {
        $this->update(['verification_sent_at' => now()]);
    }

    public function sendEmailVerificationNotification()
    {
    $this->notify(new \App\Notifications\CustomEmailVerification);
    }

    public function employeeWages()
    {
    return $this->hasMany(\App\Models\EmployeeWage::class);
    }

public function canAccessPanel(\Filament\Panel $panel): bool
{
    return $this->hasRole('admin') || $this->hasRole('cashier') || $this->hasRole('tenant');
}

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

}