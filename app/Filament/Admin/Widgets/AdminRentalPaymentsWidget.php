<?php

namespace App\Filament\Admin\Widgets;

use App\Models\RentalPayment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AdminRentalPaymentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Rental Payments';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                RentalPayment::query()
                    ->with(['stall', 'tenant'])
                    ->latest('paid_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('stall.name')
                    ->label('Stall')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-storefront'),
                    
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('paid_date')
                    ->label('Payment Date')
                    ->date('M d, Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'paid' => 'heroicon-o-check-circle',
                        'overdue' => 'heroicon-o-exclamation-circle',
                        'pending' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->status === 'overdue' ? 'danger' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'partially_paid' => 'Partially Paid',
                    ]),
                    
                Tables\Filters\SelectFilter::make('stall_id')
                    ->label('Stall')
                    ->relationship('stall', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('paid_date', 'desc')
            ->paginated([10, 25, 50]);
    }
}