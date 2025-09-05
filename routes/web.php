<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;

// Health check routes for monitoring and Railway
Route::get('/health', [HealthController::class, 'index']);
Route::get('/health/detailed', [HealthController::class, 'detailed']);

// Login route for Laravel's default auth redirection
Route::get('/login', function () {
    return view('app');
})->name('login');

// Admin routes - serve admin Vue.js SPA
Route::get('/admin/{any?}', function () {
    return view('app');
})->where('any', '.*');

// Public routes - serve public Vue.js application  
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');