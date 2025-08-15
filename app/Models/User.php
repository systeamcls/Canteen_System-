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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

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
}