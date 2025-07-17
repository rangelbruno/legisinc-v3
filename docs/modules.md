# SISTEMA DE TRAMITAÇÃO PARLAMENTAR 2.0 - CLAUDE CODE ASSISTANT

## OVERVIEW
You are a specialized Laravel development assistant for a comprehensive Parliamentary Processing System. Your mission is to help build a next-generation parliamentary system combining best global practices with emerging technologies like AI, blockchain, and gamification.

## CURRENT TECH STACK & ARCHITECTURE

### Technology Stack
```
Backend: Laravel 12 + PHP 8.2 + Blade Templates
Frontend: TailwindCSS 4.0 + Vite 6.2 + JavaScript ES6
Admin Template: Metronic Admin Template (already configured)
Database: MockApiController (configurable via API_MODE) → NodeApiClient → External API
Containerization: Docker + Nginx + PHP-FPM
Cache: Laravel Cache (File/Redis) + JWT Token Storage
Queue: Laravel Queue
Real-time: Laravel Broadcasting + Pusher
Blockchain: Web3.js + Ethereum
AI/ML: OpenAI API + Laravel HTTP Client
API Client: NodeApiClient (already implemented with login/register)
```

### Project Structure
```
laravel/
├── app/
│   ├── Http/Controllers/       # MVC Controllers
│   ├── Models/                 # Eloquent Models
│   ├── Services/              # Business Logic Services
│   ├── Repositories/          # Data Access Layer
│   ├── DTOs/                  # Data Transfer Objects
│   └── Traits/                # Reusable Traits
├── resources/
│   ├── css/                   # TailwindCSS
│   ├── js/                    # JavaScript ES6
│   └── views/                 # Blade templates + components
│       ├── components/        # Blade Components
│       ├── layouts/           # App Layouts
│       ├── modules/           # Module Views
│       └── partials/          # Reusable Partials
├── routes/
│   ├── web.php               # Web Routes
│   ├── api.php               # REST APIs
│   └── channels.php          # Broadcasting
├── config/                   # Configuration files
├── database/                 # Migrations, seeders, factories
├── public/
│   └── assets/               # Complete Metronic Template
├── docker/                   # Docker configurations
└── tests/                    # Automated tests
```

## USER PROFILES & PERMISSIONS

### Access Hierarchy
```
ADMIN > LEGISLATIVO > PARLAMENTAR > RELATOR > ASSESSOR > CIDADAO_VERIFICADO > PUBLICO
```

### Profile Definitions
- **ADMIN**: Full system access
- **LEGISLATIVO**: Legislative house technical staff
- **PARLAMENTAR**: Council members and board members
- **RELATOR**: Parliamentarians with active reportership
- **ASSESSOR**: Parliamentary advisors
- **CIDADAO_VERIFICADO**: Citizens with verified identity
- **PUBLICO**: Limited anonymous access

## 20 SYSTEM MODULES (360+ Features)

### 1. AUTHENTICATION & DIGITAL IDENTITY
**Pages**: 3 (`/auth/login`, `/identidade-digital`, `/perfil`)
- Unified login with 2FA
- gov.br integration
- ICP-Brasil digital certificates
- Facial/digital biometrics
- Blockchain wallet
- Mobile QR Code

### 2. USER MANAGEMENT
**Pages**: 2 (`/admin/usuarios`, `/admin/permissoes`)
- Complete CRUD
- Behavioral analysis
- Fraud detection
- Predictive profiles
- LGPD/GDPR compliance

### 3. PARLIAMENTARIANS & STRUCTURE
**Pages**: 5 (`/parlamentares/hub`, `/parlamentar/workspace`, `/partidos`, `/mesa-diretora`, `/comissoes`)
- Parliamentary hub
- Digital workspace
- Digital parties
- Board of directors
- Intelligent committees

### 4. DOCUMENTS & PROJECTS
**Pages**: 6 (`/projetos/comando`, `/parlamentar/portfolio`, `/relatorias`, `/arquivo`, `/editor`, `/projetos/{id}/360`)
- Legislative command center
- Legislative portfolio
- Intelligent reportership
- Historical archive
- AI legislative editor
- 360° visualizer

### 5. SESSIONS & VOTING
**Pages**: 6 (`/sessoes/controle`, `/sessoes/{id}/pauta-inteligente`, `/sessoes/{id}/presenca-digital`, `/votacao`, `/meus-votos`, `/votacoes-publicas`)
- Session control
- Intelligent agenda
- Digital presence
- Blockchain voting
- Vote history
- Public panel

### 6. DIGITAL COMMITTEES
**Pages**: 3
- Collaborative workspace
- Hybrid meetings
- Intelligent reports

### 7. TRANSPARENCY & ENGAGEMENT
**Pages**: 6
- Citizen portal
- Parliamentary observatory
- Blockchain transparency
- Legislative radar
- Consensus platform
- Interactive hearings

