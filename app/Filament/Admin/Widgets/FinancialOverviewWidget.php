<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Expense;
use App\Models\RentalPayment;
use App\Models\Payroll;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 2,
        'lg' => 3,
        'xl' => 4,
        '2xl' => 6,
    ];

    public ?string $filter = 'monthly';

    protected function getStats(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                Stat::make('No Stall Assigned', 'Contact Admin')
                    ->description('You need to be assigned to a stall')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Get date range based on filter
        [$startDate, $endDate] = $this->getDateRange();

        // INCOME
        // 1. Sales Revenue (from her stall only)
        // Using amount_total (stored in centavos as INT)
        $salesRevenueCentavos = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'completed')
        ->sum('amount_total'); // This is in CENTAVOS (int)

        // Convert from centavos to pesos
        $salesRevenue = $salesRevenueCentavos / 100;

        // 2. Rental Income (from tenants) 
        // Already in pesos (decimal)
        $rentalIncome = RentalPayment::whereBetween('paid_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('amount'); // This is in PESOS (decimal)

        $totalIncome = $salesRevenue + $rentalIncome;

        // EXPENSES
        // 1. Staff Salaries (already in pesos - decimal)
        $staffSalaries = Payroll::whereBetween('period_start', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'paid'])
            ->sum('net_pay'); // This is in PESOS (decimal)

        // 2. Other Expenses (already in pesos - decimal)
        $otherExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount'); // This is in PESOS (decimal)

        $totalExpenses = $staffSalaries + $otherExpenses;

        // NET PROFIT
        $netProfit = $totalIncome - $totalExpenses;
        $profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

        return [
            // INCOME SECTION
            Stat::make('Total Income', '₱' . number_format($totalIncome, 2))
                ->description('Sales + Rental Income')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($this->getIncomeChart($stallId, $startDate, $endDate)),

            Stat::make('Sales Revenue', '₱' . number_format($salesRevenue, 2))
                ->description('From your stall')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Rental Income', '₱' . number_format($rentalIncome, 2))
                ->description('From tenant stalls')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('primary'),

            // EXPENSE SECTION
            Stat::make('Total Expenses', '₱' . number_format($totalExpenses, 2))
                ->description('Salaries + Operations')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart($this->getExpenseChart($startDate, $endDate)),

            Stat::make('Staff Salaries', '₱' . number_format($staffSalaries, 2))
                ->description('Employee payroll')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make('Operating Expenses', '₱' . number_format($otherExpenses, 2))
                ->description('Utilities, supplies, etc.')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray'),

            // PROFIT SECTION
            Stat::make('Net Profit', '₱' . number_format($netProfit, 2))
                ->description(number_format($profitMargin, 1) . '% profit margin')
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? 'success' : 'danger')
                ->chart($this->getProfitChart($stallId, $startDate, $endDate)),
        ];
    }

    protected function getDateRange(): array
    {
        return match ($this->filter) {
            'daily' => [now()->startOfDay(), now()->endOfDay()],
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            'yearly' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Today',
            'weekly' => 'This Week',
            'monthly' => 'This Month',
            'yearly' => 'This Year',
        ];
    }

    private function getIncomeChart($stallId, $startDate, $endDate): array
    {
        $days = 7;
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            // Sales in centavos, convert to pesos
            $salesCentavos = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('amount_total');

            $sales = $salesCentavos / 100;

            // Rental already in pesos
            $rental = RentalPayment::whereDate('paid_date', $date)
                ->where('status', 'paid')
                ->sum('amount');

            $totalIncome = $sales + $rental;
            $data[] = (float) $totalIncome;
        }
        
        return $data;
    }

    private function getExpenseChart($startDate, $endDate): array
    {
        $days = 7;
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            $expenses = Expense::whereDate('expense_date', $date)->sum('amount');
            
            $data[] = (float) $expenses;
        }
        
        return $data;
    }

    private function getProfitChart($stallId, $startDate, $endDate): array
    {
        $days = 7;
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            // Sales in centavos
            $salesCentavos = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('amount_total');

            $sales = $salesCentavos / 100;

            // Rental in pesos
            $rental = RentalPayment::whereDate('paid_date', $date)
                ->where('status', 'paid')
                ->sum('amount');

            // Expenses in pesos
            $expenses = Expense::whereDate('expense_date', $date)->sum('amount');

            $income = $sales + $rental;
            $profit = $income - $expenses;
            
            $data[] = (float) $profit;
        }
        
        return $data;
    }

    /**
     * Get financial data for export (PDF/Excel)
     */
    public static function getFinancialData($stallId, $startDate, $endDate): array
    {
        // Sales Revenue - convert from centavos to pesos
        $salesRevenueCentavos = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where('status', 'completed')
        ->sum('amount_total');
        
        $salesRevenue = $salesRevenueCentavos / 100;

        // Rental Income - already in pesos
        $rentalIncome = RentalPayment::whereBetween('paid_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('amount');

        $totalIncome = $salesRevenue + $rentalIncome;

        // Expenses - already in pesos
        $staffSalaries = Payroll::whereBetween('period_start', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'paid'])
            ->sum('net_pay');

        $otherExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        $totalExpenses = $staffSalaries + $otherExpenses;

        $netProfit = $totalIncome - $totalExpenses;
        $profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

        return [
            'sales_revenue' => $salesRevenue,
            'rental_income' => $rentalIncome,
            'total_income' => $totalIncome,
            'staff_salaries' => $staffSalaries,
            'operating_expenses' => $otherExpenses,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
    }
    
}
