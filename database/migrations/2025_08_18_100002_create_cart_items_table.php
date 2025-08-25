<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->integer('unit_price'); // in cents
            $table->integer('line_total'); // in cents
            $table->timestamps();

            $table->unique(['cart_id', 'product_id']);
            $table->index(['cart_id', 'vendor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};