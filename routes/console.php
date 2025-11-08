<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('rent:generate-daily')->dailyAt('06:00');

Schedule::command('payouts:generate-weekly')
    ->weeklyOn(1, '1:00') // Every Monday at 1 AM
    ->timezone('Asia/Manila');
