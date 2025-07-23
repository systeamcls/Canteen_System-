<?php

namespace App\Filament\Tenant\Resources\ReviewResource\Pages;

use App\Filament\Tenant\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;
}
