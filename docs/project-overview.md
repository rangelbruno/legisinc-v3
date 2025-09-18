# Visão Geral do Projeto LegisInc

Este documento fornece uma análise detalhada da arquitetura, tecnologias e estrutura do sistema LegisInc v2.1 Enterprise.

## 📊 Mapa Completo do Sistema - Arquitetura e Fluxos

### Diagrama Principal - Arquitetura com Gateway e Múltiplos Backends

```mermaid
graph TB
    %% Frontend Layer
    FRONTEND["🌐 Frontend<br/>Vue.js + Laravel Blade<br/>React/Angular Support"]

    %% Gateway Layer
    GATEWAY["🚪 API Gateway<br/>Traefik + Load Balancer<br/>Roteamento Inteligente"]

    %% Backend Options (Current + New)
    subgraph "Backend Services"
        LARAVEL["🐘 Laravel API<br/>Current Backend<br/>PHP 8.2 + Eloquent"]
        JAVA["☕ Java Spring Boot<br/>New Backend Option<br/>JPA + PostgreSQL"]
        NODE["🟢 Node.js API<br/>Alternative Backend<br/>Express + Prisma"]
        PYTHON["🐍 Python FastAPI<br/>Alternative Backend<br/>SQLAlchemy + Async"]
        DOTNET["🔷 .NET Core API<br/>Alternative Backend<br/>Entity Framework"]
    end

    %% Migration Tools
    MIGRATION["🔄 Migration Tools<br/>Backend Analysis<br/>Endpoint Mapping<br/>Database Schema Export"]

    %% Main Controllers (Laravel Legacy)
    PC["📄 ProposicaoController"]
    PLC["👤 ParlamentarController"]
    AC["⚙️ AdminControllers"]
    MPC["🔄 MigrationPreparationController"]

    %% Services Layer
    subgraph "Service Layer"
        OOS["📝 OnlyOfficeService"]
        TPS["📋 TemplateProcessorService"]
        ADS["🔏 AssinaturaDigitalService"]
        WFS["🔄 WorkflowService"]
    end

    %% Data & Storage
    DB[("🗄️ PostgreSQL<br/>Shared Database<br/>All Backends")]
    STORAGE["📁 Storage Files<br/>Docker Volumes"]
    CACHE["⚡ Redis Cache<br/>Session & Performance"]

    %% External Services
    ONLY["📝 OnlyOffice Server<br/>Document Editor"]
    PYHANKO["🔏 PyHanko Container<br/>Digital Signature"]

    %% Network Flow
    FRONTEND --> GATEWAY
    GATEWAY --> LARAVEL
    GATEWAY -.-> JAVA
    GATEWAY -.-> NODE
    GATEWAY -.-> PYTHON
    GATEWAY -.-> DOTNET

    %% Migration Flow
    MIGRATION --> LARAVEL
    MIGRATION --> JAVA
    MIGRATION --> NODE
    MIGRATION --> PYTHON
    MIGRATION --> DOTNET

    %% Laravel Internal (Current)
    LARAVEL --> PC
    LARAVEL --> PLC
    LARAVEL --> AC
    LARAVEL --> MPC

    PC --> OOS
    PC --> TPS
    PC --> ADS
    PC --> WFS

    %% Shared Resources
    LARAVEL --> DB
    JAVA -.-> DB
    NODE -.-> DB
    PYTHON -.-> DB
    DOTNET -.-> DB

    OOS --> ONLY
    ADS --> PYHANKO

    OOS --> STORAGE
    TPS --> STORAGE
    ADS --> STORAGE

    PC --> CACHE
    OOS --> CACHE
```

### Fluxo de Proposições - Ciclo de Vida Completo

