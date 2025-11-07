<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('week_start_date', 'period_start');
            $table->renameColumn('week_end_date', 'period_end');
            
            // Add missing columns
            $table->integer('days_present')->default(0)->after('days_worked');
            $table->integer('days_late')->default(0)->after('days_present');
            $table->integer('days_half_day')->default(0)->after('days_late');
            $table->integer('days_absent')->default(0)->after('days_half_day');
            $table->decimal('deductions', 10, 2)->default(0)->after('gross_pay');
            $table->date('paid_date')->nullable()->after('paid_at');
            $table->foreignId('generated_by')->nullable()->after('notes')->constrained('users')->onDelete('set null');
            
            // Update net_pay to match model (10,2 instead of 8,2)
            $table->decimal('net_pay', 10, 2)->change();
            $table->decimal('gross_pay', 10, 2)->change();
            $table->decimal('daily_rate', 10, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('period_start', 'week_start_date');
            $table->renameColumn('period_end', 'week_end_date');
            
            $table->dropColumn([
                'days_present',
                'days_late', 
                'days_half_day',
                'days_absent',
                'deductions',
                'paid_date',
                'generated_by'
            ]);
        });
    }
};