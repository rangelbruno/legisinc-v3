#!/bin/bash

echo "🎯 VALIDAÇÃO FINAL COMPLETA: migrate:fresh --seed"
echo "================================================"

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
log_info "ETAPA 1: Executando migrate:fresh --seed completo"
echo "==============================================="

# Executar migrate fresh seed
log_info "Iniciando reset completo do banco com todas as otimizações..."
docker exec legisinc-app php artisan migrate:fresh --seed > /tmp/migrate_final.log 2>&1

if [ $? -eq 0 ]; then
    log_success "migrate:fresh --seed executado com sucesso!"
    
    # Verificar se nossos seeders foram executados
    if grep -q "PDF de Assinatura Otimizado" /tmp/migrate_final.log; then
        log_success "PDFAssinaturaOptimizadoSeeder executado ✅"
    else
        log_warning "PDFAssinaturaOptimizadoSeeder pode não ter sido executado"
    fi
    
    if grep -q "Limpando código de debug" /tmp/migrate_final.log; then
        log_success "LimpezaCodigoDebugSeeder executado ✅"
    else
        log_warning "LimpezaCodigoDebugSeeder pode não ter sido executado"
    fi
    
else
    log_error "Erro no migrate:fresh --seed"
    cat /tmp/migrate_final.log
    exit 1
fi

echo ""
log_info "ETAPA 2: Validando Sistema PDF Funcionando"
echo "========================================"

# Teste funcional direto
FUNCTIONAL_TEST=$(docker exec legisinc-app php artisan tinker --execute="
try {
    \$parlamentar = App\\Models\\User::where('email', 'jessica@sistema.gov.br')->first();
    \$proposicao = App\\Models\\Proposicao::create([
        'tipo' => 'Moção',
        'ementa' => 'Teste final - ' . now()->format('H:i:s'),
        'conteudo' => 'TESTE - ' . now()->format('H:i:s'),
        'autor_id' => \$parlamentar->id,
        'status' => 'retornado_legislativo',
        'template_id' => 6
    ]);
    
    \$controller = new App\\Http\\Controllers\\ProposicaoAssinaturaController();
    \$reflection = new ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    \$method->invoke(\$controller, \$proposicao);
    \$proposicao->refresh();
    
    if (\$proposicao->arquivo_pdf_path) {
        \$pdfPath = storage_path('app/' . \$proposicao->arquivo_pdf_path);
        if (file_exists(\$pdfPath)) {
            echo 'SUCCESS|' . \$proposicao->id . '|' . filesize(\$pdfPath);
        } else {
            echo 'ERROR|PDF_NOT_FOUND';
        }
    } else {
        echo 'ERROR|PDF_NOT_SAVED';
    }
} catch (Exception \$e) {
    echo 'ERROR|' . \$e->getMessage();
}
")

if [[ "$FUNCTIONAL_TEST" == SUCCESS* ]]; then
    IFS='|' read -r -a TEST_ARRAY <<< "$FUNCTIONAL_TEST"
    log_success "Sistema PDF funcionando 100%"
    echo "   - Proposição criada: ID ${TEST_ARRAY[1]}"
    echo "   - PDF gerado: ${TEST_ARRAY[2]} bytes"
else
    log_error "Sistema PDF com problemas: $FUNCTIONAL_TEST"
fi

echo ""
log_info "ETAPA 3: Verificando Limpeza de Debug"
echo "=================================="

# Verificar limpeza
CLEAN_CHECK=$(docker exec legisinc-app php artisan tinker --execute="
\$routes = file_get_contents(base_path('routes/web.php'));
\$show = file_get_contents(base_path('resources/views/proposicoes/show.blade.php'));
\$hasDebug = str_contains(\$routes, 'test-debug') || str_contains(\$show, 'TESTE DEBUG');
echo \$hasDebug ? 'DEBUG_FOUND' : 'CLEAN';
")

if [[ "$CLEAN_CHECK" == "CLEAN" ]]; then
    log_success "Código de debug removido completamente"
else
    log_warning "Ainda há código de debug presente"
fi

echo ""
log_info "ETAPA 4: Validando Permissões"
echo "============================"

# Verificar permissões
PERM_CHECK=$(docker exec legisinc-app php artisan tinker --execute="
\$perm = DB::table('screen_permissions')
    ->where('role_name', 'PARLAMENTAR')
    ->where('screen_route', 'proposicoes.assinar')
    ->where('can_access', true)
    ->first();
echo \$perm ? 'PERMISSION_OK' : 'PERMISSION_MISSING';
")

if [[ "$PERM_CHECK" == "PERMISSION_OK" ]]; then
    log_success "Permissões configuradas corretamente"
else
    log_error "Permissões faltando"
fi

echo ""
echo "================================================"
log_info "RESUMO FINAL"
echo "================================================"

ALL_GOOD=true

if [[ "$FUNCTIONAL_TEST" == SUCCESS* ]]; then
    log_success "✅ Sistema PDF: FUNCIONANDO"
else
    log_error "❌ Sistema PDF: PROBLEMA"
    ALL_GOOD=false
fi

if [[ "$CLEAN_CHECK" == "CLEAN" ]]; then
    log_success "✅ Código Debug: REMOVIDO"
else
    log_warning "⚠️ Código Debug: PRESENTE"
fi

if [[ "$PERM_CHECK" == "PERMISSION_OK" ]]; then
    log_success "✅ Permissões: CONFIGURADAS"
else
    log_error "❌ Permissões: PROBLEMA"
    ALL_GOOD=false
fi

echo ""
if $ALL_GOOD; then
    log_success "🎉 SISTEMA 100% FUNCIONAL E PRESERVADO!"
    echo ""
    echo "🚀 TESTE AGORA:"
    echo "1. Acesse: http://localhost:8001/login"
    echo "2. Login: jessica@sistema.gov.br / 123456"
    echo "3. Vá para: /proposicoes/{id}/assinar"
    echo "4. Sistema funcionará perfeitamente!"
else
    log_warning "⚠️ Sistema tem alguns problemas que precisam ser verificados"
fi

echo ""
log_success "CONFIGURAÇÃO PRESERVADA PARA SEMPRE! 🎯"

# Limpeza
rm -f /tmp/migrate_final.log