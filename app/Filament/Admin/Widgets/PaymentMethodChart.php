<?php

namespace App\Filament\Admin\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;

class PaymentMethodChart extends ChartWidget
{
    protected static ?string $heading = 'Payment Methods - Last 30 Days';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $analytics = new AnalyticsService();
        $breakdown = $analytics->getPaymentMethodBreakdown();

        return [
            'datasets' => [
                [
                    'label' => 'Amount (₱)',
                    'data' => $breakdown['data'],
                    'backgroundColor' => $breakdown['colors'],
                    'borderWidth' => 2,
                    'borderColor' => '#fff',
                ],
            ],
            'labels' => $breakdown['labels'],
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
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) { 
                            return context.label + ': ₱' + context.raw.toLocaleString(); 
                        }",
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}