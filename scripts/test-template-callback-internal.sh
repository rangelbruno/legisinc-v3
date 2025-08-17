#!/bin/bash

echo "=== TESTE: Callback Template com URL Interna ==="
echo

# 1. Criar conteúdo RTF e salvar no volume compartilhado do Laravel
echo "1. Criando conteúdo RTF no volume do Laravel..."
docker exec legisinc-app bash -c 'mkdir -p /var/www/html/storage/app/public/temp'
docker exec legisinc-app bash -c 'cat > /var/www/html/storage/app/public/temp/template-test.rtf << '\''EOF'\''
{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}\f0\fs24
${imagem_cabecalho}\par
${cabecalho_nome_camara}\par
\par
\b OFÍCIO EDITADO PELO ADMIN - TESTE INTERNO\par
\b0\par
Este é um template editado pelo administrador com alterações salvas via callback.
\par
${texto}\par
\par
${assinatura_padrao}\par
${autor_nome}\par
}
EOF'

echo "✅ Arquivo RTF criado no volume do Laravel"
echo

# 2. Verificar template antes
echo "2. Template ID 12 ANTES:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, LENGTH(conteudo) as conteudo_length, conteudo IS NULL as conteudo_nulo FROM tipo_proposicao_templates WHERE id = 12;"
echo

# 3. Simular callback com URL pública do Laravel
echo "3. Simulando callback com URL acessível pelo Laravel..."
DOC_KEY=$(docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT document_key FROM tipo_proposicao_templates WHERE id = 12;" | tr -d ' ')
echo "Document Key: $DOC_KEY"

echo "4. Testando callback com URL interna (via localhost:8001)..."
curl -X POST "http://localhost:8001/api/onlyoffice/callback/$DOC_KEY" \
     -H "Content-Type: application/json" \
     -d '{
       "status": 2,
       "url": "http://localhost:8001/storage/temp/template-test.rtf",
       "key": "'$DOC_KEY'"
     }' \
     -w "\nStatus HTTP: %{http_code}\n" \
     -s

echo
echo "5. Verificando template DEPOIS do callback:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, LENGTH(conteudo) as conteudo_length, conteudo IS NULL as conteudo_nulo, LEFT(conteudo, 200) as preview FROM tipo_proposicao_templates WHERE id = 12;"
echo

echo "=== RESULTADO ==="
echo "✅ Se conteudo_length > 0 = Callback salvou com sucesso!"
echo "❌ Se conteudo_nulo = t = Problema ainda persiste"
echo

# Cleanup
docker exec legisinc-app rm -f /var/www/html/storage/app/public/temp/template-test.rtf