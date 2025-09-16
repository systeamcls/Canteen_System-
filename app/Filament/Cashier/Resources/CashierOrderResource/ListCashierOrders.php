<?php
// app/Filament/Cashier/Resources/CashierOrderResource/Pages/ListCashierOrders.php

namespace App\Filament\Cashier\Resources\CashierOrderResource\Pages;

use App\Filament\Cashier\Resources\CashierOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCashierOrders extends ListRecords
{
    protected static string $resource = CashierOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('open_pos')
                ->label('🛒 Open POS')
                ->icon('heroicon-o-calculator')
                ->color('success')
                ->url('/cashier/pos')
                ->openUrlInNewTab(),
                
            Actions\Action::make('refresh')
                ->label('🔄 Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->dispatch('$refresh')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Orders')
                ->badge(fn () => static::getResource()::getEloquentQuery()->count()),
                
            'pending' => Tab::make('⏳ Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('status', 'pending')->count()),
                
            'processing' => Tab::make('🔥 Processing')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing'))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('status', 'processing')->count()),
                
            'ready' => Tab::make('✅ Ready')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ready'))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('status', 'ready')->count()),
                
            'completed' => Tab::make('🏁 Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
                
            'today' => Tab::make('📅 Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn () => static::getResource()::getEloquentQuery()->whereDate('created_at', today())->count()),
        ];
    }
}