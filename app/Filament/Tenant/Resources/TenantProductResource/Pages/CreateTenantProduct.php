<?php

namespace App\Filament\Tenant\Resources\TenantProductResource\Pages;

use App\Filament\Tenant\Resources\TenantProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CreateTenantProduct extends CreateRecord
{
    protected static string $resource = TenantProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $stall = $user->assignedStall;
        
        if (!$stall) {
            Notification::make()
                ->title('No stall assigned')
                ->body('You need to be assigned to a stall before creating products.')
                ->danger()
                ->send();
                
            $this->halt();
        }

        $data['stall_id'] = $stall->id;
        $data['created_by'] = $user->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Product created successfully';
    }
}
