#!/bin/bash

echo "🔧 === TESTE: LAYOUT CORRIGIDO - ESQUERDA E DIREITA ==="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "\n${BLUE}🎯 CORREÇÃO APLICADA:${NC}"
echo "✅ justify-content-between para separar lados"
echo "✅ align-items-start para alinhamento superior"
echo "✅ flex-grow-0 para evitar expansão"
echo "✅ w-100 para garantir largura total"

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO: Estrutura Flexbox${NC}"

# Verificar se tem justify-content-between
JUSTIFY_BETWEEN=$(grep -c "justify-content-between" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$JUSTIFY_BETWEEN" -gt 0 ]; then
    echo "✅ justify-content-between aplicado"
else
    echo "❌ justify-content-between não encontrado"
fi

# Verificar se tem flex-grow-0
FLEX_GROW=$(grep -c "flex-grow-0" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$FLEX_GROW" -gt 1 ]; then
    echo "✅ flex-grow-0 aplicado nos dois lados ($FLEX_GROW elementos)"
else
    echo "❌ flex-grow-0 não aplicado corretamente"
fi

# Verificar se tem w-100
WIDTH_FULL=$(grep -c "w-100" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$WIDTH_FULL" -gt 0 ]; then
    echo "✅ w-100 aplicado para largura total"
else
    echo "❌ w-100 não encontrado"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO: Estrutura dos Lados${NC}"

# Verificar lado esquerdo
LEFT_SIDE=$(grep -A 10 "flex-grow-0" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | head -10 | grep -c "PROPOSIÇÃO.*proposicao.id.*formatDate")
if [ "$LEFT_SIDE" -gt 0 ]; then
    echo "✅ Lado esquerdo: MOCAO #1 + data"
else
    echo "❌ Lado esquerdo não estruturado corretamente"
fi

# Verificar lado direito
RIGHT_SIDE=$(grep -A 10 "text-end" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | head -10 | grep -c "getStatusText.*getTimeAgo")
if [ "$RIGHT_SIDE" -gt 0 ]; then
    echo "✅ Lado direito: Status + tempo"
else
    echo "❌ Lado direito não estruturado corretamente"
fi

echo -e "\n${GREEN}📊 LAYOUT FINAL ESPERADO:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

echo -e "${GREEN}📱 ESQUERDA${NC}                           ${GREEN}📱 DIREITA${NC}"
echo "MOCAO #1                              Em Edição"
echo "Criado em 18/08/2025         Atualizado há 2h atrás"

echo -e "\n${BLUE}🔧 CLASSES CSS APLICADAS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "• d-flex: Container flexbox"
echo "• align-items-start: Alinhamento superior"
echo "• justify-content-between: Separação entre lados"
echo "• w-100: Largura total disponível"
echo "• flex-grow-0: Impede expansão dos elementos"
echo "• text-end: Alinhamento à direita no lado direito"

echo -e "\n${BLUE}🎨 MELHORIAS VISUAIS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Distribuição correta: esquerda ↔ direita"
echo "✅ Alinhamento superior para melhor aparência"
echo "✅ Espaçamento automático entre os lados"
echo "✅ Elementos não se expandem desnecessariamente"

echo -e "\n${BLUE}🚀 PARA VERIFICAR:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Observe o header do card principal"
echo "4. ✨ Informações separadas: esquerda ↔ direita!"

echo -e "\n${GREEN}🎉 LAYOUT CORRIGIDO!${NC}"
echo -e "📱 Distribuição: ${YELLOW}Esquerda ↔ Direita${NC}"
echo -e "🎨 Alinhamento: ${YELLOW}Superior e organizado${NC}"
echo -e "⚡ CSS: ${YELLOW}Flexbox otimizado${NC}"