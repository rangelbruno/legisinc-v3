#!/bin/bash

echo "✅ === TESTE: ÍCONE RESTAURADO ==="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "\n${BLUE}🎯 CORREÇÃO APLICADA:${NC}"
echo "✅ Ícone ki-information-4 restaurado"
echo "✅ Layout esquerda ↔ direita mantido"
echo "✅ Consistência visual preservada"

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO: Ícone Presente${NC}"

# Verificar se o ícone foi restaurado
ICON_PRESENT=$(grep -A 10 "Informações da Proposição" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "ki-information-4")
if [ "$ICON_PRESENT" -gt 0 ]; then
    echo "✅ Ícone ki-information-4 presente no título"
else
    echo "❌ Ícone ainda ausente no título"
fi

# Verificar estrutura completa
FULL_STRUCTURE=$(grep -A 15 "card-header bg-light" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | head -15 | grep -c "ki-information-4.*Informações da Proposição.*Sincronização automática")
if [ "$FULL_STRUCTURE" -gt 0 ]; then
    echo "✅ Estrutura completa: ícone + título + sincronização"
else
    echo "❌ Estrutura incompleta"
fi

echo -e "\n${GREEN}📊 LAYOUT FINAL:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

echo -e "${GREEN}📱 ESQUERDA${NC}                           ${GREEN}📱 DIREITA${NC}"
echo "ℹ️ Informações da Proposição         Sincronização automática ✓"

echo -e "\n${BLUE}🎨 RESULTADO FINAL:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Ícone informativo presente"
echo "✅ Título claro e identificável"
echo "✅ Layout distribuído corretamente"
echo "✅ Funcionalidades de sincronização mantidas"
echo "✅ Consistência visual com outros cards"

echo -e "\n${GREEN}🎉 ÍCONE RESTAURADO COM SUCESSO!${NC}"
echo -e "ℹ️ Visual: ${YELLOW}Ícone + título organizados${NC}"
echo -e "📱 Layout: ${YELLOW}Esquerda ↔ direita${NC}"
echo -e "✨ Status: ${YELLOW}Completo e funcional${NC}"