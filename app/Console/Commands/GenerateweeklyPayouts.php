<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WeeklyPayout;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateWeeklyPayouts extends Command
{
    protected $signature = 'payouts:generate-weekly {--week-start=}';
    protected $description = 'Generate weekly payouts for all active staff';

    public function handle()
    {
        $weekStart = $this->option('week-start') 
            ? Carbon::parse($this->option('week-start'))->startOfWeek()
            : Carbon::now()->subWeek()->startOfWeek(); // Previous week

        $this->info("Generating payouts for week: {$weekStart->format('M d, Y')}");

        $staff = User::where('is_active', true)
            ->where('is_staff', true)
            ->get();

        $count = 0;
        foreach ($staff as $employee) {
            WeeklyPayout::generateForWeek($employee, $weekStart);
            $count++;
        }

        $this->info("✅ Generated {$count} weekly payouts!");
        return 0;
    }
}