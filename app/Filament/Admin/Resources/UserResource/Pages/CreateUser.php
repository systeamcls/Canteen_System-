<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = parent::handleRecordCreation($data);
        
        // Assign role to the user
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }
        
        return $user;
    }
}