<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityMiddlewareTest extends TestCase
{
    public function test_security_headers_are_applied(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_content_security_policy_header_is_present(): void
    {
        $response = $this->get('/');

        $response->assertHeaderContains('Content-Security-Policy', "default-src 'self'");
    }

    public function test_permissions_policy_header_is_applied(): void
    {
        $response = $this->get('/');

        $response->assertHeader('Permissions-Policy');
    }
}