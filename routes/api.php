<?php

use Illuminate\Support\Facades\Route;

// Arquivo de rotas API - aplicação migrada para PostgreSQL

// Parâmetros API routes - Mantido para compatibilidade, mas deprecado
Route::prefix('parametros')->name('api.parametros.')->middleware(['web'])->group(function () {
    // Redirecionar para o novo sistema quando possível
    Route::get('/', function () {
        return response()->json([
            'message' => 'API depreciada. Use /api/parametros-modular/',
            'deprecated' => true,
            'new_endpoint' => '/api/parametros-modular/'
        ], 410);
    })->name('index');
    
    Route::post('/validar-valor', [App\Http\Controllers\Parametro\ParametroController::class, 'validar'])->name('validar-valor');
    
    // Outras rotas retornam aviso de depreciação
    Route::any('/{any}', function () {
        return response()->json([
            'message' => 'Endpoint depreciado. Use o novo sistema de parâmetros modulares.',
            'deprecated' => true,
            'new_endpoint' => '/api/parametros-modular/'
        ], 410);
    })->where('any', '.*');
});

// Sistema de Parâmetros Modulares API
Route::prefix('parametros-modular')->name('api.parametros-modular.')->group(function () {
    // Validação e configurações
    Route::get('/validar/{modulo}/{submodulo}', [App\Http\Controllers\Parametro\ParametroController::class, 'validar'])->name('validar');
    Route::get('/configuracoes/{modulo}/{submodulo}', [App\Http\Controllers\Parametro\ParametroController::class, 'obterConfiguracoes'])->name('configuracoes');
    Route::get('/valor/{modulo}/{submodulo}/{campo}', [App\Http\Controllers\Parametro\ParametroController::class, 'obterValor'])->name('valor');
    Route::post('/cache/limpar', [App\Http\Controllers\Parametro\ParametroController::class, 'limparCache'])->name('limpar-cache');
    
    // Módulos
    Route::prefix('modulos')->name('modulos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/reordenar', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'reordenar'])->name('reordenar');
        Route::post('/{id}/duplicar', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'duplicar'])->name('duplicar');
        Route::get('/{id}/exportar', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'exportar'])->name('exportar');
        Route::post('/importar', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'importar'])->name('importar');
        Route::get('/extrair-json', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'extrairJson'])->name('extrair-json');
        
        // AJAX deletion routes without CSRF protection
        Route::delete('/{id}/ajax-delete', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'destroy'])->name('ajax-delete');
        Route::get('/teste-extracao', [App\Http\Controllers\Parametro\ModuloParametroController::class, 'testeExtracao'])->name('teste-extracao');
    });
    
    // Submódulos
    Route::prefix('submodulos')->name('submodulos.')->group(function () {
        Route::get('/modulo/{moduloId}', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/reordenar', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'reordenar'])->name('reordenar');
        Route::post('/{id}/duplicar', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'duplicar'])->name('duplicar');
        Route::get('/{id}/campos', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'campos'])->name('campos');
        Route::post('/{id}/salvar-valores', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'salvarValores'])->name('salvar-valores');
        Route::post('/{id}/validar-valores', [App\Http\Controllers\Parametro\SubmoduloParametroController::class, 'validarValores'])->name('validar-valores');
    });
    
    // Campos
    Route::prefix('campos')->name('campos.')->group(function () {
        Route::get('/submodulo/{submoduloId}', [App\Http\Controllers\Parametro\CampoParametroController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Parametro\CampoParametroController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Parametro\CampoParametroController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Parametro\CampoParametroController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Parametro\CampoParametroController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Parametro\CampoParametroController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/reordenar', [App\Http\Controllers\Parametro\CampoParametroController::class, 'reordenar'])->name('reordenar');
        Route::post('/{id}/duplicar', [App\Http\Controllers\Parametro\CampoParametroController::class, 'duplicar'])->name('duplicar');
        Route::get('/tipos-disponiveis', [App\Http\Controllers\Parametro\CampoParametroController::class, 'tiposDisponiveis'])->name('tipos-disponiveis');
        Route::get('/regras-validacao', [App\Http\Controllers\Parametro\CampoParametroController::class, 'regrasValidacao'])->name('regras-validacao');
        Route::post('/validar-configuracao', [App\Http\Controllers\Parametro\CampoParametroController::class, 'validarConfiguracao'])->name('validar-configuracao');
    });
});

// ===== ONLYOFFICE API ROUTES =====
Route::prefix('onlyoffice')->name('api.onlyoffice.')->group(function () {
    
    // Callback route (without CSRF protection for ONLYOFFICE server)
    Route::post('/callback/{documentKey}', [App\Http\Controllers\OnlyOffice\OnlyOfficeController::class, 'callback'])->name('callback');
    
    // Document models API
    Route::get('/modelos', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'apiList'])->name('modelos.list');
    Route::get('/modelos/{tipo_proposicao_id}', [App\Http\Controllers\Documento\DocumentoModeloController::class, 'apiList'])->name('modelos.by-tipo');
});

// Mock API routes for testing (kept from original, no CSRF protection needed)
Route::prefix('mock-api')->name('mock-api.')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'Mock API',
            'timestamp' => now()->toISOString()
        ]);
    })->name('health');
    
    Route::get('/parlamentares', function () {
        return response()->json([
            'data' => [
                ['id' => 1, 'nome' => 'João Silva', 'partido' => 'PDT'],
                ['id' => 2, 'nome' => 'Maria Santos', 'partido' => 'PSDB'],
                ['id' => 3, 'nome' => 'Pedro Oliveira', 'partido' => 'PT']
            ],
            'total' => 3
        ]);
    })->name('parlamentares');
    
    Route::post('/parametros/validar', function () {
        return response()->json(['valid' => true]);
    })->name('parametros.validar');
    
    Route::get('/status', function () {
        return response()->json([
            'api_status' => 'operational',
            'database' => 'connected',
            'cache' => 'active',
            'timestamp' => now()->toISOString()
        ]);
    })->name('status');
});

// ===== PARTIDOS API ROUTES =====
Route::prefix('partidos')->name('api.partidos.')->group(function () {
    
    // Lista e busca de partidos
    Route::get('/', [App\Http\Controllers\Api\PartidoApiController::class, 'index'])->name('index');
    Route::get('/brasileiros', [App\Http\Controllers\Api\PartidoApiController::class, 'partidosBrasileiros'])->name('brasileiros');
    Route::get('/buscar-sigla', [App\Http\Controllers\Api\PartidoApiController::class, 'buscarPorSigla'])->name('buscar-sigla');
    Route::get('/buscar-externos', [App\Http\Controllers\Api\PartidoApiController::class, 'buscarDadosExternos'])->name('buscar-externos');
    Route::get('/estatisticas', [App\Http\Controllers\Api\PartidoApiController::class, 'estatisticas'])->name('estatisticas');
    
    // Validações
    Route::get('/validar-sigla', [App\Http\Controllers\Api\PartidoApiController::class, 'validarSigla'])->name('validar-sigla');
    Route::get('/validar-numero', [App\Http\Controllers\Api\PartidoApiController::class, 'validarNumero'])->name('validar-numero');
    
    // Partido específico
    Route::get('/{id}', [App\Http\Controllers\Api\PartidoApiController::class, 'show'])->name('show');
});