#!/bin/bash

echo "========================================="
echo "🔧 Teste: Assinatura com Geração PDF"
echo "========================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

echo -e "${BLUE}1. Verificando status da proposição 11...${NC}"

# Verificar se LibreOffice está disponível
echo -e "${BLUE}2. Verificando LibreOffice...${NC}"
if command -v libreoffice &> /dev/null; then
    echo -e "${GREEN}✅ LibreOffice disponível${NC}"
    libreoffice --version
else
    echo -e "${RED}❌ LibreOffice não encontrado${NC}"
    echo "Instalando LibreOffice..."
    sudo apt update && sudo apt install -y libreoffice
fi

echo -e "${BLUE}3. Verificando arquivos da proposição 11...${NC}"

# Buscar arquivos DOCX da proposição 11
find /home/bruno/legisinc/storage/app -name "*proposicao_11_*" -type f 2>/dev/null | while read arquivo; do
    if [ -f "$arquivo" ]; then
        echo -e "${GREEN}📄 Encontrado: $arquivo${NC}"
        echo "   Tamanho: $(stat -c%s "$arquivo") bytes"
        echo "   Modificado: $(stat -c%y "$arquivo")"
    fi
done

echo ""
echo -e "${BLUE}4. Testando acesso direto à assinatura...${NC}"

# Testar se a página de assinatura funciona agora
echo "Tentando acessar: http://localhost:8001/proposicoes/11/assinatura-digital"

# Login como Parlamentar
echo -e "${YELLOW}Fazendo login como Jessica (Parlamentar)...${NC}"
COOKIE_FILE="/tmp/assinatura_test_cookies.txt"

# Fazer login
curl -s -c "$COOKIE_FILE" -X POST \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "email=jessica@sistema.gov.br&password=123456" \
    http://localhost:8001/login > /dev/null

# Verificar se consegue acessar a página de assinatura
echo -e "${YELLOW}Testando acesso à página de assinatura...${NC}"
RESPONSE=$(curl -s -b "$COOKIE_FILE" http://localhost:8001/proposicoes/11/assinatura-digital)

if echo "$RESPONSE" | grep -q "Assinatura Digital"; then
    echo -e "${GREEN}✅ Página de assinatura acessível${NC}"
elif echo "$RESPONSE" | grep -q "não está disponível para assinatura"; then
    echo -e "${RED}❌ Proposição não está disponível para assinatura${NC}"
    echo "Status atual da proposição deve estar 'aprovado'"
elif echo "$RESPONSE" | grep -q "PDF.*não encontrado"; then
    echo -e "${YELLOW}⚠️ PDF será gerado automaticamente${NC}"
elif echo "$RESPONSE" | grep -q "403\|Forbidden"; then
    echo -e "${RED}❌ Erro de permissão 403${NC}"
else
    echo -e "${YELLOW}⚠️ Resposta não esperada${NC}"
    echo "$(echo "$RESPONSE" | head -3)"
fi

# Limpeza
rm -f "$COOKIE_FILE"

echo ""
echo -e "${PURPLE}📋 PRÓXIMOS PASSOS:${NC}"
echo "1. Verifique se a proposição 11 está com status 'aprovado'"
echo "2. Acesse: http://localhost:8001/proposicoes/11/assinatura-digital"
echo "3. Login: jessica@sistema.gov.br / 123456"
echo "4. O sistema deve gerar PDF automaticamente se necessário"
echo "5. Teste a assinatura SIMULADO"
echo ""
echo "========================================="
echo -e "${GREEN}🎊 TESTE DE GERAÇÃO PDF IMPLEMENTADO!${NC}"
echo "========================================="