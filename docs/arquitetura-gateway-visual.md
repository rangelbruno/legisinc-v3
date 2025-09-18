# Arquitetura Gateway - Visualização Completa

## 🏗️ Estrutura Atual do Projeto

### Arquitetura Geral dos Containers

```mermaid
graph TB
    subgraph "🌐 Frontend & Gateway"
        U[👤 Usuário] --> TK[🚦 Traefik Gateway :8000]
        TK --> |"80%"| L[🏛️ Laravel App :8001]
        TK --> |"20%"| N[⚡ Nova API :3001]
        L --> |"Mirror"| NS[🔍 Nginx Shadow :8002]
        NS --> |"Shadow Copy"| N
    end

    subgraph "🔍 Monitoramento"
        TK --> |"Métricas"| P[📊 Prometheus :9090]
        P --> G[📈 Grafana :3000]
        CM[🔄 Canary Monitor :3003] --> |"Controla %"| TK
        SC[📋 Shadow Comparator :3002] --> |"Compara Respostas"| CM
    end

    subgraph "💾 Dados"
        L --> DB[(🐘 PostgreSQL :5432)]
        N --> DB
        L --> R[(🔴 Redis :6379)]
        N --> R
    end

    subgraph "📄 Documentos"
        L --> OO[📝 OnlyOffice :8080]
    end

    style TK fill:#e1f5fe
    style L fill:#f3e5f5
    style N fill:#e8f5e8
    style CM fill:#fff3e0
    style DB fill:#fce4ec
    style R fill:#ffebee
```

### Detalhamento dos Serviços

```mermaid
graph LR
    subgraph "🚦 Gateway Layer"
        A[Traefik v3.0] --> |"API Routes"| B[Weighted Service]
        B --> |"99% Laravel"| C[Laravel App]
        B --> |"1% Canary"| D[Nova API]
        A --> |"Web Routes"| C
        A --> |"OnlyOffice"| E[OnlyOffice]
    end

    subgraph "🔍 Observabilidade"
        F[Prometheus] --> |"Métricas"| G[Grafana]
        H[Shadow Comparator] --> |"Logs"| I[Canary Monitor]
        I --> |"Auto Scale"| B
    end

    subgraph "💾 Persistência"
        C --> J[(PostgreSQL)]
        D --> J
        C --> K[(Redis)]
        D --> K
    end
```

## 🔄 Fluxo de Requisições

### 1. Rota Normal (Web)

```mermaid
sequenceDiagram
    participant U as 👤 Usuário
    participant T as 🚦 Traefik
    participant L as 🏛️ Laravel
    participant DB as 💾 Database

    U->>T: GET /proposicoes
    T->>L: Forward request
    L->>DB: Query data
    DB-->>L: Return results
    L-->>T: HTML response
    T-->>U: Render page
```

### 2. Rota API com Canary

```mermaid
sequenceDiagram
    participant U as 👤 Usuário
    participant T as 🚦 Traefik
    participant WS as ⚖️ Weighted Service
    participant L as 🏛️ Laravel
    participant N as ⚡ Nova API
    participant DB as 💾 Database
    participant CM as 🔄 Canary Monitor

    U->>T: GET /api/parlamentares/buscar
    T->>WS: Route to weighted service

    alt 99% das vezes
        WS->>L: Forward to Laravel
        L->>DB: Query parlamentares
        DB-->>L: Return data
        L-->>WS: JSON response
    else 1% das vezes (Canary)
        WS->>N: Forward to Nova API
        N->>DB: Query parlamentares
        DB-->>N: Return data
        N-->>WS: JSON response
    end

    WS-->>T: Response
    T-->>U: JSON data

    Note over CM: Monitor métricas e ajusta %
    CM->>WS: Update weights if healthy
```

### 3. Shadow Traffic (Teste Paralelo)

