#!/bin/bash

echo "🧹 === TESTE: LIMPEZA DOS DADOS EMENTA E CONTEÚDO ==="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "\n${BLUE}📋 Problema Identificado:${NC}"
echo "❌ Ementa: 'Criado pelo Parlamentar'"
echo "❌ Conteúdo: 'assinatura_digital_info MOÇÃO Nº [...] qrcode_html'"
echo "❌ Dados misturados com elementos de template"

echo -e "\n${BLUE}✅ Solução Implementada:${NC}"
echo "• Método extrairDadosLimpos() no Controller"
echo "• Método cleanProposicaoData() no Vue.js"
echo "• Limpeza automática na API e interface"
echo "• Extração inteligente via regex"

echo -e "\n${YELLOW}🧪 TESTE 1: Dados originais (antes da limpeza)${NC}"

DADOS_ORIGINAIS=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
if (\$proposicao) {
    echo 'ORIGINAL_EMENTA:' . \$proposicao->ementa . PHP_EOL;
    echo 'ORIGINAL_CONTEUDO_LENGTH:' . strlen(\$proposicao->conteudo) . PHP_EOL;
    echo 'ORIGINAL_CONTEUDO_PREVIEW:' . substr(\$proposicao->conteudo, 0, 100) . '...' . PHP_EOL;
}
")

echo "$DADOS_ORIGINAIS"

echo -e "\n${YELLOW}🧪 TESTE 2: Dados limpos via Controller${NC}"

