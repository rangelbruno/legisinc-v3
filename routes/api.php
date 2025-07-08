<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MockApiController;

// API Mock routes (sem CSRF)
Route::prefix('mock-api')->group(function () {
    Route::get('/', [MockApiController::class, 'health']);
    Route::post('/register', [MockApiController::class, 'register']);
    Route::post('/login', [MockApiController::class, 'login']);
    Route::post('/logout', [MockApiController::class, 'logout']);
    Route::post('/reset', [MockApiController::class, 'reset']);
    
    // Rotas de usuários (protegidas)
    Route::get('/users', [MockApiController::class, 'users']);
    Route::post('/users', [MockApiController::class, 'createUser']);
    Route::get('/users/{id}', [MockApiController::class, 'getUser']);
    Route::put('/users/{id}', [MockApiController::class, 'updateUser']);
    Route::delete('/users/{id}', [MockApiController::class, 'deleteUser']);
    
    // Rotas de parlamentares
    Route::get('/parlamentares', [MockApiController::class, 'parlamentares']);
    Route::post('/parlamentares', [MockApiController::class, 'createParlamentar']);
    Route::get('/parlamentares/{id}', [MockApiController::class, 'getParlamentar']);
    Route::put('/parlamentares/{id}', [MockApiController::class, 'updateParlamentar']);
    Route::delete('/parlamentares/{id}', [MockApiController::class, 'deleteParlamentar']);
    
    // Rotas especializadas de parlamentares
    Route::get('/parlamentares/partido/{partido}', [MockApiController::class, 'parlamentaresByPartido']);
    Route::get('/parlamentares/status/{status}', [MockApiController::class, 'parlamentaresByStatus']);
    Route::get('/parlamentares/{id}/comissoes', [MockApiController::class, 'comissoesParlamentar']);
    
    // Rotas da mesa diretora
    Route::get('/mesa-diretora', [MockApiController::class, 'mesaDiretora']);
}); 