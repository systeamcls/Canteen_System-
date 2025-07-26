<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Stall;
use App\Models\Product;

class CanteenSeeder extends Seeder
{
    public function run()
    {
        // Create sample users
        $users = [
            [
                'name' => 'Tita Maria',
                'email' => 'tita@lto.gov.ph',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Kuya Jun',
                'email' => 'jun@lto.gov.ph', 
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Ate Rose',
                'email' => 'rose@lto.gov.ph',
                'password' => bcrypt('password'),
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
        }

        // Create sample stalls
        $stalls = [
            [
                'user_id' => 1,
                'name' => 'Tita\'s Kitchen',
                'description' => 'Home-cooked Filipino meals with love. Specializing in traditional dishes that remind you of home.',
                'location' => 'Ground Floor, East Wing',
                'rental_fee' => 15000.00,
                'is_active' => true,
            ],
            [
                'user_id' => 2,
                'name' => 'Grill Master',
                'description' => 'Premium grilled meats and BBQ specialties. Fresh ingredients grilled to perfection.',
                'location' => 'Ground Floor, Center Court',
                'rental_fee' => 18000.00,
                'is_active' => true,
            ],
            [
                'user_id' => 3,
                'name' => 'Snack Corner',
                'description' => 'Quick bites and beverages for busy LTO visitors. Perfect for a quick energy boost.',
                'location' => 'Second Floor, West Wing',
                'rental_fee' => 12000.00,
                'is_active' => true,
            ]
        ];

        foreach ($stalls as $stallData) {
            Stall::create($stallData);
        }

        // Create sample products
        $products = [
            // Tita's Kitchen (Stall 1)
            [
                'stall_id' => 1,
                'name' => 'Chicken Adobo',
                'description' => 'Classic Filipino chicken adobo cooked in soy sauce and vinegar',
                'price' => 85.00,
                'category' => 'fresh-meals',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 1,
                'name' => 'Beef Tapa',
                'description' => 'Tender marinated beef served with garlic rice and egg',
                'price' => 95.00,
                'category' => 'fresh-meals',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 1,
                'name' => 'Fresh Lumpia',
                'description' => 'Fresh spring rolls filled with vegetables and served with sweet sauce',
                'price' => 45.00,
                'category' => 'fresh-meals',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 1,
                'name' => 'Pancit Canton',
                'description' => 'Stir-fried noodles with vegetables and choice of meat',
                'price' => 70.00,
                'category' => 'fresh-meals',
                'image' => null,
                'is_available' => true,
            ],

            // Grill Master (Stall 2)
            [
                'stall_id' => 2,
                'name' => 'Pork Sisig',
                'description' => 'Sizzling pork sisig with onions and chili peppers',
                'price' => 90.00,
                'category' => 'fresh-meals',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 2,
                'name' => 'Grilled Chicken',
                'description' => 'Juicy grilled chicken marinated in special spices',
                'price' => 110.00,
                'category' => 'fresh-meals',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 2,
                'name' => 'BBQ Pork',
                'description' => 'Sweet and savory barbecue pork skewers',
                'price' => 25.00,
                'category' => 'fresh-meals',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 2,
                'name' => 'Grilled Fish',
                'description' => 'Fresh fish grilled with herbs and lemon',
                'price' => 120.00,
                'category' => 'fresh-meals',
                'image' => null,
                'is_available' => true,
            ],

            // Snack Corner (Stall 3)
            [
                'stall_id' => 3,
                'name' => 'Club Sandwich',
                'description' => 'Triple-decker sandwich with chicken, bacon, and vegetables',
                'price' => 65.00,
                'category' => 'sandwiches',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 3,
                'name' => 'Tuna Sandwich',
                'description' => 'Fresh tuna salad sandwich on toasted bread',
                'price' => 55.00,
                'category' => 'sandwiches',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 3,
                'name' => 'Iced Coffee',
                'description' => 'Refreshing iced coffee with milk and sugar',
                'price' => 35.00,
                'category' => 'beverages',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 3,
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice',
                'price' => 40.00,
                'category' => 'beverages',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 3,
                'name' => 'Chocolate Chip Cookies',
                'description' => 'Homemade chocolate chip cookies (3 pieces)',
                'price' => 30.00,
                'category' => 'snacks',
                'image' => null,
                'is_available' => true,
            ],
            [
                'stall_id' => 3,
                'name' => 'Bottled Water',
                'description' => 'Pure drinking water 500ml',
                'price' => 15.00,
                'category' => 'beverages',
                'image' => null,
                'is_available' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}