<?php

// 📁 database/migrations/2025_08_18_100010_safely_add_order_group_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only add fields that don't exist in your current orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_group_id')) {
                $table->foreignId('order_group_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('orders', 'vendor_id')) {
                $table->foreignId('vendor_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('orders', 'amount_subtotal')) {
                $table->integer('amount_subtotal')
                    ->nullable()
                    ->after('total_amount')
                    ->comment('Amount in cents');
            }

            if (!Schema::hasColumn('orders', 'amount_total')) {
                $table->integer('amount_total')
                    ->nullable()
                    ->after('amount_subtotal')
                    ->comment('Amount in cents');
            }
        });

        // Add indexes if they don't exist
        $this->addIndexIfNotExists('orders', ['order_group_id', 'vendor_id']);
        $this->addIndexIfNotExists('orders', ['vendor_id', 'status']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['order_group_id', 'vendor_id', 'amount_subtotal', 'amount_total'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    if (in_array($column, ['order_group_id', 'vendor_id'])) {
                        // Drop foreign key first if it exists
                        $foreignKeyName = "orders_{$column}_foreign";
                        if ($this->foreignKeyExists('orders', $foreignKeyName)) {
                            $table->dropForeign([$foreignKeyName]);
                        }
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Safely add index if not already present.
     */
    private function addIndexIfNotExists(string $table, array $columns): void
    {
        $indexName = $table . '_' . implode('_', $columns) . '_index';

        if (!$this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($columns) {
                $table->index($columns);
            });
        }
    }

    /**
     * Check if an index exists using information_schema (works in Laravel 12).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = ? AND table_name = ? AND index_name = ?
        ", [$database, $table, $indexName]);

        return $result[0]->count > 0;
    }

    /**
     * Check if a foreign key exists.
     */
    private function foreignKeyExists(string $table, string $foreignKeyName): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.table_constraints
            WHERE table_schema = ? AND table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY'
        ", [$database, $table, $foreignKeyName]);

        return $result[0]->count > 0;
    }
};
