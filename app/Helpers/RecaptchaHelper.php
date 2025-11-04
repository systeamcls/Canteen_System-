<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaHelper
{
    /**
     * Verify reCAPTCHA v3 token (score-based)
     */
    public static function verify(string $token, string $action = 'submit', float $minScore = 0.5): bool
    {
        if (empty($token)) {
            Log::warning('reCAPTCHA v3 verification failed: Empty token');
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
            ]);

            $result = $response->json();

            Log::info('reCAPTCHA v3 verification', [
                'success' => $result['success'] ?? false,
                'score' => $result['score'] ?? 0,
                'action' => $result['action'] ?? 'unknown',
                'expected_action' => $action,
                'min_score' => $minScore,
            ]);

            // Check if verification was successful
            if (!isset($result['success']) || !$result['success']) {
                Log::warning('reCAPTCHA v3 verification failed', [
                    'error_codes' => $result['error-codes'] ?? []
                ]);
                return false;
            }

            // Check if action matches
            if (isset($result['action']) && $result['action'] !== $action) {
                Log::warning('reCAPTCHA v3 action mismatch', [
                    'expected' => $action,
                    'received' => $result['action']
                ]);
                return false;
            }

            // Check if score meets threshold
            $score = $result['score'] ?? 0;
            if ($score < $minScore) {
                Log::warning('reCAPTCHA v3 score too low', [
                    'score' => $score,
                    'min_score' => $minScore
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('reCAPTCHA v3 verification exception', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verify reCAPTCHA v2 token (checkbox or invisible)
     */
    public static function verifyV2(string $token, string $type = 'invisible'): bool
    {
        if (empty($token)) {
            Log::warning('reCAPTCHA v2 verification failed: Empty token');
            return false;
        }

        try {
            // Select the appropriate secret key
            $secretKey = $type === 'checkbox' 
                ? config('services.recaptcha.v2_checkbox_secret_key')
                : config('services.recaptcha.v2_invisible_secret_key');

            if (empty($secretKey)) {
                Log::error('reCAPTCHA v2 secret key not configured', ['type' => $type]);
                return false;
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
            ]);

            $result = $response->json();

            Log::info('reCAPTCHA v2 verification', [
                'type' => $type,
                'success' => $result['success'] ?? false,
                'challenge_ts' => $result['challenge_ts'] ?? null,
                'hostname' => $result['hostname'] ?? null,
            ]);

            // v2 is binary - either success or fail
            if (!isset($result['success']) || !$result['success']) {
                Log::warning('reCAPTCHA v2 verification failed', [
                    'type' => $type,
                    'error_codes' => $result['error-codes'] ?? []
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('reCAPTCHA v2 verification exception', [
                'type' => $type,
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get score threshold based on action
     */
    public static function getScoreThreshold(string $action): float
    {
        return match($action) {
            'login' => 0.5,
            'register' => 0.5,
            'checkout' => 0.6,
            'guest_checkout' => 0.5,
            'guest_continue' => 0.4,
            default => 0.5,
        };
    }

    /**
     * Get site key for v2 based on type
     */
    public static function getV2SiteKey(string $type = 'invisible'): string
    {
        return $type === 'checkbox'
            ? config('services.recaptcha.v2_checkbox_site_key')
            : config('services.recaptcha.v2_invisible_site_key');
    }
}