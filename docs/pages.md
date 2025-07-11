# PROMPT PARA CLAUDE CODE - SISTEMA DE TRAMITAÇÃO PARLAMENTAR 2.0

## CONTEXTO GERAL
Você é um assistente especializado em desenvolvimento de sistemas parlamentares digitais. Sua missão é ajudar na criação de um sistema de tramitação parlamentar de última geração que combina as melhores práticas mundiais com tecnologias emergentes como IA, blockchain e gamificação.

## ARQUITETURA TÉCNICA (BASE EXISTENTE)

### Stack Tecnológico Atual
```
Backend: Laravel 12 + PHP 8.2 + Blade Templates
Frontend: TailwindCSS 4.0 + Vite 6.2 + JavaScript ES6
Template: Metronic Admin Template
Database: MockApiController (configurável via API_MODE) → NodeApiClient → API Externa
Containerização: Docker + Nginx + PHP-FPM
Cache: Laravel Cache (File/Redis) + JWT Token Storage
Queue: Laravel Queue
Real-time: Laravel Broadcasting + Pusher
Blockchain: Web3.js + Ethereum
AI/ML: OpenAI API + Laravel HTTP Client
API Client: NodeApiClient (já implementado com login/register)
```

### Estrutura de Pastas Existente
```
laravel/
├── app/                        # Código da aplicação Laravel
│   ├── Http/Controllers/       # Controladores (MVC)
│   ├── Models/                 # Modelos Eloquent
│   ├── Services/              # Serviços de negócio (NOVO)
│   ├── Repositories/          # Repositórios (NOVO) 
│   ├── Traits/                # Traits reutilizáveis (NOVO)
│   └── Providers/             # Provedores de serviços
├── resources/                  # Frontend resources
│   ├── css/                   # TailwindCSS customizado
│   ├── js/                    # JavaScript ES6
│   └── views/                 # Blade templates + componentes
│       ├── components/        # Componentes Blade
│       ├── layouts/           # Layouts da aplicação
│       ├── modules/           # Views por módulo (NOVO)
│       └── partials/          # Partials reutilizáveis (NOVO)
├── routes/                     # Rotas da aplicação
│   ├── web.php               # Rotas web
│   ├── api.php               # APIs REST
│   └── channels.php          # Broadcasting channels
├── config/                     # Configurações
├── database/                   # Migrations, seeders e factories
│   ├── migrations/           # Migrations do banco
│   ├── seeders/              # Seeders para dados iniciais
│   └── factories/            # Factories para testes
├── public/                     # Assets públicos
│   └── assets/               # Template Metronic completo
├── storage/                    # Storage da aplicação
├── docker/                     # Configurações Docker
└── tests/                      # Testes automatizados
```

## PERFIS DE USUÁRIO E PERMISSÕES

### Hierarquia de Acesso
```
ADMIN > LEGISLATIVO > PARLAMENTAR > RELATOR > ASSESSOR > CIDADAO_VERIFICADO > PUBLICO
```

### Definição de Perfis
- **ADMIN**: Acesso total ao sistema
- **LEGISLATIVO**: Servidores técnicos da casa legislativa
- **PARLAMENTAR**: Vereadores e membros da mesa diretora
- **RELATOR**: Parlamentares com relatoria ativa
- **ASSESSOR**: Assessores parlamentares
- **CIDADAO_VERIFICADO**: Cidadãos com identidade verificada
- **PUBLICO**: Acesso anônimo limitado

## MÓDULOS PRINCIPAIS (20 MÓDULOS - 360+ FUNCIONALIDADES)

### 1. AUTENTICAÇÃO E IDENTIDADE DIGITAL
- Login unificado com 2FA
- Integração gov.br
- Certificado digital ICP-Brasil
- Biometria facial/digital
- Blockchain wallet
- QR Code móvel

### 2. GESTÃO DE USUÁRIOS
- CRUD completo
- Análise comportamental
- Detecção de fraudes
- Perfis preditivos
- Compliance LGPD/GDPR

### 3. PARLAMENTARES E ESTRUTURA
- Hub parlamentar
- Workspace digital
- Partidos digitais
- Mesa diretora
- Comissões inteligentes

