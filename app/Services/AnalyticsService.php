<?php
// app/Services/AnalyticsService.php
namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\RentalPayment;
use App\Models\Expense;
use App\Models\WeeklyPayout;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getTodayKPIs(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        // Sales (completed orders only)
        $todaySales = $this->getTodaySales();
        $yesterdaySales = $this->getYesterdaySales();
        $salesChange = $this->calculatePercentageChange($todaySales, $yesterdaySales);
        
        // Rentals
        $todayRentals = RentalPayment::whereDate('paid_date', $today)
            ->where('status', 'paid')
            ->sum('amount');
            
        // Expenses
        $todayExpenses = Expense::whereDate('expense_date', $today)
            ->sum('amount');
            
        // Payroll (if any payouts processed today)
        $todayPayroll = WeeklyPayout::whereDate('paid_date', $today)
            ->where('status', 'paid')
            ->sum('total_payout');
            
        // Net Revenue (Sales + Rentals - Expenses - Payroll)
        $netRevenue = $todaySales + $todayRentals - $todayExpenses - $todayPayroll;
        
        // Active stalls
        $activeStalls = DB::table('stalls')->where('is_active', true)->count();
        
        // Orders count
        $todayOrders = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();
            
        return [
            'sales' => [
                'amount' => $todaySales,
                'change' => $salesChange,
                'orders_count' => $todayOrders,
            ],
            'rentals' => [
                'amount' => $todayRentals,
            ],
            'expenses' => [
                'amount' => $todayExpenses,
            ],
            'payroll' => [
                'amount' => $todayPayroll,
            ],
            'net_revenue' => $netRevenue,
            'active_stalls' => $activeStalls,
        ];
    }
    
    public function getSalesTrend(int $days = 7): array
    {
        $startDate = Carbon::now()->subDays($days - 1);
        
        $sales = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount_total) as total'),
            DB::raw('COUNT(*) as orders_count')
        )
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, Carbon::now()])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        $labels = [];
        $salesData = [];
        $ordersData = [];

        for ($date = $startDate->copy(); $date <= Carbon::now(); $date->addDay()) {
            $dateString = $date->toDateString();
            $labels[] = $date->format('M j');
            $salesData[] = $sales->get($dateString)->total ?? 0;
            $ordersData[] = $sales->get($dateString)->orders_count ?? 0;
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'orders' => $ordersData,
        ];
    }
    
    public function getTopProducts(int $limit = 10): array
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.line_total) as revenue')
            )
            ->where('orders.status', 'completed')
            ->whereDate('orders.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
    
    public function getPaymentMethodBreakdown(): array
    {
        $payments = Payment::select('payment_method', DB::raw('SUM(amount) as total'))
            ->where('status', 'succeeded')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('payment_method')
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        $methodColors = [
            'cash' => '#10B981',
            'gcash' => '#3B82F6', 
            'paymaya' => '#8B5CF6',
            'card' => '#F59E0B',
        ];

        foreach ($payments as $payment) {
            $labels[] = ucfirst($payment->payment_method);
            $data[] = $payment->total;
            $colors[] = $methodColors[$payment->payment_method] ?? '#6B7280';
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }
    
    public function getRentalInsights(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now();
        
        $todayRentals = RentalPayment::whereDate('paid_date', $today)
            ->where('status', 'paid')
            ->sum('amount');
            
        $monthlyRentals = RentalPayment::whereYear('paid_date', $thisMonth->year)
            ->whereMonth('paid_date', $thisMonth->month)
            ->where('status', 'paid')
            ->sum('amount');
            
        // Compliance - paid vs pending this month
        $totalDueThisMonth = RentalPayment::whereYear('due_date', $thisMonth->year)
            ->whereMonth('due_date', $thisMonth->month)
            ->count();
            
        $paidThisMonth = RentalPayment::whereYear('due_date', $thisMonth->year)
            ->whereMonth('due_date', $thisMonth->month)
            ->where('status', 'paid')
            ->count();
            
        $complianceRate = $totalDueThisMonth > 0 ? ($paidThisMonth / $totalDueThisMonth) * 100 : 0;
        
        // Overdue payments
        $overduePayments = RentalPayment::where('due_date', '<', $today)
            ->whereIn('status', ['pending', 'partially_paid'])
            ->count();
            
        return [
            'today_collected' => $todayRentals,
            'monthly_collected' => $monthlyRentals,
            'compliance_rate' => round($complianceRate, 1),
            'overdue_count' => $overduePayments,
        ];
    }
    
    public function getCashFlowSummary(): array
    {
        $thisMonth = Carbon::now();
        
        // Income
        $salesIncome = Order::where('status', 'completed')
            ->whereYear('created_at', $thisMonth->year)
            ->whereMonth('created_at', $thisMonth->month)
            ->sum('amount_total');
            
        $rentalIncome = RentalPayment::where('status', 'paid')
            ->whereYear('paid_date', $thisMonth->year)
            ->whereMonth('paid_date', $thisMonth->month)
            ->sum('amount');
            
        $totalIncome = $salesIncome + $rentalIncome;
        
        // Expenses
        $expenses = Expense::whereYear('expense_date', $thisMonth->year)
            ->whereMonth('expense_date', $thisMonth->month)
            ->sum('amount');
            
        $payroll = WeeklyPayout::where('status', 'paid')
            ->whereYear('paid_date', $thisMonth->year)
            ->whereMonth('paid_date', $thisMonth->month)
            ->sum('total_payout');
            
        $totalExpenses = $expenses + $payroll;
        
        $netCashFlow = $totalIncome - $totalExpenses;
        
        return [
            'income' => [
                'sales' => $salesIncome,
                'rentals' => $rentalIncome,
                'total' => $totalIncome,
            ],
            'expenses' => [
                'operational' => $expenses,
                'payroll' => $payroll,
                'total' => $totalExpenses,
            ],
            'net_cash_flow' => $netCashFlow,
        ];
    }
    
    public function getBestPerformingDay(): array
    {
        $bestDay = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount_total) as total_sales'),
            DB::raw('COUNT(*) as orders_count')
        )
        ->where('status', 'completed')
        ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('date')
        ->orderBy('total_sales', 'desc')
        ->first();
        
        if (!$bestDay) {
            return [];
        }
        
        return [
            'date' => Carbon::parse($bestDay->date)->format('M j, Y'),
            'sales' => $bestDay->total_sales,
            'orders' => $bestDay->orders_count,
        ];
    }
    
    private function getTodaySales(): float
    {
        return Order::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('amount_total');
    }
    
    private function getYesterdaySales(): float
    {
        return Order::whereDate('created_at', Carbon::yesterday())
            ->where('status', 'completed')
            ->sum('amount_total');
    }
    
    private function calculatePercentageChange(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }
}