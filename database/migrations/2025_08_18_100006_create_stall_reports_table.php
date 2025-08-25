<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stall_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stall_id')->constrained()->cascadeOnDelete();
            $table->date('report_date');
            $table->integer('gross_sales')->default(0); // in cents
            $table->decimal('commission_rate', 5, 2)->default(0.00); // percentage (e.g., 15.50)
            $table->integer('commission_amount')->default(0); // in cents
            $table->integer('net_sales')->default(0); // in cents
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['stall_id', 'report_date']);
            $table->index(['report_date', 'is_paid']);
            $table->index(['stall_id', 'is_paid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stall_reports');
    }
};