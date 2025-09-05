<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OptimizeForProduction extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'optimize:for-production 
                            {--skip-migrations : Skip running database migrations}
                            {--skip-cache : Skip cache optimization}
                            {--force : Force optimization even if not in production}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize the application for production deployment';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting production optimization...');

        // Check environment
        if (!$this->option('force') && !app()->isProduction()) {
            $this->warn('Not in production environment. Use --force to override.');
            return Command::FAILURE;
        }

        try {
            // Step 1: Run database migrations if not skipped
            if (!$this->option('skip-migrations')) {
                $this->info('Running database migrations...');
                $this->call('migrate', ['--force' => true]);
            }

            // Step 2: Clear all caches
            if (!$this->option('skip-cache')) {
                $this->info('Clearing application caches...');
                $this->call('optimize:clear');
            }

            // Step 3: Generate optimized caches
            if (!$this->option('skip-cache')) {
                $this->info('Building optimized caches...');
                
                // Config cache
                $this->call('config:cache');
                $this->line('✓ Configuration cached');

                // Route cache
                $this->call('route:cache');
                $this->line('✓ Routes cached');

                // View cache
                $this->call('view:cache');
                $this->line('✓ Views cached');
            }

            // Step 4: Optimize autoloader
            $this->info('Optimizing autoloader...');
            $this->call('optimize');
            $this->line('✓ Autoloader optimized');

            // Step 5: Create storage link if it doesn't exist
            if (!file_exists(public_path('storage'))) {
                $this->info('Creating storage symlink...');
                $this->call('storage:link');
                $this->line('✓ Storage symlink created');
            }

            // Step 6: Set proper permissions
            $this->info('Setting storage permissions...');
            $this->setStoragePermissions();

            // Step 7: Generate application key if missing
            if (empty(config('app.key'))) {
                $this->info('Generating application key...');
                $this->call('key:generate', ['--force' => true]);
                $this->line('✓ Application key generated');
            }

            // Step 8: Validate optimization
            $this->info('Validating optimization...');
            $this->validateOptimization();

            $this->info('✅ Production optimization completed successfully!');
            
            // Display optimization summary
            $this->displayOptimizationSummary();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Production optimization failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Set proper storage permissions
     */
    private function setStoragePermissions(): void
    {
        $storagePaths = [
            storage_path('framework'),
            storage_path('logs'),
            storage_path('app'),
        ];

        foreach ($storagePaths as $path) {
            if (file_exists($path)) {
                chmod($path, 0755);
                $this->line("✓ Set permissions for {$path}");
            }
        }
    }

    /**
     * Validate optimization results
     */
    private function validateOptimization(): void
    {
        $checks = [];

        // Check if config is cached
        $checks['config_cached'] = app()->configurationIsCached();
        
        // Check if routes are cached
        $checks['routes_cached'] = app()->routesAreCached();
        
        // Check if views are cached
        $checks['views_cached'] = !empty(glob(storage_path('framework/views') . '/*.php'));
        
        // Check if application key exists
        $checks['app_key_exists'] = !empty(config('app.key'));
        
        // Check storage permissions
        $checks['storage_writable'] = is_writable(storage_path());

        foreach ($checks as $check => $passed) {
            if ($passed) {
                $this->line("✓ {$check}");
            } else {
                $this->warn("⚠ {$check} - validation failed");
            }
        }
    }

    /**
     * Display optimization summary
     */
    private function displayOptimizationSummary(): void
    {
        $this->newLine();
        $this->line('📊 Optimization Summary:');
        $this->line('========================');
        
        // Cache sizes
        $configCache = app()->configurationIsCached()
            ? $this->formatBytes(filesize(base_path('bootstrap/cache/config.php'))) 
            : 'Not cached';
            
        $routeCache = app()->routesAreCached()
            ? $this->formatBytes(filesize(app()->getCachedRoutesPath()))
            : 'Not cached';
            
        $viewCount = count(glob(storage_path('framework/views') . '/*.php'));
        
        $this->line("Config cache: {$configCache}");
        $this->line("Route cache: {$routeCache}");
        $this->line("Cached views: {$viewCount} files");
        $this->line("Environment: " . app()->environment());
        $this->line("Debug mode: " . (config('app.debug') ? 'enabled' : 'disabled'));
        
        $this->newLine();
        $this->info('🚀 Application is optimized for production!');
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}