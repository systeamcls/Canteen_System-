<?php

// ========================================
// IMPROVED RENTALPAYMENTRESOURCE.PHP
// ========================================

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
use Carbon\Carbon;

class RentalPaymentResource extends Resource
{
    protected static ?string $model = RentalPayment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?string $navigationLabel = 'Rental Payments';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->description('Record rental payment details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('stall_id')
                                    ->label('Stall')
                                    ->relationship('stall', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $stall = Stall::find($state);
                                            if ($stall) {
                                                $set('tenant_id', $stall->tenant_id);
                                                $set('amount', $stall->rental_fee);
                                            }
                                        }
                                    })
                                    ->helperText('Select the stall for this payment'),

                                Forms\Components\Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('The tenant making this payment'),
                            ]),

                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->step(0.01)
                            ->minValue(0.01)
                            ->maxValue(999999.99)
                            ->placeholder('0.00'),
                    ]),

                Forms\Components\Section::make('Payment Period')
                    ->description('Define the rental period for this payment')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('period_start')
                                    ->label('Period Start')
                                    ->required()
                                    ->default(now()->startOfMonth())
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $date = Carbon::parse($state);
                                            $set('period_end', $date->endOfMonth()->toDateString());
                                            $set('due_date', $date->addMonth()->day(5)->toDateString());
                                        }
                                    }),

                                Forms\Components\DatePicker::make('period_end')
                                    ->label('Period End')
                                    ->required()
                                    ->default(now()->endOfMonth()),

                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Due Date')
                                    ->required()
                                    ->default(now()->addMonth()->day(5))
                                    ->helperText('When payment is due'),
                            ]),
                    ]),

                Forms\Components\Section::make('Payment Status')
                    ->description('Track payment completion and dates')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
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
                                    })
                                    ->helperText('Current payment status'),

                                Forms\Components\DatePicker::make('paid_date')
                                    ->label('Date Paid')
                                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['paid', 'partially_paid']))
                                    ->helperText('When was this payment completed?'),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('Payment Notes')
                            ->placeholder('Add any additional notes about this payment...')
                            ->maxLength(500)
                            ->rows(2)
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
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->tenant?->name),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable()
                    ->weight('semibold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('payment_period')
                    ->label('Period')
                    ->getStateUsing(function ($record) {
                        return Carbon::parse($record->period_start)->format('M Y');
                    })
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : 'gray')
                    ->icon(fn ($record) => $record->is_overdue ? 'heroicon-m-exclamation-triangle' : null),

                Tables\Columns\TextColumn::make('paid_date')
                    ->label('Paid Date')
                    ->date('M j, Y')
                    ->placeholder('Not paid')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'partially_paid' => 'info',
                        'overdue' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match($state) {
                        'pending' => 'heroicon-m-clock',
                        'paid' => 'heroicon-m-check-circle',
                        'partially_paid' => 'heroicon-m-minus-circle',
                        'overdue' => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('Days Late')
                    ->getStateUsing(function ($record) {
                        if (!$record->is_overdue) return null;
                        return $record->due_date->diffInDays(now()) . ' days';
                    })
                    ->color('danger')
                    ->visible(fn () => request('tableFilters.status.value') === 'overdue'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'partially_paid' => 'Partially Paid',
                        'overdue' => 'Overdue',
                    ])
                    ->default('pending'),

                Tables\Filters\SelectFilter::make('stall')
                    ->relationship('stall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('month')
                    ->label('Payment Month')
                    ->options([
                        '1' => 'January',
                        '2' => 'February', 
                        '3' => 'March',
                        '4' => 'April',
                        '5' => 'May',
                        '6' => 'June',
                        '7' => 'July',
                        '8' => 'August',
                        '9' => 'September',
                        '10' => 'October',
                        '11' => 'November',
                        '12' => 'December',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->whereMonth('period_start', $data['value']);
                        }
                    }),

                Tables\Filters\Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query) => $query->whereMonth('period_start', now()->month))
                    ->toggle(),

                Tables\Filters\Filter::make('urgent')
                    ->label('Urgent (Due Soon)')
                    ->query(fn (Builder $query) => 
                        $query->where('status', 'pending')
                              ->where('due_date', '<=', now()->addDays(7))
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'partially_paid', 'overdue']))
                        ->form([
                            Forms\Components\DatePicker::make('paid_date')
                                ->label('Payment Date')
                                ->default(now())
                                ->required(),
                            Forms\Components\Textarea::make('notes')
                                ->label('Payment Notes')
                                ->placeholder('Add any notes about this payment...')
                                ->rows(2),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'status' => 'paid',
                                'paid_date' => $data['paid_date'],
                                'notes' => $data['notes'] ?? $record->notes,
                            ]);
                            Notification::make()->title('Payment marked as paid')->success()->send();
                        }),

                    Tables\Actions\Action::make('send_reminder')
                        ->label('Send Reminder')
                        ->icon('heroicon-o-bell')
                        ->color('warning')
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'overdue']))
                        ->action(function ($record) {
                            // Add your reminder email/SMS logic here
                            Notification::make()->title('Reminder sent to tenant')->success()->send();
                        }),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_multiple_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\DatePicker::make('paid_date')
                                ->label('Payment Date')
                                ->default(now())
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending', 'partially_paid', 'overdue'])) {
                                    $record->update([
                                        'status' => 'paid',
                                        'paid_date' => $data['paid_date'],
                                    ]);
                                    $count++;
                                }
                            }
                            Notification::make()->title("Marked {$count} payments as paid")->success()->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No rental payments recorded')
            ->emptyStateDescription('Start tracking rental payments from your tenants.')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Record Payment')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('due_date', 'desc')
            ->poll('60s');
    }

    public static function getNavigationBadge(): ?string
    {
        $overdueCount = static::getModel()::where('status', 'overdue')->count();
        $pendingCount = static::getModel()::where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(7))
            ->count();
            
        $total = $overdueCount + $pendingCount;
        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdueCount = static::getModel()::where('status', 'overdue')->count();
        return $overdueCount > 0 ? 'danger' : 'warning';
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
}