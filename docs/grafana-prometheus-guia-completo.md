# Grafana & Prometheus - Guia Completo de Uso

## 📊 Visão Geral

O **Grafana** e **Prometheus** estão configurados e funcionando para monitoramento completo da infraestrutura LegisInc v2 com Canary Deployment.

## 🔐 Credenciais de Acesso

### Grafana
- **URL**: http://localhost:3000
- **Usuário**: `admin`
- **Senha**: `admin`
- **Versão**: 12.1.1

### Prometheus
- **URL**: http://localhost:9090
- **Acesso**: Sem autenticação (apenas interno)
- **API**: http://localhost:9090/api/v1/

## 🚀 Acesso Rápido

| Serviço | URL | Descrição |
|---------|-----|-----------|
| 📈 **Grafana Dashboard** | http://localhost:3000 | Interface principal para visualização |
| 📊 **Prometheus Targets** | http://localhost:9090/targets | Status dos targets monitorados |
| 🔍 **Prometheus Query** | http://localhost:9090/graph | Interface de queries |
| 💊 **Health Check Grafana** | http://localhost:3000/api/health | Status do Grafana |

## 📈 Métricas Coletadas Atualmente

### 1. **Traefik Gateway** ✅
- **Target**: `legisinc-gateway-simple:8080`
- **Intervalo**: 10 segundos
- **Métricas**:
  - Requisições por serviço
  - Latência de resposta
  - Status codes (2xx, 4xx, 5xx)
  - Load balancing
  - Uptime dos backends

### 2. **Prometheus Self-Monitoring** ✅
- **Target**: `localhost:9090`
- **Intervalo**: 15 segundos
- **Métricas**:
  - Performance do próprio Prometheus
  - Uso de memória
  - Tempo de scraping

### 3. **PostgreSQL** ✅
- **Target**: `postgres-exporter:9187`
- **Intervalo**: 30 segundos
- **Status**: ✅ **FUNCIONANDO**
- **Connection**: `postgresql://postgres:123456@legisinc-postgres:5432/legisinc`
- **Métricas Coletadas**:
  - Estatísticas de tabelas (pg_stat_user_tables)
  - I/O de tabelas (pg_statio_user_tables)
  - Performance de queries
  - Conexões ativas
  - **Métricas Customizadas do LegisInc**:
    - `legisinc_proposicoes_total_proposicoes`
    - `legisinc_proposicoes_hoje`
    - `legisinc_proposicoes_semana`
    - `legisinc_proposicoes_ativas`
    - `legisinc_usuarios_ativos`
    - `legisinc_usuarios_ativos_semana`
    - `legisinc_documentos_total`
    - `legisinc_documentos_hoje`

## 🎯 Configuração do Banco de Dados PostgreSQL

### Passo 1: Habilitar pg_stat_statements

```sql
-- Conectar como superuser no PostgreSQL
-- docker exec -it legisinc-postgres psql -U postgres -d legisinc

-- Instalar extensão de monitoramento
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

-- Verificar se foi instalada
SELECT * FROM pg_available_extensions WHERE name = 'pg_stat_statements';
```

### Passo 2: Configurar postgres_exporter

Adicione ao `docker-compose.gateway-simple.yml`:

```yaml
  postgres-exporter:
    image: prometheuscommunity/postgres-exporter:latest
    container_name: legisinc-postgres-exporter
    restart: unless-stopped
    environment:
      DATA_SOURCE_NAME: "postgresql://postgres:sua_senha_aqui@legisinc-postgres:5432/legisinc?sslmode=disable"
    ports:
      - "9187:9187"
    networks:
      - legisinc_network
    depends_on:
      - db
```

### Passo 3: Atualizar Prometheus

Adicione ao `gateway/prometheus/prometheus.yml`:

```yaml
  # ====================================
  # POSTGRESQL METRICS
  # ====================================
  - job_name: 'postgres'
    static_configs:
      - targets: ['postgres-exporter:9187']
    scrape_interval: 30s
```

## 📊 Dashboards Recomendados

### 1. **Traefik Dashboard**

```json
{
  "dashboard": {
    "title": "LegisInc - Traefik Gateway",
    "panels": [
      {
        "title": "Requests per Service",
        "type": "graph",
        "targets": [
          {
            "expr": "rate(traefik_service_requests_total[5m])",
            "legendFormat": "{{service}}"
          }
        ]
      },
      {
        "title": "Response Time",
        "type": "graph",
        "targets": [
          {
            "expr": "histogram_quantile(0.95, rate(traefik_service_request_duration_seconds_bucket[5m]))",
            "legendFormat": "95th percentile"
          }
        ]
      },
      {
        "title": "Error Rate",
        "type": "stat",
        "targets": [
          {
            "expr": "rate(traefik_service_requests_total{code=~\"5..\"}[5m]) / rate(traefik_service_requests_total[5m]) * 100",
            "legendFormat": "Error Rate %"
          }
        ]
      }
    ]
  }
}
```

