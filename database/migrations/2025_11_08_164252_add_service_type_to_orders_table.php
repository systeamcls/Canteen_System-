<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add service_type column after order_type
            if (!Schema::hasColumn('orders', 'service_type')) {
                $table->enum('service_type', ['dine-in', 'take-away', 'delivery', 'pickup'])
                    ->nullable()
                    ->after('order_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'service_type')) {
                $table->dropColumn('service_type');
            }
        });
    }
};