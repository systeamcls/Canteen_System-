<?php

namespace App\Filament\Tenant\Resources\TenantOrderResource\Pages;

use App\Filament\Tenant\Resources\TenantOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTenantOrders extends ListRecords
{
    protected static string $resource = TenantOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->dispatch('$refresh')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Orders'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('status', 'pending')->count()),
            'processing' => Tab::make('Processing')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing'))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('status', 'processing')->count()),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn () => static::getResource()::getEloquentQuery()->whereDate('created_at', today())->count()),
        ];
    }
}