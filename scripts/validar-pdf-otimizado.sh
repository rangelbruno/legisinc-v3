#!/bin/bash

echo "🔍 VALIDAÇÃO RÁPIDA: PDF de Assinatura Otimizado"
echo "==============================================="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }
log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }

echo ""
log_info "1. Verificando arquivos críticos..."

# ProposicaoAssinaturaController
if [ -f "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php" ]; then
    if grep -q "encontrarArquivoMaisRecente" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php" && \
       grep -q "extrairConteudoDOCX" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php" && \
       grep -q "limparPDFsAntigos" "/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"; then
        log_success "ProposicaoAssinaturaController: OTIMIZADO"
    else
        log_error "ProposicaoAssinaturaController: FALTAM OTIMIZAÇÕES"
    fi
else
    log_error "ProposicaoAssinaturaController: ARQUIVO FALTANDO"
fi

# OnlyOfficeService
if [ -f "/home/bruno/legisinc/app/Services/OnlyOffice/OnlyOfficeService.php" ]; then
    if grep -q "timestamp = time()" "/home/bruno/legisinc/app/Services/OnlyOffice/OnlyOfficeService.php"; then
        log_success "OnlyOfficeService: OTIMIZADO"
    else
        log_error "OnlyOfficeService: FALTAM OTIMIZAÇÕES"
    fi
else
    log_error "OnlyOfficeService: ARQUIVO FALTANDO"
fi

# PDFAssinaturaOptimizadoSeeder
if [ -f "/home/bruno/legisinc/database/seeders/PDFAssinaturaOptimizadoSeeder.php" ]; then
    log_success "PDFAssinaturaOptimizadoSeeder: PRESENTE"
else
    log_error "PDFAssinaturaOptimizadoSeeder: FALTANDO"
fi

# DatabaseSeeder
if [ -f "/home/bruno/legisinc/database/seeders/DatabaseSeeder.php" ]; then
    if grep -q "PDFAssinaturaOptimizadoSeeder" "/home/bruno/legisinc/database/seeders/DatabaseSeeder.php"; then
        log_success "DatabaseSeeder: INCLUI PDF SEEDER"
    else
        log_error "DatabaseSeeder: PDF SEEDER NÃO INCLUÍDO"
    fi
else
    log_error "DatabaseSeeder: ARQUIVO FALTANDO"
fi

echo ""
log_info "2. Verificando diretórios..."

DIRETORIOS=(
    "/home/bruno/legisinc/storage/app/proposicoes"
    "/home/bruno/legisinc/storage/app/proposicoes/pdfs"
    "/home/bruno/legisinc/storage/framework/cache/pdf-assinatura"
)

for dir in "${DIRETORIOS[@]}"; do
    if [ -d "$dir" ]; then
        log_success "$(basename $dir): EXISTE"
    else
        log_error "$(basename $dir): FALTANDO"
    fi
done

echo ""
log_info "3. Teste rápido de funcionalidade..."

# Teste via Laravel
TESTE_RESULTADO=$(docker exec legisinc-app php artisan tinker --execute="
try {
    \$proposicao = App\\Models\\Proposicao::orderBy('id', 'desc')->first();
    if (\$proposicao) {
        \$controller = new App\\Http\\Controllers\\ProposicaoAssinaturaController();
        \$reflection = new ReflectionClass(\$controller);
        \$method = \$reflection->getMethod('encontrarArquivoMaisRecente');
        \$method->setAccessible(true);
        \$arquivo = \$method->invoke(\$controller, \$proposicao);
        
        if (\$arquivo) {
            echo 'FUNCIONAL: Busca de arquivo funcionando';
        } else {
            echo 'PROBLEMA: Busca não encontrou arquivos';
        }
    } else {
        echo 'SEM_PROPOSICOES: Nenhuma proposição encontrada';
    }
} catch (Exception \$e) {
    echo 'ERRO: ' . \$e->getMessage();
}
")

if [[ "$TESTE_RESULTADO" == *"FUNCIONAL"* ]]; then
    log_success "Sistema de busca: FUNCIONANDO"
elif [[ "$TESTE_RESULTADO" == *"SEM_PROPOSICOES"* ]]; then
    log_info "Nenhuma proposição para testar (executar migrate:fresh --seed)"
else
    log_error "Sistema de busca: PROBLEMA DETECTADO"
    echo "   Detalhes: $TESTE_RESULTADO"
fi

echo ""
echo "🎯 RESUMO:"
echo "=========="
echo ""
echo "Para garantir funcionamento completo:"
echo "1. Execute: docker exec -it legisinc-app php artisan migrate:fresh --seed"
echo "2. Teste: /proposicoes/{id}/assinar"
echo "3. Verifique se PDF mostra edições mais recentes"
echo ""
log_success "Validação concluída!"