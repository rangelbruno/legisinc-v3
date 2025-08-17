#!/bin/bash

echo "=== TESTE: Callback Template Admin ==="
echo

# 1. Verificar template ID 12 antes
echo "1. Template ID 12 ANTES da edição:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, document_key, LENGTH(conteudo) as conteudo_length, conteudo IS NULL as conteudo_nulo, updated_at FROM tipo_proposicao_templates WHERE id = 12;"
echo

# 2. Simular que o admin está editando (atualizar document_key)
echo "2. Simulando início de edição pelo admin (atualizando document_key):"
NEW_DOC_KEY="template_oficio_test_$(date +%s)_admin"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE tipo_proposicao_templates SET document_key = '$NEW_DOC_KEY', updated_at = NOW() WHERE id = 12; SELECT id, document_key FROM tipo_proposicao_templates WHERE id = 12;"
echo

# 3. Simular callback do OnlyOffice (usando curl para testar a rota)
echo "3. Testando se a rota de callback existe:"
echo "URL que deveria receber callback: http://localhost:8001/api/onlyoffice/callback/$NEW_DOC_KEY"
echo

echo "4. Testando callback manual (POST):"
curl -X POST "http://localhost:8001/api/onlyoffice/callback/$NEW_DOC_KEY" \
     -H "Content-Type: application/json" \
     -d '{
       "status": 2,
       "url": "http://legisinc-onlyoffice/cache/files/test-content.rtf",
       "key": "'$NEW_DOC_KEY'"
     }' \
     -w "\nStatus HTTP: %{http_code}\n" \
     -s || echo "ERRO: Não foi possível conectar"

echo
echo "5. Verificar se template foi atualizado após callback:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, document_key, LENGTH(conteudo) as conteudo_length, conteudo IS NULL as conteudo_nulo, updated_at FROM tipo_proposicao_templates WHERE id = 12;"
echo

echo "=== DIAGNÓSTICO ==="
echo "✅ Se 'conteudo_length' aumentou = Callback funcionando"
echo "❌ Se 'conteudo_nulo' = t = Callback NÃO funcionando"
echo
echo "📋 Para testar completamente:"
echo "1. Acesse: http://localhost:8001/admin/templates/12/editor"
echo "2. Faça alterações e salve"
echo "3. Execute este script novamente para verificar se callback funcionou"