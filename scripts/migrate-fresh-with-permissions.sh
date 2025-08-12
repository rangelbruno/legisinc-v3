#!/bin/bash

# Script wrapper para executar migrate:fresh --seed com correção automática de permissões
# Uso: ./scripts/migrate-fresh-with-permissions.sh

echo "🚀 Iniciando migrate:fresh --seed com correção de permissões..."

# Verificar se estamos em um container Docker
if [ -f /.dockerenv ]; then
    echo "🐳 Executando dentro do container Docker..."
    
    # Executar migrate:fresh --seed
    echo "📊 Executando migrate:fresh --seed..."
    php artisan migrate:fresh --seed
    
    # Verificar se o comando foi bem-sucedido
    if [ $? -eq 0 ]; then
        echo "✅ migrate:fresh --seed executado com sucesso!"
        
        # Executar correção de permissões
        echo "🔧 Corrigindo permissões..."
        php artisan fix:permissions
        
        if [ $? -eq 0 ]; then
            echo "🎉 Processo completo! Database resetada e permissões corrigidas."
        else
            echo "⚠️ Database resetada, mas houve problemas na correção de permissões."
        fi
    else
        echo "❌ Erro ao executar migrate:fresh --seed"
        exit 1
    fi
else
    echo "💻 Executando fora do container Docker..."
    echo "Use: docker exec -it legisinc-app bash -c './scripts/migrate-fresh-with-permissions.sh'"
    exit 1
fi