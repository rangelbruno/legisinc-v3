# Sistema de Observabilidade e Monitoramento - Legisinc

## 📊 Visão Geral

Sistema completo de observabilidade e monitoramento para o ambiente Legisinc, permitindo ao administrador ter visibilidade total sobre o desempenho, saúde e operações do sistema em tempo real.

## 🎯 Objetivos

1. **Monitoramento Proativo**: Identificar problemas antes que afetem usuários
2. **Visibilidade Total**: Dashboard unificado com métricas críticas
3. **Análise de Performance**: Identificar gargalos e otimizar processos
4. **Auditoria Completa**: Rastreamento de todas as operações críticas
5. **Alertas Inteligentes**: Notificações automáticas para situações críticas

## 🏗️ Arquitetura

### Componentes Principais

```
┌─────────────────────────────────────────────────────────┐
│                   Dashboard Admin                        │
├─────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    │
│  │   Métricas  │  │    Logs     │  │   Alertas   │    │
│  └─────────────┘  └─────────────┘  └─────────────┘    │
├─────────────────────────────────────────────────────────┤
│                  Camada de Coleta                       │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    │
│  │  Collector  │  │   Queues    │  │   Metrics   │    │
│  └─────────────┘  └─────────────┘  └─────────────┘    │
├─────────────────────────────────────────────────────────┤
│                 Camada de Armazenamento                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    │
│  │  PostgreSQL │  │    Redis    │  │   Storage   │    │
│  └─────────────┘  └─────────────┘  └─────────────┘    │
└─────────────────────────────────────────────────────────┘
```

## 📋 Funcionalidades Detalhadas

### 1. **Monitoramento de Banco de Dados**

#### 1.1 Métricas PostgreSQL
- **Conexões Ativas**: Número de conexões abertas vs limite máximo
- **Queries em Execução**: Lista de queries em andamento com tempo de execução
- **Queries Lentas**: Top 10 queries mais lentas das últimas 24h
- **Tamanho do Banco**: Crescimento do banco por tabela
- **Índices**: Uso e eficiência dos índices
- **Vacuum/Analyze**: Status das operações de manutenção
- **Locks**: Detecção de deadlocks e locks longos
- **Cache Hit Ratio**: Eficiência do cache do PostgreSQL

#### 1.2 Implementação

##### DatabaseDebugService - Monitoramento em Tempo Real
```php
// app/Services/DatabaseDebugService.php
class DatabaseDebugService {
    // Controle de captura
    - startCapture()                    // Inicia monitoramento com DB::listen()
    - stopCapture()                     // Para monitoramento e limpa cache
    - getCapturedQueries()              // Retorna queries capturadas
    
    // Análise de queries
    - processQuery($query)              // Processa query individual
    - getQueryType($sql)                // Identifica tipo (SELECT, INSERT, etc.)
    - analyzePerformance($time)         // Classifica performance
    - extractTables($sql)               // Extrai tabelas envolvidas
    
    // Estatísticas
    - getQueryStatistics()              // Estatísticas agregadas
    - getSlowQueries($threshold = 100)  // Queries lentas
    - getDatabaseStats()                // Estatísticas PostgreSQL
    
    // Contexto HTTP
    - captureQueryWithContext($query)   // Captura com contexto HTTP
    - getHttpContext()                  // Obtém dados da requisição atual
}
```

##### DatabaseMonitor - Métricas PostgreSQL
```php
// app/Services/Monitoring/DatabaseMonitor.php
class DatabaseMonitor {
    - getActiveConnections()
    - getSlowQueries($threshold = 1000)
    - getTableSizes()
    - getIndexUsage()
    - getVacuumStatus()
    - getLockStatus()
    - getCacheHitRatio()
    - getQueryStats()
}
```

#### 1.3 Queries de Monitoramento
```sql
-- Conexões ativas
SELECT count(*) as total,
       state,
       wait_event_type,
       wait_event
FROM pg_stat_activity
GROUP BY state, wait_event_type, wait_event;

-- Queries lentas
SELECT query,
       mean_exec_time,
       calls,
       total_exec_time
FROM pg_stat_statements
WHERE mean_exec_time > 1000
ORDER BY mean_exec_time DESC
LIMIT 10;

-- Tamanho das tabelas
SELECT schemaname,
       tablename,
       pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size
FROM pg_tables
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;
```