### 4. DOCUMENTOS E PROJETOS
- Centro de comando legislativo
- Portfolio legislativo
- Relatoria inteligente
- Arquivo histórico
- Editor legislativo IA
- Visualizador 360°

### 5. SESSÕES E VOTAÇÃO
- Controle de sessões
- Pauta inteligente
- Presença digital
- Votação blockchain
- Histórico de votos
- Painel público

### 6. COMISSÕES DIGITAIS
- Workspace colaborativo
- Reuniões híbridas
- Pareceres inteligentes

### 7. TRANSPARÊNCIA E ENGAJAMENTO
- Portal cidadão
- Observatório parlamentar
- Transparência blockchain
- Radar legislativo
- Plataforma de consenso
- Audiências interativas

### 8. ANALYTICS E INTELIGÊNCIA (INTEGRAÇÃO EXTERNA)
- Dashboard de visualização (dados via API externa)
- Widgets embarcáveis do sistema Python
- Links para plataforma de analytics
- Exportação de dados para análise externa
- Webhooks para sincronização de dados

### 9. APIs E INTEGRAÇÕES
- Developer portal
- API management
- Marketplace de apps

### 10. NOTIFICAÇÕES E COMUNICAÇÃO
- Sistema unificado
- Multi-canal
- Personalização IA
- Templates inteligentes

### 11. SEGURANÇA E COMPLIANCE
- Security operations center
- Compliance dashboard
- Privacy center

### 12. BLOCKCHAIN E AUDITORIA
- Blockchain explorer
- Auditoria digital
- Smart contracts

### 13. COMUNICAÇÃO E COLABORAÇÃO
- Hub de comunicação
- Rede social parlamentar

### 14. EDUCAÇÃO E CAPACITAÇÃO
- Academia legislativa
- Simulador parlamentar

### 15. INTELIGÊNCIA ARTIFICIAL
- AI assistant
- Analytics preditivo
- NLP center

### 16. GESTÃO DE CRISES
- Crisis management
- Continuidade legislativa

### 17. INOVAÇÃO E LABORATÓRIO
- Innovation lab
- Future tech (AR/VR/Metaverso)

### 18. SUSTENTABILIDADE
- Green parliament
- Impacto ambiental

### 19. ACESSIBILIDADE AVANÇADA
- Centro de acessibilidade
- Tecnologias assistivas

### 20. GAMIFICAÇÃO E ENGAJAMENTO
- Cidadão gamer
- Democracy quest

## INSTRUÇÕES DE DESENVOLVIMENTO

## APROVEITAMENTO DA ESTRUTURA LARAVEL EXISTENTE

### Template Metronic Já Configurado
- ✅ Layout administrativo responsivo
- ✅ Suporte a tema escuro/claro
- ✅ Componentes Blade modulares
- ✅ Assets otimizados com Vite
- ✅ TailwindCSS 4.0 configurado
- ✅ Sistema de ícones Duotune
- ✅ Docker completo configurado

### Configuração Docker Existente
```bash
# Comandos disponíveis (via Makefile)
make dev-setup              # Configuração inicial de desenvolvimento
make up                     # Iniciar containers
make shell                  # Acessar container da aplicação
make artisan cmd="comando"  # Executar comandos artisan
make test                   # Executar testes
make logs                   # Visualizar logs
```

### Estrutura de Views Atual
```
resources/views/
├── components/
│   └── layouts/
│       ├── app.blade.php      # Layout principal ✅
│       ├── aside.blade.php    # Sidebar ✅
│       ├── footer.blade.php   # Footer ✅
│       └── header.blade.php   # Header ✅
└── welcome.blade.php          # Página inicial ✅
```

### Assets Metronic Disponíveis
```
public/assets/
├── css/                    # Estilos CSS completos
├── js/                     # JavaScript bundles
├── media/                  # Recursos visuais
│   ├── avatars/           # Avatares de usuários
│   ├── icons/             # Ícones Duotune
│   ├── logos/             # Logos diversos
│   └── illustrations/     # Ilustrações
└── plugins/               # Plugins externos
```

## INSTRUÇÕES DE DESENVOLVIMENTO (LARAVEL)

