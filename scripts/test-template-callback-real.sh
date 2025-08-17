#!/bin/bash

echo "=== TESTE: Callback Template com Conteúdo Real ==="
echo

# 1. Criar arquivo RTF de teste que o OnlyOffice retornaria
echo "1. Criando arquivo RTF de teste..."
mkdir -p /tmp/onlyoffice-test
cat > /tmp/onlyoffice-test/template-content.rtf << 'EOF'
{\rtf1\ansi\ansicpg65001\deff0 {\fonttbl {\f0 Arial;}}\f0\fs24
${imagem_cabecalho}\par
${cabecalho_nome_camara}\par
${cabecalho_endereco}\par
\par
\b OFÍCIO EDITADO PELO ADMIN\par
\b0\par
Este é um template editado pelo administrador com novas alterações.
\par
${texto}\par
\par
${assinatura_padrao}\par
${autor_nome}\par
}
EOF

echo "✅ Arquivo RTF criado em /tmp/onlyoffice-test/template-content.rtf"
echo "📄 Conteúdo:"
cat /tmp/onlyoffice-test/template-content.rtf
echo
echo

# 2. Copiar para container OnlyOffice para simular URL válida
echo "2. Copiando arquivo para container OnlyOffice..."
docker cp /tmp/onlyoffice-test/template-content.rtf legisinc-onlyoffice:/tmp/
echo "✅ Arquivo copiado para container OnlyOffice"
echo

# 3. Verificar template antes
echo "3. Template ID 12 ANTES:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, LENGTH(conteudo) as conteudo_length, conteudo IS NULL as conteudo_nulo FROM tipo_proposicao_templates WHERE id = 12;"
echo

# 4. Simular callback com URL interna do container
echo "4. Simulando callback com URL real do OnlyOffice..."
DOC_KEY=$(docker exec legisinc-postgres psql -U postgres -d legisinc -t -c "SELECT document_key FROM tipo_proposicao_templates WHERE id = 12;" | tr -d ' ')
echo "Document Key: $DOC_KEY"

# Criar servidor temporário para simular OnlyOffice
echo "5. Iniciando servidor temporário para simular OnlyOffice..."
python3 -m http.server 8090 --directory /tmp/onlyoffice-test &
SERVER_PID=$!
sleep 2

echo "6. Testando callback com conteúdo real..."
curl -X POST "http://localhost:8001/api/onlyoffice/callback/$DOC_KEY" \
     -H "Content-Type: application/json" \
     -d '{
       "status": 2,
       "url": "http://127.0.0.1:8090/template-content.rtf",
       "key": "'$DOC_KEY'"
     }' \
     -w "\nStatus HTTP: %{http_code}\n" \
     -s

# Parar servidor temporário
kill $SERVER_PID 2>/dev/null

echo
echo "7. Verificando template DEPOIS do callback:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, LENGTH(conteudo) as conteudo_length, conteudo IS NULL as conteudo_nulo, LEFT(conteudo, 100) as preview FROM tipo_proposicao_templates WHERE id = 12;"
echo

echo "=== RESULTADO ==="
echo "✅ Se conteudo_length > 0 = Callback salvou com sucesso!"
echo "❌ Se conteudo_nulo = t = Problema no download/salvamento"
echo

# Cleanup
rm -rf /tmp/onlyoffice-test