#### 1.4 Sistema de Debug de Banco em Tempo Real

##### Funcionalidades Implementadas

**🎯 Monitoramento Inteligente de Queries SQL**
- **Captura em Tempo Real**: Intercepta todas as queries SQL usando `DB::listen()`
- **Correlação HTTP → SQL**: Mapeia métodos HTTP (GET, POST, PUT, DELETE) com operações SQL
- **Classificação de Performance**: Categoriza queries por tempo de execução
- **Análise de Tabelas**: Identifica quais tabelas são mais utilizadas
- **Backtrace**: Rastreia origem do código que gerou cada query

##### Interface de Usuário

**Dashboard de Monitoramento**: `/admin/monitoring/database`

**Controles Principais:**
```html
[Voltar] [Iniciar Monitoramento] [Parar Monitoramento] [Exportar] [Limpar]
```

**Abas do Sistema:**
- **🔍 Queries em Tempo Real**: Lista queries executadas com badges de performance
- **🌐 Métodos HTTP → SQL**: Cards mostrando correlação GET/POST/PUT/DELETE → SELECT/INSERT/UPDATE/DELETE
- **📈 Estatísticas**: Tabelas com métricas por tipo de query e tabela

##### Implementação Técnica

**Controller de Debug**:
```php
// app/Http/Controllers/DebugController.php
Routes::
- POST /debug/start              -> Inicia sessão de monitoramento
- POST /debug/stop               -> Para sessão de monitoramento
- GET  /debug/status             -> Status da sessão atual
- GET  /debug/database/queries   -> Retorna queries capturadas
- POST /debug/clear-cache        -> Limpa dados em cache
```

**Middleware de Captura**:
```php
// app/Http/Middleware/DebugActionLogger.php
- Captura contexto da requisição HTTP
- Injeta informações de usuário, rota, método
- Correlaciona com queries SQL executadas
```

**Armazenamento em Cache Redis**:
```php
Cache Keys:
- 'db_debug_capturing'    -> Boolean (monitoramento ativo)
- 'db_debug_queries'      -> Array (queries capturadas)
- 'db_debug_start_time'   -> Timestamp (início da sessão)
```

##### Fluxo de Funcionamento

1. **Iniciar Monitoramento**:
   - Ativa `DB::enableQueryLog()` e `DB::listen()`
   - Cria sessão com ID único
   - Inicia captura em cache Redis

2. **Captura de Queries**:
   - Intercepta cada query SQL executada
   - Adiciona contexto HTTP (método, URL, rota, usuário)
   - Classifica performance e extrai tabelas
   - Armazena em cache limitado a 1000 queries

3. **Visualização em Tempo Real**:
   - Polling JavaScript a cada 2 segundos
   - Atualiza widgets de estatísticas
   - Mostra mapeamento HTTP → SQL
   - Exibe backtrace para debug

4. **Exportação e Limpeza**:
   - Export JSON com dados completos da sessão
   - Limpeza de cache mantendo monitoramento ativo
   - Botões inteligentes baseados no estado da sessão

##### Estrutura dos Dados Capturados

```json
{
  "sql": "SELECT * FROM users WHERE id = 5",
  "formatted_sql": "SELECT *\nFROM users\nWHERE id = 5",
  "bindings": [5],
  "time": 2.58,
  "time_formatted": "2.58 ms",
  "type": "SELECT",
  "performance": "excellent",
  "tables": ["users"],
  "timestamp": "2025-09-12T13:40:00.123Z",
  "http_method": "GET",
  "http_url": "http://localhost:8001/admin/monitoring/database",
  "route_name": "admin.monitoring.database",
  "request_id": "uuid-v4",
  "backtrace": [
    {
      "file": "app/Http/Controllers/DebugController.php",
      "line": 295,
      "function": "getCapturedQueries",
      "class": "App\\Services\\DatabaseDebugService"
    }
  ]
}
```

##### Estados dos Botões

