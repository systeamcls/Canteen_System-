<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->string('cover_photo')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->dropColumn('cover_photo');
        });
    }
};