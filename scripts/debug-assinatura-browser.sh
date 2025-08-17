#!/bin/bash

echo "🔍 DEBUG: Problema do botão Assinar Documento"
echo "============================================"

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }
log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }

echo ""
log_info "1. Verificando estado atual do sistema..."

# Estado do sistema
SYSTEM_CHECK=$(docker exec legisinc-app php artisan tinker --execute="
\$prop = App\\Models\\Proposicao::find(1);
\$jessica = App\\Models\\User::find(6);
\$perm = DB::table('screen_permissions')
    ->where('role_name', 'PARLAMENTAR')
    ->where('screen_route', 'proposicoes.assinar')
    ->first();
    
echo 'PROP_STATUS:' . (\$prop ? \$prop->status : 'NAO_EXISTE') . '|';
echo 'JESSICA_EXISTS:' . (\$jessica ? 'SIM' : 'NAO') . '|';
echo 'PERMISSION:' . (\$perm && \$perm->can_access ? 'SIM' : 'NAO');
")

IFS='|' read -r -a CHECK_ARRAY <<< "$SYSTEM_CHECK"
PROP_STATUS="${CHECK_ARRAY[0]#PROP_STATUS:}"
JESSICA_EXISTS="${CHECK_ARRAY[1]#JESSICA_EXISTS:}"
PERMISSION="${CHECK_ARRAY[2]#PERMISSION:}"

if [[ "$PROP_STATUS" == "retornado_legislativo" ]]; then
    log_success "Proposição status: $PROP_STATUS (permite assinatura)"
else
    log_error "Proposição status: $PROP_STATUS (não permite assinatura)"
fi

if [[ "$JESSICA_EXISTS" == "SIM" ]]; then
    log_success "Usuário Jessica: existe"
else
    log_error "Usuário Jessica: não existe"
fi

if [[ "$PERMISSION" == "SIM" ]]; then
    log_success "Permissão proposicoes.assinar: existe"
else
    log_error "Permissão proposicoes.assinar: falta"
fi

echo ""
log_info "2. Testando URLs de acesso..."

# Teste básico de conectividade
log_info "Testando conectividade básica..."
LOGIN_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/login)
if [[ "$LOGIN_RESPONSE" == "200" ]]; then
    log_success "Página de login acessível (HTTP $LOGIN_RESPONSE)"
else
    log_error "Página de login com problema (HTTP $LOGIN_RESPONSE)"
fi

# Teste de acesso direto (sem autenticação)
ASSINAR_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8001/proposicoes/1/assinar)
if [[ "$ASSINAR_RESPONSE" == "302" ]]; then
    log_success "Rota assinar redireciona para login (HTTP $ASSINAR_RESPONSE) - comportamento correto"
elif [[ "$ASSINAR_RESPONSE" == "200" ]]; then
    log_warning "Rota assinar acessível sem login (HTTP $ASSINAR_RESPONSE) - possível problema de segurança"
else
    log_error "Rota assinar com erro (HTTP $ASSINAR_RESPONSE)"
fi

echo ""
log_info "3. Verificando view de assinatura..."

# Verificar se a view existe
ASSINAR_VIEW="/home/bruno/legisinc/resources/views/proposicoes/assinatura/assinar.blade.php"
if [ -f "$ASSINAR_VIEW" ]; then
    log_success "View assinatura existe: $(basename $ASSINAR_VIEW)"
    
    # Verificar se não tem erros de sintaxe básicos
    if grep -q "@extends" "$ASSINAR_VIEW" && grep -q "@section" "$ASSINAR_VIEW"; then
        log_success "View tem estrutura Blade correta"
    else
        log_warning "View pode ter problemas de estrutura Blade"
    fi
else
    log_error "View assinatura não encontrada: $ASSINAR_VIEW"
fi

echo ""
log_info "4. Criando teste de navegação simples..."

# Criar arquivo HTML de teste
cat > /tmp/test_navigation.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Navegação</title>
</head>
<body>
    <h2>Teste de Navegação - Assinar Documento</h2>
    
    <p><strong>Teste 1:</strong> Link direto</p>
    <a href="http://localhost:8001/proposicoes/1/assinar" target="_blank">
        🔗 Acessar /proposicoes/1/assinar diretamente
    </a>
    
    <p><strong>Teste 2:</strong> Primeiro fazer login</p>
    <a href="http://localhost:8001/login" target="_blank">
        🔐 1. Fazer login primeiro (jessica@sistema.gov.br / 123456)
    </a>
    <br><br>
    <a href="http://localhost:8001/proposicoes/1/assinar" target="_blank">
        ✍️ 2. Depois acessar assinar (em nova aba)
    </a>
    
    <p><strong>Teste 3:</strong> Via proposições</p>
    <a href="http://localhost:8001/proposicoes/1" target="_blank">
        📄 Acessar /proposicoes/1 e clicar no botão Assinar
    </a>
    
    <hr>
    <p><em>Abra este arquivo em um navegador e teste os links:</em></p>
    <code>file:///tmp/test_navigation.html</code>
</body>
</html>
EOF

log_success "Arquivo de teste criado: /tmp/test_navigation.html"

echo ""
log_info "5. Soluções possíveis se o problema persistir..."

echo ""
echo "🔧 POSSÍVEIS CAUSAS E SOLUÇÕES:"
echo "================================"
echo ""
echo "1. 📱 CACHE DO BROWSER:"
echo "   - Pressione Ctrl+F5 para limpar cache"
echo "   - Ou abra em modo incógnito"
echo ""
echo "2. 🔐 PROBLEMA DE SESSÃO:"
echo "   - Faça logout completo e login novamente"
echo "   - Verifique se está logado como jessica@sistema.gov.br"
echo ""
echo "3. 🐛 JAVASCRIPT INTERFERINDO:"
echo "   - Abra Developer Tools (F12)"
echo "   - Verifique Console por erros JavaScript"
echo "   - Verifique Network por requests bloqueados"
echo ""
echo "4. 🌐 TESTE DE CONECTIVIDADE:"
echo "   - Abra: /tmp/test_navigation.html no navegador"
echo "   - Teste os links um por um"
echo ""
echo "5. 🔍 DEBUG AVANÇADO:"
echo "   - Abra Network tab no DevTools"
echo "   - Clique no botão Assinar Documento"
echo "   - Veja se há algum request sendo feito"
echo ""

if [[ "$PROP_STATUS" == "retornado_legislativo" && "$JESSICA_EXISTS" == "SIM" && "$PERMISSION" == "SIM" ]]; then
    log_success "Sistema backend está correto. Problema é no frontend/browser."
    echo ""
    echo "✅ PRÓXIMOS PASSOS:"
    echo "1. Abra o navegador em modo incógnito"
    echo "2. Acesse: http://localhost:8001/login"
    echo "3. Login: jessica@sistema.gov.br / 123456"
    echo "4. Acesse: http://localhost:8001/proposicoes/1/assinar"
    echo "5. Se ainda não funcionar, verifique Console no DevTools (F12)"
else
    log_error "Sistema backend tem problemas. Execute:"
    echo "   docker exec -it legisinc-app php artisan migrate:fresh --seed"
fi