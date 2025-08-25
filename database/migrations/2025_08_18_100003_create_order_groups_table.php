<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_groups', function (Blueprint $table) {
            $table->id();
            $table->enum('payer_type', ['guest', 'user']);
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('guest_token')->nullable()->index();
            $table->enum('payment_method', ['online', 'onsite']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->integer('amount_total'); // in cents
            $table->string('currency', 3)->default('PHP');
            $table->json('billing_contact'); // {name, email, phone}
            $table->string('payment_provider')->nullable();
            $table->string('provider_intent_id')->nullable();
            $table->text('provider_client_secret')->nullable();
            $table->json('cart_snapshot')->nullable(); // frozen cart at checkout
            $table->timestamps();

            $table->index(['payment_status', 'created_at']);
            $table->index(['payer_type', 'user_id']);
            $table->index('provider_intent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_groups');
    }
};