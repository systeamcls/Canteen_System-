<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_type')->default('pickup')->after('status');
            // Values: 'delivery' or 'pickup'
            
            $table->string('delivery_time')->default('now')->after('delivery_type');
            // Values: 'now' or 'scheduled'
            
            $table->dateTime('scheduled_datetime')->nullable()->after('delivery_time');
            // For pre-orders: date + time
            
            $table->text('delivery_address')->nullable()->after('scheduled_datetime');
            // For delivery orders only
            
            $table->dateTime('estimated_ready_time')->nullable()->after('delivery_address');
            // Calculated ready time (now + 30 mins)
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_type',
                'delivery_time',
                'scheduled_datetime',
                'delivery_address',
                'estimated_ready_time'
            ]);
        });
    }
};