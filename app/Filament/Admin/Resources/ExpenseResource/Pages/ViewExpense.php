<?php

namespace App\Filament\Admin\Resources\ExpenseResource\Pages;

use App\Filament\Admin\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Expense Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('category.name')
                                    ->label('Category')
                                    ->badge()
                                    ->color(fn ($record) => $record->category->color ?? 'gray'),
                                    
                                TextEntry::make('expense_date')
                                    ->label('Date')
                                    ->date('F j, Y'),
                            ]),
                            
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                            
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('amount')
                                    ->label('Amount')
                                    ->money('PHP')
                                    ->size('lg')
                                    ->weight('bold'),
                                    
                                TextEntry::make('vendor')
                                    ->label('Vendor/Store')
                                    ->placeholder('Not specified'),
                                    
                                TextEntry::make('receipt_number')
                                    ->label('Receipt Number')
                                    ->placeholder('Not provided'),
                            ]),
                            
                        TextEntry::make('notes')
                            ->label('Additional Notes')
                            ->placeholder('No additional notes')
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('Record Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('recordedBy.name')
                                    ->label('Recorded By'),
                                    
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('F j, Y \a\t g:i A'),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('F j, Y \a\t g:i A')
                                    ->since(),
                                    
                                TextEntry::make('id')
                                    ->label('Expense ID')
                                    ->prefix('#'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}