# Documentação do Projeto LegisInc

## Visão Geral

O **LegisInc** é um sistema de gestão legislativa desenvolvido com Laravel 12 que integra funcionalidades modernas de administração parlamentar, tramitação de projetos, gestão de usuários e API inteligente. O projeto utiliza uma arquitetura modular com suporte a múltiplos provedores de API e interface administrativa baseada no template Metronic.

## Estrutura do Projeto

### Framework e Versões
- **Laravel**: 12.0
- **PHP**: ^8.2
- **Spatie Laravel Permission**: ^6.20
- **Vite**: ^6.2.4
- **TailwindCSS**: ^4.0.0
- **Node.js**: Módulo ES6
- **Testing**: PestPHP ^3.8

### Dependências Principais

#### PHP (Composer)
```json
{
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1",
    "spatie/laravel-permission": "^6.20"
}
```

#### Node.js (NPM)
```json
{
    "@tailwindcss/vite": "^4.0.0",
    "axios": "^1.8.2",
    "concurrently": "^9.0.1",
    "laravel-vite-plugin": "^1.2.0",
    "tailwindcss": "^4.0.0",
    "vite": "^6.2.4"
}
```

### Estrutura de Diretórios Principais

```
legisinc/
├── app/                          # Código da aplicação
│   ├── Http/Controllers/         # Controladores HTTP
│   │   ├── Projeto/             # Controladores de Projetos
│   │   ├── User/                # Controladores de Usuários
│   │   ├── Comissao/            # Controladores de Comissões
│   │   ├── Parlamentar/         # Controladores de Parlamentares
│   │   ├── MockApiController.php # Mock API para desenvolvimento
│   │   └── ApiTestController.php # Testes de API
│   ├── Models/                   # Modelos Eloquent
│   │   ├── Projeto.php          # Modelo de Projeto
│   │   ├── ProjetoTramitacao.php # Tramitação de Projetos
│   │   ├── ProjetoAnexo.php     # Anexos de Projetos
│   │   ├── ProjetoVersion.php   # Versões de Projetos
│   │   ├── ModeloProjeto.php    # Modelos de Projeto
│   │   └── User.php             # Modelo de Usuário
│   ├── Services/                 # Serviços de negócio
│   │   ├── Projeto/             # Serviços de Projetos
│   │   ├── User/                # Serviços de Usuários
│   │   ├── Comissao/            # Serviços de Comissões
│   │   ├── Parlamentar/         # Serviços de Parlamentares
│   │   └── ApiClient/           # Cliente de API
│   ├── DTOs/                     # Data Transfer Objects
│   │   ├── Projeto/             # DTOs de Projetos
│   │   ├── User/                # DTOs de Usuários
│   │   └── Parlamentar/         # DTOs de Parlamentares
│   ├── Policies/                 # Políticas de autorização
│   └── Providers/                # Provedores de serviços
├── resources/                    # Recursos frontend
│   ├── css/                      # Arquivos CSS
│   ├── js/                       # Arquivos JavaScript
│   └── views/                    # Views Blade
│       ├── components/           # Componentes Blade
│       │   ├── layouts/         # Layouts da aplicação
│       │   └── editor/          # Editor de texto
│       ├── modules/             # Views por módulo
│       │   ├── projetos/        # Views de Projetos
│       │   ├── usuarios/        # Views de Usuários
│       │   ├── comissoes/       # Views de Comissões
│       │   └── parlamentares/   # Views de Parlamentares
│       ├── admin/               # Views administrativas
│       ├── auth/                # Views de autenticação
│       ├── user/                # Views de usuários
│       └── api-test/            # Views de testes de API
├── routes/                       # Definição de rotas
│   ├── web.php                  # Rotas web
│   ├── api.php                  # Rotas de API
│   └── console.php              # Rotas de console
├── config/                       # Arquivos de configuração
│   ├── api.php                  # Configuração de API
│   ├── permission.php           # Configuração de permissões
│   └── services.php             # Configuração de serviços
├── database/                     # Migrations, seeders e factories
│   ├── migrations/              # Migrations do banco
│   ├── seeders/                 # Seeders para dados iniciais
│   └── factories/               # Factories para testes
├── docker/                       # Configurações Docker
│   ├── nginx/                   # Configuração Nginx
│   ├── php/                     # Configuração PHP
│   ├── supervisor/              # Configuração Supervisor
│   └── start.sh                 # Script de inicialização
├── public/                       # Assets públicos e template
│   └── assets/                  # Template Metronic completo
├── storage/                      # Arquivos de armazenamento
├── tests/                        # Testes automatizados
└── docs/                         # Documentação
```

