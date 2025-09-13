<?php

use App\Http\Controllers\DebugController;
use App\Http\Controllers\Monitoring\PerformanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Debug Routes
|--------------------------------------------------------------------------
| Rotas para o sistema de debug interativo de ações do usuário
*/

Route::middleware(['web', 'auth', 'can:monitoring.debug'])->prefix('debug')->name('debug.')->group(function () {
    
    // Controle do debug logger
    Route::post('/start', [DebugController::class, 'start'])->name('start');
    Route::post('/stop', [DebugController::class, 'stop'])->name('stop');
    Route::get('/status', [DebugController::class, 'status'])->name('status');
    
    // Logs
    Route::get('/logs', [DebugController::class, 'getLogs'])->name('logs');
    Route::post('/export', [DebugController::class, 'exportLogs'])->name('export');
    Route::delete('/cleanup', [DebugController::class, 'cleanup'])->name('cleanup');
    
    // Database debug routes
    Route::get('/database/queries', [DebugController::class, 'getDatabaseQueries'])->name('database.queries');
    Route::get('/database/stats', [DebugController::class, 'getDatabaseStats'])->name('database.stats');
    
    // Cache management
    Route::post('/clear-cache', [DebugController::class, 'clearCache'])->name('clear-cache');
    
    // Production-ready routes with additional security
    Route::post('/export-data', [DebugController::class, 'exportDebugData'])
        ->name('export-data')
        ->middleware('can:monitoring.debug.export');
    
    Route::get('/database/production-stats', [DebugController::class, 'getProductionDbStats'])->name('database.production-stats');
    
});

/*
|--------------------------------------------------------------------------
| Monitoring Performance Routes
|--------------------------------------------------------------------------
| Routes for performance monitoring and metrics (requires monitoring.view)
*/

Route::middleware(['web', 'auth', 'can:monitoring.view'])->prefix('admin/monitoring/performance')->name('monitoring.performance.')->group(function () {
    
    // Percentile metrics
    Route::get('/pxx-last-hour', [PerformanceController::class, 'pxxLastHour'])->name('pxx.1h');
    Route::get('/pxx-last-24h', [PerformanceController::class, 'pxxLast24Hours'])->name('pxx.24h');
    
    // Error rates and throughput
    Route::get('/error-rates', [PerformanceController::class, 'errorRatesLastHour'])->name('error-rates');
    Route::get('/throughput', [PerformanceController::class, 'throughputLastHour'])->name('throughput');
    
    // System overview
    Route::get('/overview', [PerformanceController::class, 'overview'])->name('overview');
    
    // Route-specific details
    Route::get('/route-details', [PerformanceController::class, 'routeDetails'])->name('route-details');
    
    // Debug-only routes (require monitoring.debug permission)
    Route::middleware('can:monitoring.debug')->group(function () {
        Route::get('/slow-queries', [PerformanceController::class, 'slowQueries'])->name('slow-queries');
    });
    
});

// Rota pública para servir arquivos de debug exportados
Route::get('/debug/exports/{filename}', function ($filename) {
    $path = storage_path("app/debug_exports/{$filename}");
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path, [
        'Content-Type' => 'text/plain',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
    ]);
})->name('debug.download');