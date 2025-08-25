<?php

namespace App\Filament\Tenant\Resources\TenantProductResource\Pages;

use App\Filament\Tenant\Resources\TenantProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantProduct extends CreateRecord
{
    protected static string $resource = TenantProductResource::class;
}
