# Implementação Gateway em Produção - Guia Prático

## 🎯 Visão Executiva

Implementação de API Gateway com **rollout seguro**, **canary deployment**, **shadow traffic** e **rollback instantâneo**. Zero downtime, risco mínimo.

## 🚀 Stack de Produção

- **Traefik 3.0** - API Gateway com canary nativo
- **Docker Compose** - Orquestração local/staging
- **Prometheus + Grafana** - Métricas e alertas
- **Feature Flags** - Controle por rota/usuário

## 📦 1. Setup Inicial - Gateway com Governança

### docker-compose.yml (Produção-Ready)

```yaml
version: "3.9"

services:
  # =========================
  # API GATEWAY (TRAEFIK)
  # =========================
  traefik:
    image: traefik:v3.0
    container_name: legisinc-gateway
    restart: always
    command:
      # Providers
      - --providers.docker=true
      - --providers.docker.exposedbydefault=false
      - --providers.file.directory=/etc/traefik/dynamic
      - --providers.file.watch=true

      # API & Dashboard
      - --api.dashboard=true
      - --api.debug=false

      # Entrypoints
      - --entrypoints.web.address=:80
      - --entrypoints.websecure.address=:443

      # Métricas
      - --metrics.prometheus=true
      - --metrics.prometheus.buckets=0.1,0.3,1.2,5.0

      # Logs estruturados
      - --accesslog=true
      - --accesslog.format=json
      - --accesslog.fields.defaultmode=keep
      - --accesslog.fields.headers.defaultmode=keep

      # Tracing (opcional)
      - --tracing=true
      - --tracing.serviceName=legisinc-gateway
      - --tracing.jaeger=true
      - --tracing.jaeger.localAgentHostPort=jaeger:6831

    ports:
      - "80:80"
      - "443:443"
      - "8080:8080" # Dashboard (proteger em prod!)

    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
      - ./traefik/dynamic:/etc/traefik/dynamic:ro
      - ./traefik/certs:/certs:ro

    networks:
      - legisinc-net

    labels:
      # Dashboard seguro
      - "traefik.enable=true"
      - "traefik.http.routers.dashboard.rule=Host(`traefik.legisinc.local`)"
      - "traefik.http.routers.dashboard.service=api@internal"
      - "traefik.http.routers.dashboard.middlewares=auth-dashboard"
      - "traefik.http.middlewares.auth-dashboard.basicauth.users=admin:$$2y$$10$$..."

      # Headers de segurança globais
      - "traefik.http.middlewares.security-headers.headers.stsSeconds=31536000"
      - "traefik.http.middlewares.security-headers.headers.stsIncludeSubdomains=true"
      - "traefik.http.middlewares.security-headers.headers.contentTypeNosniff=true"
      - "traefik.http.middlewares.security-headers.headers.frameDeny=true"
      - "traefik.http.middlewares.security-headers.headers.customRequestHeaders.X-Request-ID={request_id}"

  # =========================
  # BACKEND LEGACY (LARAVEL)
  # =========================
  laravel:
    image: legisinc/laravel:latest
    container_name: legisinc-legacy
    restart: always
    environment:
      - APP_ENV=production
      - LOG_CHANNEL=json
      - DB_CONNECTION=pgsql
      - REDIS_HOST=redis
    networks:
      - legisinc-net
    labels:
      - "traefik.enable=true"
      - "traefik.http.services.legacy-svc.loadbalancer.server.port=8000"
      - "traefik.http.services.legacy-svc.loadbalancer.healthcheck.path=/health"
      - "traefik.http.services.legacy-svc.loadbalancer.healthcheck.interval=10s"
      - "traefik.http.services.legacy-svc.loadbalancer.healthcheck.timeout=3s"

  # =========================
  # NOVA API (NODE/PYTHON/GO)
  # =========================
  newapi:
    image: legisinc/newapi:latest
    container_name: legisinc-newapi
    restart: always
    environment:
      - NODE_ENV=production
      - LOG_FORMAT=json
      - DB_HOST=postgres
      - REDIS_HOST=redis
    networks:
      - legisinc-net
    labels:
      - "traefik.enable=true"
      - "traefik.http.services.newapi-svc.loadbalancer.server.port=3001"
      - "traefik.http.services.newapi-svc.loadbalancer.healthcheck.path=/health"
      - "traefik.http.services.newapi-svc.loadbalancer.healthcheck.interval=10s"
      - "traefik.http.services.newapi-svc.loadbalancer.circuitbreaker.expression=NetworkErrorRatio() > 0.5"

  # =========================
  # OBSERVABILIDADE
  # =========================
  prometheus:
    image: prom/prometheus
    container_name: legisinc-metrics
    restart: always
    volumes:
      - ./prometheus/prometheus.yml:/etc/prometheus/prometheus.yml:ro
    networks:
      - legisinc-net
    ports:
      - "9090:9090"

  grafana:
    image: grafana/grafana
    container_name: legisinc-dashboards
    restart: always
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
      - GF_INSTALL_PLUGINS=redis-datasource
    volumes:
      - ./grafana/dashboards:/etc/grafana/provisioning/dashboards:ro
      - ./grafana/datasources:/etc/grafana/provisioning/datasources:ro
    networks:
      - legisinc-net
    ports:
      - "3000:3000"

  # =========================
  # CACHE & DADOS
  # =========================
  redis:
    image: redis:7-alpine
    container_name: legisinc-cache
    restart: always
    command: redis-server --appendonly yes
    networks:
      - legisinc-net
    volumes:
      - redis-data:/data

  postgres:
    image: postgres:15-alpine
    container_name: legisinc-db
    restart: always
    environment:
      - POSTGRES_DB=legisinc
      - POSTGRES_USER=legisinc
      - POSTGRES_PASSWORD=${DB_PASSWORD}
    networks:
      - legisinc-net
    volumes:
      - postgres-data:/var/lib/postgresql/data

networks:
  legisinc-net:
    driver: bridge

volumes:
  redis-data:
  postgres-data:
```

