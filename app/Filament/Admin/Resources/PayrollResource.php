<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PayrollResource\Pages;
use App\Models\Payroll;
use App\Models\User;
use App\Models\AttendanceRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Financial Management';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Payroll';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payroll Period')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Staff Member')
                                    ->options(User::where('is_staff', true)
                                        ->where('is_active', true)
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn ($set, $state) => 
                                        $set('daily_rate', User::find($state)?->daily_rate ?? 500)
                                    ),
                                    
                                Forms\Components\DatePicker::make('period_start')
                                    ->label('Period Start')
                                    ->required()
                                    ->live(),
                                    
                                Forms\Components\DatePicker::make('period_end')
                                    ->label('Period End')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($set, $get) {
                                        // Auto-calculate when dates change
                                        if ($get('user_id') && $get('period_start') && $get('period_end')) {
                                            static::calculatePayroll($set, $get);
                                        }
                                    }),
                            ]),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('calculate')
                                ->label('Calculate from Attendance')
                                ->icon('heroicon-o-calculator')
                                ->color('primary')
                                ->action(function ($set, $get) {
                                    static::calculatePayroll($set, $get);
                                    
                                    Notification::make()
                                        ->title('Payroll Calculated')
                                        ->success()
                                        ->send();
                                })
                                ->visible(fn ($get) => $get('user_id') && $get('period_start') && $get('period_end')),
                        ]),
                    ]),

                Forms\Components\Section::make('Attendance Summary')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('days_present')
                                    ->label('Days Present')
                                    ->numeric()
                                    ->default(0)
                                    ->readOnly()
                                    ->suffix('days'),
                                    
                                Forms\Components\TextInput::make('days_late')
                                    ->label('Days Late')
                                    ->numeric()
                                    ->default(0)
                                    ->readOnly()
                                    ->suffix('days'),
                                    
                                Forms\Components\TextInput::make('days_half_day')
                                    ->label('Half Days')
                                    ->numeric()
                                    ->default(0)
                                    ->readOnly()
                                    ->suffix('days'),
                                    
                                Forms\Components\TextInput::make('days_absent')
                                    ->label('Days Absent')
                                    ->numeric()
                                    ->default(0)
                                    ->readOnly()
                                    ->suffix('days'),
                            ]),

                        Forms\Components\TextInput::make('total_hours')
                            ->label('Total Hours Worked')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->suffix('hours'),
                    ]),

                Forms\Components\Section::make('Payment Calculation')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('daily_rate')
                                    ->label('Daily Rate')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($set, $get) {
                                        static::calculatePayroll($set, $get);
                                    }),
                                    
                                Forms\Components\TextInput::make('gross_pay')
                                    ->label('Gross Pay')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->readOnly()
                                    ->live(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('deductions')
                                    ->label('Deductions')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($set, $get) {
                                        $grossPay = floatval($get('gross_pay')) ?? 0;
                                        $deductions = floatval($get('deductions')) ?? 0;
                                        $set('net_pay', max(0, $grossPay - $deductions));
                                    }),
                                    
                                Forms\Components\TextInput::make('net_pay')
                                    ->label('Net Pay')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->readOnly()
                                    ->extraAttributes(['class' => 'font-bold text-lg']),
                            ]),
                    ]),

                Forms\Components\Section::make('Payment Status')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'paid' => 'Paid',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->live(),
                                    
                                Forms\Components\DatePicker::make('paid_date')
                                    ->label('Payment Date')
                                    ->visible(fn ($get) => $get('status') === 'paid'),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2)
                            ->placeholder('Additional notes about this payroll...'),
                    ])
                    ->collapsible(),
            ]);
    }

    protected static function calculatePayroll($set, $get)
    {
        $userId = $get('user_id');
        $periodStart = $get('period_start');
        $periodEnd = $get('period_end');

        if (!$userId || !$periodStart || !$periodEnd) {
            return;
        }

        $attendance = AttendanceRecord::where('user_id', $userId)
            ->whereBetween('work_date', [$periodStart, $periodEnd])
            ->get();

        $daysPresent = $attendance->where('status', 'present')->count();
        $daysLate = $attendance->where('status', 'late')->count();
        $daysHalfDay = $attendance->where('status', 'half_day')->count();
        $daysAbsent = $attendance->where('status', 'absent')->count();
        $totalHours = $attendance->sum('total_hours');

        $set('days_present', $daysPresent);
        $set('days_late', $daysLate);
        $set('days_half_day', $daysHalfDay);
        $set('days_absent', $daysAbsent);
        $set('total_hours', $totalHours);

        $dailyRate = floatval($get('daily_rate')) ?? 500;

        // Calculate gross pay
        $grossPay = ($daysPresent * $dailyRate) + 
                    ($daysLate * $dailyRate) + 
                    ($daysHalfDay * ($dailyRate / 2));

        $set('gross_pay', $grossPay);

        $deductions = floatval($get('deductions')) ?? 0;
        $netPay = max(0, $grossPay - $deductions);
        $set('net_pay', $netPay);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('period')
                    ->label('Pay Period')
                    ->getStateUsing(fn ($record) => 
                        Carbon::parse($record->period_start)->format('M j') . ' - ' . 
                        Carbon::parse($record->period_end)->format('M j, Y')
                    )
                    ->searchable(['period_start', 'period_end'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendance_summary')
                    ->label('Attendance')
                    ->getStateUsing(fn ($record) => 
                        "P: {$record->days_present} | L: {$record->days_late} | H: {$record->days_half_day} | A: {$record->days_absent}"
                    )
                    ->tooltip('Present | Late | Half Day | Absent'),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Hours')
                    ->numeric(decimalPlaces: 1)
                    ->suffix('h')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('gross_pay')
                    ->label('Gross Pay')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('deductions')
                    ->label('Deductions')
                    ->money('PHP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('net_pay')
                    ->label('Net Pay')
                    ->money('PHP')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'paid' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('paid_date')
                    ->label('Paid On')
                    ->date('M j, Y')
                    ->placeholder('Not paid')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Staff Member')
                    ->options(User::where('is_staff', true)->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'paid' => 'Paid',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('period')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->where('period_start', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->where('period_end', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->visible(fn ($record) => $record->status === 'pending')
                        ->action(function ($record) {
                            $record->update(['status' => 'approved']);
                            Notification::make()
                                ->title('Payroll Approved')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'approved']))
                        ->form([
                            Forms\Components\DatePicker::make('paid_date')
                                ->label('Payment Date')
                                ->default(now())
                                ->required(),
                            Forms\Components\Textarea::make('notes')
                                ->label('Payment Notes'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'status' => 'paid',
                                'paid_date' => $data['paid_date'],
                                'notes' => $data['notes'] ?? $record->notes,
                            ]);
                            
                            Notification::make()
                                ->title('Payment Recorded')
                                ->body("Payroll marked as paid for {$record->user->name}")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Actions'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generate_payroll')
                    ->label('Generate Payroll')
                    ->icon('heroicon-o-document-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('users')
                            ->label('Staff Members')
                            ->options(User::where('is_staff', true)->where('is_active', true)->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('period_start')
                            ->label('Period Start')
                            ->default(now()->startOfMonth())
                            ->required(),
                        Forms\Components\DatePicker::make('period_end')
                            ->label('Period End')
                            ->default(now()->endOfMonth())
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $count = 0;
                        foreach ($data['users'] as $userId) {
                            $user = User::find($userId);
                            Payroll::generateForPeriod($user, $data['period_start'], $data['period_end']);
                            $count++;
                        }
                        
                        Notification::make()
                            ->title('Payroll Generated')
                            ->body("Generated payroll for {$count} staff members")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_all')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->action(function ($records) {
                            $records->each->update(['status' => 'approved']);
                            Notification::make()
                                ->title('Payrolls Approved')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_paid_bulk')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->form([
                            Forms\Components\DatePicker::make('paid_date')
                                ->default(now())
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update([
                                'status' => 'paid',
                                'paid_date' => $data['paid_date'],
                            ]);
                            
                            Notification::make()
                                ->title('Payments Recorded')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('period_start', 'desc')
            ->emptyStateHeading('No payroll records')
            ->emptyStateDescription('Generate payroll records from attendance data.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'view' => Pages\ViewPayroll::route('/{record}'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
