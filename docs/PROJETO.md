# Documentação do Projeto LegisInc

## Visão Geral

O **LegisInc** é um sistema de gestão legislativa desenvolvido com Laravel 12 que integra funcionalidades modernas de administração parlamentar, tramitação de **proposições**, gestão de usuários e API inteligente. O projeto utiliza uma arquitetura modular com suporte a múltiplos provedores de API e interface administrativa baseada no template Metronic. **Sistema migrado de Projetos para Proposições** seguindo processo legislativo correto.

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
    "spatie/laravel-permission": "^6.20",
    "doctrine/dbal": "*",
    "phpoffice/phpword": "*"
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
    "vite": "^6.2.4",
    "@tiptap/core": "^2.10.0",
    "@tiptap/starter-kit": "^2.10.0",
    "@tiptap/extension-table": "^2.10.0",
    "yjs": "^13.6.18"
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
│   │   ├── Parametro/           # Sistema de Parâmetros Modulares
│   │   ├── Admin/               # Controladores Administrativos
│   │   ├── MockApiController.php # Mock API para desenvolvimento
│   │   └── ApiTestController.php # Testes de API
│   ├── Models/                   # Modelos Eloquent
│   │   ├── Projeto.php          # Modelo de Projeto
│   │   ├── ProjetoTramitacao.php # Tramitação de Projetos
│   │   ├── ProjetoAnexo.php     # Anexos de Projetos
│   │   ├── ProjetoVersion.php   # Versões de Projetos
│   │   ├── ModeloProjeto.php    # Modelos de Projeto
│   │   ├── Parametro/           # Models do Sistema de Parâmetros
│   │   │   ├── ParametroModulo.php    # Módulos de Parâmetros
│   │   │   ├── ParametroSubmodulo.php # Submódulos
│   │   │   ├── ParametroCampo.php     # Campos
│   │   │   └── ParametroValor.php     # Valores
│   │   └── User.php             # Modelo de Usuário
│   ├── Services/                 # Serviços de negócio
│   │   ├── Projeto/             # Serviços de Projetos
│   │   ├── User/                # Serviços de Usuários
│   │   ├── Comissao/            # Serviços de Comissões
│   │   ├── Parlamentar/         # Serviços de Parlamentares
│   │   ├── Parametro/           # Serviços de Parâmetros
│   │   │   ├── ParametroService.php         # Service principal
│   │   │   ├── CacheParametroService.php    # Cache inteligente
│   │   │   ├── ValidacaoParametroService.php # Validações
│   │   │   └── AuditoriaParametroService.php # Auditoria
│   │   └── ApiClient/           # Cliente de API
│   ├── DTOs/                     # Data Transfer Objects
│   │   ├── Projeto/             # DTOs de Projetos
│   │   ├── User/                # DTOs de Usuários
│   │   ├── Parlamentar/         # DTOs de Parlamentares
│   │   └── Parametro/           # DTOs de Parâmetros
│   │       ├── ModuloParametroDTO.php     # DTO para módulos
│   │       ├── SubmoduloParametroDTO.php  # DTO para submódulos
│   │       ├── CampoParametroDTO.php      # DTO para campos
│   │       └── ValorParametroDTO.php      # DTO para valores
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
│       │   ├── parlamentares/   # Views de Parlamentares
│       │   └── parametros/      # Views de Parâmetros Modulares
│       ├── admin/               # Views administrativas
│       │   └── parametros/      # Interface administrativa de parâmetros
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

### 1. Autenticação e Identidade Digital
- **Localização**: `app/Http/Controllers/AuthController.php`, `resources/views/auth/`
- **Funcionalidades**:
  - Login unificado
  - Registro
  - Logout
  - Middleware de autenticação

### 2. Gestão de Usuários
- **Localização**: `app/Models/User.php`, `resources/views/modules/usuarios/`
- **Funcionalidades**:
  - CRUD completo de usuários
  - Sistema de permissões (Spatie Laravel Permission)
  - Campos parlamentares específicos
  - Autenticação e autorização

### 3. Gestão de Parlamentares
- **Localização**: `resources/views/modules/parlamentares/`
- **Funcionalidades**:
  - CRUD de parlamentares
  - Mesa diretora
  - Perfis detalhados

### 4. Gestão de Comissões
- **Localização**: `resources/views/modules/comissoes/`
- **Funcionalidades**:
  - CRUD de comissões
  - Classificação por tipo
  - Gestão de membros

