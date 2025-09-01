<?php

namespace App\Filament\Tenant\Resources\TenantStallResource\Pages;

use App\Filament\Tenant\Resources\TenantStallResource;
use Filament\Resources\Pages\ListRecords;

class ListTenantStalls extends ListRecords
{
    protected static string $resource = TenantStallResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}