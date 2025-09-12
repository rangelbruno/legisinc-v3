<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlertService
{
    /**
     * Check system metrics and create alerts if thresholds are exceeded
     */
    public function checkAndNotify(): array
    {
        $alerts = [];
        $snapshot = $this->collectSystemSnapshot();
        
        // Check error rate threshold
        if (($snapshot['error_rate_pct'] ?? 0) > config('monitoring.alerts.error_rate_threshold', 1)) {
            $alerts[] = $this->createAlert(
                'error_rate_high',
                'high',
                "Error rate exceeded threshold: {$snapshot['error_rate_pct']}%",
                $snapshot
            );
        }
        
        // Check P95 latency threshold
        if (($snapshot['p95_latency_ms'] ?? 0) > config('monitoring.alerts.p95_threshold', 500)) {
            $alerts[] = $this->createAlert(
                'latency_high',
                'medium',
                "P95 latency exceeded threshold: {$snapshot['p95_latency_ms']}ms",
                $snapshot
            );
        }
        
        // Check database connections
        $dbConnections = $snapshot['database']['connections'] ?? [];
        $usagePercent = $dbConnections['usage_percent'] ?? 0;
        if ($usagePercent > config('monitoring.alerts.db_connection_threshold', 80)) {
            $alerts[] = $this->createAlert(
                'db_connections_high',
                'high',
                "Database connections near limit: {$usagePercent}%",
                $dbConnections
            );
        }
        
        // Check cache hit ratio
        $cacheHitRatio = $snapshot['database']['cache_hit_ratio'] ?? 100;
        if ($cacheHitRatio < config('monitoring.alerts.cache_hit_threshold', 90)) {
            $alerts[] = $this->createAlert(
                'cache_hit_low',
                'low',
                "Cache hit ratio below threshold: {$cacheHitRatio}%",
                ['cache_hit_ratio' => $cacheHitRatio]
            );
        }
        
        // Check blocked queries
        $blockedQueries = $snapshot['database']['blocked_queries'] ?? 0;
        if ($blockedQueries > config('monitoring.alerts.blocked_queries_threshold', 5)) {
            $alerts[] = $this->createAlert(
                'queries_blocked',
                'critical',
                "Multiple queries blocked: {$blockedQueries}",
                ['blocked_queries' => $blockedQueries]
            );
        }
        
        // Process and notify for each alert
        foreach ($alerts as $alert) {
            $this->persistAlert($alert);
            $this->notifyChannels($alert);
        }
        
        return $alerts;
    }

    /**
     * Collect current system snapshot
     */
    private function collectSystemSnapshot(): array
    {
        $dbMonitor = app(DatabaseMonitor::class);
        
        // Get performance metrics from last 5 minutes
        $performanceMetrics = DB::table('monitoring_metrics')
            ->where('metric_type', 'http_request')
            ->where('metric_name', 'duration_ms')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->selectRaw("
                COUNT(*) as total_requests,
                AVG(value) as avg_latency,
                PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY value) as p95_latency,
                COUNT(CASE WHEN (tags->>'status_code')::int >= 500 THEN 1 END) as error_count
            ")
            ->first();
        
        $errorRate = $performanceMetrics->total_requests > 0 
            ? round(($performanceMetrics->error_count / $performanceMetrics->total_requests) * 100, 2)
            : 0;
        
        return [
            'timestamp' => now()->toIso8601String(),
            'error_rate_pct' => $errorRate,
            'p95_latency_ms' => round($performanceMetrics->p95_latency ?? 0, 2),
            'avg_latency_ms' => round($performanceMetrics->avg_latency ?? 0, 2),
            'total_requests' => $performanceMetrics->total_requests ?? 0,
            'database' => $dbMonitor->getDatabaseSummary(),
        ];
    }

    /**
     * Create alert structure
     */
    private function createAlert(string $type, string $severity, string $message, array $details): array
    {
        return [
            'alert_type' => $type,
            'severity' => $severity,
            'message' => $message,
            'details' => $details,
            'created_at' => now(),
        ];
    }

    /**
     * Persist alert to database
     */
    private function persistAlert(array $alert): void
    {
        try {
            // Check if similar alert already exists and is unresolved
            $existingAlert = DB::table('monitoring_alerts')
                ->where('alert_type', $alert['alert_type'])
                ->where('resolved', false)
                ->where('created_at', '>=', now()->subHour())
                ->first();
            
            // Don't create duplicate alerts within an hour
            if (!$existingAlert) {
                DB::table('monitoring_alerts')->insert([
                    'alert_type' => $alert['alert_type'],
                    'severity' => $alert['severity'],
                    'message' => $alert['message'],
                    'details' => json_encode($alert['details']),
                    'resolved' => false,
                    'created_at' => $alert['created_at'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to persist alert', [
                'alert' => $alert,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notifications through configured channels
     */
    private function notifyChannels(array $alert): void
    {
        // Slack notification for high/critical alerts
        if (in_array($alert['severity'], ['high', 'critical'])) {
            $this->notifySlack($alert);
        }
        
        // Log all alerts
        Log::channel('monitoring')->warning('Alert triggered', $alert);
    }

    /**
     * Send Slack notification
     */
    private function notifySlack(array $alert): void
    {
        $webhookUrl = config('monitoring.notifications.slack.webhook_url');
        
        if (!$webhookUrl) {
            return;
        }
        
        try {
            $emoji = match($alert['severity']) {
                'critical' => '🚨',
                'high' => '⚠️',
                'medium' => '📊',
                default => 'ℹ️'
            };
            
            $color = match($alert['severity']) {
                'critical' => '#FF0000',
                'high' => '#FFA500',
                'medium' => '#FFFF00',
                default => '#808080'
            };
            
            Http::post($webhookUrl, [
                'text' => "{$emoji} *[{$alert['severity']}]* Monitoring Alert",
                'attachments' => [
                    [
                        'color' => $color,
                        'fields' => [
                            [
                                'title' => 'Alert Type',
                                'value' => $alert['alert_type'],
                                'short' => true,
                            ],
                            [
                                'title' => 'Severity',
                                'value' => $alert['severity'],
                                'short' => true,
                            ],
                            [
                                'title' => 'Message',
                                'value' => $alert['message'],
                                'short' => false,
                            ],
                            [
                                'title' => 'Time',
                                'value' => now()->toDateTimeString(),
                                'short' => true,
                            ],
                        ],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to send Slack notification', [
                'error' => $e->getMessage(),
                'alert' => $alert,
            ]);
        }
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert(int $alertId): bool
    {
        try {
            return DB::table('monitoring_alerts')
                ->where('id', $alertId)
                ->update([
                    'resolved' => true,
                    'resolved_at' => now(),
                ]) > 0;
        } catch (\Exception $e) {
            Log::error('Failed to resolve alert', [
                'alert_id' => $alertId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get active alerts
     */
    public function getActiveAlerts(): array
    {
        return DB::table('monitoring_alerts')
            ->where('resolved', false)
            ->orderBy('severity')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}