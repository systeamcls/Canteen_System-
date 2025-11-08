<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Expense;
use App\Models\WeeklyPayout;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AdminExpenseBreakdownWidget extends ChartWidget
{
    protected static ?string $heading = 'Expense Breakdown';
    protected static ?int $sort = 5;
    
    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getData(): array
    {
        $dateRange = $this->getDateRange();
        
        // Get expense categories with actual expenses
        $categories = Expense::join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->whereBetween('expenses.expense_date', $dateRange)
            ->select(
                'expense_categories.name',
                'expense_categories.color',
                DB::raw('SUM(expenses.amount) as total')
            )
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->having('total', '>', 0)
            ->get();
        
        // Add salary expenses if there are any (only paid)
        $salaryTotal = WeeklyPayout::where('status', 'paid')
            ->where(function($query) use ($dateRange) {
                $query->whereBetween('week_start', $dateRange)
                      ->orWhereBetween('week_end', $dateRange)
                      ->orWhere(function($q) use ($dateRange) {
                          $q->where('week_start', '<=', $dateRange[0])
                            ->where('week_end', '>=', $dateRange[1]);
                      });
            })
            ->sum('total_payout');
        
        $labels = [];
        $data = [];
        $colors = [];
        
        foreach ($categories as $category) {
            $labels[] = $category->name;
            $data[] = (float) $category->total;
            $colors[] = $category->color;
        }
        
        // Add salaries if there are any
        if ($salaryTotal > 0) {
            $labels[] = 'Staff Salaries';
            $data[] = (float) $salaryTotal;
            $colors[] = '#8b5cf6'; // Purple color for salaries
        }

        // If no expenses at all, show empty state
        if (empty($data)) {
            return [
                'datasets' => [
                    [
                        'label' => 'No Expenses',
                        'data' => [1],
                        'backgroundColor' => ['#e5e7eb'],
                    ],
                ],
                'labels' => ['No expenses recorded'],
            ];
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Expenses (PHP)',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 15,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => new \Filament\Support\RawJs('function(context) {
                            let label = context.label || "";
                            let value = context.parsed || 0;
                            return label + ": ₱" + value.toLocaleString("en-PH", {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }'),
                    ],
                ],
            ],
        ];
    }

    protected function getDateRange(): array
    {
        return match($this->filter) {
            'today' => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay()
            ],
            'week' => [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ],
            'month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ],
            'year' => [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear()
            ],
            default => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ],
        };
    }
}