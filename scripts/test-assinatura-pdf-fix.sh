#!/bin/bash

echo "🔍 TESTE: Verificando se PDF de assinatura usa arquivo editado pelo Legislativo"
echo "=========================================================================="

# 1. Verificar proposições disponíveis
echo "📋 1. Verificando proposições disponíveis:"
PGPASSWORD=123456 psql -h localhost -U postgres -d legisinc -c "
SELECT id, tipo, ementa, status, arquivo_path IS NOT NULL as tem_arquivo, 
       LENGTH(conteudo) as conteudo_length 
FROM proposicoes 
WHERE id IN (1,2,3,4,5) 
ORDER BY id;
" 2>/dev/null || echo "❌ Erro ao conectar banco de dados"

echo ""
echo "📁 2. Verificando arquivos físicos salvos:"
for id in 1 2 3 4 5; do
    echo "=== Proposição $id ==="
    
    # Verificar possíveis localizações de arquivos
    for path in "storage/app/proposicoes" "storage/app/private/proposicoes" "storage/app/public/proposicoes"; do
        if [ -d "/home/bruno/legisinc/$path" ]; then
            echo "📂 Diretório $path:"
            find "/home/bruno/legisinc/$path" -name "*$id*" -type f 2>/dev/null | head -3
        fi
    done
    echo ""
done

echo "🧪 3. Testando acesso à rota de assinatura:"
echo "URL de teste: http://localhost:8001/proposicoes/1/assinar"

# Fazer request HTTP para testar
curl -s -o /dev/null -w "Status HTTP: %{http_code}\n" \
     -H "Cookie: laravel_session=test" \
     "http://localhost:8001/proposicoes/1/assinar" || echo "❌ Erro ao acessar rota"

echo ""
echo "📋 4. Verificando logs do Laravel (últimas 10 linhas):"
if [ -f "/home/bruno/legisinc/storage/logs/laravel.log" ]; then
    echo "=== LOG ENTRIES ==="
    tail -10 /home/bruno/legisinc/storage/logs/laravel.log | grep -E "(PDF Assinatura|Arquivo encontrado|ARQUIVO NÃO ENCONTRADO)" || echo "Nenhum log específico encontrado"
else
    echo "❌ Arquivo de log não encontrado"
fi

echo ""
echo "✅ Teste concluído!"
echo ""
echo "🔧 DIAGNÓSTICO:"
echo "- Se aparecer 'Arquivo encontrado': PDF usará conteúdo editado ✅"
echo "- Se aparecer 'ARQUIVO NÃO ENCONTRADO': PDF usará template padrão ⚠️"
echo "- Verificar se arquivos .docx/.rtf estão sendo salvos corretamente"