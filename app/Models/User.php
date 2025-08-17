<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property \Illuminate\Support\Collection $roles
 * @method bool hasRole(string $role)
 * @method bool hasAnyRole(array|string $roles)
 * @method \Illuminate\Support\Collection getRoleNames()
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
        'name',
        'email',
        'password',
        'phone',
        'type',
        'is_active',
        'admin_stall_id',
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
        ];
    }

    protected $appends = [
        'profile_photo_url',
    ];

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

    /**
     * Relationship: Stall assigned to tenant
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

    public function isStallAdmin()
    {
        return $this->hasRole('admin') && $this->admin_stall_id !== null;
    }

    /**
     * Relationship: Rental payments for tenant
     */
    public function rentalPayments()
    {
        return $this->hasMany(RentalPayment::class, 'tenant_id');
    }

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
}