### 5. Sistema de Proposições (NOVO)
- **Localização**: `app/Http/Controllers/Proposicao*.php`, `resources/views/proposicoes/`
- **Funcionalidades**:
  - Workflow parlamentar completo (4 etapas especializadas)
  - Criação com modelos e rascunhos
  - Revisão legislativa com aprovação/devolução
  - Sistema de assinatura digital
  - Protocolo automatizado
  - Tramitação completa
  - Editor de texto avançado
  - Histórico e relatórios por etapa

### 6. Sistema de Middleware e Permissões (NOVO)
- **Localização**: `app/Http/Middleware/CheckProposicaoPermission.php`, `app/Services/DynamicPermissionService.php`
- **Funcionalidades**:
  - Middleware especializado para controle de acesso a proposições
  - Sistema de permissões dinâmicas por etapa do workflow
  - Descoberta automática de rotas para configuração de permissões
  - Estrutura hierárquica de permissões por módulo
  - Interface administrativa para gestão de permissões
  - Suporte a múltiplos perfis de usuário (PARLAMENTAR, RELATOR, PROTOCOLO, ASSESSOR)
  - Validação de acesso baseada em roles e permissões granulares
  - Sistema de fallback para permissões não configuradas

### 7. Sistema de Parâmetros Modulares
- **Localização**: `app/Models/Parametro/`, `resources/views/admin/parametros/`
- **Funcionalidades**:
  - Sistema hierárquico de configuração (Módulos → Submódulos → Campos → Valores)
  - CRUD completo para todos os níveis da hierarquia
  - Interface administrativa responsiva com DataTables
  - Sistema de cache inteligente (compatível com file storage e Redis)
  - Validação de integridade referencial
  - Exclusão com validação e opção de força (cascade deletion)
  - Sistema de auditoria completo
  - API funcional com endpoints reais
  - Ordenação dinâmica e controle de status ativo/inativo
  - Importação/exportação de configurações
- **Arquitetura**:
  - Controllers especializados para cada nível da hierarquia
  - Service Layer robusto com separação de responsabilidades
  - DTOs para transferência de dados estruturada
  - Cache service com detecção automática de capabilities
  - Sistema de middlewares para autenticação híbrida
- **APIs Funcionais**:
  - `/api/parametros-modular/modulos/*` - Gestão de módulos
  - `/api/parametros-modular/submodulos/*` - Gestão de submódulos  
  - `/api/parametros-modular/campos/*` - Gestão de campos
  - `/api/parametros-modular/valores/*` - Gestão de valores
  - Endpoints especiais para validação, configuração e cache

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
- **Tipo**: PostgreSQL (development/production)
- **Localização**: Container Docker com volumes persistentes
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
- Autenticação e Identidade Digital
- Gestão completa de usuários
- Gestão de parlamentares e estrutura
- Gestão de comissões
- Gestão de projetos e documentos

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

### 8. Documentação da API
- Documentação completa em `docs/apiDocumentation.md`
- Checklist de implementação em `docs/api-implementation-checklist.md`
- 31 endpoints mock implementados
- Suporte a autenticação com Laravel Sanctum

## Documentação Técnica

O projeto possui documentação técnica detalhada localizada em `docs/`:

### Documentação de Melhorias
- **`docs/modelos-improvements.md`**: Documentação completa das melhorias na página de listagem de modelos
- **`docs/create-page-improvements.md`**: Documentação das melhorias na página de criação de modelos
- **`docs/PROJETO.md`**: Documentação geral do projeto (este arquivo)
- **`docs/progress.md`**: Acompanhamento detalhado de progresso
- **`docs/apiDocumentation.md`**: Documentação completa da API
- **`docs/api-implementation-checklist.md`**: Checklist de implementação da API

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
5. **Documentação da API**: Documentação completa e checklist de implementação
6. **Sistema de Parâmetros Modulares**: Sistema completo de configuração hierárquica com APIs funcionais
   - Arquitetura modular com 4 níveis hierárquicos
   - Sistema de cache inteligente com fallback automático
   - Interface administrativa completa com validações
   - API real funcionando (não mock) com autenticação
   - Sistema de exclusão inteligente com validação e força
   - Auditoria completa de todas as operações
   - Correção de problemas de CSRF token em operações AJAX
   - JavaScript robusto com tratamento de erros diferenciado

