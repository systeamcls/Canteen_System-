<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\OrderGroup;
use App\Models\Payment;
use App\Services\RevenueSplittingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoGateway implements PaymentGatewayInterface
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl;
    private RevenueSplittingService $revenueSplittingService;

    public function __construct()
    {
        $this->secretKey = config('paymongo.secret_key');
        $this->publicKey = config('paymongo.public_key');
        $this->baseUrl = config('paymongo.base_url');
        $this->revenueSplittingService = app(RevenueSplittingService::class);
    }

    public function createPaymentIntent(
        OrderGroup $orderGroup, 
        float $amount, 
        string $paymentMethod, 
        array $customerData = []
    ): array {
        try {
            // Convert amount to centavos (PayMongo requirement)
            $amountInCentavos = (int) ($amount * 100);

            Log::info('Creating PayMongo payment intent', [
                'order_group_id' => $orderGroup->id,
                'amount' => $amount,
                'method' => $paymentMethod,
            ]);

            // Create payment intent first
            $paymentIntentData = [
                'data' => [
                    'attributes' => [
                        'amount' => $amountInCentavos,
                        'payment_method_allowed' => [$this->mapPaymentMethod($paymentMethod)],
                        'currency' => 'PHP',
                        'description' => "Order #{$orderGroup->id} - LTO Canteen Central",
                        'statement_descriptor' => 'LTO CANTEEN',
                        'metadata' => [
                            'order_group_id' => (string) $orderGroup->id,
                            'customer_name' => $customerData['name'] ?? 'Guest',
                            'customer_email' => $customerData['email'] ?? '',
                        ]
                    ]
                ]
            ];

            $response = $this->makeApiCall('POST', '/payment_intents', $paymentIntentData);
            $paymentIntent = $response['data'];

            // Create payment record in our database
            $payment = Payment::create([
                'order_group_id' => $orderGroup->id,
                'user_id' => $orderGroup->user_id,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'status' => 'pending',
                'provider' => 'paymongo',
                'provider_payment_id' => $paymentIntent['id'],
                'provider_response' => $paymentIntent,
            ]);

            // Create checkout URL based on payment method
            $checkoutUrl = $this->createCheckoutUrl($paymentIntent, $paymentMethod, $customerData);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'provider_payment_id' => $paymentIntent['id'],
                'checkout_url' => $checkoutUrl,
                'amount' => $amount,
                'currency' => 'PHP',
            ];

        } catch (\Exception $e) {
            Log::error('PayMongo payment intent creation failed', [
                'order_group_id' => $orderGroup->id,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function handleWebhook(Request $request): array
    {
        try {
            $payload = $request->json()->all();
            $eventType = $payload['data']['attributes']['type'];
            $eventData = $payload['data']['attributes']['data'];

            Log::info('PayMongo webhook received', [
                'type' => $eventType,
                'payment_id' => $eventData['id'] ?? null,
            ]);

            switch ($eventType) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentSucceeded($eventData);
                    
                case 'payment_intent.payment_failed':
                    return $this->handlePaymentFailed($eventData);
                    
                case 'source.chargeable':
                    return $this->handleSourceChargeable($eventData);
                    
                default:
                    Log::info('Unhandled webhook event type: ' . $eventType);
                    return ['success' => true, 'message' => 'Event type not handled'];
            }

        } catch (\Exception $e) {
            Log::error('PayMongo webhook handling failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifyPaymentStatus(string $paymentId): array
    {
        try {
            $response = $this->makeApiCall('GET', "/payment_intents/{$paymentId}");

            return [
                'success' => true,
                'status' => $response['data']['attributes']['status'],
                'data' => $response['data'],
            ];

        } catch (\Exception $e) {
            Log::error('PayMongo payment verification failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getSupportedPaymentMethods(): array
    {
        $methods = config('paymongo.payment_methods', []);
        $supported = [];

        foreach ($methods as $key => $config) {
            if ($config['enabled'] ?? true) {
                $supported[$key] = [
                    'name' => $config['name'],
                    'description' => $config['description'],
                    'icon' => $config['icon'],
                    'type' => $config['type'],
                ];
            }
        }

        return $supported;
    }

    public function createCashPayment(
        OrderGroup $orderGroup, 
        float $amount, 
        array $customerData = []
    ): array {
        try {
            $payment = Payment::create([
                'order_group_id' => $orderGroup->id,
                'user_id' => $orderGroup->user_id,
                'payment_method' => 'cash',
                'amount' => $amount,
                'status' => 'pending',
                'provider' => 'manual',
                'notes' => 'Cash on Delivery payment',
            ]);

            Log::info('Cash payment created', [
                'payment_id' => $payment->id,
                'order_group_id' => $orderGroup->id,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'message' => 'Cash payment recorded successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Cash payment creation failed', [
                'order_group_id' => $orderGroup->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    // Private helper methods

    private function makeApiCall(string $method, string $endpoint, array $data = []): array
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->$method($this->baseUrl . $endpoint, $data);

        if (!$response->successful()) {
            throw new \Exception("PayMongo API call failed: {$response->status()} - {$response->body()}");
        }

        return $response->json();
    }

    private function mapPaymentMethod(string $method): string
    {
        return match($method) {
            'gcash' => 'gcash',
            'paymaya' => 'grab_pay',
            'card' => 'card',
            default => throw new \InvalidArgumentException("Unsupported payment method: {$method}"),
        };
    }

    private function createCheckoutUrl(array $paymentIntent, string $paymentMethod, array $customerData): string
    {
        if (in_array($paymentMethod, ['gcash', 'paymaya'])) {
            return $this->createSourceCheckout($paymentIntent, $paymentMethod);
        } else {
            return $this->createCardCheckout($paymentIntent, $customerData);
        }
    }

    private function createSourceCheckout(array $paymentIntent, string $paymentMethod): string
{
    // Map payment method to correct PayMongo source type
    $sourceType = match($paymentMethod) {
        'gcash' => 'gcash',
        'paymaya' => 'grab_pay', // PayMongo uses 'grab_pay' not 'paymaya'
        default => $paymentMethod,
    };

    $sourceData = [
        'data' => [
            'attributes' => [
                'amount' => $paymentIntent['attributes']['amount'],
                'redirect' => [
                    'success' => config('paymongo.redirect_urls.success'),
                    'failed' => config('paymongo.redirect_urls.failed'),
                ],
                'type' => $sourceType, // Use the mapped type here
                'currency' => 'PHP',
            ]
        ]
    ];

    $response = $this->makeApiCall('POST', '/sources', $sourceData);
    $source = $response['data'];

    // Update payment with source ID
    Payment::where('provider_payment_id', $paymentIntent['id'])
        ->update(['provider_source_id' => $source['id']]);

    return $source['attributes']['redirect']['checkout_url'];
}

    private function createCardCheckout(array $paymentIntent, array $customerData): string
{
    $checkoutData = [
        'data' => [
            'attributes' => [
                'cancel_url' => config('paymongo.redirect_urls.cancelled'),
                'success_url' => config('paymongo.redirect_urls.success'),
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'currency' => 'PHP',
                        'amount' => $paymentIntent['attributes']['amount'],
                        'description' => $paymentIntent['attributes']['description'],
                        'name' => 'LTO Canteen Order',
                        'quantity' => 1,
                    ]
                ],
                'customer_email' => $customerData['email'] ?? null,
            ]
        ]
    ];

    $response = $this->makeApiCall('POST', '/checkout_sessions', $checkoutData);
    return $response['data']['attributes']['checkout_url'];
}

    private function handlePaymentSucceeded(array $eventData): array
    {
        $paymentIntentId = $eventData['id'];
        
        $payment = Payment::where('provider_payment_id', $paymentIntentId)->first();
        
        if (!$payment) {
            throw new \Exception("Payment not found for intent ID: {$paymentIntentId}");
        }

        $payment->update([
            'status' => 'succeeded',
            'paid_at' => now(),
            'provider_response' => array_merge($payment->provider_response ?? [], [
                'webhook_event' => $eventData
            ]),
        ]);

        // Update order group status
        $payment->orderGroup->markAsPaid();

        // Split revenue across stalls
        $this->revenueSplittingService->splitRevenue($payment->orderGroup);

        Log::info('Payment succeeded and revenue split', ['payment_id' => $payment->id]);

        return ['success' => true, 'message' => 'Payment marked as succeeded'];
    }

    private function handlePaymentFailed(array $eventData): array
    {
        $paymentIntentId = $eventData['id'];
        
        $payment = Payment::where('provider_payment_id', $paymentIntentId)->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'provider_response' => array_merge($payment->provider_response ?? [], [
                    'webhook_event' => $eventData
                ]),
            ]);

            $payment->orderGroup->markAsFailed();

            Log::info('Payment failed', ['payment_id' => $payment->id]);
        }

        return ['success' => true, 'message' => 'Payment marked as failed'];
    }

    private function handleSourceChargeable(array $eventData): array
    {
        // For e-wallet payments (GCash/PayMaya)
        $sourceId = $eventData['id'];
        
        $payment = Payment::where('provider_source_id', $sourceId)->first();
        
        if (!$payment) {
            throw new \Exception("Payment not found for source ID: {$sourceId}");
        }

        // For now, we'll mark as succeeded since the source is chargeable
        // In a more complex implementation, you'd attach and confirm the payment
        $payment->update([
            'status' => 'succeeded',
            'paid_at' => now(),
            'provider_response' => array_merge($payment->provider_response ?? [], [
                'source_event' => $eventData
            ]),
        ]);

        $payment->orderGroup->markAsPaid();

        Log::info('Source chargeable - payment succeeded', ['payment_id' => $payment->id]);

        return ['success' => true, 'message' => 'Payment confirmed via source'];
    }
}