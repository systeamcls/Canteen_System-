<?php

namespace App\Contracts;

use App\Models\OrderGroup;
use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Create a payment intent for the given order group
     *
     * @param OrderGroup $orderGroup
     * @param float $amount
     * @param string $paymentMethod ('gcash', 'paymaya', 'card')
     * @param array $customerData
     * @return array Payment intent data with checkout URL
     */
    public function createPaymentIntent(
        OrderGroup $orderGroup, 
        float $amount, 
        string $paymentMethod, 
        array $customerData = []
    ): array;

    /**
     * Handle webhook from payment provider
     *
     * @param Request $request
     * @return array Processing result
     */
    public function handleWebhook(Request $request): array;

    /**
     * Verify payment status directly with provider
     *
     * @param string $paymentId
     * @return array Payment status data
     */
    public function verifyPaymentStatus(string $paymentId): array;

    /**
     * Get supported payment methods
     *
     * @return array
     */
    public function getSupportedPaymentMethods(): array;

    /**
     * Create cash payment record (for COD)
     *
     * @param OrderGroup $orderGroup
     * @param float $amount
     * @param array $customerData
     * @return array
     */
    public function createCashPayment(
        OrderGroup $orderGroup, 
        float $amount, 
        array $customerData = []
    ): array;
}