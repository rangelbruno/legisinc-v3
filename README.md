<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# LegisInc - Sistema de Gestão Legislativa

## 🚀 Novo Sistema de Gerenciamento de APIs

### Visão Geral
O LegisInc agora possui um sistema centralizado para gerenciar APIs que permite alternar facilmente entre:
- **Mock API** (desenvolvimento) - API interna do Laravel
- **API Externa** (produção) - API Node.js externa

### 🔧 Configuração Rápida

#### 1. Definir Modo da API
```bash
# Usar Mock API (recomendado para desenvolvimento)
php artisan api:mode mock

# Usar API Externa (para produção)
php artisan api:mode external

# Ver status atual
php artisan api:mode --status
```

#### 2. Configurações no .env
```bash
# Modo da API (mock ou external)
API_MODE=mock

# Configurações para API Externa (quando API_MODE=external)
EXTERNAL_API_URL=http://localhost:3000
EXTERNAL_API_TIMEOUT=30
EXTERNAL_API_RETRIES=3

# Credenciais padrão para testes
API_DEFAULT_EMAIL=bruno@test.com
API_DEFAULT_PASSWORD=senha123
```

### 📋 Modos Disponíveis

#### 🎯 Mock API (Desenvolvimento)
- **Vantagens**: Não requer API externa, desenvolvimento offline, dados controlados
- **Uso**: Desenvolvimento, testes, demonstrações
- **Armazenamento**: Cache do Laravel (temporário)
- **Endpoints**: `/api/mock-api/*`

#### 🌐 API Externa (Produção)
- **Vantagens**: Dados persistentes, performance real, integração completa
- **Uso**: Produção, staging, testes de integração
- **Requer**: API Node.js rodando
- **Endpoints**: Configuráveis via `EXTERNAL_API_URL`

### 🛠️ Comandos Úteis

```bash
# Alternar para Mock API
php artisan api:mode mock

# Alternar para API Externa
php artisan api:mode external

# Ver status atual da API
php artisan api:mode --status

# Resetar dados do Mock API
curl -X POST http://localhost:8000/api/mock-api/reset

# Testar saúde da API
curl http://localhost:8000/api/mock-api/
```

### 🔍 Verificação de Status

O sistema fornece indicadores visuais na interface:
- **🟢 Online**: API funcionando normalmente
- **🟡 Verificando**: Verificando status da API
- **🔴 Offline**: API não disponível
- **⚠️ Problemas**: API responde mas com problemas

### 📁 Estrutura do Sistema

```
├── config/api.php              # Configuração centralizada
├── app/Console/Commands/
│   └── ApiModeCommand.php      # Comando para alternar modos
├── app/Services/ApiClient/
│   └── Providers/
│       └── NodeApiClient.php   # Cliente inteligente
└── app/Http/Controllers/
    └── MockApiController.php   # Mock API completo
```

### 🔄 Fluxo de Funcionamento

1. **Configuração**: `config/api.php` define modo atual
2. **Cliente**: `NodeApiClient` se adapta automaticamente
3. **Roteamento**: URLs são ajustadas conforme o modo
4. **Autenticação**: Gerenciada automaticamente
5. **Interface**: Indicadores visuais mostram status

### 📊 Características do Mock API

- **Registro de usuários** com validação
- **Autenticação** com tokens simulados
- **CRUD completo** de usuários
- **Validação** de dados
- **Respostas realistas** com códigos HTTP apropriados
- **Armazenamento temporário** em cache

### 🔧 Desenvolvimento

#### Para começar rapidamente:
```bash
# 1. Configurar modo mock
php artisan api:mode mock

# 2. Acessar a aplicação
php artisan serve

# 3. Testar registro em: http://localhost:8000/register
```

#### Para produção:
```bash
# 1. Configurar API externa
php artisan api:mode external

# 2. Configurar .env
EXTERNAL_API_URL=https://sua-api.com
API_MODE=external

# 3. Verificar conexão
php artisan api:mode --status
```

### 📝 Endpoints Disponíveis

#### Mock API (`/api/mock-api/`)
- `GET /` - Health check
- `POST /register` - Registrar usuário
- `POST /login` - Fazer login
- `POST /logout` - Fazer logout
- `GET /users` - Listar usuários (auth)
- `GET /users/{id}` - Obter usuário (auth)
- `POST /users` - Criar usuário (auth)
- `PUT /users/{id}` - Atualizar usuário (auth)
- `DELETE /users/{id}` - Deletar usuário (auth)
- `POST /reset` - Resetar dados de teste

### 💡 Dicas de Uso

1. **Desenvolvimento**: Use sempre `mock` para desenvolvimento local
2. **Testes**: Mock API permite testes isolados e reproduzíveis
3. **Produção**: Configure `external` apenas quando API externa estiver pronta
4. **Debugging**: Use `php artisan api:mode --status` para diagnóstico
5. **Reset**: Use endpoint `/reset` para limpar dados de teste

### 🚨 Solução de Problemas

#### API não responde:
```bash
# Verificar modo atual
php artisan api:mode --status

# Testar conectividade
curl -I http://localhost:8000/api/mock-api/
```

#### Problemas de autenticação:
```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear

# Verificar configurações
php artisan config:show api
```

### 🔄 Migração Entre Modos

A troca entre modos é **instantânea** e **não requer reinicialização**:

```bash
# Atualmente em mock
php artisan api:mode --status
# Modo atual: mock

# Trocar para external
php artisan api:mode external
# ✅ Modo da API alterado para: external

# Verificar mudança
php artisan api:mode --status
# Modo atual: external
```

### 📈 Monitoramento

O sistema inclui monitoramento automático:
- **Health checks** periódicos
- **Indicadores visuais** na interface
- **Logs detalhados** de operações
- **Diagnóstico** de problemas

Isso garante uma **experiência de desenvolvimento fluida** e facilita a **transição para produção**.

---

**Pronto para começar?** Execute `php artisan api:mode mock` e comece a desenvolver! 🚀
