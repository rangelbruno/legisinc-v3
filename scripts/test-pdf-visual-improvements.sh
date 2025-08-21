#!/bin/bash

echo "🎨 TESTANDO MELHORIAS VISUAIS DO PDF"
echo "===================================="
echo ""

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

echo -e "${PURPLE}🔍 VERIFICANDO IMPLEMENTAÇÃO ATUAL:${NC}"
echo ""

# Verificar se métodos estão implementados
if grep -q "obterImagemCabecalhoBase64" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Método para imagem do cabeçalho implementado${NC}"
else
    echo -e "${RED}✗ Método para imagem não encontrado${NC}"
fi

if grep -q "formatarConteudoParaPDF" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Método para formatação de texto implementado${NC}"
else
    echo -e "${RED}✗ Método para formatação não encontrado${NC}"
fi

if grep -q "gerarHTMLSimulandoOnlyOffice" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Gerador HTML com formatação implementado${NC}"
else
    echo -e "${RED}✗ Gerador HTML não encontrado${NC}"
fi

# Verificar se arquivo de imagem existe
if [ -f "/home/bruno/legisinc/public/template/cabecalho.png" ]; then
    image_size=$(stat --format='%s' "/home/bruno/legisinc/public/template/cabecalho.png")
    echo -e "${GREEN}✓ Imagem do cabeçalho encontrada (${image_size} bytes)${NC}"
else
    echo -e "${RED}✗ Imagem do cabeçalho não encontrada${NC}"
fi

# Verificar CSS no método HTML
if grep -q "cabecalho-camara" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ CSS para cabeçalho da câmara definido${NC}"
else
    echo -e "${RED}✗ CSS para cabeçalho não encontrado${NC}"
fi

if grep -q "Times New Roman" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Fonte Times New Roman configurada${NC}"
else
    echo -e "${RED}✗ Fonte padrão não configurada${NC}"
fi

# Verificar estrutura HTML
if grep -q "conteudo-documento" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Estrutura de conteúdo formatado implementada${NC}"
else
    echo -e "${RED}✗ Estrutura de formatação não encontrada${NC}"
fi

echo ""
echo -e "${BLUE}📊 ANÁLISE DA IMPLEMENTAÇÃO:${NC}"
echo ""

# Verificar detalhes específicos do CSS
if grep -q "@page" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Configuração de página A4 implementada${NC}"
else
    echo -e "${RED}✗ Configuração de página não encontrada${NC}"
fi

if grep -q "text-align: center" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Alinhamento de cabeçalho configurado${NC}"
else
    echo -e "${RED}✗ Alinhamento não configurado${NC}"
fi

if grep -q "border-bottom.*1a4b8c" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
    echo -e "${GREEN}✓ Linha decorativa do cabeçalho configurada${NC}"
else
    echo -e "${RED}✗ Linha decorativa não encontrada${NC}"
fi

echo ""
echo -e "${YELLOW}🎯 PONTOS A VERIFICAR:${NC}"
echo ""

echo "1. A imagem do cabeçalho está sendo incorporada em Base64?"
echo "2. O CSS está sendo aplicado corretamente no PDF?"
echo "3. A formatação de texto está detectando títulos?"
echo "4. As margens e espaçamentos estão adequados?"

echo ""
echo -e "${PURPLE}🧪 TESTE MANUAL RECOMENDADO:${NC}"
echo ""
echo "1. Login: http://localhost:8001/login"
echo "   Credenciais: jessica@sistema.gov.br / 123456"
echo ""
echo "2. Acesse: http://localhost:8001/proposicoes/2/assinar"
echo ""
echo "3. Clique na aba 'PDF' para visualizar"
echo ""
echo "4. Verifique se:"
echo "   ✓ Imagem do cabeçalho aparece no topo"
echo "   ✓ Dados da câmara estão formatados"
echo "   ✓ Texto está bem estruturado em parágrafos"
echo "   ✓ Títulos estão destacados"
echo "   ✓ Fonte e espaçamento estão adequados"

echo ""
echo -e "${BLUE}📝 LOGS PARA VERIFICAÇÃO:${NC}"
echo ""
echo "Logs relevantes que devem aparecer:"
echo "• 'PDF OnlyOffice: Gerando HTML com formatação adequada e imagem do cabeçalho'"
echo "• 'PDF OnlyOffice: Imagem do cabeçalho carregada - X bytes'"
echo "• 'PDF OnlyOffice: Formatando conteúdo para PDF'"
echo "• 'PDF OnlyOffice: Conteúdo formatado - X caracteres HTML'"

echo ""
echo "================================================"
echo -e "${GREEN}🎨 VERIFICAÇÃO DE MELHORIAS VISUAIS CONCLUÍDA${NC}"
echo "================================================"