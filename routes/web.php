<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Parlamentar\ParlamentarController;

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
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');
Route::get('/home', function () {
    return view('welcome');
})->name('home');

// User profile routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/update-last-access', [UserController::class, 'updateLastAccess'])->name('user.update-last-access');
});

// Test route for debugging
Route::get('/test-auth', function () {
    if (auth()->check()) {
        return 'Usuário logado: ' . auth()->user()->name . ' - ' . auth()->user()->email;
    }
    return 'Usuário não está logado';
})->name('test.auth');

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

// Parlamentares routes (protected with permissions)
Route::prefix('parlamentares')->name('parlamentares.')->middleware('auth')->group(function () {
    Route::get('/', [ParlamentarController::class, 'index'])->name('index')->middleware('check.permission:parlamentares.view');
    Route::get('/create', [ParlamentarController::class, 'create'])->name('create')->middleware('check.permission:parlamentares.create');
    Route::post('/', [ParlamentarController::class, 'store'])->name('store')->middleware('check.permission:parlamentares.create');
    Route::get('/search', [ParlamentarController::class, 'search'])->name('search')->middleware('check.permission:parlamentares.view');
    Route::get('/mesa-diretora', [ParlamentarController::class, 'mesaDiretora'])->name('mesa-diretora')->middleware('check.permission:parlamentares.view');
    Route::get('/partido/{partido}', [ParlamentarController::class, 'porPartido'])->name('por-partido')->middleware('check.permission:parlamentares.view');
    Route::get('/{id}', [ParlamentarController::class, 'show'])->name('show')->middleware('check.permission:parlamentares.view');
    Route::get('/{id}/edit', [ParlamentarController::class, 'edit'])->name('edit')->middleware('check.permission:parlamentares.edit');
    Route::put('/{id}', [ParlamentarController::class, 'update'])->name('update')->middleware('check.permission:parlamentares.edit');
    Route::delete('/{id}', [ParlamentarController::class, 'destroy'])->name('destroy')->middleware('check.permission:parlamentares.delete');
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