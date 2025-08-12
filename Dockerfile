# Dockerfile para Laravel básico
FROM php:8.2-fpm-alpine

# Definir argumentos de build
ARG USER_ID=1000
ARG GROUP_ID=1000

# Instalar dependências do sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor \
    nginx \
    nodejs \
    npm \
    postgresql-dev \
    libpq-dev \
    autoconf \
    build-base \
    libzip-dev

# Instalar extensões PHP primeiro
RUN docker-php-ext-install mbstring exif pcntl bcmath gd pdo zip

# Instalar PostgreSQL extensions
RUN docker-php-ext-configure pgsql -with-pgsql \
    && docker-php-ext-install pgsql pdo_pgsql

# Instalar Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário não-root
RUN addgroup -g $GROUP_ID laravel && \
    adduser -u $USER_ID -G laravel -s /bin/sh -D laravel

# Criar diretórios necessários
RUN mkdir -p /var/www/html \
    /var/log/nginx \
    /var/log/php \
    /var/log/supervisor \
    /var/log/laravel \
    /var/cache/nginx \
    /var/lib/nginx \
    /etc/supervisor/conf.d

# Configurar Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Configurar PHP-FPM
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Configurar Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script de inicialização
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de dependências
COPY --chown=laravel:laravel composer.json composer.lock package.json package-lock.json ./

# Instalar dependências PHP
USER laravel
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Instalar dependências NPM e compilar assets (skip se não tiver arquivos JS/CSS)
RUN npm ci && (npm run build || echo "Build skipped - no assets to compile")

# Voltar para root para configurações finais
USER root

# Copiar código da aplicação
COPY --chown=laravel:laravel . .

# Configurar permissões
RUN chown -R laravel:laravel /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache

# Executar otimizações do Laravel
USER laravel
RUN php artisan config:cache && \
    php artisan route:cache || true

USER root

# Expor portas
EXPOSE 80 443

# Comando padrão
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]