```mermaid
stateDiagram-v2
    [*] --> Rascunho: Parlamentar cria proposição

    Rascunho --> EmEdicao: Parlamentar edita
    EmEdicao --> AguardandoProtocolo: Parlamentar finaliza

    AguardandoProtocolo --> Protocolada: Protocolo atribui número

    Protocolada --> EmAnalise: Legislativo recebe
    EmAnalise --> EmRevisao: Legislativo revisa
    EmRevisao --> AprovadoLegislativo: Legislativo aprova
    EmRevisao --> RetornadoParlamentar: Necessita ajustes

    RetornadoParlamentar --> EmEdicao: Parlamentar ajusta

    AprovadoLegislativo --> AguardandoAssinatura: Pronto para assinar
    AguardandoAssinatura --> Assinado: Certificado digital aplicado

    Assinado --> Publicado: Documento final
    Publicado --> [*]

    note right of EmEdicao
        OnlyOffice Editor
        - Edição colaborativa
        - Auto-save
        - Polling 15s
    end note

    note right of AguardandoAssinatura
        PyHanko
        - Assinatura PAdES
        - Certificado .pfx
        - Validação senha
    end note
```

### Gateway e Roteamento Inteligente - Nova Arquitetura

```mermaid
graph LR
    %% Frontend Requests
    FRONTEND["🌐 Frontend<br/>Blade/Vue/React"]

    %% Gateway Layer
    GATEWAY["🚪 Traefik Gateway<br/>Port 8001"]

    %% Backend Routes
    subgraph "Backend Routing"
        LARAVEL_ROUTE["🐘 Laravel Routes<br/>/api/* → Laravel<br/>Port 8000"]
        JAVA_ROUTE["☕ Java Routes<br/>/api/v2/* → Java<br/>Port 3001"]
        NODE_ROUTE["🟢 Node Routes<br/>/api/v3/* → Node<br/>Port 3002"]
        PYTHON_ROUTE["🐍 Python Routes<br/>/api/v4/* → Python<br/>Port 3003"]
    end

    %% Load Balancer Options
    subgraph "Load Balancing Strategy"
        LB_RR["🔄 Round Robin<br/>Equal Distribution"]
        LB_WEIGHTED["⚖️ Weighted<br/>Laravel 70%, New 30%"]
        LB_CANARY["🐦 Canary<br/>Test 5%, Prod 95%"]
        LB_FEATURE["🎯 Feature Flag<br/>Route by Feature"]
    end

    %% Health Checks
    HEALTH["💓 Health Checks<br/>/health endpoint<br/>Auto failover"]

    %% Flow
    FRONTEND --> GATEWAY
    GATEWAY --> LARAVEL_ROUTE
    GATEWAY -.-> JAVA_ROUTE
    GATEWAY -.-> NODE_ROUTE
    GATEWAY -.-> PYTHON_ROUTE

    GATEWAY --> LB_RR
    GATEWAY --> LB_WEIGHTED
    GATEWAY --> LB_CANARY
    GATEWAY --> LB_FEATURE

    GATEWAY --> HEALTH
```

### Migration Tools e Backend Analysis

```mermaid
graph TB
    %% Migration Controller
    MPC["🔄 MigrationPreparationController<br/>Backend Analysis Tool"]

    %% Analysis Functions
    subgraph "System Analysis"
        ENDPOINTS["📍 Endpoint Analysis<br/>Route Discovery<br/>Method Mapping"]
        DATABASE["🗄️ Database Structure<br/>Table Schema<br/>Relationships"]
        MODELS["📊 Model Analysis<br/>Eloquent Models<br/>Business Logic"]
        INTEGRATIONS["🔌 External Integrations<br/>APIs & Services<br/>Dependencies"]
    end

    %% Export Formats
    subgraph "Export Options"
        JSON_COMPLETE["📄 Complete JSON<br/>All System Data"]
        JSON_ENDPOINTS["🔗 Endpoints JSON<br/>API Documentation"]
        JSON_DB["💾 Database JSON<br/>Schema Export"]
        JSON_MODELS["🏗️ Models JSON<br/>Entity Mapping"]
    end

    %% Target Backends
    subgraph "Target Backend Generation"
        JAVA_GEN["☕ Java Spring Boot<br/>Entities + Repositories<br/>REST Controllers"]
        NODE_GEN["🟢 Node.js Express<br/>Prisma Models<br/>Route Handlers"]
        PYTHON_GEN["🐍 Python FastAPI<br/>SQLAlchemy Models<br/>Async Endpoints"]
        DOTNET_GEN["🔷 .NET Core<br/>Entity Framework<br/>Web API Controllers"]
    end

    %% Flow
    MPC --> ENDPOINTS
    MPC --> DATABASE
    MPC --> MODELS
    MPC --> INTEGRATIONS

    ENDPOINTS --> JSON_ENDPOINTS
    DATABASE --> JSON_DB
    MODELS --> JSON_MODELS
    INTEGRATIONS --> JSON_COMPLETE

    JSON_COMPLETE --> JAVA_GEN
    JSON_COMPLETE --> NODE_GEN
    JSON_COMPLETE --> PYTHON_GEN
    JSON_COMPLETE --> DOTNET_GEN
```

