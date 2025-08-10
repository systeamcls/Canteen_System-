<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Attendance';

    public static function getEloquentQuery(): Builder
    {
        // Show attendance for cashiers only (employees managed by admin)
        return parent::getEloquentQuery()
            ->whereHas('user', function (Builder $query) {
                $query->whereHas('roles', function (Builder $roleQuery) {
                    $roleQuery->where('name', 'cashier');
                });
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Employee')
                            ->relationship('user', 'name', function (Builder $query) {
                                // Only show cashiers
                                $query->whereHas('roles', function (Builder $roleQuery) {
                                    $roleQuery->where('name', 'cashier');
                                });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(today())
                            ->maxDate(today()),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'present' => 'Present',
                                'absent' => 'Absent',
                                'late' => 'Late',
                                'half_day' => 'Half Day',
                                'sick' => 'Sick Leave',
                                'vacation' => 'Vacation',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Time Details')
                    ->schema([
                        Forms\Components\TimePicker::make('clock_in')
                            ->label('Clock In')
                            ->visible(fn (Forms\Get $get): bool => 
                                in_array($get('status'), ['present', 'late', 'half_day']))
                            ->required(fn (Forms\Get $get): bool => 
                                in_array($get('status'), ['present', 'late', 'half_day'])),
                        
                        Forms\Components\TimePicker::make('clock_out')
                            ->label('Clock Out')
                            ->visible(fn (Forms\Get $get): bool => 
                                in_array($get('status'), ['present', 'late', 'half_day']))
                            ->after('clock_in'),
                        
                        Forms\Components\TimePicker::make('break_start')
                            ->label('Break Start')
                            ->visible(fn (Forms\Get $get): bool => 
                                $get('status') === 'present'),
                        
                        Forms\Components\TimePicker::make('break_end')
                            ->label('Break End')
                            ->visible(fn (Forms\Get $get): bool => 
                                $get('status') === 'present')
                            ->after('break_start'),
                    ])
                    ->columns(4)
                    ->visible(fn (Forms\Get $get): bool => 
                        !in_array($get('status'), ['absent', 'sick', 'vacation'])),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Approved')
                            ->disabled(fn (?Attendance $record): bool => 
                                $record && $record->is_approved)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('approved_by', Auth::id());
                                    $set('approved_at', now());
                                }
                            }),
                        
                        Forms\Components\Hidden::make('approved_by'),
                        Forms\Components\Hidden::make('approved_at'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('M j, Y')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (Attendance $record): string => $record->status_color)
                    ->icon(fn (Attendance $record): string => $record->status_icon),
                
                Tables\Columns\TextColumn::make('clock_in')
                    ->label('Clock In')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('clock_out')
                    ->label('Clock Out')
                    ->time('H:i')
                    ->placeholder('--:--')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours')
                    ->formatStateUsing(fn ($state): string => number_format($state, 2) . 'h')
                    ->alignCenter()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('overtime_hours')
                    ->label('Overtime')
                    ->formatStateUsing(fn ($state): string => 
                        $state > 0 ? '+' . number_format($state, 2) . 'h' : '--')
                    ->color(fn ($state): string => $state > 0 ? 'warning' : 'gray')
                    ->alignCenter()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->placeholder('Pending')
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('notes')
                    ->limit(30)
                    ->tooltip(fn (Attendance $record): string => $record->notes ?? '')
                    ->placeholder('No notes'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name', function (Builder $query) {
                        $query->whereHas('roles', function (Builder $roleQuery) {
                            $roleQuery->where('name', 'cashier');
                        });
                    }),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                        'sick' => 'Sick Leave',
                        'vacation' => 'Vacation',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Approval Status'),
                
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->action(function (Attendance $record) {
                        $record->update([
                            'is_approved' => true,
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Attendance $record): bool => !$record->is_approved),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-m-check-badge')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'is_approved' => true,
                                    'approved_by' => Auth::id(),
                                    'approved_at' => now(),
                                ]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_approved', false)
            ->whereHas('user', function (Builder $query) {
                $query->whereHas('roles', function (Builder $roleQuery) {
                    $roleQuery->where('name', 'cashier');
                });
            })
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}