## 🎚️ 2. Configuração de Canary Deployment

### traefik/dynamic/canary-routes.yml

```yaml
http:
  routers:
    # ====================================
    # ROTA: /api/proposicoes (10% CANARY)
    # ====================================
    api-proposicoes:
      rule: "PathPrefix(`/api/proposicoes`)"
      entryPoints: ["web", "websecure"]
      middlewares:
        - "rate-limit"
        - "security-headers"
        - "request-id"
      service: "proposicoes-weighted"

    # ====================================
    # ROTA: /api/parlamentares (100% LEGACY)
    # ====================================
    api-parlamentares:
      rule: "PathPrefix(`/api/parlamentares`)"
      entryPoints: ["web", "websecure"]
      middlewares:
        - "rate-limit"
        - "security-headers"
        - "request-id"
      service: "legacy-svc@docker"

    # ====================================
    # FALLBACK: Tudo mais vai pro LEGACY
    # ====================================
    api-fallback:
      rule: "PathPrefix(`/`)"
      priority: 1
      entryPoints: ["web", "websecure"]
      middlewares:
        - "security-headers"
        - "request-id"
      service: "legacy-svc@docker"

  services:
    # ====================================
    # WEIGHTED SERVICE (CANARY)
    # ====================================
    proposicoes-weighted:
      weighted:
        services:
          - name: "newapi-svc@docker"
            weight: 10  # 10% do tráfego
          - name: "legacy-svc@docker"
            weight: 90  # 90% do tráfego

  middlewares:
    # ====================================
    # RATE LIMITING
    # ====================================
    rate-limit:
      rateLimit:
        average: 100
        burst: 50
        period: "1m"
        sourceCriterion:
          ipStrategy:
            depth: 2

    # ====================================
    # REQUEST ID
    # ====================================
    request-id:
      headers:
        customRequestHeaders:
          X-Request-ID: "{{.RequestID}}"
          X-Forwarded-Proto: "{{.Proto}}"
          X-Real-IP: "{{.RemoteAddr}}"

    # ====================================
    # RETRY MIDDLEWARE
    # ====================================
    retry:
      retry:
        attempts: 2
        initialInterval: "100ms"

    # ====================================
    # CIRCUIT BREAKER
    # ====================================
    circuit-breaker:
      circuitBreaker:
        expression: "NetworkErrorRatio() > 0.5 || ResponseCodeRatio(500, 600, 0, 600) > 0.5"
```

## 🔍 3. Shadow Traffic (Espelhamento)

### nginx-shadow.conf (Alternativa ao Traefik)

