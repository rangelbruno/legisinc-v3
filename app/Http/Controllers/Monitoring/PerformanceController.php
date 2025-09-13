<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:monitoring.view']);
    }

    /**
     * Get HTTP latency percentiles for the last hour
     */
    public function pxxLastHour(): JsonResponse
    {
        $cacheKey = 'monitoring:performance:pxx_1h';
        
        $data = Cache::remember($cacheKey, 60, function () {
            $rows = DB::table('vw_http_latency_last_1h')
                ->orderByDesc('p95')
                ->limit(20)
                ->get()
                ->map(function ($row) {
                    return [
                        'route' => $row->route,
                        'method' => $row->method,
                        'p50' => round((float) $row->p50, 1),
                        'p95' => round((float) $row->p95, 1),
                        'p99' => round((float) $row->p99, 1),
                        'avg_duration' => round((float) $row->avg_duration, 1),
                        'min_duration' => round((float) $row->min_duration, 1),
                        'max_duration' => round((float) $row->max_duration, 1),
                        'request_count' => (int) $row->request_count,
                        'slow_requests' => (int) $row->slow_requests,
                    ];
                });

            return $rows;
        });

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'timeframe' => 'last_1h',
            'cache_ttl_seconds' => 60,
            'rows' => $data,
            'total_routes' => count($data),
        ]);
    }

    /**
     * Get HTTP latency percentiles for the last 24 hours
     */
    public function pxxLast24Hours(): JsonResponse
    {
        $cacheKey = 'monitoring:performance:pxx_24h';
        
        $data = Cache::remember($cacheKey, 300, function () {
            return DB::table('vw_http_latency_last_24h')
                ->orderByDesc('p95')
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'route' => $row->route,
                        'method' => $row->method,
                        'p50' => round((float) $row->p50, 1),
                        'p95' => round((float) $row->p95, 1),
                        'p99' => round((float) $row->p99, 1),
                        'avg_duration' => round((float) $row->avg_duration, 1),
                        'request_count' => (int) $row->request_count,
                        'slow_requests' => (int) $row->slow_requests,
                        'error_5xx_count' => (int) $row->error_5xx_count,
                    ];
                });
        });

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'timeframe' => 'last_24h',
            'cache_ttl_seconds' => 300,
            'rows' => $data,
            'total_routes' => count($data),
        ]);
    }

    /**
     * Get error rates by route
     */
    public function errorRatesLastHour(): JsonResponse
    {
        $cacheKey = 'monitoring:performance:error_rates_1h';
        
        $data = Cache::remember($cacheKey, 120, function () {
            return DB::table('vw_http_error_rates_last_1h')
                ->orderByDesc('total_error_rate')
                ->limit(30)
                ->get()
                ->map(function ($row) {
                    return [
                        'route' => $row->route,
                        'method' => $row->method,
                        'total_requests' => (int) $row->total_requests,
                        'error_4xx_count' => (int) $row->error_4xx_count,
                        'error_5xx_count' => (int) $row->error_5xx_count,
                        'error_4xx_rate' => (float) $row->error_4xx_rate,
                        'error_5xx_rate' => (float) $row->error_5xx_rate,
                        'total_error_rate' => (float) $row->total_error_rate,
                        'health_status' => $this->getHealthStatus((float) $row->total_error_rate),
                    ];
                });
        });

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'timeframe' => 'last_1h',
            'cache_ttl_seconds' => 120,
            'rows' => $data,
            'total_routes' => count($data),
        ]);
    }

    /**
     * Get throughput metrics by route
     */
    public function throughputLastHour(): JsonResponse
    {
        $cacheKey = 'monitoring:performance:throughput_1h';
        
        $data = Cache::remember($cacheKey, 60, function () {
            return DB::table('vw_http_throughput_last_1h')
                ->orderByDesc('total_requests')
                ->limit(30)
                ->get()
                ->map(function ($row) {
                    return [
                        'route' => $row->route,
                        'method' => $row->method,
                        'total_requests' => (int) $row->total_requests,
                        'requests_per_minute' => (float) $row->requests_per_minute,
                        'first_request' => $row->first_request,
                        'last_request' => $row->last_request,
                        'traffic_level' => $this->getTrafficLevel((int) $row->total_requests),
                    ];
                });
        });

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'timeframe' => 'last_1h',
            'cache_ttl_seconds' => 60,
            'rows' => $data,
            'total_routes' => count($data),
        ]);
    }

    /**
     * Get monitoring system overview
     */
    public function overview(): JsonResponse
    {
        $cacheKey = 'monitoring:performance:overview';
        
        $data = Cache::remember($cacheKey, 30, function () {
            $overview = DB::table('vw_monitoring_overview')->get()->keyBy('timeframe');
            
            $summary = [];
            foreach (['last_1h', 'last_24h'] as $timeframe) {
                $row = $overview->get($timeframe);
                if ($row) {
                    $summary[$timeframe] = [
                        'total_metrics' => (int) $row->total_metrics,
                        'unique_routes' => (int) $row->unique_routes,
                        'avg_response_time' => round((float) $row->avg_response_time, 1),
                        'p95_response_time' => round((float) $row->p95_response_time, 1),
                        'slow_requests' => (int) $row->slow_requests,
                        'server_errors' => (int) $row->server_errors,
                        'period_start' => $row->period_start,
                        'period_end' => $row->period_end,
                        'health_score' => $this->calculateHealthScore($row),
                    ];
                }
            }
            
            return $summary;
        });

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'cache_ttl_seconds' => 30,
            'overview' => $data,
        ]);
    }

    /**
     * Get slow queries analysis (if debug mode is enabled)
     */
    public function slowQueries(Request $request): JsonResponse
    {
        if (!auth()->user()->can('monitoring.debug')) {
            abort(403, 'Debug permission required for slow query analysis');
        }

        $threshold = (int) $request->get('threshold', 1000); // Default 1000ms
        $limit = min((int) $request->get('limit', 50), 100); // Max 100 results

        $slowQueries = DB::table('monitoring_metrics')
            ->select([
                DB::raw("(tags->>'route') AS route"),
                DB::raw("(tags->>'method') AS method"),
                'value AS duration_ms',
                'created_at',
                DB::raw("(tags->>'status') AS status_code")
            ])
            ->where('metric_name', 'request_duration_ms')
            ->where('value', '>', $threshold)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderByDesc('value')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'route' => $row->route ?: 'unknown',
                    'method' => $row->method ?: 'GET',
                    'duration_ms' => round((float) $row->duration_ms, 1),
                    'status_code' => (int) $row->status_code,
                    'created_at' => $row->created_at,
                    'severity' => $this->getSlowQuerySeverity((float) $row->duration_ms),
                ];
            });

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'threshold_ms' => $threshold,
            'limit' => $limit,
            'timeframe' => 'last_24h',
            'slow_queries' => $slowQueries,
            'total_found' => count($slowQueries),
        ]);
    }

    /**
     * Get performance summary for a specific route
     */
    public function routeDetails(Request $request): JsonResponse
    {
        $routeName = $request->get('route');
        if (!$routeName) {
            return response()->json(['error' => 'Route parameter required'], 400);
        }

        $cacheKey = "monitoring:performance:route_details:" . md5($routeName);
        
        $data = Cache::remember($cacheKey, 120, function () use ($routeName) {
            // Get 1-hour stats
            $hourlyStats = DB::table('vw_http_latency_last_1h')
                ->where('route', $routeName)
                ->first();

            // Get 24-hour stats  
            $dailyStats = DB::table('vw_http_latency_last_24h')
                ->where('route', $routeName)
                ->first();

            // Get error rates
            $errorRates = DB::table('vw_http_error_rates_last_1h')
                ->where('route', $routeName)
                ->first();

            // Get throughput
            $throughput = DB::table('vw_http_throughput_last_1h')
                ->where('route', $routeName)
                ->first();

            return [
                'route' => $routeName,
                'hourly_stats' => $hourlyStats ? $this->formatLatencyStats($hourlyStats) : null,
                'daily_stats' => $dailyStats ? $this->formatLatencyStats($dailyStats) : null,
                'error_rates' => $errorRates ? $this->formatErrorRates($errorRates) : null,
                'throughput' => $throughput ? $this->formatThroughput($throughput) : null,
            ];
        });

        if (!$data['hourly_stats'] && !$data['daily_stats']) {
            return response()->json(['error' => 'No data found for route'], 404);
        }

        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'cache_ttl_seconds' => 120,
            'data' => $data,
        ]);
    }

    // Helper methods

    protected function getHealthStatus(float $errorRate): string
    {
        if ($errorRate <= 1.0) return 'healthy';
        if ($errorRate <= 5.0) return 'warning';
        return 'critical';
    }

    protected function getTrafficLevel(int $requestCount): string
    {
        if ($requestCount >= 1000) return 'high';
        if ($requestCount >= 100) return 'medium';
        return 'low';
    }

    protected function getSlowQuerySeverity(float $durationMs): string
    {
        if ($durationMs >= 10000) return 'critical';  // 10s+
        if ($durationMs >= 5000) return 'high';       // 5-10s
        if ($durationMs >= 2000) return 'medium';     // 2-5s
        return 'low';                                 // 1-2s
    }

    protected function calculateHealthScore($row): int
    {
        $score = 100;
        
        // Penalize high response times
        $p95 = (float) $row->p95_response_time;
        if ($p95 > 500) $score -= min(30, ($p95 - 500) / 100 * 5);
        
        // Penalize server errors
        $errorRate = $row->total_metrics > 0 ? ($row->server_errors / $row->total_metrics * 100) : 0;
        $score -= min(40, $errorRate * 8);
        
        // Penalize slow requests
        $slowRate = $row->total_metrics > 0 ? ($row->slow_requests / $row->total_metrics * 100) : 0;
        $score -= min(20, $slowRate * 4);
        
        return max(0, min(100, (int) $score));
    }

    protected function formatLatencyStats($stats): array
    {
        return [
            'p50' => round((float) $stats->p50, 1),
            'p95' => round((float) $stats->p95, 1),
            'p99' => round((float) $stats->p99, 1),
            'avg_duration' => round((float) $stats->avg_duration, 1),
            'request_count' => (int) $stats->request_count,
            'slow_requests' => (int) $stats->slow_requests,
        ];
    }

    protected function formatErrorRates($errorRates): array
    {
        return [
            'total_requests' => (int) $errorRates->total_requests,
            'error_4xx_rate' => (float) $errorRates->error_4xx_rate,
            'error_5xx_rate' => (float) $errorRates->error_5xx_rate,
            'total_error_rate' => (float) $errorRates->total_error_rate,
            'health_status' => $this->getHealthStatus((float) $errorRates->total_error_rate),
        ];
    }

    protected function formatThroughput($throughput): array
    {
        return [
            'total_requests' => (int) $throughput->total_requests,
            'requests_per_minute' => (float) $throughput->requests_per_minute,
            'traffic_level' => $this->getTrafficLevel((int) $throughput->total_requests),
        ];
    }
}