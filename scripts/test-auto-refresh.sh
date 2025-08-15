#!/bin/bash

echo "=== Teste de Auto-Refresh do OnlyOffice ==="
echo ""

echo "✅ MELHORIAS IMPLEMENTADAS:"
echo "1. Document key baseado em timestamp de modificação"
echo "2. Auto-refresh JavaScript a cada 5 segundos"
echo "3. Notificação quando documento é atualizado"
echo "4. Endpoint de status para verificar mudanças"
echo ""

echo "📋 Status atual das proposições:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, arquivo_path IS NOT NULL as tem_arquivo, ultima_modificacao FROM proposicoes ORDER BY id;"

echo ""
echo "🧪 COMO TESTAR O AUTO-REFRESH:"
echo ""
echo "1. Abra: http://localhost:8001"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Abra uma proposição no OnlyOffice"
echo "4. Faça uma alteração no documento"
echo "5. Salve (Ctrl+S ou botão Salvar)"
echo "6. ✅ RESULTADO ESPERADO:"
echo "   - Notificação de 'Documento Atualizado' aparece automaticamente"
echo "   - Status muda para 'Salvo' (badge verde)"
echo "   - NÃO precisa mais pressionar Ctrl+F5"
echo ""

echo "🔍 Teste do endpoint de status:"
echo "GET /proposicoes/1/onlyoffice/status"
curl -s http://localhost:8001/proposicoes/1/onlyoffice/status | jq '.' 2>/dev/null || echo "JSON response (sem jq formatting)"

echo ""
echo "📊 MONITORAMENTO DE LOGS:"
echo "Execute o comando abaixo para ver logs em tempo real:"
echo "tail -f storage/logs/laravel.log | grep -E 'callback.*status.*2|Arquivo.*atualizado|OnlyOffice.*Config'"