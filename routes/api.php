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
    
    // Rotas de comissões
    Route::get('/comissoes', [MockApiController::class, 'comissoes']);
    Route::post('/comissoes', [MockApiController::class, 'createComissao']);
    
    // Rotas especializadas de comissões (devem vir antes das rotas com parâmetros)
    Route::get('/comissoes/estatisticas', [MockApiController::class, 'estatisticasComissoes']);
    Route::get('/comissoes/search', [MockApiController::class, 'searchComissoes']);
    Route::get('/comissoes/tipo/{tipo}', [MockApiController::class, 'comissoesByTipo']);
    Route::get('/comissoes/status/{status}', [MockApiController::class, 'comissoesByStatus']);
    
    // Rotas com parâmetros ID (devem vir por último)
    Route::get('/comissoes/{id}', [MockApiController::class, 'getComissao']);
    Route::put('/comissoes/{id}', [MockApiController::class, 'updateComissao']);
    Route::delete('/comissoes/{id}', [MockApiController::class, 'deleteComissao']);
    Route::get('/comissoes/{id}/membros', [MockApiController::class, 'membrosComissao']);
    Route::get('/comissoes/{id}/reunioes', [MockApiController::class, 'reunioesComissao']);
    
    // Rotas de sessões
    Route::get('/sessions', [MockApiController::class, 'sessions']);
    Route::post('/sessions', [MockApiController::class, 'createSession']);
    Route::get('/sessions/{id}', [MockApiController::class, 'getSession']);
    Route::put('/sessions/{id}', [MockApiController::class, 'updateSession']);
    Route::delete('/sessions/{id}', [MockApiController::class, 'deleteSession']);
    
    // Rotas de matérias das sessões
    Route::get('/sessions/{sessionId}/matters', [MockApiController::class, 'sessionMatters']);
    Route::post('/sessions/{sessionId}/matters', [MockApiController::class, 'addSessionMatter']);
    Route::put('/sessions/{sessionId}/matters/{matterId}', [MockApiController::class, 'updateSessionMatter']);
    Route::delete('/sessions/{sessionId}/matters/{matterId}', [MockApiController::class, 'removeSessionMatter']);
    
    // Rotas de XML e exportação
    Route::post('/sessions/{sessionId}/xml', [MockApiController::class, 'generateSessionXml']);
    Route::post('/sessions/{sessionId}/export', [MockApiController::class, 'exportSessionXml']);
    Route::get('/sessions/{sessionId}/exports', [MockApiController::class, 'sessionExports']);
}); 