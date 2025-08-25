<?php

// 📁 app/Models/StallReport.php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class StallReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'stall_id',
        'report_date',
        'gross_sales',
        'commission_rate',
        'commission_amount',
        'net_sales',
        'is_paid',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'stall_id' => 'integer',
        'report_date' => 'datetime',
        'gross_sales' => 'integer',
        'commission_rate' => 'float',
        'commission_amount' => 'integer',
        'net_sales' => 'integer',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }

    // Domain Methods
    public function calculateCommission(float $rate): void
    {
    $this->commission_rate = round($rate, 2); // ✅ float, matches decimal:2
        $this->commission_amount = (int) round($this->gross_sales * ($rate / 100));
        $this->net_sales = $this->gross_sales - $this->commission_amount;
        $this->save();
    }

    public function markAsPaid(?string $paymentReference = null): void
    {
        $this->update([
            'is_paid' => true,
            'paid_at' => now(),
            'payment_reference' => $paymentReference,
        ]);
    }

    public function markAsUnpaid(): void
    {
        $this->update([
            'is_paid' => false,
            'paid_at' => null,
            'payment_reference' => null,
        ]);
    }

    public function getFormattedGrossSales(): string
    {
        return '₱' . number_format($this->gross_sales / 100, 2);
    }

    public function getFormattedCommissionAmount(): string
    {
        return '₱' . number_format($this->commission_amount / 100, 2);
    }

    public function getFormattedNetSales(): string
    {
        return '₱' . number_format($this->net_sales / 100, 2);
    }

    public function isOverdue(): bool
    {
        if ($this->is_paid) {
            return false;
        }

        // Consider reports older than 30 days as overdue
        return $this->report_date->addDays(30)->isPast();
    }

    public static function generateForDate(Stall $stall, Carbon $date): self
    {
        return static::firstOrCreate(
            [
                'stall_id' => $stall->id,
                'report_date' => $date->toDateString(),
            ],
            [
                'gross_sales' => 0,
                'commission_rate' => $stall->commission_rate,
                'commission_amount' => 0,
                'net_sales' => 0,
                'is_paid' => false,
            ]
        );
    }
}