<?php
// app/Filament/Pages/AnalyticsDashboard.php
namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\AnalyticsKPIWidget;
use App\Filament\Admin\Widgets\SalesTrendChart;
use App\Filament\Admin\Widgets\TopProductsChart;
use App\Filament\Admin\Widgets\PaymentMethodChart;
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
            PaymentMethodChart::class,
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
                    $this->notify('success', 'Dashboard data refreshed successfully!');
                }),

            Action::make('export_report')
                ->label('Export PDF Report')
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
                        ])
                        ->default('daily')
                        ->required(),
                        
                    \Filament\Forms\Components\DatePicker::make('date')
                        ->label('Report Date')
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    // We'll implement PDF generation later
                    $this->notify('info', 'PDF export feature coming soon!');
                }),
        ];
    }

    public function getTitle(): string
    {
        return 'Analytics Dashboard - ' . now()->format('M j, Y');
    }

    public function getSubheading(): ?string
    {
        return 'Real-time insights for your canteen management system';
    }

    // Add some helper methods for the view
    public function getQuickInsights(): array
    {
        $analytics = new AnalyticsService();
        
        return [
            'best_day' => $analytics->getBestPerformingDay(),
            'cash_flow' => $analytics->getCashFlowSummary(),
        ];
    }
}