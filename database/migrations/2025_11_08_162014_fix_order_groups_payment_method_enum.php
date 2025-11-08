<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'cash' to order_groups.payment_method ENUM
        DB::statement("ALTER TABLE order_groups MODIFY COLUMN payment_method ENUM('online', 'onsite', 'cash', 'gcash', 'paymaya', 'card') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE order_groups MODIFY COLUMN payment_method ENUM('online', 'onsite') NOT NULL");
    }
};