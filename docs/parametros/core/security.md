# Security Policies - Sistema de Parâmetros SGVP

**Versão:** 2.0  
**Última Atualização:** 2024-01-15  
**Responsável:** Security Officer SGVP  
**Classificação:** Interno

---

## 🛡️ Visão Geral de Segurança

O Sistema de Parâmetros SGVP implementa múltiplas camadas de segurança para proteger dados sensíveis e garantir a integridade das operações administrativas.

## 🎯 Princípios de Segurança

### **1. Defense in Depth (Defesa em Profundidade)**
- Múltiplas camadas de proteção
- Nenhum ponto único de falha
- Validação em cada nível

### **2. Principle of Least Privilege**
- Acesso mínimo necessário
- Autorização granular
- Revisão periódica de permissões

### **3. Zero Trust Architecture**
- "Nunca confie, sempre verifique"
- Autenticação contínua
- Validação de cada requisição

---

## 🔐 Autenticação e Autorização

### **1. Token-Based Authentication**

#### **Implementação**
```php
// Middleware de Autenticação
class TokenAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = session('token');
        
        if (!$token || $this->isTokenExpired($token)) {
            return redirect()->route('login')
                ->withErrors('Sessão expirada. Faça login novamente.');
        }
        
        if (!$this->isValidToken($token)) {
            return $this->handleInvalidToken();
        }
        
        return $next($request);
    }
    
    private function isValidToken($token): bool
    {
        // Validação com API externa
        return ApiSgvp::validateToken($token);
    }
}
```

#### **Políticas de Token**
- **Duração:** 8 horas (sessão de trabalho)
- **Renovação:** Automática em ações do usuário
- **Revogação:** Imediata ao logout
- **Rotação:** A cada 2 horas de atividade contínua

### **2. Role-Based Access Control (RBAC)**

#### **Níveis de Acesso**
```php
// Roles e Permissões
const ROLES = [
    'super_admin' => [
        'parameters.*',
        'system.config',
        'users.manage'
    ],
    'admin' => [
        'parameters.data.*',
        'parameters.config.view'
    ],
    'operator' => [
        'parameters.data.view',
        'parameters.data.create'
    ],
    'viewer' => [
        'parameters.*.view'
    ]
];
```

#### **Middleware de Autorização**
```php
Route::middleware(['auth.token', 'can:parameters.manage'])->group(function () {
    // Rotas protegidas
});
```

---

## 🔍 Validação e Sanitização

### **1. Input Validation**

#### **Form Request Validation**
```php
class StoreParameterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'dto.name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-_]+$/',
            'dto.description' => 'nullable|string|max:500|strip_tags',
            'dto.active' => 'boolean',
            'dto.file' => 'sometimes|file|mimes:jpg,png,pdf|max:2048'
        ];
    }
    
    public function prepareForValidation()
    {
        $this->merge([
            'dto.name' => strip_tags($this->input('dto.name')),
            'dto.description' => Purifier::clean($this->input('dto.description'))
        ]);
    }
}
```

#### **Custom Validation Rules**
```php
// Validação de dados específicos do domínio
class ValidParameterType implements Rule
{
    public function passes($attribute, $value)
    {
        $allowedTypes = ['config', 'data', 'system'];
        return in_array($value, $allowedTypes);
    }
}
```

### **2. Output Sanitization**

#### **Blade Templating (Auto-escape)**
```blade
{{-- Escapado automaticamente --}}
{{ $userInput }}

{{-- Não escapado (apenas para dados confiáveis) --}}
{!! $trustedHtml !!}

{{-- Sanitização manual --}}
{{ Purifier::clean($userInput) }}
```

#### **API Response Sanitization**
```php
class ParameterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => strip_tags($this->name),
            'description' => Purifier::clean($this->description),
            'active' => (bool) $this->active
        ];
    }
}
```

---

## 🚫 Proteção Contra Ataques

### **1. CSRF Protection**

#### **Laravel Built-in CSRF**
```php
// Automático em todos os formulários
@csrf

// Verificação manual
if (!Hash::check($request->get('_token'), session('_token'))) {
    abort(419, 'CSRF token mismatch');
}
```

#### **AJAX CSRF Setup**
```javascript
// Setup global para AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

### **2. XSS Protection**

#### **Content Security Policy (CSP)**
```php
// Middleware CSP
'Content-Security-Policy' => "default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; " .
    "style-src 'self' 'unsafe-inline' fonts.googleapis.com; " .
    "font-src 'self' fonts.gstatic.com; " .
    "img-src 'self' data: *.amazonaws.com"
