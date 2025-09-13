<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Throwable;

class MonitoringFlushRedis extends Command
{
    protected $signature = 'monitoring:flush-redis 
                           {--batch=500 : Number of items to process per batch}
                           {--timeout=5 : Redis BLPOP timeout in seconds}
                           {--queues=* : Specific queues to process (optional)}';
    
    protected $description = 'Flush metrics from Redis queues to Postgres in batches';

    protected array $defaultQueues = [
        'metrics:http',      // request_duration_ms, request_count, error_count
        'metrics:queue',     // backlog, failed, processed
        'metrics:custom',    // space for extensions
    ];

    public function handle(): int
    {
        if (!config('monitoring.metrics.enabled', false)) {
            $this->warn('Monitoring metrics disabled. Check MONITORING_METRICS_ENABLED.');
            return self::SUCCESS;
        }

        $queues = $this->option('queues') ?: $this->defaultQueues;
        $batchSize = (int) $this->option('batch');
        $timeout = (int) $this->option('timeout');

        $totalFlushed = 0;
        
        foreach ($queues as $queue) {
            $flushed = $this->flushQueue($queue, $batchSize, $timeout);
            $totalFlushed += $flushed;
        }

        if ($totalFlushed > 0) {
            $this->info("✅ Monitoring flush complete. Total processed: {$totalFlushed}");
        } else {
            $this->line('No metrics to flush.');
        }

        return self::SUCCESS;
    }

    protected function flushQueue(string $queueKey, int $batchSize, int $timeout): int
    {
        $items = [];
        $startTime = microtime(true);

        // Collect items from Redis queue
        for ($i = 0; $i < $batchSize; $i++) {
            try {
                // Use BLPOP for non-blocking behavior with timeout
                $result = Redis::blpop($queueKey, $timeout);
                
                if (!$result) {
                    break; // No more items or timeout reached
                }

                $payload = json_decode($result[1] ?? 'null', true);
                
                if (!$this->isValidMetric($payload)) {
                    $this->warn("Invalid metric payload in {$queueKey}: " . ($result[1] ?? 'null'));
                    continue;
                }

                $items[] = $this->sanitizeMetric($payload);

            } catch (Throwable $e) {
                Log::warning('Redis read error during flush', [
                    'queue' => $queueKey,
                    'error' => $e->getMessage()
                ]);
                break;
            }
        }

        if (empty($items)) {
            return 0;
        }

        // Insert items to PostgreSQL with error handling
        try {
            $this->insertMetricsBatch($items);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $this->line("📊 Flushed " . count($items) . " metrics from {$queueKey} ({$duration}ms)");
            
            return count($items);

        } catch (Throwable $e) {
            // Critical: Requeue items to prevent data loss
            $this->requeueItems($queueKey, $items);
            
            Log::error('Database error during metrics flush', [
                'queue' => $queueKey,
                'items_count' => count($items),
                'error' => $e->getMessage()
            ]);
            
            $this->error("🔴 DB error for {$queueKey}, requeued " . count($items) . " items");
            
            return 0;
        }
    }

    protected function isValidMetric(?array $payload): bool
    {
        return is_array($payload) 
            && isset($payload['metric_type']) 
            && isset($payload['metric_name'])
            && array_key_exists('value', $payload);
    }

    protected function sanitizeMetric(array $payload): array
    {
        return [
            'metric_type' => $this->truncateString($payload['metric_type'] ?? 'app', 50),
            'metric_name' => $this->truncateString($payload['metric_name'] ?? 'unknown', 100),
            'value' => is_numeric($payload['value']) ? (float) $payload['value'] : null,
            'tags' => json_encode($payload['tags'] ?? []),
            'created_at' => $payload['created_at'] ?? now()->toDateTimeString(),
        ];
    }

    protected function insertMetricsBatch(array $items): void
    {
        // Insert in chunks to handle large batches
        $chunkSize = config('monitoring.metrics.insert_chunk_size', 200);
        
        collect($items)->chunk($chunkSize)->each(function ($chunk) {
            DB::table('monitoring_metrics')->insert($chunk->toArray());
        });
    }

    protected function requeueItems(string $queueKey, array $items): void
    {
        try {
            // Push items back to the end of the queue (RPUSH)
            foreach ($items as $item) {
                Redis::rpush($queueKey, json_encode($item));
            }
        } catch (Throwable $e) {
            Log::critical('Failed to requeue metrics after DB error', [
                'queue' => $queueKey,
                'items_count' => count($items),
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function truncateString(string $text, int $maxLength): string
    {
        return mb_strlen($text) > $maxLength 
            ? mb_substr($text, 0, $maxLength - 1) . '…'
            : $text;
    }
}