### 2. **Canary Deployment Dashboard**

```json
{
  "dashboard": {
    "title": "LegisInc - Canary Deployment",
    "panels": [
      {
        "title": "Traffic Distribution",
        "type": "piechart",
        "targets": [
          {
            "expr": "sum by (service) (rate(traefik_service_requests_total{service=~\"laravel.*|nova-api.*\"}[5m]))",
            "legendFormat": "{{service}}"
          }
        ]
      },
      {
        "title": "Canary Health Score",
        "type": "gauge",
        "targets": [
          {
            "expr": "(1 - (rate(traefik_service_requests_total{service=\"nova-api-svc@docker\",code=~\"5..\"}[5m]) / rate(traefik_service_requests_total{service=\"nova-api-svc@docker\"}[5m]))) * 100",
            "legendFormat": "Health %"
          }
        ]
      }
    ]
  }
}
```

## ✅ Testando a Configuração

### Verificar se PostgreSQL está funcionando

1. **Verificar Targets no Prometheus**: http://localhost:9090/targets
   - Deve mostrar `postgres` target como `UP`

2. **Testar queries no Prometheus**: http://localhost:9090/graph
   ```promql
   # Métricas básicas do PostgreSQL
   pg_up

   # Métricas customizadas do LegisInc
   legisinc_proposicoes_total_proposicoes
   legisinc_usuarios_ativos
   ```

3. **Verificar no Grafana**: http://localhost:3000
   - Login: admin/admin
   - Ir em Explore
   - Selecionar datasource Prometheus
   - Testar as queries acima

## 🔍 Queries Úteis do Prometheus

### Monitoramento de Canary

```promql
# Taxa de requisições por serviço (último minuto)
rate(traefik_service_requests_total[1m])

# Taxa de erro do canary (Nova API)
rate(traefik_service_requests_total{service="nova-api-svc@docker",code=~"5.."}[5m]) /
rate(traefik_service_requests_total{service="nova-api-svc@docker"}[5m]) * 100

# Latência P95 do canary
histogram_quantile(0.95,
  rate(traefik_service_request_duration_seconds_bucket{service="nova-api-svc@docker"}[5m])
) * 1000

# Distribuição de tráfego entre Laravel e Nova API
sum by (service) (rate(traefik_service_requests_total{service=~".*laravel.*|.*nova-api.*"}[5m]))

# Uptime dos backends
up{job="traefik"}
```

### Monitoramento de Performance

```promql
# Requisições por segundo (RPS)
sum(rate(traefik_service_requests_total[1m]))

# Tempo de resposta médio
rate(traefik_service_request_duration_seconds_sum[5m]) /
rate(traefik_service_requests_total[5m])

# Top 5 serviços com mais tráfego
topk(5, sum by (service) (rate(traefik_service_requests_total[5m])))

# Status codes breakdown
sum by (code) (rate(traefik_service_requests_total[5m]))
```

### Monitoramento de PostgreSQL

```promql
# Status do PostgreSQL
pg_up

# Conexões ativas
pg_stat_database_numbackends

# Queries por segundo
rate(pg_stat_database_xact_commit[5m]) + rate(pg_stat_database_xact_rollback[5m])

# Tabelas com mais inserções
topk(5, sum by (relname) (rate(pg_stat_user_tables_n_tup_ins[5m])))

# Métricas de negócio LegisInc
legisinc_proposicoes_total_proposicoes
legisinc_usuarios_ativos
legisinc_documentos_total

# Taxa de crescimento de proposições (por dia)
increase(legisinc_proposicoes_total_proposicoes[1d])
```

## 🚨 Alertas Recomendados

### Canary Health Alerts

```yaml
groups:
  - name: canary.rules
    rules:
      - alert: CanaryHighErrorRate
        expr: rate(traefik_service_requests_total{service="nova-api-svc@docker",code=~"5.."}[5m]) / rate(traefik_service_requests_total{service="nova-api-svc@docker"}[5m]) * 100 > 5
        for: 2m
        labels:
          severity: critical
        annotations:
          summary: "Canary deployment error rate too high"
          description: "Nova API error rate is {{ $value }}% for more than 2 minutes"

      - alert: CanaryHighLatency
        expr: histogram_quantile(0.95, rate(traefik_service_request_duration_seconds_bucket{service="nova-api-svc@docker"}[5m])) * 1000 > 500
        for: 3m
        labels:
          severity: warning
        annotations:
          summary: "Canary deployment latency too high"
          description: "Nova API 95th percentile latency is {{ $value }}ms"
```

