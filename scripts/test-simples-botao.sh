#!/bin/bash

echo "🔧 Teste simples: Acessar proposição 2 diretamente via navegador"
echo "================================================================="

echo "📊 Status da proposição 2:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, status, autor_id FROM proposicoes WHERE id = 2;"

echo ""
echo "👥 Autor da proposição (ID 2 = Jessica):"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, name, email FROM users WHERE id = 2;"

echo ""
echo "🔍 Para testar manualmente:"
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: jessica@sistema.gov.br"  
echo "3. Senha: 123456"
echo "4. Depois acesse: http://localhost:8001/proposicoes/2"
echo ""
echo "✅ O botão 'Assinar Documento' deve aparecer APENAS se:"
echo "   - Status = 'aprovado' (✅ atual: aprovado)"
echo "   - Usuário = Parlamentar (✅ Jessica é PARLAMENTAR)"
echo "   - É o autor (✅ autor_id = 2 = Jessica)"
echo ""
echo "🎯 Se o botão NÃO aparecer, verifique no console do navegador (F12):"
echo "   - Procure por 'canSign check:' nos logs"
echo "   - Verifique se há erros JavaScript"

# Verificar se o servidor está rodando
if curl -s http://localhost:8001 > /dev/null; then
    echo ""
    echo "✅ Servidor Laravel rodando em http://localhost:8001"
else
    echo ""
    echo "❌ Servidor Laravel NÃO está respondendo"
    echo "Execute: ./vendor/bin/sail up -d"
fi

echo ""
echo "🚀 Link direto: http://localhost:8001/proposicoes/2"