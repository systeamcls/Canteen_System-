<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\OrderGroup;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private PaymentGatewayInterface $paymentGateway
    ) {}

    /**
     * Process payment for order group
     */
    public function processPayment(
        OrderGroup $orderGroup,
        string $paymentMethod,
        array $customerData = []
    ): array {
        try {
            $amount = $orderGroup->amount_total / 100; // Convert from centavos

            if ($paymentMethod === 'cash') {
                return $this->processCashPayment($orderGroup, $amount, $customerData);
            } else {
                return $this->processOnlinePayment($orderGroup, $amount, $paymentMethod, $customerData);
            }

        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_group_id' => $orderGroup->id,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get available payment methods for user type
     */
    public function getAvailablePaymentMethods(string $userType): array
    {
        $onlineMethods = $this->paymentGateway->getSupportedPaymentMethods();

        if ($userType === 'guest') {
            // Guests can only use online payments
            return $onlineMethods;
        } else {
            // Logged-in users can use online + cash
            $onlineMethods['cash'] = [
                'name' => 'Cash on Delivery',
                'description' => 'Pay when you receive your order',
                'icon' => 'cash-icon',
                'type' => 'cash',
            ];

            return $onlineMethods;
        }
    }

    /**
     * Confirm cash payment (for admin/cashier)
     */
    public function confirmCashPayment(Payment $payment, ?string $notes = null): bool
    {
        if ($payment->payment_method !== 'cash') {
            throw new \InvalidArgumentException('Only cash payments can be manually confirmed');
        }

        $payment->update([
            'status' => 'succeeded',
            'paid_at' => now(),
            'notes' => $notes,
        ]);

        $payment->orderGroup->markAsPaid();

        Log::info('Cash payment confirmed manually', [
            'payment_id' => $payment->id,
            'order_group_id' => $payment->order_group_id,
            'notes' => $notes,
        ]);

        return true;
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = Payment::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $totalRevenue = $query->clone()->successful()->sum('amount');
        $totalOrders = $query->clone()->successful()->count();
        
        $revenueByMethod = $query->clone()
            ->successful()
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $pendingPayments = $query->clone()->pending()->count();
        $failedPayments = $query->clone()->failed()->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'revenue_by_method' => $revenueByMethod,
            'pending_payments' => $pendingPayments,
            'failed_payments' => $failedPayments,
            'success_rate' => $totalOrders > 0 ? ($totalOrders / ($totalOrders + $failedPayments)) * 100 : 0,
        ];
    }

    private function processCashPayment(OrderGroup $orderGroup, float $amount, array $customerData): array
    {
        return $this->paymentGateway->createCashPayment($orderGroup, $amount, $customerData);
    }

    private function processOnlinePayment(
        OrderGroup $orderGroup, 
        float $amount, 
        string $paymentMethod, 
        array $customerData
    ): array {
        return $this->paymentGateway->createPaymentIntent($orderGroup, $amount, $paymentMethod, $customerData);
    }
}