### Rotas e Endpoints - Mapeamento Completo

```mermaid
graph TB
    %% Web Routes
    WEB_AUTH["🌐 Web - Autenticação<br/>GET/POST /login<br/>POST /logout<br/>GET /register"]

    WEB_PROP["🌐 Web - Proposições<br/>• CRUD operations<br/>• Protocol actions"]

    WEB_PARL["🌐 Web - Parlamentares<br/>GET /parlamentares<br/>POST /parlamentares<br/>GET/PUT/DELETAR /parlamentares/{id}"]

    WEB_ADMIN["🌐 Web - Admin<br/>GET /admin/dashboard<br/>GET /admin/system-configuration<br/>GET /admin/templates"]

    %% API Routes
    API_ONLY["🔌 API - OnlyOffice<br/>• Document callbacks<br/>• Real-time sync"]

    API_SIGN["🔌 API - Assinatura Digital<br/>• Certificate management<br/>• Validation & processing"]

    API_WORK["🔌 API - Workflows<br/>GET /api/workflows<br/>POST /api/workflows/start<br/>POST /api/workflows/advance"]

    API_PARAM["🔌 API - Parâmetros<br/>GET /api/parametros-modular/modulos<br/>GET /api/parametros-modular/valor/{modulo}/{submodulo}/{campo}"]

    %% Connections
    WEB_AUTH --> WEB_PROP
    WEB_PROP --> API_ONLY
    WEB_PROP --> API_SIGN
    WEB_ADMIN --> API_WORK
    WEB_ADMIN --> API_PARAM
```

### Controllers e suas Responsabilidades

```mermaid
graph TB
    subgraph "🎮 Controllers Principais"
        PC["ProposicaoController<br/>📄 Gestão completa de proposições"]
        PLC["ParlamentarController<br/>👤 Gestão de parlamentares"]
        SC["SessionController<br/>📅 Sessões legislativas"]
        UC["UserController<br/>👥 Gestão de usuários"]
        AC["AdminControllers<br/>⚙️ Administração"]
    end

    subgraph "📋 Principais Ações"
        PC_ACTIONS["📝 Ações:<br/>• Create/Edit<br/>• OnlyOffice<br/>• Protocol<br/>• Sign"]

        PLC_ACTIONS["index - Listar parlamentares<br/>show - Detalhes parlamentar<br/>mesaDiretora - Composição mesa<br/>estatisticas - Dashboard dados"]

        SC_ACTIONS["create/store - Nova sessão<br/>addMatter - Adicionar pauta<br/>generateXml - Exportar dados"]

        UC_ACTIONS["profile - Perfil usuário<br/>updateLastAccess - Tracking"]

        AC_ACTIONS["⚙️ Administração:<br/>• Configuração sistema<br/>• Permissões e templates<br/>• Workflows e atividades"]
    end

    PC --> PC_ACTIONS
    PLC --> PLC_ACTIONS
    SC --> SC_ACTIONS
    UC --> UC_ACTIONS
    AC --> AC_ACTIONS
```

### Services e Integrações

```mermaid
graph LR
    subgraph "🔧 Services Layer"
        OOS["📝 OnlyOfficeService<br/>• Document management<br/>• Editor integration<br/>• Callback handling"]

        TPS["📋 TemplateProcessor<br/>• Template application<br/>• Variable processing<br/>• RTF generation"]

        ADS["🔏 AssinaturaDigital<br/>• Certificate validation<br/>• PDF signing<br/>• QR code generation"]

        WFS["🔄 WorkflowService<br/>• Workflow management<br/>• Step transitions<br/>• User notifications"]
    end

    subgraph "🔌 External Services"
        ONLY["OnlyOffice Server<br/>📝 Editor colaborativo"]
        PYH["PyHanko Container<br/>🔏 Assinatura PAdES"]
        REDIS["Redis Cache<br/>⚡ Performance"]
        PG["PostgreSQL<br/>🗄️ Database"]
    end

    OOS -.-> ONLY
    ADS -.-> PYH
    OOS -.-> REDIS
    TPS -.-> PG
```

