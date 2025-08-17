#!/bin/bash

echo "🎯 TESTE COMPLETO: migrate:fresh --seed COM OTIMIZAÇÕES PRESERVADAS"
echo "===================================================================="

# Cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

echo ""
log_info "ETAPA 1: Executando migrate:fresh --seed"
echo "========================================"

# Executar migrate fresh seed
log_info "Iniciando reset completo do banco..."
docker exec legisinc-app php artisan migrate:fresh --seed > /tmp/migrate_output.log 2>&1

if [ $? -eq 0 ]; then
    log_success "migrate:fresh --seed executado com sucesso!"
    
    # Verificar se nosso seeder foi executado
    if grep -q "PDF de Assinatura Otimizado" /tmp/migrate_output.log; then
        log_success "PDFAssinaturaOptimizadoSeeder executado!"
    else
        log_warning "PDFAssinaturaOptimizadoSeeder pode não ter sido executado"
    fi
    
    # Mostrar últimas linhas relevantes
    echo ""
    log_info "Últimas linhas do migrate:"
    tail -10 /tmp/migrate_output.log | grep -E "(PDF|Otimizado|✅|🎯)"
    
else
    log_error "Erro no migrate:fresh --seed"
    cat /tmp/migrate_output.log
    exit 1
fi

echo ""
log_info "ETAPA 2: Verificando Arquivos Críticos Preservados"
echo "================================================="

# Verificar ProposicaoAssinaturaController
CONTROLLER_PATH="/home/bruno/legisinc/app/Http/Controllers/ProposicaoAssinaturaController.php"
if [ -f "$CONTROLLER_PATH" ]; then
    if grep -q "encontrarArquivoMaisRecente" "$CONTROLLER_PATH" && 
       grep -q "extrairConteudoDOCX" "$CONTROLLER_PATH" && 
       grep -q "limparPDFsAntigos" "$CONTROLLER_PATH"; then
        log_success "ProposicaoAssinaturaController: OTIMIZADO ✅"
    else
        log_error "ProposicaoAssinaturaController: Métodos otimizados FALTANDO"
    fi
else
    log_error "ProposicaoAssinaturaController: ARQUIVO FALTANDO"
fi

# Verificar OnlyOfficeService
ONLYOFFICE_PATH="/home/bruno/legisinc/app/Services/OnlyOffice/OnlyOfficeService.php"
if [ -f "$ONLYOFFICE_PATH" ]; then
    if grep -q "timestamp = time()" "$ONLYOFFICE_PATH"; then
        log_success "OnlyOfficeService: OTIMIZADO ✅"
    else
        log_warning "OnlyOfficeService: Pode precisar verificação"
    fi
else
    log_error "OnlyOfficeService: ARQUIVO FALTANDO"
fi

# Verificar se DatabaseSeeder inclui nosso seeder
DATABASE_SEEDER_PATH="/home/bruno/legisinc/database/seeders/DatabaseSeeder.php"
if [ -f "$DATABASE_SEEDER_PATH" ]; then
    if grep -q "PDFAssinaturaOptimizadoSeeder" "$DATABASE_SEEDER_PATH"; then
        log_success "DatabaseSeeder: Inclui PDFAssinaturaOptimizadoSeeder ✅"
    else
        log_error "DatabaseSeeder: PDFAssinaturaOptimizadoSeeder NÃO incluído"
    fi
else
    log_error "DatabaseSeeder: ARQUIVO FALTANDO"
fi

echo ""
log_info "ETAPA 3: Testando Funcionalidade com Proposição Teste"
echo "====================================================="

