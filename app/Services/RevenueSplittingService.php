<?php

namespace App\Services;

use App\Models\OrderGroup;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RevenueSplittingService
{
    /**
     * Split revenue from order group across stalls
     */
    public function splitRevenue(OrderGroup $orderGroup): bool
    {
        try {
            DB::transaction(function () use ($orderGroup) {
                // Get the main payment
                $mainPayment = $orderGroup->getMainPayment();
                
                if (!$mainPayment || !$mainPayment->isSuccessful()) {
                    throw new \Exception('No successful payment found for order group');
                }

                // Group orders by vendor/stall
                $ordersByStall = $orderGroup->orders()
                    ->with('vendor')
                    ->get()
                    ->groupBy('vendor_id');

                foreach ($ordersByStall as $stallId => $stallOrders) {
                    $stallTotal = $stallOrders->sum('total_amount');
                    
                    // Convert from centavos if needed
                    if ($stallTotal > 1000) {
                        $stallTotal = $stallTotal / 100;
                    }

                    // Create payment record for this stall
                    $stallPayment = Payment::create([
                        'order_group_id' => $orderGroup->id,
                        'user_id' => $orderGroup->user_id,
                        'stall_id' => $stallId,
                        'payment_method' => $mainPayment->payment_method,
                        'amount' => $stallTotal,
                        'status' => 'succeeded',
                        'provider' => 'split_revenue',
                        'paid_at' => $mainPayment->paid_at,
                        'notes' => "Revenue split from payment #{$mainPayment->id}",
                        'provider_response' => [
                            'parent_payment_id' => $mainPayment->id,
                            'stall_order_count' => $stallOrders->count(),
                            'split_timestamp' => now()->toISOString(),
                        ],
                    ]);

                    Log::info('Revenue split created for stall', [
                        'order_group_id' => $orderGroup->id,
                        'stall_id' => $stallId,
                        'stall_payment_id' => $stallPayment->id,
                        'amount' => $stallTotal,
                        'order_count' => $stallOrders->count(),
                    ]);
                }

                // Mark main payment as processed for revenue splitting
                $mainPayment->update([
                    'provider_response' => array_merge(
                        $mainPayment->provider_response ?? [],
                        [
                            'revenue_split_at' => now()->toISOString(),
                            'stalls_count' => $ordersByStall->count(),
                        ]
                    ),
                ]);
            });

            return true;

        } catch (\Exception $e) {
            Log::error('Revenue splitting failed', [
                'order_group_id' => $orderGroup->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Get revenue summary for a stall
     */
    public function getStallRevenueSummary(int $stallId, ?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = Payment::where('stall_id', $stallId)->successful();

        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('paid_at', '<=', $endDate);
        }

        $payments = $query->get();

        $summary = [
            'total_revenue' => $payments->sum('amount'),
            'total_orders' => $payments->count(),
            'average_order_value' => $payments->count() > 0 ? $payments->sum('amount') / $payments->count() : 0,
            'revenue_by_method' => $payments->groupBy('payment_method')->map->sum('amount'),
            'daily_revenue' => $payments->groupBy(function ($payment) {
                return $payment->paid_at->format('Y-m-d');
            })->map->sum('amount'),
        ];

        return $summary;
    }

    /**
     * Get platform-wide revenue summary
     */
    public function getPlatformRevenueSummary(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        // Get main payments (not split payments)
        $query = Payment::whereIn('provider', ['paymongo', 'manual'])
            ->successful();

        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('paid_at', '<=', $endDate);
        }

        $mainPayments = $query->get();

        // Get stall-specific revenue
        $stallRevenueQuery = Payment::where('provider', 'split_revenue')
            ->successful();

        if ($startDate) {
            $stallRevenueQuery->where('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $stallRevenueQuery->where('paid_at', '<=', $endDate);
        }

        $stallPayments = $stallRevenueQuery->with('stall')->get();

        return [
            'platform_total' => $mainPayments->sum('amount'),
            'total_orders' => $mainPayments->count(),
            'revenue_by_method' => $mainPayments->groupBy('payment_method')->map->sum('amount'),
            'revenue_by_stall' => $stallPayments->groupBy('stall_id')->map->sum('amount'),
            'top_performing_stalls' => $stallPayments->groupBy('stall_id')
                ->map->sum('amount')
                ->sortDesc()
                ->take(10),
            'daily_revenue' => $mainPayments->groupBy(function ($payment) {
                return $payment->paid_at->format('Y-m-d');
            })->map->sum('amount'),
        ];
    }

    /**
     * Check if revenue has been split for order group
     */
    public function isRevenueSplit(OrderGroup $orderGroup): bool
    {
        return Payment::where('order_group_id', $orderGroup->id)
            ->where('provider', 'split_revenue')
            ->exists();
    }
}