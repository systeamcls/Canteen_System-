<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Admin permissions
            'manage_tenants',
            'manage_staff',
            'view_all_orders',
            'manage_expenses',
            'view_reports',
            'manage_rental',
            
            // Tenant permissions
            'manage_products',
            'manage_orders',
            'view_reviews',
            'view_earnings',
            
            // Cashier permissions
            'process_orders',
            'handle_payments',
            
            // Customer permissions
            'place_orders',
            'write_reviews',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign created permissions
        Role::create(['name' => 'admin'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'tenant'])
            ->givePermissionTo([
                'manage_products',
                'manage_orders',
                'view_reviews',
                'view_earnings',
            ]);

        Role::create(['name' => 'cashier'])
            ->givePermissionTo([
                'process_orders',
                'handle_payments',
            ]);

        Role::create(['name' => 'customer'])
            ->givePermissionTo([
                'place_orders',
                'write_reviews',
            ]);
    }
}