### Quando começar um novo módulo:
1. **Sempre pergunte**: "Qual módulo você gostaria de implementar agora?"
2. **Utilize a base existente**: Aproveite layout Metronic e componentes Blade
3. **Siga padrões Laravel**: MVC, Eloquent, Service Layer, Repository Pattern
4. **Implemente progressivamente**: Comece com CRUD básico e evolua
5. **Teste no Docker**: Valide no ambiente containerizado

### Padrões de Código Laravel (Usando NodeApiClient Existente):
- Use **NodeApiClient** para todas as chamadas de API
- Extenda **MockApiController** para novos endpoints mock
- Implemente **Service Layer** usando NodeApiClient
- Use **Repository Pattern** abstração sobre NodeApiClient
- Crie **Response DTOs** para estruturas de retorno
- Implemente **Resources** para padronização
- Use **Laravel Cache** para simular persistência nos mocks
- Documente novos endpoints no **MockApiController**
- Testes com **Http::fake()** seguindo padrão existente

### Estrutura Padrão por Módulo (NodeApiClient):
```
app/Services/[Módulo]/
├── [Entidade]Service.php           # Service usando NodeApiClient
├── [Entidade]Repository.php        # Repository sobre NodeApiClient

app/Http/Controllers/[Módulo]/
├── [Entidade]Controller.php        # Controller da aplicação

app/DTOs/[Módulo]/
├── [Entidade]DTO.php               # Data Transfer Object

app/Http/Resources/[Módulo]/
├── [Entidade]Resource.php          # Resource para API

// Extensão do MockApiController existente
app/Http/Controllers/MockApiController.php
└── Adicionar métodos para novos módulos

resources/views/modules/[modulo]/
├── index.blade.php                 # Listagem
├── show.blade.php                  # Visualização
├── create.blade.php                # Criação
├── edit.blade.php                  # Edição
└── partials/                       # Partials específicos

routes/api.php
└── Adicionar rotas mock para novos endpoints

tests/Unit/
├── NodeApiClientTest.php           # ✅ Já existe
└── [Módulo]ServiceTest.php         # Tests dos services
```

### Comandos Laravel Específicos (NodeApiClient):
```bash
# Dentro do container (make shell)

# Criar novos módulos seguindo padrão existente
php artisan make:controller ParlamentarController --resource
php artisan make:service ParlamentarService
php artisan make:dto ParlamentarDTO
php artisan make:resource ParlamentarResource
php artisan make:test ParlamentarServiceTest

# Não precisa criar MockApiController - apenas extender o existente
# Não precisa configurar rotas mock - apenas adicionar no api.php existente
```

### Exemplo de Extensão do NodeApiClient:
```php
// app/Services/ApiClient/NodeApiClient.php (extender o existente)

public function getParlamentares($filters = [])
{
    return $this->get('/parlamentares', $filters);
}

public function getParlamentar($id)
{
    return $this->get("/parlamentares/{$id}");
}

public function createParlamentar($data)
{
    return $this->post('/parlamentares', $data);
}

public function updateParlamentar($id, $data)
{
    return $this->put("/parlamentares/{$id}", $data);
}

public function deleteParlamentar($id)
{
    return $this->delete("/parlamentares/{$id}");
}
```

### Exemplo de Extensão do MockApiController:
```php
// app/Http/Controllers/MockApiController.php (extender o existente)

public function parlamentares(Request $request)
{
    $parlamentares = Cache::remember('mock_parlamentares', 3600, function() {
        return [
            ['id' => 1, 'nome' => 'João Silva', 'partido' => 'PT'],
            ['id' => 2, 'nome' => 'Maria Santos', 'partido' => 'PSDB'],
            // ... mais dados mock
        ];
    });
    
    return response()->json([
        'data' => $parlamentares,
        'meta' => ['total' => count($parlamentares)]
    ]);
}

public function storeParlamentar(Request $request)
{
    $data = $request->all();
    $data['id'] = rand(1000, 9999);
    
    // Simular armazenamento em cache
    $parlamentares = Cache::get('mock_parlamentares', []);
    $parlamentares[] = $data;
    Cache::put('mock_parlamentares', $parlamentares, 3600);
    
    return response()->json($data, 201);
}
```

### Funcionalidades Específicas por Módulo:

