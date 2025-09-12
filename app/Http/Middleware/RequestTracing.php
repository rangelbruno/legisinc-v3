<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestTracing
{
    /**
     * Redis key for metrics buffer
     */
    private const METRICS_BUFFER_KEY = 'metrics:http_durations';
    
    /**
     * Max buffer size before forcing flush
     */
    private const MAX_BUFFER_SIZE = 1000;

    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-Id') ?: (string) Str::uuid();
        $startTime = microtime(true);
        
        // Store request ID in container for global access
        app()->instance('request_id', $requestId);
        
        // Inject request ID into log context
        Log::withContext([
            'request_id' => $requestId,
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        // Process request
        $response = $next($request);
        
        // Collect metrics after response
        $this->collectMetrics($request, $response, $startTime, $requestId);
        
        // Add correlation headers
        $response->headers->set('X-Request-Id', $requestId);
        $response->headers->set('X-Response-Time', round((microtime(true) - $startTime) * 1000, 2) . 'ms');
        
        return $response;
    }

    private function collectMetrics(Request $request, Response $response, float $startTime, string $requestId): void
    {
        $duration = (microtime(true) - $startTime) * 1000; // ms
        $statusCode = $response->getStatusCode();
        $route = $request->route() ? $request->route()->getName() : 'unknown';
        
        // Skip health check endpoint to avoid noise
        if ($route === 'monitoring.health') {
            return;
        }
        
        // Prepare metric data
        $metric = [
            'metric_type' => 'http_request',
            'metric_name' => 'duration_ms',
            'value' => round($duration, 2),
            'tags' => [
                'route' => $route,
                'method' => $request->method(),
                'status_code' => $statusCode,
                'is_error' => $statusCode >= 400,
            ],
            'created_at' => now()->toDateTimeString(),
            'request_id' => $requestId,
        ];
        
        // Buffer metric in Redis for batch processing
        $this->bufferMetric($metric);
        
        // Log performance for slow requests
        if ($duration > 1000 || $statusCode >= 500) {
            Log::channel('monitoring')->warning('slow_or_error_request', [
                'request_id' => $requestId,
                'route' => $route,
                'method' => $request->method(),
                'status_code' => $statusCode,
                'duration_ms' => round($duration, 2),
                'memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ]);
        }
    }

    private function bufferMetric(array $metric): void
    {
        try {
            // Use Redis directly for better performance
            $redis = Cache::store('redis')->getRedis();
            
            // Push to Redis list
            $redis->rpush(self::METRICS_BUFFER_KEY, json_encode($metric));
            
            // Check buffer size
            $bufferSize = $redis->llen(self::METRICS_BUFFER_KEY);
            
            // Force flush if buffer is too large
            if ($bufferSize >= self::MAX_BUFFER_SIZE) {
                // Dispatch job to flush buffer (non-blocking)
                dispatch(function () {
                    app(\App\Services\Monitoring\MetricsFlushService::class)->flush();
                })->afterResponse();
            }
            
            // Set TTL on key to prevent infinite growth if flush fails
            $redis->expire(self::METRICS_BUFFER_KEY, 3600); // 1 hour TTL
            
        } catch (\Exception $e) {
            // Fallback to direct database insert if Redis fails
            try {
                \DB::table('monitoring_metrics')->insert([
                    'metric_type' => $metric['metric_type'],
                    'metric_name' => $metric['metric_name'],
                    'value' => $metric['value'],
                    'tags' => json_encode($metric['tags']),
                    'created_at' => $metric['created_at'],
                ]);
            } catch (\Exception $dbException) {
                // Log but don't fail the request
                Log::warning('Failed to save monitoring metric', [
                    'redis_error' => $e->getMessage(),
                    'db_error' => $dbException->getMessage(),
                    'request_id' => $metric['request_id'],
                ]);
            }
        }
    }
}