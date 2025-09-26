<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ExpenseByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'This Month\'s Expenses by Category';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $categories = ExpenseCategory::withSum(['expenses' => function ($query) {
            $query->thisMonth();
        }], 'amount')
        ->where('is_active', true)
        ->get();

        $labels = $categories->pluck('name')->toArray();
        $data = $categories->pluck('expenses_sum_amount')->map(fn ($amount) => $amount ?? 0)->toArray();
        $colors = $categories->pluck('color')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Expenses (₱)',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 1,
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}