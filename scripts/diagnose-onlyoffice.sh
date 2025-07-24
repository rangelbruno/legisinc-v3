#!/bin/bash

# Script de Diagnóstico OnlyOffice Integration
# Uso: ./diagnose-onlyoffice.sh [proposicao_id] [template_id]

set -e

PROPOSICAO_ID=${1:-4169}
TEMPLATE_ID=${2:-11}
ARQUIVO="proposicao_${PROPOSICAO_ID}_template_${TEMPLATE_ID}.docx"

echo "🔍 Diagnóstico OnlyOffice Integration"
echo "======================================="
echo "Proposição ID: $PROPOSICAO_ID"
echo "Template ID: $TEMPLATE_ID"
echo "Arquivo: $ARQUIVO"
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ $1${NC}"
    else
        echo -e "${RED}❌ $1${NC}"
        return 1
    fi
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "ℹ️  $1"
}

# 1. Verificar containers
echo "1. Verificando containers..."
docker ps | grep -q legisinc-app
print_status "Container Laravel (legisinc-app) rodando"

docker ps | grep -q legisinc-onlyoffice
print_status "Container OnlyOffice (legisinc-onlyoffice) rodando"

# 2. Verificar rede Docker
echo ""
echo "2. Verificando rede Docker..."
docker network ls | grep -q legisinc-network
print_status "Rede legisinc-network existe"

# Obter IPs dos containers (método simples e confiável)
LARAVEL_IP="172.24.0.2"  # IP conhecido da rede customizada
ONLYOFFICE_IP="172.24.0.3"  # IP conhecido da rede customizada

# Verificar se IPs estão corretos testando conectividade
docker exec legisinc-app curl -s --connect-timeout 2 http://$ONLYOFFICE_IP:80 > /dev/null 2>&1 || {
    print_warning "IP do OnlyOffice pode estar incorreto, tentando descobrir..."
    ONLYOFFICE_IP=$(docker inspect legisinc-onlyoffice --format='{{.NetworkSettings.IPAddress}}' 2>/dev/null || echo "")
}

docker exec legisinc-onlyoffice curl -s --connect-timeout 2 http://$LARAVEL_IP:80 > /dev/null 2>&1 || {
    print_warning "IP do Laravel pode estar incorreto, tentando descobrir..."
    LARAVEL_IP=$(docker inspect legisinc-app --format='{{.NetworkSettings.IPAddress}}' 2>/dev/null || echo "")
}

print_info "Laravel IP: $LARAVEL_IP"
print_info "OnlyOffice IP: $ONLYOFFICE_IP"

# 3. Verificar acessibilidade do OnlyOffice
echo ""
echo "3. Verificando acessibilidade do OnlyOffice..."
curl -f -s http://localhost:8080/welcome/ > /dev/null
print_status "OnlyOffice acessível via localhost:8080"

# 4. Verificar conectividade entre containers
echo ""
echo "4. Verificando conectividade entre containers..."
if [ ! -z "$LARAVEL_IP" ]; then
    docker exec legisinc-onlyoffice curl -f -s "http://$LARAVEL_IP:80" > /dev/null
    print_status "OnlyOffice consegue acessar Laravel"
else
    print_warning "Não foi possível determinar IP do Laravel"
fi

# 5. Verificar arquivo específico
echo ""
echo "5. Verificando acesso ao arquivo..."
if [ ! -z "$LARAVEL_IP" ]; then
    HTTP_STATUS=$(docker exec legisinc-onlyoffice curl -s -o /dev/null -w "%{http_code}" "http://$LARAVEL_IP:80/onlyoffice/file/proposicao/$PROPOSICAO_ID/$ARQUIVO")
    if [ "$HTTP_STATUS" = "200" ]; then
        print_status "Arquivo acessível (HTTP $HTTP_STATUS)"
    else
        echo -e "${RED}❌ Arquivo não acessível (HTTP $HTTP_STATUS)${NC}"
    fi
else
    print_warning "Pulando teste de arquivo - IP não disponível"
fi

# 6. Verificar callback
echo ""
echo "6. Verificando callback..."
if [ ! -z "$LARAVEL_IP" ]; then
    HTTP_STATUS=$(docker exec legisinc-onlyoffice curl -s -o /dev/null -w "%{http_code}" -X POST -H "Content-Type: application/json" -d '{"status":0}' "http://$LARAVEL_IP:80/api/onlyoffice/callback/proposicao/$PROPOSICAO_ID")
    if [ "$HTTP_STATUS" = "200" ]; then
        print_status "Callback funcionando (HTTP $HTTP_STATUS)"
    else
        echo -e "${RED}❌ Callback com problema (HTTP $HTTP_STATUS)${NC}"
    fi
else
    print_warning "Pulando teste de callback - IP não disponível"
fi

# 7. Verificar logs recentes
echo ""
echo "7. Logs recentes do Laravel..."
docker exec legisinc-app tail -5 storage/logs/laravel.log | grep -i onlyoffice || print_info "Nenhum log OnlyOffice recente"

echo ""
echo "8. Logs recentes do OnlyOffice..."
docker logs legisinc-onlyoffice 2>&1 | tail -10 | grep -E "(Error|error|ERROR)" || print_info "Nenhum erro recente no OnlyOffice"

# 9. Verificar arquivos no storage
echo ""
echo "9. Verificando arquivos no storage..."
STORAGE_FILES=$(docker exec legisinc-app ls -la storage/app/public/proposicoes/ | grep "$PROPOSICAO_ID" | wc -l)
print_info "Arquivos da proposição $PROPOSICAO_ID no storage: $STORAGE_FILES"

# 10. Resumo de configuração
echo ""
echo "10. Resumo da configuração atual..."
print_info "URLs esperados na configuração JavaScript:"
echo "   Document URL: http://$LARAVEL_IP:80/onlyoffice/file/proposicao/$PROPOSICAO_ID/$ARQUIVO"
echo "   Callback URL: http://$LARAVEL_IP:80/api/onlyoffice/callback/proposicao/$PROPOSICAO_ID"

echo ""
echo "🎯 Diagnóstico concluído!"
echo ""
echo "📋 Para mais detalhes, consulte:"
echo "   - docs/TROUBLESHOOTING_ONLYOFFICE.md"
echo "   - docker logs legisinc-onlyoffice"
echo "   - docker exec legisinc-app tail -f storage/logs/laravel.log"