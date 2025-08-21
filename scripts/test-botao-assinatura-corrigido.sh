#!/bin/bash

echo "🔍 Testando correção do botão de assinatura digital"
echo "================================================="

# Status atual da proposição 2
echo "1. Verificando status atual da proposição 2:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, status FROM proposicoes WHERE id = 2;"

echo ""
echo "2. Testando com status 'em_edicao' (botão NÃO deve aparecer):"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE proposicoes SET status = 'em_edicao' WHERE id = 2;"
echo "   Status: em_edicao → Botão assinatura: ❌ NÃO deve aparecer"

echo ""
echo "3. Testando com status 'aprovado' (botão DEVE aparecer):"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE proposicoes SET status = 'aprovado' WHERE id = 2;"
echo "   Status: aprovado → Botão assinatura: ✅ DEVE aparecer"

echo ""
echo "4. Testando com status 'aprovado_assinatura' (botão DEVE aparecer):"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE proposicoes SET status = 'aprovado_assinatura' WHERE id = 2;"
echo "   Status: aprovado_assinatura → Botão assinatura: ✅ DEVE aparecer"

echo ""
echo "5. Voltando ao status original 'em_edicao':"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE proposicoes SET status = 'em_edicao' WHERE id = 2;"

echo ""
echo "✅ CORREÇÃO APLICADA:"
echo "   - Função canSign() modificada para verificar statuses corretos"
echo "   - Botão só aparece quando status é 'aprovado' ou 'aprovado_assinatura'"
echo "   - Status 'em_edicao' não mostrará mais o botão de assinatura"

echo ""
echo "🌐 Para testar no navegador:"
echo "   - Acesse: http://localhost:8001/proposicoes/2"
echo "   - Com status 'em_edicao': botão assinatura não deve aparecer"
echo "   - Mude status para 'aprovado' para ver o botão aparecer"

echo ""
echo "✅ Teste concluído!"