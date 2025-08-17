#!/bin/bash

echo "🔐 TESTE DE FLUXO: Login e Assinatura"
echo "=================================="

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
log_info "1. Verificando estado da proposição..."

# Verificar proposição no banco
PROPOSICAO_INFO=$(docker exec legisinc-app php artisan tinker --execute="
\$prop = App\\Models\\Proposicao::find(1);
if (\$prop) {
    echo 'ID: ' . \$prop->id . '|';
    echo 'Status: ' . \$prop->status . '|';
    echo 'Autor: ' . \$prop->autor_id . '|';
    echo 'PDF: ' . (\$prop->arquivo_pdf_path ? 'SIM' : 'NAO');
} else {
    echo 'NAO_ENCONTRADA';
}
")

if [[ "$PROPOSICAO_INFO" == *"NAO_ENCONTRADA"* ]]; then
    log_error "Proposição não encontrada no banco"
    exit 1
fi

IFS='|' read -r -a INFO_ARRAY <<< "$PROPOSICAO_INFO"
log_success "Proposição encontrada:"
echo "  - ID: ${INFO_ARRAY[0]#ID: }"
echo "  - Status: ${INFO_ARRAY[1]#Status: }"
echo "  - Autor: ${INFO_ARRAY[2]#Autor: }"
echo "  - PDF: ${INFO_ARRAY[3]#PDF: }"

echo ""
log_info "2. Verificando usuário Jessica..."

# Verificar usuário Jessica
JESSICA_INFO=$(docker exec legisinc-app php artisan tinker --execute="
\$jessica = App\\Models\\User::where('email', 'jessica@sistema.gov.br')->first();
if (\$jessica) {
    echo 'ID: ' . \$jessica->id . '|';
    echo 'Nome: ' . \$jessica->name . '|';
    echo 'Email: ' . \$jessica->email;
} else {
    echo 'NAO_ENCONTRADA';
}
")

if [[ "$JESSICA_INFO" == *"NAO_ENCONTRADA"* ]]; then
    log_error "Usuário Jessica não encontrado"
    exit 1
fi

log_success "Usuário Jessica encontrado:"
IFS='|' read -r -a JESSICA_ARRAY <<< "$JESSICA_INFO"
echo "  - ID: ${JESSICA_ARRAY[0]#ID: }"
echo "  - Nome: ${JESSICA_ARRAY[1]#Nome: }"
echo "  - Email: ${JESSICA_ARRAY[2]#Email: }"

echo ""
log_info "3. Verificando permissões..."

# Verificar permissões
PERMISSION_CHECK=$(docker exec legisinc-app php artisan tinker --execute="
\$perm = DB::table('screen_permissions')
    ->where('role_name', 'PARLAMENTAR')
    ->where('screen_route', 'proposicoes.assinar')
    ->where('can_access', true)
    ->first();
    
if (\$perm) {
    echo 'PERMITIDO';
} else {
    echo 'NEGADO';
}
")

if [[ "$PERMISSION_CHECK" == "PERMITIDO" ]]; then
    log_success "Permissões OK"
else
    log_error "Permissões faltando"
    exit 1
fi

echo ""
log_info "4. Testando geração de PDF..."

# Testar geração de PDF
PDF_TEST=$(docker exec legisinc-app php artisan tinker --execute="
try {
    \$proposicao = App\\Models\\Proposicao::find(1);
    \$controller = new App\\Http\\Controllers\\ProposicaoAssinaturaController();
    \$reflection = new ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    \$method->invoke(\$controller, \$proposicao);
    \$proposicao->refresh();
    
    if (\$proposicao->arquivo_pdf_path) {
        \$pdfPath = storage_path('app/' . \$proposicao->arquivo_pdf_path);
        if (file_exists(\$pdfPath)) {
            echo 'PDF_OK|' . \$proposicao->arquivo_pdf_path . '|' . filesize(\$pdfPath);
        } else {
            echo 'PDF_ARQUIVO_NAO_EXISTE';
        }
    } else {
        echo 'PDF_NAO_SALVO';
    }
} catch (Exception \$e) {
    echo 'PDF_ERRO|' . \$e->getMessage();
}
")

if [[ "$PDF_TEST" == PDF_OK* ]]; then
    log_success "Geração de PDF funcionando"
    IFS='|' read -r -a PDF_ARRAY <<< "$PDF_TEST"
    echo "  - Arquivo: ${PDF_ARRAY[1]}"
    echo "  - Tamanho: ${PDF_ARRAY[2]} bytes"
else
    log_error "Problema na geração de PDF: $PDF_TEST"
fi

echo ""
log_info "5. Instruções para teste manual..."

echo ""
echo "🎯 PARA TESTAR NO BROWSER:"
echo "=========================="
echo ""
echo "1. Acesse: http://localhost:8001/login"
echo "2. Faça login com:"
echo "   - Email: jessica@sistema.gov.br"
echo "   - Senha: 123456"
echo ""
echo "3. Após login, acesse diretamente:"
echo "   http://localhost:8001/proposicoes/1/assinar"
echo ""
echo "4. OU navegue para:"
echo "   - Dashboard → Minhas Proposições → Visualizar Proposição ID 1"
echo "   - Clique no botão 'Assinar Documento'"
echo ""

if [[ "$PDF_TEST" == PDF_OK* ]]; then
    log_success "Sistema pronto para teste! 🎉"
    echo ""
    echo "Se o botão 'Assinar Documento' ainda não funcionar,"
    echo "pode ser um problema de JavaScript ou cache do browser."
    echo ""
    echo "Teste diretamente a URL: http://localhost:8001/proposicoes/1/assinar"
else
    log_warning "Há problemas na geração de PDF que precisam ser resolvidos primeiro."
fi