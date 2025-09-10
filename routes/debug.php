<?php

use App\Http\Controllers\DebugController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Debug Routes
|--------------------------------------------------------------------------
| Rotas para o sistema de debug interativo de ações do usuário
*/

Route::middleware(['web', 'auth'])->prefix('debug')->name('debug.')->group(function () {
    
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