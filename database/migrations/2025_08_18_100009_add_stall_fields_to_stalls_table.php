<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            if (!Schema::hasColumn('stalls', 'owner_id')) {
                $table->foreignId('owner_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            }
            
            if (!Schema::hasColumn('stalls', 'is_active')) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('description');
            }
            
            if (!Schema::hasColumn('stalls', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)
                    ->default(15.00)
                    ->after('is_active'); // default 15%
            }
        });

        // ✅ Check if the index exists using INFORMATION_SCHEMA
        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'stalls')
            ->where('index_name', 'stalls_owner_id_is_active_index')
            ->exists();

        if (!$indexExists) {
            Schema::table('stalls', function (Blueprint $table) {
                $table->index(['owner_id', 'is_active'], 'stalls_owner_id_is_active_index');
            });
        }
    }

    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            if (Schema::hasColumn('stalls', 'owner_id')) {
                $table->dropForeign(['owner_id']);
            }

            if (Schema::hasColumn('stalls', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('stalls', 'commission_rate')) {
                $table->dropColumn('commission_rate');
            }

            $table->dropIndex('stalls_owner_id_is_active_index');
        });
    }
};