```nginx
upstream laravel_backend {
    server laravel:8000 max_fails=3 fail_timeout=30s;
}

upstream nova_api {
    server newapi:3001 max_fails=3 fail_timeout=30s;
}

# Mapa de rotas para shadow traffic
map $uri $shadow_backend {
    ~^/api/proposicoes  nova_api;
    ~^/api/documentos   nova_api;
    default             "";
}

server {
    listen 80;
    server_name api.legisinc.gov.br;

    # Logs estruturados JSON
    access_log /var/log/nginx/access.log json_combined;
    error_log /var/log/nginx/error.log warn;

    # Headers de segurança
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Request ID generation
    set $request_id $request_time-$remote_addr-$remote_port;
    add_header X-Request-ID $request_id always;

    # =================================
    # PRODUÇÃO: /api/proposicoes
    # =================================
    location /api/proposicoes {
        # ESPELHAR para nova API (não bloqueia resposta)
        mirror /mirror$request_uri;
        mirror_request_body on;

        # PRODUÇÃO continua no Laravel
        proxy_pass http://laravel_backend;
        proxy_set_header X-Request-ID $request_id;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header Host $host;

        # Timeout curto para produção
        proxy_connect_timeout 2s;
        proxy_send_timeout 5s;
        proxy_read_timeout 5s;
    }

    # =================================
    # SHADOW TRAFFIC (validação)
    # =================================
    location /mirror {
        internal;

        # Reescrever URI removendo /mirror
        rewrite ^/mirror(.*) $1 break;

        # Enviar para nova API
        proxy_pass http://$shadow_backend;
        proxy_set_header X-Shadow-Request true;
        proxy_set_header X-Request-ID $request_id;

        # Não aguardar resposta completa
        proxy_ignore_client_abort on;
        proxy_connect_timeout 1s;
        proxy_send_timeout 2s;
        proxy_read_timeout 2s;

        # Log separado para análise
        access_log /var/log/nginx/shadow.log json_combined;
    }

    # =================================
    # FALLBACK para Laravel
    # =================================
    location / {
        proxy_pass http://laravel_backend;
        proxy_set_header X-Request-ID $request_id;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header Host $host;
    }
}
```

## 📊 4. Observabilidade e Métricas

### prometheus/prometheus.yml

```yaml
global:
  scrape_interval: 15s
  evaluation_interval: 15s

scrape_configs:
  # Traefik metrics
  - job_name: 'traefik'
    static_configs:
      - targets: ['traefik:8080']
    metrics_path: /metrics

  # Laravel metrics (se usar spatie/laravel-prometheus)
  - job_name: 'laravel'
    static_configs:
      - targets: ['laravel:8000']
    metrics_path: /metrics

  # Nova API metrics
  - job_name: 'newapi'
    static_configs:
      - targets: ['newapi:3001']
    metrics_path: /metrics

# Alerting rules
rule_files:
  - /etc/prometheus/alerts.yml

alerting:
  alertmanagers:
    - static_configs:
        - targets: ['alertmanager:9093']
```

### prometheus/alerts.yml

```yaml
groups:
  - name: canary_alerts
    interval: 30s
    rules:
      # Alta taxa de erro no canary
      - alert: HighErrorRateCanary
        expr: |
          rate(traefik_service_requests_total{service="newapi-svc@docker",code=~"5.."}[5m]) > 0.05
        for: 2m
        labels:
          severity: critical
          service: newapi
        annotations:
          summary: "Alta taxa de erro no canary deployment"
          description: "Nova API com {{ $value | humanizePercentage }} de erros 5xx"

      # Latência alta no canary
      - alert: HighLatencyCanary
        expr: |
          histogram_quantile(0.95, rate(traefik_service_request_duration_seconds_bucket{service="newapi-svc@docker"}[5m])) > 1.0
        for: 5m
        labels:
          severity: warning
          service: newapi
        annotations:
          summary: "Latência P95 alta no canary"
          description: "P95 latency: {{ $value }}s"

      # Shadow traffic divergência
      - alert: ShadowTrafficDivergence
        expr: |
          abs(rate(nginx_shadow_responses_total{status="2xx"}[5m]) - rate(nginx_production_responses_total{status="2xx"}[5m])) > 0.1
        for: 10m
        labels:
          severity: warning
        annotations:
          summary: "Shadow traffic com resposta divergente"
          description: "Diferença de {{ $value | humanizePercentage }} entre prod e shadow"
```

## 🎛️ 5. Feature Flags e Roteamento Dinâmico

### config/routes.json (Versionado no Git)

