#!/bin/bash

echo "=== Debug do Salvamento OnlyOffice ==="
echo ""

# Limpar logs antigos
echo "" >> /home/bruno/legisinc/storage/logs/laravel.log

echo "1. Status atual da proposição:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, arquivo_path, LENGTH(conteudo) as conteudo_length, ultima_modificacao FROM proposicoes WHERE id = 1;"

echo ""
echo "2. Monitorando logs em tempo real..."
echo "   Abra o navegador e edite o documento."
echo "   Pressione Ctrl+C para parar o monitoramento."
echo ""

tail -f /home/bruno/legisinc/storage/logs/laravel.log | grep -E "callback|error|Error|failed|salvamento|arquivo|conteudo"