### 8. ANALYTICS & INTELLIGENCE (EXTERNAL INTEGRATION)
**Pages**: 4
- Visualization dashboard (data via external API)
- Embeddable widgets from Python system
- Links to analytics platform
- Data export for external analysis
- Webhooks for data synchronization

### 9. APIS & INTEGRATIONS
**Pages**: 3
- Developer portal
- API management
- App marketplace

### 10. NOTIFICATIONS & COMMUNICATION
**Pages**: 4
- Unified system
- Multi-channel
- AI personalization
- Intelligent templates

### 11. SECURITY & COMPLIANCE
**Pages**: 3
- Security operations center
- Compliance dashboard
- Privacy center

### 12. BLOCKCHAIN & AUDIT
**Pages**: 3
- Blockchain explorer
- Digital audit
- Smart contracts

### 13. COMMUNICATION & COLLABORATION
**Pages**: 2
- Communication hub
- Parliamentary social network

### 14. EDUCATION & TRAINING
**Pages**: 2
- Legislative academy
- Parliamentary simulator

### 15. ARTIFICIAL INTELLIGENCE
**Pages**: 3
- AI assistant
- Predictive analytics
- NLP center

### 16. CRISIS MANAGEMENT
**Pages**: 2
- Crisis management
- Legislative continuity

### 17. INNOVATION & LABORATORY
**Pages**: 2
- Innovation lab
- Future tech (AR/VR/Metaverse)

### 18. SUSTAINABILITY
**Pages**: 2
- Green parliament
- Environmental impact

### 19. ADVANCED ACCESSIBILITY
**Pages**: 2
- Accessibility center
- Assistive technologies

### 20. GAMIFICATION & ENGAGEMENT
**Pages**: 2
- Citizen gamer
- Democracy quest

## DEVELOPMENT GUIDELINES

### Current Working Architecture (NodeApiClient)
The project already has a working architecture with:
- ✅ **NodeApiClient**: HTTP client for API communication
- ✅ **MockApiController**: Mock endpoints for development
- ✅ **Authentication**: Login/register implemented with JWT
- ✅ **Environment Configuration**: Switch between mock/external API via API_MODE
- ✅ **Testing**: Http::fake() patterns established
- ✅ **Docker**: Complete development environment

### Development Pattern for New Modules

#### 1. Extend NodeApiClient (app/Services/ApiClient/NodeApiClient.php)
```php
// Add methods for new entities
public function getParlamentares($filters = [])
{
    return $this->get('/parlamentares', $filters);
}

public function createParlamentar($data)
{
    return $this->post('/parlamentares', $data);
}
```

#### 2. Extend MockApiController (app/Http/Controllers/MockApiController.php)
```php
// Add mock endpoints
public function parlamentares(Request $request)
{
    $parlamentares = Cache::remember('mock_parlamentares', 3600, function() {
        return [
            ['id' => 1, 'nome' => 'João Silva', 'partido' => 'PT'],
            ['id' => 2, 'nome' => 'Maria Santos', 'partido' => 'PSDB'],
        ];
    });
    
    return response()->json([
        'data' => $parlamentares,
        'meta' => ['total' => count($parlamentares)]
    ]);
}
```

#### 3. Create Service Layer
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
}
```

#### 4. Create Controllers
```php
// app/Http/Controllers/ParlamentarController.php
class ParlamentarController extends Controller
{
    protected $parlamentarService;
    
    public function __construct(ParlamentarService $parlamentarService)
    {
        $this->parlamentarService = $parlamentarService;
    }
    
    public function index()
    {
        $parlamentares = $this->parlamentarService->getAll();
        return view('modules.parlamentares.index', compact('parlamentares'));
    }
}
```

#### 5. Create Views Using Metronic Components
```php
// resources/views/modules/parlamentares/index.blade.php
<x-layouts.app>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Parlamentares</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Partido</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parlamentares as $parlamentar)
                        <tr>
                            <td>{{ $parlamentar['nome'] }}</td>
                            <td>{{ $parlamentar['partido'] }}</td>
                            <td>
                                <a href="{{ route('parlamentares.show', $parlamentar['id']) }}" class="btn btn-sm btn-primary">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
```

#### 6. Add Routes
```php
// routes/web.php
Route::resource('parlamentares', ParlamentarController::class);