## 🛠️ Configuração Avançada

### 1. **Adicionar Métricas do Laravel**

Instale o pacote Prometheus PHP:

```bash
# No container Laravel
composer require promphp/prometheus_client_php
composer require promphp/prometheus_push_gateway_php
```

Crie endpoint `/metrics` no Laravel:

```php
// routes/web.php
Route::get('/metrics', function () {
    $registry = \Prometheus\CollectorRegistry::getDefault();

    // Métrica de requests
    $counter = $registry->getOrRegisterCounter('app', 'requests_total', 'Total requests', ['method', 'route']);
    $counter->incBy(1, [request()->method(), request()->route()->getName()]);

    // Métrica de usuários ativos
    $gauge = $registry->getOrRegisterGauge('app', 'active_users', 'Active users');
    $gauge->set(\App\Models\User::where('last_activity', '>', now()->subMinutes(5))->count());

    $renderer = new \Prometheus\RenderTextFormat();
    return response($renderer->render($registry->getMetricFamilySamples()), 200)
        ->header('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
});
```

### 2. **Monitoramento de Banco de Dados**

```yaml
# Adicionar ao prometheus.yml
  - job_name: 'postgres'
    static_configs:
      - targets: ['postgres-exporter:9187']
    scrape_interval: 30s
```

### 3. **Integração com OnlyOffice**

```yaml
  - job_name: 'onlyoffice'
    static_configs:
      - targets: ['onlyoffice-documentserver:80']
    metrics_path: /healthcheck
    scrape_interval: 60s
```

## 📋 Checklist de Configuração

### ✅ Setup Inicial
- [x] Prometheus funcionando (localhost:9090)
- [x] Grafana funcionando (localhost:3000)
- [x] Datasource Prometheus configurado
- [x] Métricas do Traefik sendo coletadas
- [x] PostgreSQL exporter configurado e funcionando
- [x] Métricas personalizadas do LegisInc sendo coletadas

### 🔄 Próximos Passos
- [x] Configurar postgres-exporter ✅ **CONCLUÍDO**
- [ ] Adicionar métricas do Laravel
- [ ] Criar dashboards customizados
- [ ] Configurar alertas
- [ ] Adicionar monitoramento do OnlyOffice

### 📊 Dashboards Básicos
- [ ] Dashboard de Canary Deployment
- [ ] Dashboard de Performance Geral
- [ ] Dashboard de Database
- [ ] Dashboard de Business Metrics

## 🎯 Métricas de Negócio Sugeridas

### Aplicação Laravel

```promql
# Proposições criadas por dia
increase(laravel_proposicoes_total[1d])

# Usuários únicos ativos
laravel_active_users

# Documentos processados pelo OnlyOffice
increase(laravel_documents_processed_total[1h])

# Tempo médio de processamento de proposições
rate(laravel_proposicao_processing_duration_seconds_sum[5m]) /
rate(laravel_proposicao_processing_duration_seconds_count[5m])
```

## 🔧 Troubleshooting

### Problema: Métricas não aparecem
```bash
# Verificar targets do Prometheus
curl http://localhost:9090/api/v1/targets

# Verificar logs do Prometheus
docker logs legisinc-prometheus-simple

# Verificar conectividade
docker exec legisinc-prometheus-simple wget -O- http://legisinc-gateway-simple:8080/metrics
```

### Problema: Grafana não conecta ao Prometheus
```bash
# Verificar datasource
curl http://localhost:3000/api/datasources

# Testar conectividade do Grafana
docker exec legisinc-grafana-simple wget -O- http://prometheus:9090/api/v1/label/__name__/values
```

### Problema: Dashboard vazio
```bash
# Verificar queries no Prometheus
curl "http://localhost:9090/api/v1/query?query=up"

# Verificar se há dados
curl "http://localhost:9090/api/v1/query?query=traefik_service_requests_total"
```

## 📚 Links Úteis

- **Documentação Prometheus**: https://prometheus.io/docs/
- **Documentação Grafana**: https://grafana.com/docs/
- **Traefik Metrics**: https://doc.traefik.io/traefik/observability/metrics/prometheus/
- **PromQL Guide**: https://prometheus.io/docs/prometheus/latest/querying/basics/
- **Grafana Dashboards Community**: https://grafana.com/grafana/dashboards/

---

**📝 Autor**: LegisInc v2 DevOps
**📅 Data**: 2025-09-17
**🔄 Versão**: 1.0

---

> **💡 Dica**: Use o endpoint http://localhost:3003/status do Canary Monitor junto com essas métricas para ter visibilidade completa do deployment!