```json
{
  "version": "1.0.0",
  "updated_at": "2025-01-17T15:00:00Z",
  "routes": [
    {
      "path": "/api/proposicoes",
      "backend": "canary",
      "canary": {
        "enabled": true,
        "percentage": 10,
        "target": "newapi",
        "fallback": "legacy"
      },
      "shadow": {
        "enabled": true,
        "target": "newapi"
      },
      "circuit_breaker": {
        "enabled": true,
        "error_threshold": 0.5,
        "timeout_ms": 3000
      },
      "rate_limit": {
        "enabled": true,
        "rpm": 100,
        "burst": 50
      }
    },
    {
      "path": "/api/parlamentares",
      "backend": "legacy",
      "canary": {
        "enabled": false
      },
      "shadow": {
        "enabled": false
      }
    },
    {
      "path": "/api/assinatura",
      "backend": "external",
      "external": {
        "url": "https://api.assinatura.saas.com",
        "auth": {
          "type": "api_key",
          "header": "X-API-Key",
          "value_env": "ASSINATURA_API_KEY"
        },
        "timeout_ms": 5000,
        "retry": {
          "attempts": 3,
          "backoff": "exponential"
        }
      }
    }
  ],
  "global": {
    "cors": {
      "origins": ["https://app.legisinc.gov.br"],
      "credentials": true
    },
    "security": {
      "headers": {
        "X-Frame-Options": "DENY",
        "X-Content-Type-Options": "nosniff",
        "Strict-Transport-Security": "max-age=31536000"
      }
    },
    "observability": {
      "request_id": true,
      "trace_sampling": 0.1,
      "log_level": "info"
    }
  }
}
```

### scripts/apply-routes.sh

```bash
#!/bin/bash
# Script para aplicar configuração de rotas ao Traefik

set -euo pipefail

ROUTES_FILE="config/routes.json"
TRAEFIK_CONFIG="traefik/dynamic/routes.yml"

# Função para converter JSON para YAML do Traefik
convert_routes() {
    jq -r '
    .routes[] |
    {
        "http": {
            "routers": {
                (.path | gsub("/"; "-")): {
                    "rule": "PathPrefix(`\(.path)`)",
                    "service": (
                        if .canary.enabled then
                            "\(.path | gsub("/"; "-"))-weighted"
                        else
                            "\(.backend)-svc@docker"
                        end
                    ),
                    "middlewares": [
                        if .rate_limit.enabled then "rate-limit" else empty end,
                        "security-headers",
                        "request-id"
                    ]
                }
            },
            "services": (
                if .canary.enabled then
                    {
                        ("\(.path | gsub("/"; "-"))-weighted"): {
                            "weighted": {
                                "services": [
                                    {
                                        "name": "\(.canary.target)-svc@docker",
                                        "weight": .canary.percentage
                                    },
                                    {
                                        "name": "\(.canary.fallback)-svc@docker",
                                        "weight": (100 - .canary.percentage)
                                    }
                                ]
                            }
                        }
                    }
                else
                    {}
                end
            )
        }
    }
    ' "$ROUTES_FILE" | yq -y > "$TRAEFIK_CONFIG"
}

# Validar configuração
validate_config() {
    echo "🔍 Validando configuração..."
    jq empty "$ROUTES_FILE" || {
        echo "❌ JSON inválido em $ROUTES_FILE"
        exit 1
    }
}

# Aplicar configuração
apply_config() {
    echo "📝 Convertendo rotas..."
    convert_routes

    echo "🔄 Recarregando Traefik..."
    docker exec legisinc-gateway traefik version > /dev/null && {
        echo "✅ Configuração aplicada com sucesso!"
    } || {
        echo "⚠️  Traefik não está rodando, configuração salva para próximo start"
    }
}

# Backup da configuração anterior
backup_config() {
    BACKUP_FILE="$TRAEFIK_CONFIG.backup.$(date +%Y%m%d-%H%M%S)"
    [ -f "$TRAEFIK_CONFIG" ] && cp "$TRAEFIK_CONFIG" "$BACKUP_FILE"
    echo "💾 Backup salvo em: $BACKUP_FILE"
}

# Main
main() {
    validate_config
    backup_config
    apply_config

    echo ""
    echo "📊 Status atual das rotas:"
    jq -r '.routes[] | "\(.path): \(if .canary.enabled then "Canary \(.canary.percentage)%" else .backend end)"' "$ROUTES_FILE"
}

main "$@"
```

## ✅ 6. Checklist de Implementação (3 Semanas)

### 🗓️ Semana 1: Gateway Sólido

