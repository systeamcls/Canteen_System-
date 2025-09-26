<?php

namespace App\Filament\Admin\Resources\AttendanceRecordResource\Pages;

use App\Filament\Admin\Resources\AttendanceRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceRecord extends CreateRecord
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate hours before saving
        if (isset($data['clock_in']) && isset($data['clock_out'])) {
            $record = new \App\Models\AttendanceRecord($data);
            $record->calculateHours();
            $data = array_merge($data, $record->only(['total_hours', 'regular_hours', 'overtime_hours']));
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
