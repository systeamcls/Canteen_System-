<?php
// app/Filament/Cashier/Resources/CashierProductResource/Pages/ListCashierProducts.php

namespace App\Filament\Cashier\Resources\CashierProductResource\Pages;

use App\Filament\Cashier\Resources\CashierProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCashierProducts extends ListRecords
{
    protected static string $resource = CashierProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh_stock')
                ->label('🔄 Refresh Stock')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->dispatch('$refresh')),
                
            Actions\Action::make('stock_report')
                ->label('📊 Stock Report')
                ->icon('heroicon-o-document-chart-bar')
                ->color('info')
                ->url('/cashier/reports/stock'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Products')
                ->badge(fn () => static::getResource()::getEloquentQuery()->count()),
                
            'available' => Tab::make('✅ Available')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_available', true))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('is_available', true)->count()),
                
            'out_of_stock' => Tab::make('❌ Out of Stock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_available', false))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('is_available', false)->count()),
                
            'popular' => Tab::make('🔥 Popular Today')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('orderItems', function ($q) {
                        $q->whereHas('order', function ($orderQuery) {
                            $orderQuery->whereDate('created_at', today())
                                      ->where('status', '!=', 'cancelled');
                        });
                    }, '>=', 3);
                })
                ->badge(function () {
                    return static::getResource()::getEloquentQuery()
                        ->whereHas('orderItems', function ($q) {
                            $q->whereHas('order', function ($orderQuery) {
                                $orderQuery->whereDate('created_at', today())
                                          ->where('status', '!=', 'cancelled');
                            });
                        }, '>=', 3)
                        ->count();
                }),
        ];
    }
}