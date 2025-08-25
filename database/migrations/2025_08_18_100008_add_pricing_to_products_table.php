<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price')) {
                $table->integer('price')->after('description'); // in cents
            }
            
            if (!Schema::hasColumn('products', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('price');
            }
            
            if (!Schema::hasColumn('products', 'stall_id')) {
                $table->foreignId('stall_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->cascadeOnDelete();
            }
        });

        // ✅ Check if index exists using INFORMATION_SCHEMA (no Doctrine needed)
        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'products')
            ->where('index_name', 'products_stall_id_is_available_index')
            ->exists();

        if (!$indexExists) {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['stall_id', 'is_available'], 'products_stall_id_is_available_index');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'stall_id')) {
                $table->dropForeign(['stall_id']);
            }

            $table->dropColumn(['price', 'is_available', 'stall_id']);
            $table->dropIndex('products_stall_id_is_available_index');
        });
    }
};
