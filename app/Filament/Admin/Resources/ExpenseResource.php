<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Expense Tracker';

    public static function getEloquentQuery(): Builder
    {
        $adminStall = Auth::user()->stall;
        
        // Admin can only see expenses for their stall
        return parent::getEloquentQuery()
            ->when($adminStall, function (Builder $query) use ($adminStall) {
                $query->where('stall_id', $adminStall->id);
            })
            ->when(!$adminStall, function (Builder $query) {
                // If admin has no stall, show no expenses
                $query->whereRaw('1 = 0');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Expense Information')
                    ->schema([
                        Forms\Components\Hidden::make('stall_id')
                            ->default(fn () => Auth::user()->stall?->id),
                        
                        Forms\Components\Hidden::make('user_id')
                            ->default(Auth::id()),
                        
                        Forms\Components\Select::make('category')
                            ->options([
                                'ingredients' => 'Ingredients & Food Supplies',
                                'utilities' => 'Utilities (Electricity, Water, Gas)',
                                'equipment' => 'Equipment & Tools',
                                'rent' => 'Rent & Space Fees',
                                'marketing' => 'Marketing & Advertising',
                                'maintenance' => 'Maintenance & Repairs',
                                'packaging' => 'Packaging Materials',
                                'cleaning' => 'Cleaning Supplies',
                                'transportation' => 'Transportation',
                                'miscellaneous' => 'Miscellaneous',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable(),
                        
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Brief description of the expense'),
                        
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0)
                            ->maxValue(999999.99),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Purchase Details')
                    ->schema([
                        Forms\Components\DatePicker::make('expense_date')
                            ->required()
                            ->default(today())
                            ->maxDate(today()),
                        
                        Forms\Components\TextInput::make('vendor_name')
                            ->maxLength(255)
                            ->placeholder('Name of supplier or vendor'),
                        
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'gcash' => 'GCash',
                                'bank_transfer' => 'Bank Transfer',
                                'card' => 'Credit/Debit Card',
                                'check' => 'Check',
                            ])
                            ->required()
                            ->native(false),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Approval',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Documentation')
                    ->schema([
                        Forms\Components\FileUpload::make('receipt_image')
                            ->label('Receipt/Invoice')
                            ->image()
                            ->directory('expenses')
                            ->maxSize(5120) // 5MB
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio(null)
                            ->imageResizeTargetWidth(800)
                            ->imageResizeTargetHeight(600)
                            ->helperText('Upload receipt or invoice (max 5MB)'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->placeholder('Additional notes or comments')
                            ->columnSpanFull(),
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
                
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (Expense $record): string => $record->category_color)
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (Expense $record): string => $record->description),
                
                Tables\Columns\TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable()
                    ->weight('semibold')
                    ->alignEnd(),
                
                Tables\Columns\TextColumn::make('vendor_name')
                    ->label('Vendor')
                    ->searchable()
                    ->placeholder('N/A')
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'gcash' => 'info',
                        'bank_transfer' => 'primary',
                        'card' => 'warning',
                        'check' => 'gray',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (Expense $record): string => $record->status_color)
                    ->icon(fn (Expense $record): string => $record->status_icon)
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('receipt_image')
                    ->label('Receipt')
                    ->size(40)
                    ->defaultImageUrl('/images/no-receipt.png'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Recorded By')
                    ->limit(15)
                    ->tooltip(fn (Expense $record): string => $record->user->name),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'ingredients' => 'Ingredients & Food Supplies',
                        'utilities' => 'Utilities',
                        'equipment' => 'Equipment & Tools',
                        'rent' => 'Rent & Space Fees',
                        'marketing' => 'Marketing & Advertising',
                        'maintenance' => 'Maintenance & Repairs',
                        'packaging' => 'Packaging Materials',
                        'cleaning' => 'Cleaning Supplies',
                        'transportation' => 'Transportation',
                        'miscellaneous' => 'Miscellaneous',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'gcash' => 'GCash',
                        'bank_transfer' => 'Bank Transfer',
                        'card' => 'Credit/Debit Card',
                        'check' => 'Check',
                    ]),
                
                Tables\Filters\Filter::make('expense_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date),
                            );
                    }),
                
                Tables\Filters\Filter::make('amount')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->label('Min Amount')
                            ->numeric()
                            ->prefix('₱'),
                        Forms\Components\TextInput::make('amount_until')
                            ->label('Max Amount')
                            ->numeric()
                            ->prefix('₱'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_until'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
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
                    ->action(function (Expense $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Expense $record): bool => $record->status === 'pending'),
                
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->action(fn (Expense $record) => $record->update(['status' => 'rejected']))
                    ->requiresConfirmation()
                    ->visible(fn (Expense $record): bool => $record->status === 'pending'),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('expense_date', 'desc')
            ->striped();
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

    public static function getNavigationBadge(): ?string
    {
        $adminStall = Auth::user()->stall;
        
        if (!$adminStall) {
            return null;
        }
        
        return static::getModel()::where('stall_id', $adminStall->id)
            ->where('status', 'pending')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}