<?php

namespace App\Filament\Tenant\Resources\TenantProductResource\Pages;

use App\Filament\Tenant\Resources\TenantProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenantProduct extends EditRecord
{
    protected static string $resource = TenantProductResource::class;
}
