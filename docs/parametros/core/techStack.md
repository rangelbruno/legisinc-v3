# Tech Stack - Sistema de Parâmetros SGVP

**Versão:** 2.0  
**Última Atualização:** 2024-01-15  
**Responsável:** Tech Lead SGVP

---

## 🏗️ Overview Tecnológico

O Sistema de Parâmetros SGVP utiliza uma stack moderna e robusta, baseada em Laravel, para garantir performance, segurança e manutenibilidade.

## 🔧 Stack Principal

### **Backend Framework**
- **Laravel 10.x** - Framework PHP principal
  - **Eloquent ORM** - Para modelagem de dados
  - **Blade Templates** - Engine de template
  - **Artisan CLI** - Comandos personalizados
  - **Laravel Mix/Vite** - Build system

### **Frontend Stack**
- **Bootstrap 5.x** - Framework CSS
- **jQuery 3.x** - Manipulação DOM e AJAX
- **DataTables.js** - Tabelas avançadas
- **SweetAlert2** - Modais e alertas
- **Font Awesome** - Ícones
- **SCSS** - Preprocessador CSS

### **Cache & Session**
- **Redis** - Cache distribuído e sessions
- **Laravel Cache** - Abstração de cache
- **Session Storage** - Gerenciamento de sessões

### **External Integrations**
- **SGVP API** - API REST externa
- **AWS S3** - Armazenamento de arquivos
- **HTTP Client** - Cliente HTTP Laravel

---

## 📊 Arquitetura de Dependências

```
┌─────────────────────────────────────────────────────────────┐
│                      PRESENTATION                           │
├─────────────────────────────────────────────────────────────┤
│  Blade 10.x  │ Bootstrap 5.x │ jQuery 3.x │ DataTables.js  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      APPLICATION                            │
├─────────────────────────────────────────────────────────────┤
│           Laravel 10.x Framework Ecosystem                  │
│  Controllers │ Routes │ Middleware │ Form Requests         │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                       SERVICES                              │
├─────────────────────────────────────────────────────────────┤
│   PHP 8.2+   │  Custom Services  │  Validation Rules      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE                           │
├─────────────────────────────────────────────────────────────┤
│    Redis     │    HTTP Client    │      File System        │
│   (Cache)    │   (API Calls)     │    (Local/S3)          │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔧 Detalhamento Técnico

### **1. Laravel Framework Stack**

#### **Core Dependencies**
```json
{
  "php": "^8.2",
  "laravel/framework": "^10.0",
  "laravel/tinker": "^2.8"
}
```

#### **Laravel Features Utilizadas**
- **Route Model Binding** - Injeção automática de modelos
- **Form Request Validation** - Validação robusta
- **Service Container** - Injeção de dependência
- **Facades** - Interfaces estáticas limpas
- **Middleware** - Camada de segurança
- **Artisan Commands** - Automação personalizada
- **Cache System** - Performance otimizada
- **Logging** - Monitoramento estruturado

### **2. Frontend Technologies**

#### **CSS Framework & Preprocessors**
```scss
// Bootstrap 5.x Configuration
@import "bootstrap/scss/bootstrap";

// Custom SGVP Styles
@import "components/parameters";
@import "components/tables";
@import "components/forms";
```

#### **JavaScript Libraries**
```javascript
// Core Libraries
- jQuery 3.x              // DOM manipulation & AJAX
- Bootstrap 5.x JS        // UI components
- DataTables.js          // Advanced tables
- SweetAlert2            // Modern alerts
- Axios                  // HTTP client (alternative)

// DataTables Plugins
- Responsive extension   // Mobile optimization
- Buttons extension     // Export functionality
- ColReorder extension  // Column management
```

#### **Build System**
```javascript
// Laravel Vite Configuration
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
```

### **3. Cache & Performance Stack**

#### **Redis Configuration**
```php
// config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'prefix' => env('CACHE_PREFIX', 'sgvp_cache'),
    ],
],

