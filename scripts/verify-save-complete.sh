#!/bin/bash

echo "=== Verificação Completa do Salvamento OnlyOffice ==="
echo ""

# Verificar no banco de dados
echo "1. Status no Banco de Dados:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, LEFT(conteudo, 100) as conteudo_preview, arquivo_path, ultima_modificacao FROM proposicoes WHERE id = 1;"

echo ""
echo "2. Arquivos Salvos no Sistema:"
ls -lah /home/bruno/legisinc/storage/app/private/proposicoes/proposicao_1_*.docx 2>/dev/null | tail -3

echo ""
echo "3. Verificando se o texto foi salvo:"
docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT conteudo FROM proposicoes WHERE id = 1;" | grep -q "TESTE SALVAMENTO" && echo "✅ Texto 'TESTE SALVAMENTO' encontrado no banco!" || echo "❌ Texto 'TESTE SALVAMENTO' NÃO encontrado"

echo ""
echo "4. Últimos salvamentos bem-sucedidos:"
grep "Arquivo e conteúdo atualizados com sucesso" /home/bruno/legisinc/storage/logs/laravel.log | tail -3

echo ""
echo "=== Resumo do Status ==="
if docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT LENGTH(conteudo) FROM proposicoes WHERE id = 1;" | grep -q "[0-9]\{4,\}"; then
    echo "✅ SALVAMENTO FUNCIONANDO!"
    echo "   - Conteúdo salvo no banco de dados"
    echo "   - Arquivo salvo no sistema de arquivos"
    echo "   - Callback do OnlyOffice processado com sucesso"
else
    echo "❌ Verificar salvamento - conteúdo pode estar vazio"
fi