```mermaid
sequenceDiagram
    participant U as 👤 Usuário
    participant NS as 🔍 Nginx Shadow
    participant L as 🏛️ Laravel
    participant N as ⚡ Nova API
    participant SC as 📋 Shadow Comparator

    U->>NS: GET /api/endpoint

    par Produção
        NS->>L: Main request
        L-->>NS: Response A
        NS-->>U: Return response A
    and Shadow
        NS->>N: Mirror request
        N-->>NS: Response B (not sent to user)
    end

    NS->>SC: Compare responses A vs B
    SC->>SC: Log differences

    Note over SC: Valida compatibilidade<br/>sem impactar usuário
```

## 🚀 Processo de Migration Backend

### Etapa 1: Gateway Setup

```mermaid
graph TD
    A[🎯 Objetivo: Zero Downtime] --> B[🚦 Setup Traefik Gateway]
    B --> C[📊 Configure Prometheus + Grafana]
    C --> D[🔧 Route 100% to Laravel]
    D --> E[✅ Gateway Funcionando]

    style A fill:#ffeb3b
    style E fill:#4caf50
```

### Etapa 2: Shadow Traffic

```mermaid
graph TD
    A[✅ Gateway Funcionando] --> B[⚡ Deploy Nova API]
    B --> C[🔍 Configure Nginx Mirror]
    C --> D[📋 Setup Shadow Comparator]
    D --> E[🧪 Test Endpoints Paralelos]
    E --> F[📊 Validate Compatibility]
    F --> G[✅ Shadow Traffic OK]

    style A fill:#4caf50
    style G fill:#4caf50
```

### Etapa 3: Canary Deployment

```mermaid
graph TD
    A[Shadow Traffic OK] --> B[Setup Canary Monitor]
    B --> C[Configure Weighted Routes]
    C --> D[Start with 1% Traffic]
    D --> E[Auto-scale Based on Metrics]
    E --> F{Healthy?}
    F -->|Yes| G[Increase Gradually]
    F -->|No| H[Rollback to 0%]
    G --> I[Reach 100% Gradually]
    H --> J[Fix Issues]
    J --> D
    I --> K[Full Migration]

    style A fill:#4caf50
    style K fill:#4caf50
    style H fill:#f44336
```

### Etapa 4: Full Replacement

```mermaid
graph TD
    A[Full Migration] --> B[Expand Nova API Endpoints]
    B --> C[Monitor All Routes]
    C --> D[Migrate Endpoint by Endpoint]
    D --> E[Deprecate Laravel Routes]
    E --> F[Keep Laravel for Legacy]
    F --> G[Nova API as Primary]
    G --> H[Backend Replacement Complete]

    style A fill:#4caf50
    style H fill:#4caf50
```

## 📊 Estados dos Containers

### Desenvolvimento Atual

```mermaid
stateDiagram-v2
    [*] --> Gateway_Only: Etapa 1
    Gateway_Only --> Shadow_Active: Etapa 2
    Shadow_Active --> Canary_1: Etapa 3
    Canary_1 --> Canary_Auto: Auto-scaling
    Canary_Auto --> Full_Canary: 100% Nova API
    Full_Canary --> [*]: Migration Complete

    Canary_Auto --> Rollback: Health Issues
    Rollback --> Canary_1: Fix & Retry

    Gateway_Only : 100% Laravel<br/>Traefik + Observability
    Shadow_Active : Laravel + Shadow Nova API<br/>Comparing responses
    Canary_1 : 99% Laravel + 1% Nova API<br/>Monitoring metrics
    Full_Canary : 100% Nova API<br/>Laravel as fallback
```

## 🔧 Como Trocar Backend: Exemplo Prático

### Adicionando Novo Endpoint

```mermaid
graph TD
    A[Novo Endpoint tipos-proposicao] --> B[Implementar na Nova API]
    B --> C[Testar via Shadow Traffic]
    C --> D{Responses Compatible?}
    D -->|No| E[Fix Nova API]
    E --> C
    D -->|Yes| F[Add to Weighted Routes]
    F --> G[Start Canary 1%]
    G --> H[Auto-scale if Healthy]
    H --> I[Route Live on Nova API]

    style A fill:#ffeb3b
    style I fill:#4caf50
    style E fill:#f44336
```

### Configuração Step-by-Step

