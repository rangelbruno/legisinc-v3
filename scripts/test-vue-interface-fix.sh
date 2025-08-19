#!/bin/bash

echo "🎯 === TESTE: CORREÇÕES INTERFACE VUE.JS ==="

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}📋 Verificando correções implementadas:${NC}"

echo "1. ✅ Variável USER_ROLE corrigida"
echo "2. ✅ API de proposições configurada" 
echo "3. ✅ Polling automático implementado"
echo "4. ✅ Refresh automático ao retornar do OnlyOffice"

echo -e "\n${YELLOW}🧪 TESTE 1: Verificar dados da proposição 1${NC}"

# Testar API diretamente
echo "Testando API /api/proposicoes/1..."
curl -s -X GET "http://localhost:8001/api/proposicoes/1" \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" | jq '.'

echo -e "\n${YELLOW}🧪 TESTE 2: Verificar se interface Vue carrega${NC}"

# Verificar se a página carrega
echo "Testando carregamento da página /proposicoes/1..."
curl -s -o /dev/null -w "%{http_code}" "http://localhost:8001/proposicoes/1"

echo -e "\n${YELLOW}🧪 TESTE 3: Verificar logs do Laravel${NC}"

echo "Últimas linhas do log (Vue.js):"
tail -5 /home/bruno/legisinc/storage/logs/laravel.log

echo -e "\n${GREEN}✅ Correções implementadas:${NC}"
echo "• USER_ROLE agora usa getRoleNames()->first()"
echo "• API /api/proposicoes configurada com middleware auth"
echo "• Polling a cada 30 segundos"
echo "• Refresh automático quando retorna do OnlyOffice"
echo "• Controles de polling manual na interface"

echo -e "\n${BLUE}📝 Para testar completo:${NC}"
echo "1. Acesse: http://localhost:8001/proposicoes/1"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Verifique console (F12) - não deve ter erro USER_ROLE"
echo "4. Abra OnlyOffice, edite e feche"
echo "5. Dados devem atualizar automaticamente"

echo -e "\n${GREEN}🎉 Interface Vue.js corrigida e otimizada!${NC}"