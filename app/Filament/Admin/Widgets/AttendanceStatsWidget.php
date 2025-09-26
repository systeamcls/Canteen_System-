<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AttendanceRecord;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AttendanceStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected function getStats(): array
    {
        $todayAttendance = AttendanceRecord::whereDate('work_date', today());
        $totalEmployees = User::where('is_active', true)->where('type', '!=', 'admin')->count();
        
        $presentToday = $todayAttendance->where('status', 'present')->count();
        $lateToday = $todayAttendance->where('status', 'late')->count();
        $absentToday = $totalEmployees - $todayAttendance->count();
        
        $totalHoursToday = $todayAttendance->sum('total_hours');
        $overtimeHoursToday = $todayAttendance->sum('overtime_hours');
        
        return [
            Stat::make('Present Today', $presentToday . '/' . $totalEmployees)
                ->description('Employees present')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
                
            Stat::make('Late Arrivals', $lateToday)
                ->description('Late today')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Total Hours Today', number_format($totalHoursToday, 1) . ' hrs')
                ->description('+ ' . number_format($overtimeHoursToday, 1) . ' OT hrs')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}