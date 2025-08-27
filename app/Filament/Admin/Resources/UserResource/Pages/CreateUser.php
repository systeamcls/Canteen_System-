<?php

// app/Filament/Admin/Resources/UserResource/Pages/CreateUser.php
namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extract role from form data
        $role = $data['role'] ?? 'tenant';
        
        // Remove role from data since it's not a database field
        unset($data['role']);
        
        // Create the user
        $user = static::getModel()::create($data);
        
        // Assign the role
        $user->assignRole($role);
        
        // Send notification
        Notification::make()
            ->title('User created successfully')
            ->body("New {$role} user has been created and can now access the system.")
            ->success()
            ->send();
            
        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