## Sistema de Gestão de APIs

### Visão Geral do Sistema de APIs

O LegisInc possui um sistema centralizado para gerenciar APIs que permite alternar facilmente entre:
- **Mock API** (desenvolvimento) - API interna do Laravel
- **API Externa** (produção) - API Node.js externa

### Configuração da API

#### Modos Disponíveis
```bash
# Usar Mock API (recomendado para desenvolvimento)
php artisan api:mode mock

# Usar API Externa (para produção)
php artisan api:mode external

# Ver status atual
php artisan api:mode --status
```

#### Configurações no .env
```env
# Modo da API (mock ou external)
API_MODE=mock

# Configurações para API Externa
EXTERNAL_API_URL=http://localhost:3000
EXTERNAL_API_TIMEOUT=30
EXTERNAL_API_RETRIES=3

# Credenciais padrão para testes
API_DEFAULT_EMAIL=bruno@test.com
API_DEFAULT_PASSWORD=senha123
```

### Arquivos de Configuração

#### config/api.php
Configuração centralizada do sistema de APIs com:
- Modo de operação (mock/external)
- URLs e timeouts
- Credenciais padrão
- Configurações de cache e logging

#### config/services.php
Configuração de serviços externos incluindo:
- API clients para diferentes provedores
- Configurações de autenticação
- Configurações de timeout e retry

## Módulos Principais

### 1. Gestão de Projetos
- **Localização**: `app/Models/Projeto.php`, `resources/views/modules/projetos/`
- **Funcionalidades**:
  - CRUD completo de projetos
  - Sistema de tramitação
  - Anexos de projetos
  - Controle de versões
  - Editor de texto integrado

### 2. Gestão de Usuários
- **Localização**: `app/Models/User.php`, `resources/views/modules/usuarios/`
- **Funcionalidades**:
  - CRUD de usuários
  - Sistema de permissões (Spatie Laravel Permission)
  - Campos parlamentares específicos
  - Autenticação e autorização

### 3. Gestão de Comissões
- **Localização**: `resources/views/modules/comissoes/`
- **Funcionalidades**:
  - CRUD de comissões
  - Classificação por tipo
  - Gestão de membros

### 4. Gestão de Parlamentares
- **Localização**: `resources/views/modules/parlamentares/`
- **Funcionalidades**:
  - CRUD de parlamentares
  - Mesa diretora
  - Perfis detalhados

### 5. Sistema de Permissões
- **Localização**: `config/permission.php`
- **Funcionalidades**:
  - Roles e permissões
  - Controle de acesso granular
  - Sistema de times (opcional)

### 6. Gestão de Modelos de Projeto
- **Localização**: `app/Models/ModeloProjeto.php`, `resources/views/admin/modelos/`
- **Funcionalidades**:
  - CRUD completo de modelos de projeto
  - Interface Grid View e List View
  - Sistema de cards interativos com tipos específicos
  - Filtros dinâmicos e busca em tempo real
  - Ícones ki-duotone específicos para cada tipo de projeto
  - Editor de texto integrado com variáveis dinâmicas
  - Confirmações modais para exclusão
  - Design responsivo seguindo padrão Metronic
- **Tipos de Projeto Suportados**:
  - Projeto de Lei Ordinária (ki-document)
  - Projeto de Lei Complementar (ki-file-added)  
  - Emenda Constitucional (ki-security-user)
  - Decreto Legislativo (ki-notepad)
  - Resolução (ki-verify)
  - Indicação (ki-arrow-up-right)
  - Requerimento (ki-questionnaire-tablet)

