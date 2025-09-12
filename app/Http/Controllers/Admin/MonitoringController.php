<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\DatabaseMonitor;
use App\Services\Monitoring\MetricsFlushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonitoringController extends Controller
{
    private DatabaseMonitor $dbMonitor;

    public function __construct(DatabaseMonitor $dbMonitor)
    {
        $this->dbMonitor = $dbMonitor;
    }

    /**
     * Dashboard principal de monitoramento
     */
    public function index()
    {
        // Cache de 30 segundos para evitar sobrecarga
        $dashboardData = Cache::remember('monitoring.dashboard', 30, function () {
            return [
                'database' => $this->dbMonitor->getDatabaseSummary(),
                'timestamp' => now()->toISOString(),
            ];
        });

        return view('admin.monitoring.index', compact('dashboardData'));
    }

    /**
     * Página dedicada ao monitoramento de banco de dados
     */
    public function database()
    {
        $data = [
            'connections' => $this->dbMonitor->getActiveConnections(),
            'cache_hit_ratio' => $this->dbMonitor->getCacheHitRatio(),
            'table_sizes' => $this->dbMonitor->getTableSizes(),
            'slow_queries' => $this->dbMonitor->getSlowQueries(),
            'locks' => $this->dbMonitor->getLockStatus(),
        ];

        return view('admin.monitoring.database', compact('data'));
    }

    /**
     * API endpoint para atualização em tempo real (AJAX)
     */
    public function apiDatabaseStats()
    {
        // Cache muito curto para dashboards em tempo real
        $stats = Cache::remember('monitoring.db_stats', 10, function () {
            return $this->dbMonitor->getDatabaseSummary();
        });

        return response()->json($stats);
    }

    /**
     * Métricas de performance das últimas 24h
     */
    public function performance()
    {
        // Agregados por hora das últimas 24h
        $performanceData = \DB::table('monitoring_metrics')
            ->where('metric_type', 'http_request')
            ->where('metric_name', 'duration_ms')
            ->where('created_at', '>=', now()->subDay())
            ->selectRaw("
                DATE_TRUNC('hour', created_at) as hour,
                COUNT(*) as requests,
                ROUND(AVG(value), 2) as avg_response_time,
                ROUND(PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY value), 2) as p50,
                ROUND(PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY value), 2) as p95,
                ROUND(PERCENTILE_CONT(0.99) WITHIN GROUP (ORDER BY value), 2) as p99,
                COUNT(CASE WHEN JSON_EXTRACT_PATH_TEXT(tags, 'status_code')::int >= 400 THEN 1 END) as errors
            ")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('admin.monitoring.performance', compact('performanceData'));
    }

    /**
     * Busca de logs com filtros
     */
    public function logs(Request $request)
    {
        $query = \DB::table('monitoring_logs');

        // Filtros
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('request_id')) {
            $query->where('request_id', $request->request_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString();

        return view('admin.monitoring.logs', compact('logs'));
    }

    /**
     * Configurações de alertas
     */
    public function alerts()
    {
        $activeAlerts = \DB::table('monitoring_alerts')
            ->where('resolved', false)
            ->orderBy('severity')
            ->orderBy('created_at', 'desc')
            ->get();

        $alertStats = [
            'total_active' => $activeAlerts->count(),
            'critical' => $activeAlerts->where('severity', 'critical')->count(),
            'high' => $activeAlerts->where('severity', 'high')->count(),
            'medium' => $activeAlerts->where('severity', 'medium')->count(),
            'low' => $activeAlerts->where('severity', 'low')->count(),
        ];

        return view('admin.monitoring.alerts', compact('activeAlerts', 'alertStats'));
    }

    /**
     * Health check endpoint para monitoring externo
     */
    public function health()
    {
        try {
            // Verificações básicas
            $checks = [
                'database' => $this->checkDatabase(),
                'redis' => $this->checkRedis(),
                'storage' => $this->checkStorage(),
            ];

            $allHealthy = collect($checks)->every(function ($check) {
                return $check['status'] === 'ok';
            });

            return response()->json([
                'status' => $allHealthy ? 'healthy' : 'unhealthy',
                'timestamp' => now()->toISOString(),
                'checks' => $checks,
            ], $allHealthy ? 200 : 503);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    private function checkDatabase(): array
    {
        try {
            \DB::select('SELECT 1');
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkRedis(): array
    {
        try {
            \Cache::put('health_check_test', 'ok', 10);
            $result = \Cache::get('health_check_test');
            \Cache::forget('health_check_test');
            
            if ($result === 'ok') {
                return ['status' => 'ok', 'message' => 'Cache (Redis) connection successful'];
            }
            return ['status' => 'error', 'message' => 'Cache test failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkStorage(): array
    {
        try {
            $testFile = 'health_check_' . time();
            \Storage::put($testFile, 'test');
            \Storage::delete($testFile);
            return ['status' => 'ok', 'message' => 'Storage write/delete successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Server-Sent Events stream for real-time monitoring
     */
    public function stream(): StreamedResponse
    {
        return response()->stream(function () {
            // Set no time limit for this script
            set_time_limit(0);
            
            // Disable output buffering
            ob_implicit_flush(true);
            ob_end_flush();
            
            $iteration = 0;
            
            while (true) {
                $iteration++;
                
                try {
                    // Collect current metrics
                    $payload = [
                        'timestamp' => now()->toIso8601String(),
                        'iteration' => $iteration,
                        'database' => $this->dbMonitor->getDatabaseSummary(),
                        'performance' => $this->getRecentPerformanceMetrics(),
                        'buffer_size' => app(MetricsFlushService::class)->getBufferSize(),
                        'active_alerts' => $this->getActiveAlertsCount(),
                    ];
                    
                    // Send heartbeat event
                    echo "event: heartbeat\n";
                    echo "data: " . json_encode($payload) . "\n\n";
                    
                    // Check for slow queries every 5 iterations (25 seconds)
                    if ($iteration % 5 === 0) {
                        $slowQueries = $this->dbMonitor->getSlowQueries(500);
                        if (!isset($slowQueries['error']) && count($slowQueries['slow_queries'] ?? []) > 0) {
                            echo "event: slow_queries\n";
                            echo "data: " . json_encode($slowQueries) . "\n\n";
                        }
                    }
                    
                    // Flush output
                    flush();
                    
                } catch (\Exception $e) {
                    // Send error event
                    echo "event: error\n";
                    echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                    flush();
                }
                
                // Wait 5 seconds before next update
                sleep(5);
                
                // Stop after 5 minutes to prevent infinite connections
                if ($iteration >= 60) {
                    echo "event: close\n";
                    echo "data: " . json_encode(['message' => 'Connection closing after 5 minutes']) . "\n\n";
                    flush();
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // Disable Nginx buffering
        ]);
    }

    private function getRecentPerformanceMetrics(): array
    {
        try {
            // Get average response time for last 5 minutes
            $metrics = \DB::table('monitoring_metrics')
                ->where('metric_type', 'http_request')
                ->where('metric_name', 'duration_ms')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->selectRaw('
                    COUNT(*) as total_requests,
                    AVG(value) as avg_response_time,
                    MIN(value) as min_response_time,
                    MAX(value) as max_response_time
                ')
                ->first();
            
            return [
                'total_requests' => $metrics->total_requests ?? 0,
                'avg_response_time_ms' => round($metrics->avg_response_time ?? 0, 2),
                'min_response_time_ms' => round($metrics->min_response_time ?? 0, 2),
                'max_response_time_ms' => round($metrics->max_response_time ?? 0, 2),
            ];
        } catch (\Exception $e) {
            return ['error' => 'Unable to fetch performance metrics'];
        }
    }

    private function getActiveAlertsCount(): int
    {
        try {
            return \DB::table('monitoring_alerts')
                ->where('resolved', false)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}