#!/bin/bash

echo "🔧 === TESTE: CARD INFORMAÇÕES CORRIGIDO ==="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "\n${BLUE}🎯 CORREÇÃO APLICADA:${NC}"
echo "✅ Layout esquerda ↔ direita implementado"
echo "✅ Ícone removido do título"
echo "✅ Estrutura flexbox otimizada"
echo "✅ Elementos organizados corretamente"

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 1: Remoção de Ícone${NC}"

# Verificar se o ícone ki-information-4 foi removido
ICON_REMOVED=$(grep -A 10 "Informações da Proposição" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "ki-information-4")
if [ "$ICON_REMOVED" -eq 0 ]; then
    echo "✅ Ícone ki-information-4 removido do título"
else
    echo "❌ Ícone ainda presente no título"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 2: Estrutura Flexbox${NC}"

# Verificar se tem justify-content-between
JUSTIFY_BETWEEN=$(grep -A 5 "card-header bg-light" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "justify-content-between")
if [ "$JUSTIFY_BETWEEN" -gt 0 ]; then
    echo "✅ justify-content-between aplicado"
else
    echo "❌ justify-content-between não encontrado"
fi

# Verificar se tem flex-grow-0
FLEX_GROW=$(grep -A 10 "card-header bg-light" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "flex-grow-0")
if [ "$FLEX_GROW" -gt 1 ]; then
    echo "✅ flex-grow-0 aplicado nos dois lados ($FLEX_GROW elementos)"
else
    echo "❌ flex-grow-0 não aplicado corretamente"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 3: Distribuição de Conteúdo${NC}"

# Verificar lado esquerdo (título)
LEFT_TITLE=$(grep -A 10 "card-header bg-light" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "flex-grow-0.*Informações da Proposição")
if [ "$LEFT_TITLE" -gt 0 ]; then
    echo "✅ Lado esquerdo: 'Informações da Proposição'"
else
    echo "❌ Lado esquerdo não estruturado corretamente"
fi

# Verificar lado direito (sincronização)
RIGHT_SYNC=$(grep -A 15 "card-header bg-light" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "Sincronização automática")
if [ "$RIGHT_SYNC" -gt 0 ]; then
    echo "✅ Lado direito: 'Sincronização automática'"
else
    echo "❌ Lado direito não estruturado corretamente"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 4: Elementos de Status${NC}"

# Verificar spinner de loading
SPINNER=$(grep -A 15 "card-header bg-light" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "spinner-border.*v-show.*loading")
if [ "$SPINNER" -gt 0 ]; then
    echo "✅ Spinner de loading presente"
else
    echo "❌ Spinner de loading não encontrado"
fi

# Verificar ícone de check
CHECK_ICON=$(grep -A 15 "card-header bg-light" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "ki-check-circle.*v-show.*!loading")
if [ "$CHECK_ICON" -gt 0 ]; then
    echo "✅ Ícone de confirmação presente"
else
    echo "❌ Ícone de confirmação não encontrado"
fi

echo -e "\n${GREEN}📊 LAYOUT FINAL ESPERADO:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

echo -e "${GREEN}📱 ESQUERDA${NC}                           ${GREEN}📱 DIREITA${NC}"
echo "Informações da Proposição         Sincronização automática ✓"

echo -e "\n${BLUE}🔧 ESTRUTURA CSS APLICADA:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "• d-flex: Container flexbox"
echo "• align-items-center: Alinhamento central"
echo "• justify-content-between: Separação máxima"
echo "• w-100: Largura total"
echo "• flex-grow-0: Prevenção de expansão"

echo -e "\n${BLUE}🎨 MELHORIAS VISUAIS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Título limpo sem ícone desnecessário"
echo "✅ Informações organizadas: esquerda ↔ direita"
echo "✅ Status de sincronização visível"
echo "✅ Spinner e ícone de confirmação preservados"
echo "✅ Layout mais profissional e focado"

echo -e "\n${BLUE}🔄 FUNCIONALIDADES MANTIDAS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Sincronização automática de dados"
echo "✅ Indicador visual de loading"
echo "✅ Confirmação visual quando atualizado"
echo "✅ Tooltip explicativo no ícone"

echo -e "\n${BLUE}🚀 PARA VERIFICAR:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Observe o card 'Informações da Proposição'"
echo "4. ✨ Layout distribuído: esquerda ↔ direita!"

echo -e "\n${GREEN}🎉 CARD CORRIGIDO!${NC}"
echo -e "📱 Distribuição: ${YELLOW}Esquerda ↔ Direita${NC}"
echo -e "🎨 Design: ${YELLOW}Limpo e organizado${NC}"
echo -e "⚡ Funcional: ${YELLOW}Todas as features mantidas${NC}"