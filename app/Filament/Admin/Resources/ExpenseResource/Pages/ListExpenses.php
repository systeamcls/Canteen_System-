<?php

namespace App\Filament\Admin\Resources\ExpenseResource\Pages;

use App\Filament\Admin\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Remove the action from here - it will be in the table header instead
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Expenses')
                ->badge(Expense::count()),
                
            'today' => Tab::make('Today')
                ->badge(Expense::whereDate('expense_date', today())->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereDate('expense_date', today())
                ),
                
            'this_week' => Tab::make('This Week')
                ->badge(Expense::whereBetween('expense_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereBetween('expense_date', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])
                ),
                
            'this_month' => Tab::make('This Month')
                ->badge(Expense::whereYear('expense_date', now()->year)
                    ->whereMonth('expense_date', now()->month)
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereYear('expense_date', now()->year)
                          ->whereMonth('expense_date', now()->month)
                ),
                
            'high_amount' => Tab::make('High Amount (>₱5,000)')
                ->badge(Expense::where('amount', '>', 5000)->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('amount', '>', 5000)
                ),
        ];
    }

    // Get expense summary data for the header
    public function getExpenseSummary(): array
    {
        $today = Expense::whereDate('expense_date', today())->sum('amount');
        $thisWeek = Expense::whereBetween('expense_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->sum('amount');
        $thisMonth = Expense::whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');
        $thisYear = Expense::whereYear('expense_date', now()->year)->sum('amount');

        // Get category breakdown for visualizations
        $categoryBreakdown = ExpenseCategory::withSum(['expenses' => function ($query) {
            $query->whereYear('expense_date', now()->year)
                  ->whereMonth('expense_date', now()->month);
        }], 'amount')
        ->where('is_active', true)
        ->get()
        ->map(function ($category) use ($thisMonth) {
            $amount = $category->expenses_sum_amount ?? 0;
            $percentage = ($thisMonth > 0 && $amount > 0) ? ($amount / $thisMonth) * 100 : 0;
            $percentage = min($percentage, 100);
            
            return [
                'name' => $category->name,
                'amount' => $amount,
                'percentage' => round($percentage, 1),
                'color' => $category->color ?? '#6B7280',
            ];
        })
        ->filter(fn($cat) => $cat['amount'] > 0) // Only show categories with expenses
        ->sortByDesc('amount')
        ->take(6);

        return [
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
            'this_year' => $thisYear,
            'categories' => $categoryBreakdown,
            'monthly_avg' => $thisYear > 0 ? $thisYear / now()->month : 0,
        ];
    }

    public function getHeader(): ?View
    {
        // Always show header
        return view('filament.pages.expenses.header', [
            'summary' => $this->getExpenseSummary(),
        ]);
    }
}