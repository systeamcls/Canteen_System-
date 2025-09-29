<?php

namespace App\Filament\Admin\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;

class TopProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Best Selling Products';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $analytics = new AnalyticsService();
        $products = $analytics->getTopProducts(10);

        $colors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(251, 146, 60, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(14, 165, 233, 0.8)',
            'rgba(34, 197, 94, 0.8)',
            'rgba(249, 115, 22, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(244, 63, 94, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₱)',
                    'data' => collect($products)->pluck('revenue')->toArray(),
                    'backgroundColor' => $colors,
                    'borderColor' => array_map(fn($color) => str_replace('0.8', '1', $color), $colors),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => collect($products)->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
            'indexAxis' => 'y',
        ];
    }
}