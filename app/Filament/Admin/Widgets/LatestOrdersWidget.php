<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class AdminLatestOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Latest Orders';
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 1;
    protected static ?string $pollingInterval = '10s';

    #[On('orderCreated')]
    public function refreshWidget(): void
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        return $table
            ->query(function () use ($stallId) {
                if (!$stallId) {
                    return Order::query()->whereRaw('1 = 0'); // Return empty query
                }

                return Order::query()
                    ->whereHas('items.product', function ($query) use ($stallId) {
                        $query->where('stall_id', $stallId);
                    })
                    ->latest()
                    ->limit(5);
            })
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->weight('medium')
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->default('Guest')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('PHP')
                    ->color('success')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'processing' => 'heroicon-o-cog-6-tooth',
                        'completed' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('No orders yet')
            ->emptyStateDescription('Orders will appear here when customers place them.')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->paginated(false);
    }
}