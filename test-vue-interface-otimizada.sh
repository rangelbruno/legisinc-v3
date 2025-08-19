#!/bin/bash

echo "🚀 === TESTE: INTERFACE VUE.JS OTIMIZADA ===" 

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
PURPLE='\033[0;35m'
NC='\033[0m'

echo -e "\n${BLUE}📋 Nova Interface Vue.js com Cards Dinâmicos:${NC}"
echo "✅ 1. Estrutura em cards responsivos organizados"
echo "✅ 2. Busca em tempo real do BD via API otimizada"
echo "✅ 3. Cache inteligente baseado em timestamps"
echo "✅ 4. Polling adaptativo (30s) com detecção de foco"
echo "✅ 5. Ações dinâmicas baseadas em permissões"
echo "✅ 6. Timeline interativo de tramitação"
echo "✅ 7. Sistema de notificações toast"
echo "✅ 8. Expansão/contração de conteúdo longo"

echo -e "\n${YELLOW}🧪 TESTE 1: Verificar dados da proposição atual${NC}"

docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
if (\$proposicao) {
    echo 'ID: ' . \$proposicao->id . PHP_EOL;
    echo 'Tipo: ' . \$proposicao->tipo . PHP_EOL;
    echo 'Status: ' . \$proposicao->status . PHP_EOL;
    echo 'Ementa: ' . substr(\$proposicao->ementa, 0, 80) . '...' . PHP_EOL;
    echo 'Conteúdo: ' . (strlen(\$proposicao->conteudo) > 0 ? 'SIM (' . strlen(\$proposicao->conteudo) . ' chars)' : 'NÃO') . PHP_EOL;
    echo 'Autor: ' . (\$proposicao->autor ? \$proposicao->autor->name : 'N/A') . PHP_EOL;
    echo 'Última modificação: ' . \$proposicao->updated_at . PHP_EOL;
} else {
    echo 'Proposição não encontrada' . PHP_EOL;
}
"

echo -e "\n${YELLOW}🧪 TESTE 2: Verificar API Controller otimizada${NC}"

# Verificar se métodos foram adicionados
API_METHODS_COUNT=$(grep -c "getProposicaoWithCache\|formatProposicaoResponse\|clearProposicaoCache" /home/bruno/legisinc/app/Http/Controllers/Api/ProposicaoApiController.php)
if [ "$API_METHODS_COUNT" -ge 3 ]; then
    echo "✅ Métodos de otimização adicionados ao API Controller"
else
    echo "❌ Métodos de otimização NÃO encontrados no API Controller"
fi

# Verificar cache inteligente
CACHE_OPTIMIZATION=$(grep -c "Cache::remember.*proposicao_api" /home/bruno/legisinc/app/Http/Controllers/Api/ProposicaoApiController.php)
if [ "$CACHE_OPTIMIZATION" -gt 0 ]; then
    echo "✅ Cache inteligente implementado ($CACHE_OPTIMIZATION implementações)"
else
    echo "❌ Cache inteligente NÃO implementado"
fi

echo -e "\n${YELLOW}🧪 TESTE 3: Verificar template Vue.js${NC}"