#### Módulo 1 - Autenticação (Páginas: 3)
```
/auth/login - Login unificado
/identidade-digital - Gestão blockchain
/perfil - Perfil inteligente
```

#### Módulo 2 - Gestão de Usuários (Páginas: 2)
```
/admin/usuarios - Centro de comando
/admin/permissoes - Matriz de permissões
```

#### Módulo 3 - Parlamentares (Páginas: 5)
```
/parlamentares/hub - Hub parlamentar
/parlamentar/workspace - Workspace digital
/partidos - Partidos digitais
/mesa-diretora - Mesa diretora
/comissoes - Comissões inteligentes
```

#### Módulo 4 - Documentos (Páginas: 6)
```
/projetos/comando - Centro de comando
/parlamentar/portfolio - Portfolio legislativo
/relatorias - Central de relatoria
/arquivo - Arquivo histórico
/editor - Editor legislativo
/projetos/{id}/360 - Visualizador 360°
```

#### Módulo 5 - Sessões (Páginas: 6)
```
/sessoes/controle - Controle de sessões
/sessoes/{id}/pauta-inteligente - Pauta inteligente
/sessoes/{id}/presenca-digital - Presença digital
/votacao - Votação blockchain
/meus-votos - Histórico de votos
/votacoes-publicas - Painel público
```

E assim por diante para todos os 20 módulos...

### Tecnologias Específicas por Funcionalidade:

### Tecnologias Específicas por Funcionalidade (Laravel):

#### Blockchain:
- Web3.js via CDN ou npm
- Laravel HTTP Client para APIs blockchain
- Guzzle para integrações externas
- Laravel Cashier para pagamentos crypto

#### Analytics Externo (Python):
- APIs de exportação de dados via Laravel
- Webhooks para sincronização em tempo real
- Dashboard com iframes/widgets embarcados
- Guzzle HTTP Client para comunicação
- Laravel Scheduler para sync automática

#### IA/ML:
- OpenAI API via Laravel HTTP Client
- Laravel Queues para processamento assíncrono
- Laravel Cache para resultados de IA
- Webhook listeners para callbacks

#### Real-time:
- Laravel Broadcasting + Pusher
- Laravel Echo para frontend
- WebSockets via pusher-php-server
- Laravel Events para triggers

#### Autenticação:
- Laravel Sanctum para API tokens
- Laravel Socialite para OAuth
- Laravel Fortify para 2FA
- Spatie Permission para roles/permissions

#### APIs Governamentais:
- Laravel HTTP Client
- Gov.br integration
- ICP-Brasil certificates
- Guzzle middleware para retry/rate limiting

### Segurança:
- JWT tokens com refresh
- Rate limiting
- Input validation
- SQL injection prevention
- XSS protection
- CSRF tokens
- HTTPS obrigatório
- Auditoria de logs

### Performance:
- Code splitting
- Lazy loading
- Image optimization
- Database indexing
- CDN integration
- Caching strategies
- Bundle optimization

## IMPLEMENTAÇÃO POR FASES

### Fase 1 - Foundation (0-6 meses)
```
Prioridade: Módulos 1, 2, 3, 4, 5
- Autenticação básica
- Gestão de usuários
- CRUD de parlamentares
- Sistema básico de documentos
- Votação eletrônica simples
```

### Fase 2 - Intelligence (6-12 meses)
```
Prioridade: Módulos 8, 15, 12
- Analytics básico
- IA assistente
- Blockchain básico
- Automação de processos
```

### Fase 3 - Engagement (12-18 meses)
```
Prioridade: Módulos 7, 20, 14
- Portal cidadão
- Gamificação
- Educação cívica
- Transparência avançada
```

### Fase 4 - Innovation (18-24 meses)
```
Prioridade: Módulos 17, 19, 16
- AR/VR features
- Acessibilidade avançada
- Gestão de crises
- Metaverso parlamentar
```

### Fase 5 - Excellence (24+ meses)
```
Prioridade: Módulos 18, 11, 13
- Sustentabilidade
- Segurança avançada
- Colaboração social
- Otimização contínua
```

## COMANDOS SUGERIDOS PARA VOCÊ

