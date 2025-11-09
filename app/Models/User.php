<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


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
    use LogsActivity; // Add this for audit logging

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'type', // Keep for backward compatibility
        'is_active',
        'is_staff',
        'daily_rate',
        'admin_stall_id',
        'verification_sent_at',
        'preferred_notification_channel',
        'profile_photo_path',
        'profile_picture',
        'is_guest',
        'can_pay_onsite',
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
            'is_guest' => 'boolean',
            'can_pay_onsite' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    protected $appends = [
        'profile_photo_url',
    ];

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'type', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /*
    |--------------------------------------------------------------------------
    | Filament Access Control
    |--------------------------------------------------------------------------
    */

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }

        if ($panel->getId() === 'tenant') {
            return $this->hasRole('tenant') && $this->hasEnabledTwoFactorAuthentication();
        }

        if ($panel->getId() === 'cashier') {
            return $this->hasRole('cashier');
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication Methods
    |--------------------------------------------------------------------------
    */

    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

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

    public function hasRecoveryCodes(): bool
    {
        return count($this->getTwoFactorRecoveryCodes()) > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | 🔐 SECURE Role Methods - Use ONLY Spatie
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isTenant(): bool
    {
        return $this->hasRole('tenant');
    }

    public function isCashier(): bool
    {
        return $this->hasRole('cashier');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function isStallAdmin(): bool
    {
        return $this->hasRole('admin') && $this->admin_stall_id !== null;
    }

    /**
     * Get user's primary role name
     */
    public function getPrimaryRole(): ?string
    {
        return $this->roles->first()?->name;
    }

    /**
     * Get user's primary role display name
     */
    public function getPrimaryRoleLabel(): string
    {
        return match($this->getPrimaryRole()) {
            'admin' => 'Administrator',
            'tenant' => 'Stall Tenant',
            'cashier' => 'Cashier',
            'customer' => 'Customer',
            default => 'Unknown',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Auto-sync type field with Spatie role (for backward compatibility)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        // After creating a user, sync type with role
        static::created(function ($user) {
            if ($user->roles->isNotEmpty()) {
                $user->syncTypeWithRole();
            }
        });

        // After updating roles, sync type
        static::updated(function ($user) {
            if ($user->wasChanged('type') && $user->roles->isNotEmpty()) {
                $user->syncTypeWithRole();
            }
        });
    }

    /**
     * Keep type field in sync with Spatie roles (cache only)
     */
    public function syncTypeWithRole(): void
    {
        $primaryRole = $this->getPrimaryRole();
        
        if ($primaryRole && $this->type !== $primaryRole) {
            $this->updateQuietly(['type' => $primaryRole]);
        }
    }

    public function requiresTwoFactor(): bool
    {
        try {
            $hasRequiredRole = $this->hasAnyRole(['admin', 'tenant']);
            return $hasRequiredRole && $this->hasEnabledTwoFactorAuthentication();
        } catch (\Exception $e) {
            return $this->hasEnabledTwoFactorAuthentication();
        }
    }

    public function canUseTwoFactor(): bool
    {
        try {
            return $this->hasAnyRole(['admin', 'tenant']);
        } catch (\Exception $e) {
            return true;
        }
    }

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
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assignedStall(): HasOne
    {
        return $this->hasOne(Stall::class, 'tenant_id');
    }

    public function stall(): HasOne
    {
        return $this->hasOne(Stall::class);
    }

    public function adminStall(): BelongsTo
    {
        return $this->belongsTo(Stall::class, 'admin_stall_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function rentalPayments(): HasMany
    {
        return $this->hasMany(RentalPayment::class, 'tenant_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orderGroups(): HasMany
    {
        return $this->hasMany(OrderGroup::class);
    }

    public function ownedStall(): HasOne
    {
        return $this->hasOne(Stall::class, 'owner_id');
    }

    public function vendorOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'vendor_id');
    }

    public function employeeWages(): HasMany
    {
        return $this->hasMany(EmployeeWage::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }

    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return $this->profile_photo_url;
    }
}