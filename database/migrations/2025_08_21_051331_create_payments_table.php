<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('stall_id')->nullable()->constrained('stalls')->onDelete('set null'); // Assuming vendors table is your stalls
            $table->enum('payment_method', ['gcash', 'paymaya', 'card', 'cash']);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'succeeded', 'failed', 'refunded'])->default('pending');
            $table->string('provider')->default('paymongo'); // 'paymongo', 'manual', etc.
            $table->string('provider_payment_id')->nullable(); // PayMongo payment intent ID
            $table->string('provider_source_id')->nullable(); // PayMongo source ID (for GCash/PayMaya)
            $table->text('receipt_url')->nullable();
            $table->json('provider_response')->nullable(); // Store full API response for debugging
            $table->text('notes')->nullable(); // Admin notes for cash payments
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'payment_method']);
            $table->index(['user_id', 'status']);
            $table->index(['stall_id', 'created_at']);
            $table->index('provider_payment_id');
            $table->index(['created_at', 'status']); // For analytics
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};