<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $tenantRole = Role::firstOrCreate(['name' => 'tenant']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create permissions
        $permissions = [
            'manage_stalls',
            'manage_products',
            'manage_orders',
            'manage_users',
            'manage_rental_payments',
            'view_reports',
            'handle_cashier_orders',
            'use_onsite_payment',
            'use_online_payment',
            'browse_as_guest',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->syncPermissions([
            'manage_stalls',
            'manage_products', 
            'manage_orders',
            'manage_users',
            'manage_rental_payments',
            'view_reports',
            'use_onsite_payment',
            'use_online_payment',
        ]);

        $tenantRole->syncPermissions([
            'manage_products',
            'manage_orders',
            'view_reports',
            'use_onsite_payment',
            'use_online_payment',
        ]);

        $cashierRole->syncPermissions([
            'handle_cashier_orders',
            'use_onsite_payment',
            'use_online_payment',
        ]);

        $customerRole->syncPermissions([
            'use_onsite_payment',
            'use_online_payment',
        ]);
    }
}
