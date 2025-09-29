<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\ExpenseCategory;
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
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Grouping\Group;
use Filament\Notifications\Notification;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financial Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Expense Details')
                    ->description('Record a new expense entry')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(ExpenseCategory::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\ColorPicker::make('color')
                                            ->default('#3B82F6'),
                                        Forms\Components\Textarea::make('description'),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return ExpenseCategory::create($data)->id;
                                    }),
                                    
                                Forms\Components\DatePicker::make('expense_date')
                                    ->label('Date')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->displayFormat('M j, Y'),
                            ]),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount')
                                    ->numeric()
                                    ->required()
                                    ->prefix('₱')
                                    ->step(0.01)
                                    ->minValue(0.01)
                                    ->maxValue(999999.99),
                                    
                                Forms\Components\TextInput::make('receipt_number')
                                    ->label('Receipt Number')
                                    ->maxLength(255)
                                    ->placeholder('Optional'),
                                    
                                Forms\Components\TextInput::make('vendor')
                                    ->label('Vendor/Store')
                                    ->maxLength(255)
                                    ->placeholder('Where was this purchased?'),
                            ]),
                            
                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('What was this expense for?')
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Additional Notes')
                            ->rows(3)
                            ->placeholder('Any additional details...')
                            ->columnSpanFull(),
                            
                        Forms\Components\Hidden::make('recorded_by')
                            ->default(Auth::id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->category->color ?? '#6B7280')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('N/A'),
                    
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('recordedBy.name')
                    ->label('Recorded By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('expense_date')
                    ->label('Date')
                    ->date()
                    ->collapsible()
                    ->orderQueryUsing(fn (Builder $query, string $direction) => 
                        $query->orderBy('expense_date', $direction)
                    ),
            ])
            ->defaultGroup('expense_date')
            ->groupsOnly()
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(ExpenseCategory::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                Filter::make('date_range')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['from'] && !$data['until']) {
                            return null;
                        }
                        
                        $from = $data['from'] ? Carbon::parse($data['from'])->format('M j, Y') : 'start';
                        $until = $data['until'] ? Carbon::parse($data['until'])->format('M j, Y') : 'now';
                        
                        return "From {$from} to {$until}";
                    }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::Dropdown)
            ->actions([
                // Inline edit action
                Tables\Actions\Action::make('quick_edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->size('sm')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(ExpenseCategory::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                    
                                Forms\Components\DatePicker::make('expense_date')
                                    ->label('Date')
                                    ->required(),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount')
                                    ->numeric()
                                    ->required()
                                    ->prefix('₱'),
                                    
                                Forms\Components\TextInput::make('vendor')
                                    ->label('Vendor/Store'),
                            ]),
                            
                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ])
                    ->fillForm(fn ($record): array => [
                        'category_id' => $record->category_id,
                        'expense_date' => $record->expense_date,
                        'amount' => $record->amount,
                        'vendor' => $record->vendor,
                        'description' => $record->description,
                        'notes' => $record->notes,
                    ])
                    ->action(function ($record, array $data) {
                        $record->update($data);
                        
                        Notification::make()
                            ->title('Expense Updated')
                            ->body('The expense has been updated successfully.')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Edit Expense')
                    ->modalWidth('lg'),
                    
                Tables\Actions\DeleteAction::make()
                    ->size('sm')
                    ->requiresConfirmation(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Expense')
                    ->icon('heroicon-o-plus')
                    ->color('danger')
                    ->modalWidth('lg'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_categorize')
                        ->label('Change Category')
                        ->icon('heroicon-o-tag')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('category_id')
                                ->label('New Category')
                                ->options(ExpenseCategory::where('is_active', true)->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['category_id' => $data['category_id']]);
                            
                            Notification::make()
                                ->title('Categories Updated')
                                ->body('Updated category for ' . $records->count() . ' expenses.')
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No expenses recorded yet')
            ->emptyStateDescription('Start tracking your expenses by adding your first entry.')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add First Expense'),
            ])
            ->defaultSort('expense_date', 'desc')
            ->poll('60s')
            ->deferLoading();
    }

    public static function getNavigationBadge(): ?string
    {
        $todayCount = static::getModel()::whereDate('expense_date', today())->count();
        return $todayCount > 0 ? (string) $todayCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    // Custom method to get expense summaries for dashboard
    public static function getExpenseSummary(): array
    {
        $today = static::getModel()::whereDate('expense_date', today())->sum('amount');
        $thisWeek = static::getModel()::whereBetween('expense_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->sum('amount');
        $thisMonth = static::getModel()::whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');
        $thisYear = static::getModel()::whereYear('expense_date', now()->year)->sum('amount');

        return [
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
            'this_year' => $thisYear,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}