<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixUserRoles extends Command
{
    protected $signature = 'users:fix-roles';
    protected $description = 'Fix user roles by syncing type field with Spatie roles';

    public function handle()
    {
        $this->info('Starting to fix user roles...');

        $users = User::all();
        $fixed = 0;

        foreach ($users as $user) {
            $roleMapping = [
                'tenant' => 'tenant',
                'cashier' => 'cashier',
                'staff' => 'customer',
                'admin' => 'admin',
            ];

            if ($user->type && isset($roleMapping[$user->type])) {
                $role = $roleMapping[$user->type];
                
                // Check if user already has correct role
                if (!$user->hasRole($role)) {
                    $user->syncRoles([$role]);
                    $this->info("✓ Fixed: {$user->email} → Role: {$role}");
                    $fixed++;
                } else {
                    $this->comment("- Skipped: {$user->email} (already has correct role)");
                }
            } else {
                // Assign default customer role
                if ($user->roles->isEmpty()) {
                    $user->syncRoles(['customer']);
                    $this->warn("⚠ Default: {$user->email} → Role: customer");
                    $fixed++;
                }
            }
        }

        $this->info("✅ Fixed {$fixed} users successfully!");
    }
}