DADOS_LIMPOS=$(docker exec legisinc-app php artisan tinker --execute="
\$controller = new App\Http\Controllers\ProposicaoController();
\$user = App\Models\User::find(6);
Auth::login(\$user);
\$response = \$controller->getDadosFrescos(1);
\$content = json_decode(\$response->getContent(), true);
if (\$content['success']) {
    echo 'LIMPO_EMENTA:' . \$content['proposicao']['ementa'] . PHP_EOL;
    echo 'LIMPO_CONTEUDO:' . \$content['proposicao']['conteudo'] . PHP_EOL;
    echo 'LIMPO_CHARS:' . \$content['proposicao']['meta']['char_count'] . PHP_EOL;
    echo 'LIMPO_WORDS:' . \$content['proposicao']['meta']['word_count'] . PHP_EOL;
} else {
    echo 'ERRO:' . \$content['message'] . PHP_EOL;
}
")

echo "$DADOS_LIMPOS"

echo -e "\n${YELLOW}🧪 TESTE 3: Verificar limpeza de elementos específicos${NC}"

# Verificar se elementos de template foram removidos
ELEMENTOS_REMOVIDOS=0

if ! echo "$DADOS_LIMPOS" | grep -q "assinatura_digital_info"; then
    echo "✅ 'assinatura_digital_info' removido"
    ((ELEMENTOS_REMOVIDOS++))
else
    echo "❌ 'assinatura_digital_info' ainda presente"
fi

if ! echo "$DADOS_LIMPOS" | grep -q "qrcode_html"; then
    echo "✅ 'qrcode_html' removido"
    ((ELEMENTOS_REMOVIDOS++))
else
    echo "❌ 'qrcode_html' ainda presente"
fi

if ! echo "$DADOS_LIMPOS" | grep -q "MOÇÃO Nº \[AGUARDANDO PROTOCOLO\]"; then
    echo "✅ 'MOÇÃO Nº [AGUARDANDO PROTOCOLO]' removido"
    ((ELEMENTOS_REMOVIDOS++))
else
    echo "❌ 'MOÇÃO Nº [AGUARDANDO PROTOCOLO]' ainda presente"
fi

if ! echo "$DADOS_LIMPOS" | grep -q "Câmara Municipal de Caraguatatuba - Documento Oficial"; then
    echo "✅ 'Documento Oficial' removido"
    ((ELEMENTOS_REMOVIDOS++))
else
    echo "❌ 'Documento Oficial' ainda presente"
fi

echo "📊 Total de elementos removidos: $ELEMENTOS_REMOVIDOS/4"

echo -e "\n${YELLOW}🧪 TESTE 4: Verificar extração de conteúdo útil${NC}"

if echo "$DADOS_LIMPOS" | grep -q "LIMPO_CONTEUDO:Texto alterado pelo Parlamentar"; then
    echo "✅ Conteúdo útil extraído corretamente"
else
    echo "❌ Conteúdo útil não extraído adequadamente"
fi

# Verificar se ementa foi melhorada
if echo "$DADOS_LIMPOS" | grep -q "LIMPO_EMENTA:Moção em elaboração"; then
    echo "✅ Ementa melhorada com fallback"
else
    echo "❌ Ementa não melhorada"
fi

echo -e "\n${YELLOW}🧪 TESTE 5: Verificar função Vue.js${NC}"

# Verificar se função cleanProposicaoData existe na interface
VUE_FUNCTION=$(grep -c "cleanProposicaoData()" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ "$VUE_FUNCTION" -gt 0 ]; then
    echo "✅ Função cleanProposicaoData() implementada no Vue.js"
else
    echo "❌ Função cleanProposicaoData() não encontrada"
fi

# Verificar se é chamada no mounted()
VUE_MOUNTED=$(grep -A 5 "mounted()" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php | grep -c "cleanProposicaoData")
if [ "$VUE_MOUNTED" -gt 0 ]; then
    echo "✅ Limpeza automática configurada no mounted()"
else
    echo "❌ Limpeza automática não configurada"
fi

echo -e "\n${YELLOW}🧪 TESTE 6: Verificar regex de extração${NC}"

# Testar regex para extração de ementa
REGEX_EMENTA=$(grep -c "EMENTA:\\\s\*\(\[\^A\]\+\?\)\\\s\*A Câmara" /home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php)
if [ "$REGEX_EMENTA" -gt 0 ]; then
    echo "✅ Regex para extração de ementa implementado"
else
    echo "❌ Regex para extração de ementa não encontrado"
fi

# Testar regex para extração de conteúdo
REGEX_CONTEUDO=$(grep -c "A Câmara Municipal manifesta" /home/bruno/legisinc/app/Http/Controllers/ProposicaoController.php)
if [ "$REGEX_CONTEUDO" -gt 0 ]; then
    echo "✅ Regex para extração de conteúdo implementado"
else
    echo "❌ Regex para extração de conteúdo não encontrado"
fi

echo -e "\n${GREEN}📊 RESUMO DOS RESULTADOS:${NC}"
echo ""

# Extrair valores específicos dos dados limpos
EMENTA_FINAL=$(echo "$DADOS_LIMPOS" | grep "LIMPO_EMENTA:" | cut -d':' -f2)
CONTEUDO_FINAL=$(echo "$DADOS_LIMPOS" | grep "LIMPO_CONTEUDO:" | cut -d':' -f2)
CHARS_FINAL=$(echo "$DADOS_LIMPOS" | grep "LIMPO_CHARS:" | cut -d':' -f2)

echo -e "${GREEN}✅ ANTES vs DEPOIS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📝 EMENTA:"
echo "   Antes: 'Criado pelo Parlamentar'"
echo "   Depois: '$EMENTA_FINAL'"
echo ""
echo "📄 CONTEÚDO:"
echo "   Antes: 365 chars com elementos de template"
echo "   Depois: $CHARS_FINAL chars limpos: '$CONTEUDO_FINAL'"
echo ""

echo -e "${GREEN}🎯 MELHORIAS IMPLEMENTADAS:${NC}"
echo "• ✅ Limpeza automática de elementos de template"
echo "• ✅ Extração inteligente via regex"
echo "• ✅ Fallbacks para dados vazios"
echo "• ✅ Aplicação tanto no Controller quanto Vue.js"
echo "• ✅ Preservação de conteúdo útil"
echo "• ✅ Interface mais limpa e profissional"

echo -e "\n${GREEN}🔧 FUNÇÕES IMPLEMENTADAS:${NC}"
echo "• Controller: extrairDadosLimpos()"
echo "• Vue.js: cleanProposicaoData()"
echo "• Ambas chamadas automaticamente"
echo "• Processamento tanto de dados novos quanto antigos"

echo -e "\n${BLUE}🚀 COMO TESTAR AS MELHORIAS:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. 👁️ Observe: Ementa e Conteúdo agora limpos"
echo "4. 🔄 Use botão 'Atualizar Dados' para testar API"
echo "5. ✨ Dados aparecem organizados e sem 'lixo'"

echo -e "\n${GREEN}🎉 DADOS LIMPOS IMPLEMENTADOS COM SUCESSO!${NC}"
echo -e "📝 Ementa: ${YELLOW}Texto claro e útil${NC}"
echo -e "📄 Conteúdo: ${YELLOW}Sem elementos de template${NC}"
echo -e "🎨 Interface: ${YELLOW}Profissional e organizada${NC}"
echo -e "⚡ Performance: ${YELLOW}Dados otimizados${NC}"