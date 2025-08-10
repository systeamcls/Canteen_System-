<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'hours_worked',
        'overtime_hours',
        'status',
        'notes',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            $attendance->calculateHours();
        });
    }

    public function calculateHours(): void
    {
        if ($this->clock_in && $this->clock_out) {
            $clockIn = Carbon::parse($this->clock_in);
            $clockOut = Carbon::parse($this->clock_out);
            
            // Calculate total minutes worked
            $totalMinutes = $clockOut->diffInMinutes($clockIn);
            
            // Subtract break time if present
            if ($this->break_start && $this->break_end) {
                $breakStart = Carbon::parse($this->break_start);
                $breakEnd = Carbon::parse($this->break_end);
                $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                $totalMinutes -= $breakMinutes;
            }
            
            // Convert to hours
            $totalHours = $totalMinutes / 60;
            
            // Standard work day is 8 hours
            $standardHours = 8;
            
            if ($totalHours > $standardHours) {
                $this->hours_worked = $standardHours;
                $this->overtime_hours = $totalHours - $standardHours;
            } else {
                $this->hours_worked = $totalHours;
                $this->overtime_hours = 0;
            }

            // Update status based on hours worked
            if ($totalHours >= $standardHours) {
                $this->status = 'present';
            } elseif ($totalHours >= 4) {
                $this->status = 'half_day';
            } elseif ($totalHours > 0) {
                $this->status = 'late';
            } else {
                $this->status = 'absent';
            }
        }
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present' => 'success',
            'late' => 'warning',
            'half_day' => 'info',
            'sick', 'vacation' => 'primary',
            'absent' => 'danger',
            default => 'gray',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'present' => 'heroicon-m-check-circle',
            'late' => 'heroicon-m-clock',
            'half_day' => 'heroicon-m-minus-circle',
            'sick' => 'heroicon-m-heart',
            'vacation' => 'heroicon-m-sun',
            'absent' => 'heroicon-m-x-circle',
            default => 'heroicon-m-question-mark-circle',
        };
    }
}