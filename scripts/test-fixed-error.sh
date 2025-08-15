#!/bin/bash

echo "=== Teste Após Correção do Erro Storage ==="
echo ""

echo "✅ Erro corrigido: Adicionado use Illuminate\Support\Facades\Storage;"
echo ""

echo "📋 Status das proposições:"
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, COALESCE(arquivo_path, 'sem arquivo') as status_arquivo FROM proposicoes ORDER BY id;"

echo ""
echo "🧪 TESTE FUNCIONAL:"
echo "1. Acesse: http://localhost:8001"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Vá em 'Minhas Proposições'"
echo "4. Teste com a Proposição ID 3 'Teste Limpo'"
echo "5. Clique em 'Continuar Edição no OnlyOffice'"
echo ""
echo "RESULTADO ESPERADO:"
echo "- ✅ Página carrega sem erro 500"
echo "- ✅ OnlyOffice abre normalmente"
echo "- ✅ Template aplicado sem duplicação"
echo ""

read -p "Pressione ENTER para monitorar logs em tempo real..."

echo "Monitorando logs... (Ctrl+C para parar)"
tail -f /home/bruno/legisinc/storage/logs/laravel.log | grep -v "vendor/" | grep -E "regeneração|arquivo.*salvo|ERROR|Exception"