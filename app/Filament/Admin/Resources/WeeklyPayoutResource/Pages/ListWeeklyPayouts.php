<?php

// File 1: app/Filament/Admin/Resources/WeeklyPayoutResource/Pages/ListWeeklyPayouts.php
namespace App\Filament\Admin\Resources\WeeklyPayoutResource\Pages;

use App\Filament\Admin\Resources\WeeklyPayoutResource;
use Filament\Resources\Pages\ListRecords;

class ListWeeklyPayouts extends ListRecords
{
    protected static string $resource = WeeklyPayoutResource::class;
}