<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\DatabaseDebugService;
use Illuminate\Support\Facades\Cache;

class DebugController extends Controller
{
    /**
     * Ativar debug logger na sessão
     */
    public function start(Request $request)
    {
        $sessionId = 'debug_' . time() . '_' . Auth::id();
        
        session([
            'debug_logger_active' => true,
            'debug_session_id' => $sessionId,
            'debug_started_at' => now()
        ]);

        Log::info('Debug logger started', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'session_id' => $sessionId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Iniciar captura de queries do banco de dados
        if (config('app.debug')) {
            $dbDebug = new DatabaseDebugService();
            $dbDebug->startCapture();
        }

        return response()->json([
            'status' => 'started',
            'session_id' => $sessionId,
            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->roles()->first()?->name
            ],
            'db_capture' => config('app.debug') ? 'enabled' : 'disabled'
        ]);
    }

    /**
     * Parar debug logger
     */
    public function stop(Request $request)
    {
        $sessionId = session('debug_session_id');
        $startedAt = session('debug_started_at');
        $duration = $startedAt ? now()->diffInSeconds($startedAt) : 0;

        session([
            'debug_logger_active' => false,
            'debug_session_id' => null,
            'debug_started_at' => null
        ]);

        Log::info('Debug logger stopped', [
            'user_id' => Auth::id(),
            'session_id' => $sessionId,
            'duration_seconds' => $duration
        ]);
        
        // Parar captura de queries do banco de dados
        if (config('app.debug')) {
            $dbDebug = new DatabaseDebugService();
            $dbDebug->stopCapture();
        }

        return response()->json([
            'status' => 'stopped',
            'duration' => $duration
        ]);
    }

    /**
     * Obter status atual do debug
     */
    public function status()
    {
        // Verificar também o status da captura de banco de dados
        $dbCaptureActive = Cache::get('db_debug_capturing', false);
        $dbQueriesCount = $dbCaptureActive ? count(Cache::get('db_debug_queries', [])) : 0;
        
        return response()->json([
            'active' => session('debug_logger_active', false),
            'session_id' => session('debug_session_id'),
            'started_at' => session('debug_started_at'),
            'user' => Auth::user() ? [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->roles()->first()?->name
            ] : null,
            'db_capture_active' => $dbCaptureActive,
            'db_queries_count' => $dbQueriesCount
        ]);
    }

    /**
     * Obter logs da sessão atual
     */
    public function getLogs(Request $request)
    {
        $sessionId = session('debug_session_id');
        if (!$sessionId) {
            return response()->json(['logs' => []]);
        }

        try {
            // Ler logs do arquivo
            $logPath = storage_path('logs/debug_actions.log');
            
            if (!file_exists($logPath)) {
                return response()->json(['logs' => []]);
            }

            $logs = [];
            $file = fopen($logPath, 'r');
            
            if ($file) {
                while (($line = fgets($file)) !== false) {
                    // Filtrar apenas logs desta sessão
                    if (str_contains($line, $sessionId)) {
                        $logs[] = $this->parseLogLine($line);
                    }
                }
                fclose($file);
            }

            // Ordenar por timestamp
            usort($logs, function($a, $b) {
                return strtotime($a['timestamp']) - strtotime($b['timestamp']);
            });

            return response()->json([
                'logs' => array_filter($logs), // Remove nulls
                'session_id' => $sessionId
            ]);

        } catch (\Exception $e) {
            Log::error('Error reading debug logs', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);

            return response()->json(['logs' => [], 'error' => 'Could not read logs']);
        }
    }

    /**
     * Salvar logs em arquivo para download
     */
    public function exportLogs(Request $request)
    {
        $sessionId = session('debug_session_id');
        $logs = $this->getLogs($request)->getData()->logs ?? [];

        $content = $this->generateLogReport($logs, $sessionId);
        
        $filename = "debug_log_{$sessionId}_" . date('Y-m-d_H-i-s') . '.txt';
        $path = "debug_exports/{$filename}";
        
        Storage::put($path, $content);

        return response()->json([
            'download_url' => Storage::url($path),
            'filename' => $filename
        ]);
    }

    /**
     * Limpar logs antigos
     */
    public function cleanup(Request $request)
    {
        $days = $request->get('days', 7);
        $cutoffDate = now()->subDays($days);

        // Limpar arquivos de export antigos
        $exportFiles = Storage::files('debug_exports');
        $cleaned = 0;

        foreach ($exportFiles as $file) {
            if (Storage::lastModified($file) < $cutoffDate->timestamp) {
                Storage::delete($file);
                $cleaned++;
            }
        }

        return response()->json([
            'cleaned_files' => $cleaned,
            'cutoff_date' => $cutoffDate->toDateTimeString()
        ]);
    }

