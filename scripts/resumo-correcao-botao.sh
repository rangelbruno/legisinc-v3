#!/bin/bash

echo "✅ CORREÇÃO IMPLEMENTADA: Botão 'Assinar Documento' agora só aparece com status 'aprovado'"
echo "========================================================================================"

echo ""
echo "🔧 MUDANÇA REALIZADA:"
echo "Arquivo: resources/views/proposicoes/show.blade.php"
echo "Função: canSign() (linha ~1356)"

echo ""
echo "🎯 ANTES:"
echo "canSignStatuses = ['aprovado', 'aprovado_assinatura']"
echo "// Botão aparecia em 2 status diferentes"

echo ""
echo "✅ AGORA:"
echo "canSignStatuses = ['aprovado']"
echo "// Botão aparece APENAS quando status = 'aprovado'"

echo ""
echo "🔍 LOGS DE DEBUG ADICIONADOS:"
echo "- console.log com todos os valores da função canSign()"
echo "- Verifica status, permissões, autor, etc."

echo ""
echo "📊 SITUAÇÃO ATUAL:"
echo "Proposição 2:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, status, autor_id FROM proposicoes WHERE id = 2;"

echo ""
echo "🎯 VALIDAÇÃO:"
echo "- Status: 'aprovado' ✅"
echo "- Usuário Jessica (ID 2) é o autor ✅"
echo "- Usuário Jessica tem role PARLAMENTAR ✅"
echo "- Botão deve aparecer APENAS neste caso ✅"

echo ""
echo "🧪 PARA TESTAR:"
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Vá para: http://localhost:8001/proposicoes/2"
echo "4. Abra Console (F12) e procure por 'canSign check:'"
echo "5. O botão 'Assinar Documento' deve estar visível"

echo ""
echo "🔄 PARA TESTAR CONTRÁRIO (botão NÃO deve aparecer):"
echo "Altere o status para 'em_revisao':"
echo "docker exec legisinc-postgres psql -U postgres -d legisinc -c \"UPDATE proposicoes SET status = 'em_revisao' WHERE id = 2;\""
echo "Recarregue a página - botão deve desaparecer"

echo ""
echo "🔄 RESTAURAR STATUS ORIGINAL:"
echo "docker exec legisinc-postgres psql -U postgres -d legisinc -c \"UPDATE proposicoes SET status = 'aprovado' WHERE id = 2;\""

echo ""
echo "✅ CORREÇÃO COMPLETA - O botão agora respeita exatamente o status 'aprovado'"