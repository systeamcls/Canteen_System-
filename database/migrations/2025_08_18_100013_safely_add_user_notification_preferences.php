<?php

// 📁 database/migrations/2025_08_18_100013_safely_add_user_notification_preferences.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add notification preferences if not exists
            if (!Schema::hasColumn('users', 'preferred_notification_channel')) {
                $table->enum('preferred_notification_channel', ['email', 'sms', 'both'])
                      ->default('email')
                      ->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'preferred_notification_channel')) {
                $table->dropColumn('preferred_notification_channel');
            }
        });
    }
};