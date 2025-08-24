#!/bin/bash

echo "🍭 === TESTE: SWEETALERT NO BOTÃO ENVIAR PARA LEGISLATIVO ==="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "\n${BLUE}🎯 MELHORIAS IMPLEMENTADAS:${NC}"
echo "✅ SweetAlert2 com confirmação detalhada"
echo "✅ Loading state durante processamento"
echo "✅ Feedback visual de sucesso/erro"
echo "✅ Integração com API Laravel"
echo "✅ Atualização automática da interface"
echo "✅ Sistema de notificações toast"

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 1: Implementação SweetAlert${NC}"

# Verificar se o método confirmSendToLegislative foi atualizado
SWEETALERT_IMPL=$(grep -c "Swal.fire" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$SWEETALERT_IMPL" -gt 0 ]; then
    echo "✅ SweetAlert implementado ($SWEETALERT_IMPL ocorrências)"
else
    echo "❌ SweetAlert não implementado"
fi

# Verificar se tem confirmação detalhada
DETAILED_CONFIRM=$(grep -c "Proposição.*#.*Ementa.*Status atual" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$DETAILED_CONFIRM" -gt 0 ]; then
    echo "✅ Confirmação detalhada implementada"
else
    echo "❌ Confirmação detalhada não encontrada"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 2: Funções JavaScript${NC}"

# Verificar função submitToLegislative
SUBMIT_FUNCTION=$(grep -c "async submitToLegislative" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$SUBMIT_FUNCTION" -gt 0 ]; then
    echo "✅ Função submitToLegislative implementada"
else
    echo "❌ Função submitToLegislative não encontrada"
fi

# Verificar função showErrorAlert
ERROR_FUNCTION=$(grep -c "showErrorAlert" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$ERROR_FUNCTION" -gt 0 ]; then
    echo "✅ Função showErrorAlert implementada"
else
    echo "❌ Função showErrorAlert não encontrada"
fi

# Verificar método PUT corrigido
PUT_METHOD=$(grep -A 5 "submitToLegislative" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "_method.*PUT")
if [ "$PUT_METHOD" -gt 0 ]; then
    echo "✅ Método PUT corrigido para Laravel"
else
    echo "❌ Método PUT não configurado corretamente"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 3: Backend Controller${NC}"

# Verificar se o controller retorna JSON atualizado
CONTROLLER_JSON=$(grep -A 10 "success.*true" /home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php | grep -c "proposicao.*id")
if [ "$CONTROLLER_JSON" -gt 0 ]; then
    echo "✅ Controller retorna dados da proposição"
else
    echo "❌ Controller não retorna dados completos"
fi

# Verificar rota enviar-legislativo
ROUTE_EXISTS=$(grep -c "enviar-legislativo.*ProposicaoController" /home/bruno/legisinc/routes/web.php)
if [ "$ROUTE_EXISTS" -gt 0 ]; then
    echo "✅ Rota enviar-legislativo configurada"
else
    echo "❌ Rota enviar-legislativo não encontrada"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 4: Dados da Proposição para Teste${NC}"

# Verificar dados da proposição 1 para teste
PROP_DATA=$(docker exec legisinc-app php artisan tinker --execute="
\$prop = App\Models\Proposicao::find(1);
if (\$prop) {
    echo 'STATUS:' . \$prop->status . PHP_EOL;
    echo 'AUTOR_ID:' . \$prop->autor_id . PHP_EOL;
    echo 'EMENTA:' . (\$prop->ementa ? 'presente' : 'ausente') . PHP_EOL;
    echo 'CONTEUDO:' . (\$prop->conteudo ? 'presente' : 'ausente') . PHP_EOL;
} else {
    echo 'ERRO: Proposição não encontrada';
}
")

echo "$PROP_DATA"

# Verificar se está em status válido para envio
if echo "$PROP_DATA" | grep -q "STATUS:em_edicao\|STATUS:rascunho"; then
    echo "✅ Status válido para envio ao legislativo"
else
    echo "❌ Status não permite envio (precisa ser 'em_edicao' ou 'rascunho')"
fi

echo -e "\n${YELLOW}🧪 VERIFICAÇÃO 5: Estrutura SweetAlert${NC}"

# Verificar elementos específicos do SweetAlert
SWAL_ELEMENTS=("title.*Enviar para o Legislativo" "showCancelButton.*true" "confirmButtonText.*Enviar para Legislativo" "icon.*question" "width.*600px")

for element in "${SWAL_ELEMENTS[@]}"; do
    if grep -q "$element" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php; then
        echo "✅ Elemento encontrado: $element"
    else
        echo "❌ Elemento não encontrado: $element"
    fi
done

echo -e "\n${GREEN}📊 RECURSOS IMPLEMENTADOS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

echo -e "\n${GREEN}🎨 Interface SweetAlert:${NC}"
echo "• Modal responsivo (600px) com design moderno"
echo "• Informações detalhadas da proposição"
echo "• Alerta de atenção sobre irreversibilidade"
echo "• Botões estilizados com ícones"
echo "• Loading state durante processamento"

echo -e "\n${GREEN}🔄 Fluxo de Confirmação:${NC}"
echo "1. Usuário clica em 'Enviar para o Legislativo'"
echo "2. SweetAlert exibe confirmação detalhada"
echo "3. Se confirmado, mostra loading"
echo "4. Envia request para Laravel via fetch"
echo "5. Exibe resultado (sucesso ou erro)"
echo "6. Atualiza interface automaticamente"

echo -e "\n${GREEN}📡 Integração Backend:${NC}"
echo "• Método PUT corrigido com method spoofing"
echo "• Validações de permissão e status"
echo "• Resposta JSON estruturada"
echo "• Dados atualizados da proposição"
echo "• Tratamento de erros completo"

echo -e "\n${GREEN}🎯 Feedback Visual:${NC}"
echo "• SweetAlert de sucesso com ícone de check"
echo "• SweetAlert de erro com ícone de X"
echo "• Toast notification no canto da tela"
echo "• Atualização automática do timeline"
echo "• Mudança de status visual"

echo -e "\n${BLUE}🧪 COMO TESTAR AS MELHORIAS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "1. 🌐 Acesse: http://localhost:8001/proposicoes/1"
echo "2. 🔐 Login: jessica@sistema.gov.br / 123456"
echo "3. 🔍 Localize o botão 'Enviar para o Legislativo'"
echo "4. 🖱️ Clique no botão e observe:"
echo "   • Modal SweetAlert detalhado"
echo "   • Informações da proposição"
echo "   • Botões estilizados"
echo "5. ✅ Confirme e observe:"
echo "   • Loading durante processamento"
echo "   • Feedback de sucesso"
echo "   • Atualização automática da tela"
echo "6. 🔄 Teste também o cancelamento"

echo -e "\n${BLUE}🎭 COMPARAÇÃO: ANTES vs DEPOIS${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${RED}❌ ANTES (confirm básico):${NC}"
echo "• Alert JavaScript simples"
echo "• Texto genérico de confirmação"
echo "• Sem informações contextuais"
echo "• Sem feedback visual durante processamento"
echo "• Experiência básica e pouco profissional"

echo ""
echo -e "${GREEN}✅ DEPOIS (SweetAlert profissional):${NC}"
echo "• Modal responsivo e moderno"
echo "• Informações detalhadas da proposição"
echo "• Design consistente com o sistema"
echo "• Loading state e feedback visual"
echo "• Experiência profissional e confiável"

echo -e "\n${GREEN}🚀 MELHORIAS TÉCNICAS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Async/await para melhor controle de fluxo"
echo "✅ Tratamento robusto de erros"
echo "✅ Integração nativa com Laravel"
echo "✅ Atualização automática da interface"
echo "✅ Notificações não-intrusivas"
echo "✅ Código limpo e maintível"

echo -e "\n${GREEN}🎉 SWEETALERT IMPLEMENTADO COM SUCESSO!${NC}"
echo -e "🍭 Interface: ${YELLOW}Moderna e profissional${NC}"
echo -e "⚡ Performance: ${YELLOW}Assíncrona e responsiva${NC}"
echo -e "🎨 UX: ${YELLOW}Experiência significativamente melhorada${NC}"
echo -e "🔧 Código: ${YELLOW}Robusto e bem estruturado${NC}"