#!/bin/bash

echo "========================================="
echo "🔧 Teste Completo: Sistema de Assinatura"
echo "========================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# 1. Verificar se aplicação está rodando
echo -e "${BLUE}1. Verificando aplicação...${NC}"
if curl -s http://localhost:8001 > /dev/null; then
    echo -e "${GREEN}✅ Aplicação rodando em localhost:8001${NC}"
else
    echo -e "${RED}❌ Aplicação não está rodando${NC}"
    exit 1
fi

# 2. Criar arquivo de teste PFX
echo -e "${BLUE}2. Criando arquivo de teste...${NC}"
echo "Conteúdo simulado de certificado PFX" > /tmp/certificado-teste.pfx
echo -e "${GREEN}✅ Arquivo /tmp/certificado-teste.pfx criado${NC}"

# 3. Testar login do parlamentar
echo -e "${BLUE}3. Testando login parlamentar...${NC}"
LOGIN_RESPONSE=$(curl -s -c /tmp/cookies.txt -X POST \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "email=jessica@sistema.gov.br&password=123456" \
    http://localhost:8001/login 2>/dev/null)

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Login parlamentar executado${NC}"
else
    echo -e "${RED}❌ Erro no login parlamentar${NC}"
fi

# 4. Verificar acesso à página de assinatura
echo -e "${BLUE}4. Verificando acesso à assinatura...${NC}"
CSRF_TOKEN=$(curl -s -b /tmp/cookies.txt http://localhost:8001/proposicoes/1/assinatura-digital | grep -o 'name="_token" value="[^"]*"' | grep -o 'value="[^"]*"' | head -1 | cut -d'"' -f2)

if [ ! -z "$CSRF_TOKEN" ]; then
    echo -e "${GREEN}✅ Página de assinatura acessível${NC}"
    echo -e "${YELLOW}CSRF Token: ${CSRF_TOKEN:0:20}...${NC}"
else
    echo -e "${RED}❌ Não foi possível acessar página de assinatura${NC}"
fi

# 5. Testar assinatura SIMULADO (mais simples)
echo -e "${BLUE}5. Testando assinatura SIMULADO...${NC}"
if [ ! -z "$CSRF_TOKEN" ]; then
    SIMULADO_RESPONSE=$(curl -s -b /tmp/cookies.txt -X POST \
        -H "Content-Type: application/x-www-form-urlencoded" \
        -d "_token=${CSRF_TOKEN}&tipo_certificado=SIMULADO" \
        http://localhost:8001/proposicoes/1/assinatura-digital 2>/dev/null)
    
    if echo "$SIMULADO_RESPONSE" | grep -q "success\|assinada\|sucesso"; then
        echo -e "${GREEN}✅ Assinatura SIMULADO funcionou${NC}"
    elif echo "$SIMULADO_RESPONSE" | grep -q "error\|erro\|fail"; then
        echo -e "${RED}❌ Erro na assinatura SIMULADO${NC}"
        echo "Resposta: $(echo "$SIMULADO_RESPONSE" | head -3)"
    else
        echo -e "${YELLOW}⚠️ Resposta não conclusiva para SIMULADO${NC}"
    fi
else
    echo -e "${RED}❌ Sem CSRF token para teste${NC}"
fi

# 6. Testar assinatura A1 (com senha)
echo -e "${BLUE}6. Testando assinatura A1...${NC}"
if [ ! -z "$CSRF_TOKEN" ]; then
    A1_RESPONSE=$(curl -s -b /tmp/cookies.txt -X POST \
        -H "Content-Type: application/x-www-form-urlencoded" \
        -d "_token=${CSRF_TOKEN}&tipo_certificado=A1&senha=1234" \
        http://localhost:8001/proposicoes/1/assinatura-digital 2>/dev/null)
    
    if echo "$A1_RESPONSE" | grep -q "success\|assinada\|sucesso"; then
        echo -e "${GREEN}✅ Assinatura A1 funcionou${NC}"
    elif echo "$A1_RESPONSE" | grep -q "error\|erro\|fail"; then
        echo -e "${RED}❌ Erro na assinatura A1${NC}"
    else
        echo -e "${YELLOW}⚠️ Resposta não conclusiva para A1${NC}"
    fi
fi

# 7. Verificar métodos otimizados implementados
echo -e "${BLUE}7. Verificando implementação...${NC}"

if grep -q "substr(md5" /home/bruno/legisinc/app/Services/AssinaturaDigitalService.php; then
    echo -e "${GREEN}✅ Identificador otimizado (32 chars) implementado${NC}"
else
    echo -e "${RED}❌ Identificador ainda muito longo${NC}"
fi

if grep -q "json_encode(\$dadosCompactos)" /home/bruno/legisinc/app/Http/Controllers/AssinaturaDigitalController.php; then
    echo -e "${GREEN}✅ JSON compacto implementado${NC}"
else
    echo -e "${RED}❌ JSON ainda muito grande${NC}"
fi

if grep -q "substr(\$checksum, 0, 32)" /home/bruno/legisinc/app/Services/AssinaturaDigitalService.php; then
    echo -e "${GREEN}✅ Checksum encurtado (32 chars) implementado${NC}"
else
    echo -e "${RED}❌ Checksum ainda muito longo${NC}"
fi

# 8. Limpeza
echo -e "${BLUE}8. Limpeza...${NC}"
rm -f /tmp/cookies.txt
echo -e "${GREEN}✅ Cookies temporários removidos${NC}"

echo ""
echo "========================================="
echo -e "${GREEN}🎊 TESTE DE ASSINATURA CONCLUÍDO!${NC}"
echo "========================================="
echo ""
echo "🔍 PRÓXIMOS PASSOS:"
echo "1. Acesse: http://localhost:8001/proposicoes/1/assinatura-digital"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Teste todos os tipos de certificado"
echo "4. Confirme que não há mais erros de banco"
echo ""
echo "Se todos os ✅ aparecerem acima, o sistema está funcionando!"
echo "========================================="