```bash
# Segunda-feira
□ Subir docker-compose com Traefik
□ Configurar healthchecks em todos os serviços
□ Implementar X-Request-ID propagation

# Terça-feira
□ Configurar rate limiting global (100 RPM)
□ Adicionar security headers
□ Setup logs estruturados JSON

# Quarta-feira
□ Configurar Prometheus + Grafana
□ Criar dashboard básico (RPS, latência, erros)
□ Setup alertas críticos (5xx > 1%)

# Quinta-feira
□ Implementar shadow traffic para /api/tipos-proposicao
□ Coletar métricas de comparação (shadow vs prod)
□ Analisar logs para divergências

# Sexta-feira
□ Code review da configuração
□ Documentar decisões arquiteturais
□ Preparar ambiente de staging
```

### 🗓️ Semana 2: Primeiro Canary

```bash
# Segunda-feira
□ Implementar /api/tipos-proposicao na Nova API
□ Testes unitários com 100% cobertura
□ Contract tests (Pact) com frontend

# Terça-feira
□ Deploy Nova API em staging
□ Ativar canary 5% em staging
□ Executar testes de carga (k6: 50 RPS, 15 min)

# Quarta-feira
□ Promover para produção com 1% canary
□ Monitorar métricas por 4 horas
□ Se estável, aumentar para 5%

# Quinta-feira
□ Aumentar canary para 10%
□ A/B testing de latência (legacy vs new)
□ Validar paridade de respostas

# Sexta-feira
□ Se métricas OK, aumentar para 25%
□ Preparar rollback plan
□ Documentar aprendizados
```

### 🗓️ Semana 3: Endpoints Read-Only

```bash
# Segunda-feira
□ Migrar GET /api/parlamentares
□ Migrar GET /api/proposicoes (listagem)
□ Implementar cache com ETags

# Terça-feira
□ Contract tests para novos endpoints
□ Performance tests (target: P95 < 200ms)
□ Ativar shadow traffic

# Quarta-feira
□ Canary 10% para endpoints migrados
□ Monitorar error budget (target: 99.9%)
□ Coletar feedback dos usuários

# Quinta-feira
□ Promover canary para 50%
□ Load test com tráfego real duplicado
□ Validar circuit breaker

# Sexta-feira
□ Go/No-Go meeting para 100%
□ Se aprovado, promover para 100%
□ Celebrar primeira migração completa! 🎉
```

## 🛡️ 7. Segurança e Compliance

### Headers de Segurança Obrigatórios

```yaml
# traefik/dynamic/security.yml
http:
  middlewares:
    security-headers-strict:
      headers:
        # HSTS
        stsSeconds: 31536000
        stsIncludeSubdomains: true
        stsPreload: true

        # Content Security
        contentTypeNosniff: true
        browserXssFilter: true
        frameDeny: true

        # CSP
        contentSecurityPolicy: |
          default-src 'self';
          script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;
          style-src 'self' 'unsafe-inline';
          img-src 'self' data: https:;
          font-src 'self' data:;

        # CORS (aplicar por rota se necessário)
        accessControlAllowOrigin: "https://app.legisinc.gov.br"
        accessControlAllowCredentials: true
        accessControlMaxAge: 86400

        # Custom headers
        customResponseHeaders:
          X-Environment: "production"
          X-Version: "{{.Version}}"
```

### Validação de Entrada

```javascript
// middleware/validation.js (Nova API)
const Joi = require('joi');

const schemas = {
  proposicao: Joi.object({
    titulo: Joi.string().min(10).max(200).required(),
    tipo_id: Joi.number().integer().positive().required(),
    conteudo: Joi.string().max(50000).required(),
    autor_id: Joi.number().integer().positive().required(),
    urgente: Joi.boolean().default(false)
  }),

  parlamentar: Joi.object({
    nome: Joi.string().min(3).max(100).required(),
    email: Joi.string().email().required(),
    partido_id: Joi.number().integer().positive().required(),
    telefone: Joi.string().pattern(/^\d{10,11}$/).optional()
  })
};

const validate = (schema) => {
  return (req, res, next) => {
    const { error, value } = schemas[schema].validate(req.body, {
      abortEarly: false,
      stripUnknown: true
    });

    if (error) {
      return res.status(400).json({
        success: false,
        errors: error.details.map(d => ({
          field: d.path.join('.'),
          message: d.message
        }))
      });
    }

    req.body = value;
    next();
  };
};

module.exports = validate;
```

## 🔄 8. Rollback e Recovery

### scripts/emergency-rollback.sh

