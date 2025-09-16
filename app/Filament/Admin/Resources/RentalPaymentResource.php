<?php

// ========================================
// UPDATED RENTALPAYMENTRESOURCE.PHP - DAILY RATE SYSTEM
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
                Forms\Components\Section::make('Rental Payment Information')
                    ->description('Record daily rental payment details')
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
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            $stall = Stall::find($state);
                                            if ($stall) {
                                                $set('tenant_id', $stall->tenant_id);
                                                
                                                // Calculate rental amount based on period and daily rate
                                                $periodStart = $get('period_start');
                                                $periodEnd = $get('period_end');
                                                
                                                if ($periodStart && $periodEnd) {
                                                    $days = Carbon::parse($periodStart)->diffInDays(Carbon::parse($periodEnd)) + 1;
                                                    $totalAmount = $stall->rental_fee * $days;
                                                    $set('amount', $totalAmount);
                                                } else {
                                                    $set('amount', $stall->rental_fee);
                                                }
                                            }
                                        }
                                    })
                                    ->helperText('Select the stall for this rental payment'),

                                Forms\Components\Select::make('tenant_id')
                                    ->label('Tenant')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('The tenant making this rental payment'),
                            ]),
                    ]),

                Forms\Components\Section::make('Rental Period')
                    ->description('Define the rental period (daily rate system)')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('period_start')
                                    ->label('Period Start')
                                    ->required()
                                    ->default(now()->startOfDay())
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            $startDate = Carbon::parse($state);
                                            
                                            // Auto-set period end if not already set
                                            if (!$get('period_end')) {
                                                $set('period_end', $startDate->toDateString());
                                            }
                                            
                                            // Set due date to next day
                                            $set('due_date', $startDate->addDay()->toDateString());
                                            
                                            // Recalculate amount if stall is selected
                                            $stallId = $get('stall_id');
                                            $periodEnd = $get('period_end');
                                            if ($stallId && $periodEnd) {
                                                $stall = Stall::find($stallId);
                                                if ($stall) {
                                                    $days = $startDate->diffInDays(Carbon::parse($periodEnd)) + 1;
                                                    $totalAmount = $stall->rental_fee * $days;
                                                    $set('amount', $totalAmount);
                                                }
                                            }
                                        }
                                    }),

                                Forms\Components\DatePicker::make('period_end')
                                    ->label('Period End')
                                    ->required()
                                    ->default(now())
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            // Recalculate amount based on days
                                            $stallId = $get('stall_id');
                                            $periodStart = $get('period_start');
                                            if ($stallId && $periodStart) {
                                                $stall = Stall::find($stallId);
                                                if ($stall) {
                                                    $days = Carbon::parse($periodStart)->diffInDays(Carbon::parse($state)) + 1;
                                                    $totalAmount = $stall->rental_fee * $days;
                                                    $set('amount', $totalAmount);
                                                }
                                            }
                                        }
                                    }),

                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Payment Due Date')
                                    ->required()
                                    ->default(now()->addDay())
                                    ->helperText('When rental payment is due'),
                            ]),
                    ]),

                Forms\Components\Section::make('Payment Amount & Details')
                    ->description('Rental amount calculation and payment tracking')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('daily_rate_info')
                                    ->label('Daily Rate Info')
                                    ->content(function (Forms\Get $get) {
                                        $stallId = $get('stall_id');
                                        if (!$stallId) return 'Select a stall to see daily rate';
                                        
                                        $stall = Stall::find($stallId);
                                        if (!$stall) return 'Stall not found';
                                        
                                        $periodStart = $get('period_start');
                                        $periodEnd = $get('period_end');
                                        
                                        if ($periodStart && $periodEnd) {
                                            $days = Carbon::parse($periodStart)->diffInDays(Carbon::parse($periodEnd)) + 1;
                                            return "Daily Rate: ₱" . number_format($stall->rental_fee, 2) . 
                                                   "\nDays: {$days}" .
                                                   "\nTotal: ₱" . number_format($stall->rental_fee * $days, 2);
                                        }
                                        
                                        return "Daily Rate: ₱" . number_format($stall->rental_fee, 2);
                                    }),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Total Rental Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₱')
                                    ->step(0.01)
                                    ->minValue(0.01)
                                    ->maxValue(999999.99)
                                    ->placeholder('0.00')
                                    ->helperText('Calculated from daily rate × number of days'),

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
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('paid_date')
                                    ->label('Date Paid')
                                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['paid', 'partially_paid']))
                                    ->helperText('When was this rental payment completed?'),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Payment Notes')
                                    ->placeholder('Add any notes about this rental payment (e.g., partial payment amount, payment method, etc.)')
                                    ->maxLength(500)
                                    ->rows(2),
                            ]),
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
                    ->description(fn ($record) => $record->tenant?->name . ' • Daily: ₱' . number_format($record->stall?->rental_fee ?? 0, 2)),

                Tables\Columns\TextColumn::make('rental_period')
                    ->label('Rental Period')
                    ->getStateUsing(function ($record) {
                        $start = Carbon::parse($record->period_start);
                        $end = Carbon::parse($record->period_end);
                        $days = $start->diffInDays($end) + 1;
                        
                        if ($days === 1) {
                            return $start->format('M j, Y');
                        } else {
                            return $start->format('M j') . ' - ' . $end->format('M j, Y') . " ({$days} days)";
                        }
                    })
                    ->searchable(['period_start', 'period_end'])
                    ->sortable('period_start'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Rental Amount')
                    ->money('PHP')
                    ->sortable()
                    ->weight('semibold')
                    ->color('success')
                    ->description(function ($record) {
                        $start = Carbon::parse($record->period_start);
                        $end = Carbon::parse($record->period_end);
                        $days = $start->diffInDays($end) + 1;
                        $dailyRate = $record->stall?->rental_fee ?? 0;
                        
                        return "₱{$dailyRate}/day × {$days} day" . ($days > 1 ? 's' : '');
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : 'gray')
                    ->icon(fn ($record) => $record->is_overdue ? 'heroicon-m-exclamation-triangle' : null)
                    ->description(function ($record) {
                        if ($record->is_overdue && $record->status !== 'paid') {
                            $days = $record->due_date->diffInDays(now());
                            return "Overdue by {$days} day" . ($days > 1 ? 's' : '');
                        }
                        return null;
                    }),

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

                Tables\Filters\SelectFilter::make('tenant')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('today')
                    ->label('Today\'s Rentals')
                    ->query(fn (Builder $query) => 
                        $query->whereDate('period_start', '<=', now())
                              ->whereDate('period_end', '>=', now())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query) => 
                        $query->whereBetween('period_start', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ])
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('overdue_urgent')
                    ->label('Urgent (Overdue)')
                    ->query(fn (Builder $query) => 
                        $query->where('status', '!=', 'paid')
                              ->where('due_date', '<', now())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('due_soon')
                    ->label('Due Soon (Next 3 Days)')
                    ->query(fn (Builder $query) => 
                        $query->where('status', 'pending')
                              ->whereBetween('due_date', [now(), now()->addDays(3)])
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
                                ->placeholder('Add any notes about this rental payment...')
                                ->rows(2),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'status' => 'paid',
                                'paid_date' => $data['paid_date'],
                                'notes' => $data['notes'] ?? $record->notes,
                            ]);
                            Notification::make()->title('Rental payment marked as paid')->success()->send();
                        }),

                    Tables\Actions\Action::make('partial_payment')
                        ->label('Partial Payment')
                        ->icon('heroicon-o-minus-circle')
                        ->color('info')
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'overdue']))
                        ->form([
                            Forms\Components\TextInput::make('partial_amount')
                                ->label('Amount Paid')
                                ->numeric()
                                ->prefix('₱')
                                ->required()
                                ->placeholder('Enter partial payment amount'),
                            Forms\Components\DatePicker::make('paid_date')
                                ->label('Payment Date')
                                ->default(now())
                                ->required(),
                            Forms\Components\Textarea::make('notes')
                                ->label('Payment Notes')
                                ->placeholder('Add details about the partial payment...')
                                ->rows(2),
                        ])
                        ->action(function ($record, array $data) {
                            $notes = $record->notes ? $record->notes . "\n" : '';
                            $notes .= "Partial payment: ₱" . number_format($data['partial_amount'], 2) . " on " . Carbon::parse($data['paid_date'])->format('M j, Y');
                            if ($data['notes']) {
                                $notes .= " - " . $data['notes'];
                            }
                            
                            $record->update([
                                'status' => 'partially_paid',
                                'paid_date' => $data['paid_date'],
                                'notes' => $notes,
                            ]);
                            
                            Notification::make()->title('Partial rental payment recorded')->success()->send();
                        }),

                    Tables\Actions\Action::make('send_reminder')
                        ->label('Send Reminder')
                        ->icon('heroicon-o-bell')
                        ->color('warning')
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'overdue', 'partially_paid']))
                        ->action(function ($record) {
                            // Add your reminder email/SMS logic here
                            Notification::make()->title('Rental payment reminder sent to tenant')->success()->send();
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
                            Notification::make()->title("Marked {$count} rental payments as paid")->success()->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('send_reminders')
                        ->label('Send Reminders')
                        ->icon('heroicon-o-bell')
                        ->color('warning')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending', 'overdue', 'partially_paid'])) {
                                    // Add your reminder logic here
                                    $count++;
                                }
                            }
                            Notification::make()->title("Sent reminders for {$count} rental payments")->success()->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No rental payments recorded')
            ->emptyStateDescription('Start tracking daily rental payments from your tenants.')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Record Rental Payment')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('due_date', 'desc')
            ->poll('60s');
    }

    public static function getNavigationBadge(): ?string
    {
        $overdueCount = static::getModel()::where('status', 'overdue')->count();
        $dueSoonCount = static::getModel()::where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(3))
            ->count();
            
        $total = $overdueCount + $dueSoonCount;
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