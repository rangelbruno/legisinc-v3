# Visão Geral do Projeto LegisInc

Este documento fornece uma análise detalhada da arquitetura, tecnologias e estrutura do sistema LegisInc v2.1 Enterprise.

## 📊 Mapa Completo do Sistema - Arquitetura e Fluxos

### Diagrama Principal - Visão Geral do Sistema

```mermaid
graph TB
    %% Frontend Layer
    FRONTEND["🌐 Frontend<br/>Vue.js + Laravel Blade"]

    %% Main Controllers
    PC["📄 ProposicaoController"]
    PLC["👤 ParlamentarController"]
    AC["⚙️ AdminControllers"]
    UC["👥 UserController"]

    %% Services
    OOS["📝 OnlyOfficeService"]
    TPS["📋 TemplateProcessorService"]
    ADS["🔏 AssinaturaDigitalService"]
    WFS["🔄 WorkflowService"]

    %% Data & Storage
    DB[("🗄️ PostgreSQL")]
    STORAGE["📁 Storage Files"]
    CACHE["⚡ Redis Cache"]

    %% External Services
    ONLY["📝 OnlyOffice Server"]
    PYHANKO["🔏 PyHanko Container"]

    %% Connections
    FRONTEND --> PC
    FRONTEND --> PLC
    FRONTEND --> AC
    FRONTEND --> UC

    PC --> OOS
    PC --> TPS
    PC --> ADS
    PC --> WFS

    OOS --> ONLY
    ADS --> PYHANKO

    PC --> DB
    PLC --> DB
    UC --> DB
    AC --> DB

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

## 2. Ambiente de Desenvolvimento (Docker)

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

## 4. Arquitetura do Backend (Laravel)

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

## 9. Status Atual - v2.1 Enterprise

**🎊 SISTEMA 100% OPERACIONAL**

- ✅ OnlyOffice integrado com polling realtime
- ✅ Templates automatizados (23 tipos)
- ✅ Assinatura digital funcional
- ✅ PDF sempre atualizado
- ✅ Performance otimizada (70% redução I/O)
- ✅ Interface Vue.js responsiva
- ✅ Certificação digital integrada

**Última atualização:** 05/09/2025 