    /**
     * Parse de linha de log
     */
    private function parseLogLine(string $line): ?array
    {
        try {
            // Formato do log: [timestamp] environment.LEVEL: message context
            if (preg_match('/\[(.*?)\].*?user_action (.*)/', $line, $matches)) {
                $timestamp = $matches[1];
                $contextJson = $matches[2] ?? '{}';
                
                $context = json_decode($contextJson, true);
                if (!$context) return null;

                return [
                    'timestamp' => $timestamp,
                    'action_type' => $context['action_type'] ?? 'unknown',
                    'method' => $context['request']['method'] ?? 'GET',
                    'url' => $context['request']['url'] ?? '',
                    'status_code' => $context['response']['status_code'] ?? 200,
                    'duration_ms' => $context['response']['duration_ms'] ?? 0,
                    'is_error' => $context['is_error'] ?? false,
                    'user_email' => $context['request']['user_email'] ?? 'unknown',
                    'raw_context' => $context
                ];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Gerar relatório formatado
     */
    private function generateLogReport(array $logs, string $sessionId): string
    {
        $header = "🎯 DEBUG ACTION LOG REPORT\n";
        $header .= "=====================================\n\n";
        $header .= "📅 Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $header .= "🔑 Session ID: {$sessionId}\n";
        $header .= "👤 User: " . Auth::user()->name . " (" . Auth::user()->email . ")\n";
        $header .= "🏷️  Role: " . (Auth::user()->roles()->first()?->name ?? 'N/A') . "\n";
        $header .= "📊 Total Actions: " . count($logs) . "\n\n";

        $header .= "DETAILED ACTION LOG:\n";
        $header .= "==================\n\n";

        $content = $header;

        foreach ($logs as $index => $log) {
            $errorFlag = $log['is_error'] ? ' ❌' : '';
            $content .= sprintf(
                "%d. [%s] %s %s%s\n",
                $index + 1,
                $log['timestamp'],
                strtoupper($log['method']),
                $log['url'],
                $errorFlag
            );

            $content .= "   Action Type: {$log['action_type']}\n";
            $content .= "   Status: {$log['status_code']}\n";
            $content .= "   Duration: {$log['duration_ms']}ms\n";
            $content .= "   User: {$log['user_email']}\n\n";
        }

        $content .= "\n=== END OF LOG ===\n";

        return $content;
    }
    
    /**
     * Get captured database queries
     */
    public function getDatabaseQueries(Request $request)
    {
        try {
            $dbDebug = new DatabaseDebugService();
            $queries = $dbDebug->getCapturedQueries();
            $stats = $dbDebug->getQueryStatistics();
            
            // Allow access to cached data even when session is not active
            $isActive = session('debug_logger_active', false);
            $sessionId = session('debug_session_id', 'inactive');
            
            return response()->json([
                'success' => true,
                'queries' => $queries,
                'statistics' => $stats,
                'session_id' => $sessionId,
                'session_active' => $isActive,
                'message' => $isActive ? 'Active session' : 'Showing cached data'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getDatabaseQueries', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving database queries',
                'queries' => [],
                'statistics' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get database statistics
     */
    public function getDatabaseStats(Request $request)
    {
        if (!session('debug_logger_active', false)) {
            return response()->json([
                'success' => false,
                'message' => 'Debug session not active',
                'stats' => []
            ]);
        }
        
        $dbDebug = new DatabaseDebugService();
        $stats = $dbDebug->getDatabaseStats();
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'session_id' => session('debug_session_id')
        ]);
    }
    
    /**
     * Clear cached debug data
     */
    public function clearCache(Request $request)
    {
        // Allow clearing cache even if session is not active
        $sessionActive = session('debug_logger_active', false);
        
        $sessionId = session('debug_session_id');
        
        try {
            // Clear cached queries
            Cache::forget('db_debug_queries');
            
            // Clear other debug-related cache if needed
            if ($request->input('clear_all', false)) {
                Cache::forget('db_debug_capturing');
                Cache::forget('db_debug_start_time');
            }
            
            Log::info('Debug cache cleared', [
                'session_id' => $sessionId,
                'user_id' => Auth::id(),
                'cleared_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully',
                'session_id' => $sessionId,
                'session_active' => $sessionActive,
                'cleared_items' => ['db_debug_queries']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing debug cache', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache',
                'error' => $e->getMessage()
            ]);
        }
    }
}