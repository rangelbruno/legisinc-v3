#!/bin/bash

# Script de Setup OnlyOffice Integration
# Configura completamente a integração OnlyOffice no sistema Legisinc

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_step() {
    echo -e "${BLUE}📋 $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

echo -e "${BLUE}"
echo "🚀 Setup OnlyOffice Integration - Sistema Legisinc"
echo "================================================="
echo -e "${NC}"

# Verificar se Docker está rodando
print_step "Verificando Docker..."
if ! docker --version > /dev/null 2>&1; then
    print_error "Docker não encontrado. Instale o Docker primeiro."
    exit 1
fi
print_success "Docker instalado"

# Verificar se está no diretório correto
if [ ! -f "docker-compose.yml" ]; then
    print_error "Execute este script no diretório raiz do projeto Legisinc"
    exit 1
fi

# 1. Parar containers existentes (se houver)
print_step "Parando containers existentes..."
docker stop legisinc-onlyoffice 2>/dev/null || true
docker rm legisinc-onlyoffice 2>/dev/null || true
print_success "Containers limpos"

# 2. Criar rede Docker customizada
print_step "Configurando rede Docker..."
if ! docker network ls | grep -q legisinc-network; then
    docker network create legisinc-network
    print_success "Rede legisinc-network criada"
else
    print_warning "Rede legisinc-network já existe"
fi

# 3. Conectar Laravel container à rede (se não estiver)
print_step "Conectando Laravel à rede customizada..."
if docker ps | grep -q legisinc-app; then
    docker network connect legisinc-network legisinc-app 2>/dev/null || print_warning "Laravel já conectado à rede"
    print_success "Laravel container conectado"
else
    print_warning "Container legisinc-app não está rodando. Inicie-o primeiro com docker-compose up"
fi

# 4. Iniciar OnlyOffice container
print_step "Iniciando OnlyOffice Document Server..."
docker run -d --name legisinc-onlyoffice \
    --network legisinc-network \
    -p 8080:80 \
    -e JWT_ENABLED=false \
    -e JWT_SECRET="" \
    -e ALLOW_PRIVATE_IP_ADDRESS=true \
    -e DS_LOG_LEVEL=WARN \
    --restart=unless-stopped \
    onlyoffice/documentserver:8.0

print_success "OnlyOffice container iniciado"

# 5. Aguardar OnlyOffice inicializar
print_step "Aguardando OnlyOffice inicializar..."
for i in {1..60}; do
    if curl -f -s http://localhost:8080/welcome/ > /dev/null 2>&1; then
        break
    fi
    echo -n "."
    sleep 2
done
echo ""

if curl -f -s http://localhost:8080/welcome/ > /dev/null 2>&1; then
    print_success "OnlyOffice inicializado com sucesso"
else
    print_error "OnlyOffice não respondeu após 2 minutos"
    print_warning "Verifique os logs: docker logs legisinc-onlyoffice"
    exit 1
fi

# 6. Verificar conectividade entre containers
print_step "Testando conectividade entre containers..."
sleep 5
if docker exec legisinc-onlyoffice curl -f -s http://172.24.0.2:80 > /dev/null 2>&1; then
    print_success "Conectividade OK"
else
    print_warning "Problema na conectividade - pode ser temporário"
fi

# 7. Limpar cache do Laravel
if docker ps | grep -q legisinc-app; then
    print_step "Limpando cache do Laravel..."
    docker exec legisinc-app php artisan config:clear 2>/dev/null || true
    docker exec legisinc-app php artisan route:clear 2>/dev/null || true
    print_success "Cache limpo"
fi

# 8. Executar diagnóstico inicial
print_step "Executando diagnóstico inicial..."
if [ -f "./scripts/diagnose-onlyoffice.sh" ]; then
    echo ""
    ./scripts/diagnose-onlyoffice.sh
else
    print_warning "Script de diagnóstico não encontrado em ./scripts/diagnose-onlyoffice.sh"
fi

# 9. Resumo final
echo ""
echo -e "${GREEN}🎉 Setup OnlyOffice Concluído!${NC}"
echo "================================="
echo ""
echo "📋 Próximos passos:"
echo "1. Acesse http://localhost:8080 para verificar o OnlyOffice"
echo "2. Teste no sistema: /proposicoes/{id}/preparar-edicao/{template}"
echo "3. Execute diagnósticos: ./scripts/diagnose-onlyoffice.sh"
echo ""
echo "🔧 Comandos úteis:"
echo "   Logs OnlyOffice:    docker logs -f legisinc-onlyoffice"
echo "   Logs Laravel:       docker exec legisinc-app tail -f storage/logs/laravel.log"
echo "   Monitoramento:      ./scripts/monitor-onlyoffice.sh"
echo "   Diagnóstico:        ./scripts/diagnose-onlyoffice.sh"
echo ""
echo "📖 Documentação:"
echo "   Guia de testes:     docs/COMO_TESTAR_ONLYOFFICE.md"
echo "   Troubleshooting:    docs/TROUBLESHOOTING_ONLYOFFICE.md"
echo "   Integração técnica: docs/ONLYOFFICE_INTEGRATION.md"
echo ""

# 10. Verificar se tudo está funcionando
HEALTH_CHECK="OK"
if ! curl -f -s http://localhost:8080/welcome/ > /dev/null 2>&1; then
    HEALTH_CHECK="FAILED"
    print_error "OnlyOffice não está respondendo"
fi

if ! docker ps | grep -q legisinc-onlyoffice; then
    HEALTH_CHECK="FAILED"
    print_error "Container OnlyOffice não está rodando"
fi

if [ "$HEALTH_CHECK" = "OK" ]; then
    echo -e "${GREEN}✅ Sistema pronto para uso!${NC}"
    echo ""
    echo "🎯 Teste imediato:"
    echo "   curl http://localhost:8080/welcome/"
    echo ""
else
    echo -e "${RED}⚠️  Algumas verificações falharam${NC}"
    echo "   Execute: docker logs legisinc-onlyoffice"
    echo "   Consulte: docs/TROUBLESHOOTING_ONLYOFFICE.md"
    echo ""
fi