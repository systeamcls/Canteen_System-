<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Stall;
use App\Models\RentalPayment;
use App\Models\WeeklyPayout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnalyticsService
{
    /**
     * Convert centavos to pesos
     */
    public static function centavosToPesos($centavos): float
    {
        return round($centavos / 100, 2);
    }

    /**
     * Get admin stall ID (the stall owned/managed by admin)
     */
    private function getAdminStallId(): ?int
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return Stall::first()?->id; // Fallback if not authenticated
        }
        
        // Find stall owned by current user
        $stall = Stall::where('owner_id', $userId)->first();
        
        return $stall?->id ?? Stall::first()?->id; // Fallback to first stall
    }

    /**
     * Admin Stall Sales Overview
     */
    public function getAdminStallSales(Carbon $startDate, Carbon $endDate): array
    {
        $stallId = $this->getAdminStallId();
        
        $orders = Order::where('vendor_id', $stallId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->get();

        $totalSales = $orders->sum('amount_total');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return [
            'total_sales' => self::centavosToPesos($totalSales),
            'total_orders' => $totalOrders,
            'avg_order_value' => self::centavosToPesos($avgOrderValue),
        ];
    }

    /**
     * Rental Income Overview
     */
    public function getRentalIncome(Carbon $startDate, Carbon $endDate): array
    {
        $totalCollected = RentalPayment::whereBetween('paid_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('amount');

        $totalPending = RentalPayment::whereBetween('due_date', [$startDate, $endDate])
            ->whereIn('status', ['pending', 'partially_paid', 'overdue'])
            ->sum('amount');

        $overdueCount = RentalPayment::where('due_date', '<', now())
            ->whereIn('status', ['pending', 'partially_paid'])
            ->count();

        $overdueAmount = RentalPayment::where('due_date', '<', now())
            ->whereIn('status', ['pending', 'partially_paid'])
            ->sum('amount');

        return [
            'collected' => $totalCollected,
            'pending' => $totalPending,
            'overdue_count' => $overdueCount,
            'overdue_amount' => $overdueAmount,
            'total_expected' => $totalCollected + $totalPending,
        ];
    }

    /**
     * Expense Summary
     */
    public function getExpenseSummary(Carbon $startDate, Carbon $endDate): array
    {
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();
        
        $totalExpenses = $expenses->sum('amount');
        
        $byCategory = $expenses->groupBy('category.name')
            ->map(fn($group) => $group->sum('amount'))
            ->sortDesc()
            ->take(5);

        return [
            'total_expenses' => $totalExpenses,
            'by_category' => $byCategory->toArray(),
            'count' => $expenses->count(),
        ];
    }

    /**
     * Staff Payroll Summary
     */
    public function getPayrollSummary(Carbon $startDate, Carbon $endDate): array
    {
        $payouts = WeeklyPayout::whereBetween('paid_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->get();

        $totalPayroll = $payouts->sum('total_payout');
        $employeeCount = $payouts->unique('user_id')->count();

        return [
            'total_payroll' => $totalPayroll,
            'employee_count' => $employeeCount,
            'avg_per_employee' => $employeeCount > 0 ? $totalPayroll / $employeeCount : 0,
        ];
    }

    /**
     * Financial Summary (Profit & Loss)
     */
    public function getFinancialSummary(Carbon $startDate, Carbon $endDate): array
    {
        // Revenue
        $stallSales = $this->getAdminStallSales($startDate, $endDate);
        $rentalIncome = $this->getRentalIncome($startDate, $endDate);
        $totalRevenue = $stallSales['total_sales'] + $rentalIncome['collected'];

        // Expenses
        $expenses = $this->getExpenseSummary($startDate, $endDate);
        $payroll = $this->getPayrollSummary($startDate, $endDate);
        $totalExpenses = $expenses['total_expenses'] + $payroll['total_payroll'];

        // Net Profit/Loss
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        return [
            'revenue' => [
                'stall_sales' => $stallSales['total_sales'],
                'rental_income' => $rentalIncome['collected'],
                'total' => $totalRevenue,
            ],
            'expenses' => [
                'operational' => $expenses['total_expenses'],
                'payroll' => $payroll['total_payroll'],
                'total' => $totalExpenses,
            ],
            'net_profit' => $netProfit,
            'profit_margin' => round($profitMargin, 1),
            'is_profitable' => $netProfit > 0,
        ];
    }

    /**
     * Sales Trend for Charts (Admin Stall Only)
     */
    public function getStallSalesTrend(int $days = 30): array
    {
        $stallId = $this->getAdminStallId();
        $startDate = now()->subDays($days);
        
        $sales = Order::where('vendor_id', $stallId)
            ->where('created_at', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, SUM(amount_total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $sales->firstWhere('date', $date);
            
            $data[] = [
                'date' => now()->subDays($i)->format('M j'),
                'sales' => $dayData ? self::centavosToPesos($dayData->total) : 0,
                'orders' => $dayData ? $dayData->count : 0,
            ];
        }

        return $data;
    }

    /**
     * Top Selling Products (Admin Stall Only)
     */
    public function getTopStallProducts(int $limit = 10): array
    {
        $stallId = $this->getAdminStallId();

        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.vendor_id', $stallId)
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.line_total) as total_revenue')
            )
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'name' => $item->product_name,
                'quantity' => $item->total_quantity,
                'revenue' => self::centavosToPesos($item->total_revenue),
            ])
            ->toArray();
    }

    /**
     * Rental Payment Status Breakdown
     */
    public function getRentalPaymentStatus(): array
    {
        $thisMonth = now();

        $paid = RentalPayment::whereYear('due_date', $thisMonth->year)
            ->whereMonth('due_date', $thisMonth->month)
            ->where('status', 'paid')
            ->count();

        $pending = RentalPayment::whereYear('due_date', $thisMonth->year)
            ->whereMonth('due_date', $thisMonth->month)
            ->where('status', 'pending')
            ->count();

        $overdue = RentalPayment::where('due_date', '<', now())
            ->whereIn('status', ['pending', 'partially_paid'])
            ->count();

        $total = $paid + $pending + $overdue;
        $complianceRate = $total > 0 ? ($paid / $total) * 100 : 0;

        return [
            'paid' => $paid,
            'pending' => $pending,
            'overdue' => $overdue,
            'total' => $total,
            'compliance_rate' => round($complianceRate, 1),
        ];
    }

    /**
     * Monthly Comparison (Current vs Previous Month)
     */
    public function getMonthlyComparison(): array
    {
        $currentMonth = $this->getFinancialSummary(
            now()->startOfMonth(),
            now()
        );

        $previousMonth = $this->getFinancialSummary(
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        );

        $revenueGrowth = $previousMonth['revenue']['total'] > 0
            ? (($currentMonth['revenue']['total'] - $previousMonth['revenue']['total']) / $previousMonth['revenue']['total']) * 100
            : 0;

        $profitGrowth = abs($previousMonth['net_profit']) > 0
            ? (($currentMonth['net_profit'] - $previousMonth['net_profit']) / abs($previousMonth['net_profit'])) * 100
            : 0;

        return [
            'current' => $currentMonth,
            'previous' => $previousMonth,
            'revenue_growth' => round($revenueGrowth, 1),
            'profit_growth' => round($profitGrowth, 1),
        ];
    }

    /**
     * Generate Financial Report Data
     */
    public function generateFinancialReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'period' => [
                'start' => $startDate->format('M j, Y'),
                'end' => $endDate->format('M j, Y'),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'stall_performance' => $this->getAdminStallSales($startDate, $endDate),
            'rental_income' => $this->getRentalIncome($startDate, $endDate),
            'expenses' => $this->getExpenseSummary($startDate, $endDate),
            'payroll' => $this->getPayrollSummary($startDate, $endDate),
            'financial_summary' => $this->getFinancialSummary($startDate, $endDate),
            'top_products' => $this->getTopStallProducts(5),
            'rental_status' => $this->getRentalPaymentStatus(),
        ];
    }

    /**
     * Get cash flow summary (backward compatibility)
     */
    public function getCashFlowSummary(): array
    {
        $thisMonth = now();
        $financial = $this->getFinancialSummary(
            $thisMonth->startOfMonth(),
            $thisMonth
        );

        return [
            'cash_in' => $financial['revenue']['total'],
            'cash_out' => $financial['expenses']['total'],
            'net_cash_flow' => $financial['net_profit'],
            'is_positive' => $financial['is_profitable'],
        ];
    }

    /**
     * Get best performing day (backward compatibility)
     */
    public function getBestPerformingDay(): array
    {
        $stallId = $this->getAdminStallId();
        
        $bestDay = Order::where('vendor_id', $stallId)
            ->where('created_at', '>=', now()->subDays(30))
            ->where('payment_status', 'paid')
            ->selectRaw('DATE(created_at) as date, SUM(amount_total) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderByDesc('total')
            ->first();

        if (!$bestDay) {
            return [
                'date' => 'N/A',
                'sales' => 0,
                'orders' => 0,
            ];
        }

        return [
            'date' => Carbon::parse($bestDay->date)->format('M j, Y'),
            'sales' => self::centavosToPesos($bestDay->total),
            'orders' => $bestDay->orders,
        ];
    }

    // Keep old methods for backward compatibility
    public function getSalesOverview(Carbon $startDate, Carbon $endDate): array
    {
        return $this->getAdminStallSales($startDate, $endDate);
    }

    public function getSalesTrend(string $period = 'daily', int $days = 30): array
    {
        return $this->getStallSalesTrend($days);
    }

    public function getTopProducts(int $limit = 10): array
    {
        return $this->getTopStallProducts($limit);
    }
}