<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RentalPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'stall_id',
        'tenant_id',
        'amount',
        'period_start',
        'period_end',
        'due_date',
        'paid_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
       return [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
        'amount' => 'decimal:2',
    ];
    }

    /**
     * Relationship: Rental payment belongs to a stall
     */
    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }

    /**
     * Relationship: Rental payment belongs to a tenant (user)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Scope: Get overdue payments
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['pending', 'partially_paid']);
    }

    /**
     * Scope: Get paid payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Accessor: Get formatted period
     */
    public function getFormattedPeriodAttribute(): string
    {
        return $this->period_start->format('M j') . ' → ' . $this->period_end->format('M j, Y');
    }

    /**
     * Accessor: Check if payment is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && in_array($this->status, ['pending', 'partially_paid']);
    }

    /**
     * Method: Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now()->toDateString(),
        ]);
    }

    /**
     * Method: Mark as partially paid
     */
    public function markAsPartiallyPaid(): void
    {
        $this->update([
            'status' => 'partially_paid',
        ]);
    }

    /**
     * Boot method to auto-update overdue status
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($payment) {
            // Auto-update overdue status
            if ($payment->is_overdue && $payment->status === 'pending') {
                $payment->status = 'overdue';
            }
        });
    }
}