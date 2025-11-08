<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WeeklyPayoutResource\Pages;
use App\Models\WeeklyPayout;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class WeeklyPayoutResource extends Resource
{
    protected static ?string $model = WeeklyPayout::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financial Management';
    protected static ?string $navigationLabel = 'Weekly Payouts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Staff Member')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                    
                Forms\Components\DatePicker::make('week_start')
                    ->label('Week Start')
                    ->required(),
                    
                Forms\Components\DatePicker::make('week_end')
                    ->label('Week End')
                    ->required(),
                    
                Forms\Components\TextInput::make('total_payout')
                    ->label('Total Payout')
                    ->numeric()
                    ->prefix('₱')
                    ->required(),
                    
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
                    
                Forms\Components\DatePicker::make('paid_date')
                    ->label('Paid Date')
                    ->visible(fn ($get) => $get('status') === 'paid'),
                    
                Forms\Components\Textarea::make('notes')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('week_period')
                    ->label('Week')
                    ->getStateUsing(fn ($record) => 
                        $record->week_start->format('M d') . ' - ' . $record->week_end->format('M d, Y')
                    ),
                    
                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Hours')
                    ->suffix('h'),
                    
                Tables\Columns\TextColumn::make('total_payout')
                    ->label('Payout')
                    ->money('PHP')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    }),
                    
                Tables\Columns\TextColumn::make('paid_date')
                    ->date('M j, Y')
                    ->placeholder('Not paid'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generate')
                    ->label('Generate This Week')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Weekly Payouts')
                    ->modalDescription('Generate payouts for current week based on attendance records')
                    ->action(function () {
                        $weekStart = Carbon::now()->startOfWeek();
                        $weekEnd = Carbon::now()->endOfWeek();
                        
                        $staff = User::where('is_active', true)
                            ->where('is_staff', true)
                            ->get();
                        
                        $count = 0;
                        foreach ($staff as $employee) {
                            WeeklyPayout::generateForWeek($employee, $weekStart);
                            $count++;
                        }
                        
                        Notification::make()
                            ->title('Payouts Generated!')
                            ->body("Generated {$count} payouts for week " . $weekStart->format('M d') . ' - ' . $weekEnd->format('M d, Y'))
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\DatePicker::make('paid_date')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'paid',
                            'paid_date' => $data['paid_date'],
                        ]);
                        Notification::make()->title('Marked as Paid')->success()->send();
                    }),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('week_start', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWeeklyPayouts::route('/'),
            'create' => Pages\CreateWeeklyPayout::route('/create'),
            'edit' => Pages\EditWeeklyPayout::route('/{record}/edit'),
        ];
    }
}