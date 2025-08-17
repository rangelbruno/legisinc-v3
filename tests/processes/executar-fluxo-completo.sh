#!/bin/bash

# Script para executar teste completo do fluxo de proposições
# Sistema Legisinc - Teste de Processo Completo

echo "🏛️  SISTEMA LEGISINC - TESTE DE FLUXO COMPLETO"
echo "=============================================="
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para imprimir mensagens coloridas
print_step() {
    echo -e "${BLUE}📋 $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Verificar se estamos no diretório correto
if [ ! -f "composer.json" ]; then
    print_error "Execute este script a partir da raiz do projeto Legisinc"
    exit 1
fi

# Verificar se o Docker está rodando
if ! docker ps > /dev/null 2>&1; then
    print_error "Docker não está rodando. Inicie o Docker primeiro."
    exit 1
fi

# Verificar se o container da aplicação está rodando
if ! docker ps | grep -q "legisinc-app"; then
    print_error "Container 'legisinc-app' não está rodando. Execute 'docker-compose up -d' primeiro."
    exit 1
fi

print_step "Preparando ambiente de teste..."

# Limpar cache e otimizar aplicação
print_step "Limpando cache da aplicação..."
docker exec -it legisinc-app php artisan cache:clear
docker exec -it legisinc-app php artisan config:clear
docker exec -it legisinc-app php artisan route:clear

print_success "Cache limpo com sucesso"

# Executar migrate fresh com seed para garantir estado limpo
print_step "Preparando banco de dados..."
docker exec -it legisinc-app php artisan migrate:fresh --seed

if [ $? -eq 0 ]; then
    print_success "Banco de dados preparado com sucesso"
else
    print_error "Erro ao preparar banco de dados"
    exit 1
fi

# Verificar se o OnlyOffice está rodando
print_step "Verificando serviços..."
if docker ps | grep -q "onlyoffice"; then
    print_success "OnlyOffice está rodando"
else
    print_warning "OnlyOffice não está rodando. Alguns testes podem falhar."
fi

# Executar o teste de fluxo completo
print_step "Executando teste de fluxo completo..."
echo ""
echo "🚀 INICIANDO EXECUÇÃO DO TESTE..."
echo ""

# Executar o teste usando PHPUnit
docker exec -it legisinc-app php artisan test tests/processes/ProposicaoFluxoCompletoTest.php --verbose

TEST_RESULT=$?

echo ""
echo "📊 RESULTADO DO TESTE:"
echo "======================"

if [ $TEST_RESULT -eq 0 ]; then
    print_success "TESTE EXECUTADO COM SUCESSO!"
    echo ""
    echo "🎯 Todas as etapas do fluxo foram validadas:"
    echo "   1. ✅ Configuração de Templates"
    echo "   2. ✅ Criação de Proposição"
    echo "   3. ✅ Edição pelo Parlamentar"
    echo "   4. ✅ Envio ao Legislativo"
    echo "   5. ✅ Edição pelo Legislativo"
    echo "   6. ✅ Retorno ao Parlamentar"
    echo "   7. ✅ Assinatura Digital"
    echo "   8. ✅ Visualização PDF"
    echo "   9. ✅ Protocolo Oficial"
    echo ""
    print_success "Sistema funcionando corretamente!"
else
    print_error "TESTE FALHOU!"
    echo ""
    echo "🔍 Verifique os logs acima para identificar o problema."
    echo "   Possíveis causas:"
    echo "   - Configuração incorreta do banco"
    echo "   - OnlyOffice não configurado"
    echo "   - Permissões de arquivo"
    echo "   - Configuração de storage"
fi

echo ""
echo "📄 RELATÓRIOS DISPONÍVEIS:"
echo "=========================="
echo "   📊 Visualizador Gráfico: file://$(pwd)/tests/processes/fluxo-visualizer.html"
echo "   📁 Logs do teste: storage/logs/laravel.log"
echo "   🗄️  Banco de dados: Acesse phpMyAdmin ou PostgreSQL"

echo ""
echo "🌐 ACESSOS RÁPIDOS:"
echo "==================="
echo "   🖥️  Aplicação: http://localhost:8001"
echo "   📝 OnlyOffice: http://localhost:8080"
echo "   📊 Visualizador: file://$(pwd)/tests/processes/fluxo-visualizer.html"

echo ""
echo "👥 USUÁRIOS DE TESTE:"
echo "===================="
echo "   🔑 Admin: bruno@sistema.gov.br / 123456"
echo "   👤 Parlamentar: jessica@sistema.gov.br / 123456"
echo "   🏛️  Legislativo: joao@sistema.gov.br / 123456"
echo "   📋 Protocolo: roberto@sistema.gov.br / 123456"

echo ""
print_step "Teste concluído!"

# Abrir o visualizador gráfico se possível
if command -v xdg-open > /dev/null; then
    echo "🚀 Abrindo visualizador gráfico..."
    xdg-open "tests/processes/fluxo-visualizer.html" 2>/dev/null &
elif command -v open > /dev/null; then
    echo "🚀 Abrindo visualizador gráfico..."
    open "tests/processes/fluxo-visualizer.html" 2>/dev/null &
else
    echo "💡 Abra manualmente: tests/processes/fluxo-visualizer.html"
fi

exit $TEST_RESULT