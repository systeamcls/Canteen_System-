<?php

namespace App\Filament\Admin\Resources\AttendanceRecordResource\Pages;

use App\Filament\Admin\Resources\AttendanceRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceRecord extends ViewRecord
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}