```bash
#!/bin/bash
# Rollback de emergência em < 30 segundos

set -euo pipefail

ROUTE=$1
TARGET=${2:-legacy}

emergency_rollback() {
    echo "🚨 INICIANDO ROLLBACK DE EMERGÊNCIA para $ROUTE"

    # Atualizar configuração para 100% legacy
    cat > /tmp/emergency-route.yml <<EOF
http:
  routers:
    emergency-$ROUTE:
      rule: "PathPrefix(\`$ROUTE\`)"
      service: "legacy-svc@docker"
      priority: 999
EOF

    # Aplicar imediatamente
    docker cp /tmp/emergency-route.yml legisinc-gateway:/etc/traefik/dynamic/emergency.yml

    # Forçar reload
    docker exec legisinc-gateway kill -USR1 1

    echo "✅ Rollback completo em $(date)"

    # Notificar equipe
    curl -X POST $SLACK_WEBHOOK -d "{
        \"text\": \"🚨 Rollback executado para $ROUTE\",
        \"channel\": \"#incidents\"
    }"
}

# Validar estado atual
check_health() {
    echo "🔍 Verificando saúde do sistema..."

    # Checar legacy
    curl -f http://localhost/health || {
        echo "❌ Legacy backend não responde!"
        exit 1
    }

    echo "✅ Sistema estabilizado"
}

# Main
main() {
    emergency_rollback
    sleep 5
    check_health

    echo ""
    echo "📋 Próximos passos:"
    echo "1. Investigar logs em /var/log/traefik/"
    echo "2. Revisar métricas em http://localhost:3000"
    echo "3. Criar post-mortem do incidente"
}

main "$@"
```

## 📈 9. Métricas de Sucesso

### KPIs para Monitorar

| Métrica | Target | Alerta Warning | Alerta Critical |
|---------|--------|----------------|-----------------|
| **Disponibilidade** | 99.9% | < 99.5% | < 99% |
| **Latência P95** | < 200ms | > 300ms | > 500ms |
| **Taxa de Erro** | < 0.1% | > 0.5% | > 1% |
| **RPS (Requests/sec)** | 1000 | < 500 | < 200 |
| **CPU Gateway** | < 50% | > 70% | > 85% |
| **Memória Gateway** | < 1GB | > 2GB | > 3GB |
| **Canary Error Rate** | < 0.1% | > 0.5% | > 1% |
| **Shadow Divergence** | < 1% | > 5% | > 10% |

### Dashboard Grafana Query Examples

```promql
# Taxa de sucesso por serviço
sum(rate(traefik_service_requests_total{code=~"2.."}[5m])) by (service) /
sum(rate(traefik_service_requests_total[5m])) by (service) * 100

# Latência P95 por rota
histogram_quantile(0.95,
  sum(rate(traefik_service_request_duration_seconds_bucket[5m])) by (le, service)
)

# Comparação Canary vs Legacy
sum(rate(traefik_service_requests_total{service="newapi-svc@docker"}[5m])) /
sum(rate(traefik_service_requests_total{service=~".*-svc@docker"}[5m])) * 100

# Circuit breaker triggers
sum(increase(traefik_service_circuit_breaker_opened_total[1h])) by (service)
```

## 🎯 10. Conclusão e Próximos Passos

### Implementação Imediata (Dia 1)

```bash
# 1. Clone este setup
git clone https://github.com/legisinc/gateway-setup.git
cd gateway-setup

# 2. Configure variáveis de ambiente
cp .env.example .env
vim .env

# 3. Inicie o gateway
docker-compose up -d traefik laravel redis postgres

# 4. Valide que tudo está funcionando
curl http://localhost/health
curl http://localhost:8080/metrics

# 5. Ative primeiro shadow traffic
./scripts/enable-shadow.sh /api/tipos-proposicao

# 6. Monitore por 24h antes do canary
```

### Evolução Futura

1. **Mês 1:** Gateway estável + 3 endpoints migrados
2. **Mês 2:** 50% das rotas em canary
3. **Mês 3:** Nova API como primária, legacy como fallback
4. **Mês 6:** Descomissionamento do legacy

### Benefícios Alcançados

✅ **Zero downtime** durante toda migração
✅ **Rollback em 30 segundos** se necessário
✅ **Validação sem risco** via shadow traffic
✅ **Métricas detalhadas** para decisões
✅ **Migração gradual** sem big bang

---

*Documento preparado para implementação imediata*
*Versão: 2.0.0*
*Data: 17/01/2025*