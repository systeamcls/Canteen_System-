<?php

namespace App\Filament\Tenant\Resources\TenantProductResource\Pages;

use App\Filament\Tenant\Resources\TenantProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantProducts extends ListRecords
{
    protected static string $resource = TenantProductResource::class;
}