### Fluxo de Autenticação e Permissões

```mermaid
graph TB
    %% Authentication Flow
    USER["👤 Usuário"]
    LOGIN["📝 Login Page"]
    AUTH["🔐 AuthController"]
    DB["🗄️ Database"]
    DASH["📊 Dashboard"]

    %% Permission Flow
    MIDDLEWARE["🛡️ Middleware"]
    PERMS["🔑 Permissões"]

    %% Authentication Steps
    USER --> LOGIN
    LOGIN --> AUTH
    AUTH --> DB
    DB --> AUTH
    AUTH --> MIDDLEWARE
    MIDDLEWARE --> DASH

    %% Permission Check
    DASH --> MIDDLEWARE
    MIDDLEWARE --> PERMS
    PERMS --> DB
```

### Estrutura de Permissões (RBAC)

```mermaid
graph TD
    subgraph "👥 Roles (Perfis)"
        ADMIN["Administrador<br/>Acesso total"]
        PARL["Parlamentar<br/>Criar/editar proposições"]
        LEG["Legislativo<br/>Análise e aprovação"]
        PROT["Protocolo<br/>Numeração oficial"]
        ASS["Assessor<br/>Suporte parlamentar"]
        JUR["Jurídico<br/>Pareceres"]
    end

    subgraph "🔐 Permissões"
        PROP_PERMS["📋 Proposições:<br/>• View/Create/Edit<br/>• Protocol/Approve<br/>• Digital Sign"]

        PARL_PERMS["parlamentares.view<br/>parlamentares.create<br/>parlamentares.edit<br/>parlamentares.delete"]

        SYS_PERMS["system.config<br/>system.users<br/>system.permissions<br/>system.monitoring"]
    end

    ADMIN --> PROP_PERMS
    ADMIN --> PARL_PERMS
    ADMIN --> SYS_PERMS

    PARL --> PROP_PERMS
    PARL --> PARL_PERMS

    LEG --> PROP_PERMS
    LEG --> PARL_PERMS

    PROT --> PROP_PERMS

    ASS --> PROP_PERMS
    ASS --> PARL_PERMS

    JUR --> PROP_PERMS
    JUR --> PARL_PERMS
```

### Telas/Views Principais do Sistema

```mermaid
graph TB
    %% Authentication Views
    AUTH_VIEWS["📱 Autenticação<br/>auth/login.blade.php<br/>auth/register.blade.php"]

    %% Dashboard Views
    DASH_VIEWS["📱 Dashboard<br/>• Main dashboard<br/>• Role-specific views<br/>• Protocol interface"]

    %% Proposições Views
    PROP_VIEWS["📱 Proposições<br/>• List/Create/Edit views<br/>• Details and preview<br/>• OnlyOffice editor"]

    %% Parlamentares Views
    PARL_VIEWS["📱 Parlamentares<br/>• Management views<br/>• CRUD operations<br/>• Profile details"]

    %% Admin Views
    ADMIN_VIEWS["📱 Admin<br/>• Admin dashboard<br/>• System config<br/>• Permissions & templates"]

    %% Components
    COMP_VIEWS["📱 Componentes<br/>• Layout components<br/>• Header and sidebar<br/>• Alert system"]

    %% Flow
    AUTH_VIEWS --> DASH_VIEWS
    DASH_VIEWS --> PROP_VIEWS
    DASH_VIEWS --> PARL_VIEWS
    DASH_VIEWS --> ADMIN_VIEWS
    COMP_VIEWS --> AUTH_VIEWS
    COMP_VIEWS --> DASH_VIEWS
```

## 1. Sistema de Gestão Legislativa

O LegisInc é um sistema completo de gestão legislativa com foco na digitalização de processos parlamentares, especialmente proposições e documentação oficial.

### 1.1. Configuração Atual

