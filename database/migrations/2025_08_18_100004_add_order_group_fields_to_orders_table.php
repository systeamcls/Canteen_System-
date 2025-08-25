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
        // Add the new columns if they don’t exist yet
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_group_id')) {
                $table->unsignedBigInteger('order_group_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('orders', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('order_group_id');
            }
        });

        // Add indexes safely
        if (!$this->indexExists('orders', 'orders_order_vendor_idx')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['order_group_id', 'vendor_id'], 'orders_order_vendor_idx');
            });
        }

        if (!$this->indexExists('orders', 'orders_vendor_status_idx')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['vendor_id', 'status'], 'orders_vendor_status_idx');
            });
        }

        if (!$this->indexExists('orders', 'orders_status_created_idx')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['status', 'created_at'], 'orders_status_created_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_group_id')) {
                $table->dropColumn('order_group_id');
            }

            if (Schema::hasColumn('orders', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }

            $table->dropIndex('orders_order_vendor_idx');
            $table->dropIndex('orders_vendor_status_idx');
            $table->dropIndex('orders_status_created_idx');
        });
    }

    /**
     * Custom indexExists check without Doctrine.
     */
    protected function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