## Configuração do Desenvolvimento

### Build System
O projeto utiliza **Vite** como bundler principal:

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

### Scripts Disponíveis

#### Composer Scripts
```bash
composer run dev      # Inicia todos os serviços em desenvolvimento
composer run test     # Executa testes
```

#### NPM Scripts
```bash
npm run dev           # Inicia Vite em modo desenvolvimento
npm run build         # Build para produção
```

### Banco de Dados
- **Tipo**: SQLite (development)
- **Localização**: `database/database.sqlite`
- **Migrations**: Incluídas para users, projetos, tramitação, anexos, versões, permissões

## Configuração Docker

### Visão Geral do Docker

O projeto foi configurado para rodar completamente em containers Docker, proporcionando:

- **Isolamento**: Ambiente padronizado independente do sistema operacional
- **Portabilidade**: Execução consistente em qualquer máquina
- **Escalabilidade**: Fácil adição de novos serviços
- **Desenvolvimento**: Ambiente de desenvolvimento idêntico ao de produção

### Arquitetura dos Containers

```
app (Laravel + PHP-FPM + Nginx + Supervisor)
```

### Serviços Disponíveis

#### Container Principal (app)
- **Base**: PHP 8.2 FPM Alpine
- **Serviços**: Nginx, PHP-FPM, Supervisor
- **Portas**: 8000 (HTTP), 8444 (HTTPS)
- **Recursos**: Composer, extensões PHP

### Arquivos de Configuração

```
docker/
├── nginx/
│   ├── nginx.conf          # Configuração principal do Nginx
│   └── default.conf        # Virtual host do Laravel
├── php/
│   ├── php.ini             # Configuração do PHP
│   └── php-fpm.conf        # Configuração do PHP-FPM
├── supervisor/
│   └── supervisord.conf    # Configuração do Supervisor
└── start.sh                # Script de inicialização
```

### Comandos Docker Disponíveis

#### Makefile
O projeto inclui um Makefile com comandos úteis:

```bash
# Configuração inicial
make dev-setup              # Configura ambiente de desenvolvimento
make prod-setup             # Configura ambiente de produção

# Gerenciamento de containers
make up                     # Inicia containers
make down                   # Para containers
make restart                # Reinicia containers
make logs                   # Visualiza logs

# Acesso aos containers
make shell                  # Shell do container da aplicação

# Comandos Laravel
make artisan cmd="route:list"  # Executa comandos artisan

# Comandos Composer
make composer-install       # Instala dependências PHP

# Testes e cache
make test                   # Executa testes
make cache-clear            # Limpa cache
make cache-build            # Reconstrói cache
```

#### Docker Compose
```bash
# Desenvolvimento
docker-compose -f docker-compose.dev.yml up -d

# Produção
docker-compose up -d

# Logs
docker-compose logs -f

# Parar containers
docker-compose down
```

### Inicialização Automática

O script `docker/start.sh` automatiza:

1. 🔑 Geração da chave da aplicação
2. ⚡ Otimização de cache
3. 🔒 Configuração de permissões
4. 🔗 Criação de links simbólicos

### URLs de Acesso

- **Aplicação**: http://localhost:8000
- **Desenvolvimento**: http://localhost:3001

## Sistema de Testes

### Estrutura de Testes
- **Framework**: PestPHP
- **Localização**: `tests/`
- **Tipos**: Feature e Unit tests

### Executar Testes
```bash
# Via Docker
make test

# Localmente
php artisan test

# Com coverage
php artisan test --coverage
```

## Funcionalidades Implementadas

### 1. Sistema de Layout Responsivo
- Layout administrativo completo baseado no Metronic
- Suporte a tema escuro/claro
- Componentes modulares reutilizáveis
- Dashboard principal

### 2. Sistema de Assets
- Integração com Vite
- TailwindCSS 4.0 configurado
- Template Metronic completo
- Hot reload configurado

