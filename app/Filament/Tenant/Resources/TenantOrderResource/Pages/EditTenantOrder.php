<?php

namespace App\Filament\Tenant\Resources\TenantOrderResource\Pages;

use App\Filament\Tenant\Resources\TenantOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantOrder extends EditRecord
{
    protected static string $resource = TenantOrderResource::class;
}
