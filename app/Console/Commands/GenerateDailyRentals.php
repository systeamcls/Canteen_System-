<?php

namespace App\Console\Commands;

use App\Models\RentalPayment;
use App\Models\Stall;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDailyRentals extends Command
{
    protected $signature = 'rent:generate-daily {--date=}';
    protected $description = 'Generate daily rental payment records for all active tenants';

    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : today();
        
        $this->info("Generating daily rent for: " . $date->format('Y-m-d'));
        
        // Get all active stalls with tenants
        $activeStalls = Stall::where('is_active', true)
            ->whereNotNull('tenant_id')
            ->with('tenant')
            ->get();

        if ($activeStalls->isEmpty()) {
            $this->warn('No active stalls with tenants found.');
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($activeStalls as $stall) {
            // Check if payment record already exists for this date
            $existingPayment = RentalPayment::where('stall_id', $stall->id)
                ->where('tenant_id', $stall->tenant_id)
                ->whereDate('period_start', $date)
                ->whereDate('period_end', $date)
                ->first();

            if ($existingPayment) {
                $this->line("Skipped {$stall->name} - {$stall->tenant->name}: Already exists");
                $skipped++;
                continue;
            }

            // Create daily rental payment record
            RentalPayment::create([
                'stall_id' => $stall->id,
                'tenant_id' => $stall->tenant_id,
                'amount' => $stall->rental_fee,
                'period_start' => $date,
                'period_end' => $date,
                'due_date' => $date, // Due same day
                'status' => 'pending',
                'notes' => 'Auto-generated daily rent for ' . $date->format('M j, Y'),
            ]);

            $this->info("Created: {$stall->name} - {$stall->tenant->name} (₱{$stall->rental_fee})");
            $created++;
        }

        $this->info("\n=== Summary ===");
        $this->info("Created: {$created} payment records");
        $this->info("Skipped: {$skipped} existing records");
        $this->info("Total stalls processed: " . $activeStalls->count());
    }
}