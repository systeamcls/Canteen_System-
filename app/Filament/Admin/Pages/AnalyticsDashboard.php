<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\AnalyticsKPIWidget;
use App\Filament\Admin\Widgets\SalesTrendChart;
use App\Filament\Admin\Widgets\TopProductsChart;
use App\Filament\Admin\Widgets\RentalInsightsWidget;
use App\Filament\Admin\Widgets\CashFlowWidget;
use App\Services\AnalyticsService;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;

class AnalyticsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Analytics Dashboard';
    protected static ?string $title = 'Analytics & Insights';
    protected static string $view = 'filament.admin.pages.analytics-dashboard';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Analytics';

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticsKPIWidget::class,
        ];
    }

    protected function getWidgets(): array
    {
        return [
            SalesTrendChart::class,
            TopProductsChart::class,
            RentalInsightsWidget::class,
            CashFlowWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    $this->dispatch('refreshWidgets');
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Dashboard Refreshed')
                        ->body('All analytics data has been updated successfully.')
                        ->success()
                        ->send();
                }),

            Action::make('export_report')
                ->label('Export Report')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalWidth(MaxWidth::Medium)
                ->form([
                    \Filament\Forms\Components\Select::make('report_type')
                        ->label('Report Type')
                        ->options([
                            'daily' => 'Daily Summary',
                            'weekly' => 'Weekly Overview', 
                            'monthly' => 'Monthly Report',
                            'custom' => 'Custom Date Range',
                        ])
                        ->default('monthly')
                        ->required()
                        ->live(),
                        
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->default(now()->startOfMonth())
                        ->required()
                        ->visible(fn (\Filament\Forms\Get $get) => $get('report_type') === 'custom'),
                        
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->default(now())
                        ->required()
                        ->visible(fn (\Filament\Forms\Get $get) => $get('report_type') === 'custom'),
                ])
                ->action(function (array $data) {
                    // Generate report based on type
                    $analytics = new AnalyticsService();
                    
                    switch ($data['report_type']) {
                        case 'daily':
                            $report = $analytics->generateFinancialReport(
                                now()->startOfDay(),
                                now()
                            );
                            break;
                        case 'weekly':
                            $report = $analytics->generateFinancialReport(
                                now()->startOfWeek(),
                                now()
                            );
                            break;
                        case 'monthly':
                            $report = $analytics->generateFinancialReport(
                                now()->startOfMonth(),
                                now()
                            );
                            break;
                        case 'custom':
                            $report = $analytics->generateFinancialReport(
                                \Carbon\Carbon::parse($data['start_date']),
                                \Carbon\Carbon::parse($data['end_date'])
                            );
                            break;
                    }
                    
                    // For now, just show the data
                    // TODO: Implement PDF generation
                    \Filament\Notifications\Notification::make()
                        ->title('Report Generated')
                        ->body('Financial report data prepared. PDF export coming soon!')
                        ->info()
                        ->send();
                }),
        ];
    }

    public function getTitle(): string
    {
        return 'Analytics Dashboard - ' . now()->format('M j, Y');
    }

    public function getSubheading(): ?string
    {
        return 'Financial insights for your stall and rental properties';
    }

    // Helper methods for the view
    public function getQuickInsights(): array
    {
        $analytics = new AnalyticsService();
        
        return [
            'best_day' => $analytics->getBestPerformingDay(),
            'cash_flow' => $analytics->getCashFlowSummary(),
        ];
    }
}