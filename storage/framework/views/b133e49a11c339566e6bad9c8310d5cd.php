<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'ModernBlog')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Meta Tags -->
    <meta name="description" content="ModernBlog - A modern blogging platform built with Laravel and Vue.js">
    <meta name="keywords" content="blog, laravel, vue, modern, cms">
    <meta name="author" content="ModernBlog Team">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:title" content="<?php echo e(config('app.name', 'ModernBlog')); ?>">
    <meta property="og:description" content="A modern blogging platform built with Laravel and Vue.js">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo e(url()->current()); ?>">
    <meta property="twitter:title" content="<?php echo e(config('app.name', 'ModernBlog')); ?>">
    <meta property="twitter:description" content="A modern blogging platform built with Laravel and Vue.js">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <?php
        $isAdmin = request()->is('admin*');
        $appType = $isAdmin ? 'admin' : 'public';
    ?>

    <!-- Scripts and Styles -->
    <?php if($isAdmin): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/admin.js']); ?>
    <?php else: ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/public.js']); ?>
    <?php endif; ?>

    <!-- Additional head content for specific pages -->
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="antialiased">
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-overlay">
        <div class="text-center">
            <div class="spinner h-12 w-12 mx-auto mb-4"></div>
            <p class="text-white text-lg">Loading...</p>
        </div>
    </div>

    <!-- Vue.js Application -->
    <div id="app">
        <!-- Fallback content for when JavaScript is disabled -->
        <noscript>
            <div class="flex items-center justify-center min-h-screen bg-gray-50">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900 mb-4">JavaScript Required</h1>
                    <p class="text-gray-600 mb-4">
                        This application requires JavaScript to function properly.
                        Please enable JavaScript in your browser settings and reload the page.
                    </p>
                    <button onclick="window.location.reload()" class="btn btn-primary">
                        Reload Page
                    </button>
                </div>
            </div>
        </noscript>
    </div>

    <!-- Global Scripts -->
    <script>
        // Global configuration
        window.AppConfig = {
            name: '<?php echo e(config('app.name')); ?>',
            url: '<?php echo e(config('app.url')); ?>',
            env: '<?php echo e(config('app.env')); ?>',
            debug: <?php echo e(config('app.debug') ? 'true' : 'false'); ?>,
            locale: '<?php echo e(app()->getLocale()); ?>',
            csrf_token: '<?php echo e(csrf_token()); ?>',
            api_url: '<?php echo e(config('app.url')); ?>/api',
            app_type: '<?php echo e($appType); ?>'
        };

        // Hide loading screen when Vue app is mounted
        window.addEventListener('load', function() {
            setTimeout(function() {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.style.opacity = '0';
                    setTimeout(function() {
                        loadingScreen.style.display = 'none';
                    }, 300);
                }
            }, 500);
        });

        // Error handling for uncaught JavaScript errors
        window.addEventListener('error', function(event) {
            console.error('Global error:', event.error);
            
            // You can send errors to a logging service here
            if (window.AppConfig.env === 'production') {
                // Send to error tracking service like Sentry, Bugsnag, etc.
            }
        });

        // Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
            
            if (window.AppConfig.env === 'production') {
                // Send to error tracking service
            }
        });
    </script>

    <!-- Additional body scripts for specific pages -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH /Users/bayumbp132017/Dev/Vibe Coding/modern-blog/resources/views/app.blade.php ENDPATH**/ ?>