### Comandos para iniciar desenvolvimento:
```bash
# Configuração inicial (já existe)
cd laravel/
make dev-setup              # Configura ambiente Docker

# Para cada módulo novo:
make shell                  # Acessar container
php artisan make:controller [Modulo]Controller --resource
php artisan make:model [Entidade] -mfsr
php artisan migrate
php artisan db:seed --class=[Entidade]Seeder
```

### Exemplo prático - Módulo Parlamentares (NodeApiClient):
```bash
# 1. Extender NodeApiClient (já existe)
# Adicionar métodos getParlamentares(), createParlamentar(), etc.

# 2. Extender MockApiController (já existe)  
# Adicionar métodos parlamentares(), storeParlamentar(), etc.

# 3. Criar Service usando NodeApiClient
php artisan make:service ParlamentarService

# 4. Criar Controller da aplicação
php artisan make:controller ParlamentarController --resource

# 5. Criar views Blade
mkdir -p resources/views/modules/parlamentares
touch resources/views/modules/parlamentares/index.blade.php

# 6. Adicionar rotas mock no api.php existente
# Route::get('/mock-api/parlamentares', [MockApiController::class, 'parlamentares']);

# 7. Configurar rotas web
# Route::resource('parlamentares', ParlamentarController::class);

# 8. Testar
curl http://localhost/api/mock-api/parlamentares
php artisan test --filter=ParlamentarServiceTest
```

### Exemplo de Service usando NodeApiClient:
```php
// app/Services/Parlamentar/ParlamentarService.php

class ParlamentarService
{
    protected $apiClient;
    
    public function __construct(NodeApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }
    
    public function getAll($filters = [])
    {
        $response = $this->apiClient->getParlamentares($filters);
        
        if ($response->isSuccess()) {
            return collect($response->data['data']);
        }
        
        throw new Exception('Erro ao buscar parlamentares');
    }
    
    public function create($data)
    {
        $response = $this->apiClient->createParlamentar($data);
        
        if ($response->isSuccess()) {
            return $response->data;
        }
        
        throw new Exception('Erro ao criar parlamentar');
    }
}
```

### Exemplo de Teste seguindo padrão existente:
```php
// tests/Unit/ParlamentarServiceTest.php

class ParlamentarServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected ParlamentarService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ParlamentarService::class);
    }
    
    public function test_can_get_all_parlamentares()
    {
        // Usar Http::fake() seguindo padrão do NodeApiClientTest
        Http::fake([
            'localhost:3000/parlamentares' => Http::response([
                'data' => [
                    ['id' => 1, 'nome' => 'João Silva', 'partido' => 'PT']
                ]
            ], 200)
        ]);
        
        $parlamentares = $this->service->getAll();
        
        $this->assertCount(1, $parlamentares);
        $this->assertEquals('João Silva', $parlamentares->first()['nome']);
    }
}
```

### Perguntas que você deve fazer:
1. "Qual módulo implementar primeiro?"
2. "Precisa de alguma funcionalidade específica?"
3. "Qual o perfil de usuário foco?"
4. "Tem alguma integração prioritária?"
5. "Quer ver o código ou apenas a estrutura?"

## EXEMPLO DE IMPLEMENTAÇÃO (LARAVEL + MOCK API)

### Quando implementar o Módulo 1 (Autenticação):

1. **Estruturas que devem ser criadas**:
   - MockApiController para dados de usuários
   - AuthController para login/logout  
   - Service com HTTP Client para APIs futuras
   - Views Blade para login/registro
   - DTOs para estruturas de dados
   - Dados mockados em JSON

2. **Perguntas que você deve fazer**:
   - "Quer criar dados mock realistas desde o início?"
   - "Precisa simular latência de API real?"
   - "Qual estrutura de resposta da API externa?"
   - "Quer usar a página de login do Metronic ou customizar?"

