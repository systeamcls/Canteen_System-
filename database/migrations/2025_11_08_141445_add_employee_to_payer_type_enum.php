<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE order_groups MODIFY COLUMN payer_type ENUM('guest', 'user', 'employee') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE order_groups MODIFY COLUMN payer_type ENUM('guest', 'user') NOT NULL");
    }
};