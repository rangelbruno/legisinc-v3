#!/bin/bash

# Script de Monitoramento OnlyOffice Integration
# Uso: ./monitor-onlyoffice.sh
# Para monitoramento contínuo: watch -n 30 ./monitor-onlyoffice.sh

LOGFILE="/tmp/onlyoffice-monitor.log"
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log_message() {
    echo "[$TIMESTAMP] $1" >> $LOGFILE
    echo -e "$1"
}

check_health() {
    local service=$1
    local command=$2
    local description=$3
    
    if eval $command &>/dev/null; then
        log_message "${GREEN}✅ $description${NC}"
        return 0
    else
        log_message "${RED}❌ $description${NC}"
        return 1
    fi
}

echo "🔄 OnlyOffice Health Monitor - $TIMESTAMP"
echo "=============================================="

# Verificações básicas
HEALTH_SCORE=0
TOTAL_CHECKS=6

# 1. Containers rodando
if check_health "containers" "docker ps | grep -q legisinc-app && docker ps | grep -q legisinc-onlyoffice" "Containers rodando"; then
    ((HEALTH_SCORE++))
fi

# 2. OnlyOffice web acessível
if check_health "web" "curl -f -s http://localhost:8080/welcome/" "OnlyOffice web interface"; then
    ((HEALTH_SCORE++))
fi

# 3. Conectividade entre containers
LARAVEL_IP="172.24.0.2"  # IP conhecido da rede customizada
if [ ! -z "$LARAVEL_IP" ]; then
    if check_health "connectivity" "docker exec legisinc-onlyoffice curl -f -s http://$LARAVEL_IP:80" "Conectividade entre containers"; then
        ((HEALTH_SCORE++))
    fi
else
    log_message "${YELLOW}⚠️  Não foi possível verificar conectividade - IP não encontrado${NC}"
fi

# 4. Callback endpoint
if [ ! -z "$LARAVEL_IP" ]; then
    if check_health "callback" "docker exec legisinc-onlyoffice curl -f -s -X POST -H 'Content-Type: application/json' -d '{\"status\":0}' http://$LARAVEL_IP:80/api/onlyoffice/callback/proposicao/test" "Callback endpoint"; then
        ((HEALTH_SCORE++))
    fi
else
    log_message "${YELLOW}⚠️  Não foi possível verificar callback - IP não encontrado${NC}"
fi

# 5. Storage acessível
if check_health "storage" "docker exec legisinc-app ls storage/app/public/proposicoes/" "Storage de proposições"; then
    ((HEALTH_SCORE++))
fi

# 6. Logs sem erros críticos
RECENT_ERRORS=$(docker logs legisinc-onlyoffice 2>&1 | tail -50 | grep -i "error\|exception\|failed" | wc -l)
if [ $RECENT_ERRORS -eq 0 ]; then
    check_health "logs" "true" "Sem erros recentes no OnlyOffice"
    ((HEALTH_SCORE++))
else
    log_message "${RED}❌ Encontrados $RECENT_ERRORS erros recentes no OnlyOffice${NC}"
fi

# Cálculo do score de saúde
HEALTH_PERCENTAGE=$((HEALTH_SCORE * 100 / TOTAL_CHECKS))

echo ""
echo "📊 Health Score: $HEALTH_SCORE/$TOTAL_CHECKS ($HEALTH_PERCENTAGE%)"

if [ $HEALTH_PERCENTAGE -eq 100 ]; then
    echo -e "${GREEN}🎉 Sistema OnlyOffice funcionando perfeitamente!${NC}"
elif [ $HEALTH_PERCENTAGE -ge 80 ]; then
    echo -e "${YELLOW}⚠️  Sistema OnlyOffice funcionando com pequenos problemas${NC}"
else
    echo -e "${RED}🚨 Sistema OnlyOffice com problemas significativos${NC}"
fi

# Estatísticas adicionais
echo ""
echo "📈 Estatísticas:"
LARAVEL_LOGS=$(docker exec legisinc-app grep -c "OnlyOffice\|onlyoffice" storage/logs/laravel.log 2>/dev/null || echo "0")
echo "   - Logs OnlyOffice no Laravel: $LARAVEL_LOGS"

PROPOSICOES_COUNT=$(docker exec legisinc-app ls storage/app/public/proposicoes/ 2>/dev/null | wc -l)
echo "   - Arquivos de proposições: $PROPOSICOES_COUNT"

CONTAINER_UPTIME=$(docker inspect legisinc-onlyoffice --format='{{.State.StartedAt}}' 2>/dev/null || echo "N/A")
echo "   - OnlyOffice iniciado em: $CONTAINER_UPTIME"

# Recomendações baseadas no health score
echo ""
echo "💡 Recomendações:"
if [ $HEALTH_PERCENTAGE -lt 100 ]; then
    echo "   - Execute ./diagnose-onlyoffice.sh para diagnóstico detalhado"
    echo "   - Verifique logs: docker logs legisinc-onlyoffice"
    echo "   - Consulte docs/TROUBLESHOOTING_ONLYOFFICE.md"
fi

if [ $HEALTH_PERCENTAGE -lt 50 ]; then
    echo "   - Considere reiniciar containers: docker restart legisinc-onlyoffice legisinc-app"
    echo "   - Verifique configuração de rede Docker"
fi

echo ""
echo "📋 Log completo salvo em: $LOGFILE"