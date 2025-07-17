<?php

use Illuminate\Support\Facades\Route;

// Arquivo de rotas API - aplicação migrada para PostgreSQL

// Parâmetros API routes (protected with auth - Admin only)
Route::prefix('parametros')->name('api.parametros.')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [App\Http\Controllers\Api\ParametroController::class, 'index'])->name('index');
    Route::get('/grupos', [App\Http\Controllers\Api\ParametroController::class, 'grupos'])->name('grupos');
    Route::get('/estatisticas', [App\Http\Controllers\Api\ParametroController::class, 'estatisticas'])->name('estatisticas');
    Route::get('/exportar', [App\Http\Controllers\Api\ParametroController::class, 'exportar'])->name('exportar');
    Route::post('/importar', [App\Http\Controllers\Api\ParametroController::class, 'importar'])->name('importar');
    Route::post('/validar-valor', [App\Http\Controllers\Api\ParametroController::class, 'validarValor'])->name('validar-valor');
    
    // Parâmetros específicos
    Route::get('/{codigo}', [App\Http\Controllers\Api\ParametroController::class, 'show'])->name('show');
    Route::put('/{codigo}', [App\Http\Controllers\Api\ParametroController::class, 'atualizarValor'])->name('atualizar-valor');
    
    // Parâmetros por grupo
    Route::get('/grupo/{codigoGrupo}', [App\Http\Controllers\Api\ParametroController::class, 'porGrupo'])->name('por-grupo');
    Route::put('/grupo/{codigoGrupo}', [App\Http\Controllers\Api\ParametroController::class, 'atualizarPorGrupo'])->name('atualizar-por-grupo');
}); 