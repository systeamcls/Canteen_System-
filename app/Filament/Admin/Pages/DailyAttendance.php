<?php

namespace App\Filament\Admin\Pages;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class DailyAttendance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.admin.pages.daily-attendance';
    protected static ?string $navigationGroup = 'Staff Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Daily Attendance';
    protected static ?string $navigationLabel = 'Quick Attendance';

    public $selectedDate;
    public $employees = [];
    public $attendanceData = [];
    public $lastRefresh = null;

    public function mount(): void
    {
        // Always force today's date - no more date persistence issues
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadAttendanceData();
    }

    public function updatedSelectedDate()
    {
        $this->loadAttendanceData();
    }

    public function checkDateAdvancement(): void
    {
        // If it's a new day since last refresh, automatically advance the date
        if ($this->lastRefresh && $this->lastRefresh->format('Y-m-d') !== now()->format('Y-m-d')) {
            $this->selectedDate = today()->format('Y-m-d');
            $this->loadAttendanceData();
        }
        
        $this->lastRefresh = now();
    }

    public function loadAttendanceData(): void
    {
        // Get all active staff members (not customer employees)
        $this->employees = User::where('is_active', true)
            ->where('is_staff', true)  // Only actual staff members
            ->orderBy('name')
            ->get();

        // Get existing attendance records for the selected date
        $existingRecords = AttendanceRecord::where('work_date', $this->selectedDate)
            ->get()
            ->keyBy('user_id');

        // Build attendance data array
        $this->attendanceData = [];
        foreach ($this->employees as $employee) {
            $record = $existingRecords->get($employee->id);
            $this->attendanceData[$employee->id] = [
                'status' => $record?->status ?? null,
                'clock_in' => $record?->clock_in ?? null,
                'clock_out' => $record?->clock_out ?? null,
                'free_meal_taken' => $record?->free_meal_taken ?? false,
                'record_id' => $record?->id ?? null,
            ];
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        DatePicker::make('selectedDate')
                            ->label('Select Date')
                            ->default(today())
                            ->maxDate(today())
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadAttendanceData()),
                    ]),
            ]);
    }

    public function markAttendance($employeeId, $status): void
    {
        $employee = User::find($employeeId);
        
        if (!$employee) {
            Notification::make()
                ->title('Error')
                ->body('Employee not found')
                ->danger()
                ->send();
            return;
        }

        // Set default times based on status
        $defaultTimes = match($status) {
            'present' => ['clock_in' => '08:00', 'clock_out' => '17:00'],
            'late' => ['clock_in' => '09:00', 'clock_out' => '17:00'],
            'half_day' => ['clock_in' => '08:00', 'clock_out' => '12:00'],
            'absent' => ['clock_in' => null, 'clock_out' => null],
            default => ['clock_in' => null, 'clock_out' => null],
        };

        // Calculate hours
        $totalHours = 0;
        if ($defaultTimes['clock_in'] && $defaultTimes['clock_out']) {
            $clockIn = Carbon::parse($this->selectedDate . ' ' . $defaultTimes['clock_in']);
            $clockOut = Carbon::parse($this->selectedDate . ' ' . $defaultTimes['clock_out']);
            $totalHours = $clockIn->diffInHours($clockOut);
        }

        // Update or create attendance record
        AttendanceRecord::updateOrCreate(
            [
                'user_id' => $employeeId,
                'work_date' => $this->selectedDate,
            ],
            [
                'status' => $status,
                'clock_in' => $defaultTimes['clock_in'],
                'clock_out' => $defaultTimes['clock_out'],
                'total_hours' => $totalHours,
                'regular_hours' => min($totalHours, 8),
                'overtime_hours' => max(0, $totalHours - 8),
                'recorded_by' => Auth::id(),
                'free_meal_taken' => $status !== 'absent' ? ($this->attendanceData[$employeeId]['free_meal_taken'] ?? false) : false,
            ]
        );

        // Force reload data and refresh UI
        $this->loadAttendanceData();
        $this->dispatch('attendance-updated');

        // Show notification
        $statusText = match($status) {
            'present' => 'Present (Full Day)',
            'late' => 'Late Arrival',
            'half_day' => 'Half Day',
            'absent' => 'Absent',
        };

        Notification::make()
            ->title('Attendance Updated')
            ->body("{$employee->name} marked as {$statusText}")
            ->success()
            ->send();
    }
    public function changeStatus($employeeId, $status): void
    {
        $employee = User::find($employeeId);
        
        if (!$employee) {
            Notification::make()
                ->title('Error')
                ->body('Employee not found')
                ->danger()
                ->send();
            return;
        }

        // Set default times based on status
        $defaultTimes = match($status) {
            'present' => ['clock_in' => '08:00', 'clock_out' => '17:00'],
            'late' => ['clock_in' => '09:00', 'clock_out' => '17:00'],
            'half_day' => ['clock_in' => '08:00', 'clock_out' => '12:00'],
            'absent' => ['clock_in' => null, 'clock_out' => null],
            default => ['clock_in' => null, 'clock_out' => null],
        };

        // Calculate hours
        $totalHours = 0;
        if ($defaultTimes['clock_in'] && $defaultTimes['clock_out']) {
            $clockIn = Carbon::parse($this->selectedDate . ' ' . $defaultTimes['clock_in']);
            $clockOut = Carbon::parse($this->selectedDate . ' ' . $defaultTimes['clock_out']);
            $totalHours = $clockIn->diffInHours($clockOut);
        }

        // Update or create attendance record
        AttendanceRecord::updateOrCreate(
            [
                'user_id' => $employeeId,
                'work_date' => $this->selectedDate,
            ],
            [
                'status' => $status,
                'clock_in' => $defaultTimes['clock_in'],
                'clock_out' => $defaultTimes['clock_out'],
                'total_hours' => $totalHours,
                'regular_hours' => min($totalHours, 8),
                'overtime_hours' => max(0, $totalHours - 8),
                'recorded_by' => Auth::id(),
                'free_meal_taken' => $status !== 'absent' ? ($this->attendanceData[$employeeId]['free_meal_taken'] ?? false) : false,
            ]
        );

        // Reload data to reflect changes
        $this->loadAttendanceData();

        // Show notification
        $statusText = match($status) {
            'present' => 'Present (Full Day)',
            'late' => 'Late Arrival',
            'half_day' => 'Half Day',
            'absent' => 'Absent',
        };

        Notification::make()
            ->title('Attendance Updated')
            ->body("{$employee->name} marked as {$statusText}")
            ->success()
            ->send();
    }

    public function toggleMeal($employeeId): void
    {
        $employee = User::find($employeeId);
        $currentStatus = $this->attendanceData[$employeeId]['free_meal_taken'] ?? false;
        $newStatus = !$currentStatus;

        // Update the record if it exists
        if ($recordId = $this->attendanceData[$employeeId]['record_id'] ?? null) {
            AttendanceRecord::find($recordId)->update(['free_meal_taken' => $newStatus]);
        }

        // Update local data
        $this->attendanceData[$employeeId]['free_meal_taken'] = $newStatus;

        $mealStatus = $newStatus ? 'added' : 'removed';
        Notification::make()
            ->title('Meal Status Updated')
            ->body("Free meal {$mealStatus} for {$employee->name}")
            ->success()
            ->send();
    }

    public function bulkMarkPresent(): void
    {
        $markedCount = 0;
        foreach ($this->employees as $employee) {
            if (!$this->attendanceData[$employee->id]['status']) {
                $this->markAttendance($employee->id, 'present');
                $markedCount++;
            }
        }

        Notification::make()
            ->title('Bulk Attendance')
            ->body("{$markedCount} employees marked as present")
            ->success()
            ->send();
    }

    public function clearAllAttendance(): void
    {
        AttendanceRecord::where('work_date', $this->selectedDate)->delete();
        $this->loadAttendanceData();

        Notification::make()
            ->title('Attendance Cleared')
            ->body('All attendance records for this date have been cleared')
            ->warning()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulk_present')
                ->label('Mark All Present')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action('bulkMarkPresent')
                ->requiresConfirmation()
                ->modalDescription('This will mark all employees without attendance as present for today.'),

            Action::make('clear_all')
                ->label('Clear All')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action('clearAllAttendance')
                ->requiresConfirmation()
                ->modalDescription('This will remove all attendance records for the selected date.'),

            Action::make('view_records')
                ->label('View Records')
                ->icon('heroicon-o-table-cells')
                ->color('gray')
                ->url(route('filament.admin.resources.attendance-records.index')),
        ];
    }

    public function getTitle(): string
    {
        $date = Carbon::parse($this->selectedDate);
        return 'Daily Attendance - ' . $date->format('M j, Y');
    }
}