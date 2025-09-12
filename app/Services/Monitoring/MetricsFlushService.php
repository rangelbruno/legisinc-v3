<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MetricsFlushService
{
    private const METRICS_BUFFER_KEY = 'metrics:http_durations';
    private const BATCH_SIZE = 100;

    /**
     * Flush buffered metrics from Redis to database
     */
    public function flush(): int
    {
        $redis = Cache::store('redis')->getRedis();
        $flushedCount = 0;
        
        try {
            // Process in batches
            while ($redis->llen(self::METRICS_BUFFER_KEY) > 0) {
                $batch = [];
                
                // Pop batch from Redis
                for ($i = 0; $i < self::BATCH_SIZE; $i++) {
                    $metricJson = $redis->lpop(self::METRICS_BUFFER_KEY);
                    
                    if (!$metricJson) {
                        break;
                    }
                    
                    $metric = json_decode($metricJson, true);
                    if ($metric) {
                        $batch[] = [
                            'metric_type' => $metric['metric_type'],
                            'metric_name' => $metric['metric_name'],
                            'value' => $metric['value'],
                            'tags' => json_encode($metric['tags']),
                            'created_at' => $metric['created_at'],
                        ];
                    }
                }
                
                // Insert batch into database
                if (!empty($batch)) {
                    DB::table('monitoring_metrics')->insert($batch);
                    $flushedCount += count($batch);
                }
            }
            
            if ($flushedCount > 0) {
                Log::channel('monitoring')->info('Metrics flushed to database', [
                    'count' => $flushedCount,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to flush metrics', [
                'error' => $e->getMessage(),
                'flushed_so_far' => $flushedCount,
            ]);
        }
        
        return $flushedCount;
    }

    /**
     * Get current buffer size
     */
    public function getBufferSize(): int
    {
        try {
            $redis = Cache::store('redis')->getRedis();
            return $redis->llen(self::METRICS_BUFFER_KEY);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Clear the buffer (emergency use only)
     */
    public function clearBuffer(): void
    {
        try {
            $redis = Cache::store('redis')->getRedis();
            $redis->del(self::METRICS_BUFFER_KEY);
            
            Log::warning('Metrics buffer cleared manually');
        } catch (\Exception $e) {
            Log::error('Failed to clear metrics buffer', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}