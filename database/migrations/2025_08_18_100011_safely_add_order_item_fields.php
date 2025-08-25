<?php
// 📁 database/migrations/2025_08_18_100011_safely_add_order_item_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Only add fields that don't exist
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_id')->comment('Denormalized product name for history');
            }
            
            if (!Schema::hasColumn('order_items', 'line_total')) {
                $table->integer('line_total')->nullable()->after('subtotal')->comment('Line total in cents');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $columns = ['product_name', 'line_total'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};