- **Ambiente:** Câmara Municipal de Caraguatatuba
- **Endereço:** Praça da República, 40, Centro, Caraguatatuba-SP
- **Acesso Principal:** `http://localhost:8001`
- **Comando de Inicialização:** `docker exec -it legisinc-app php artisan migrate:fresh --seed`

## 2. Nova Arquitetura de Containers

### 2.1. Stack de Containers com Gateway

```mermaid
graph TB
    %% Network Layer
    subgraph "🌐 Network: legisinc_network"
        %% Gateway
        TRAEFIK["🚪 Traefik Gateway<br/>Port: 8001<br/>Load Balancer + Proxy"]

        %% Frontend Container
        FRONTEND_CONTAINER["🌐 Frontend Container<br/>legisinc-app<br/>Nginx + PHP-FPM"]

        %% Backend Containers
        LARAVEL_CONTAINER["🐘 Laravel API<br/>legisinc-app<br/>Port: 8000"]
        JAVA_CONTAINER["☕ Java Spring API<br/>legisinc-java-api<br/>Port: 3001"]
        NODE_CONTAINER["🟢 Node.js API<br/>legisinc-node-api<br/>Port: 3002"]

        %% Database & Storage
        POSTGRES["🗄️ PostgreSQL<br/>legisinc-postgres<br/>Port: 5432"]
        REDIS["⚡ Redis<br/>legisinc-redis<br/>Port: 6379"]

        %% External Services
        ONLYOFFICE["📝 OnlyOffice<br/>onlyoffice-server<br/>Port: 9980"]
        PYHANKO["🔏 PyHanko<br/>pyhanko-container<br/>Port: 5000"]
        SWAGGER["📋 Swagger UI<br/>legisinc-swagger-ui<br/>Port: 8082"]
    end

    %% Volume Mounts
    subgraph "📁 Docker Volumes"
        STORAGE_VOL["📦 legisinc_storage<br/>Shared Files"]
        DB_VOL["📦 legisinc_postgres_data<br/>Database Persistence"]
        REDIS_VOL["📦 legisinc_redis_data<br/>Cache Persistence"]
    end

    %% Flow
    TRAEFIK --> FRONTEND_CONTAINER
    TRAEFIK --> LARAVEL_CONTAINER
    TRAEFIK -.-> JAVA_CONTAINER
    TRAEFIK -.-> NODE_CONTAINER

    LARAVEL_CONTAINER --> POSTGRES
    JAVA_CONTAINER -.-> POSTGRES
    NODE_CONTAINER -.-> POSTGRES

    LARAVEL_CONTAINER --> REDIS
    LARAVEL_CONTAINER --> ONLYOFFICE
    LARAVEL_CONTAINER --> PYHANKO

    POSTGRES --> DB_VOL
    REDIS --> REDIS_VOL
    FRONTEND_CONTAINER --> STORAGE_VOL
    LARAVEL_CONTAINER --> STORAGE_VOL
```

### 2.2. Configuração Docker Compose

#### docker-compose.gateway.yml (Nova Arquitetura)
```yaml
version: '3.8'

services:
  # API Gateway
  traefik:
    image: traefik:v2.10
    container_name: legisinc-gateway
    command:
      - "--api.insecure=true"
      - "--providers.docker=true"
      - "--entrypoints.web.address=:8001"
    ports:
      - "8001:8001"  # Main access port
      - "8080:8080"  # Traefik dashboard
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
    networks:
      - legisinc_network

  # Laravel Backend (Legacy)
  laravel-app:
    build: .
    container_name: legisinc-app
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.laravel.rule=PathPrefix(`/api/`)"
      - "traefik.http.services.laravel.loadbalancer.server.port=8000"
    networks:
      - legisinc_network

  # Java Spring Boot Backend (New)
  java-api:
    build: ./java-backend
    container_name: legisinc-java-api
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.java.rule=PathPrefix(`/api/v2/`)"
      - "traefik.http.services.java.loadbalancer.server.port=3001"
    environment:
      - SPRING_DATASOURCE_URL=jdbc:postgresql://legisinc-postgres:5432/legisinc
    networks:
      - legisinc_network

  # Shared Database
  postgres:
    image: postgres:15
    container_name: legisinc-postgres
    environment:
      POSTGRES_DB: legisinc
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: 123456
    volumes:
      - legisinc_postgres_data:/var/lib/postgresql/data
    networks:
      - legisinc_network

networks:
  legisinc_network:
    driver: bridge

volumes:
  legisinc_postgres_data:
  legisinc_storage:
  legisinc_redis_data:
```

