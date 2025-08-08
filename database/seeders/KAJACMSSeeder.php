<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Stall;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class KAJACMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles first
        $roles = ['admin', 'tenant', 'cashier', 'customer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create Admin (Concessionaire) - Also runs their own food stall
        $admin = User::firstOrCreate([
            'email' => 'admin@kajacms.com'
        ], [
            'name' => 'KAJACMS Admin',
            'password' => Hash::make('password'),
            'phone' => '+63-912-345-6789',
            'type' => 'employee',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create Cashier
        $cashier = User::firstOrCreate([
            'email' => 'cashier@kajacms.com'
        ], [
            'name' => 'KAJACMS Cashier',
            'password' => Hash::make('password'),
            'phone' => '+63-912-345-6790',
            'type' => 'employee',
            'is_active' => true,
        ]);
        $cashier->assignRole('cashier');

        // Create Stalls (7 tenant stalls + 1 admin stall)
        $stalls = [
            [
                'name' => 'KAJACMS Main Kitchen',
                'description' => 'Official canteen managed by KAJACMS administration',
                'location' => 'Main Hall',
                'rental_fee' => 0.00, // No rental fee for admin stall
                'is_active' => true,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Pizza Corner',
                'description' => 'Authentic Italian pizzas with fresh ingredients',
                'location' => 'Stall #1',
                'rental_fee' => 5000.00,
                'is_active' => true,
                'user_id' => $admin->id, // Temporarily assign to admin, will be reassigned
            ],
            [
                'name' => 'Fast Bite',
                'description' => 'Quick and delicious fast food favorites',
                'location' => 'Stall #2',
                'rental_fee' => 4500.00,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Noodle House',
                'description' => 'Traditional and modern noodle dishes',
                'location' => 'Stall #3',
                'rental_fee' => 4000.00,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Sweet Treats',
                'description' => 'Desserts, cakes, and sweet delights',
                'location' => 'Stall #4',
                'rental_fee' => 3500.00,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Ocean Fresh',
                'description' => 'Fresh seafood and marine delicacies',
                'location' => 'Stall #5',
                'rental_fee' => 6000.00,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Sushi Master',
                'description' => 'Authentic Japanese sushi and sashimi',
                'location' => 'Stall #6',
                'rental_fee' => 7000.00,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
            [
                'name' => 'Ramen Bar',
                'description' => 'Rich, flavorful ramen bowls',
                'location' => 'Stall #7',
                'rental_fee' => 5500.00,
                'is_active' => true,
                'user_id' => $admin->id,
            ],
        ];

        $createdStalls = [];
        foreach ($stalls as $stallData) {
            $stall = Stall::firstOrCreate([
                'name' => $stallData['name']
            ], $stallData);
            $createdStalls[] = $stall;
        }

        // Create Tenant Users for stalls 2-8
        $tenants = [
            ['name' => 'Mario Giuseppe', 'email' => 'mario@pizzacorner.com', 'stall_index' => 1],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@fastbite.com', 'stall_index' => 2],
            ['name' => 'Chen Wei', 'email' => 'chen@noodlehouse.com', 'stall_index' => 3],
            ['name' => 'Maria Santos', 'email' => 'maria@sweettreats.com', 'stall_index' => 4],
            ['name' => 'Captain Hook', 'email' => 'hook@oceanfresh.com', 'stall_index' => 5],
            ['name' => 'Tanaka San', 'email' => 'tanaka@sushimaster.com', 'stall_index' => 6],
            ['name' => 'Ramen Master', 'email' => 'master@ramenbar.com', 'stall_index' => 7],
        ];

        foreach ($tenants as $tenantData) {
            $tenant = User::firstOrCreate([
                'email' => $tenantData['email']
            ], [
                'name' => $tenantData['name'],
                'password' => Hash::make('password'),
                'phone' => '+63-912-000-' . str_pad($tenantData['stall_index'] + 100, 4, '0', STR_PAD_LEFT),
                'type' => 'employee',
                'is_active' => true,
            ]);
            $tenant->assignRole('tenant');

            // Assign tenant to their stall
            $createdStalls[$tenantData['stall_index']]->update(['user_id' => $tenant->id]);
        }

        // Create Products for each stall
        $products = [
            // KAJACMS Main Kitchen products
            [
                'stall_id' => $createdStalls[0]->id,
                'products' => [
                    ['name' => 'Classic Adobo Rice', 'description' => 'Traditional Filipino adobo with steamed rice', 'price' => 120.00, 'category' => 'rice'],
                    ['name' => 'Grilled Chicken', 'description' => 'Perfectly grilled chicken with vegetables', 'price' => 150.00, 'category' => 'grilled'],
                    ['name' => 'Mixed Vegetable Curry', 'description' => 'Fresh vegetables in aromatic curry sauce', 'price' => 110.00, 'category' => 'vegetarian'],
                    ['name' => 'Pancit Canton', 'description' => 'Stir-fried noodles with vegetables and meat', 'price' => 95.00, 'category' => 'noodle'],
                ]
            ],
            // Pizza Corner
            [
                'stall_id' => $createdStalls[1]->id,
                'products' => [
                    ['name' => 'Margherita Pizza', 'description' => 'Classic pizza with tomato, mozzarella, and basil', 'price' => 250.00, 'category' => 'pizza'],
                    ['name' => 'Pepperoni Supreme', 'description' => 'Loaded with pepperoni and extra cheese', 'price' => 320.00, 'category' => 'pizza'],
                    ['name' => 'Hawaiian Delight', 'description' => 'Ham, pineapple, and cheese combination', 'price' => 290.00, 'category' => 'pizza'],
                    ['name' => 'Meat Lovers', 'description' => 'Pepperoni, sausage, ham, and bacon', 'price' => 380.00, 'category' => 'pizza'],
                ]
            ],
            // Fast Bite
            [
                'stall_id' => $createdStalls[2]->id,
                'products' => [
                    ['name' => 'Classic Burger', 'description' => 'Beef patty with lettuce, tomato, and cheese', 'price' => 145.00, 'category' => 'fast-food'],
                    ['name' => 'Chicken Sandwich', 'description' => 'Crispy chicken breast with mayo and pickles', 'price' => 135.00, 'category' => 'fast-food'],
                    ['name' => 'French Fries', 'description' => 'Golden crispy potato fries', 'price' => 65.00, 'category' => 'fast-food'],
                    ['name' => 'Fish Fillet Burger', 'description' => 'Crispy fish fillet with tartar sauce', 'price' => 155.00, 'category' => 'fast-food'],
                ]
            ],
            // Noodle House
            [
                'stall_id' => $createdStalls[3]->id,
                'products' => [
                    ['name' => 'Beef Lo Mein', 'description' => 'Soft noodles with tender beef and vegetables', 'price' => 165.00, 'category' => 'noodle'],
                    ['name' => 'Chicken Chow Mein', 'description' => 'Crispy noodles with chicken and mixed vegetables', 'price' => 155.00, 'category' => 'noodle'],
                    ['name' => 'Vegetable Pad Thai', 'description' => 'Thai-style rice noodles with vegetables', 'price' => 140.00, 'category' => 'noodle'],
                    ['name' => 'Spicy Dan Dan Noodles', 'description' => 'Sichuan-style spicy noodles with ground pork', 'price' => 175.00, 'category' => 'noodle'],
                ]
            ],
            // Sweet Treats
            [
                'stall_id' => $createdStalls[4]->id,
                'products' => [
                    ['name' => 'Chocolate Cake Slice', 'description' => 'Rich chocolate cake with chocolate frosting', 'price' => 85.00, 'category' => 'dessert'],
                    ['name' => 'Cheesecake', 'description' => 'Creamy New York style cheesecake', 'price' => 95.00, 'category' => 'dessert'],
                    ['name' => 'Ice Cream Sundae', 'description' => 'Vanilla ice cream with chocolate sauce and nuts', 'price' => 75.00, 'category' => 'dessert'],
                    ['name' => 'Fresh Fruit Tart', 'description' => 'Pastry shell with custard and fresh fruits', 'price' => 110.00, 'category' => 'dessert'],
                ]
            ],
            // Ocean Fresh
            [
                'stall_id' => $createdStalls[5]->id,
                'products' => [
                    ['name' => 'Grilled Salmon', 'description' => 'Fresh salmon fillet grilled to perfection', 'price' => 285.00, 'category' => 'sea-food'],
                    ['name' => 'Fish and Chips', 'description' => 'Beer-battered fish with crispy fries', 'price' => 195.00, 'category' => 'sea-food'],
                    ['name' => 'Shrimp Scampi', 'description' => 'Garlic butter shrimp with pasta', 'price' => 245.00, 'category' => 'sea-food'],
                    ['name' => 'Seafood Paella', 'description' => 'Traditional Spanish rice with mixed seafood', 'price' => 320.00, 'category' => 'sea-food'],
                ]
            ],
            // Sushi Master
            [
                'stall_id' => $createdStalls[6]->id,
                'products' => [
                    ['name' => 'California Roll', 'description' => 'Crab, avocado, and cucumber roll', 'price' => 165.00, 'category' => 'sushi'],
                    ['name' => 'Salmon Nigiri Set', 'description' => '6 pieces of fresh salmon nigiri', 'price' => 225.00, 'category' => 'sushi'],
                    ['name' => 'Rainbow Roll', 'description' => 'California roll topped with assorted fish', 'price' => 285.00, 'category' => 'sushi'],
                    ['name' => 'Sashimi Combo', 'description' => 'Assorted fresh fish sashimi', 'price' => 320.00, 'category' => 'sushi'],
                ]
            ],
            // Ramen Bar
            [
                'stall_id' => $createdStalls[7]->id,
                'products' => [
                    ['name' => 'Tonkotsu Ramen', 'description' => 'Rich pork bone broth with tender chashu', 'price' => 195.00, 'category' => 'ramen'],
                    ['name' => 'Miso Ramen', 'description' => 'Fermented soybean paste broth with vegetables', 'price' => 185.00, 'category' => 'ramen'],
                    ['name' => 'Spicy Tantanmen', 'description' => 'Spicy sesame and miso based ramen', 'price' => 205.00, 'category' => 'ramen'],
                    ['name' => 'Vegetarian Ramen', 'description' => 'Plant-based broth with tofu and vegetables', 'price' => 175.00, 'category' => 'ramen'],
                ]
            ],
        ];

        foreach ($products as $stallProducts) {
            foreach ($stallProducts['products'] as $productData) {
                Product::firstOrCreate([
                    'name' => $productData['name'],
                    'stall_id' => $stallProducts['stall_id']
                ], [
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'category' => $productData['category'],
                    'is_available' => true,
                    'image' => null, // Can be added later
                ]);
            }
        }

        $this->command->info('KAJACMS Multi-Vendor Canteen System seeded successfully!');
        $this->command->info('Admin Login: admin@kajacms.com / password');
        $this->command->info('Cashier Login: cashier@kajacms.com / password');
        $this->command->info('Tenant Logins: mario@pizzacorner.com, sarah@fastbite.com, etc. / password');
    }
}
