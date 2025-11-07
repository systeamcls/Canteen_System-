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
        // Add daily_rate to users table
        if (!Schema::hasColumn('users', 'daily_rate')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('daily_rate', 10, 2)->default(500.00)->after('is_staff');
            });
        }

        // Add free_meal_taken to attendance_records table
        if (!Schema::hasColumn('attendance_records', 'free_meal_taken')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->boolean('free_meal_taken')->default(false)->after('status');
            });
        }

        // Create payrolls table if it doesn't exist
        if (!Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('period_start');
                $table->date('period_end');
                $table->integer('days_present')->default(0);
                $table->integer('days_late')->default(0);
                $table->integer('days_half_day')->default(0);
                $table->integer('days_absent')->default(0);
                $table->decimal('total_hours', 6, 2)->default(0);
                $table->decimal('daily_rate', 10, 2);
                $table->decimal('gross_pay', 10, 2);
                $table->decimal('deductions', 10, 2)->default(0);
                $table->decimal('net_pay', 10, 2);
                $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
                $table->date('paid_date')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->unique(['user_id', 'period_start', 'period_end']);
                $table->index(['period_start', 'period_end']);
                $table->index(['status', 'paid_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'daily_rate')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('daily_rate');
            });
        }

        if (Schema::hasColumn('attendance_records', 'free_meal_taken')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropColumn('free_meal_taken');
            });
        }

        Schema::dropIfExists('payrolls');
    }
};
