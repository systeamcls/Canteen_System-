<?php

namespace App\Filament\Cashier\Resources\CashierProductResource\Pages;

use App\Filament\Cashier\Resources\CashierProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashierProduct extends EditRecord
{
    protected static string $resource = CashierProductResource::class;

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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Product settings updated successfully!';
    }
}