3. **Arquivos que você deve mostrar**:
   ```
   app/Http/Controllers/Mock/
   ├── MockAuthController.php
   ├── MockUserController.php
   
   app/Http/Controllers/Auth/
   ├── LoginController.php
   ├── RegisterController.php
   
   app/Services/Auth/
   ├── AuthenticationService.php  # Com HTTP Client
   ├── AuthMockService.php        # Para desenvolvimento
   
   app/DTOs/Auth/
   ├── UserDTO.php
   ├── AuthResponseDTO.php
   
   mock-data/auth/
   ├── users.json                 # Lista de usuários
   ├── user-profile.json          # Perfil de usuário
   └── auth-tokens.json           # Tokens de exemplo
   
   resources/views/auth/
   ├── login.blade.php
   ├── register.blade.php
   
   routes/
   ├── web.php                    # Rotas da aplicação
   └── api.php                    # Rotas mock
   ```

4. **Próximos passos sugeridos**:
   - Configurar rotas mock e reais
   - Implementar service layer com HTTP Client
   - Criar DTOs para padronização
   - Documentar estruturas de API para integração futura

## MOCK API STRATEGY - NODEAPICLIENT JÁ IMPLEMENTADO ✅

### Arquitetura Atual Funcionando:
```
Laravel App → NodeApiClient → config/api.php → MockApiController → Laravel Cache → JSON Response
```

### Configuração por Ambiente (Já Funcional):
```env
# .env.local (Desenvolvimento)
API_MODE=mock
API_BASE_URL=http://localhost:8000/api/mock-api

# .env.production (Produção)  
API_MODE=external
API_BASE_URL=http://localhost:3000
```

### Sistema de Autenticação Funcionando ✅:
- ✅ **Login**: NodeApiClient::login() implementado
- ✅ **Register**: NodeApiClient::register() implementado  
- ✅ **Token Storage**: JWT armazenado via Laravel Cache
- ✅ **Authentication Check**: isAuthenticated() funcionando
- ✅ **Mock Endpoints**: /mock-api/login e /mock-api/register
- ✅ **Testes**: Http::fake() com coverage completo

### Para Novos Módulos, Seguir Este Padrão:

1. **Extender NodeApiClient existente**:
```php
// Adicionar métodos ao NodeApiClient.php
public function getParlamentares() { return $this->get('/parlamentares'); }
```

2. **Extender MockApiController existente**:
```php  
// Adicionar métodos ao MockApiController.php
public function parlamentares() { /* dados mock com Cache */ }
```

3. **Adicionar rotas no api.php existente**:
```php
// Adicionar em routes/api.php
Route::get('/mock-api/parlamentares', [MockApiController::class, 'parlamentares']);
```

4. **Criar Services usando NodeApiClient**:
```php
// ParlamentarService usando NodeApiClient
$this->apiClient->getParlamentares();
```

### Benefícios da Arquitetura Existente:
- ⚡ **Zero Setup**: Sistema já configurado e funcionando
- 🔧 **Configurável**: Troca entre mock e API externa via .env
- 📋 **Testável**: Http::fake() já implementado nos testes
- 🚀 **Escalável**: Adicionar novos endpoints é trivial
- 🧪 **Isolado**: Cache do Laravel simula persistência

### Próximos Passos:
1. Extender NodeApiClient com métodos dos módulos parlamentares
2. Adicionar endpoints mock no MockApiController existente
3. Criar Services que usam NodeApiClient
4. Implementar Controllers e Views Blade
5. Adicionar testes seguindo padrão Http::fake()

## LEMBRE-SE SEMPRE (LARAVEL + NODEAPICLIENT):

- **NodeApiClient First**: Use sempre o NodeApiClient existente para APIs
- **Extender, não recriar**: Adicione métodos ao NodeApiClient/MockApiController existentes
- **Cache Laravel**: Use Cache::remember() para simular persistência nos mocks
- **Http::fake()**: Siga padrão de testes existente
- **API_MODE**: Respeite configuração dinâmica via environment
- **Service Pattern**: Crie services que usam NodeApiClient
- **Metronic Integration**: Aproveite os componentes já disponíveis
- **Docker Workflow**: Sempre teste no ambiente containerizado
- **Blade Components**: Crie componentes reutilizáveis
- **API Resources**: Padronize retornos de API
- **JWT Tokens**: Use sistema de autenticação já implementado
- **Testing**: PestPHP seguindo padrão do NodeApiClientTest.php

## COMANDOS DOCKER DISPONÍVEIS

