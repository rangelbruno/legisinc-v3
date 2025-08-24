#!/bin/bash
# Script para corrigir permissões do cache

echo "Corrigindo permissões do diretório de cache..."

# Limpar cache antigo
php artisan cache:clear
php artisan config:clear

# Criar estrutura de diretórios se não existir
mkdir -p storage/framework/cache/data

# Ajustar permissões
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Garantir que o grupo www-data pode escrever
chgrp -R www-data storage
chgrp -R www-data bootstrap/cache

echo "Permissões corrigidas!"
echo "Agora tente novamente carregar as permissões no admin."