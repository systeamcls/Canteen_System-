<?php

// app/Filament/Cashier/Resources/CashierOrderResource/Pages/ViewCashierOrder.php

namespace App\Filament\Cashier\Resources\CashierOrderResource\Pages;

use App\Filament\Cashier\Resources\CashierOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCashierOrder extends ViewRecord
{
    protected static string $resource = CashierOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('print_receipt')
                ->label('🖨️ Print Receipt')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->action(function () {
                    // Add print logic
                    \Filament\Notifications\Notification::make()
                        ->title('Receipt Printed')
                        ->success()
                        ->send();
                }),
        ];
    }
}