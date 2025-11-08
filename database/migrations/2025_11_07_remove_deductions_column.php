<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove deductions column from payrolls
        if (Schema::hasColumn('payrolls', 'deductions')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropColumn('deductions');
            });
        }

        // Remove deductions column from weekly_payouts
        if (Schema::hasColumn('weekly_payouts', 'deductions')) {
            Schema::table('weekly_payouts', function (Blueprint $table) {
                $table->dropColumn('deductions');
            });
        }
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('deductions', 10, 2)->default(0)->after('gross_pay');
        });

        Schema::table('weekly_payouts', function (Blueprint $table) {
            $table->decimal('deductions', 10, 2)->default(0)->after('overtime_pay');
        });
    }
};
