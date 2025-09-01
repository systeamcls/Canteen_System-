<?php
// app/Filament/Tenant/Widgets/TenantTopProducts.php
namespace App\Filament\Tenant\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TenantTopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Selling Products';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        return $table
            ->query(function () use ($stall) {
                if (!$stall) {
                    return Product::query()->whereRaw('1 = 0');
                }

                return Product::query()
                    ->where('stall_id', $stall->id)
                    ->withCount(['orderItems as total_sold' => function ($query) {
                        $query->whereHas('order', function ($q) {
                            $q->where('status', 'completed')
                              ->where('created_at', '>=', now()->subDays(30));
                        });
                    }])
                    ->orderByDesc('total_sold')
                    ->limit(5);
            })
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->weight('medium')
                    ->limit(20),
                Tables\Columns\TextColumn::make('price')
                    ->getStateUsing(fn ($record) => 'PHP ' . number_format($record->price / 100, 2))
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Sold (30d)')
                    ->badge()
                    ->color('primary'),
            ])
            ->emptyStateHeading('No sales data')
            ->emptyStateDescription('Product sales will appear here once orders are completed.')
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->paginated(false);
    }
}