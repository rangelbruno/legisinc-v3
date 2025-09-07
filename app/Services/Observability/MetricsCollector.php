<?php

namespace App\Services\Observability;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MetricsCollector
{
    private const METRICS_PREFIX = 'legisinc_';
    private const CACHE_TTL = 300; // 5 minutos

    /**
     * Incrementa contador de métrica
     */
    public static function increment(string $metric, array $labels = [], int $value = 1): void
    {
        $metricKey = self::buildMetricKey($metric, $labels);
        
        // Incrementar no cache para coleta posterior
        Cache::increment($metricKey, $value);
        
        // Log estruturado para agregação externa
        Log::info('metric.increment', [
            'metric' => self::METRICS_PREFIX . $metric,
            'labels' => $labels,
            'value' => $value,
            'timestamp' => now()->timestamp
        ]);
    }

    /**
     * Registra valor de gauge (medida atual)
     */
    public static function gauge(string $metric, float $value, array $labels = []): void
    {
        $metricKey = self::buildMetricKey($metric, $labels);
        
        // Armazenar valor atual
        Cache::put($metricKey . '_gauge', $value, self::CACHE_TTL);
        
        Log::info('metric.gauge', [
            'metric' => self::METRICS_PREFIX . $metric,
            'labels' => $labels,
            'value' => $value,
            'timestamp' => now()->timestamp
        ]);
    }

    /**
     * Registra duração de operação
     */
    public static function timing(string $metric, float $durationMs, array $labels = []): void
    {
        Log::info('metric.timing', [
            'metric' => self::METRICS_PREFIX . $metric,
            'labels' => $labels,
            'duration_ms' => $durationMs,
            'timestamp' => now()->timestamp
        ]);
    }

    /**
     * Inicia timer para operação
     */
    public static function startTimer(): float
    {
        return microtime(true);
    }

    /**
     * Finaliza timer e registra métrica
     */
    public static function endTimer(float $start, string $metric, array $labels = []): void
    {
        $duration = (microtime(true) - $start) * 1000; // Em millisegundos
        self::timing($metric, $duration, $labels);
    }

    /**
     * Obter todas as métricas coletadas
     */
    public static function getAllMetrics(): array
    {
        $pattern = self::METRICS_PREFIX . '*';
        $keys = Cache::getRedis()->keys($pattern);
        $metrics = [];
        
        foreach ($keys as $key) {
            $value = Cache::get($key);
            if ($value !== null) {
                $metrics[str_replace(self::METRICS_PREFIX, '', $key)] = $value;
            }
        }
        
        return $metrics;
    }

    /**
     * Limpar métricas antigas
     */
    public static function cleanup(): void
    {
        $pattern = self::METRICS_PREFIX . '*';
        $keys = Cache::getRedis()->keys($pattern);
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        Log::info('metrics.cleanup', ['keys_removed' => count($keys)]);
    }

    /**
     * Constrói chave única para métrica com labels
     */
    private static function buildMetricKey(string $metric, array $labels): string
    {
        $labelString = '';
        if (!empty($labels)) {
            ksort($labels);
            $labelString = '_' . http_build_query($labels, '', '_');
        }
        
        return self::METRICS_PREFIX . $metric . $labelString;
    }
}

/**
 * Trait para adicionar observabilidade a classes
 */
trait HasObservability
{
    /**
     * Mede tempo de execução de uma operação
     */
    protected function measured(string $operation, callable $callback, array $labels = [])
    {
        $start = MetricsCollector::startTimer();
        
        try {
            $result = $callback();
            
            MetricsCollector::increment("{$operation}_total", array_merge($labels, ['status' => 'success']));
            MetricsCollector::endTimer($start, "{$operation}_duration_ms", $labels);
            
            return $result;
            
        } catch (\Exception $e) {
            MetricsCollector::increment("{$operation}_total", array_merge($labels, ['status' => 'error']));
            MetricsCollector::endTimer($start, "{$operation}_duration_ms", array_merge($labels, ['status' => 'error']));
            
            throw $e;
        }
    }
}