<?php

namespace App\Filament\Admin\Resources\AttendanceRecordResource\Pages;

use App\Filament\Admin\Resources\AttendanceRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceRecord extends EditRecord
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate hours when updating
        if (isset($data['clock_in']) && isset($data['clock_out'])) {
            $record = clone $this->record;
            $record->fill($data);
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