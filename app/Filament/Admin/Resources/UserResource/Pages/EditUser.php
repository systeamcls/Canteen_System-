<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
                        'password' => Hash::make($data['new_password'])
                    ]);
                    
                    // Log password change
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->log('Password reset by admin');
                    
                    Notification::make()
                        ->title('Password Updated')
                        ->body('User password has been reset successfully.')
                        ->success()
                        ->send();
                }),
                
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->before(function () {
                    // Log deletion
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->log('User deleted by admin');
                })
                ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load current role from Spatie
        $primaryRole = $this->record->getPrimaryRole();
        if ($primaryRole) {
            $data['type'] = $primaryRole;
        }
        
        // Load assigned stall for tenants
        if ($this->record->hasRole('tenant')) {
            $stall = \App\Models\Stall::where('tenant_id', $this->record->id)->first();
            if ($stall) {
                $data['assigned_stall'] = $stall->id;
            }
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $oldRole = $this->record->getPrimaryRole();
            
            // 🔐 SECURE: Sync Spatie roles when user type changes
            $roleMapping = [
                'tenant' => 'tenant',
                'cashier' => 'cashier',
                'staff' => 'customer',
            ];

            if (isset($data['type']) && isset($roleMapping[$data['type']])) {
                $newRole = $roleMapping[$data['type']];
                
                // Only update if role changed
                if ($oldRole !== $newRole) {
                    $this->record->syncRoles([$newRole]);
                    
                    // Log role change
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties([
                            'old_role' => $oldRole,
                            'new_role' => $newRole
                        ])
                        ->log('User role changed');
                    
                    Notification::make()
                        ->title('Role Updated')
                        ->body("User role changed from '{$oldRole}' to '{$newRole}'")
                        ->success()
                        ->send();
                }
            }

            return $data;
        });
    }

    protected function afterSave(): void
    {
        UserResource::handleStallAssignment($this->record, $this->data);
    }
}