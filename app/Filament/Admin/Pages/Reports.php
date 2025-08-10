<?php

namespace App\Filament\Admin\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\Product;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.admin.pages.reports';

    public ?array $data = [];
    public $startDate;
    public $endDate;

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->form->fill([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Report Period')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->default(Carbon::now()->startOfMonth())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => $this->generateReport()),
                        
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->default(Carbon::now())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => $this->generateReport()),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $adminStall = Auth::user()->stall;
        
        if (!$adminStall) {
            return;
        }

        $startDate = $this->data['start_date'] ?? $this->startDate;
        $endDate = $this->data['end_date'] ?? $this->endDate;

        // Revenue data
        $revenue = $this->getRevenueData($adminStall, $startDate, $endDate);
        
        // Expense data
        $expenses = $this->getExpenseData($adminStall, $startDate, $endDate);
        
        // Product performance
        $topProducts = $this->getTopProducts($adminStall, $startDate, $endDate);
        
        // Daily trends
        $dailyTrends = $this->getDailyTrends($adminStall, $startDate, $endDate);

        $this->data = array_merge($this->data, [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $revenue['total'] - $expenses['total'],
            'top_products' => $topProducts,
            'daily_trends' => $dailyTrends,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ]);
    }

    protected function getRevenueData($stall, $startDate, $endDate): array
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereHas('items.product', function ($query) use ($stall) {
                $query->where('stall_id', $stall->id);
            })
            ->with('items.product')
            ->get();

        $total = $orders->sum(function ($order) use ($stall) {
            return $order->items->where('product.stall_id', $stall->id)->sum('subtotal');
        });

        $orderCount = $orders->count();
        $averageOrderValue = $orderCount > 0 ? $total / $orderCount : 0;

        return [
            'total' => $total,
            'order_count' => $orderCount,
            'average_order_value' => $averageOrderValue,
        ];
    }

    protected function getExpenseData($stall, $startDate, $endDate): array
    {
        $expenses = Expense::where('stall_id', $stall->id)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        $total = $expenses->sum('amount');
        $byCategory = $expenses->groupBy('category')->map(function ($group) {
            return [
                'total' => $group->sum('amount'),
                'count' => $group->count(),
            ];
        });

        return [
            'total' => $total,
            'count' => $expenses->count(),
            'by_category' => $byCategory,
        ];
    }

    protected function getTopProducts($stall, $startDate, $endDate): array
    {
        return OrderItem::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('product', function ($query) use ($stall) {
                $query->where('stall_id', $stall->id);
            })
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity_sold' => $item->total_quantity,
                    'revenue' => $item->total_revenue,
                ];
            });
    }

    protected function getDailyTrends($stall, $startDate, $endDate): array
    {
        $dates = [];
        $revenues = [];
        $expenses = [];

        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $dates[] = $current->format('M j');
            
            // Daily revenue
            $dailyRevenue = Order::whereDate('created_at', $current)
                ->where('status', 'completed')
                ->whereHas('items.product', function ($query) use ($stall) {
                    $query->where('stall_id', $stall->id);
                })
                ->with('items.product')
                ->get()
                ->sum(function ($order) use ($stall) {
                    return $order->items->where('product.stall_id', $stall->id)->sum('subtotal');
                });

            // Daily expenses
            $dailyExpenses = Expense::where('stall_id', $stall->id)
                ->whereDate('expense_date', $current)
                ->where('status', 'approved')
                ->sum('amount');

            $revenues[] = $dailyRevenue;
            $expenses[] = $dailyExpenses;

            $current->addDay();
        }

        return [
            'dates' => $dates,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }
}