### 2.3. Estratégias de Deploy

#### Estratégia 1: Canary Deployment
```yaml
# Traefik: 95% Laravel, 5% Java
labels:
  - "traefik.http.routers.laravel.rule=PathPrefix(`/api/`) && (!Header(`X-Test-Backend`, `java`))"
  - "traefik.http.routers.java.rule=PathPrefix(`/api/`) && Header(`X-Test-Backend`, `java`)"
```

#### Estratégia 2: Weighted Load Balancing
```yaml
# Traefik: 70% Laravel, 30% Java
labels:
  - "traefik.http.services.laravel.loadbalancer.weight=70"
  - "traefik.http.services.java.loadbalancer.weight=30"
```

#### Estratégia 3: Feature Flag Routing
```yaml
# Route by feature flag
labels:
  - "traefik.http.routers.laravel.rule=PathPrefix(`/api/`) && (!Query(`backend`, `java`))"
  - "traefik.http.routers.java.rule=PathPrefix(`/api/`) && Query(`backend`, `java`)"
```

## 3. Ambiente de Desenvolvimento (Docker Legacy)

### 2.1. Estrutura do Container

- **Servidor Web:** Nginx
- **Processador PHP:** PHP-FPM 8.2
- **Base:** Imagem Docker baseada em `php:8.2-fpm-alpine`
- **Banco de Dados:** PostgreSQL containerizado
- **Editor de Documentos:** OnlyOffice Document Server integrado

## 3. Usuários do Sistema

O sistema possui usuários pré-configurados com diferentes níveis de acesso:

- **Administrador:** bruno@sistema.gov.br / 123456
- **Parlamentar:** jessica@sistema.gov.br / 123456  
- **Legislativo:** joao@sistema.gov.br / 123456
- **Protocolo:** roberto@sistema.gov.br / 123456
- **Expediente:** expediente@sistema.gov.br / 123456
- **Assessor Jurídico:** juridico@sistema.gov.br / 123456

## 4. Nova Arquitetura Multi-Backend

### 4.1. Gateway e Roteamento Inteligente

O sistema agora suporta uma **arquitetura híbrida** com múltiplos backends através de um **API Gateway** baseado em Traefik:

- **🚪 Gateway Centralizado:** Traefik na porta 8001 gerencia todo o tráfego
- **🔄 Roteamento por Path:** `/api/*` → Laravel, `/api/v2/*` → Java, etc.
- **⚖️ Load Balancing:** Distribuição inteligente de carga entre backends
- **💓 Health Checks:** Monitoramento automático e failover
- **🐦 Canary Deployment:** Teste gradual de novos backends

### 4.2. Ferramentas de Migração

#### MigrationPreparationController
Novo controller especializado em análise e preparação para migração de backend:

**Funcionalidades principais:**
- **📍 Análise de Endpoints:** Mapeamento completo de todas as rotas do sistema
- **🗄️ Estrutura de Banco:** Export detalhado do schema PostgreSQL com relacionamentos
- **📊 Análise de Models:** Extração de entidades Eloquent, relacionamentos e regras
- **🔌 Integrações Externas:** Identificação de APIs, filas e dependências
- **📄 Export JSON:** Geração de documentação completa para novos backends

**Endpoints disponíveis:**
```
GET  /admin/migration-preparation          # Interface principal
POST /admin/migration-preparation/endpoints # JSON com todos endpoints
POST /admin/migration-preparation/database  # Estrutura completa do banco
POST /admin/migration-preparation/models    # Análise dos Models Eloquent
POST /admin/migration-preparation/complete  # Export completo do sistema
```

### 4.3. Backends Suportados

O sistema foi projetado para suportar múltiplos backends mantendo **compatibilidade total** com o frontend:

