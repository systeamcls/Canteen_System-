<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\RentalPaymentResource\Pages;
use App\Models\RentalPayment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth; 

class RentalPaymentResource extends Resource
{
    protected static ?string $model = RentalPayment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = null; 
    protected static ?string $navigationLabel = 'Rental Payments';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->disabled()
                            ->prefix('₱'),
                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->disabled(),
                        Forms\Components\DatePicker::make('paid_date')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_reference')
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('payment_reference')
                    ->searchable(),
            ])
            ->defaultSort('due_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
    return parent::getEloquentQuery()
        ->whereHas('stall', function ($query) {
            $query->where('user_id', Auth::id()); 
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalPayments::route('/'),
            'view' => Pages\ViewRentalPayment::route('/{record}'),
        ];
    }
}