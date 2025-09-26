<?php

// app/Filament/Cashier/Resources/CashierOrderResource/Pages/EditCashierOrder.php

namespace App\Filament\Cashier\Resources\CashierOrderResource\Pages;

use App\Filament\Cashier\Resources\CashierOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashierOrder extends EditRecord
{
    protected static string $resource = CashierOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}