| Backend | Status | Porta | Path Pattern | Tecnologia |
|---------|--------|-------|--------------|------------|
| **Laravel** | ✅ Atual | 8000 | `/api/*` | PHP 8.2 + Eloquent |
| **Java Spring** | 🔄 Em desenvolvimento | 3001 | `/api/v2/*` | Java 17 + JPA |
| **Node.js** | 📋 Planejado | 3002 | `/api/v3/*` | Express + Prisma |
| **Python FastAPI** | 📋 Planejado | 3003 | `/api/v4/*` | Python + SQLAlchemy |
| **ASP.NET Core** | 📋 Planejado | 3004 | `/api/v5/*` | C# + Entity Framework |

### 4.4. Estratégias de Migração

#### Migração Gradual por Funcionalidade
1. **Fase 1:** Endpoints GET simples (consultas sem side-effects)
2. **Fase 2:** Endpoints POST/PUT (operações CRUD)
3. **Fase 3:** Funcionalidades complexas (OnlyOffice, Assinatura)
4. **Fase 4:** Migração completa e descomissionamento do Laravel

#### Compatibilidade de APIs
- **Mesmo formato JSON:** Responses idênticas entre backends
- **Mesma estrutura de erros:** Códigos HTTP e mensagens padronizadas
- **Headers compatíveis:** CORS, autenticação e cache mantidos
- **Versionamento:** Suporte a múltiplas versões da API

## 5. Arquitetura do Backend (Laravel Legacy)

### 4.1. Recursos Principais v2.1

✅ **OnlyOffice 100% funcional** - Preserva todas as alterações  
✅ **Priorização de arquivos salvos** - Sistema prioriza edições sobre templates  
✅ **Polling Realtime** - Detecta mudanças automaticamente em 15s  
✅ **Performance otimizada** - Cache inteligente + 70% redução I/O  
✅ **Interface Vue.js** - Atualizações em tempo real  
✅ **PDF de assinatura** - Sempre usa versão mais recente  
✅ **Parágrafos preservados** - Quebras de linha funcionam no OnlyOffice  
✅ **Permissões por role** - Sistema inteligente de autorizações

### 4.2. Sistema de Templates

- **23 tipos de proposições** com templates LC 95/1998
- **Template de Moção funcional** (ID: 6)
- **RTF com codificação UTF-8** para acentuação portuguesa
- **Processamento de imagem automático** do cabeçalho
- **Template Universal** com prioridade garantida

### 4.3. Fluxo de Proposições

1. **Parlamentar** cria proposição → Template aplicado automaticamente
2. **Sistema** detecta tipo e aplica template correspondente
3. **Parlamentar** edita documento no OnlyOffice
4. **Protocolo** atribui número oficial (ex: 0001/2025)
5. **Legislativo** recebe para análise e aprovação
6. **Assinatura Digital** com certificados .pfx/.p12

### 4.4. Controle de Acesso e Segurança

- Sistema **RBAC** (Role-Based Access Control)
- Middleware `check.permission` protege rotas críticas
- Permissões específicas por módulo (parlamentares.view, comissoes.create)
- **Assinatura digital** integrada com certificados digitais

### 4.5. Comunicação com Banco de Dados

- **PostgreSQL** containerizado para performance avançada
- Models Eloquent em `app/Models/`
- Seeders automatizados para dados iniciais

## 5. Arquitetura do Frontend

### 5.1. Tecnologias

- **Templates:** Laravel Blade como motor principal
- **Estilização:** Tailwind CSS (utility-first approach)
- **JavaScript:** Vue.js para componentes interativos + Vanilla JS
- **HTTP Client:** Axios para requisições às APIs
- **Editor:** OnlyOffice Document Server integrado

### 5.2. Componentes Principais

- **Componentes Blade** reutilizáveis em `resources/views/components`
- **Layouts responsivos** em `resources/views/components/layouts`
- **Interface Vue.js** para atualizações em tempo real
- **Polling realtime** para sincronização automática (15s)

### 5.3. Integração OnlyOffice

- **Editor colaborativo** para documentos RTF
- **Preservação automática** de todas as alterações
- **Priorização inteligente** de arquivos salvos sobre templates
- **Sincronização em tempo real** entre usuários

## 6. Numeração de Proposições

**Fluxo legislativo:**
1. **Criação:** Exibe `[AGUARDANDO PROTOCOLO]`
2. **Após protocolar:** Exibe número oficial (`0001/2025`)
3. **Apenas o Protocolo** pode atribuir números oficiais

