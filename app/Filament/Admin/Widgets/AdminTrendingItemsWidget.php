<?php

namespace App\Filament\Admin\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class AdminTrendingItemsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Selling Products';
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = [
    'md' => 12,
    'lg' => 12,
    'xl' => 12,
];

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        return $table
            ->query(function () use ($stallId) {
                if (!$stallId) {
                    return Product::query()->whereRaw('1 = 0');
                }

                return Product::query()
                    ->where('stall_id', $stallId)
                    ->withCount(['orderItems as total_sold' => function ($query) {
                        $query->whereHas('order', function ($q) {
                            $q->where('status', 'completed')
                              ->where('created_at', '>=', now()->subDays(7));
                        });
                        $query->selectRaw('SUM(quantity)');
                    }])
                    ->withSum(['orderItems as revenue' => function ($query) {
                        $query->whereHas('order', function ($q) {
                            $q->where('status', 'completed')
                              ->where('created_at', '>=', now()->subDays(7));
                        });
                    }], 'subtotal')
                    ->orderByDesc('total_sold')
                    ->limit(8);
            })
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\ImageColumn::make('image')
                            ->disk('public')
                            ->circular()
                            ->size(50)
                            ->defaultImageUrl(url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iNCIgZmlsbD0iI0YzRjRGNiIvPgo8cGF0aCBkPSJNMTIgMTZIMjhWMjRIMTJWMTZaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xNiAxMkgyNFYyOEgxNlYxMloiIGZpbGw9IiM2QjdGODAiLz4KPC9zdmc+')),
                        
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('name')
                                ->weight('semibold')
                                ->color('primary')
                                ->searchable()
                                ->limit(25)
                                ->tooltip(fn($record) => $record->name),
                                
                            Tables\Columns\TextColumn::make('category.name')
                                ->badge()
                                ->size('sm')
                                ->color('gray'),
                        ])->space(1),
                        
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('price')
                                ->money('PHP')
                                ->color('success')
                                ->weight('medium'),
                                
                            Tables\Columns\TextColumn::make('total_sold')
                                ->label('Sold')
                                ->badge()
                                ->suffix(' units')
                                ->color(fn($state) => match(true) {
                                    $state >= 50 => 'success',
                                    $state >= 20 => 'warning', 
                                    $state >= 10 => 'info',
                                    default => 'gray'
                                }),
                        ])->space(1)->alignEnd(),
                    ]),
                    
                    Tables\Columns\Layout\Panel::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('revenue')
                                ->label('7-Day Revenue')
                                ->money('PHP')
                                ->color('success')
                                ->icon('heroicon-m-banknotes'),
                                
                            Tables\Columns\IconColumn::make('is_available')
                                ->label('Available')
                                ->boolean()
                                ->trueIcon('heroicon-m-check-circle')
                                ->falseIcon('heroicon-m-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),
                        ]),
                    ])->collapsible(),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 2,
                'xl' => 2,
            ])
            ->emptyStateHeading('No trending items')
            ->emptyStateDescription('No sales data available for the past 7 days.')
            ->emptyStateIcon('heroicon-o-chart-bar-square')
            ->paginated(false);
    }
}