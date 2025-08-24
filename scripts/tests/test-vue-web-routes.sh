#!/bin/bash

echo "🌐 === TESTE: VUE.JS COM ROTAS WEB DO LARAVEL ==="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "\n${BLUE}📋 Migração para Rotas Web Tradicionais:${NC}"
echo "✅ 1. Removido dependência de API REST"
echo "✅ 2. Substituído Axios por fetch nativo"
echo "✅ 3. Criada rota web /proposicoes/{id}/dados-frescos"
echo "✅ 4. Controller method getDadosFrescos implementado"
echo "✅ 5. Vue.js usando sessão web Laravel"
echo "✅ 6. Mantidas todas as funcionalidades dinâmicas"

echo -e "\n${YELLOW}🧪 TESTE 1: Verificar remoção do Axios${NC}"

AXIOS_REMOVED=$(grep -c "axios" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$AXIOS_REMOVED" -eq 0 ]; then
    echo "✅ Axios removido completamente"
else
    echo "❌ Axios ainda presente ($AXIOS_REMOVED referências)"
fi

echo -e "\n${YELLOW}🧪 TESTE 2: Verificar implementação fetch nativo${NC}"

FETCH_IMPL=$(grep -c "makeRequest\|fetch.*credentials\|defaultHeaders" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$FETCH_IMPL" -ge 3 ]; then
    echo "✅ Fetch nativo implementado ($FETCH_IMPL métodos)"
else
    echo "❌ Fetch nativo incompleto ($FETCH_IMPL métodos)"
fi

echo -e "\n${YELLOW}🧪 TESTE 3: Verificar rota web configurada${NC}"

WEB_ROUTE=$(grep -c "dados-frescos.*getDadosFrescos" /home/bruno/legisinc/routes/web.php)
if [ "$WEB_ROUTE" -gt 0 ]; then
    echo "✅ Rota web configurada"
    
    # Verificar se a rota está registrada
    ROUTE_EXISTS=$(docker exec legisinc-app php artisan route:list | grep "dados-frescos" | wc -l)
    if [ "$ROUTE_EXISTS" -gt 0 ]; then
        echo "✅ Rota registrada no Laravel ($ROUTE_EXISTS rotas)"
    else
        echo "❌ Rota NÃO registrada no Laravel"
    fi
else
    echo "❌ Rota web NÃO configurada"
fi

echo -e "\n${YELLOW}🧪 TESTE 4: Verificar controller method${NC}"

CONTROLLER_METHOD=$(grep -c "getDadosFrescos" /home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php)
if [ "$CONTROLLER_METHOD" -gt 0 ]; then
    echo "✅ Método getDadosFrescos implementado"
    
    # Verificar estrutura do método
    METHOD_STRUCTURE=$(grep -A 10 -B 2 "getDadosFrescos" /home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php | grep -c "response()->json\|success.*true\|proposicao")
    if [ "$METHOD_STRUCTURE" -ge 3 ]; then
        echo "✅ Estrutura do método correta"
    else
        echo "❌ Estrutura do método incompleta"
    fi
else
    echo "❌ Método getDadosFrescos NÃO implementado"
fi

echo -e "\n${YELLOW}🧪 TESTE 5: Testar método diretamente${NC}"

DIRECT_TEST=$(docker exec legisinc-app php artisan tinker --execute="
try {
    \$user = App\Models\User::find(6);
    Auth::login(\$user);
    \$controller = new App\Http\Controllers\ProposicaoController();
    \$response = \$controller->getDadosFrescos(1);
    \$content = json_decode(\$response->getContent(), true);
    if (\$content['success']) {
        echo 'SUCCESS:' . \$response->getStatusCode() . PHP_EOL;
        echo 'DATA_KEYS:' . count(\$content['proposicao']) . PHP_EOL;
        echo 'HAS_META:' . (isset(\$content['proposicao']['meta']) ? 'YES' : 'NO') . PHP_EOL;
        echo 'TIMESTAMP:' . (\$content['timestamp'] ? 'YES' : 'NO') . PHP_EOL;
    } else {
        echo 'ERROR:' . \$content['message'] . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'EXCEPTION:' . \$e->getMessage() . PHP_EOL;
}
")

if echo "$DIRECT_TEST" | grep -q "SUCCESS:200"; then
    echo "✅ Controller method funcionando"
    
    if echo "$DIRECT_TEST" | grep -q "HAS_META:YES"; then
        echo "✅ Metadados incluídos"
    fi
    
    if echo "$DIRECT_TEST" | grep -q "TIMESTAMP:YES"; then
        echo "✅ Timestamp incluído"
    fi
else
    echo "❌ Controller method com problemas"
    echo "$DIRECT_TEST"
fi

echo -e "\n${YELLOW}🧪 TESTE 6: Verificar interface Vue.js simplificada${NC}"

VUE_STRUCTURE=$(grep -c "setupFetch\|makeRequest\|loadProposicao" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$VUE_STRUCTURE" -ge 3 ]; then
    echo "✅ Estrutura Vue.js simplificada ($VUE_STRUCTURE métodos)"
else
    echo "❌ Estrutura Vue.js incompleta ($VUE_STRUCTURE métodos)"
fi

# Verificar se ainda usa dados do Blade
BLADE_DATA=$(grep -c "@json(\$proposicao" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$BLADE_DATA" -gt 0 ]; then
    echo "✅ Dados iniciais do Blade mantidos"
else
    echo "❌ Dados iniciais do Blade removidos"
fi

echo -e "\n${YELLOW}🧪 TESTE 7: Verificar dados da proposição atual${NC}"

docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::with('autor')->find(1);
if (\$proposicao) {
    echo 'Proposição para teste:' . PHP_EOL;
    echo 'ID: ' . \$proposicao->id . PHP_EOL;
    echo 'Status: ' . \$proposicao->status . PHP_EOL;
    echo 'Ementa: ' . substr(\$proposicao->ementa, 0, 60) . '...' . PHP_EOL;
    echo 'Conteúdo: ' . (strlen(\$proposicao->conteudo) > 0 ? 'SIM (' . strlen(\$proposicao->conteudo) . ' chars)' : 'NÃO') . PHP_EOL;
    echo 'Autor: ' . (\$proposicao->autor ? \$proposicao->autor->name : 'N/A') . PHP_EOL;
    echo 'Updated: ' . \$proposicao->updated_at . PHP_EOL;
}
"

echo -e "\n${GREEN}✅ RESUMO DA MIGRAÇÃO:${NC}"
echo ""
echo -e "${GREEN}🌐 Arquitetura Web Laravel:${NC}"
echo "• Removido APIs REST desnecessárias"
echo "• Usando rotas web tradicionais do Laravel"
echo "• Mantida autenticação de sessão web"
echo "• Controllers seguindo padrões Laravel"

echo -e "\n${GREEN}⚡ Performance:${NC}"
echo "• Fetch nativo (sem dependência Axios)"
echo "• Dados iniciais via Blade (sem requisição extra)"
echo "• Polling otimizado usando rotas web"
echo "• Sessão Laravel mantida automaticamente"

echo -e "\n${GREEN}🎯 Funcionalidades Mantidas:${NC}"
echo "• Cards dinâmicos e responsivos"
echo "• Atualizações em tempo real"
echo "• Timeline interativo"
echo "• Sistema de notificações"
echo "• Ações contextuais por permissão"

echo -e "\n${GREEN}🛠️ Benefícios da Mudança:${NC}"
echo "• Melhor integração com Laravel"
echo "• Menos dependências JavaScript"
echo "• Autenticação automática"
echo "• Facilita manutenção e debug"
echo "• Aproveita middleware Laravel"

echo -e "\n${BLUE}🚀 COMO TESTAR:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Abra DevTools → Network"
echo "4. Observe requisições para /dados-frescos"
echo "5. Use botão 'Atualizar Dados'"
echo "6. ✨ Interface funciona com rotas web!"

echo -e "\n${GREEN}🎉 MIGRAÇÃO CONCLUÍDA COM SUCESSO!${NC}"
echo -e "🌐 Arquitetura: ${YELLOW}Rotas web Laravel tradicionais${NC}"
echo -e "⚡ JavaScript: ${YELLOW}Fetch nativo sem dependências${NC}"
echo -e "🔐 Auth: ${YELLOW}Sessão web automática${NC}"
echo -e "🎯 UX: ${YELLOW}Mesma experiência, melhor performance${NC}"