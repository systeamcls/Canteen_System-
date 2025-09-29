<?php

// app/Filament/Admin/Resources/UserResource/Pages/EditUser.php
namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            
            Actions\Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\TextInput::make('new_password')
                        ->label('New Password')
                        ->password()
                        ->required()
                        ->minLength(8),
                    \Filament\Forms\Components\TextInput::make('confirm_password')
                        ->label('Confirm Password')
                        ->password()
                        ->required()
                        ->same('new_password'),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'password' => \Illuminate\Support\Facades\Hash::make($data['new_password'])
                    ]);
                    
                    Notification::make()
                        ->title('Password updated')
                        ->body('User password has been reset successfully.')
                        ->success()
                        ->send();
                }),
                
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Add current role to form data
        if ($this->record->roles->isNotEmpty()) {
            $data['role'] = $this->record->roles->first()->name;
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle role updates
        if (isset($data['role'])) {
            $newRole = $data['role'];
            $currentRoles = $this->record->roles->pluck('name')->toArray();
            
            // Only update roles if they changed
            if (!in_array($newRole, $currentRoles)) {
                $this->record->syncRoles([$newRole]);
                
                Notification::make()
                    ->title('Role updated')
                    ->body("User role changed to {$newRole}")
                    ->success()
                    ->send();
            }
            
            // Remove role from data since it's not a database field
            unset($data['role']);
        }

        return $data;
    }

    protected function afterSave(): void
{
    UserResource::handleStallAssignment($this->record, $this->data);
}
}