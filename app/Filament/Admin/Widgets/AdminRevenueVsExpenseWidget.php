<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\RentalPayment;
use App\Models\Expense;
use App\Models\WeeklyPayout;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminRevenueVsExpenseWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue vs Expenses';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                'datasets' => [
                    [
                        'label' => 'No Data Available',
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $data = match ($this->filter) {
            'week' => $this->getWeeklyComparison($stallId),
            'month' => $this->getMonthlyComparison($stallId),
            'year' => $this->getYearlyComparison($stallId),
            default => $this->getMonthlyComparison($stallId),
        };
        
        return [
            'datasets' => [
                [
                    'label' => 'Revenue (PHP)',
                    'data' => $data['revenue'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => '#22c55e',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Expenses (PHP)',
                    'data' => $data['expenses'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => '#ef4444',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
        ];
    }

    protected function getWeeklyComparison($stallId): array
    {
        $labels = [];
        $revenue = [];
        $expenses = [];
        
        $startOfWeek = Carbon::now()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $labels[] = $date->format('D, M j');
            
            // Calculate Revenue (Sales + Rental)
            $sales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');
                
            $rental = RentalPayment::whereDate('paid_date', $date)
                ->where('status', 'paid')
                ->sum('amount');
            
            $revenue[] = (float) ($sales + $rental);
            
            // Calculate Expenses (Operating + Salaries)
            $operating = Expense::whereDate('expense_date', $date)
                ->sum('amount');
                
            $salaries = WeeklyPayout::where('status', 'paid')
                ->whereDate('week_start', '<=', $date)
                ->whereDate('week_end', '>=', $date)
                ->sum('total_payout') / 7; // Average daily payout for the week
            
            $expenses[] = (float) ($operating + $salaries);
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'expenses' => $expenses,
        ];
    }

    protected function getMonthlyComparison($stallId): array
    {
        $labels = [];
        $revenue = [];
        $expenses = [];
        
        $startOfMonth = Carbon::now()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->addDays($day - 1);
            
            // Show every 3rd day label to avoid crowding
            $labels[] = ($day % 3 === 1 || $day === 1) ? $date->format('M j') : '';
            
            // Calculate Revenue
            $sales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');
                
            $rental = RentalPayment::whereDate('paid_date', $date)
                ->where('status', 'paid')
                ->sum('amount');
            
            $revenue[] = (float) ($sales + $rental);
            
            // Calculate Expenses
            $operating = Expense::whereDate('expense_date', $date)
                ->sum('amount');
                
            $salaries = WeeklyPayout::where('status', 'paid')
                ->whereDate('week_start', '<=', $date)
                ->whereDate('week_end', '>=', $date)
                ->sum('total_payout') / 7; // Average daily payout
            
            $expenses[] = (float) ($operating + $salaries);
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'expenses' => $expenses,
        ];
    }

    protected function getYearlyComparison($stallId): array
    {
        $labels = [];
        $revenue = [];
        $expenses = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(Carbon::now()->year, $month, 1);
            $labels[] = $date->format('M');
            
            // Calculate Revenue
            $sales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $month)
            ->where('status', 'completed')
            ->sum('total_amount');
                
            $rental = RentalPayment::whereYear('paid_date', $date->year)
                ->whereMonth('paid_date', $month)
                ->where('status', 'paid')
                ->sum('amount');
            
            $revenue[] = (float) ($sales + $rental);
            
            // Calculate Expenses
            $operating = Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $month)
                ->sum('amount');
                
            $salaries = WeeklyPayout::where('status', 'paid')
                ->whereYear('week_start', $date->year)
                ->whereMonth('week_start', $month)
                ->sum('total_payout');
            
            $expenses[] = (float) ($operating + $salaries);
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'expenses' => $expenses,
        ];
    }
}