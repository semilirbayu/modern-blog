<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    /**
     * Basic health check endpoint
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'service' => 'ModernBlog API',
            'version' => config('app.version', '1.0.0'),
        ]);
    }

    /**
     * Detailed health check with system dependencies
     */
    public function detailed(): JsonResponse
    {
        $checks = [
            'app' => $this->checkApplication(),
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $overallStatus = collect($checks)->every(fn($check) => $check['status'] === 'ok') 
            ? 'healthy' 
            : 'unhealthy';

        return response()->json([
            'status' => $overallStatus,
            'timestamp' => now(),
            'checks' => $checks,
            'uptime' => $this->getUptime(),
        ], $overallStatus === 'healthy' ? 200 : 503);
    }

    /**
     * Check application status
     */
    private function checkApplication(): array
    {
        try {
            return [
                'status' => 'ok',
                'message' => 'Application running',
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Application check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check database connectivity
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            
            // Test basic query
            $result = DB::select('SELECT 1 as test');
            
            return [
                'status' => 'ok',
                'message' => 'Database connected',
                'connection' => config('database.default'),
                'test_query' => isset($result[0]) ? 'passed' : 'failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache system
     */
    private function checkCache(): array
    {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'test_value';

            // Test cache write and read
            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);

            if ($retrieved !== $testValue) {
                throw new \Exception('Cache read/write test failed');
            }

            $status = [
                'status' => 'ok',
                'message' => 'Cache system working',
                'driver' => config('cache.default'),
            ];

            // Additional Redis check if using Redis
            if (config('cache.default') === 'redis') {
                try {
                    Redis::ping();
                    $status['redis'] = 'connected';
                } catch (\Exception $e) {
                    $status['redis'] = 'connection_failed';
                }
            }

            return $status;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache system failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage accessibility
     */
    private function checkStorage(): array
    {
        try {
            $storagePath = storage_path('app');
            
            if (!is_writable($storagePath)) {
                throw new \Exception('Storage directory not writable');
            }

            // Test file write/read
            $testFile = $storagePath . '/health_check_test.txt';
            $testContent = 'health_check_' . time();
            
            file_put_contents($testFile, $testContent);
            $readContent = file_get_contents($testFile);
            unlink($testFile);

            if ($readContent !== $testContent) {
                throw new \Exception('File read/write test failed');
            }

            return [
                'status' => 'ok',
                'message' => 'Storage accessible',
                'path' => $storagePath,
                'writable' => true,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get application uptime
     */
    private function getUptime(): array
    {
        $uptimeFile = storage_path('framework/cache/app_start_time');
        
        if (!file_exists($uptimeFile)) {
            file_put_contents($uptimeFile, time());
        }

        $startTime = (int) file_get_contents($uptimeFile);
        $uptime = time() - $startTime;

        return [
            'seconds' => $uptime,
            'human' => $this->formatUptime($uptime),
        ];
    }

    /**
     * Format uptime in human readable format
     */
    private function formatUptime(int $seconds): string
    {
        $units = [
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1,
        ];

        $parts = [];
        foreach ($units as $name => $divisor) {
            $value = floor($seconds / $divisor);
            if ($value > 0) {
                $parts[] = $value . ' ' . $name . ($value > 1 ? 's' : '');
                $seconds %= $divisor;
            }
        }

        return implode(', ', $parts) ?: '0 seconds';
    }
}