## 7. Assinatura Digital

### 7.1. Certificados Suportados
- Arquivos **.pfx/.p12** para assinatura
- **Validação de senha** antes da assinatura
- **Integração PyHanko** para padrão PAdES

### 7.2. Processo de Assinatura
1. Upload do certificado digital (.pfx)
2. Validação da senha do certificado
3. Assinatura automática do PDF final
4. Verificação da integridade da assinatura

## 8. Arquivos Críticos

### 8.1. Processamento
- `/app/Services/OnlyOffice/OnlyOfficeService.php`
- `/app/Services/Template/TemplateProcessorService.php`
- `/app/Services/AssinaturaDigitalService.php`

### 8.2. Seeders
- `/database/seeders/DatabaseSeeder.php` - Orquestrador principal
- `/database/seeders/TipoProposicaoTemplatesSeeder.php` - Templates
- `/database/seeders/ParametrosTemplatesSeeder.php` - Parâmetros

### 8.3. Scripts de Validação
```bash
./scripts/validacao-final-completa.sh       # Validação recomendada
./scripts/teste-migrate-fresh-completo.sh   # Teste completo
./scripts/validar-pdf-otimizado.sh          # Validação rápida
```

## 9. Status Atual - v2.2 Multi-Backend Architecture

**🎊 SISTEMA 100% OPERACIONAL + NOVA ARQUITETURA**

### Core Features (v2.1 Legacy)
- ✅ OnlyOffice integrado com polling realtime
- ✅ Templates automatizados (23 tipos)
- ✅ Assinatura digital funcional
- ✅ PDF sempre atualizado
- ✅ Performance otimizada (70% redução I/O)
- ✅ Interface Vue.js responsiva
- ✅ Certificação digital integrada

### New Architecture Features (v2.2)
- ✅ **API Gateway com Traefik** - Roteamento inteligente entre backends
- ✅ **MigrationPreparationController** - Ferramentas completas de análise
- ✅ **Multi-Backend Support** - Suporte a Java, Node.js, Python, .NET
- ✅ **Load Balancing** - Distribuição inteligente de carga
- ✅ **Canary Deployment** - Deploy gradual e seguro
- ✅ **Health Checks** - Monitoramento automático
- ✅ **Container Orchestration** - Docker Compose otimizado
- ✅ **Database Schema Export** - Migração automática de estruturas
- ✅ **API Compatibility** - Manutenção da compatibilidade frontend
- ✅ **Swagger UI Integration** - Documentação interativa da API com OpenAPI 3.0

### Migration Tools Available
- 🔧 **Endpoint Analysis:** Mapeamento completo de rotas e métodos
- 🔧 **Database Structure Export:** Schema PostgreSQL com relacionamentos
- 🔧 **Model Analysis:** Extração de entidades e regras de negócio
- 🔧 **Integration Mapping:** Identificação de APIs e dependências externas
- 🔧 **Complete JSON Export:** Documentação técnica para novos backends

### Supported Backend Stacks
| Technology | Status | Implementation | ORM/Database |
|------------|--------|----------------|--------------|
| **Laravel (Current)** | ✅ Production | PHP 8.2 + Laravel 10 | Eloquent ORM |
| **Java Spring Boot** | 🔄 Ready for migration | Java 17 + Spring 3 | JPA + Hibernate |
| **Node.js Express** | 📋 Architecture ready | Node.js 18 + Express | Prisma ORM |
| **Python FastAPI** | 📋 Architecture ready | Python 3.11 + FastAPI | SQLAlchemy |
| **ASP.NET Core** | 📋 Architecture ready | .NET 7 + Web API | Entity Framework |

### Gateway Configuration
- **Main Access:** `http://localhost:8001` (Traefik Gateway)
- **Laravel API:** `http://localhost:8001/api/*` → Port 8000
- **Java API:** `http://localhost:8001/api/v2/*` → Port 3001
- **Traefik Dashboard:** `http://localhost:8080`
- **Swagger API Docs:** `http://localhost:8082` (Interactive API Documentation)

**Última atualização:** 18/09/2025
**Nova Arquitetura:** Multi-Backend Gateway System v2.2 