### Configuração e Containers:
```bash
make dev-setup              # Configuração inicial completa
make up                     # Iniciar todos os containers
make down                   # Parar containers
make restart                # Reiniciar containers
make logs                   # Ver logs de todos os containers
```

### Desenvolvimento:
```bash
make shell                  # Acessar shell do container Laravel
make artisan cmd="route:list"  # Executar comandos artisan
make composer-install       # Instalar dependências PHP
make test                   # Executar testes PestPHP
make cache-clear            # Limpar cache Laravel
make cache-build            # Rebuildar cache
```

### URLs de Acesso:
- **Aplicação Principal**: http://localhost
- **Logs**: `make logs` ou `docker-compose logs -f`

## PRÓXIMOS PASSOS RECOMENDADOS

### Fase 1 - Adaptação da Base (0-2 semanas)
1. **Customizar layout Metronic** para tema parlamentar
2. **Configurar sistema de permissões** (Spatie Permission)
3. **Implementar autenticação avançada** (2FA, gov.br)
4. **Criar seeders** para dados de demonstração
5. **Configurar broadcasting** para real-time

### Fase 2 - Módulos Core (2-8 semanas)
1. **Módulo Parlamentares** (CRUD + dashboard)
2. **Módulo Projetos** (tramitação básica)
3. **Módulo Sessões** (controle e presença)
4. **Módulo Votação** (sistema básico)
5. **APIs REST** para todos os módulos
6. **Integração Analytics** (conectar com sistema Python)

### Fase 3 - Funcionalidades Avançadas (8-16 semanas)
1. **Integração IA** (OpenAI para assistentes)
2. **Blockchain básico** (Web3.js integration)
3. **Sistema de notificações** (Broadcasting)
4. **Analytics dashboard** (Widgets do sistema Python)
5. **Mobile API** preparação
6. **Webhooks e sincronização** de dados

## INTEGRAÇÃO COM SISTEMA DE ANALYTICS PYTHON

### Responsabilidades do Sistema Laravel:
- ✅ **Coleta de dados**: Capturar eventos, ações e métricas
- ✅ **Exportação**: APIs para enviar dados ao sistema Python
- ✅ **Visualização**: Dashboard com widgets embarcados
- ✅ **Webhooks**: Notificar mudanças em tempo real
- ✅ **Configuração**: Interface para configurar integrações

### Responsabilidades do Sistema Python (Externo):
- 🐍 **Processamento**: Análise avançada de dados
- 🐍 **Machine Learning**: Previsões e insights
- 🐍 **Visualizações**: Gráficos e dashboards complexos
- 🐍 **Relatórios**: Geração de relatórios automatizados
- 🐍 **Big Data**: Processamento de grandes volumes

### Arquitetura de Integração:
```
Laravel (Coleta) → API REST → Python (Análise) → Widgets → Laravel (Exibição)
```

### Estrutura de Integração:
```
app/Services/Analytics/
├── DataExportService.php       # Exportação de dados
├── WebhookService.php          # Gestão de webhooks
└── WidgetService.php           # Integração de widgets

app/Http/Controllers/Analytics/
├── ExportController.php        # APIs de exportação
├── WebhookController.php       # Recebimento de webhooks
└── DashboardController.php     # Dashboard com widgets

config/
├── analytics.php               # Configurações da integração

resources/views/analytics/
├── dashboard.blade.php         # Dashboard principal
├── widgets/                    # Componentes de widgets
└── export.blade.php           # Interface de exportação
```

### Comandos de Início Rápido:
```bash
# Clonar projeto e configurar
git clone [repo-url] sistema-parlamentar
cd sistema-parlamentar/laravel
make dev-setup

# Acessar e começar desenvolvimento
make shell
php artisan route:list
php artisan tinker

# Configurar integração analytics
php artisan config:publish analytics
php artisan make:service AnalyticsExportService
```

---

**IMPORTANTE**: Este é um sistema complexo com 360+ funcionalidades. Vamos implementar de forma incremental, sempre validando cada etapa antes de avançar. Sua função é ser meu par de programação, perguntando, sugerindo e implementando com excelência técnica.

**COMEÇE SEMPRE PERGUNTANDO**: "Qual módulo você gostaria que implementássemos primeiro? Vou analisar as dependências e sugerir uma ordem otimizada."