### 3. Sistema de Gestão de APIs
- Mock API para desenvolvimento
- Cliente inteligente para APIs externas
- Autenticação automática
- Sistema de cache

### 4. Sistema de Autorização
- Spatie Laravel Permission integrado
- Roles e permissões granulares
- Middleware de autorização

### 5. Módulos Funcionais
- Gestão completa de projetos
- Sistema de tramitação
- Gestão de usuários e parlamentares
- Sistema de comissões
- Gestão avançada de modelos de projeto

### 6. Sistema de Templates e Interface
- **Interface Metronic Completa**: Todas as páginas administrativas seguem o padrão Metronic
- **Sistema Grid/List View**: Visualização dupla para melhor experiência do usuário
- **Cards Interativos**: Sistema de cards com hover effects e animações
- **Ícones Semânticos**: Ícones ki-duotone específicos para cada tipo de conteúdo
- **Filtros Dinâmicos**: Sistema de filtros em tempo real
- **Busca Instantânea**: Busca sem recarregamento de página
- **Modal Confirmations**: Confirmações elegantes para ações críticas
- **Design Responsivo**: Interface otimizada para todos os dispositivos

### 7. Sistema de Assets Otimizado
- **Comando FixAssetPaths**: Correção automática de caminhos de assets 404
- **Estrutura Organizada**: Assets organizados seguindo padrões Laravel
- **Integração Vite**: Build system otimizado para desenvolvimento e produção

## Documentação Técnica

O projeto possui documentação técnica detalhada localizada em `docs/`:

### Documentação de Melhorias
- **`docs/modelos-improvements.md`**: Documentação completa das melhorias na página de listagem de modelos
- **`docs/create-page-improvements.md`**: Documentação das melhorias na página de criação de modelos
- **`docs/PROJETO.md`**: Documentação geral do projeto (este arquivo)

### Características da Documentação
- **Diagramas Mermaid**: Fluxos visuais das funcionalidades
- **Screenshots**: Demonstrações visuais das interfaces
- **Código de Exemplo**: Snippets de código para referência
- **Checklist de Funcionalidades**: Acompanhamento de implementações
- **Estrutura Modular**: Organização por módulos e funcionalidades

## Próximos Passos

Este documento será atualizado conforme o desenvolvimento do projeto progride. As próximas implementações incluirão:

### Melhorias Completadas ✅
1. **Sistema de Modelos de Projeto**: Interface completa com Grid/List View, cards interativos e design Metronic
2. **Correção de Assets**: Comando automático para correção de caminhos 404
3. **Documentação Técnica**: Documentação detalhada das melhorias implementadas
4. **Interface Responsiva**: Design otimizado para todos os dispositivos

### Próximas Implementações 🔄
1. Aprimoramento do sistema de tramitação
2. Integração com APIs externas
3. Dashboard de analytics avançado
4. Sistema de notificações em tempo real
5. Módulos de transparência
6. Sistema de workflow automatizado
7. Relatórios e estatísticas avançadas

## Comandos Úteis

### Desenvolvimento Local (sem Docker)
```bash
# Iniciar servidor de desenvolvimento
php artisan serve

# Executar testes
php artisan test

# Limpar cache
php artisan cache:clear

# Gerar chave da aplicação
php artisan key:generate

# Comandos específicos de API
php artisan api:mode mock
php artisan api:mode external
php artisan api:mode --status

# Comando de correção de assets
php artisan assets:fix-paths      # Corrige caminhos de assets para usar {{ asset() }}
```

### Desenvolvimento com Docker
```bash
# Configuração inicial
make dev-setup

# Comandos comuns
make up                    # Iniciar containers
make shell                 # Acessar shell do container
make artisan cmd="route:list" # Executar comandos artisan
make test                  # Executar testes
make logs                  # Ver logs
```

---

**Última atualização**: 2025-01-16
**Versão do Laravel**: 12.0
**Status**: Sistema LegisInc com interface Metronic completa, gestão avançada de modelos e documentação técnica detalhada