<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Expense;
use App\Models\RentalPayment;
use App\Models\WeeklyPayout;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    protected function getStallId()
    {
        return Auth::user()->admin_stall_id;
    }

    /**
     * Export Full Financial Dashboard to PDF
     */
    public function exportFullDashboard(Request $request)
    {
        $stallId = $this->getStallId();
        $filter = $request->get('filter', 'month');
        $dateRange = $this->getDateRange($filter);

        $data = [
            'title' => 'Financial Analytics Report',
            'period' => $this->getPeriodLabel($filter),
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'generated_by' => Auth::user()->name,
            
            // Stats
            'stats' => $this->getStats($dateRange),
            
            // Charts Data
            'sales_chart' => $this->getSalesChartData($filter),
            'revenue_expense' => $this->getRevenueExpenseData($filter),
            'expense_breakdown' => $this->getExpenseBreakdownData($dateRange),
            
            // Tables
            'rental_payments' => $this->getRentalPaymentsData(),
            'order_summary' => $this->getOrderSummary($dateRange),
        ];

        $pdf = Pdf::loadView('pdf.financial-dashboard', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $filename = 'financial-report-' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export Sales Chart Only
     */
    public function exportSalesChart(Request $request)
    {
        $filter = $request->get('filter', 'week');
        $data = [
            'title' => 'Sales Chart Report',
            'period' => $this->getPeriodLabel($filter),
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'chart_data' => $this->getSalesChartData($filter),
        ];

        $pdf = Pdf::loadView('pdf.sales-chart', $data)->setPaper('a4', 'landscape');
        return $pdf->download('sales-chart-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Revenue vs Expense Chart
     */
    public function exportRevenueExpense(Request $request)
    {
        $filter = $request->get('filter', 'month');
        $data = [
            'title' => 'Revenue vs Expense Report',
            'period' => $this->getPeriodLabel($filter),
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'chart_data' => $this->getRevenueExpenseData($filter),
        ];

        $pdf = Pdf::loadView('pdf.revenue-expense', $data)->setPaper('a4', 'landscape');
        return $pdf->download('revenue-expense-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Expense Breakdown
     */
    public function exportExpenseBreakdown(Request $request)
    {
        $filter = $request->get('filter', 'month');
        $dateRange = $this->getDateRange($filter);
        
        $data = [
            'title' => 'Expense Breakdown Report',
            'period' => $this->getPeriodLabel($filter),
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'breakdown' => $this->getExpenseBreakdownData($dateRange),
        ];

        $pdf = Pdf::loadView('pdf.expense-breakdown', $data)->setPaper('a4', 'portrait');
        return $pdf->download('expense-breakdown-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Rental Payments Table
     */
    public function exportRentalPayments()
    {
        $data = [
            'title' => 'Rental Payments Report',
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
            'payments' => $this->getRentalPaymentsData(),
        ];

        $pdf = Pdf::loadView('pdf.rental-payments', $data)->setPaper('a4', 'portrait');
        return $pdf->download('rental-payments-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    // ========== Helper Methods ==========

    protected function getDateRange(string $filter): array
    {
        return match($filter) {
            'today' => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    protected function getPeriodLabel(string $filter): string
    {
        return match($filter) {
            'today' => 'Today - ' . Carbon::today()->format('F d, Y'),
            'week' => 'This Week - ' . Carbon::now()->startOfWeek()->format('M d') . ' to ' . Carbon::now()->endOfWeek()->format('M d, Y'),
            'month' => Carbon::now()->format('F Y'),
            'year' => Carbon::now()->format('Y'),
            default => Carbon::now()->format('F Y'),
        };
    }

    protected function getStats(array $dateRange): array
    {
        $stallId = $this->getStallId();
        
        // Sales
        $sales = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $dateRange)
        ->where('status', 'completed')
        ->sum('total_amount');

        // Rental Income
        $rental = RentalPayment::whereBetween('paid_date', $dateRange)
            ->where('status', 'paid')
            ->sum('amount');

        // Revenue
        $revenue = $sales + $rental;

        // Expenses
        $operating = Expense::whereBetween('expense_date', $dateRange)->sum('amount');
        $salaries = WeeklyPayout::where('status', 'paid')
            ->where(function($query) use ($dateRange) {
                $query->whereBetween('week_start', $dateRange)
                      ->orWhereBetween('week_end', $dateRange);
            })
            ->sum('total_payout');
        
        $expenses = $operating + $salaries;

        // Net Profit
        $netProfit = $revenue - $expenses;

        // Orders
        $totalOrders = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $dateRange)
        ->count();

        $completedOrders = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $dateRange)
        ->where('status', 'completed')
        ->count();

        return [
            'sales' => $sales,
            'rental_income' => $rental,
            'revenue' => $revenue,
            'operating_expenses' => $operating,
            'salary_expenses' => $salaries,
            'total_expenses' => $expenses,
            'net_profit' => $netProfit,
            'profit_margin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'completion_rate' => $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0,
        ];
    }

    protected function getSalesChartData(string $filter): array
    {
        // Implementation depends on filter
        // Returns labels and data arrays
        return [
            'labels' => [],
            'data' => [],
        ];
    }

    protected function getRevenueExpenseData(string $filter): array
    {
        return [
            'labels' => [],
            'revenue' => [],
            'expenses' => [],
        ];
    }

    protected function getExpenseBreakdownData(array $dateRange): array
    {
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

        $salaryTotal = WeeklyPayout::where('status', 'paid')
            ->where(function($query) use ($dateRange) {
                $query->whereBetween('week_start', $dateRange)
                      ->orWhereBetween('week_end', $dateRange);
            })
            ->sum('total_payout');

        $breakdown = [];
        $total = 0;

        foreach ($categories as $category) {
            $breakdown[] = [
                'name' => $category->name,
                'amount' => $category->total,
                'color' => $category->color,
            ];
            $total += $category->total;
        }

        if ($salaryTotal > 0) {
            $breakdown[] = [
                'name' => 'Staff Salaries',
                'amount' => $salaryTotal,
                'color' => '#8b5cf6',
            ];
            $total += $salaryTotal;
        }

        // Calculate percentages
        foreach ($breakdown as &$item) {
            $item['percentage'] = $total > 0 ? ($item['amount'] / $total) * 100 : 0;
        }

        return $breakdown;
    }

    protected function getRentalPaymentsData(): array
    {
        return RentalPayment::with(['stall', 'tenant'])
            ->latest('paid_date')
            ->limit(20)
            ->get()
            ->map(function ($payment) {
                return [
                    'stall' => $payment->stall->name ?? 'N/A',
                    'tenant' => $payment->tenant->name ?? 'N/A',
                    'amount' => $payment->amount,
                    'payment_date' => $payment->paid_date,  // Using paid_date from DB
                    'due_date' => $payment->due_date,
                    'status' => $payment->status,
                    'payment_method' => $payment->payment_method ?? 'N/A',
                ];
            })
            ->toArray();
    }

    protected function getOrderSummary(array $dateRange): array
    {
        $stallId = $this->getStallId();
        
        return Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $dateRange)
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
            DB::raw('SUM(CASE WHEN status = "completed" THEN total_amount ELSE 0 END) as sales')
        )
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->limit(30)
        ->get()
        ->toArray();
    }
}