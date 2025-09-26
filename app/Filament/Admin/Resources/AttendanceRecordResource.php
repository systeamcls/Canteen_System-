<?php
// app/Filament/Resources/AttendanceRecordResource.php
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceRecordResource extends Resource
{
    protected static ?string $model = AttendanceRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Attendance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Employee')
                            ->options(User::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                            
                        Forms\Components\DatePicker::make('work_date')
                            ->label('Work Date')
                            ->required()
                            ->default(now()),
                    ]),
                    
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\TimePicker::make('clock_in')
                            ->label('Clock In')
                            ->seconds(false),
                            
                        Forms\Components\TimePicker::make('clock_out')
                            ->label('Clock Out')
                            ->seconds(false),
                            
                        Forms\Components\TimePicker::make('break_start')
                            ->label('Break Start')
                            ->seconds(false),
                            
                        Forms\Components\TimePicker::make('break_end')
                            ->label('Break End')
                            ->seconds(false),
                    ]),
                    
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('total_hours')
                            ->label('Total Hours')
                            ->numeric()
                            ->step(0.25)
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('regular_hours')
                            ->label('Regular Hours')
                            ->numeric()
                            ->step(0.25)
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('overtime_hours')
                            ->label('Overtime Hours')
                            ->numeric()
                            ->step(0.25)
                            ->disabled()
                            ->dehydrated(false),
                    ]),
                    
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                    ])
                    ->default('present')
                    ->required(),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
                    
                Forms\Components\Hidden::make('recorded_by')
                    ->default(Auth::id()),
            ])
            ->afterStateUpdated(function ($state, $set, $get) {
                // Auto-calculate hours when clock times change
                if ($get('clock_in') && $get('clock_out')) {
                    $clockIn = Carbon::parse($get('work_date') . ' ' . $get('clock_in'));
                    $clockOut = Carbon::parse($get('work_date') . ' ' . $get('clock_out'));
                    
                    if ($clockOut < $clockIn) {
                        $clockOut->addDay();
                    }

                    $totalMinutes = $clockIn->diffInMinutes($clockOut);
                    
                    if ($get('break_start') && $get('break_end')) {
                        $breakStart = Carbon::parse($get('work_date') . ' ' . $get('break_start'));
                        $breakEnd = Carbon::parse($get('work_date') . ' ' . $get('break_end'));
                        $breakMinutes = $breakStart->diffInMinutes($breakEnd);
                        $totalMinutes -= $breakMinutes;
                    }

                    $totalHours = round($totalMinutes / 60, 2);
                    $regularHours = min($totalHours, 8);
                    $overtimeHours = max(0, $totalHours - 8);

                    $set('total_hours', $totalHours);
                    $set('regular_hours', $regularHours);
                    $set('overtime_hours', $overtimeHours);
                }
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('work_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('clock_in')
                    ->label('Clock In')
                    ->time('H:i'),
                    
                Tables\Columns\TextColumn::make('clock_out')
                    ->label('Clock Out')
                    ->time('H:i'),
                    
                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Total Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' hrs'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'late' => 'warning',
                        'absent' => 'danger',
                        'half_day' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('overtime_hours')
                    ->label('OT Hours')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' hrs')
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Employee')
                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
                    
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                    ]),
                    
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('calculate_hours')
                    ->label('Recalculate')
                    ->icon('heroicon-o-calculator')
                    ->action(function (AttendanceRecord $record) {
                        $record->calculateHours();
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('recalculate_hours')
                        ->label('Recalculate Hours')
                        ->icon('heroicon-o-calculator')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->calculateHours();
                                $record->save();
                            }
                        })
                        ->requiresConfirmation()
                        ->color('warning'),
                ]),
            ])
            ->defaultSort('work_date', 'desc');
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('work_date', today())->count();
    }
}