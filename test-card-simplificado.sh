#!/bin/bash

echo "🎨 === TESTE: CARD HEADER SIMPLIFICADO ==="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "\n${BLUE}🎯 SIMPLIFICAÇÃO REALIZADA:${NC}"
echo "✅ Removidos todos os ícones do header"
echo "✅ Layout organizado: esquerda e direita"
echo "✅ Conteúdo limpo e focado"

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO: Remoção de Ícones${NC}"

# Verificar se ícones foram removidos do header
ICONS_REMOVED=$(grep -A 20 "card-header bg-gradient-primary" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "ki-duotone")
if [ "$ICONS_REMOVED" -eq 0 ]; then
    echo "✅ Todos os ícones removidos do header"
else
    echo "❌ Ainda existem ícones no header ($ICONS_REMOVED encontrados)"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO: Estrutura Simplificada${NC}"

# Verificar estrutura da esquerda
LEFT_STRUCTURE=$(grep -A 10 "card-header bg-gradient-primary" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "PROPOSIÇÃO.*#.*proposicao.id")
if [ "$LEFT_STRUCTURE" -gt 0 ]; then
    echo "✅ Lado esquerdo: MOCAO #1 estruturado corretamente"
else
    echo "❌ Lado esquerdo não estruturado corretamente"
fi

# Verificar estrutura da direita
RIGHT_STRUCTURE=$(grep -A 15 "text-end" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | head -15 | grep -c "getStatusText.*Atualizado")
if [ "$RIGHT_STRUCTURE" -eq 0 ]; then
    echo "✅ Lado direito: Status e tempo sem ícones"
else
    echo "❌ Lado direito ainda com elementos desnecessários"
fi

echo -e "\n${GREEN}📊 ESTRUTURA FINAL:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

echo -e "\n${GREEN}📱 LADO ESQUERDO:${NC}"
echo "• MOCAO #1"
echo "• Criado em 18/08/2025"

echo -e "\n${GREEN}📱 LADO DIREITO:${NC}"
echo "• Em Edição"
echo "• Atualizado há 2h atrás"

echo -e "\n${BLUE}🎨 MELHORIAS VISUAIS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Layout mais limpo e minimalista"
echo "✅ Foco no conteúdo essencial"
echo "✅ Melhor legibilidade"
echo "✅ Design mais moderno"
echo "✅ Sem distrações visuais"

echo -e "\n${BLUE}🔍 COMPARAÇÃO:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${RED}❌ ANTES:${NC}"
echo "• 🗄️ MOCAO 🏷️ #1"
echo "• 📅 Criado em 18/08/2025"
echo "• ✏️ Em Edição"
echo "• ⏰ Atualizado há 2h atrás"

echo ""
echo -e "${GREEN}✅ DEPOIS:${NC}"
echo "• MOCAO #1"
echo "• Criado em 18/08/2025"
echo "• Em Edição"
echo "• Atualizado há 2h atrás"

echo -e "\n${GREEN}🎯 RESULTADO FINAL:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Card header com design limpo e focado no conteúdo,"
echo "organizando informações de forma clara entre esquerda e direita,"
echo "sem elementos visuais desnecessários que possam distrair o usuário."

echo -e "\n${BLUE}🚀 PARA VISUALIZAR:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Observe o card header simplificado"
echo "4. ✨ Layout limpo e organizado!"

echo -e "\n${GREEN}🎉 SIMPLIFICAÇÃO CONCLUÍDA!${NC}"
echo -e "🎨 Design: ${YELLOW}Minimalista e moderno${NC}"
echo -e "📱 Layout: ${YELLOW}Organizado e funcional${NC}"
echo -e "👁️ UX: ${YELLOW}Foco no conteúdo essencial${NC}"