**Lógica Inteligente**:
- **Exportar**: Habilitado quando há dados (monitoramento ativo OU dados em cache)
- **Limpar**: Habilitado apenas durante monitoramento ativo
- **Start/Stop**: Alternância baseada no status da sessão

##### Casos de Uso

**Desenvolvimento e Debug**:
- Identificar queries N+1
- Analisar performance de endpoints
- Rastrear origem de queries lentas
- Correlacionar ações do usuário com operações SQL

**Produção e Monitoramento**:
- Monitorar operações críticas (criação de proposições, assinaturas)
- Análisar padrões de uso do banco
- Identificar gargalos em tempo real
- Auditoria de operações SQL

##### Performance e Limitações

**Otimizações**:
- Cache Redis para evitar overhead de I/O
- Limite de 1000 queries em cache
- Polling eficiente com intervalo de 2s
- Formatação lazy de SQL apenas quando necessário

**Considerações**:
- Ativar apenas quando necessário (overhead mínimo)
- Dados sensíveis são filtrados automaticamente
- Sessões têm timeout automático
- Cache é limpo automaticamente ao parar monitoramento

### 2. **Monitoramento de Performance**

#### 2.1 Métricas de Aplicação
- **Tempo de Resposta**: P50, P95, P99 por endpoint
- **Taxa de Erro**: Porcentagem de requisições com erro
- **Throughput**: Requisições por segundo
- **Memória**: Uso de memória PHP/Laravel
- **CPU**: Utilização de CPU por processo
- **Cache Hit Rate**: Eficiência do cache Redis
- **Queue Size**: Tamanho das filas de processamento
- **Jobs Failed**: Jobs que falharam nas últimas 24h

#### 2.2 Implementação
```php
// app/Services/Monitoring/PerformanceMonitor.php
class PerformanceMonitor {
    - getResponseTimes($period = '1h')
    - getErrorRate($period = '1h')
    - getThroughput($period = '1h')
    - getMemoryUsage()
    - getCpuUsage()
    - getCacheStats()
    - getQueueMetrics()
    - getFailedJobs($period = '24h')
}
```

### 3. **Sistema de Logs Centralizado**

#### 3.1 Tipos de Logs
- **Application Logs**: Logs do Laravel (info, warning, error, critical)
- **Access Logs**: Logs de acesso HTTP (nginx/apache)
- **Security Logs**: Tentativas de login, alterações de permissão
- **Audit Logs**: Todas as operações CRUD em entidades críticas
- **Performance Logs**: Queries lentas, endpoints lentos
- **Error Logs**: Exceções e erros de sistema
- **Container Logs**: Logs dos containers Docker

#### 3.2 Estrutura de Log
```json
{
    "timestamp": "2025-09-12T10:30:45.123Z",
    "level": "error",
    "message": "Database connection failed",
    "context": {
        "user_id": 123,
        "session_id": "abc123",
        "request_id": "uuid-v4",
        "ip": "192.168.1.100",
        "user_agent": "Mozilla/5.0...",
        "url": "/admin/proposicoes",
        "method": "GET"
    },
    "exception": {
        "class": "PDOException",
        "message": "Connection refused",
        "file": "/app/Database.php",
        "line": 45,
        "trace": "..."
    }
}
```

#### 3.3 Implementação
```php
// app/Services/Monitoring/LogAggregator.php
class LogAggregator {
    - searchLogs($filters = [])
    - getLogStats($period = '1h')
    - getErrorTrends($period = '24h')
    - getTopErrors($limit = 10)
    - exportLogs($format = 'json')
}
```

### 4. **Monitoramento de Containers**

#### 4.1 Métricas Docker
- **Status**: Running, Stopped, Restarting
- **CPU Usage**: Uso de CPU por container
- **Memory Usage**: Uso de memória por container
- **Network I/O**: Tráfego de rede entrada/saída
- **Disk I/O**: Leitura/escrita em disco
- **Container Logs**: Últimas linhas de log
- **Health Checks**: Status dos health checks
- **Restart Count**: Número de restarts

