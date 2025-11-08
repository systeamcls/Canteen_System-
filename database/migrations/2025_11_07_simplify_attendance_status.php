<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing 'late' and 'half_day' records to 'present'
        DB::table('attendance_records')
            ->whereIn('status', ['late', 'half_day'])
            ->update(['status' => 'present']);

        // Modify the enum to only have 'present' and 'absent'
        DB::statement("ALTER TABLE attendance_records MODIFY COLUMN status ENUM('present', 'absent') NOT NULL DEFAULT 'present'");
    }

    public function down(): void
    {
        // Restore the original enum
        DB::statement("ALTER TABLE attendance_records MODIFY COLUMN status ENUM('present', 'absent', 'late', 'half_day') NOT NULL DEFAULT 'present'");
    }
};