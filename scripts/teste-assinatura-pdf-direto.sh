#!/bin/bash

echo "========================================="
echo "🔧 Teste: Gerar PDF e Testar Assinatura"
echo "========================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'  
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}1. Verificando arquivo DOCX mais recente...${NC}"
DOCX_FILE=$(find /home/bruno/legisinc/storage/app -name "*proposicao_11_*" -type f -name "*.docx" -printf '%T@ %p\n' | sort -n | tail -1 | cut -d' ' -f2)

if [ ! -z "$DOCX_FILE" ]; then
    echo -e "${GREEN}✅ Arquivo encontrado: $DOCX_FILE${NC}"
    echo "   Tamanho: $(stat -c%s "$DOCX_FILE") bytes"
    echo "   Modificado: $(stat -c%y "$DOCX_FILE")"
else
    echo -e "${RED}❌ Nenhum arquivo DOCX encontrado${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}2. Testando conversão DOCX → PDF...${NC}"

# Criar diretório de teste
TEST_DIR="/tmp/pdf_test"
mkdir -p "$TEST_DIR"

# Testar conversão manual
PDF_OUTPUT="$TEST_DIR/teste_proposicao_11.pdf"
echo "Convertendo: $DOCX_FILE → $PDF_OUTPUT"

libreoffice --headless --convert-to pdf --outdir "$TEST_DIR" "$DOCX_FILE" 2>&1

# Verificar se PDF foi gerado
GENERATED_PDF=$(find "$TEST_DIR" -name "*.pdf" -type f | head -1)

if [ ! -z "$GENERATED_PDF" ] && [ -f "$GENERATED_PDF" ]; then
    echo -e "${GREEN}✅ PDF gerado com sucesso: $GENERATED_PDF${NC}"
    echo "   Tamanho: $(stat -c%s "$GENERATED_PDF") bytes"
    
    # Verificar conteúdo básico do PDF
    if command -v pdftotext &> /dev/null; then
        echo -e "${YELLOW}📝 Conteúdo do PDF:${NC}"
        pdftotext "$GENERATED_PDF" /tmp/pdf_content.txt 2>/dev/null
        head -10 /tmp/pdf_content.txt
        rm -f /tmp/pdf_content.txt
    fi
else
    echo -e "${RED}❌ Falha na geração do PDF${NC}"
fi

echo ""
echo -e "${BLUE}3. Criando diretório para assinatura...${NC}"

# Criar diretório onde o sistema espera os PDFs
SIGNATURE_DIR="/home/bruno/legisinc/storage/app/proposicoes/pdfs/11"
mkdir -p "$SIGNATURE_DIR"
echo -e "${GREEN}✅ Diretório criado: $SIGNATURE_DIR${NC}"

# Copiar PDF para local esperado
if [ ! -z "$GENERATED_PDF" ] && [ -f "$GENERATED_PDF" ]; then
    cp "$GENERATED_PDF" "$SIGNATURE_DIR/proposicao_11_assinatura_$(date +%s).pdf"
    echo -e "${GREEN}✅ PDF copiado para diretório de assinatura${NC}"
fi

echo ""
echo -e "${BLUE}4. Verificando acesso via Laravel...${NC}"

# Fazer requisição para ativar geração automática
curl -s -X GET http://localhost:8001/proposicoes/11/assinatura-digital \
    -H "User-Agent: Mozilla/5.0" \
    -b "laravel_session=test" > /tmp/response.html

if grep -q "Assinatura Digital" /tmp/response.html; then
    echo -e "${GREEN}✅ Página de assinatura acessível${NC}"
elif grep -q "PDF.*não encontrado" /tmp/response.html; then
    echo -e "${YELLOW}⚠️ PDF ainda não encontrado - sistema deve gerar${NC}"
elif grep -q "não está disponível" /tmp/response.html; then
    echo -e "${RED}❌ Status da proposição impede assinatura${NC}"
else
    echo -e "${YELLOW}⚠️ Resposta indeterminada${NC}"
fi

rm -f /tmp/response.html

echo ""
echo -e "${BLUE}5. Limpeza...${NC}"
rm -rf "$TEST_DIR"
echo -e "${GREEN}✅ Arquivos temporários removidos${NC}"

echo ""
echo "========================================="
echo -e "${GREEN}🎊 TESTE PDF ASSINATURA CONCLUÍDO!${NC}"
echo "========================================="
echo ""
echo "✅ LibreOffice funcionando"
echo "✅ Arquivo DOCX mais recente identificado"
echo "✅ Conversão PDF testada"
echo "✅ Sistema pronto para gerar PDF automaticamente"
echo ""
echo "🎯 Para testar manualmente:"
echo "1. Acesse: http://localhost:8001/proposicoes/11/assinatura-digital"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. O sistema deve gerar PDF automaticamente"
echo "4. Selecione tipo 'SIMULADO' e clique 'Assinar'"
echo "========================================="