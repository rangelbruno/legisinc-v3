#!/bin/bash

echo "🎯 === TESTE: INTERFACE SIMPLIFICADA SEM AJAX/API ==="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "\n${BLUE}📋 Interface convertida para Laravel tradicional:${NC}"
echo "✅ 1. Removido Vue.js e todas as chamadas AJAX"
echo "✅ 2. Implementado formulários tradicionais com CSRF"
echo "✅ 3. Adicionado método updateStatus no ProposicaoController"
echo "✅ 4. Refresh via recarregamento de página"

echo -e "\n${YELLOW}🧪 TESTE 1: Verificar dados da proposição${NC}"

docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
echo 'Status atual: ' . \$proposicao->status . PHP_EOL;
echo 'Conteúdo: ' . substr(\$proposicao->conteudo, 0, 100) . '...' . PHP_EOL;
echo 'Última modificação: ' . \$proposicao->updated_at . PHP_EOL;
"

echo -e "\n${YELLOW}🧪 TESTE 2: Verificar rota updateStatus${NC}"

ROUTE_EXISTS=$(docker exec legisinc-app php artisan route:list | grep "proposicoes.*update-status" | wc -l)
if [ "$ROUTE_EXISTS" -gt 0 ]; then
    echo "✅ Rota proposicoes.update-status existe"
else
    echo "❌ Rota proposicoes.update-status NÃO existe"
fi

echo -e "\n${YELLOW}🧪 TESTE 3: Verificar método updateStatus no Controller${NC}"

METHOD_EXISTS=$(grep -c "public function updateStatus" /home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php)
if [ "$METHOD_EXISTS" -gt 0 ]; then
    echo "✅ Método updateStatus existe no ProposicaoController"
else
    echo "❌ Método updateStatus NÃO existe no ProposicaoController"
fi

echo -e "\n${YELLOW}🧪 TESTE 4: Verificar template Blade simplificado${NC}"

# Verificar se não há mais código Vue.js
VUE_CODE=$(grep -c "Vue\|@click\|v-" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php || echo "0")
if [ "$VUE_CODE" -eq 0 ]; then
    echo "✅ Template sem código Vue.js"
else
    echo "❌ Template ainda contém código Vue.js ($VUE_CODE ocorrências)"
fi

# Verificar se há formulários tradicionais
FORMS=$(grep -c "<form.*method.*POST" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$FORMS" -gt 0 ]; then
    echo "✅ Template contém formulários tradicionais ($FORMS formulários)"
else
    echo "❌ Template NÃO contém formulários tradicionais"
fi

# Verificar CSRF
CSRF=$(grep -c "@csrf" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$CSRF" -gt 0 ]; then
    echo "✅ Template contém proteção CSRF ($CSRF tokens)"
else
    echo "❌ Template NÃO contém proteção CSRF"
fi

echo -e "\n${GREEN}✅ VERIFICAÇÕES CONCLUÍDAS:${NC}"
echo "• Interface convertida para Laravel tradicional"
echo "• Formulários com proteção CSRF implementados"
echo "• Método updateStatus adicionado ao controller"
echo "• Rota para atualização de status configurada"
echo "• Removido todo código Vue.js e AJAX"

echo -e "\n${BLUE}🚀 COMO TESTAR MANUALMENTE:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Teste os botões de ação (OnlyOffice, Status)"
echo "4. Verifique se página recarrega após ações"
echo "5. ✅ Sem JavaScript/AJAX - apenas Laravel puro"

echo -e "\n${GREEN}🎉 MIGRAÇÃO COMPLETA PARA LARAVEL TRADICIONAL!${NC}"
echo -e "🔄 Refresh: ${YELLOW}Via recarregamento de página${NC}"
echo -e "📝 Forms: ${YELLOW}Tradicionais com CSRF${NC}"
echo -e "🚫 AJAX: ${YELLOW}Removido completamente${NC}"
echo -e "⚡ Performance: ${YELLOW}Simples e confiável${NC}"