// routes/api.php (for mock endpoints)
Route::get('/mock-api/parlamentares', [MockApiController::class, 'parlamentares']);
```

#### 7. Create Tests
```php
// tests/Unit/ParlamentarServiceTest.php
class ParlamentarServiceTest extends TestCase
{
    protected ParlamentarService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ParlamentarService::class);
    }
    
    public function test_can_get_all_parlamentares()
    {
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

## DOCKER COMMANDS

### Setup and Containers
```bash
make dev-setup              # Complete initial setup
make up                     # Start all containers
make down                   # Stop containers
make restart                # Restart containers
make logs                   # View all container logs
```

### Development
```bash
make shell                  # Access Laravel container shell
make artisan cmd="route:list"  # Execute artisan commands
make composer-install       # Install PHP dependencies
make test                   # Run PestPHP tests
make cache-clear            # Clear Laravel cache
```

### Access URLs
- **Main Application**: http://localhost
- **Logs**: `make logs` or `docker-compose logs -f`

## IMPLEMENTATION PHASES

### Phase 1 - Foundation (0-6 months)
**Priority**: Modules 1, 2, 3, 4, 5
- Basic authentication
- User management
- Parliamentarians CRUD
- Basic document system
- Simple electronic voting

### Phase 2 - Intelligence (6-12 months)
**Priority**: Modules 8, 15, 12
- Basic analytics
- AI assistant
- Basic blockchain
- Process automation

### Phase 3 - Engagement (12-18 meses)
**Priority**: Modules 7, 20, 14
- Citizen portal
- Gamification
- Civic education
- Advanced transparency

### Phase 4 - Innovation (18-24 months)
**Priority**: Modules 17, 19, 16
- AR/VR features
- Advanced accessibility
- Crisis management
- Parliamentary metaverse

### Phase 5 - Excellence (24+ months)
**Priority**: Modules 18, 11, 13
- Sustainability
- Advanced security
- Social collaboration
- Continuous optimization

## DEVELOPMENT COMMANDS

### For Each New Module:
```bash
# Access container
make shell

# Create service
php artisan make:service [Module]Service

# Create controller
php artisan make:controller [Module]Controller --resource

# Create views directory
mkdir -p resources/views/modules/[module]

# Create test
php artisan make:test [Module]ServiceTest
```

### Example: Parliamentary Module
```bash
# 1. Extend NodeApiClient (add methods)
# 2. Extend MockApiController (add endpoints)
# 3. Create Service
php artisan make:service ParlamentarService

# 4. Create Controller
php artisan make:controller ParlamentarController --resource

# 5. Create views
mkdir -p resources/views/modules/parlamentares
touch resources/views/modules/parlamentares/index.blade.php

# 6. Add routes to web.php and api.php
# 7. Test
curl http://localhost/api/mock-api/parlamentares
php artisan test --filter=ParlamentarServiceTest
```

## EXTERNAL ANALYTICS INTEGRATION

### Laravel Responsibilities
- ✅ **Data Collection**: Capture events, actions and metrics
- ✅ **Export**: APIs to send data to Python system
- ✅ **Visualization**: Dashboard with embedded widgets
- ✅ **Webhooks**: Notify changes in real-time
- ✅ **Configuration**: Interface to configure integrations

### Python System Responsibilities (External)
- 🐍 **Processing**: Advanced data analysis
- 🐍 **Machine Learning**: Predictions and insights
- 🐍 **Visualizations**: Complex charts and dashboards
- 🐍 **Reports**: Automated report generation
- 🐍 **Big Data**: Large volume processing

### Integration Architecture
```
Laravel (Collection) → REST API → Python (Analysis) → Widgets → Laravel (Display)
```

## IMPORTANT REMINDERS

### Always Use Existing Architecture
- **NodeApiClient First**: Always use existing NodeApiClient for APIs
- **Extend, Don't Recreate**: Add methods to existing NodeApiClient/MockApiController
- **Laravel Cache**: Use Cache::remember() to simulate persistence in mocks
- **Http::fake()**: Follow existing test patterns
- **API_MODE**: Respect dynamic configuration via environment
- **Service Pattern**: Create services that use NodeApiClient
- **Metronic Integration**: Leverage available components
- **Docker Workflow**: Always test in containerized environment
- **Blade Components**: Create reusable components
- **API Resources**: Standardize API returns
- **JWT Tokens**: Use implemented authentication system
- **Testing**: PestPHP following NodeApiClientTest.php pattern

### Development Workflow
1. Choose module to implement
2. Extend NodeApiClient with new methods
3. Add mock endpoints to MockApiController
4. Create Service using NodeApiClient
5. Create Controller using Service
6. Create Blade views with Metronic components
7. Add routes (web.php and api.php)
8. Create tests following Http::fake() pattern
9. Test in Docker environment

## STARTING QUESTIONS

When starting development, always ask:
1. "Which module would you like to implement first?"
2. "Do you need any specific functionality?"
3. "What is the target user profile?"
4. "Are there any priority integrations?"
5. "Do you want to see the code or just the structure?"

## QUICK START COMMANDS

```bash
# Clone and setup
git clone [repo-url] sistema-parlamentar
cd sistema-parlamentar/laravel
make dev-setup

# Access and start development
make shell
php artisan route:list
php artisan tinker

# Configure analytics integration
php artisan config:publish analytics
php artisan make:service AnalyticsExportService
```

---

**GOAL**: Build a comprehensive parliamentary system incrementally, always validating each step before advancing. Act as a programming pair, asking, suggesting and implementing with technical excellence.

**ALWAYS START BY ASKING**: "Which module would you like us to implement first? I'll analyze dependencies and suggest an optimized order."