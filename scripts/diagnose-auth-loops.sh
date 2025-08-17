#!/bin/bash

# Script para diagnosticar problemas de loop de redirecionamento
# Sistema Legisinc - Diagnóstico de Autenticação

echo "🔍 DIAGNÓSTICO DE LOOPS DE REDIRECIONAMENTO"
echo "==========================================="
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_step() {
    echo -e "${BLUE}🔧 $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ️  $1${NC}"
}

# 1. Teste de conectividade básica
print_step "1. Testando conectividade básica..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/login | grep -q "200"; then
    print_success "Servidor está respondendo (HTTP 200)"
else
    print_error "Servidor não está respondendo corretamente"
    exit 1
fi

# 2. Teste de redirecionamentos
print_step "2. Testando redirecionamentos..."
REDIRECTS=$(curl -s -L -o /dev/null -w "%{num_redirects}" http://localhost:8001/login)
echo "   📊 Número de redirecionamentos: $REDIRECTS"

if [ "$REDIRECTS" -gt 5 ]; then
    print_error "Muitos redirecionamentos detectados ($REDIRECTS)!"
    print_info "Isso indica um loop de redirecionamento"
else
    print_success "Redirecionamentos normais ($REDIRECTS)"
fi

# 3. Verificar headers de resposta
print_step "3. Analisando headers de resposta..."
HEADERS=$(curl -s -I http://localhost:8001/login)
echo "$HEADERS" | grep -E "(Location|Set-Cookie|Cache-Control)" | while read line; do
    echo "   📋 $line"
done

# 4. Verificar se há sessões ativas
print_step "4. Verificando sessões ativas..."
ACTIVE_SESSIONS=$(docker exec legisinc-app php artisan tinker --execute="echo DB::table('sessions')->count();" 2>/dev/null | tail -n 1)
echo "   📊 Sessões ativas no banco: $ACTIVE_SESSIONS"

# 5. Verificar usuários autenticados
print_step "5. Verificando usuários com remember_token..."
REMEMBER_TOKENS=$(docker exec legisinc-app php artisan tinker --execute="echo DB::table('users')->whereNotNull('remember_token')->count();" 2>/dev/null | tail -n 1)
echo "   📊 Usuários com remember_token: $REMEMBER_TOKENS"

# 6. Testar com diferentes user agents
print_step "6. Testando com diferentes User-Agents..."

echo "   🌐 Chrome:"
CHROME_CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36" http://localhost:8001/login)
echo "      HTTP: $CHROME_CODE"

echo "   🦊 Firefox:"
FIREFOX_CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0" http://localhost:8001/login)
echo "      HTTP: $FIREFOX_CODE"

# 7. Verificar rotas disponíveis
print_step "7. Verificando rotas de autenticação..."
docker exec legisinc-app php artisan route:list | grep -E "(login|logout|dashboard)" | while read line; do
    echo "   🛣️  $line"
done

# 8. Verificar logs recentes
print_step "8. Verificando logs recentes (últimas 10 linhas)..."
docker exec legisinc-app tail -n 10 storage/logs/laravel.log 2>/dev/null | while read line; do
    echo "   📋 $line"
done

# 9. Teste com cookies limpos
print_step "9. Testando com sessão limpa..."
rm -f /tmp/test_cookies.txt
CLEAN_CODE=$(curl -s -o /dev/null -w "%{http_code}" -c /tmp/test_cookies.txt -b /tmp/test_cookies.txt http://localhost:8001/login)
echo "   📊 HTTP com cookies limpos: $CLEAN_CODE"

# 10. Verificar configurações de sessão
print_step "10. Verificando configurações de sessão..."
docker exec legisinc-app php artisan tinker --execute="
echo 'SESSION_DRIVER: ' . config('session.driver') . '\n';
echo 'SESSION_LIFETIME: ' . config('session.lifetime') . '\n';
echo 'SESSION_DOMAIN: ' . config('session.domain') . '\n';
echo 'APP_URL: ' . config('app.url') . '\n';
" 2>/dev/null | while read line; do
    echo "   ⚙️  $line"
done

echo ""
echo "📊 RESUMO DO DIAGNÓSTICO"
echo "======================="

if [ "$REDIRECTS" -gt 5 ]; then
    echo "❌ PROBLEMA DETECTADO: Loop de redirecionamento"
    echo ""
    echo "🔧 SOLUÇÕES RECOMENDADAS:"
    echo "========================"
    echo "1. 🧹 Limpe TODOS os cookies do navegador para localhost:8001"
    echo "2. 🔄 Feche o navegador completamente e reabra"
    echo "3. 🕵️  Teste em modo incógnito/privado"
    echo "4. 🌐 Teste em outro navegador"
    echo "5. 🔄 Execute: ./scripts/reset-auth-system.sh"
    echo ""
    echo "💡 CAUSA PROVÁVEL:"
    echo "   - Cookie de sessão corrompido"
    echo "   - Sessão persistente inválida"
    echo "   - Middleware causando loop"
else
    print_success "Sistema funcionando normalmente"
    echo ""
    echo "🎯 Se ainda há problemas no navegador:"
    echo "   1. Limpe cookies específicos para localhost:8001"
    echo "   2. Teste em modo incógnito"
    echo "   3. Verifique se não há extensões interferindo"
fi

echo ""
print_info "Para mais ajuda, execute: ./scripts/reset-auth-system.sh"