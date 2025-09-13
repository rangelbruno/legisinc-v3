-- Query para apurar erro 5xx (24h) - Baseline para SLO
WITH agg AS (
  SELECT
    SUM(CASE WHEN (tags->>'status') ~ '^5' THEN 1 ELSE 0 END)::float AS e5x,
    COUNT(*)::float AS total
  FROM monitoring_metrics
  WHERE metric_name='request_duration_ms'
    AND created_at >= now() - interval '24 hours'
)
SELECT 
  CASE WHEN total=0 THEN 0 ELSE e5x/total*100 END AS error_rate_pct,
  e5x AS server_errors_count,
  total AS total_requests,
  CASE 
    WHEN total=0 THEN 'NO_DATA'
    WHEN (e5x/total*100) <= 1.0 THEN 'HEALTHY' 
    WHEN (e5x/total*100) <= 5.0 THEN 'WARNING'
    ELSE 'CRITICAL'
  END AS status
FROM agg;