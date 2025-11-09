<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use App\Models\Stall;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): User
    {
        // Use database transaction for data integrity
        return DB::transaction(function () use ($data) {
            // Extract special fields
            $assignedStall = $data['assigned_stall'] ?? null;
            $dailyRate = $data['daily_rate'] ?? null;
            $userType = $data['type'] ?? 'customer';
            
            // Remove non-user-table fields
            unset($data['assigned_stall'], $data['daily_rate']);

            // Ensure boolean fields are properly converted
            $data['is_staff'] = (bool)($data['is_staff'] ?? false);
            $data['is_active'] = (bool)($data['is_active'] ?? true);

            // Create the user
            $user = User::create($data);

            // 🔥 Auto-verify admin-created accounts
            $user->update(['email_verified_at' => now()]);


            // 🔐 SECURE: Assign Spatie role based on type
            $this->assignSecureRole($user, $userType);

            // Handle stall assignment for tenants
            if ($user->hasRole('tenant') && $assignedStall) {
                $this->assignStallToTenant($user, $assignedStall);
            }

            // Create employee wage record if user is staff
            if ($user->is_staff && $dailyRate) {
                $this->createEmployeeWage($user, $dailyRate);
            }

            // Log the creation
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'role' => $user->getPrimaryRole(),
                    'type' => $userType,
                ])
                ->log('User created by admin');

            // Send success notification
            Notification::make()
                ->title('User Created Successfully')
                ->body("User '{$user->name}' created with role: {$user->getPrimaryRoleLabel()}")
                ->success()
                ->send();

            return $user;
        });
    }

    /**
     * 🔐 Assign role securely using Spatie
     */
    protected function assignSecureRole(User $user, ?string $type): void
    {
        $roleMapping = [
            'tenant' => 'tenant',
            'cashier' => 'cashier',
            'staff' => 'customer',
        ];

        if ($type && isset($roleMapping[$type])) {
            // Use syncRoles to ensure only one role is assigned
            $user->syncRoles([$roleMapping[$type]]);
        } else {
            // Default role if type is not recognized
            $user->syncRoles(['customer']);
            
            Notification::make()
                ->title('Default Role Assigned')
                ->body('User was assigned default customer role')
                ->warning()
                ->send();
        }
    }

    /**
     * Assign stall to tenant
     */
    protected function assignStallToTenant(User $user, int $stallId): void
    {
        $stall = Stall::find($stallId);
        
        if ($stall) {
            // Clear any previous tenant assignment
            Stall::where('tenant_id', $user->id)->update(['tenant_id' => null]);
            
            // Assign new stall
            $stall->update(['tenant_id' => $user->id]);
            
            Notification::make()
                ->title('Stall Assigned')
                ->body("Tenant assigned to stall: {$stall->name}")
                ->success()
                ->send();
        }
    }

    /**
     * Create employee wage record
     */
    protected function createEmployeeWage(User $user, float $dailyRate): void
    {
        $user->employeeWages()->create([
            'hourly_rate' => $dailyRate / 8,
            'daily_rate' => $dailyRate,
            'pay_type' => 'daily',
            'effective_from' => now()->toDateString(),
            'is_active' => true,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}