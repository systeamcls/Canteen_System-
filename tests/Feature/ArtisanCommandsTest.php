<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArtisanCommandsTest extends TestCase
{
    /**
     * Test that artisan commands work correctly after bootstrap configuration.
     */
    public function test_artisan_commands_are_executable(): void
    {
        // Test that basic artisan commands work
        $this->artisan('--version')
            ->assertExitCode(0);

        $this->artisan('list')
            ->assertExitCode(0);

        $this->artisan('about')
            ->assertExitCode(0);
    }

    /**
     * Test that application instance is properly created.
     */
    public function test_application_instance_is_properly_configured(): void
    {
        // Test that the application instance exists and has the required methods
        $app = app();
        
        $this->assertInstanceOf(\Illuminate\Foundation\Application::class, $app);
        $this->assertTrue(method_exists($app, 'handleCommand'));
    }

    /**
     * Test that middleware configuration is properly loaded.
     */
    public function test_middleware_configuration_is_loaded(): void
    {
        // Test that middleware aliases are registered
        $middlewareAliases = app('router')->getMiddleware();
        
        $this->assertArrayHasKey('checkusertype', $middlewareAliases);
        $this->assertArrayHasKey('security.headers', $middlewareAliases);
        $this->assertArrayHasKey('rate.limit.auth', $middlewareAliases);
    }
}