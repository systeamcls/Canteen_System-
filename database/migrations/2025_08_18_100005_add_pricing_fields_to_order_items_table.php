<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'unit_price')) {
                $table->integer('unit_price')->after('quantity'); // in cents
            }
            
            if (!Schema::hasColumn('order_items', 'line_total')) {
                $table->integer('line_total')->after('unit_price'); // in cents
            }
            
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->after('product_id'); // denormalized for history
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'line_total', 'product_name']);
        });
    }
};