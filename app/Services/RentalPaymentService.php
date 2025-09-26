<?php

namespace App\Services;

use App\Models\Stall;
use App\Models\RentalPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RentalPaymentService
{
    public function generateDailyRentals(?Carbon $date = null, array $options = []): array
    {
        $date = $date ?? Carbon::today();
        
        $results = [
            'date' => $date->format('Y-m-d'),
            'generated' => 0,
            'skipped' => 0,
            'errors' => [],
            'total_amount' => 0,
            'payments' => []
        ];

        $stalls = $this->getEligibleStalls($options);

        DB::transaction(function () use ($stalls, $date, $options, &$results) {
            foreach ($stalls as $stall) {
                try {
                    $result = $this->createRentalPaymentForStall($stall, $date, $options);
                    
                    if ($result['created']) {
                        $results['generated']++;
                        $results['total_amount'] += $result['amount'];
                        $results['payments'][] = $result['payment'];
                    } else {
                        $results['skipped']++;
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'stall' => $stall->name,
                        'stall_id' => $stall->id,
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error("Failed to generate rental for stall {$stall->id}", [
                        'stall_name' => $stall->name,
                        'date' => $date->format('Y-m-d'),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });

        return $results;
    }

    public function generateBulkRentals(Carbon $startDate, Carbon $endDate, array $options = []): array
    {
        $results = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => 0,
            'generated' => 0,
            'skipped' => 0,
            'errors' => [],
            'daily_results' => []
        ];

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Skip weekends if option is set
            if (($options['skip_weekends'] ?? false) && $currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            $dayResults = $this->generateDailyRentals($currentDate, $options);
            
            $results['total_days']++;
            $results['generated'] += $dayResults['generated'];
            $results['skipped'] += $dayResults['skipped'];
            $results['errors'] = array_merge($results['errors'], $dayResults['errors']);
            $results['daily_results'][$currentDate->format('Y-m-d')] = $dayResults;

            $currentDate->addDay();
        }

        return $results;
    }

    public function markOverduePayments(): int
    {
        $updated = RentalPayment::where('status', 'pending')
            ->where('due_date', '<', Carbon::now())
            ->update(['status' => 'overdue']);

        if ($updated > 0) {
            Log::info("Marked {$updated} rental payments as overdue");
        }

        return $updated;
    }

    private function getEligibleStalls(array $options = []): Collection
    {
        $query = Stall::query()
            ->where('is_active', true)
            ->whereNotNull('tenant_id')
            ->with(['tenant:id,name,email']);

        if (isset($options['stall_ids'])) {
            $query->whereIn('id', $options['stall_ids']);
        }

        if (isset($options['tenant_ids'])) {
            $query->whereIn('tenant_id', $options['tenant_ids']);
        }

        return $query->get();
    }

    private function createRentalPaymentForStall(Stall $stall, Carbon $date, array $options): array
    {
        // Check if payment already exists
        $existingPayment = RentalPayment::where('stall_id', $stall->id)
            ->whereDate('period_start', $date)
            ->whereDate('period_end', $date)
            ->first();

        if ($existingPayment && !($options['force'] ?? false)) {
            return ['created' => false, 'reason' => 'exists'];
        }

        // Delete existing if force mode
        if ($existingPayment && ($options['force'] ?? false)) {
            $existingPayment->delete();
        }

        // Calculate due date
        $dueDate = $this->calculateDueDate($date, $options);

        // Create rental payment
        $payment = RentalPayment::create([
            'stall_id' => $stall->id,
            'tenant_id' => $stall->tenant_id,
            'amount' => $stall->rental_fee,
            'period_start' => $date,
            'period_end' => $date,
            'due_date' => $dueDate,
            'status' => 'pending',
            'notes' => $options['notes'] ?? 'Auto-generated daily rental payment',
        ]);

        return [
            'created' => true,
            'payment' => $payment,
            'amount' => $payment->amount
        ];
    }

    private function calculateDueDate(Carbon $date, array $options): Carbon
    {
        $dueDate = $date->copy()->addDay(); // Default: due next day
        
        if (isset($options['due_days'])) {
            $dueDate = $date->copy()->addDays($options['due_days']);
        }

        // Skip weekends if option is set
        if ($options['skip_weekend_due_dates'] ?? false) {
            while ($dueDate->isWeekend()) {
                $dueDate->addDay();
            }
        }

        return $dueDate;
    }
}