<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;



class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'days_present',
        'days_late',
        'days_half_day',
        'days_absent',
        'total_hours',
        'daily_rate',
        'gross_pay',
        'deductions',
        'net_pay',
        'status',
        'paid_date',
        'notes',
        'generated_by',
        'daily_rate',
        'is_staff'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_date' => 'date',
        'daily_rate' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'is_staff' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Generate payroll for a user for a given period
     */
    public static function generateForPeriod(User $user, $startDate, $endDate)
    {
        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->get();

        $daysPresent = $attendance->where('status', 'present')->count();
        $daysLate = $attendance->where('status', 'late')->count();
        $daysHalfDay = $attendance->where('status', 'half_day')->count();
        $daysAbsent = $attendance->where('status', 'absent')->count();
        $totalHours = $attendance->sum('total_hours');

        $dailyRate = $user->daily_rate ?? 500;

        // Calculate gross pay
        // Present and late = full day, half_day = half pay, absent = no pay
        $grossPay = ($daysPresent * $dailyRate) + 
                    ($daysLate * $dailyRate) + 
                    ($daysHalfDay * ($dailyRate / 2));

        // Deductions (can be customized)
        $deductions = 0;

        // Net pay
        $netPay = $grossPay - $deductions;

        return self::updateOrCreate(
            [
                'user_id' => $user->id,
                'period_start' => $startDate,
                'period_end' => $endDate,
            ],
            [
                'days_present' => $daysPresent,
                'days_late' => $daysLate,
                'days_half_day' => $daysHalfDay,
                'days_absent' => $daysAbsent,
                'total_hours' => $totalHours,
                'daily_rate' => $dailyRate,
                'gross_pay' => $grossPay,
                'deductions' => $deductions,
                'net_pay' => $netPay,
                'status' => 'pending',
                'generated_by' => Auth::id(),
            ]
        );
    }
}
