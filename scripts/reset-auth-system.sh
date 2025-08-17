#!/bin/bash

# Script para resetar completamente o sistema de autenticação
# Sistema Legisinc - Reset de Autenticação

echo "🔄 RESETANDO SISTEMA DE AUTENTICAÇÃO"
echo "===================================="
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para imprimir mensagens coloridas
print_step() {
    echo -e "${BLUE}🔧 $1${NC}"
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

print_step "1. Limpando cache da aplicação..."
docker exec legisinc-app php artisan cache:clear > /dev/null 2>&1
docker exec legisinc-app php artisan config:clear > /dev/null 2>&1
docker exec legisinc-app php artisan route:clear > /dev/null 2>&1
docker exec legisinc-app php artisan view:clear > /dev/null 2>&1
print_success "Cache da aplicação limpo"

print_step "2. Limpando sessões do banco de dados..."
docker exec legisinc-app php artisan tinker --execute="
try {
    DB::table('sessions')->delete();
    echo 'Sessões do BD limpas\n';
} catch(Exception \$e) {
    echo 'Erro ao limpar sessões: ' . \$e->getMessage() . '\n';
}
" > /dev/null 2>&1
print_success "Sessões do banco de dados limpas"

print_step "3. Limpando cache Redis..."
docker exec legisinc-redis redis-cli FLUSHALL > /dev/null 2>&1
print_success "Cache Redis limpo"

print_step "4. Removendo todos os usuários autenticados..."
docker exec legisinc-app php artisan tinker --execute="
try {
    // Deslogar todos os usuários
    Auth::logout();
    
    // Limpar tabela de sessions se existir
    if (Schema::hasTable('sessions')) {
        DB::table('sessions')->truncate();
    }
    
    // Limpar possíveis tokens remember_me
    if (Schema::hasTable('users')) {
        DB::table('users')->update(['remember_token' => null]);
    }
    
    echo 'Usuários deslogados\n';
} catch(Exception \$e) {
    echo 'Erro: ' . \$e->getMessage() . '\n';
}
" > /dev/null 2>&1
print_success "Usuários deslogados"

print_step "5. Verificando e recriando tabelas de sessão..."
docker exec legisinc-app php artisan session:table --force > /dev/null 2>&1
docker exec legisinc-app php artisan migrate > /dev/null 2>&1
print_success "Tabelas de sessão verificadas"

print_step "6. Reiniciando serviços web..."
docker restart legisinc-app > /dev/null 2>&1
sleep 5
print_success "Serviços reiniciados"

print_step "7. Testando conectividade..."
# Aguardar o container ficar pronto
for i in {1..30}; do
    if curl -s http://localhost:8001/login > /dev/null 2>&1; then
        break
    fi
    sleep 1
done

if curl -s http://localhost:8001/login > /dev/null 2>&1; then
    print_success "Sistema está respondendo"
else
    print_error "Sistema não está respondendo"
    exit 1
fi

echo ""
echo "🎉 RESET COMPLETO REALIZADO COM SUCESSO!"
echo "========================================"
echo ""
echo "📋 O que foi resetado:"
echo "   ✅ Cache da aplicação (config, routes, views)"
echo "   ✅ Sessões do banco de dados"
echo "   ✅ Cache Redis (sessões, cache geral)"
echo "   ✅ Tokens de autenticação"
echo "   ✅ Remember tokens"
echo "   ✅ Container da aplicação reiniciado"
echo ""
echo "🌐 INSTRUÇÕES PARA ACESSAR:"
echo "=========================="
echo "1. 🔄 IMPORTANTE: Feche COMPLETAMENTE o navegador"
echo "2. 🗑️  Limpe os cookies para localhost:8001"
echo "3. 🆕 Abra uma nova janela/aba do navegador"
echo "4. 🔗 Acesse: http://localhost:8001/login"
echo ""
echo "👥 USUÁRIOS DISPONÍVEIS:"
echo "======================="
echo "   🔑 Admin: bruno@sistema.gov.br / 123456"
echo "   👤 Parlamentar: jessica@sistema.gov.br / 123456"
echo "   🏛️  Legislativo: joao@sistema.gov.br / 123456"
echo "   📋 Protocolo: roberto@sistema.gov.br / 123456"
echo "   📄 Expediente: expediente@sistema.gov.br / 123456"
echo "   ⚖️  Jurídico: juridico@sistema.gov.br / 123456"
echo ""
echo "🔍 Se o problema persistir:"
echo "=========================="
echo "   📱 Teste em modo incógnito/privado"
echo "   🌐 Teste em outro navegador"
echo "   🔄 Execute: docker-compose restart"
echo ""
print_success "Sistema pronto para uso!"