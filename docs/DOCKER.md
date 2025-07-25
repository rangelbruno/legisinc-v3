# Configurações Docker - Legisinc

## Visão Geral

O projeto Legisinc utiliza Docker Compose para orquestrar 4 containers principais:
- **app**: Aplicação Laravel (PHP 8.2 + Nginx)
- **db**: Banco de dados PostgreSQL 15
- **redis**: Cache e filas Redis 7
- **onlyoffice**: Servidor de documentos OnlyOffice

## Estrutura de Containers

### Container App (legisinc-app)
- **Imagem**: Custom (baseada em php:8.2-fpm-alpine)
- **Portas expostas**: 8001:80, 8444:443
- **Diretório de trabalho**: `/var/www/html`
- **Volumes montados**:
  - `./:/var/www/html` (código fonte)
  - `./docker/php/php.ini:/usr/local/etc/php/php.ini`

### Container PostgreSQL (legisinc-postgres)
- **Imagem**: postgres:15-alpine
- **Porta exposta**: 5432:5432
- **Volume persistente**: `postgres_data:/var/lib/postgresql/data`

### Container Redis (legisinc-redis)
- **Imagem**: redis:7-alpine
- **Porta exposta**: 6379:6379
- **Volume persistente**: `redis_data:/data`
- **Configuração**: Persistência ativa com `--appendonly yes`

### Container OnlyOffice (legisinc-onlyoffice)
- **Imagem**: onlyoffice/documentserver:8.0
- **Porta exposta**: 8080:80
- **Volumes persistentes**:
  - `onlyoffice_data:/var/www/onlyoffice/Data`
  - `onlyoffice_logs:/var/log/onlyoffice`
  - `onlyoffice_cache:/var/lib/onlyoffice/documentserver/App_Data/cache/files`
  - `onlyoffice_forgotten:/var/lib/onlyoffice/documentserver/App_Data/cache/forgotten`
  - `./storage/app/public:/var/www/onlyoffice/Data/public`

## Configurações de Rede

### Rede Principal
- **Nome**: `legisinc_network`
- **Driver**: bridge
- **Comunicação interna**: Todos os containers estão na mesma rede

### Comunicação Entre Containers
Os containers se comunicam através dos nomes de serviço:
- **App → DB**: `db:5432`
- **App → Redis**: `redis:6379`
- **App → OnlyOffice**: `onlyoffice:80`
- **OnlyOffice → DB**: `db:5432`
- **OnlyOffice → Redis**: `redis:6379`

## Credenciais e Acesso ao Banco de Dados

### PostgreSQL - Configuração Principal
```
Host: db (interno) / localhost:5432 (externo)
Database: legisinc
Username: postgres
Password: 123456
Port: 5432
```

### Variáveis de Ambiente do Container DB
```
POSTGRES_DB=legisinc
POSTGRES_USER=postgres
POSTGRES_PASSWORD=123456
```

### Variáveis de Ambiente do Container App
```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=legisinc
DB_USERNAME=postgres
DB_PASSWORD=123456
```

### OnlyOffice - Acesso ao PostgreSQL
```
DB_TYPE=postgres
DB_HOST=db
DB_PORT=5432
DB_NAME=legisinc
DB_USER=postgres
DB_PWD=123456
```

## Configurações Redis

### Container Redis
- **Host interno**: `redis`
- **Porta**: 6379
- **Password**: null (sem autenticação)

### Aplicação Laravel
```
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=null
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Configurações OnlyOffice

### Segurança JWT
```
JWT_ENABLED=false
JWT_SECRET=MySecretKey123 (padrão)
JWT_HEADER=Authorization
JWT_IN_BODY=true
```

### Outros Parâmetros
```
WOPI_ENABLED=false
USE_UNAUTHORIZED_STORAGE=false
```

## Volumes Persistentes

### Volumes Named
- `postgres_data`: Dados do PostgreSQL
- `redis_data`: Dados do Redis
- `onlyoffice_data`: Dados do OnlyOffice
- `onlyoffice_logs`: Logs do OnlyOffice
- `onlyoffice_cache`: Cache de arquivos
- `onlyoffice_forgotten`: Cache de arquivos esquecidos

### Volumes Bind Mount
- `./:/var/www/html`: Código fonte da aplicação
- `./docker/php/php.ini`: Configurações PHP
- `./storage/app/public:/var/www/onlyoffice/Data/public`: Compartilhamento de arquivos públicos

## Dependências entre Containers

### Container App
- Depende de: `db`, `redis`
- Aguarda inicialização completa antes de subir

### Container OnlyOffice
- Depende de: `db`, `redis`
- Aguarda inicialização completa antes de subir

## Arquivos de Configuração Docker

### Principais
- `docker-compose.yml`: Configuração principal de produção
- `docker-compose.dev.yml`: Configuração simplificada para desenvolvimento
- `Dockerfile`: Imagem customizada da aplicação
- `.env.docker`: Variáveis de ambiente para Docker

### Configurações Específicas
- `docker/nginx/nginx.conf`: Configuração principal do Nginx
- `docker/nginx/default.conf`: Virtual host padrão
- `docker/php/php.ini`: Configurações PHP
- `docker/php/php-fpm.conf`: Configurações PHP-FPM
- `docker/supervisor/supervisord.conf`: Gerenciamento de processos
- `docker/start.sh`: Script de inicialização

## Como Executar

### Ambiente de Produção
```bash
docker-compose up -d
```

### Ambiente de Desenvolvimento
```bash
docker-compose -f docker-compose.dev.yml up -d
```

### Parar os Serviços
```bash
docker-compose down
```

### Verificar Logs
```bash
docker-compose logs -f [nome_do_serviço]
```

## Portas Expostas

| Serviço | Porta Host | Porta Container | Descrição |
|---------|------------|----------------|-----------|
| App | 8001 | 80 | Aplicação web HTTP |
| App | 8444 | 443 | Aplicação web HTTPS |
| PostgreSQL | 5432 | 5432 | Banco de dados |
| Redis | 6379 | 6379 | Cache e filas |
| OnlyOffice | 8080 | 80 | Servidor de documentos |

## Segurança

### Boas Práticas Implementadas
- Usuário não-root no container da aplicação
- Volumes nomeados para persistência
- Rede isolada para comunicação interna
- Configurações específicas por ambiente

### Pontos de Atenção
- **Senhas padrão**: Alterar as senhas padrão em produção
- **JWT desabilitado**: OnlyOffice sem autenticação JWT
- **Portas expostas**: Considerar firewall para acesso externo