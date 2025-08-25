<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $paymentGateway
    ) {}

    /**
     * Handle PayMongo webhook events
     */
    public function paymongo(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature for security
            if (!$this->verifyPayMongoSignature($request)) {
                Log::warning('Invalid PayMongo webhook signature', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Process the webhook
            $result = $this->paymentGateway->handleWebhook($request);

            if ($result['success']) {
                return response()->json(['message' => 'Webhook processed successfully']);
            } else {
                return response()->json(['error' => $result['error']], 400);
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Test webhook endpoint for development
     */
    public function testWebhook(Request $request): JsonResponse
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        Log::info('Test webhook received', $request->all());

        return response()->json(['message' => 'Test webhook received']);
    }

    /**
     * Verify PayMongo webhook signature
     */
    private function verifyPayMongoSignature(Request $request): bool
    {
        $signature = $request->header('paymongo-signature');
        $payload = $request->getContent();
        $webhookSecret = config('paymongo.webhook_secret');

        if (!$signature || !$webhookSecret) {
            return false;
        }

        // Parse signature header
        $elements = explode(',', $signature);
        $signatureData = [];

        foreach ($elements as $element) {
            [$key, $value] = explode('=', $element, 2);
            $signatureData[$key] = $value;
        }

        if (!isset($signatureData['t']) || !isset($signatureData['v1'])) {
            return false;
        }

        $timestamp = $signatureData['t'];
        $signatures = [$signatureData['v1']];

        // Check timestamp tolerance (5 minutes)
        $tolerance = 300;
        if (abs(time() - $timestamp) > $tolerance) {
            return false;
        }

        // Verify signature
        $expectedSignature = hash_hmac('sha256', $timestamp . '.' . $payload, $webhookSecret);

        return in_array($expectedSignature, $signatures);
    }
}