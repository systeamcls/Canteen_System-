<?php

namespace App\Console\Commands;

use App\Models\Stall;
use App\Models\RentalPayment;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateDailyRentals extends Command
{
    protected $signature = 'rentals:generate-daily 
                            {--date= : Specific date (Y-m-d format)}
                            {--stall= : Specific stall ID}
                            {--force : Force generation}
                            {--dry-run : Show what would be generated}';

    protected $description = 'Generate daily rental payment records for active stalls';

    public function handle(): int
    {
        $targetDate = $this->option('date') 
            ? Carbon::parse($this->option('date'))
            : Carbon::today();
            
        $stallId = $this->option('stall');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        $this->info("🏪 Generating daily rentals for: " . $targetDate->format('Y-m-d'));
        
        if ($dryRun) {
            $this->warn("🔍 DRY RUN MODE - No records will be created");
        }

        // Get active stalls with tenants
        $query = Stall::query()
            ->where('is_active', true)
            ->whereNotNull('tenant_id')
            ->with(['tenant:id,name']);

        if ($stallId) {
            $query->where('id', $stallId);
        }

        $activeStalls = $query->get();
        
        if ($activeStalls->isEmpty()) {
            $this->warn("⚠️  No active stalls with tenants found");
            return Command::SUCCESS;
        }

        $stats = [
            'generated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'total_amount' => 0
        ];

        $this->withProgressBar($activeStalls, function ($stall) use ($targetDate, $force, $dryRun, &$stats) {
            try {
                $result = $this->processStall($stall, $targetDate, $force, $dryRun);
                $stats[$result['status']]++;
                if (isset($result['amount'])) {
                    $stats['total_amount'] += $result['amount'];
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Error processing stall {$stall->id}: " . $e->getMessage());
                $this->newLine();
                $this->error("❌ Error processing {$stall->name}: " . $e->getMessage());
            }
        });

        $this->newLine(2);
        $this->displayResults($stats, $dryRun);

        return $stats['errors'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function processStall(Stall $stall, Carbon $targetDate, bool $force, bool $dryRun): array
    {
        // Check existing payment
        $existingPayment = RentalPayment::where('stall_id', $stall->id)
            ->whereDate('period_start', $targetDate)
            ->whereDate('period_end', $targetDate)
            ->first();

        if ($existingPayment && !$force) {
            return ['status' => 'skipped', 'reason' => 'exists'];
        }

        if ($dryRun) {
            return [
                'status' => 'generated',
                'amount' => $stall->rental_fee,
                'dry_run' => true
            ];
        }

        // Delete existing if force mode
        if ($existingPayment && $force) {
            $existingPayment->delete();
        }

        // Create new rental payment
        $rentalPayment = RentalPayment::create([
            'stall_id' => $stall->id,
            'tenant_id' => $stall->tenant_id,
            'amount' => $stall->rental_fee,
            'period_start' => $targetDate,
            'period_end' => $targetDate,
            'due_date' => $targetDate->copy()->addDay(),
            'status' => 'pending',
            'notes' => 'Auto-generated daily rental payment - ' . $targetDate->format('Y-m-d'),
        ]);

        return [
            'status' => 'generated',
            'amount' => $rentalPayment->amount,
            'id' => $rentalPayment->id
        ];
    }

    private function displayResults(array $stats, bool $dryRun): void
    {
        $this->info("📊 Generation Summary:");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Generated', $stats['generated']],
                ['Skipped', $stats['skipped']],
                ['Errors', $stats['errors']],
                ['Total Amount', '₱' . number_format($stats['total_amount'], 2)],
            ]
        );

        if ($dryRun) {
            $this->warn("🔍 This was a dry run - no records were actually created");
        } elseif ($stats['generated'] > 0) {
            $this->info("✅ Successfully generated {$stats['generated']} rental payment records");
        }
    }
}