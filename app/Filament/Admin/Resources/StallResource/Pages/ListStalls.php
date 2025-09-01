<?php

namespace App\Filament\Admin\Resources\StallResource\Pages;

use App\Filament\Admin\Resources\StallResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStalls extends ListRecords
{
    protected static string $resource = StallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add New Stall')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}