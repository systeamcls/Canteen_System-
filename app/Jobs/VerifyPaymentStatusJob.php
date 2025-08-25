<?php

namespace App\Jobs;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyPaymentStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30; // seconds

    public function __construct(
        private Payment $payment
    ) {}

    /**
     * Execute the job to verify payment status
     */
    public function handle(PaymentGatewayInterface $paymentGateway): void
    {
        try {
            // Only verify online payments that are still pending
            if ($this->payment->isCashPayment() || !$this->payment->isPending()) {
                return;
            }

            if (!$this->payment->provider_payment_id) {
                Log::warning('Payment verification skipped - no provider payment ID', [
                    'payment_id' => $this->payment->id,
                ]);
                return;
            }

            Log::info('Verifying payment status', [
                'payment_id' => $this->payment->id,
                'provider_payment_id' => $this->payment->provider_payment_id,
            ]);

            $result = $paymentGateway->verifyPaymentStatus($this->payment->provider_payment_id);

            if (!$result['success']) {
                Log::error('Payment verification failed', [
                    'payment_id' => $this->payment->id,
                    'error' => $result['error'],
                ]);
                return;
            }

            $providerStatus = $result['status'];
            
            // Update payment status based on provider response
            $this->updatePaymentStatus($providerStatus, $result['data'] ?? []);

        } catch (\Exception $e) {
            Log::error('Payment verification job failed', [
                'payment_id' => $this->payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Update payment status based on provider response
     */
    private function updatePaymentStatus(string $providerStatus, array $providerData): void
    {
        $currentStatus = $this->payment->status;

        switch ($providerStatus) {
            case 'succeeded':
                if ($currentStatus === 'pending') {
                    $this->payment->update([
                        'status' => 'succeeded',
                        'paid_at' => now(),
                        'provider_response' => array_merge(
                            $this->payment->provider_response ?? [],
                            ['verification' => $providerData]
                        ),
                    ]);

                    $this->payment->orderGroup->markAsPaid();

                    Log::info('Payment status updated to succeeded via verification', [
                        'payment_id' => $this->payment->id,
                    ]);
                }
                break;

            case 'processing':
                // Payment is still processing, schedule another check
                self::dispatch($this->payment)->delay(now()->addMinutes(5));
                
                Log::info('Payment still processing, will check again', [
                    'payment_id' => $this->payment->id,
                ]);
                break;

            case 'failed':
            case 'cancelled':
                if ($currentStatus === 'pending') {
                    $this->payment->update([
                        'status' => 'failed',
                        'provider_response' => array_merge(
                            $this->payment->provider_response ?? [],
                            ['verification' => $providerData]
                        ),
                    ]);

                    $this->payment->orderGroup->markAsFailed();

                    Log::info('Payment status updated to failed via verification', [
                        'payment_id' => $this->payment->id,
                        'provider_status' => $providerStatus,
                    ]);
                }
                break;

            default:
                Log::info('Unknown payment status from provider', [
                    'payment_id' => $this->payment->id,
                    'provider_status' => $providerStatus,
                ]);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Payment verification job permanently failed', [
            'payment_id' => $this->payment->id,
            'error' => $exception->getMessage(),
        ]);

        // Optionally mark payment as failed after max retries
        if ($this->payment->isPending()) {
            $this->payment->update([
                'status' => 'failed',
                'notes' => 'Payment verification failed after multiple attempts',
            ]);
        }
    }
}