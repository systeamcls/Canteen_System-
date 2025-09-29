<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceRecordResource\Pages;
use App\Models\AttendanceRecord;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceRecordResource extends Resource
{
    protected static ?string $model = AttendanceRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationGroup = 'Staff Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Attendance Records';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Record daily attendance for staff members')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Staff Member')
                                    ->options(User::where('is_active', true)
                                        ->where('is_staff', true)  // Only staff members
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                    
                                Forms\Components\DatePicker::make('work_date')
                                    ->label('Work Date')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->live(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Attendance Status')
                                    ->options([
                                        'present' => 'Present (Full Day)',
                                        'late' => 'Late Arrival',
                                        'half_day' => 'Half Day',
                                        'absent' => 'Absent',
                                    ])
                                    ->default('present')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        // Auto-set default times based on status
                                        if ($state === 'present') {
                                            $set('clock_in', '08:00');
                                            $set('clock_out', '17:00');
                                        } elseif ($state === 'late') {
                                            $set('clock_in', '09:00');
                                            $set('clock_out', '17:00');
                                        } elseif ($state === 'half_day') {
                                            $set('clock_in', '08:00');
                                            $set('clock_out', '12:00');
                                        } elseif ($state === 'absent') {
                                            $set('clock_in', null);
                                            $set('clock_out', null);
                                        }
                                    }),

                                Forms\Components\Toggle::make('free_meal_taken')
                                    ->label('Free Meal Taken')
                                    ->helperText('Did the employee take their free meal today?')
                                    ->default(false)
                                    ->visible(fn ($get) => in_array($get('status'), ['present', 'late', 'half_day'])),
                            ]),
                    ]),

                Forms\Components\Section::make('Time Records')
                    ->description('Clock in/out times (auto-filled based on status)')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('clock_in')
                                    ->label('Clock In Time')
                                    ->seconds(false)
                                    ->visible(fn ($get) => $get('status') !== 'absent')
                                    ->live(),
                                    
                                Forms\Components\TimePicker::make('clock_out')
                                    ->label('Clock Out Time')
                                    ->seconds(false)
                                    ->visible(fn ($get) => $get('status') !== 'absent')
                                    ->live(),
                            ]),

                        // Auto-calculated display fields
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('calculated_hours')
                                    ->label('Working Hours')
                                    ->content(function ($get): string {
                                        if (!$get('clock_in') || !$get('clock_out') || $get('status') === 'absent') {
                                            return '0 hours';
                                        }

                                        $clockIn = Carbon::parse($get('work_date') . ' ' . $get('clock_in'));
                                        $clockOut = Carbon::parse($get('work_date') . ' ' . $get('clock_out'));
                                        
                                        if ($clockOut < $clockIn) {
                                            $clockOut->addDay();
                                        }

                                        $totalHours = $clockIn->diffInHours($clockOut);
                                        $totalMinutes = $clockIn->diffInMinutes($clockOut) % 60;
                                        
                                        return "{$totalHours}h {$totalMinutes}m";
                                    }),

                                Forms\Components\Placeholder::make('daily_pay_preview')
                                    ->label('Daily Pay Preview')
                                    ->content(function ($get): string {
                                        $status = $get('status');
                                        $userId = $get('user_id');
                                        
                                        if (!$userId) return 'Select employee first';
                                        
                                        $user = User::find($userId);
                                        $dailyRate = $user?->daily_rate ?? 500; // Default fallback
                                        
                                        return match($status) {
                                            'present', 'late' => "₱{$dailyRate}",
                                            'half_day' => "₱" . ($dailyRate / 2),
                                            'absent' => "₱0",
                                            default => "₱0"
                                        };
                                    }),
                            ])
                            ->visible(fn ($get) => $get('status') && $get('work_date')),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($get) => $get('status') === 'absent'),

                Forms\Components\Section::make('Additional Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Any additional notes about this attendance record...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Hidden::make('recorded_by')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('work_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->weight('semibold'),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('time_summary')
                    ->label('Time')
                    ->getStateUsing(function (AttendanceRecord $record): string {
                        if ($record->status === 'absent') {
                            return 'Absent';
                        }
                        
                        if (!$record->clock_in || !$record->clock_out) {
                            return 'Incomplete';
                        }
                        
                        $clockIn = \Carbon\Carbon::parse($record->clock_in)->format('g:i A');
                        $clockOut = \Carbon\Carbon::parse($record->clock_out)->format('g:i A');
                        
                        return "{$clockIn} - {$clockOut}";
                    })
                    ->color(fn ($record) => match($record->status) {
                        'present' => 'success',
                        'late' => 'warning', 
                        'half_day' => 'info',
                        'absent' => 'danger',
                        default => 'gray'
                    })
                    ->weight('medium')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'late' => 'warning',
                        'half_day' => 'info',
                        'absent' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => 'Full Day',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                        'absent' => 'Absent',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('free_meal_taken')
                    ->label('Meal')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn ($record): string => $record->free_meal_taken ? 'Free meal taken' : 'No meal taken'),
                    
                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Hours')
                    ->numeric(decimalPlaces: 1)
                    ->suffix('h')
                    ->alignCenter()
                    ->color(fn ($state) => $state >= 8 ? 'success' : ($state >= 4 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('daily_earnings')
                    ->label('Daily Pay')
                    ->getStateUsing(function (AttendanceRecord $record): string {
                        $dailyRate = $record->user?->daily_rate ?? 500; // Get user's rate or default
                        
                        $earnings = match($record->status) {
                            'present', 'late' => $dailyRate,
                            'half_day' => $dailyRate / 2,
                            'absent' => 0,
                            default => 0
                        };
                        
                        return '₱' . number_format($earnings, 0);
                    })
                    ->color(fn ($record) => $record->status === 'absent' ? 'danger' : 'success')
                    ->weight('medium')
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Staff Member')
                    ->options(User::where('is_active', true)
                        ->where('is_staff', true)  // Only staff members
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->multiple(),
                    
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Present',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                        'absent' => 'Absent',
                    ])
                    ->multiple(),

                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereBetween('work_date', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ])
                    )
                    ->toggle(),

                Filter::make('today')
                    ->label('Today Only')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereDate('work_date', today())
                    )
                    ->toggle(),

                Filter::make('absent_only')
                    ->label('Absences Only')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('status', 'absent')
                    )
                    ->toggle(),

                Filter::make('work_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('work_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('work_date', '<=', $date),
                            );
                    })
            ])
            ->headerActions([
                Tables\Actions\Action::make('quick_attendance')
                    ->label('Quick Attendance')
                    ->icon('heroicon-o-bolt')
                    ->color('primary')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->default(today())
                            ->required(),
                        Forms\Components\Select::make('employees')
                            ->label('Mark Present')
                            ->options(User::where('is_active', true)
                                ->where('type', 'employee')
                                ->pluck('name', 'id'))
                            ->multiple()
                            ->searchable(),
                    ])
                    ->action(function (array $data) {
                        $created = 0;
                        foreach ($data['employees'] as $userId) {
                            AttendanceRecord::updateOrCreate(
                                [
                                    'user_id' => $userId,
                                    'work_date' => $data['date'],
                                ],
                                [
                                    'status' => 'present',
                                    'clock_in' => '08:00',
                                    'clock_out' => '17:00',
                                    'total_hours' => 8,
                                    'recorded_by' => Auth::id(),
                                ]
                            );
                            $created++;
                        }
                        
                        Notification::make()
                            ->title('Bulk Attendance Recorded')
                            ->body("{$created} employees marked present for " . Carbon::parse($data['date'])->format('M j, Y'))
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('toggle_meal')
                        ->label(fn ($record) => $record->free_meal_taken ? 'Remove Meal' : 'Add Meal')
                        ->icon(fn ($record) => $record->free_meal_taken ? 'heroicon-o-minus-circle' : 'heroicon-o-plus-circle')
                        ->color(fn ($record) => $record->free_meal_taken ? 'warning' : 'success')
                        ->action(function (AttendanceRecord $record) {
                            $record->update(['free_meal_taken' => !$record->free_meal_taken]);
                            
                            $status = $record->free_meal_taken ? 'added' : 'removed';
                            Notification::make()
                                ->title('Meal Status Updated')
                                ->body("Free meal {$status} for {$record->user->name}")
                                ->success()
                                ->send();
                        })
                        ->visible(fn ($record) => in_array($record->status, ['present', 'late', 'half_day'])),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_meals_taken')
                        ->label('Mark Meals Taken')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                if (in_array($record->status, ['present', 'late', 'half_day'])) {
                                    $record->update(['free_meal_taken' => true]);
                                    $updated++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Bulk Meal Update')
                                ->body("{$updated} employees marked as having taken free meals")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('export_attendance')
                        ->label('Export to Payroll')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function ($records) {
                            // Future integration with payroll system
                            Notification::make()
                                ->title('Export Prepared')
                                ->body(count($records) . ' attendance records ready for payroll processing')
                                ->info()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('work_date', 'desc')
            ->poll('30s') // Auto-refresh for real-time updates
            ->emptyStateHeading('No attendance records')
            ->emptyStateDescription('Start tracking daily attendance for your staff members.')
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Record First Attendance'),
            ])
            ->striped();
    }

    public static function getNavigationBadge(): ?string
    {
        $todayCount = static::getModel()::whereDate('work_date', today())->count();
        $totalStaff = User::where('is_active', true)->where('is_staff', true)->count();
        
        return $todayCount > 0 ? "{$todayCount}/{$totalStaff}" : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $todayCount = static::getModel()::whereDate('work_date', today())->count();
        $totalStaff = User::where('is_active', true)->where('is_staff', true)->count();
        
        if ($totalStaff === 0) return 'gray';
        
        $ratio = $todayCount / $totalStaff;
        
        return match(true) {
            $ratio >= 0.9 => 'success',   // Most staff recorded
            $ratio >= 0.6 => 'warning',  // Some staff recorded
            $ratio > 0 => 'info',        // Few staff recorded
            default => 'danger',         // No staff recorded yet
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceRecords::route('/'),
            'create' => Pages\CreateAttendanceRecord::route('/create'),
            'view' => Pages\ViewAttendanceRecord::route('/{record}'),
            'edit' => Pages\EditAttendanceRecord::route('/{record}/edit'),
        ];
    }
}