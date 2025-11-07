<?php

namespace App\Filament\Admin\Resources\PayrollResource\Pages;

use App\Filament\Admin\Resources\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayroll extends ViewRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
