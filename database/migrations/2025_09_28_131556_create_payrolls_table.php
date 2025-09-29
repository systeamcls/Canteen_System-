<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('week_start_date');
            $table->date('week_end_date');
            $table->integer('days_worked')->default(0);
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('daily_rate', 8, 2);
            $table->decimal('gross_pay', 8, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'week_start_date']);
            $table->index(['status', 'week_start_date']);
            $table->unique(['user_id', 'week_start_date']); // Prevent duplicate weekly payrolls
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};