<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Contracts\Support\Htmlable;

class FinancialDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static string $view = 'filament.admin.pages.financial-dashboard';
    
    protected static ?string $navigationLabel = 'Financial Analytics';
    
    protected static ?string $title = 'Financial Analytics Dashboard';
    
    protected static ?int $navigationSort = 2;
    
    // Make this a dashboard-like page
    protected static ?string $navigationGroup = 'Reports';

    public function getTitle(): string | Htmlable
    {
        return 'Financial Analytics Dashboard';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Financial Analytics Dashboard';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadReport')
                ->label('Download Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn (): string => route('admin.financial.export-full', ['filter' => request()->get('filter', 'month')]))
                ->openUrlInNewTab(),
        ];
    }

    // PDF Export Action
    public function downloadPdfReport()
    {
        return redirect()->route('admin.financial.export-full', ['filter' => request()->get('filter', 'month')]);
    }

    // Get widgets for this page
    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\FinancialOverviewWidget::class,  // ✅ Your existing overview widget
            \App\Filament\Admin\Widgets\AdminOrderStatsWidget::class,
            \App\Filament\Admin\Widgets\AdminSalesChartWidget::class,
            \App\Filament\Admin\Widgets\AdminRevenueVsExpenseWidget::class,
            \App\Filament\Admin\Widgets\AdminExpenseBreakdownWidget::class,
            \App\Filament\Admin\Widgets\AdminRentalPaymentsWidget::class,
        ];
    }

    // Get columns for widget layout
    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
            '2xl' => 2,
        ];
    }
}