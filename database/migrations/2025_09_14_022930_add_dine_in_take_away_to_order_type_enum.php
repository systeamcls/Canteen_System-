<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_type_enum', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('online', 'onsite', 'dine-in', 'take-away')");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_type_enum', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('online', 'onsite')");
        });
    }
};
