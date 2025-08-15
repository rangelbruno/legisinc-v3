#!/bin/bash

echo "=== Teste de Não-Duplicação do OnlyOffice ==="
echo ""

# Status atual
echo "1. Status atual da proposição:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, arquivo_path, LENGTH(conteudo) as conteudo_length FROM proposicoes WHERE id = 1;"

echo ""
echo "2. Verificando arquivo salvo:"
ls -lah /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_1_1755269883.docx 2>/dev/null || echo "Arquivo não encontrado"

echo ""
echo "3. Conteúdo atual (primeiros 200 caracteres):"
docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT LEFT(conteudo, 200) FROM proposicoes WHERE id = 1;"

echo ""
echo "=== TESTE PRÁTICO ==="
echo ""
echo "1. Abra: http://localhost:8001"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Abra a proposição no OnlyOffice"
echo "4. Verifique se NÃO há duplicação do cabeçalho"
echo "5. Faça uma pequena alteração e salve"
echo "6. Feche e reabra - deve carregar SEM duplicar conteúdo"
echo ""
echo "Monitorando logs (Ctrl+C para parar)..."

tail -f /home/bruno/legisinc/storage/logs/laravel.log | grep -E "regeneração|arquivo.*salvo|tem_arquivo_salvo"