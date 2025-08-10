<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;


class SecurityMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        
    config([
        'security.rate_limit_auth_attempts' => 10,
        'security.rate_limit_auth_decay' => 300
    ]);

        // Clear rate limiter before each test
        RateLimiter::clear('test-key');
    }

    #[Test]
    public function security_headers_are_applied(): void
    {
        $response = $this->get('/');

        // Basic security headers
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy
        $this->assertTrue($response->headers->has('Permissions-Policy'));
        $permissionsPolicy = $response->headers->get('Permissions-Policy');
        $this->assertStringContainsString('camera=()', $permissionsPolicy);
        $this->assertStringContainsString('microphone=()', $permissionsPolicy);
    }

    #[Test]
    public function csp_header_is_applied_when_enabled(): void
    {
        config(['security.csp_enabled' => true]);

        $response = $this->get('/');

        $this->assertTrue($response->headers->has('Content-Security-Policy'));
        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-src 'none'", $csp);
    }

    #[Test]
    public function csp_header_is_not_applied_when_disabled(): void
    {
        config(['security.csp_enabled' => false]);

        $response = $this->get('/');

        $this->assertFalse($response->headers->has('Content-Security-Policy'));
    }

    #[Test]
    public function https_headers_are_applied_in_production(): void
    {
        config([
        'app.env' => 'production',
        'security.https_enforce' => true
    ]);
        // Force non-localhost test
    $this->serverVariables = ['REMOTE_ADDR' => '8.8.8.8'];
    
    $response = $this->get('/');
    $response->assertHeader('Strict-Transport-Security');
    }

    #[Test]
public function rate_limiting_is_applied_on_auth_routes(): void
{
    config([
        'security.rate_limit_auth_attempts' => 3,
        'security.rate_limit_auth_decay' => 300,
    ]);

    // Clear ALL possible rate limiters
    $this->clearAllRateLimiters();

    // Test with /login since it definitely exists
    for ($i = 0; $i < 4; $i++) {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);

        if ($i < 3) {
            $this->assertTrue(
                $response->headers->has('X-RateLimit-Limit'),
                "X-RateLimit-Limit header missing on attempt {$i}"
            );
            $this->assertTrue(
                $response->headers->has('X-RateLimit-Remaining'),
                "X-RateLimit-Remaining header missing on attempt {$i}"
            );
        }
    }

    // Verify 4th attempt is blocked
    $this->assertEquals(429, $response->getStatusCode());
}

    #[Test]
public function rate_limit_headers_are_present(): void
{
    // Nuclear option - clear everything
    $this->clearAllRateLimiters();
    
    config([
        'security.rate_limit_auth_attempts' => 10,
        'security.rate_limit_auth_decay' => 300
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password'
    ]);

    // Debug output
    dump([
        'X-RateLimit-Limit' => $response->headers->get('X-RateLimit-Limit'),
        'X-RateLimit-Remaining' => $response->headers->get('X-RateLimit-Remaining')
    ]);

    $this->assertEquals(10, (int)$response->headers->get('X-RateLimit-Limit'));
    $this->assertEquals(9, (int)$response->headers->get('X-RateLimit-Remaining'));
}

protected function clearAllRateLimiters(): void
{
    // Clear standard keys
    RateLimiter::clear('login');
    RateLimiter::clear('test-key');
    
    // Clear your custom middleware's key
    RateLimiter::clear(sha1('127.0.0.1|/login|POST|auth-rate-limit'));
    
    // Nuclear option if using Redis
    if (config('cache.default') === 'redis') {
        \Illuminate\Support\Facades\Redis::connection()->flushdb();
    }
}


    protected function resolveTestKey(): string
    {
        return sha1('127.0.0.1|login|POST|auth-rate-limit');
    }

    #[Test]
    public function non_auth_routes_are_not_rate_limited(): void
    {
        $response = $this->get('/');

        $this->assertFalse($response->headers->has('X-RateLimit-Limit'));
        $this->assertFalse($response->headers->has('X-RateLimit-Remaining'));
    }

    #[Test]
public function auth_routes_are_detected_for_rate_limiting(): void
{
    config(['security.rate_limit_auth_attempts' => 10]);

    $authRoutes = [
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password', 
        '/two-factor-challenge',
        '/user/confirm-password',
        '/user/profile-information'
    ];

    foreach ($authRoutes as $route) {
        // Skip if route doesn't exist
        if (!Route::has(str_replace('/', '.', trim($route, '/')))) {
            continue;
        }

        // Clear rate limiter
        $key = sha1('127.0.0.1|'.$route.'|POST|auth-rate-limit');
        RateLimiter::clear($key);

        // Make request with proper method
        $method = in_array($route, ['/two-factor-challenge','/user/confirm-password']) ? 'get' : 'post';
        $response = $this->$method($route);

        // Assert
        $this->assertTrue(
            $response->headers->has('X-RateLimit-Limit'),
            "Failed: {$route} should be rate limited"
        );
    }
}

    #[Test]
    public function security_headers_can_be_disabled(): void
    {
        config(['security.headers_enabled' => false]);

        $response = $this->get('/');

        $this->assertFalse($response->headers->has('X-Content-Type-Options'));
        $this->assertFalse($response->headers->has('X-Frame-Options'));
        $this->assertFalse($response->headers->has('X-XSS-Protection'));
    }
}
