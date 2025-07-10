#!/bin/bash

# Script de inicialização do container Laravel

set -e

echo "🚀 Iniciando aplicação Laravel..."

# Criar diretórios de log se não existirem
mkdir -p /var/log/php /var/log/nginx /var/log/supervisor /var/log/laravel

# Navegar para o diretório da aplicação
cd /var/www/html

# Verificar se o arquivo .env existe
if [ ! -f .env ]; then
    echo "📋 Criando arquivo .env..."
    cp .env.docker .env
fi

# Gerar chave da aplicação se não existir
if grep -q "APP_KEY=$" .env; then
    echo "🔑 Gerando chave da aplicação..."
    php artisan key:generate --ansi
fi

# Limpar e cachear configurações
echo "⚡ Otimizando aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar permissões
echo "🔒 Configurando permissões..."
chown -R laravel:laravel /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Criar link simbólico para storage público
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Criando link simbólico para storage..."
    php artisan storage:link
fi

echo "🎉 Aplicação Laravel iniciada com sucesso!"
echo "📍 Acesse: http://localhost:8001"

# Iniciar nginx e php-fpm via supervisor
supervisorctl -c /etc/supervisor/conf.d/supervisord.conf start nginx
supervisorctl -c /etc/supervisor/conf.d/supervisord.conf start php-fpm

echo "✅ Serviços iniciados"