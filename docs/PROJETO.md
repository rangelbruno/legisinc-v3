# Documentação do Projeto Laravel

## Visão Geral

Este é um projeto Laravel 12 com uma interface administrativa baseada no template Metronic. O projeto utiliza uma estrutura moderna com Vite para build dos assets, TailwindCSS para styling e Blade components para organização da interface.

## Estrutura do Projeto

### Framework e Versões
- **Laravel**: 12.0
- **PHP**: ^8.2
- **Vite**: ^6.2.4
- **TailwindCSS**: ^4.0.0
- **Node.js**: Módulo ES6

### Estrutura de Diretórios Principais

```
laravel/
├── app/                          # Código da aplicação
│   ├── Http/Controllers/         # Controladores HTTP
│   ├── Models/                   # Modelos Eloquent
│   └── Providers/                # Provedores de serviços
├── resources/                    # Recursos frontend
│   ├── css/                      # Arquivos CSS
│   ├── js/                       # Arquivos JavaScript
│   └── views/                    # Views Blade
├── routes/                       # Definição de rotas
├── config/                       # Arquivos de configuração
├── database/                     # Migrations, seeders e factories
├── public/                       # Assets públicos e template
└── storage/                      # Arquivos de armazenamento
```

## Organização dos Componentes

### Sistema de Views

O projeto utiliza o sistema de componentes Blade do Laravel com uma estrutura bem organizada:

#### Layout Principal
- **Localização**: `resources/views/components/layouts/app.blade.php`
- **Funcionalidade**: Layout base da aplicação com integração do template Metronic
- **Características**:
  - Suporte a modo escuro/claro
  - Estrutura responsiva
  - Integração com assets do Metronic
  - Componentes modulares (header, aside, footer)

#### Componentes de Layout
```
resources/views/components/layouts/
├── app.blade.php      # Layout principal
├── aside.blade.php    # Barra lateral
├── footer.blade.php   # Rodapé
└── header.blade.php   # Cabeçalho
```

#### Página Inicial
- **Localização**: `resources/views/welcome.blade.php`
- **Estrutura**: Utiliza o component `<x-layouts.app>` com slot para título

### Assets e Template

#### Template Metronic
O projeto utiliza o template Metronic com uma estrutura completa de assets:

```
public/assets/
├── css/                    # Estilos CSS
│   ├── plugins.bundle.css  # Plugins CSS
│   └── style.bundle.css    # Estilos principais
├── js/                     # JavaScript
│   ├── custom/             # Scripts customizados
│   ├── scripts.bundle.js   # Scripts principais
│   └── widgets.bundle.js   # Widgets
├── media/                  # Recursos visuais
│   ├── avatars/            # Imagens de avatares
│   ├── auth/               # Imagens de autenticação
│   ├── icons/              # Ícones
│   ├── illustrations/      # Ilustrações
│   └── logos/              # Logos
└── plugins/                # Plugins externos
```

#### Recursos Incluídos
- **Ícones**: Duotune icons
- **Flags**: Conjunto completo de bandeiras de países
- **Ilustrações**: Múltiplas coleções (dozzy-1, sigma-1, sketchy-1, unitedpalms-1)
- **Logos**: Logos de frameworks e tecnologias
- **Avatars**: Conjunto de avatares para demonstração

## Configuração do Desenvolvimento

### Build System
O projeto utiliza **Vite** como bundler principal:

```javascript
// vite.config.js
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
- **Migrations**: Incluídas para users, cache e jobs

## Dependências Principais

### PHP (Composer)
```json
{
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1"
}
```

### Node.js (NPM)
```json
{
    "@tailwindcss/vite": "^4.0.0",
    "laravel-vite-plugin": "^1.2.0",
    "tailwindcss": "^4.0.0",
    "vite": "^6.2.4"
}
```

### Dependências de Desenvolvimento
- **Testing**: PestPHP
- **Code Quality**: Laravel Pint
- **Development**: Laravel Sail, Pail

## Funcionalidades Implementadas

### 1. Sistema de Layout Responsivo
- Layout administrativo completo
- Suporte a tema escuro/claro
- Componentes modulares reutilizáveis

### 2. Sistema de Assets
- Integração com Vite
- TailwindCSS configurado
- Template Metronic completo

### 3. Estrutura de Desenvolvimento
- Hot reload configurado
- Scripts de desenvolvimento otimizados
- Testes automatizados com PestPHP

## Configuração Docker

### Visão Geral do Docker

O projeto foi configurado para rodar completamente em containers Docker, proporcionando:

- **Isolamento**: Ambiente padronizado independente do sistema operacional
- **Portabilidade**: Execução consistente em qualquer máquina
- **Escalabilidade**: Fácil adição de novos serviços
- **Desenvolvimento**: Ambiente de desenvolvimento idêntico ao de produção

### Arquitetura dos Containers

```
app (Laravel + PHP-FPM + Nginx)
```

### Serviços Disponíveis

#### Container Principal (app)
- **Base**: PHP 8.2 FPM Alpine
- **Serviços**: Nginx, PHP-FPM, Supervisor
- **Portas**: 80 (HTTP), 443 (HTTPS)
- **Recursos**: Composer
- **Storage**: Sistema de arquivos local

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
make composer-install      # Instala dependências PHP

# Testes e cache
make test                  # Executa testes
make cache-clear           # Limpa cache
make cache-build           # Reconstrói cache
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

### Estrutura de Volumes

```yaml
volumes:
  - ./:/var/www/html          # Código da aplicação
  - ./storage:/var/www/html/storage # Storage da aplicação
  - ./bootstrap/cache:/var/www/html/bootstrap/cache # Cache do Laravel
```

### Variáveis de Ambiente

O arquivo `.env.docker` contém configurações otimizadas para Docker:

```env
# Banco de dados
DB_CONNECTION=null

# Cache e sessões
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Email
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
```

### Inicialização Automática

O script `docker/start.sh` automatiza:

1. 🔑 Geração da chave da aplicação
2. ⚡ Otimização de cache
3. 🔒 Configuração de permissões
4. 🔗 Criação de links simbólicos

### URLs de Acesso

- **Aplicação**: http://localhost

### Ambiente de Desenvolvimento

Para desenvolvimento, utilize:

```bash
make dev-setup
```

Este comando:
- Cria o arquivo `.env`
- Constrói as imagens
- Inicia os containers
- Instala dependências PHP
- Gera chave da aplicação

### Ambiente de Produção

Para produção, utilize:

```bash
make prod-setup
```

Configurações de produção incluem:
- Otimizações de performance
- Cache de configuração habilitado
- Logs estruturados
- Configurações de segurança
- Sem banco de dados (stateless)

## Próximos Passos

Este documento será atualizado conforme o desenvolvimento do projeto progride. As próximas implementações incluirão:

1. Desenvolvimento de APIs REST
2. Integração com serviços externos
3. Sistema de autenticação JWT
4. Dashboard administrativo
5. Implementação de microserviços

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

**Última atualização**: 2025-07-06
**Versão do Laravel**: 12.0
**Status**: Configuração Docker minimalista completa