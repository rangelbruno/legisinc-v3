#!/bin/bash

# Script para corrigir permissões do Laravel após migrate:fresh --seed
# Este script deve ser executado dentro do container Docker

echo "🔧 Corrigindo permissões do Laravel..."

# Garantir que o diretório de cache existe
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public

# Corrigir ownership para o usuário do PHP-FPM (laravel)
echo "📁 Corrigindo ownership dos diretórios de storage..."
chown -R laravel:laravel storage/
chown -R laravel:laravel bootstrap/cache/

# Definir permissões corretas
echo "🔐 Definindo permissões corretas..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Permissões especiais para cache e logs (escrita necessária)
chmod 775 storage/framework/cache/data/
chmod -R 775 storage/logs/
chmod -R 775 storage/framework/sessions/
chmod -R 775 storage/framework/views/
chmod -R 775 storage/app/

echo "✅ Permissões corrigidas com sucesso!"

# Limpar caches para garantir que não há arquivos corrompidos
echo "🧹 Limpando caches..."
php artisan cache:clear --quiet
php artisan config:cache --quiet
php artisan route:cache --quiet
php artisan view:cache --quiet

echo "🎉 Processo de correção de permissões concluído!"