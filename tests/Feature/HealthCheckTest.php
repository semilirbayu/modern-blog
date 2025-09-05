<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function basic_health_check_returns_ok()
    {
        $response = $this->getJson('/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'service',
                'version'
            ])
            ->assertJson([
                'status' => 'ok',
                'service' => 'ModernBlog API'
            ]);
    }

    /** @test */
    public function detailed_health_check_returns_system_status()
    {
        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'checks' => [
                    'app' => ['status', 'message', 'environment', 'debug_mode'],
                    'database' => ['status', 'message', 'connection'],
                    'cache' => ['status', 'message', 'driver'],
                    'storage' => ['status', 'message', 'path', 'writable']
                ],
                'uptime' => ['seconds', 'human']
            ]);
    }

    /** @test */
    public function health_check_validates_database_connection()
    {
        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200);
        
        $checks = $response->json('checks');
        $this->assertEquals('ok', $checks['database']['status']);
        $this->assertStringContainsString('Database connected', $checks['database']['message']);
    }

    /** @test */
    public function health_check_validates_cache_system()
    {
        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200);
        
        $checks = $response->json('checks');
        $this->assertEquals('ok', $checks['cache']['status']);
        $this->assertStringContainsString('Cache system working', $checks['cache']['message']);
    }

    /** @test */
    public function health_check_validates_storage_access()
    {
        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200);
        
        $checks = $response->json('checks');
        $this->assertEquals('ok', $checks['storage']['status']);
        $this->assertTrue($checks['storage']['writable']);
    }

    /** @test */
    public function health_check_shows_application_environment()
    {
        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200);
        
        $checks = $response->json('checks');
        $this->assertEquals('testing', $checks['app']['environment']);
        $this->assertFalse($checks['app']['debug_mode']); // Debug should be false in testing
    }

    /** @test */
    public function health_check_returns_unhealthy_on_system_failure()
    {
        // Mock a database failure by using invalid connection
        config(['database.connections.mysql.host' => 'invalid-host']);
        
        // Clear any cached connections
        DB::purge();

        $response = $this->getJson('/health/detailed');

        // Should return 503 Service Unavailable when unhealthy
        $response->assertStatus(503)
            ->assertJson([
                'status' => 'unhealthy'
            ]);
    }

    /** @test */
    public function health_check_includes_uptime_information()
    {
        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200);
        
        $uptime = $response->json('uptime');
        $this->assertIsInt($uptime['seconds']);
        $this->assertIsString($uptime['human']);
        $this->assertGreaterThanOrEqual(0, $uptime['seconds']);
    }

    /** @test */
    public function health_check_cache_test_does_not_interfere_with_app_cache()
    {
        // Set a cache value before health check
        Cache::put('test_key', 'test_value', 300);

        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200);
        
        // Ensure our cache value is still there
        $this->assertEquals('test_value', Cache::get('test_key'));
    }

    /** @test */
    public function health_check_database_test_performs_actual_query()
    {
        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200);
        
        $checks = $response->json('checks');
        $this->assertEquals('passed', $checks['database']['test_query']);
    }

    /** @test */
    public function health_check_storage_test_cleans_up_test_files()
    {
        $response = $this->getJson('/health/detailed');

        $response->assertStatus(200);
        
        // Ensure no test files are left behind
        $testFiles = glob(storage_path('app/health_check_test*'));
        $this->assertEmpty($testFiles);
    }

    /** @test */
    public function basic_health_check_is_fast()
    {
        $start = microtime(true);
        
        $response = $this->getJson('/health');
        
        $duration = microtime(true) - $start;
        
        $response->assertStatus(200);
        $this->assertLessThan(1.0, $duration, 'Basic health check should be fast');
    }

    /** @test */
    public function health_check_endpoints_are_accessible_without_authentication()
    {
        // These tests already pass without authentication, but let's be explicit
        
        $basicResponse = $this->getJson('/health');
        $detailedResponse = $this->getJson('/health/detailed');

        $basicResponse->assertStatus(200);
        $detailedResponse->assertStatus(200);
    }

    /** @test */
    public function health_check_handles_missing_storage_directories()
    {
        // Temporarily rename storage directory to simulate missing directory
        $originalPath = storage_path('framework');
        $tempPath = storage_path('framework_backup');
        
        if (file_exists($originalPath)) {
            rename($originalPath, $tempPath);
        }

        $response = $this->getJson('/health/detailed');

        // Restore directory
        if (file_exists($tempPath)) {
            rename($tempPath, $originalPath);
        }

        // Health check should handle this gracefully
        $this->assertContains($response->status(), [200, 503]);
    }
}