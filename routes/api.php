<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PublicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('public')->group(function () {
    Route::get('/posts', [PublicController::class, 'posts']);
    Route::get('/posts/{slug}', [PublicController::class, 'postBySlug']);
    Route::get('/categories', [PublicController::class, 'categories']);
    Route::get('/categories/{slug}', [PublicController::class, 'categoryBySlug']);
    Route::get('/categories/{slug}/posts', [PublicController::class, 'postsByCategory']);
});

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);

    // Posts management
    Route::apiResource('posts', PostController::class);

    // Categories management
    Route::apiResource('categories', CategoryController::class);
});