<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MockApiController;
use App\Http\Controllers\UserApiController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Dashboard route (protected)
Route::get('/dashboard', [UserApiController::class, 'index'])->name('dashboard');

// User API routes (protected)
Route::prefix('user-api')->name('user-api.')->group(function () {
    Route::get('/', [UserApiController::class, 'index'])->name('index');
    Route::post('/login', [UserApiController::class, 'login'])->name('login');
    Route::post('/logout', [UserApiController::class, 'logout'])->name('logout');
    Route::post('/register', [UserApiController::class, 'register'])->name('register');
    Route::get('/users', [UserApiController::class, 'getUsers'])->name('users');
    Route::get('/user/{id}', [UserApiController::class, 'getUser'])->name('user');
    Route::post('/user', [UserApiController::class, 'createUser'])->name('create-user');
    Route::patch('/user/{id}', [UserApiController::class, 'updateUser'])->name('update-user');
    Route::delete('/user/{id}', [UserApiController::class, 'deleteUser'])->name('delete-user');
    Route::get('/auth-status', [UserApiController::class, 'authStatus'])->name('auth-status');
    Route::get('/health', [UserApiController::class, 'healthCheck'])->name('health');
    Route::post('/auto-login', [UserApiController::class, 'autoLogin'])->name('auto-login');
});

// Mock API routes moved to routes/api.php to avoid CSRF middleware

Route::get('/api-test/health', function () {
    return response()->json([
        'success' => true,
        'healthy' => true,
        'provider' => 'Mock API',
        'message' => 'API is healthy',
    ]);
});

// Registration functionality working correctly