```

#### **HTML Purifier Configuration**
```php
// config/purifier.php
'default' => [
    'HTML.Allowed' => 'p,br,strong,em,ul,ol,li,a[href],h1,h2,h3,h4,h5,h6',
    'HTML.SafeObject' => false,
    'Output.FlashCompat' => false,
    'AutoFormat.RemoveEmpty' => true
]
```

### **3. SQL Injection Prevention**

#### **Prepared Statements via API**
```php
// Todas as queries passam pela API externa
// que utiliza prepared statements
class ApiSgvp
{
    public function post($endpoint, $data)
    {
        // Data é enviada como JSON, não como query string
        return Http::withToken($this->token)
            ->post($this->baseUrl . $endpoint, $data);
    }
}
```

### **4. Rate Limiting**

#### **Request Throttling**
```php
// Throttling por usuário
Route::middleware('throttle:60,1')->group(function () {
    // Rotas de parâmetros (60 requests/minuto)
});

// Throttling para operações sensíveis
Route::middleware('throttle:10,1')->group(function () {
    // Uploads e alterações críticas (10 requests/minuto)
});
```

#### **Custom Rate Limiting**
```php
class ParameterRateLimiter
{
    public function __invoke(Request $request)
    {
        $key = 'parameter_actions:' . $request->user()?->id;
        
        if (Cache::get($key, 0) >= 100) { // 100 ações por hora
            throw new TooManyRequestsException();
        }
        
        Cache::increment($key, 1, 3600); // TTL 1 hora
        
        return Limit::perHour(100);
    }
}
```

---

## 🔒 Proteção de Dados

### **1. Data Classification**

#### **Níveis de Classificação**
- **🔴 Confidencial:** Tokens de autenticação, senhas
- **🟡 Interno:** Configurações de sistema, dados de sessão
- **🟢 Público:** Dados já publicados, informações gerais

### **2. Encryption & Hashing**

#### **Data at Rest**
```php
// Criptografia de dados sensíveis
use Illuminate\Support\Facades\Crypt;