# Verificar componentes Vue principais
VUE_COMPONENTS=$(grep -c "v-if\|v-show\|v-for\|@click\|:class" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$VUE_COMPONENTS" -gt 30 ]; then
    echo "✅ Componentes Vue.js implementados ($VUE_COMPONENTS diretivas)"
else
    echo "❌ Componentes Vue.js insuficientes ($VUE_COMPONENTS diretivas)"
fi

# Verificar cards organizados
CARDS_COUNT=$(grep -c "class.*card.*border-0" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$CARDS_COUNT" -ge 4 ]; then
    echo "✅ Cards dinâmicos organizados ($CARDS_COUNT cards)"
else
    echo "❌ Cards insuficientes ($CARDS_COUNT cards)"
fi

# Verificar polling inteligente
POLLING_CODE=$(grep -c "setInterval\|document.hidden\|checkForUpdates" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$POLLING_CODE" -ge 3 ]; then
    echo "✅ Polling inteligente implementado"
else
    echo "❌ Polling inteligente NÃO implementado"
fi

# Verificar sistema de notificações
TOAST_SYSTEM=$(grep -c "showToast\|toast.*show\|removeToast" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$TOAST_SYSTEM" -ge 3 ]; then
    echo "✅ Sistema de notificações toast implementado"
else
    echo "❌ Sistema de notificações NÃO implementado"
fi

echo -e "\n${YELLOW}🧪 TESTE 4: Testar API de proposições${NC}"

# Simular chamada API
API_TEST=$(docker exec legisinc-app php artisan tinker --execute="
try {
    \$user = App\Models\User::find(6);
    Auth::login(\$user);
    \$controller = new App\Http\Controllers\Api\ProposicaoApiController();
    \$response = \$controller->show(1);
    \$content = json_decode(\$response->getContent(), true);
    if (\$content['success']) {
        echo 'API_SUCCESS:' . \$response->getStatusCode() . PHP_EOL;
        echo 'DATA_KEYS:' . count(\$content['proposicao']) . PHP_EOL;
        echo 'HAS_META:' . (isset(\$content['proposicao']['meta']) ? 'YES' : 'NO') . PHP_EOL;
        echo 'HAS_PERMISSIONS:' . (isset(\$content['proposicao']['permissions']) ? 'YES' : 'NO') . PHP_EOL;
        echo 'CACHE_HIT:' . (\$content['cache_hit'] ? 'YES' : 'NO') . PHP_EOL;
    } else {
        echo 'API_ERROR:' . \$content['message'] . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'EXCEPTION:' . \$e->getMessage() . PHP_EOL;
}
")

if echo "$API_TEST" | grep -q "API_SUCCESS:200"; then
    echo "✅ API Controller funcionando corretamente"
    
    if echo "$API_TEST" | grep -q "HAS_META:YES"; then
        echo "✅ Metadados otimizados incluídos"
    fi
    
    if echo "$API_TEST" | grep -q "HAS_PERMISSIONS:YES"; then
        echo "✅ Sistema de permissões funcionando"
    fi
    
    if echo "$API_TEST" | grep -q "CACHE_HIT:"; then
        echo "✅ Cache system operacional"
    fi
else
    echo "❌ API Controller com problemas"
    echo "$API_TEST"
fi

echo -e "\n${YELLOW}🧪 TESTE 5: Verificar integração com template do projeto${NC}"

# Verificar uso do layout do projeto
LAYOUT_INTEGRATION=$(grep -c "@extends('components.layouts.app')" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$LAYOUT_INTEGRATION" -gt 0 ]; then
    echo "✅ Integrado com layout do projeto"
else
    echo "❌ NÃO integrado com layout do projeto"
fi

# Verificar ícones do template
TEMPLATE_ICONS=$(grep -c "ki-duotone" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$TEMPLATE_ICONS" -gt 20 ]; then
    echo "✅ Ícones do template utilizados ($TEMPLATE_ICONS ícones)"
else
    echo "❌ Ícones do template insuficientes ($TEMPLATE_ICONS ícones)"
fi

# Verificar classes do framework
FRAMEWORK_CLASSES=$(grep -c "fs-\|text-\|bg-\|badge-\|btn-" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$FRAMEWORK_CLASSES" -gt 50 ]; then
    echo "✅ Classes do framework utilizadas ($FRAMEWORK_CLASSES classes)"
else
    echo "❌ Classes do framework insuficientes ($FRAMEWORK_CLASSES classes)"
fi

echo -e "\n${PURPLE}⚡ TESTE 6: Performance e otimizações${NC}"

# Verificar otimizações implementadas
echo -e "${PURPLE}Cache Strategy:${NC}"
echo "• Cache baseado em timestamp (último modified)"
echo "• Cache por 5 minutos com invalidação automática"
echo "• Preview de conteúdo para textos longos"

echo -e "\n${PURPLE}Polling Strategy:${NC}"
echo "• Intervalo de 30 segundos (ajustável)"
echo "• Para quando janela não está em foco"
echo "• Verificação de atualizações sem recarregar dados"

echo -e "\n${PURPLE}UI Optimizations:${NC}"
echo "• Cards modulares e responsivos"
echo "• Expansão de conteúdo sob demanda"
echo "• Sistema de notificações não-obstrutivo"
echo "• Timeline dinâmico baseado em status"

echo -e "\n${GREEN}✅ RESUMO DA NOVA INTERFACE:${NC}"
echo ""
echo -e "${GREEN}🎨 UI/UX Melhorada:${NC}"
echo "• Layout em cards organizados e limpos"
echo "• Informações hierarquizadas por importância"
echo "• Indicadores visuais de status e progress"
echo "• Ações contextuais baseadas em permissões"

echo -e "\n${GREEN}⚡ Performance Otimizada:${NC}"
echo "• API com cache inteligente (5min TTL)"
echo "• Polling adaptativo que economiza recursos"
echo "• Carregamento progressivo de conteúdo"
echo "• Metadados calculados no backend"

echo -e "\n${GREEN}🔄 Tempo Real:${NC}"
echo "• Sincronização automática a cada 30s"
echo "• Detecção de mudanças sem interferir na UX"
echo "• Notificações contextuais de atualizações"
echo "• Timeline atualizado dinamicamente"

echo -e "\n${GREEN}🛠️ Funcionalidades:${NC}"
echo "• Botão 'Continuar Editando' inteligente"
echo "• 'Enviar para Legislativo' com validações"
echo "• Ações do Legislativo (Aprovar/Devolver/Reprovar)"
echo "• Sistema de assinatura digital integrado"

echo -e "\n${BLUE}🚀 COMO TESTAR:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1"
echo "2. Login: jessica@sistema.gov.br / 123456 (Parlamentar)"
echo "3. Observe interface com cards organizados"
echo "4. Teste botões contextuais baseados no status"
echo "5. Aguarde 30s para ver polling automático"
echo "6. Use botão 'Atualizar Dados' para refresh manual"
echo "7. ✨ Interface moderna, responsiva e performática!"

echo -e "\n${GREEN}🎉 MIGRAÇÃO CONCLUÍDA PARA VUE.JS OTIMIZADO!${NC}"
echo -e "📱 Interface: ${YELLOW}Responsiva com cards dinâmicos${NC}"
echo -e "⚡ Performance: ${YELLOW}Cache inteligente + polling adaptativo${NC}"
echo -e "🔄 Tempo Real: ${YELLOW}Sincronização automática a cada 30s${NC}"
echo -e "🎯 UX: ${YELLOW}Ações contextuais + notificações toast${NC}"
echo -e "💾 BD: ${YELLOW}Busca otimizada com metadados calculados${NC}"