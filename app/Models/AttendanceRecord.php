<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'total_hours',
        'regular_hours',
        'overtime_hours',
        'status',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'total_hours' => 'decimal:2',
        'regular_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function calculateHours()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return;
        }

        $clockIn = Carbon::parse($this->work_date . ' ' . $this->clock_in);
        $clockOut = Carbon::parse($this->work_date . ' ' . $this->clock_out);
        
        // Handle next day clock out
        if ($clockOut < $clockIn) {
            $clockOut->addDay();
        }

        $totalMinutes = $clockIn->diffInMinutes($clockOut);
        
        // Subtract break time if recorded
        if ($this->break_start && $this->break_end) {
            $breakStart = Carbon::parse($this->work_date . ' ' . $this->break_start);
            $breakEnd = Carbon::parse($this->work_date . ' ' . $this->break_end);
            $breakMinutes = $breakStart->diffInMinutes($breakEnd);
            $totalMinutes -= $breakMinutes;
        }

        $totalHours = round($totalMinutes / 60, 2);
        $regularHours = min($totalHours, 8); // 8 hours regular
        $overtimeHours = max(0, $totalHours - 8);

        $this->update([
            'total_hours' => $totalHours,
            'regular_hours' => $regularHours,
            'overtime_hours' => $overtimeHours,
        ]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForWeek($query, $startDate)
    {
        $endDate = Carbon::parse($startDate)->endOfWeek();
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }
}