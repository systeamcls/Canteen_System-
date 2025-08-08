<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Update payment_method enum to include 'online'
            $table->dropColumn('payment_method');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'e-wallet', 'online'])->after('total_amount');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'e-wallet'])->after('total_amount');
        });
    }
};