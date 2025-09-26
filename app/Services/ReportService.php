<?php
// app/Services/ReportService.php
namespace App\Services;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class ReportService
{
    protected AnalyticsService $analytics;

    public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    public function generateDailyReport(Carbon $date): array
    {
        // Set the context date for analytics
        $originalDate = Carbon::now();
        Carbon::setTestNow($date);

        try {
            $kpis = $this->analytics->getTodayKPIs();
            $trend = $this->analytics->getSalesTrend(7);
            $topProducts = $this->analytics->getTopProducts(5);
            $paymentBreakdown = $this->analytics->getPaymentMethodBreakdown();
            $rentals = $this->analytics->getRentalInsights();
            $cashFlow = $this->analytics->getCashFlowSummary();

            return [
                'report_type' => 'Daily Summary',
                'date' => $date->format('M j, Y'),
                'kpis' => $kpis,
                'sales_trend' => $trend,
                'top_products' => $topProducts,
                'payment_methods' => $paymentBreakdown,
                'rental_insights' => $rentals,
                'cash_flow' => $cashFlow,
                'generated_at' => now()->format('M j, Y g:i A'),
            ];
        } finally {
            // Reset the date
            Carbon::setTestNow($originalDate);
        }
    }

    public function generateWeeklyReport(Carbon $startDate): array
    {
        $endDate = $startDate->copy()->endOfWeek();
        
        return [
            'report_type' => 'Weekly Overview',
            'period' => $startDate->format('M j') . ' - ' . $endDate->format('M j, Y'),
            'week_number' => $startDate->weekOfYear,
            // Add weekly specific analytics here
            'generated_at' => now()->format('M j, Y g:i A'),
        ];
    }

    public function generateMonthlyReport(Carbon $month): array
    {
        return [
            'report_type' => 'Monthly Report',
            'month' => $month->format('F Y'),
            // Add monthly specific analytics here
            'generated_at' => now()->format('M j, Y g:i A'),
        ];
    }

    public function generateHTMLReport(array $data): string
    {
        return View::make('reports.analytics-report', $data)->render();
    }
}

// Create the report view file
// resources/views/reports/analytics-report.blade.php