### Próximas Implementações 🔄
1. **Sessões Plenárias**: Controle de sessões, atas, presenças, pauta
2. **Sistema de Votação**: Votação eletrônica, resultados, histórico
3. **Transparência e Engajamento**: Portal cidadão, participação pública
4. **Analytics e Inteligência**: Dashboards, relatórios, estatísticas avançadas
5. **Notificações e Comunicação**: Sistema unificado, multi-canal
6. **Segurança e Compliance**: Security center, auditoria, LGPD
7. **Blockchain e Auditoria**: Trilha de auditoria, smart contracts

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

**Última atualização**: 2025-07-21
**Versão do Laravel**: 12.0
**Status**: 6 módulos core implementados (30% do total), **migração completa de Projetos para Proposições**, estrutura base completa, sistema de parâmetros modulares funcional, APIs reais funcionando, documentação completa, workflow legislativo correto implementado, sistema estável e consolidado, pronto para implementação de módulos de negócio avançados

---

## 🆕 Changelog Recente (2025-07-21)

### Sistema de Parâmetros Modulares - Implementação Completa

**Funcionalidades Adicionadas:**
- ✅ **Sistema Hierárquico Completo**: 4 níveis (Módulos → Submódulos → Campos → Valores)
- ✅ **Controllers Especializados**: `ParametroController`, `ModuloParametroController`, etc.
- ✅ **Service Layer Robusto**: `ParametroService`, `CacheParametroService`, `ValidacaoParametroService`, `AuditoriaParametroService`
- ✅ **Models com Relacionamentos**: Eloquent relationships bem definidos
- ✅ **Cache Inteligente**: Funciona com file storage e Redis automaticamente
- ✅ **API Funcional**: Endpoints reais `/api/parametros-modular/*` (não mock)
- ✅ **Interface Administrativa**: Views Metronic com DataTables
- ✅ **Sistema de Exclusão Avançado**: Validação + confirmação + exclusão forçada
- ✅ **Auditoria Completa**: Log de todas as operações
- ✅ **Tratamento de Erros**: JavaScript robusto com diferenciação de tipos de erro

**Problemas Resolvidos:**
- 🔧 **Cache Tagging**: Sistema compatível com drivers sem suporte a tagging
- 🔧 **CSRF Token Issues**: Endpoints API sem proteção CSRF para AJAX
- 🔧 **Cascade Deletion**: Exclusão inteligente com validação de dependências
- 🔧 **Error Handling**: Tratamento diferenciado entre erros de rede, validação e autenticação
- 🔧 **Database Relationships**: Estrutura hierárquica com integridade referencial

**Qualidade Técnica:**
- 📋 **Service Layer Pattern** implementado corretamente
- 📋 **DTO Pattern** para transferência de dados
- 📋 **Repository Pattern** com Eloquent
- 📋 **Error Handling** padronizado em toda a aplicação
- 📋 **Logging Completo** para debugging e auditoria
- 📋 **Testes de API** validados com curl
- 📋 **Documentação Inline** completa em todos os métodos

---

## 🔄 Migração Recente: Projetos → Proposições (2025-07-20)

### Motivação da Migração

O sistema antigo de "Projetos" não seguia corretamente o processo legislativo parlamentar brasileiro. Foi necessário uma migração completa para um sistema de "Proposições" que implementa o workflow correto.

### Arquivos Removidos

**Models e Estrutura de Dados:**
- ❌ `app/Models/Projeto.php`
- ❌ `app/Models/TipoProjeto.php`
- ❌ `app/Models/ModeloProjeto.php`
- ❌ `app/Models/ProjetoTramitacao.php`
- ❌ `app/Models/ProjetoAnexo.php`
- ❌ `app/Models/ProjetoVersion.php`

**Controllers e Lógica de Negócio:**
- ❌ `app/Http/Controllers/Projeto/` (diretório completo)
- ❌ `app/Http/Controllers/ModeloProjetoController.php`
- ❌ `app/Http/Controllers/TramitacaoController.php`

**Services e DTOs:**
- ❌ `app/Services/Projeto/` (diretório completo)
- ❌ `app/DTOs/Projeto/` (diretório completo)

**Views e Interface:**
- ❌ `resources/views/modules/projetos/` (diretório completo)

**Policies e Autorizações:**
- ❌ `app/Policies/ProjetoPolicy.php`
- ❌ `app/Policies/ModeloProjetoPolicy.php`

**Database:**
- ❌ Todas as migrations relacionadas a projetos
- ❌ `database/seeders/TipoProjetoSeeder.php`

**Testes:**
- ❌ `tests/Feature/ProjetoAccessControlTest.php`

### Sistema de Proposições Implementado