// Cache Strategy
- L1: Memory cache (per request)
- L2: Redis cache (shared)
- L3: HTTP cache (browser)
```

#### **Session Management**
```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'redis'),
'connection' => 'session',
'table' => 'sessions',
'store' => 'redis',
```

### **4. External API Integration**

#### **HTTP Client Stack**
```php
// Custom API Facade
use Illuminate\Support\Facades\Http;

class ApiSgvp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'api.sgvp';
    }
}

// HTTP Configuration
Http::withOptions([
    'timeout' => 30,
    'connect_timeout' => 10,
    'verify' => env('API_VERIFY_SSL', true),
]);
```

#### **API Response Handling**
```php
// Response Processing Pipeline
Request → Validation → API Call → Response Validation → Cache → Return

// Error Handling Layers
1. Network errors (connection, timeout)
2. HTTP errors (4xx, 5xx status codes)  
3. API errors (business logic failures)
4. Data validation errors (malformed responses)
```

---

## 📦 Dependências Detalhadas

### **Composer Dependencies (PHP)**

#### **Production Dependencies**
```json
{
  "laravel/framework": "^10.0",
  "laravel/sanctum": "^3.2",
  "predis/predis": "^2.0",
  "aws/aws-sdk-php": "^3.0",
  "intervention/image": "^2.7",
  "league/flysystem-aws-s3-v3": "^3.0"
}
```

#### **Development Dependencies**
```json
{
  "laravel/breeze": "^1.21",
  "laravel/pint": "^1.0",
  "laravel/tinker": "^2.8",
  "nunomaduro/collision": "^7.0",
  "phpunit/phpunit": "^10.0",
  "spatie/laravel-ignition": "^2.0"
}
```

### **NPM Dependencies (JavaScript)**

#### **Production Dependencies**
```json
{
  "bootstrap": "^5.3.0",
  "jquery": "^3.7.0",
  "datatables.net": "^1.13.0",
  "datatables.net-bs5": "^1.13.0",
  "datatables.net-responsive": "^2.5.0",
  "sweetalert2": "^11.0.0",
  "axios": "^1.4.0",
  "@fortawesome/fontawesome-free": "^6.4.0"
}
```

#### **Development Dependencies**
```json
{
  "vite": "^4.0.0",
  "laravel-vite-plugin": "^0.7.2",
  "sass": "^1.62.0",
  "autoprefixer": "^10.4.14",
  "postcss": "^8.4.24"
}
```

---

## 🏗️ Infrastructure Requirements

### **Minimum System Requirements**

#### **Server Environment**
- **PHP:** 8.2+ with extensions:
  - BCMath, Ctype, Fileinfo, JSON, Mbstring
  - OpenSSL, PDO, Tokenizer, XML, cURL
  - Redis extension (for caching)

#### **Web Server**
- **Nginx 1.18+** (preferred) or **Apache 2.4+**
- **SSL/TLS** certificate (HTTPS required)
- **URL Rewriting** enabled

#### **Database & Cache**
- **Redis 6.0+** for cache and sessions
- No direct database (uses external API)

#### **Storage**
- **Local filesystem** for temporary files
- **AWS S3** for persistent file storage
- **Minimum 512MB** RAM per process
- **SSD storage** recommended

### **Development Environment**

#### **Recommended Setup**
```bash
# Laravel Sail (Docker) - Recommended
sail up -d

# Or Local Environment
php artisan serve
npm run dev
redis-server
```

#### **IDE Configuration**
```json
// VSCode Extensions Recommended
{
  "recommendations": [
    "bmewburn.vscode-intelephense-client",
    "ryannaddy.laravel-artisan",
    "onecentlin.laravel-blade",
    "bradlc.vscode-tailwindcss"
  ]
}
```

---

## 🚀 Performance Optimizations

### **PHP Optimizations**

#### **OPcache Configuration**
```ini
; php.ini optimizations
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
```

#### **Laravel Optimizations**
```bash
# Production Optimization Commands
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### **Frontend Optimizations**