class ParameterService
{
    public function storeSensitiveData($data)
    {
        return [
            'data' => Crypt::encrypt($data),
            'hash' => Hash::make($data)
        ];
    }
}
```

#### **Data in Transit**
- **HTTPS Obrigatório** em produção
- **TLS 1.3** preferencialmente
- **Certificate Pinning** para APIs críticas

### **3. File Upload Security**

#### **Upload Validation**
```php
class FileUploadService
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'application/pdf'];
    private const MAX_SIZE = 2048; // KB
    
    public function validateUpload(UploadedFile $file): bool
    {
        // Validação de tipo MIME
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new InvalidFileTypeException();
        }
        
        // Validação de tamanho
        if ($file->getSize() > self::MAX_SIZE * 1024) {
            throw new FileTooLargeException();
        }
        
        // Scan de malware (se disponível)
        if ($this->hasVirusScanner()) {
            return $this->scanForVirus($file);
        }
        
        return true;
    }
    
    public function storeSecurely(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        return Storage::disk('s3-secure')->putFileAs(
            'parameters',
            $file,
            $filename,
            'private'
        );
    }
}
```

---

## 📊 Auditoria e Logging

### **1. Security Event Logging**

#### **Structured Logging**
```php
class SecurityLogger
{
    public function logAuthenticationAttempt($user, $success, $ip)
    {
        Log::channel('security')->info('Authentication Attempt', [
            'event_type' => 'auth_attempt',
            'user_id' => $user?->id,
            'username' => $user?->username,
            'success' => $success,
            'ip_address' => $ip,
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    public function logParameterChange($user, $action, $parameter, $oldValue, $newValue)
    {
        Log::channel('audit')->info('Parameter Changed', [
            'event_type' => 'parameter_change',
            'user_id' => $user->id,
            'action' => $action, // create, update, delete
            'parameter_type' => $parameter['type'],
            'parameter_id' => $parameter['id'],
            'old_value' => $this->sanitizeForLog($oldValue),
            'new_value' => $this->sanitizeForLog($newValue),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    private function sanitizeForLog($data)
    {
        // Remove dados sensíveis dos logs
        if (is_array($data)) {
            unset($data['password'], $data['token'], $data['secret']);
        }
        return $data;
    }
}
```

### **2. Intrusion Detection**

#### **Anomaly Detection**
```php
class SecurityMonitor
{
    public function detectAnomalies(Request $request)
    {
        $patterns = [
            'multiple_failed_logins' => $this->checkFailedLogins($request->ip()),
            'suspicious_user_agent' => $this->checkUserAgent($request->userAgent()),
            'unusual_request_volume' => $this->checkRequestVolume($request->ip()),
            'parameter_tampering' => $this->checkParameterTampering($request->all())
        ];
        
        foreach ($patterns as $pattern => $detected) {
            if ($detected) {
                $this->triggerSecurityAlert($pattern, $request);
            }
        }
    }
    
    private function triggerSecurityAlert($pattern, $request)
    {
        Log::channel('security')->warning('Security Alert', [
            'pattern' => $pattern,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'timestamp' => now()
        ]);
        
        // Notificar equipe de segurança se crítico
        if (in_array($pattern, ['multiple_failed_logins', 'parameter_tampering'])) {
            $this->notifySecurityTeam($pattern, $request);
        }
    }
}
```

---

## 🚨 Incident Response

### **1. Security Incident Classification**

#### **Severity Levels**
- **🔴 Critical:** Breach de dados, acesso não autorizado
- **🟡 High:** Tentativas de ataque, falhas de autenticação
- **🟢 Medium:** Comportamento suspeito, violações de política
- **⚪ Low:** Eventos informativos, logs de auditoria

### **2. Incident Response Workflow**

#### **Detection → Assessment → Containment → Eradication → Recovery → Lessons Learned**

```php
class IncidentResponse
{
    public function handleSecurityIncident($severity, $type, $details)
    {
        $incident = $this->createIncident($severity, $type, $details);
        
        switch ($severity) {
            case 'critical':
                $this->immediateLockdown();
                $this->notifyAllStakeholders($incident);
                break;
                
            case 'high':
                $this->increaseMonitoring();
                $this->notifySecurityTeam($incident);
                break;
                
            case 'medium':
                $this->logForReview($incident);
                break;
        }
        
        return $incident;
    }
    
    private function immediateLockdown()
    {
        // Desabilitar sessões ativas
        Cache::flush();
        
        // Revogar tokens ativos
        $this->revokeAllTokens();
        
        // Notificar administradores
        $this->sendEmergencyNotification();
    }
}
```

---

## 🔧 Security Configuration

### **1. Environment Security**

#### **Environment Variables**
```env
# Produção - Valores seguros
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sgvp.domain.com

# Chaves e Secrets
APP_KEY=base64:STRONG_32_CHARACTER_KEY
REDIS_PASSWORD=COMPLEX_PASSWORD

# API Security
API_VERIFY_SSL=true
API_TIMEOUT=30

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

#### **Server Security Headers**
```php
// Security Headers Middleware
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    return $response->withHeaders([
        'X-Frame-Options' => 'DENY',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()'
    ]);
}
```

### **2. Database Security**

#### **Connection Security**
```php
// config/database.php
'redis' => [
    'client' => 'predis',
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', 'sgvp_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
        'read_timeout' => 60,
        'context' => [
            'auth' => [env('REDIS_PASSWORD')],
            'stream' => [
                'verify_peer' => env('REDIS_VERIFY_PEER', true),
                'verify_peer_name' => env('REDIS_VERIFY_PEER_NAME', true),
            ]
        ]
    ],
],
```

---

## 📋 Security Checklist

### **Development Security Checklist**
- [ ] Input validation implementada
- [ ] Output sanitization configurada
- [ ] CSRF protection ativa
- [ ] Rate limiting aplicado
- [ ] Logging de segurança configurado
- [ ] Headers de segurança definidos
- [ ] Uploads de arquivo validados
- [ ] Autenticação e autorização testadas

### **Deployment Security Checklist**
- [ ] HTTPS configurado
- [ ] Certificados SSL válidos
- [ ] Environment variables seguras
- [ ] Debug mode desabilitado
- [ ] Error reporting configurado
- [ ] File permissions corretas
- [ ] Backup procedures testados
- [ ] Monitoring ativo

### **Operational Security Checklist**
- [ ] Logs de segurança monitorados
- [ ] Patches de segurança aplicados
- [ ] Access reviews realizados
- [ ] Incident response testado
- [ ] Security training atualizado
- [ ] Vulnerability scans executados
- [ ] Compliance verificado
- [ ] Documentation atualizada

---

## 📚 Compliance e Standards

### **Standards Seguidos**
- **OWASP Top 10** - Proteção contra principais vulnerabilidades
- **ISO 27001** - Gestão de segurança da informação
- **LGPD** - Lei Geral de Proteção de Dados
- **PCI DSS** - Para processamento de dados de cartão (se aplicável)

### **Regular Security Reviews**
- **Quarterly:** Vulnerability assessments
- **Biannually:** Penetration testing
- **Annually:** Security architecture review
- **Continuously:** Code security scanning

---

## 📞 Security Contacts

### **Emergency Response**
- **Security Team:** security@sgvp.com
- **Incident Hotline:** +55 (11) 9999-9999
- **On-call Security:** security-oncall@sgvp.com

### **Regular Communication**
- **Security Updates:** security-updates@sgvp.com
- **Policy Questions:** security-policy@sgvp.com
- **Training Requests:** security-training@sgvp.com

---

## 📖 Links Relacionados

- [Project Brief](./projectBrief.md)
- [System Architecture](./systemArchitecture.md)
- [Tech Stack](./techStack.md)
- [Development Workflows](../processes/creation-workflow.md)
- [Testing Strategy](../processes/testing-strategy.md)

---

**🔒 Security Note:** Este documento contém informações sensíveis sobre implementação de segurança. Acesso restrito a membros autorizados da equipe de desenvolvimento e segurança. 