#### 4.2 Implementação
```php
// app/Services/Monitoring/ContainerMonitor.php
class ContainerMonitor {
    - getContainerStatus()
    - getContainerStats()
    - getContainerLogs($container, $lines = 100)
    - getHealthStatus()
    - getResourceUsage()
    - getNetworkStats()
}
```

### 5. **Rastreamento de Requisições (Request Tracing)**

#### 5.1 Informações Coletadas
- **Request ID**: UUID único para cada requisição
- **User Context**: Usuário, role, IP
- **Timeline**: Tempo em cada componente
- **Database Queries**: Todas as queries executadas
- **Cache Hits/Misses**: Operações de cache
- **External API Calls**: Chamadas para serviços externos
- **Errors/Exceptions**: Erros durante a requisição

#### 5.2 Implementação
```php
// app/Middleware/RequestTracing.php
class RequestTracing {
    public function handle($request, Closure $next) {
        $requestId = Str::uuid();
        $startTime = microtime(true);
        
        // Injeta request ID no contexto
        Log::withContext(['request_id' => $requestId]);
        
        $response = $next($request);
        
        // Coleta métricas
        $this->collectMetrics($request, $response, $startTime);
        
        return $response;
    }
}
```

### 6. **Dashboard de Observabilidade**

#### 6.1 Widgets Principais

##### Widget: Status Geral
```
┌─────────────────────────────────┐
│ 🟢 Sistema Operacional          │
│ Uptime: 15d 3h 42m              │
│ Load: 0.75 / 1.20 / 0.95       │
│ Memória: 4.2GB / 8GB (52%)     │
│ Disco: 45GB / 100GB (45%)      │
└─────────────────────────────────┘
```

##### Widget: Banco de Dados
```
┌─────────────────────────────────┐
│ PostgreSQL Status               │
│ Conexões: 12/100                │
│ Queries/s: 145                  │
│ Cache Hit: 98.5%                │
│ Tamanho: 2.3GB                  │
│ [Ver Detalhes]                  │
└─────────────────────────────────┘
```

##### Widget: Performance
```
┌─────────────────────────────────┐
│ Performance (Últimas 24h)       │
│ Req/s: 234 avg                  │
│ P50: 45ms | P95: 120ms         │
│ Errors: 0.02%                   │
│ [Gráfico de linha]              │
└─────────────────────────────────┘
```

##### Widget: Alertas Ativos
```
┌─────────────────────────────────┐
│ ⚠️ Alertas Ativos (3)           │
│ • CPU alta em app (85%)         │
│ • Query lenta detectada         │
│ • Cache miss rate alto (15%)    │
└─────────────────────────────────┘
```

#### 6.2 Páginas Detalhadas

##### /admin/monitoring/database
- **Queries em tempo real**: Monitoramento live de queries SQL executadas
- **Mapeamento HTTP → SQL**: Correlação entre métodos HTTP e operações SQL
- **Análise de performance**: Classificação por tempo de execução (excellent/good/average/slow/very_slow)
- **Estatísticas de tabelas**: Uso de tabelas e métricas de performance
- **Sistema Start/Stop**: Controle de sessões de monitoramento
- **Export/Import**: Exportação de dados para análise offline
- **Backtrace**: Rastreamento de origem das queries

##### /admin/monitoring/logs
- Busca avançada em logs
- Filtros por nível, período, usuário
- Export de logs
- Análise de padrões

##### /admin/monitoring/performance
- Gráficos de performance
- Análise de endpoints lentos
- Comparação histórica
- Recomendações de otimização

##### /admin/monitoring/traces
- Busca por request ID
- Timeline detalhado
- Waterfall de operações
- Análise de gargalos

### 7. **Sistema de Alertas**

#### 7.1 Tipos de Alertas
- **Critical**: Sistema down, banco indisponível
- **High**: CPU > 90%, Memória > 90%, Disco > 90%
- **Medium**: Queries lentas, Cache miss alto
- **Low**: Jobs falhando, logs de warning

#### 7.2 Canais de Notificação
- Email
- Slack
- Telegram
- SMS (para críticos)
- Dashboard popup