#### **Asset Optimization**
```javascript
// vite.config.js - Production Build
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['jquery', 'bootstrap'],
                    datatables: ['datatables.net', 'datatables.net-bs5']
                }
            }
        }
    }
});
```

#### **CDN Configuration**
```php
// Use CDN for static assets in production
'asset_url' => env('ASSET_URL', 'https://cdn.sgvp.com'),
```

---

## 🔒 Security Stack

### **Laravel Security Features**
- **CSRF Protection** - Token validation
- **XSS Protection** - Input sanitization
- **SQL Injection** - Prepared statements (via API)
- **Rate Limiting** - Throttling middleware
- **HTTPS Enforcement** - SSL/TLS required

### **Additional Security Tools**
```php
// Security Headers Middleware
'security.headers' => [
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block',
    'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains'
]
```

---

## 📊 Monitoring & Debugging Stack

### **Logging Configuration**
```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'bugsnag'],
    ],
    'structured' => [
        'driver' => 'daily',
        'path' => storage_path('logs/structured.log'),
        'formatter' => JsonFormatter::class,
    ]
]
```

### **Development Tools**
- **Laravel Telescope** - Debugging dashboard
- **Laravel Debugbar** - Development profiler
- **Clockwork** - Performance profiler
- **Ray** - Debug tool (optional)

---

## 🔄 Version Management

### **Version Compatibility Matrix**

| Component | Minimum | Recommended | Latest Tested |
|-----------|---------|-------------|---------------|
| PHP | 8.1 | 8.2 | 8.3 |
| Laravel | 10.0 | 10.x | 10.34 |
| Redis | 6.0 | 7.0 | 7.2 |
| Node.js | 16.0 | 18.0 | 20.0 |
| Bootstrap | 5.0 | 5.3 | 5.3.2 |
| jQuery | 3.6 | 3.7 | 3.7.1 |

### **Upgrade Strategy**
```bash
# Regular Update Process
composer update
npm update
php artisan migrate
php artisan config:clear
npm run build
```

---

## 📚 Documentation & Resources

### **Official Documentation**
- [Laravel Documentation](https://laravel.com/docs/10.x)
- [Bootstrap Documentation](https://getbootstrap.com/docs/5.3/)
- [DataTables Documentation](https://datatables.net/manual/)
- [Redis Documentation](https://redis.io/documentation)

### **Internal Resources**
- [API Documentation](../reference/apiDocumentation.md)
- [Deployment Guide](../reference/deploymentGuide.md)
- [Security Policies](./security.md)
- [Development Workflows](../processes/creation-workflow.md)

---

## ⚡ Quick Start Commands

### **Development Setup**
```bash
# Clone and setup
git clone [repository]
cd sgvp-parameters
composer install
npm install
cp .env.example .env
php artisan key:generate

# Start development
php artisan serve
npm run dev
redis-server
```

### **Testing**
```bash
# Run tests
php artisan test
npm run test

# Code quality
./vendor/bin/pint
php artisan insights
```

### **Deployment**
```bash
# Production deployment
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔮 Future Considerations

### **Potential Upgrades**
- **Laravel 11.x** - When LTS is available
- **PHP 8.3** - Latest stable version
- **Vue.js 3** - For more interactive components
- **Tailwind CSS** - Alternative to Bootstrap
- **Inertia.js** - SPA-like experience

### **Performance Improvements**
- **Laravel Octane** - High-performance application server
- **Queue System** - Background job processing
- **CDN Integration** - Global asset distribution
- **Database Optimization** - When direct DB access is added

---

## 📖 Links Relacionados

- [Project Brief](./projectBrief.md)
- [System Architecture](./systemArchitecture.md)
- [Security Policies](./security.md)
- [Development Workflows](../processes/creation-workflow.md)
- [Deployment Guide](../reference/deploymentGuide.md)

---

**🔧 Maintenance Note:** Este documento deve ser atualizado sempre que houver mudanças significativas no stack tecnológico ou atualizações de versão. 