```mermaid
sequenceDiagram
    participant Dev as 👨‍💻 Developer
    participant API as ⚡ Nova API
    participant Config as ⚚ Traefik Config
    participant Monitor as 🔄 Canary Monitor

    Dev->>API: 1. Implement endpoint
    Note over API: GET /api/tipos-proposicao<br/>Compatible response format

    Dev->>Config: 2. Add weighted route
    Note over Config: tipos-proposicao-weighted:<br/>1% Nova API + 99% Laravel

    Config->>Monitor: 3. Monitor detects new route
    Monitor->>Monitor: 4. Start health checks
    Monitor->>Config: 5. Auto-scale if healthy
    Note over Monitor: 1% → 2% → 4% → 8% → 16% → 32% → 64% → 100%

    Dev->>Monitor: 6. Check metrics
    Note over Monitor: Error rate: <5%<br/>Latency: <500ms<br/>Status: HEALTHY
```

## 🔍 Monitoramento em Tempo Real

### Dashboard de Métricas

```mermaid
graph TB
    subgraph "📊 Grafana Dashboard"
        A[📈 Request Rate] --> D[🎯 Overall Health]
        B[⏱️ Response Time] --> D
        C[❌ Error Rate] --> D
    end

    subgraph "🔄 Canary Monitor API"
        E[GET /status] --> F[Current State]
        G[POST /canary/update] --> H[Manual Control]
        I[POST /canary/rollback] --> J[Emergency Stop]
    end

    subgraph "🚦 Traefik Dashboard"
        K[Services Status] --> L[Load Balancing]
        M[Health Checks] --> L
        N[Route Mapping] --> L
    end

    D --> O[🚨 Alertas Automáticos]
    L --> O
    F --> O
```

### Endpoints de Controle

| Endpoint | Método | Descrição |
|----------|--------|-----------|
| `http://localhost:3003/status` | GET | Status atual do canary |
| `http://localhost:3003/metrics/history` | GET | Histórico de métricas |
| `http://localhost:3003/canary/update` | POST | Controle manual do % |
| `http://localhost:3003/canary/rollback` | POST | Rollback de emergência |
| `http://localhost:8090/dashboard/` | GET | Dashboard Traefik |
| `http://localhost:3000` | GET | Grafana (admin/admin) |

## 🛡️ Rollback e Segurança

### Estratégias de Rollback

```mermaid
graph TD
    A[🚨 Problema Detectado] --> B{Automatic?}
    B -->|Yes| C[🤖 Auto Rollback]
    B -->|No| D[👨‍💻 Manual Rollback]

    C --> E[⚡ Set to 0% in 5s]
    D --> F[🔧 POST /canary/rollback]

    E --> G[📊 Monitor Recovery]
    F --> G

    G --> H{Issues Fixed?}
    H -->|Yes| I[🔄 Restart Canary]
    H -->|No| J[🏛️ Stay on Laravel]

    I --> K[📈 1% → Auto-scale]
    J --> L[🔧 Debug & Fix]
    L --> I

    style A fill:#f44336
    style E fill:#ff9800
    style F fill:#ff9800
    style K fill:#4caf50
```

### Triggers de Rollback Automático

- **Error Rate > 5%**: Muitos erros 500/400
- **Latency > 500ms**: Resposta muito lenta
- **Health Check Fail**: Endpoint /health retorna erro
- **Manual Trigger**: Desenvolvedor força rollback

## 🎯 Benefícios da Arquitetura

```mermaid
mindmap
  root((Gateway Architecture))
    Zero Downtime
      Canary Deployment
      Health Monitoring
      Auto Rollback
    Observabilidade
      Métricas Tempo Real
      Logs Estruturados
      Dashboards Visuais
    Flexibilidade
      Multiple Backends
      A/B Testing
      Feature Flags
    Segurança
      Rollback Instantâneo
      Shadow Testing
      Gradual Migration
```

---

**📝 Documentação Técnica**: LegisInc v2 Gateway
**📅 Data**: 2025-09-17
**🏗️ Status**: ✅ Implementado e Funcionando
**🔄 Versão**: 1.0

---

> **💡 Esta arquitetura permite migration de backend sem risco, com observabilidade completa e rollback automático!**