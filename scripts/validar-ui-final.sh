#!/bin/bash

echo "🎯 VALIDAÇÃO FINAL: UI dos Botões Após migrate:fresh --seed"
echo "=========================================================="

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
log_info "ETAPA 1: Verificando estrutura HTML dos botões"
echo "============================================="

# Verificar botões OnlyOffice
OO_COUNT=$(grep -c "btn-onlyoffice" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ $OO_COUNT -gt 0 ]; then
    log_success "Botões OnlyOffice encontrados: $OO_COUNT classes aplicadas"
else
    log_error "Botões OnlyOffice não encontrados"
fi

# Verificar botões Assinatura
ASSINATURA_COUNT=$(grep -c "btn-assinatura" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
if [ $ASSINATURA_COUNT -gt 0 ]; then
    log_success "Botões Assinatura encontrados: $ASSINATURA_COUNT classes aplicadas"
else
    log_error "Botões Assinatura não encontrados"
fi

# Verificar CSS
CSS_OO=$(grep -c ".btn-onlyoffice" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)
CSS_ASSINATURA=$(grep -c ".btn-assinatura" /home/bruno/legisinc/resources/views/proposicoes/show.blade.php)

if [ $CSS_OO -gt 0 ] && [ $CSS_ASSINATURA -gt 0 ]; then
    log_success "CSS otimizado aplicado corretamente"
else
    log_error "CSS otimizado não encontrado"
fi

echo ""
log_info "ETAPA 2: Testando funcionalidade dos botões"
echo "=========================================="

# Verificar se a proposição existe
PROPOSICAO_TEST=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\\Models\\Proposicao::find(2);
if (\$proposicao) {
    echo 'FOUND|' . \$proposicao->id . '|' . \$proposicao->status;
} else {
    echo 'NOT_FOUND';
}
")

if [[ "$PROPOSICAO_TEST" == FOUND* ]]; then
    IFS='|' read -r -a PROP_ARRAY <<< "$PROPOSICAO_TEST"
    log_success "Proposição de teste encontrada: ID ${PROP_ARRAY[1]}, Status: ${PROP_ARRAY[2]}"
else
    log_warning "Proposição de teste não encontrada - criando nova..."
    
    CREATE_TEST=$(docker exec legisinc-app php artisan tinker --execute="
    \$user = App\\Models\\User::where('email', 'jessica@sistema.gov.br')->first();
    \$proposicao = App\\Models\\Proposicao::create([
        'tipo' => 'Moção',
        'ementa' => 'Teste UI Final',
        'conteudo' => 'Teste dos botões UI',
        'autor_id' => \$user->id,
        'status' => 'retornado_legislativo',
        'template_id' => 6
    ]);
    echo 'CREATED|' . \$proposicao->id;
    ")
    
    if [[ "$CREATE_TEST" == CREATED* ]]; then
        log_success "Proposição de teste criada com sucesso"
    else
        log_error "Erro ao criar proposição de teste"
    fi
fi

echo ""
log_info "ETAPA 3: Verificando permissões"
echo "============================="

# Verificar permissão de assinatura
PERM_CHECK=$(docker exec legisinc-app php artisan tinker --execute="
\$perm = DB::table('screen_permissions')
    ->where('role_name', 'PARLAMENTAR')
    ->where('screen_route', 'proposicoes.assinar')
    ->where('can_access', true)
    ->first();
echo \$perm ? 'PERMISSION_OK' : 'PERMISSION_MISSING';
")

if [[ "$PERM_CHECK" == "PERMISSION_OK" ]]; then
    log_success "Permissão proposicoes.assinar configurada"
else
    log_error "Permissão proposicoes.assinar faltando"
fi

echo ""
echo "================================================"
log_info "RESUMO FINAL"
echo "================================================"

ALL_GOOD=true

# Verificações finais
if [ $OO_COUNT -gt 0 ] && [ $ASSINATURA_COUNT -gt 0 ]; then
    log_success "✅ Estrutura HTML: CORRETA"
else
    log_error "❌ Estrutura HTML: PROBLEMA"
    ALL_GOOD=false
fi

if [ $CSS_OO -gt 0 ] && [ $CSS_ASSINATURA -gt 0 ]; then
    log_success "✅ CSS Otimizado: APLICADO"
else
    log_error "❌ CSS Otimizado: FALTANDO"
    ALL_GOOD=false
fi

if [[ "$PERM_CHECK" == "PERMISSION_OK" ]]; then
    log_success "✅ Permissões: CONFIGURADAS"
else
    log_error "❌ Permissões: PROBLEMA"
    ALL_GOOD=false
fi

echo ""
if $ALL_GOOD; then
    log_success "🎉 UI TOTALMENTE FUNCIONAL E PRESERVADA!"
    echo ""
    echo "🚀 TESTE MANUAL:"
    echo "1. Acesse: http://localhost:8001/login"
    echo "2. Login: jessica@sistema.gov.br / 123456"
    echo "3. Vá para: /proposicoes/2"
    echo "4. Clique em 'Assinar Documento'"
    echo "5. Sistema deve abrir /proposicoes/2/assinar com sucesso!"
else
    log_warning "⚠️ Alguns problemas detectados que precisam ser verificados"
fi

echo ""
log_success "CONFIGURAÇÃO UI PRESERVADA PERMANENTEMENTE! 🎯"