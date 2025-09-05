#!/bin/bash

echo "🔧 Corrigindo permissões do Laravel..."

# Criar diretórios necessários
mkdir -p storage/logs
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/app/public

# Criar arquivo de log se não existir
touch storage/logs/laravel.log

# Definir permissões corretas
chown -R www-data:www-data storage/
chmod -R 775 storage/

# Permissões específicas para logs
chmod 664 storage/logs/laravel.log

echo "✅ Permissões corrigidas com sucesso!"