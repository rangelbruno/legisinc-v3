<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Throwable;

class AlertService
{
    const CACHE_PREFIX = 'monitoring:alerts:';
    const ALERT_SUPPRESSION_TTL = 300; // 5 minutes

    /**
     * Evaluate all monitoring rules and send notifications
     */
    public function evaluateAndNotify(): int
    {
        if (!config('monitoring.alerts.enabled', false)) {
            return 0;
        }

        $alertCount = 0;
        $snapshot = $this->createSystemSnapshot();
        
        foreach ($this->getAlertRules() as $rule) {
            try {
                $breach = $rule['check']($snapshot);
                
                if ($breach && !$this->isAlertSuppressed($rule['type'])) {
                    $alert = [
                        'alert_type' => $rule['type'],
                        'severity' => $rule['severity'],
                        'message' => $rule['message']($snapshot),
                        'details' => $breach,
                        'rule_config' => $rule['config'] ?? [],
                        'snapshot_time' => now()->toIso8601String(),
                    ];
                    
                    $this->persistAlert($alert);
                    $this->sendNotifications($alert);
                    $this->suppressAlert($rule['type']);
                    
                    $alertCount++;
                    
                    Log::warning('Monitoring alert triggered', [
                        'alert_type' => $rule['type'],
                        'severity' => $rule['severity'],
                        'details' => $breach
                    ]);
                }
            } catch (Throwable $e) {
                Log::error('Error evaluating alert rule', [
                    'rule_type' => $rule['type'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        if ($alertCount > 0) {
            Log::info("Monitoring alerts evaluation complete", [
                'total_triggered' => $alertCount,
                'snapshot_time' => $snapshot['timestamp']
            ]);
        }

        return $alertCount;
    }

    /**
     * Create system snapshot with key metrics
     */
    protected function createSystemSnapshot(): array
    {
        $snapshot = [
            'timestamp' => now()->toIso8601String(),
            'error_rate_5m' => $this->calculateErrorRate5m(),
            'p95_latency_10m' => $this->calculateP95Latency10m(),
            'queue_backlog' => $this->calculateQueueBacklog(),
            'slow_requests_1h' => $this->calculateSlowRequests1h(),
            'database_connections' => $this->getDatabaseConnectionCount(),
            'memory_usage_mb' => $this->getMemoryUsage(),
            'disk_usage_percent' => $this->getDiskUsage(),
        ];

        Log::debug('System snapshot created', $snapshot);
        
        return $snapshot;
    }

    /**
     * Get configurable alert rules
     */
    protected function getAlertRules(): array
    {
        $config = config('monitoring.alerts');
        
        return [
            [
                'type' => 'error_rate_high',
                'severity' => 'high',
                'config' => ['threshold' => $config['thresholds']['error_rate'] ?? 5.0],
                'message' => fn($s) => "Taxa de erro elevada: {$s['error_rate_5m']}% (últimos 5min). Limite: " . ($config['thresholds']['error_rate'] ?? 5.0) . "%",
                'check' => function ($snapshot) use ($config) {
                    $threshold = $config['thresholds']['error_rate'] ?? 5.0;
                    return $snapshot['error_rate_5m'] > $threshold 
                        ? ['error_rate_pct' => $snapshot['error_rate_5m'], 'threshold' => $threshold]
                        : null;
                },
            ],
            [
                'type' => 'latency_p95_high',
                'severity' => 'high',
                'config' => ['threshold' => $config['thresholds']['response_time'] ?? 2000],
                'message' => fn($s) => "P95 latência elevada: {$s['p95_latency_10m']['value']}ms na rota '{$s['p95_latency_10m']['route']}' (últimos 10min)",
                'check' => function ($snapshot) use ($config) {
                    $threshold = $config['thresholds']['response_time'] ?? 2000;
                    $p95Data = $snapshot['p95_latency_10m'];
                    return $p95Data['value'] > $threshold ? $p95Data : null;
                },
            ],
            [
                'type' => 'queue_backlog_high',
                'severity' => 'medium',
                'config' => ['threshold' => 1000],
                'message' => fn($s) => "Backlog de fila elevado: {$s['queue_backlog']} jobs pendentes",
                'check' => function ($snapshot) {
                    return $snapshot['queue_backlog'] > 1000 
                        ? ['backlog_count' => $snapshot['queue_backlog']]
                        : null;
                },
            ],
            [
                'type' => 'slow_requests_excessive',
                'severity' => 'medium',
                'config' => ['threshold' => 50],
                'message' => fn($s) => "Muitas requisições lentas: {$s['slow_requests_1h']} requests >1s (última 1h)",
                'check' => function ($snapshot) {
                    return $snapshot['slow_requests_1h'] > 50
                        ? ['slow_count' => $snapshot['slow_requests_1h']]
                        : null;
                },
            ],
            [
                'type' => 'memory_usage_high',
                'severity' => 'medium',
                'config' => ['threshold' => $config['thresholds']['memory_usage'] ?? 85],
                'message' => fn($s) => "Uso de memória elevado: {$s['memory_usage_mb']}MB",
                'check' => function ($snapshot) use ($config) {
                    $thresholdMB = 1024; // 1GB default threshold
                    return $snapshot['memory_usage_mb'] > $thresholdMB
                        ? ['memory_mb' => $snapshot['memory_usage_mb'], 'threshold_mb' => $thresholdMB]
                        : null;
                },
            ],
            [
                'type' => 'disk_usage_high',
                'severity' => 'medium',
                'config' => ['threshold' => $config['thresholds']['disk_usage'] ?? 90],
                'message' => fn($s) => "Uso de disco elevado: {$s['disk_usage_percent']}%",
                'check' => function ($snapshot) use ($config) {
                    $threshold = $config['thresholds']['disk_usage'] ?? 90;
                    return $snapshot['disk_usage_percent'] > $threshold
                        ? ['disk_percent' => $snapshot['disk_usage_percent'], 'threshold' => $threshold]
                        : null;
                },
            ],
        ];
    }

    /**
     * Calculate error rate for last 5 minutes
     */
    protected function calculateErrorRate5m(): float
    {
        $result = DB::selectOne("
            WITH error_stats AS (
                SELECT
                    COUNT(*) as total_requests,
                    COUNT(CASE WHEN (tags->>'status')::text ~ '^5' THEN 1 END) as error_5xx_count
                FROM monitoring_metrics
                WHERE metric_name IN ('request_duration_ms', 'request_count')
                  AND created_at >= NOW() - INTERVAL '5 minutes'
            )
            SELECT 
                CASE 
                    WHEN total_requests = 0 THEN 0 
                    ELSE ROUND((error_5xx_count::float / total_requests * 100)::numeric, 2)
                END as error_rate
            FROM error_stats
        ");

        return (float) ($result->error_rate ?? 0.0);
    }

    /**
     * Calculate P95 latency for last 10 minutes
     */
    protected function calculateP95Latency10m(): array
    {
        $result = DB::selectOne("
            SELECT 
                COALESCE((tags->>'route')::text, 'unknown') AS route,
                percentile_disc(0.95) WITHIN GROUP (ORDER BY value) AS p95_latency
            FROM monitoring_metrics
            WHERE metric_name = 'request_duration_ms'
              AND created_at >= NOW() - INTERVAL '10 minutes'
              AND value IS NOT NULL
            GROUP BY (tags->>'route')
            ORDER BY p95_latency DESC
            LIMIT 1
        ");

        return [
            'route' => $result->route ?? 'unknown',
            'value' => (int) ($result->p95_latency ?? 0)
        ];
    }

    /**
     * Calculate queue backlog (placeholder - implement based on your queue system)
     */
    protected function calculateQueueBacklog(): int
    {
        try {
            // Example for Laravel Horizon
            $result = DB::selectOne("
                SELECT COALESCE(SUM(COALESCE((tags->>'backlog')::int, 0)), 0) AS total_backlog
                FROM monitoring_metrics
                WHERE metric_name = 'queue_backlog'
                  AND created_at >= NOW() - INTERVAL '10 minutes'
            ");

            return (int) ($result->total_backlog ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * Calculate slow requests in last hour
     */
    protected function calculateSlowRequests1h(): int
    {
        $result = DB::selectOne("
            SELECT COUNT(*) as slow_count
            FROM monitoring_metrics
            WHERE metric_name = 'request_duration_ms'
              AND value > 1000
              AND created_at >= NOW() - INTERVAL '1 hour'
        ");

        return (int) ($result->slow_count ?? 0);
    }

    /**
     * Get database connection count
     */
    protected function getDatabaseConnectionCount(): int
    {
        try {
            $result = DB::selectOne("
                SELECT COUNT(*) as connection_count
                FROM pg_stat_activity
                WHERE datname = current_database()
                  AND state = 'active'
            ");

            return (int) ($result->connection_count ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * Get memory usage in MB
     */
    protected function getMemoryUsage(): int
    {
        $memoryBytes = memory_get_peak_usage(true);
        return (int) round($memoryBytes / 1024 / 1024);
    }

    /**
     * Get disk usage percentage
     */
    protected function getDiskUsage(): float
    {
        try {
            $path = storage_path();
            $totalBytes = disk_total_space($path);
            $freeBytes = disk_free_space($path);
            
            if ($totalBytes && $freeBytes) {
                $usedBytes = $totalBytes - $freeBytes;
                return round(($usedBytes / $totalBytes) * 100, 1);
            }
            
            return 0.0;
        } catch (Throwable $e) {
            return 0.0;
        }
    }

    /**
     * Check if alert is suppressed (to avoid spam)
     */
    protected function isAlertSuppressed(string $alertType): bool
    {
        $key = self::CACHE_PREFIX . 'suppression:' . $alertType;
        return Cache::has($key);
    }

    /**
     * Suppress alert for a period
     */
    protected function suppressAlert(string $alertType): void
    {
        $key = self::CACHE_PREFIX . 'suppression:' . $alertType;
        Cache::put($key, true, self::ALERT_SUPPRESSION_TTL);
    }

    /**
     * Persist alert to database
     */
    protected function persistAlert(array $alert): void
    {
        try {
            DB::table('monitoring_alerts')->insert([
                'alert_type' => $alert['alert_type'],
                'severity' => $alert['severity'],
                'message' => $alert['message'],
                'details' => json_encode($alert['details']),
                'resolved' => false,
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to persist alert', [
                'alert_type' => $alert['alert_type'],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notifications via configured channels
     */
    protected function sendNotifications(array $alert): void
    {
        $channels = config('monitoring.notifications', []);

        // Slack notification
        if ($channels['slack']['enabled'] ?? false) {
            $this->sendSlackNotification($alert);
        }

        // Email notification
        if ($channels['email']['enabled'] ?? false) {
            $this->sendEmailNotification($alert);
        }

        // Log notification (always enabled)
        $this->sendLogNotification($alert);
    }

    /**
     * Send Slack notification
     */
    protected function sendSlackNotification(array $alert): void
    {
        try {
            $webhookUrl = config('monitoring.notifications.slack.webhook_url');
            if (!$webhookUrl) return;

            $icon = match($alert['severity']) {
                'critical' => ':rotating_light:',
                'high' => ':warning:',
                'medium' => ':point_right:',
                'low' => ':information_source:',
                default => ':bell:'
            };

            $color = match($alert['severity']) {
                'critical' => 'danger',
                'high' => 'warning',
                'medium' => 'good',
                'low' => '#808080',
                default => 'warning'
            };

            $payload = [
                'username' => config('monitoring.notifications.slack.username', 'Legisinc Monitor'),
                'channel' => config('monitoring.notifications.slack.channel', '#monitoring'),
                'attachments' => [
                    [
                        'color' => $color,
                        'title' => "{$icon} Alerta de Monitoramento - {$alert['alert_type']}",
                        'text' => $alert['message'],
                        'fields' => [
                            [
                                'title' => 'Severidade',
                                'value' => strtoupper($alert['severity']),
                                'short' => true
                            ],
                            [
                                'title' => 'Horário',
                                'value' => now()->format('d/m/Y H:i:s'),
                                'short' => true
                            ]
                        ],
                        'footer' => 'Sistema Legisinc',
                        'ts' => now()->timestamp
                    ]
                ]
            ];

            Http::timeout(10)->post($webhookUrl, $payload);
            
        } catch (Throwable $e) {
            Log::error('Failed to send Slack notification', [
                'alert_type' => $alert['alert_type'],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send email notification (placeholder)
     */
    protected function sendEmailNotification(array $alert): void
    {
        try {
            $recipients = config('monitoring.notifications.email.recipients', []);
            if (empty($recipients)) return;

            // TODO: Implement email notification
            // Mail::to($recipients)->send(new MonitoringAlert($alert));
            
            Log::info('Email notification would be sent', [
                'recipients' => $recipients,
                'alert_type' => $alert['alert_type']
            ]);
            
        } catch (Throwable $e) {
            Log::error('Failed to send email notification', [
                'alert_type' => $alert['alert_type'],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send log notification
     */
    protected function sendLogNotification(array $alert): void
    {
        $logLevel = match($alert['severity']) {
            'critical' => 'critical',
            'high' => 'error',
            'medium' => 'warning',
            'low' => 'info',
            default => 'warning'
        };

        Log::log($logLevel, 'Monitoring Alert: ' . $alert['message'], [
            'alert_type' => $alert['alert_type'],
            'severity' => $alert['severity'],
            'details' => $alert['details'],
            'timestamp' => $alert['snapshot_time']
        ]);
    }

    /**
     * Get active alerts
     */
    public function getActiveAlerts(): array
    {
        try {
            return DB::table('monitoring_alerts')
                ->where('resolved', false)
                ->orderBy('severity')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($alert) {
                    return [
                        'id' => $alert->id,
                        'alert_type' => $alert->alert_type,
                        'severity' => $alert->severity,
                        'message' => $alert->message,
                        'details' => json_decode($alert->details, true),
                        'created_at' => $alert->created_at,
                    ];
                })
                ->toArray();
        } catch (Throwable $e) {
            Log::error('Failed to get active alerts', ['error' => $e->getMessage()]);
            return [];
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
        } catch (Throwable $e) {
            Log::error('Failed to resolve alert', [
                'alert_id' => $alertId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}