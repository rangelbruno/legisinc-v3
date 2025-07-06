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
    nginx

# Instalar extensões PHP
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

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
COPY --chown=laravel:laravel composer.json composer.lock ./

# Instalar dependências PHP
USER laravel
RUN composer install --no-dev --optimize-autoloader --no-scripts

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
    php artisan route:cache && \
    php artisan view:cache

USER root

# Expor portas
EXPOSE 80 443

# Comando padrão
CMD ["sh", "-c", "cp .env.docker .env 2>/dev/null || true && php artisan key:generate --ansi && nginx -g 'daemon off;' & php-fpm"]