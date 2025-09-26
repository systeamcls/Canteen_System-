<?php

namespace App\Filament\Cashier\Resources\CashierProductResource\Pages;

use App\Filament\Cashier\Resources\CashierProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCashierProduct extends ViewRecord
{
    protected static string $resource = CashierProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('toggle_availability')
                ->label(fn () => $this->record->is_available ? '❌ Mark Unavailable' : '✅ Mark Available')
                ->icon(fn () => $this->record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn () => $this->record->is_available ? 'danger' : 'success')
                ->action(function () {
                    $this->record->update(['is_available' => !$this->record->is_available]);
                    $status = $this->record->is_available ? 'available' : 'unavailable';
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Stock Updated')
                        ->body("{$this->record->name} is now {$status}")
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }
}