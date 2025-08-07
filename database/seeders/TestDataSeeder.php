<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Stall;
use App\Models\Product;
use Spatie\Permission\Models\Role;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@lto.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'phone' => '09123456789',
                'type' => 'admin',
                'is_active' => true,
                'can_use_onsite_payment' => true,
            ]
        );
        $admin->assignRole('admin');

        // Create tenant user
        $tenant = User::firstOrCreate(
            ['email' => 'tenant@lto.com'],
            [
                'name' => 'Tenant User',
                'password' => bcrypt('password'),
                'phone' => '09123456788',
                'type' => 'tenant',
                'is_active' => true,
                'can_use_onsite_payment' => true,
            ]
        );
        $tenant->assignRole('tenant');

        // Create customer user
        $customer = User::firstOrCreate(
            ['email' => 'customer@lto.com'],
            [
                'name' => 'Customer User',
                'password' => bcrypt('password'),
                'phone' => '09123456787',
                'type' => 'customer',
                'is_active' => true,
                'can_use_onsite_payment' => true,
            ]
        );
        $customer->assignRole('customer');

        // Create test stalls
        $stall1 = Stall::firstOrCreate(
            ['name' => 'Fresh Meals Corner'],
            [
                'description' => 'Hot and fresh Filipino meals',
                'user_id' => $tenant->id,
                'location' => 'Section A',
                'rental_fee' => 2500.00,
                'is_active' => true,
            ]
        );

        $stall2 = Stall::firstOrCreate(
            ['name' => 'Snack Attack'],
            [
                'description' => 'Quick snacks and instant foods',
                'user_id' => $admin->id,
                'location' => 'Section B',
                'rental_fee' => 2000.00,
                'is_active' => true,
            ]
        );

        $stall3 = Stall::firstOrCreate(
            ['name' => 'Beverage Bar'],
            [
                'description' => 'Refreshing drinks and coffee',
                'user_id' => $admin->id,
                'location' => 'Section C',
                'rental_fee' => 1800.00,
                'is_active' => true,
            ]
        );

        // Create test products
        $products = [
            // Fresh Meals Corner
            ['name' => 'Adobo Rice Bowl', 'price' => 85.00, 'stall_id' => $stall1->id],
            ['name' => 'Fried Chicken Rice', 'price' => 95.00, 'stall_id' => $stall1->id],
            ['name' => 'Pork Sisig', 'price' => 90.00, 'stall_id' => $stall1->id],
            
            // Snack Attack
            ['name' => 'Instant Pancit Canton', 'price' => 25.00, 'stall_id' => $stall2->id],
            ['name' => 'Cup Noodles', 'price' => 20.00, 'stall_id' => $stall2->id],
            ['name' => 'Sandwich', 'price' => 45.00, 'stall_id' => $stall2->id],
            
            // Beverage Bar
            ['name' => 'Iced Coffee', 'price' => 35.00, 'stall_id' => $stall3->id],
            ['name' => 'Fresh Juice', 'price' => 30.00, 'stall_id' => $stall3->id],
            ['name' => 'Soft Drinks', 'price' => 15.00, 'stall_id' => $stall3->id],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name'], 'stall_id' => $productData['stall_id']],
                [
                    'description' => 'Delicious ' . $productData['name'],
                    'price' => $productData['price'],
                    'is_available' => true,
                ]
            );
        }
    }
}
