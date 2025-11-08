<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WeeklyPayout extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'total_hours',
        'regular_hours',
        'overtime_hours',
        'hourly_rate',
        'regular_pay',
        'overtime_pay',
        'bonuses',
        'total_payout',
        'status',
        'paid_date',
        'notes',
        'processed_by',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'paid_date' => 'date',
        'total_hours' => 'decimal:2',
        'regular_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'regular_pay' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'total_payout' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Auto-generate payout from attendance
    public static function generateForWeek(User $user, Carbon $weekStart): self
    {
        $weekEnd = $weekStart->copy()->endOfWeek();

        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->where('status', 'present')
            ->get();

        $daysWorked = $attendance->count();
        $dailyRate = $user->daily_rate ?? 500;
        $totalPayout = $daysWorked * $dailyRate;

        return self::updateOrCreate(
            [
                'user_id' => $user->id,
                'week_start' => $weekStart,
            ],
            [
                'week_end' => $weekEnd,
                'total_hours' => $daysWorked * 8, // 8 hours per day
                'regular_hours' => $daysWorked * 8,
                'overtime_hours' => 0,
                'hourly_rate' => $dailyRate / 8,
                'regular_pay' => $totalPayout,
                'overtime_pay' => 0,
                'bonuses' => 0,
                'total_payout' => $totalPayout,
                'status' => 'pending',
            ]
        );
    }
}