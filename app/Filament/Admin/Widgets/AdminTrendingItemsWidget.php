<?php

namespace App\Filament\Admin\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminTrendingItemsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 5 Trending Items';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        return $table
            ->query(function () use ($stallId) {
                if (!$stallId) {
                    return Product::query()->whereRaw('1 = 0'); // Return empty query
                }

                return Product::query()
                    ->where('stall_id', $stallId)
                    ->withCount(['orderItems as total_sold' => function ($query) {
                        $query->whereHas('order', function ($q) {
                            $q->where('status', 'completed')
                              ->where('created_at', '>=', now()->subDays(7));
                        });
                    }])
                    ->orderByDesc('total_sold')
                    ->limit(5);
            })
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(function () {
                        return asset('images/default-product.png');
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->weight('medium')
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('PHP')
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Sold (7d)')
                    ->badge()
                    ->color(function ($state) {
                        return match (true) {
                            $state >= 20 => 'success',
                            $state >= 10 => 'warning',
                            default => 'gray'
                        };
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->emptyStateHeading('No trending items')
            ->emptyStateDescription('No sales data available for the past 7 days.')
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->paginated(false);
    }
}