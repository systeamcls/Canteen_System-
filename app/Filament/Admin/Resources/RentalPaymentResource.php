<?php

namespace App\Filament\Admin\Resources;

use App\Models\RentalPayment;
use App\Models\Stall;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\RentalPaymentResource\Pages;
use Filament\Notifications\Notification;

class RentalPaymentResource extends Resource
{
    protected static ?string $model = RentalPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Canteen Management';
    
    protected static ?string $navigationLabel = 'Stall Rental Payments';
    
    protected static ?string $modelLabel = 'Rental Payment';
    
    protected static ?string $pluralModelLabel = 'Rental Payments';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Select::make('stall_id')
                            ->label('Stall')
                            ->relationship('stall', 'name')
                            ->options(Stall::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $stall = Stall::find($state);
                                    if ($stall && $stall->tenant) {
                                        $set('tenant_id', $stall->tenant_id);
                                    }
                                    if ($stall && $stall->rental_fee) {
                                        $set('amount', $stall->rental_fee);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('tenant_id')
                            ->label('Tenant')
                            ->relationship('tenant', 'name')
                            ->options(User::role('tenant')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->maxValue(999999.99)
                            ->step(0.01),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Period & Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('period_start')
                            ->label('Period Start')
                            ->required()
                            ->default(now()->startOfMonth())
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    // Auto-set period end to last day of same month
                                    $periodEnd = \Carbon\Carbon::parse($state)->endOfMonth();
                                    $set('period_end', $periodEnd->toDateString());
                                    
                                    // Auto-set due date to 5th of next month
                                    $dueDate = \Carbon\Carbon::parse($state)->addMonth()->day(5);
                                    $set('due_date', $dueDate->toDateString());
                                }
                            }),

                        Forms\Components\DatePicker::make('period_end')
                            ->label('Period End')
                            ->required()
                            ->default(now()->endOfMonth()),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required()
                            ->default(now()->addMonth()->day(5)),

                        Forms\Components\DatePicker::make('paid_date')
                            ->label('Paid Date')
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'partially_paid' => 'Partially Paid',
                                'overdue' => 'Overdue',
                            ])
                            ->required()
                            ->default('pending')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === 'paid' && !request()->input('data.paid_date')) {
                                    $set('paid_date', now()->toDateString());
                                }
                            }),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Add any additional notes about this payment...')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stall.name')
                    ->label('Stall')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_period')
                    ->label('Period')
                    ->getStateUsing(fn ($record) => $record->formatted_period),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null),

                Tables\Columns\TextColumn::make('paid_date')
                    ->label('Paid Date')
                    ->date()
                    ->placeholder('Not paid')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'info' => 'partially_paid',
                        'danger' => 'overdue',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'partially_paid' => 'Partially Paid',
                        'overdue' => 'Overdue',
                    ]),

                Tables\Filters\SelectFilter::make('stall')
                    ->relationship('stall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('tenant')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Payments')
                    ->query(fn (Builder $query) => $query->overdue())
                    ->toggle(),

                Tables\Filters\Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query) => $query->whereMonth('period_start', now()->month))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'partially_paid', 'overdue']))
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Paid')
                    ->modalDescription('Are you sure you want to mark this payment as paid?')
                    ->action(function ($record) {
                        $record->markAsPaid();
                        
                        Notification::make()
                            ->title('Payment marked as paid')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('mark_as_partially_paid')
                    ->label('Partial')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'overdue']))
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Partially Paid')
                    ->modalDescription('Mark this payment as partially paid?')
                    ->action(function ($record) {
                        $record->markAsPartiallyPaid();
                        
                        Notification::make()
                            ->title('Payment marked as partially paid')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('mark_multiple_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Mark Multiple Payments as Paid')
                        ->modalDescription('Are you sure you want to mark all selected payments as paid?')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending', 'partially_paid', 'overdue'])) {
                                    $record->markAsPaid();
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title("Marked {$count} payments as paid")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('mark_multiple_partially_paid')
                        ->label('Mark as Partially Paid')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Mark Multiple Payments as Partially Paid')
                        ->modalDescription('Are you sure you want to mark all selected payments as partially paid?')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending', 'overdue'])) {
                                    $record->markAsPartiallyPaid();
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title("Marked {$count} payments as partially paid")
                                ->warning()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('due_date', 'desc')
            ->poll('60s'); // Auto-refresh every minute to update overdue status
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalPayments::route('/'),
            'create' => Pages\CreateRentalPayment::route('/create'),
            'view' => Pages\ViewRentalPayment::route('/{record}'),
            'edit' => Pages\EditRentalPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'overdue')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'overdue')->count() > 0 ? 'danger' : null;
    }
}