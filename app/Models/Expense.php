<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'stall_id',
        'user_id',
        'category',
        'description',
        'amount',
        'expense_date',
        'receipt_image',
        'vendor_name',
        'payment_method',
        'status',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'heroicon-m-check-circle',
            'pending' => 'heroicon-m-clock',
            'rejected' => 'heroicon-m-x-circle',
            default => 'heroicon-m-question-mark-circle',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'ingredients' => 'success',
            'utilities' => 'warning',
            'equipment' => 'info',
            'rent' => 'danger',
            'marketing' => 'primary',
            'maintenance' => 'gray',
            default => 'secondary',
        };
    }
}