**Controllers Especializados:**
- ✅ `ProposicaoController.php` - Criação e gestão geral
- ✅ `ProposicaoLegislativoController.php` - Revisão legislativa
- ✅ `ProposicaoAssinaturaController.php` - Processo de assinatura
- ✅ `ProposicaoProtocoloController.php` - Protocolo e tramitação

**Middleware e Services:**
- ✅ `CheckProposicaoPermission.php` - Middleware de controle de acesso
- ✅ `DynamicPermissionService.php` - Gestão de permissões dinâmicas
- ✅ `RouteDiscoveryService.php` - Descoberta automática de rotas

**Views Especializadas:**
- ✅ `resources/views/proposicoes/` - Interface completa do workflow
- ✅ Views para cada etapa: criar, revisar, assinar, protocolar

**Database Schema:**
- ✅ Migrations atualizadas para proposições
- ✅ Campos específicos para cada etapa do processo
- ✅ Sistema de status e tramitação

### Workflow Parlamentar Implementado

1. **📝 Criação** (`proposicoes.criar`)
   - Escolha de modelos
   - Editor de texto avançado
   - Sistema de rascunhos
   - Envio para revisão legislativa

2. **🔍 Revisão Legislativa** (`proposicoes.revisar`)
   - Análise técnica
   - Aprovação ou devolução
   - Observações e correções
   - Envio para assinatura

3. **✍️ Assinatura** (`proposicoes.assinatura`)
   - Confirmação de leitura
   - Assinatura digital
   - Correções finais
   - Envio para protocolo

4. **📋 Protocolo** (`proposicoes.protocolar`)
   - Numeração automática
   - Efetivação do protocolo
   - Início da tramitação
   - Relatórios e estatísticas

### Limpeza do Sistema

**Navegação e Menus:**
- 🧹 Remoção de todas as referências a "Projetos" nos menus
- 🧹 Atualização da navegação para "Proposições"
- 🧹 Limpeza de links e rotas obsoletas

**Permissões e Roles:**
- 🧹 Remoção de permissões de projeto em todos os roles
- 🧹 Implementação de permissões específicas para proposições
- 🧹 Atualização do sistema de screen permissions

**Enums e Configurações:**
- 🧹 Remoção de `PROJETOS` do `SystemModule`
- 🧹 Limpeza do `AuthServiceProvider`
- 🧹 Atualização de configurações de sistema

### Benefícios da Migração

1. **Processo Correto:** Workflow que segue o processo legislativo real
2. **Controle Granular:** Permissões específicas para cada etapa
3. **Interface Especializada:** Views otimizadas para cada fase
4. **Rastreabilidade:** Histórico completo de toda a tramitação
5. **Flexibilidade:** Sistema extensível para novas funcionalidades
6. **Performance:** Código otimizado sem legacy code
7. **Manutenibilidade:** Arquitetura limpa e bem organizada

---

## 🔄 Status Atual do Sistema (2025-07-21)

### Sistema Consolidado e Estável

O LegisInc encontra-se em um estado **estável e consolidado** após a migração completa do sistema de Projetos para Proposições. Todos os 6 módulos core estão funcionando perfeitamente e o sistema está pronto para a próxima fase de desenvolvimento.

### Indicadores de Qualidade

**🟢 Funcionalidade:** Todos os módulos operacionais sem bugs críticos
**🟢 Performance:** Interface responsiva e otimizada  
**🟢 Segurança:** Sistema de autenticação e autorização robusto
**🟢 Usabilidade:** Interface intuitiva seguindo padrões Metronic
**🟢 Documentação:** Documentação técnica completa e atualizada

### Módulos em Produção

1. ✅ **Autenticação e Identidade Digital** - Sistema completo de login/logout
2. ✅ **Gestão de Usuários** - CRUD completo com permissões granulares  
3. ✅ **Gestão de Parlamentares** - Interface completa com busca avançada
4. ✅ **Gestão de Comissões** - Sistema de comissões permanentes e temporárias
5. ✅ **Sistema de Proposições** - Workflow legislativo completo implementado
6. ✅ **Sistema de Parâmetros Modulares** - Configuração hierárquica funcional

### Próximo Marco: Sessões Plenárias

O próximo módulo a ser implementado é o **Sistema de Sessões Plenárias**, que incluirá:
- Controle de sessões ordinárias e extraordinárias
- Sistema de atas digitais
- Controle de presença automatizado  
- Gestão inteligente de pauta
- Integração com sistema de votação