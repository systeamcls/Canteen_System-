<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->after('logo');
            $table->time('opening_time')->nullable()->after('contact_number');
            $table->time('closing_time')->nullable()->after('opening_time');
        });
    }

    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->dropColumn(['contact_number', 'opening_time', 'closing_time']);
        });
    }
};
