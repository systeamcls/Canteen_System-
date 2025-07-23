<?php

namespace App\Filament\Tenant\Resources\RentalPaymentResource\Pages;

use App\Filament\Tenant\Resources\RentalPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalPayments extends ListRecords
{
    protected static string $resource = RentalPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
