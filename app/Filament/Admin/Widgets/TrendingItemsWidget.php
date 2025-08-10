<?php

namespace App\Filament\Admin\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class TrendingItemsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Trending Items (Last 7 Days)';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $adminStall = Auth::user()->stall;
                
                if (!$adminStall) {
                    return Product::query()->whereRaw('1 = 0'); // Return empty query
                }

                // Get trending products for admin's stall only
                return Product::query()
                    ->where('stall_id', $adminStall->id)
                    ->withCount(['orderItems as total_orders' => function (Builder $query) {
                        $query->whereHas('order', function (Builder $subQuery) {
                            $subQuery->where('status', 'completed')
                                ->where('created_at', '>=', Carbon::now()->subDays(7));
                        });
                    }])
                    ->withSum(['orderItems as total_revenue' => function (Builder $query) {
                        $query->whereHas('order', function (Builder $subQuery) {
                            $subQuery->where('status', 'completed')
                                ->where('created_at', '>=', Carbon::now()->subDays(7));
                        });
                    }], 'subtotal')
                    ->withSum(['orderItems as total_quantity' => function (Builder $query) {
                        $query->whereHas('order', function (Builder $subQuery) {
                            $subQuery->where('status', 'completed')
                                ->where('created_at', '>=', Carbon::now()->subDays(7));
                        });
                    }], 'quantity')
                    ->having('total_orders', '>', 0)
                    ->orderByDesc('total_quantity');
            })
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('/images/placeholder-product.png'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->weight('semibold')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Quantity Sold')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('total_orders')
                    ->label('Orders')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->money('PHP')
                    ->sortable()
                    ->alignEnd()
                    ->weight('semibold'),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Unit Price')
                    ->money('PHP')
                    ->sortable()
                    ->alignEnd(),
                
                Tables\Columns\TextColumn::make('change_percentage')
                    ->label('Trend')
                    ->getStateUsing(function (Product $record): string {
                        // Calculate percentage change compared to previous 7 days
                        $adminStall = Auth::user()->stall;
                        
                        $currentPeriodQuantity = OrderItem::whereHas('product', function (Builder $query) use ($record) {
                                $query->where('id', $record->id);
                            })
                            ->whereHas('order', function (Builder $query) {
                                $query->where('status', 'completed')
                                    ->whereBetween('created_at', [
                                        Carbon::now()->subDays(7),
                                        Carbon::now()
                                    ]);
                            })
                            ->sum('quantity');

                        $previousPeriodQuantity = OrderItem::whereHas('product', function (Builder $query) use ($record) {
                                $query->where('id', $record->id);
                            })
                            ->whereHas('order', function (Builder $query) {
                                $query->where('status', 'completed')
                                    ->whereBetween('created_at', [
                                        Carbon::now()->subDays(14),
                                        Carbon::now()->subDays(7)
                                    ]);
                            })
                            ->sum('quantity');

                        if ($previousPeriodQuantity == 0) {
                            return $currentPeriodQuantity > 0 ? '+100%' : '0%';
                        }

                        $percentage = (($currentPeriodQuantity - $previousPeriodQuantity) / $previousPeriodQuantity) * 100;
                        $sign = $percentage >= 0 ? '+' : '';
                        
                        return $sign . number_format($percentage, 1) . '%';
                    })
                    ->badge()
                    ->color(fn (string $state): string => str_starts_with($state, '+') ? 'success' : 
                        (str_starts_with($state, '-') ? 'danger' : 'warning'))
                    ->alignCenter(),
            ])
            ->defaultSort('total_quantity', 'desc')
            ->striped()
            ->paginated([5, 10, 25]);
    }
}