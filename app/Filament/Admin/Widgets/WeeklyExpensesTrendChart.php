<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklyExpensesTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Expenses - Last 7 Days';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(6);
        $endDate = Carbon::now();

        $expenses = Expense::select(
            DB::raw('DATE(expense_date) as date'),
            DB::raw('SUM(amount) as total')
        )
        ->whereBetween('expense_date', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        $labels = [];
        $data = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateString = $date->toDateString();
            $labels[] = $date->format('M j');
            $data[] = $expenses->get($dateString)->total ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Daily Expenses (₱)',
                    'data' => $data,
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return '₱' + value.toLocaleString(); }",
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}