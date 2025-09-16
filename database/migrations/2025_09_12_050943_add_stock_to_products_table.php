<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->integer('stock_quantity')->default(0)->after('price');
        $table->integer('low_stock_alert')->default(5)->after('stock_quantity');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
             $table->dropColumn(['stock_quantity', 'low_stock_alert']);
        });
    }
};
