<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use App\Models\Stall;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): User
    {
        // Remove fields that don't belong to users table
        $assignedStall = $data['assigned_stall'] ?? null;
        $dailyRate = $data['daily_rate'] ?? null;
        unset($data['assigned_stall'], $data['daily_rate']);

        // Ensure boolean fields are properly converted
        $data['is_staff'] = (bool)($data['is_staff'] ?? false);
        $data['is_active'] = (bool)($data['is_active'] ?? true);

        // Create the user
        $user = User::create($data);

        // Handle stall assignment for tenants
        if ($user->type === 'tenant' && $assignedStall) {
            $stall = Stall::find($assignedStall);
            if ($stall) {
                $stall->update(['tenant_id' => $user->id]);
            }
        }

        // Create employee wage record if user is staff
        if ($user->is_staff && $dailyRate) {
            $user->employeeWages()->create([
                'hourly_rate' => $dailyRate / 8,
                'daily_rate' => $dailyRate,
                'pay_type' => 'daily',
                'effective_from' => now()->toDateString(),
                'is_active' => true,
            ]);
        }

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}