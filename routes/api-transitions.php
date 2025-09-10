<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WorkflowTransitionController;

/*
|--------------------------------------------------------------------------
| Workflow Transitions API Routes
|--------------------------------------------------------------------------
|
| Rotas da API para o sistema de transições de workflow
|
*/

Route::middleware(['auth:sanctum'])->prefix('workflow/transitions')->name('api.workflow.transitions.')->group(function () {
    
    // Obter transições disponíveis para um documento
    Route::get('/disponiveis', [WorkflowTransitionController::class, 'disponiveis'])
        ->name('disponiveis');
    
    // Executar uma transição
    Route::post('/executar', [WorkflowTransitionController::class, 'executar'])
        ->name('executar');
    
    // Validar uma transição sem executá-la
    Route::post('/validar', [WorkflowTransitionController::class, 'validar'])
        ->name('validar');
    
    // Processar transições automáticas
    Route::post('/processar-automaticas', [WorkflowTransitionController::class, 'processarAutomaticas'])
        ->name('processar-automaticas');
    
    // Obter histórico de transições
    Route::get('/historico', [WorkflowTransitionController::class, 'historico'])
        ->name('historico');
    
    // Obter status atual do workflow
    Route::get('/status', [WorkflowTransitionController::class, 'status'])
        ->name('status');
});

// Rotas administrativas (apenas para admins)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin/workflow/transitions')->name('api.admin.workflow.transitions.')->group(function () {
    
    // CRUD de transições (para administradores)
    Route::apiResource('', \App\Http\Controllers\Admin\Api\WorkflowTransitionAdminController::class);
    
    // Operações em lote
    Route::post('/batch/executar', [\App\Http\Controllers\Admin\Api\WorkflowTransitionAdminController::class, 'executarLote'])
        ->name('batch.executar');
    
    Route::post('/batch/processar-automaticas', [\App\Http\Controllers\Admin\Api\WorkflowTransitionAdminController::class, 'processarAutomaticasLote'])
        ->name('batch.processar-automaticas');
    
    // Relatórios e estatísticas
    Route::get('/relatorios/execucoes', [\App\Http\Controllers\Admin\Api\WorkflowTransitionAdminController::class, 'relatorioExecucoes'])
        ->name('relatorios.execucoes');
    
    Route::get('/relatorios/performance', [\App\Http\Controllers\Admin\Api\WorkflowTransitionAdminController::class, 'relatorioPerformance'])
        ->name('relatorios.performance');
});