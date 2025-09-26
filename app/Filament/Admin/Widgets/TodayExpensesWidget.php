<?php
// app/Filament/Widgets/TodayExpensesWidget.php
namespace App\Filament\Admin\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TodayExpensesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected function getStats(): array
    {
        $todayExpenses = Expense::today()->sum('amount');
        $yesterdayExpenses = Expense::whereDate('expense_date', Carbon::yesterday())->sum('amount');
        
        $change = $yesterdayExpenses > 0 
            ? (($todayExpenses - $yesterdayExpenses) / $yesterdayExpenses) * 100 
            : 0;
            
        $thisWeekExpenses = Expense::thisWeek()->sum('amount');
        $thisMonthExpenses = Expense::thisMonth()->sum('amount');
        
        return [
            Stat::make('Today\'s Expenses', '₱' . number_format($todayExpenses, 2))
                ->description($change >= 0 ? 'Up from yesterday' : 'Down from yesterday')
                ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($change >= 0 ? 'danger' : 'success'),
                
            Stat::make('This Week\'s Expenses', '₱' . number_format($thisWeekExpenses, 2))
                ->description('Weekly total')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
                
            Stat::make('This Month\'s Expenses', '₱' . number_format($thisMonthExpenses, 2))
                ->description('Monthly total')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}