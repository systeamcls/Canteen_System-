<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaHelper
{
    /**
     * Verify reCAPTCHA v3 token
     * 
     * @param string $token The reCAPTCHA token from frontend
     * @param string|null $action Expected action name (optional)
     * @param float $minScore Minimum acceptable score (0.0 to 1.0)
     * @return bool
     */
    public static function verify($token, $action = null, $minScore = 0.5)
    {
        try {
            // Call Google's API
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
            ]);

            $result = $response->json();

            // Log for debugging
            Log::info('reCAPTCHA verification', [
                'success' => $result['success'] ?? false,
                'score' => $result['score'] ?? 0,
                'action' => $result['action'] ?? null,
                'hostname' => $result['hostname'] ?? null,
            ]);

            // Check if request was successful
            if (!isset($result['success']) || !$result['success']) {
                Log::warning('reCAPTCHA failed', ['errors' => $result['error-codes'] ?? []]);
                return false;
            }

            // Check score
            if (!isset($result['score']) || $result['score'] < $minScore) {
                Log::warning('reCAPTCHA score too low', [
                    'score' => $result['score'] ?? 0,
                    'required' => $minScore,
                ]);
                return false;
            }

            // Optional: Verify action matches
            if ($action && isset($result['action']) && $result['action'] !== $action) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $action,
                    'received' => $result['action'],
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', [
                'error' => $e->getMessage(),
            ]);
            // In production, you might want to allow the action if reCAPTCHA is down
            // return true; // Fail open
            return false; // Fail closed (more secure)
        }
    }

    /**
     * Get score threshold for different actions
     */
    public static function getScoreThreshold($action)
{
    return match($action) {
        'register' => 0.5,
        'login' => 0.3,
        'checkout' => 0.5,
        'guest_checkout' => 0.6,
        'place_order' => 0.5,
        default => 0.5,
    };
}
}