#### 7.3 Configuração
```php
// config/monitoring.php
return [
    'alerts' => [
        'cpu_threshold' => 85,
        'memory_threshold' => 90,
        'disk_threshold' => 85,
        'query_time_threshold' => 1000, // ms
        'error_rate_threshold' => 1, // %
        'cache_miss_threshold' => 20, // %
    ],
    'notifications' => [
        'email' => ['admin@sistema.gov.br'],
        'slack' => ['webhook_url' => env('SLACK_WEBHOOK')],
    ]
];
```

### 8. **Métricas de Negócio**

#### 8.1 KPIs do Sistema
- **Proposições Criadas**: Por dia/semana/mês
- **Taxa de Aprovação**: % de proposições aprovadas
- **Tempo Médio de Tramitação**: Da criação à aprovação
- **Usuários Ativos**: DAU, WAU, MAU
- **Documentos Assinados**: Total e por período
- **Uso do OnlyOffice**: Sessões e tempo de edição

#### 8.2 Implementação
```php
// app/Services/Monitoring/BusinessMetrics.php
class BusinessMetrics {
    - getProposicaoMetrics($period)
    - getApprovalRate($period)
    - getAverageProcessingTime()
    - getActiveUsers($type = 'daily')
    - getDocumentSignatures($period)
    - getOnlyOfficeUsage($period)
}
```

## 🛠️ Implementação Técnica

### Fase 1: Infraestrutura Base (Semana 1)
1. ✅ Criar estrutura de tabelas para métricas
2. ✅ Implementar collectors básicos
3. ✅ Setup do sistema de logs centralizado
4. ✅ Criar middleware de request tracing
5. ✅ **Sistema de Debug de Banco em Tempo Real** - Implementado com correlação HTTP → SQL

### Fase 2: Coleta de Dados (Semana 2)
1. ✅ Implementar monitoramento de banco
2. ✅ Implementar monitoramento de containers
3. ✅ Implementar coleta de métricas de performance
4. ✅ Criar jobs para coleta periódica

### Fase 3: Dashboard (Semana 3)
1. ✅ Criar layout do dashboard
2. ✅ Implementar widgets em tempo real
3. ✅ Criar páginas de detalhamento
4. ✅ Implementar filtros e buscas

### Fase 4: Alertas e Notificações (Semana 4)
1. ✅ Implementar sistema de alertas
2. ✅ Configurar canais de notificação
3. ✅ Criar regras de alertas customizáveis
4. ✅ Implementar histórico de alertas

### Fase 5: Otimizações (Semana 5)
1. ✅ Otimizar queries de coleta
2. ✅ Implementar cache de métricas
3. ✅ Criar índices necessários
4. ✅ Documentar e treinar equipe

## 📊 Estrutura de Banco de Dados

### Tabelas Principais

```sql
-- Tabela de métricas
CREATE TABLE monitoring_metrics (
    id BIGSERIAL PRIMARY KEY,
    metric_type VARCHAR(50) NOT NULL,
    metric_name VARCHAR(100) NOT NULL,
    value NUMERIC(15,4),
    tags JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_metrics_type_time (metric_type, created_at DESC),
    INDEX idx_metrics_tags (tags)
);

-- Tabela de logs agregados
CREATE TABLE monitoring_logs (
    id BIGSERIAL PRIMARY KEY,
    level VARCHAR(20) NOT NULL,
    message TEXT,
    context JSONB,
    exception JSONB,
    request_id UUID,
    user_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_logs_level_time (level, created_at DESC),
    INDEX idx_logs_request (request_id),
    INDEX idx_logs_user (user_id)
);

-- Tabela de traces
CREATE TABLE monitoring_traces (
    id BIGSERIAL PRIMARY KEY,
    request_id UUID NOT NULL,
    span_id VARCHAR(50),
    parent_span_id VARCHAR(50),
    operation VARCHAR(100),
    start_time TIMESTAMP,
    end_time TIMESTAMP,
    duration_ms INTEGER,
    tags JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_traces_request (request_id),
    INDEX idx_traces_duration (duration_ms DESC)
);

-- Tabela de alertas
CREATE TABLE monitoring_alerts (
    id BIGSERIAL PRIMARY KEY,
    alert_type VARCHAR(50) NOT NULL,
    severity VARCHAR(20) NOT NULL,
    message TEXT,
    details JSONB,
    resolved BOOLEAN DEFAULT FALSE,
    resolved_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_alerts_severity (severity, created_at DESC),
    INDEX idx_alerts_resolved (resolved)
);
```

