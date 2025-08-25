<?php

namespace App\Filament\Tenant\Resources\TenantOrderResource\Pages;

use App\Filament\Tenant\Resources\TenantOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenantOrders extends ListRecords
{
    protected static string $resource = TenantOrderResource::class;
}
