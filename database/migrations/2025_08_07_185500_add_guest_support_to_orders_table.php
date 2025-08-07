<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('guest_token')->nullable()->after('user_id');
            $table->text('special_instructions')->nullable()->after('order_type');
            $table->string('user_type')->default('employee')->after('special_instructions'); // 'guest' or 'employee'
            $table->json('guest_details')->nullable()->after('user_type'); // Store guest info if needed
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['guest_token', 'special_instructions', 'user_type', 'guest_details']);
        });
    }
};