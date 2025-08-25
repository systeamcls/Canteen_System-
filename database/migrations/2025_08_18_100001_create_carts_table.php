<?php

// 📁 database/migrations/2025_08_18_100001_create_carts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('guest_token')->nullable()->index();
            $table->timestamps();

            // Ensure either user_id or guest_token is present
            $table->index(['user_id', 'guest_token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};