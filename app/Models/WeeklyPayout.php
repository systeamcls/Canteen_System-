<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WeeklyPayout extends Model
{
    use HasFactory;

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
        'deductions',
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
        'deductions' => 'decimal:2',
        'total_payout' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public static function generateWeeklyPayout($userId, $weekStart)
    {
        $weekEnd = Carbon::parse($weekStart)->endOfWeek();
        
        // Get attendance records for the week
        $attendanceRecords = AttendanceRecord::forUser($userId)
            ->forWeek($weekStart)
            ->get();

        // Get current wage info
        $wage = EmployeeWage::forUser($userId)->active()->first();
        if (!$wage) {
            return null;
        }

        $totalHours = $attendanceRecords->sum('total_hours');
        $regularHours = $attendanceRecords->sum('regular_hours');
        $overtimeHours = $attendanceRecords->sum('overtime_hours');

        $regularPay = $regularHours * $wage->hourly_rate;
        $overtimePay = $overtimeHours * ($wage->hourly_rate * 1.5); // 1.5x for overtime

        return static::updateOrCreate(
            ['user_id' => $userId, 'week_start' => $weekStart],
            [
                'week_end' => $weekEnd,
                'total_hours' => $totalHours,
                'regular_hours' => $regularHours,
                'overtime_hours' => $overtimeHours,
                'hourly_rate' => $wage->hourly_rate,
                'regular_pay' => $regularPay,
                'overtime_pay' => $overtimePay,
                'total_payout' => $regularPay + $overtimePay,
            ]
        );
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}