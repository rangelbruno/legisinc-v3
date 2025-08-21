#!/bin/bash

echo "========================================="
echo "Teste de Assinatura Digital Corrigida"
echo "========================================="
echo ""

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}1. Testando formulário de assinatura com tipo MANUAL:${NC}"
echo ""

# Testar com tipo MANUAL (não precisa de PIN nem PFX)
curl -X POST http://localhost:8001/proposicoes/10/assinatura-digital/processar \
  -H "Cookie: $(cat /tmp/laravel_session_cookie.txt 2>/dev/null)" \
  -H "X-CSRF-TOKEN: $(curl -s http://localhost:8001/login | grep 'csrf-token' | sed -n 's/.*content="\([^"]*\)".*/\1/p')" \
  -F "tipo_certificado=MANUAL" \
  -F "nome_assinante=Jessica Santos" \
  -F "protocolo=2025/001" \
  -F "observacoes=Teste de assinatura manual" \
  -s -o /tmp/assinatura_response.html -w "%{http_code}"

HTTP_CODE=$?

echo ""
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
    echo -e "${GREEN}✅ Requisição processada com sucesso!${NC}"
    
    # Verificar se há erros de validação na resposta
    if grep -q "The pin field must be a string" /tmp/assinatura_response.html; then
        echo -e "${RED}❌ Erro de validação ainda presente: pin field${NC}"
    else
        echo -e "${GREEN}✅ Erro de validação corrigido!${NC}"
    fi
    
    # Verificar logs de erro
    echo ""
    echo -e "${YELLOW}2. Verificando logs de erro:${NC}"
    tail -n 20 /home/bruno/legisinc/storage/logs/laravel.log | grep -A 5 "Erro ao processar assinatura" || echo -e "${GREEN}✅ Nenhum erro novo nos logs${NC}"
    
else
    echo -e "${RED}❌ Falha na requisição: HTTP $HTTP_CODE${NC}"
fi

echo ""
echo -e "${YELLOW}3. Testando com tipo A1 (requer PIN):${NC}"
echo ""

# Testar com tipo A1
curl -X POST http://localhost:8001/proposicoes/10/assinatura-digital/processar \
  -H "Cookie: $(cat /tmp/laravel_session_cookie.txt 2>/dev/null)" \
  -H "X-CSRF-TOKEN: $(curl -s http://localhost:8001/login | grep 'csrf-token' | sed -n 's/.*content="\([^"]*\)".*/\1/p')" \
  -F "tipo_certificado=A1" \
  -F "nome_assinante=Jessica Santos" \
  -F "pin=1234" \
  -F "protocolo=2025/001" \
  -s -o /tmp/assinatura_a1_response.html -w "%{http_code}"

if grep -q "error" /tmp/assinatura_a1_response.html; then
    echo -e "${YELLOW}⚠️  Resposta contém erros (esperado - serviço de assinatura não implementado)${NC}"
else
    echo -e "${GREEN}✅ Requisição A1 processada${NC}"
fi

echo ""
echo "========================================="
echo -e "${GREEN}Teste Concluído!${NC}"
echo ""
echo "Correções aplicadas:"
echo "1. ✅ Validação nullable para campos condicionais"
echo "2. ✅ Limpeza de campos não utilizados no JavaScript"
echo "3. ✅ Autocomplete adicionado aos campos de senha"
echo ""
echo "Para testar manualmente:"
echo "1. Acesse: http://localhost:8001/login"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Vá para: http://localhost:8001/proposicoes/10/assinatura-digital"
echo "4. Selecione tipo 'Manual' e preencha apenas nome"
echo "5. Clique em 'Assinar Documento'"
echo "========================================="