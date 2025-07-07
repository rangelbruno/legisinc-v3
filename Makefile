# Makefile para gerenciar o ambiente Docker do Laravel

.PHONY: help build up down restart logs shell composer artisan npm test

# Variáveis
COMPOSE_FILE = docker-compose.yml
COMPOSE_DEV_FILE = docker-compose.dev.yml
APP_CONTAINER = legisinc-app

# Comandos padrão
help: ## Exibe esta ajuda
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Constrói as imagens Docker
	docker-compose -f $(COMPOSE_FILE) build

build-dev: ## Constrói as imagens Docker para desenvolvimento
	docker-compose -f $(COMPOSE_DEV_FILE) build

up: ## Inicia os containers
	docker-compose -f $(COMPOSE_FILE) up -d

up-dev: ## Inicia os containers em modo desenvolvimento
	docker-compose -f $(COMPOSE_DEV_FILE) up -d

down: ## Para e remove os containers
	docker-compose -f $(COMPOSE_FILE) down

down-dev: ## Para e remove os containers de desenvolvimento
	docker-compose -f $(COMPOSE_DEV_FILE) down

restart: ## Reinicia os containers
	docker-compose -f $(COMPOSE_FILE) restart

restart-dev: ## Reinicia os containers de desenvolvimento
	docker-compose -f $(COMPOSE_DEV_FILE) restart

logs: ## Exibe os logs dos containers
	docker-compose -f $(COMPOSE_FILE) logs -f

logs-dev: ## Exibe os logs dos containers de desenvolvimento
	docker-compose -f $(COMPOSE_DEV_FILE) logs -f

shell: ## Acessa o shell do container da aplicação
	docker exec -it $(APP_CONTAINER) sh

shell-root: ## Acessa o shell do container da aplicação como root
	docker exec -it -u root $(APP_CONTAINER) sh

# Comandos Laravel
artisan: ## Executa comandos do Artisan (uso: make artisan cmd="route:list")
	docker exec -it $(APP_CONTAINER) php artisan $(cmd)

# Comandos Composer
composer: ## Executa comandos do Composer (uso: make composer cmd="install")
	docker exec -it $(APP_CONTAINER) composer $(cmd)

composer-install: ## Instala dependências do Composer
	docker exec -it $(APP_CONTAINER) composer install

composer-update: ## Atualiza dependências do Composer
	docker exec -it $(APP_CONTAINER) composer update


# Comandos de teste
test: ## Executa os testes
	docker exec -it $(APP_CONTAINER) php artisan test

test-coverage: ## Executa os testes com coverage
	docker exec -it $(APP_CONTAINER) php artisan test --coverage

# Comandos de limpeza
clean: ## Remove containers, volumes e imagens não utilizadas
	docker system prune -a --volumes

clean-all: ## Remove todos os containers, volumes e imagens
	docker-compose -f $(COMPOSE_FILE) down --volumes --remove-orphans
	docker-compose -f $(COMPOSE_DEV_FILE) down --volumes --remove-orphans
	docker system prune -a --volumes

# Comandos de cache
cache-clear: ## Limpa todos os caches
	docker exec -it $(APP_CONTAINER) php artisan cache:clear
	docker exec -it $(APP_CONTAINER) php artisan config:clear
	docker exec -it $(APP_CONTAINER) php artisan route:clear
	docker exec -it $(APP_CONTAINER) php artisan view:clear

cache-build: ## Reconstrói todos os caches
	docker exec -it $(APP_CONTAINER) php artisan config:cache
	docker exec -it $(APP_CONTAINER) php artisan route:cache
	docker exec -it $(APP_CONTAINER) php artisan view:cache

# Comandos de monitoramento
status: ## Mostra o status dos containers
	docker-compose -f $(COMPOSE_FILE) ps

stats: ## Mostra estatísticas dos containers
	docker stats

# Comandos de desenvolvimento
dev-setup: ## Configuração inicial para desenvolvimento
	cp .env.docker .env
	make build-dev
	make up-dev
	make composer-install
	make artisan cmd="key:generate"

prod-setup: ## Configuração inicial para produção
	cp .env.docker .env
	make build
	make up
	make artisan cmd="key:generate"

fresh-start: ## Reinicia o ambiente completamente
	make down
	make clean
	make build
	make up
	make artisan cmd="key:generate"