## 🔧 Tecnologias Utilizadas

### Backend
- **Laravel 10**: Framework principal
- **PostgreSQL**: Banco de dados com pg_stat_statements
- **Redis**: Cache e métricas em tempo real
- **Docker**: Monitoramento de containers
- **Horizon**: Monitoramento de queues

### Frontend
- **Vue.js 3**: Dashboard interativo
- **Chart.js**: Gráficos e visualizações
- **WebSockets**: Atualizações em tempo real
- **Tailwind CSS**: Estilização

### Ferramentas de Monitoramento
- **Laravel Telescope**: Debug e profiling (desenvolvimento)
- **Laravel Pulse**: Monitoramento de performance (produção)
- **Spatie Activity Log**: Auditoria de ações
- **Custom Collectors**: Coletores específicos do sistema

## 📈 Benefícios Esperados

1. **Redução de Downtime**: Detecção proativa de problemas
2. **Melhoria de Performance**: Identificação de gargalos
3. **Maior Confiabilidade**: Monitoramento contínuo
4. **Tomada de Decisão**: Dados para decisões técnicas
5. **Compliance**: Auditoria completa de operações
6. **ROI**: Redução de custos com manutenção reativa

## 🚀 Próximos Passos

1. **Aprovação do Projeto**: Validar escopo com stakeholders
2. **Setup Inicial**: Preparar ambiente de desenvolvimento
3. **POC**: Implementar versão mínima para validação
4. **Desenvolvimento**: Implementar fases 1-5
5. **Testes**: Validar em ambiente de staging
6. **Deploy**: Implementar em produção
7. **Monitoramento do Monitoramento**: Meta-observabilidade

## 📝 Notas de Implementação

### Lições Aprendidas - Sistema de Debug de Banco

**Problemas Encontrados e Soluções**:
1. **Incompatibilidade de Formatos**: `DB::getQueryLog()` vs `DB::listen()` usam chaves diferentes (`sql` vs `query`)
   - **Solução**: Suporte para ambos os formatos no `processQuery()`

2. **Erros 500 em Sessões Inativas**: Endpoint bloqueava acesso a dados em cache
   - **Solução**: Permitir acesso a dados mesmo com sessão inativa

3. **Tratamento de Exceções**: Falta de validação causava crashes
   - **Solução**: Try/catch abrangente com logging e fallbacks seguros

4. **Estados dos Botões**: Lógica inconsistente de habilitação/desabilitação
   - **Solução**: Função `updateButtonStates()` com lógica inteligente baseada em dados disponíveis

### Considerações de Performance
- Usar sampling para métricas de alta frequência
- Implementar agregação de logs para reduzir volume
- Usar particionamento de tabelas por data
- Implementar retenção automática de dados antigos

### Segurança
- Sanitizar logs para remover dados sensíveis
- Implementar RBAC para acesso ao dashboard
- Criptografar dados sensíveis em métricas
- Audit log de acesso ao sistema de monitoramento

### Escalabilidade
- Preparar para sharding de dados de métricas
- Implementar cache distribuído para dashboard
- Usar workers assíncronos para coleta pesada
- Preparar para múltiplos ambientes (dev, staging, prod)

## 🎯 Critérios de Sucesso

1. **Cobertura**: 100% dos componentes críticos monitorados
2. **Performance**: Dashboard carrega em < 2 segundos
3. **Confiabilidade**: 99.9% uptime do sistema de monitoramento
4. **Alertas**: < 5 minutos para detecção de problemas críticos
5. **Adoção**: 100% da equipe técnica usando ativamente

---

**Versão**: 1.0.0  
**Data**: 12/09/2025  
**Autor**: Sistema Legisinc - Equipe de Arquitetura  
**Status**: 📋 Planejamento Completo - Aguardando Aprovação para Implementação