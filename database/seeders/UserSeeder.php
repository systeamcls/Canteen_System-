<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
        ]);
        $admin->syncRoles(['admin']);

        // Tenant
        $tenant = User::firstOrCreate([
            'email' => 'tenant@example.com',
        ], [
            'name' => 'Sample Tenant',
            'password' => bcrypt('password'),
        ]);
        $tenant->syncRoles(['tenant']);

        // Cashier
        $cashier = User::firstOrCreate([
            'email' => 'cashier@example.com',
        ], [
            'name' => 'Sample Cashier',
            'password' => bcrypt('password'),
        ]);
        $cashier->syncRoles(['cashier']);

        // Customer
        $customer = User::firstOrCreate([
            'email' => 'customer@example.com',
        ], [
            'name' => 'Sample Customer',
            'password' => bcrypt('password'),
        ]);
        $customer->syncRoles(['customer']);
    }
}