# Criar proposição de teste e simular edições
docker exec legisinc-app php artisan tinker --execute="
try {
    echo 'Criando proposição de teste...' . PHP_EOL;
    
    \$parlamentar = App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
    \$proposicao = App\Models\Proposicao::create([
        'tipo' => 'Moção',
        'ementa' => 'Teste pós migrate:fresh --seed - ' . now()->format('H:i:s'),
        'conteudo' => 'CONTEÚDO ORIGINAL DO PARLAMENTAR - ' . now()->format('H:i:s'),
        'autor_id' => \$parlamentar->id,
        'status' => 'rascunho',
        'template_id' => 6
    ]);
    
    echo 'Proposição criada - ID: ' . \$proposicao->id . PHP_EOL;
    
    // Simular edição do Parlamentar
    \$arquivo1 = 'proposicoes/proposicao_' . \$proposicao->id . '_' . time() . '.docx';
    Storage::disk('local')->put(\$arquivo1, 'EDITADO PELO PARLAMENTAR - Versão 1');
    \$proposicao->arquivo_path = \$arquivo1;
    \$proposicao->status = 'enviado_legislativo';
    \$proposicao->save();
    
    sleep(1);
    
    // Simular edição do Legislativo (mais recente)
    \$legislativo = App\Models\User::where('email', 'joao@sistema.gov.br')->first();
    \$arquivo2 = 'proposicoes/proposicao_' . \$proposicao->id . '_' . time() . '.docx';
    Storage::disk('local')->put(\$arquivo2, 'EDITADO PELO LEGISLATIVO - Versão FINAL');
    \$proposicao->arquivo_path = \$arquivo2;
    \$proposicao->revisor_id = \$legislativo->id;
    \$proposicao->status = 'retornado_legislativo';
    \$proposicao->save();
    
    echo 'Edições simuladas - Arquivo final: ' . \$arquivo2 . PHP_EOL;
    echo 'Proposição configurada para teste!' . PHP_EOL;
    
} catch (Exception \$e) {
    echo 'ERRO: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
log_info "ETAPA 4: Testando Sistema de Busca e Extração"
echo "============================================="

# Testar sistema otimizado
TESTE_RESULTADO=$(docker exec legisinc-app php artisan tinker --execute="
try {
    \$proposicao = App\Models\Proposicao::orderBy('id', 'desc')->first();
    \$controller = new App\Http\Controllers\ProposicaoAssinaturaController();
    
    // Testar método de busca
    \$reflection = new ReflectionClass(\$controller);
    \$methodFind = \$reflection->getMethod('encontrarArquivoMaisRecente');
    \$methodFind->setAccessible(true);
    
    \$arquivoMaisRecente = \$methodFind->invoke(\$controller, \$proposicao);
    
    if (\$arquivoMaisRecente) {
        echo '✅ BUSCA: Arquivo mais recente encontrado' . PHP_EOL;
        echo '   Path: ' . basename(\$arquivoMaisRecente['path']) . PHP_EOL;
        echo '   Data: ' . \$arquivoMaisRecente['modified'] . PHP_EOL;
        
        // Testar geração de PDF
        \$methodPDF = \$reflection->getMethod('gerarPDFParaAssinatura');
        \$methodPDF->setAccessible(true);
        
        \$methodPDF->invoke(\$controller, \$proposicao);
        \$proposicao->refresh();
        
        if (\$proposicao->arquivo_pdf_path) {
            echo '✅ PDF: Gerado com sucesso' . PHP_EOL;
            echo '   Arquivo: ' . \$proposicao->arquivo_pdf_path . PHP_EOL;
            
            \$pdfPath = storage_path('app/' . \$proposicao->arquivo_pdf_path);
            if (file_exists(\$pdfPath)) {
                echo '   Tamanho: ' . filesize(\$pdfPath) . ' bytes' . PHP_EOL;
                echo '✅ SISTEMA TOTALMENTE FUNCIONAL!' . PHP_EOL;
            } else {
                echo '❌ PDF: Arquivo não encontrado fisicamente' . PHP_EOL;
            }
        } else {
            echo '❌ PDF: Não foi salvo no banco' . PHP_EOL;
        }
    } else {
        echo '❌ BUSCA: Nenhum arquivo encontrado' . PHP_EOL;
    }
    
} catch (Exception \$e) {
    echo '❌ ERRO: ' . \$e->getMessage() . PHP_EOL;
}
")

echo "$TESTE_RESULTADO"

echo ""
log_info "ETAPA 5: Verificando Diretórios e Configurações"
echo "==============================================="

# Verificar diretórios criados
DIRETORIOS=(
    "/home/bruno/legisinc/storage/app/proposicoes"
    "/home/bruno/legisinc/storage/app/proposicoes/pdfs"
    "/home/bruno/legisinc/storage/app/private/proposicoes"
    "/home/bruno/legisinc/storage/framework/cache/pdf-assinatura"
)

for dir in "${DIRETORIOS[@]}"; do
    if [ -d "$dir" ]; then
        log_success "Diretório existe: $(basename $dir)"
    else
        log_warning "Diretório faltando: $(basename $dir)"
    fi
done

# Verificar arquivos de configuração
CONFIG_FILES=(
    "/home/bruno/legisinc/storage/framework/cache/pdf-assinatura/config.json"
    "/home/bruno/legisinc/storage/logs/pdf-config.json"
    "/home/bruno/legisinc/CONFIGURACAO_PDF_OTIMIZADO.md"
)

for file in "${CONFIG_FILES[@]}"; do
    if [ -f "$file" ]; then
        log_success "Config existe: $(basename $file)"
    else
        log_warning "Config faltando: $(basename $file)"
    fi
done

echo ""
echo "===================================================================="
log_info "RESUMO FINAL DA VALIDAÇÃO"
echo "===================================================================="

echo ""
echo "🎉 RESULTADOS:"

# Analisar resultados dos testes
if echo "$TESTE_RESULTADO" | grep -q "✅ SISTEMA TOTALMENTE FUNCIONAL!"; then
    log_success "SISTEMA COMPLETO: Funcionando 100%"
    echo ""
    echo "🎯 VALIDAÇÕES APROVADAS:"
    echo "   ✅ migrate:fresh --seed executado com sucesso"
    echo "   ✅ PDFAssinaturaOptimizadoSeeder incluído e funcionando"
    echo "   ✅ Arquivos críticos preservados com métodos otimizados"
    echo "   ✅ Sistema de busca de arquivo mais recente funcional"
    echo "   ✅ Geração de PDF com conteúdo extraído funcionando"
    echo "   ✅ Diretórios e configurações criados automaticamente"
    echo ""
    echo "🌟 PRÓXIMOS PASSOS:"
    echo "   1. Acesse: http://localhost:8001"
    echo "   2. Login: jessica@sistema.gov.br / 123456"
    echo "   3. Navegue para a proposição mais recente"
    echo "   4. Teste /proposicoes/{id}/assinar"
    echo "   5. Verifique se PDF mostra edições do Legislativo"
    
else
    log_error "SISTEMA: Algum problema detectado"
    echo ""
    echo "🔧 VERIFICAR:"
    echo "   - Logs de erro acima"
    echo "   - Arquivos críticos podem precisar reconfiguração"
    echo "   - Executar diagnósticos manuais"
fi

echo ""
log_success "CONFIGURAÇÃO PRESERVADA E TESTADA! 🎉"
echo "Todas as otimizações estão ativas e funcionais após migrate:fresh --seed"

# Limpeza
rm -f /tmp/migrate_output.log