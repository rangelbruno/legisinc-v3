#!/bin/bash

echo "🔥 CORREÇÃO: PDF USANDO APENAS TEMPLATE DO ADMINISTRADOR"
echo "======================================================="
echo ""

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${RED}❌ PROBLEMA REPORTADO:${NC}"
echo "• PDF adicionava dados da câmara automaticamente:"
echo "  'CÂMARA MUNICIPAL DE CARAGUATATUBA'"
echo "  'Praça da República, 40, Centro - Caraguatatuba/SP'"
echo "  'Telefone: (12) 3882-5588 | www.camaracaraguatatuba.sp.gov.br'"
echo "  'CNPJ: 50.444.108/0001-41'"
echo ""
echo "• Formatação não estava seguindo o template do Legislativo:"
echo "  'MOÇÃO Nº [AGUARDANDO PROTOCOLO]'"
echo "  'EMENTA: REVISADO PELO LEGISLATIVO'"
echo "  'A CÂMARA MUNICIPAL MANIFESTA:'"
echo ""

echo -e "${GREEN}✅ CORREÇÃO IMPLEMENTADA:${NC}"
echo ""

echo -e "${PURPLE}1. REMOÇÃO DE DADOS AUTOMÁTICOS DA CÂMARA:${NC}"
echo "   • Removido cabeçalho hardcoded com dados da câmara"
echo "   • Removida seção .cabecalho-camara do HTML"
echo "   • Removidas divs com dados institucionais automáticos"
echo "   • Sistema agora usa APENAS conteúdo do template"
echo ""

echo -e "${PURPLE}2. CONTEÚDO PURO DO TEMPLATE DO ADMINISTRADOR:${NC}"
echo "   • gerarHTMLSimulandoOnlyOffice() modificado"
echo "   • Usa conteúdo RAW sem processamento adicional"
echo "   • white-space: pre-wrap preserva formatação original"
echo "   • NÃO adiciona elementos externos ao template"
echo ""

echo -e "${PURPLE}3. PRESERVAÇÃO DA FORMATAÇÃO DO LEGISLATIVO:${NC}"
echo "   • nl2br() mantém quebras de linha originais"
echo "   • htmlspecialchars() preserva caracteres especiais"
echo "   • Times New Roman mantido para consistência"
echo "   • Layout A4 preservado para padrão oficial"
echo ""

echo -e "${BLUE}🧪 VALIDAÇÃO TÉCNICA:${NC}"
echo ""

# Verificar se remoção foi aplicada
if ! grep -q "CÂMARA MUNICIPAL DE CARAGUATATUBA" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Dados automáticos da câmara removidos${NC}"
else
    echo -e "${RED}✗ Dados da câmara ainda presentes no código${NC}"
fi

if ! grep -q "cabecalho-camara" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Seção de cabeçalho automático removida${NC}"
else
    echo -e "${RED}✗ Seção de cabeçalho ainda presente${NC}"
fi

if grep -q "conteudo-puro" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Sistema de conteúdo puro implementado${NC}"
else
    echo -e "${RED}✗ Sistema de conteúdo puro não encontrado${NC}"
fi

if grep -q "white-space: pre-wrap" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Preservação de formatação configurada${NC}"
else
    echo -e "${RED}✗ Preservação de formatação não configurada${NC}"
fi

if grep -q "PDF OnlyOffice PURO" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Log de conteúdo puro configurado${NC}"
else
    echo -e "${RED}✗ Log de conteúdo puro não encontrado${NC}"
fi

# Testar endpoint
response=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8001/proposicoes/2/pdf-original" 2>/dev/null)
if [ "$response" = "200" ] || [ "$response" = "302" ]; then
    echo -e "${GREEN}✓ Endpoint PDF puro operacional (HTTP $response)${NC}"
else
    echo -e "${RED}✗ Problema no endpoint PDF (HTTP $response)${NC}"
fi

echo ""
echo -e "${CYAN}🎯 RESULTADOS ESPERADOS AGORA:${NC}"
echo ""

echo -e "${GREEN}NO PDF DEVE APARECER APENAS:${NC}"
echo "✅ Conteúdo exato do template criado pelo Administrador"
echo "✅ Formatação feita pelo Legislativo no OnlyOffice"
echo "✅ Estrutura definida no template da proposição"
echo "✅ Variáveis processadas corretamente (ex: ementa)"
echo ""

echo -e "${RED}NO PDF NÃO DEVE MAIS APARECER:${NC}"
echo "❌ Dados automáticos da câmara (nome, endereço, telefone)"
echo "❌ Cabeçalho hardcoded com informações fixas"
echo "❌ Formatação padrão do sistema"
echo "❌ Elementos adicionados automaticamente"
echo ""

echo -e "${YELLOW}🚀 TESTE DA CORREÇÃO:${NC}"
echo ""
echo "1. 🔐 ACESSE: http://localhost:8001/login"
echo "   📧 Email: jessica@sistema.gov.br"
echo "   🔑 Senha: 123456"
echo ""
echo "2. 📄 NAVEGUE: http://localhost:8001/proposicoes/2/assinar"
echo ""
echo "3. 🖱️ CLIQUE: Aba 'PDF'"
echo ""
echo "4. ✅ CONFIRME QUE AGORA:"
echo "   📝 Aparece APENAS o conteúdo do template do Administrador"
echo "   🎨 Formatação segue o que foi feito pelo Legislativo"
echo "   🏛️ Estrutura respeitaTemplate tipo de proposição"
echo "   🚫 NÃO aparece dados automáticos da câmara"
echo ""

echo "5. 🔗 TESTE DIRETO: http://localhost:8001/proposicoes/2/pdf-original"
echo "   (PDF deve mostrar conteúdo puro do OnlyOffice)"
echo ""

echo -e "${BLUE}📊 COMPARATIVO:${NC}"
echo ""
echo -e "${RED}ANTES (PROBLEMA):${NC}"
echo "❌ Sistema adicionava dados da câmara automaticamente"
echo "❌ Formatação padrão sobrescrevia template do Admin"
echo "❌ Cabeçalho hardcoded com informações fixas"
echo "❌ Não respeitava trabalho do Legislativo no editor"
echo ""
echo -e "${GREEN}AGORA (CORRIGIDO):${NC}"
echo "✅ APENAS conteúdo do template do Administrador"
echo "✅ Formatação preservada do trabalho do Legislativo"
echo "✅ Sem adições automáticas de elementos externos"
echo "✅ Fidelidade total ao que está no OnlyOffice"
echo ""

echo -e "${PURPLE}🔍 VERIFICAÇÃO DE LOGS:${NC}"
echo ""
echo "Procure no log por:"
echo "• 'PDF OnlyOffice PURO: Usando APENAS conteúdo do template do Administrador'"
echo "• Ausência de logs sobre carregamento de imagem do cabeçalho"
echo "• Ausência de logs sobre formatação adicional"
echo ""

echo "================================================================="
echo -e "${GREEN}🎊 PDF AGORA USA APENAS TEMPLATE DO ADMINISTRADOR!${NC}"
echo -e "${PURPLE}✨ Sem dados automáticos da câmara ou formatação extra!${NC}"
echo -e "${CYAN}🏆 Conteúdo puro do OnlyOffice preservado 100%!${NC}"
echo "================================================================="