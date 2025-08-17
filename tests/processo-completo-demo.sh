#!/bin/bash

echo "🎯 TESTE COMPLETO: Fluxo Parlamentar → Legislativo → Assinatura"
echo "================================================================"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log colorido
log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

# 1. Reset do banco de dados
echo ""
log_info "1. RESETANDO BANCO DE DADOS..."
docker exec legisinc-app php artisan migrate:fresh --seed
if [ $? -eq 0 ]; then
    log_success "Banco resetado com sucesso"
else
    log_error "Erro ao resetar banco"
    exit 1
fi

# 2. Criar proposição com o Parlamentar
echo ""
log_info "2. CRIANDO PROPOSIÇÃO COM PARLAMENTAR (jessica@sistema.gov.br)..."
docker exec legisinc-app php artisan tinker --execute="
\$parlamentar = App\Models\User::where('email', 'jessica@sistema.gov.br')->first();
\$proposicao = App\Models\Proposicao::create([
    'tipo' => 'Moção',
    'ementa' => 'Teste de edições múltiplas - ' . now()->format('H:i:s'),
    'conteudo' => 'CONTEÚDO ORIGINAL DO PARLAMENTAR - Criado às ' . now()->format('H:i:s'),
    'autor_id' => \$parlamentar->id,
    'status' => 'rascunho',
    'template_id' => 6
]);
echo 'Proposição criada - ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status: ' . \$proposicao->status . PHP_EOL;
"

# 3. Simular edição do Parlamentar no OnlyOffice
echo ""
log_info "3. SIMULANDO EDIÇÃO DO PARLAMENTAR NO ONLYOFFICE..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);

// Simular callback do OnlyOffice com edição do Parlamentar
\$nomeArquivo = 'proposicoes/proposicao_1_' . time() . '.docx';
\$conteudoEditado = 'CONTEÚDO EDITADO PELO PARLAMENTAR - ' . now()->format('H:i:s') . PHP_EOL;
\$conteudoEditado .= 'Esta é a versão editada pelo parlamentar Jessica.';

// Salvar arquivo simulando OnlyOffice
Storage::disk('local')->put(\$nomeArquivo, \$conteudoEditado);
\$proposicao->arquivo_path = \$nomeArquivo;
\$proposicao->ultima_modificacao = now();
\$proposicao->save();

echo 'Edição do Parlamentar salva em: ' . \$nomeArquivo . PHP_EOL;
"

# 4. Enviar para o Legislativo
echo ""
log_info "4. ENVIANDO PROPOSIÇÃO PARA O LEGISLATIVO..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
\$proposicao->status = 'enviado_legislativo';
\$proposicao->save();
echo 'Proposição enviada para Legislativo - Status: ' . \$proposicao->status . PHP_EOL;
"

# 5. Simular edição do Legislativo
echo ""
log_info "5. SIMULANDO EDIÇÃO DO LEGISLATIVO (joao@sistema.gov.br)..."
sleep 2 # Aguardar para garantir timestamp diferente
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
\$legislativo = App\Models\User::where('email', 'joao@sistema.gov.br')->first();

// Simular callback do OnlyOffice com edição do Legislativo
\$nomeArquivo = 'proposicoes/proposicao_1_' . time() . '.docx';
\$conteudoEditado = 'CONTEÚDO FINAL EDITADO PELO LEGISLATIVO - ' . now()->format('H:i:s') . PHP_EOL;
\$conteudoEditado .= 'Versão original do Parlamentar foi revisada.' . PHP_EOL;
\$conteudoEditado .= 'Esta é a versão FINAL editada pelo Legislativo João.' . PHP_EOL;
\$conteudoEditado .= 'Data da revisão: ' . now()->format('d/m/Y H:i:s');

// Salvar arquivo simulando OnlyOffice
Storage::disk('local')->put(\$nomeArquivo, \$conteudoEditado);
\$proposicao->arquivo_path = \$nomeArquivo;
\$proposicao->revisor_id = \$legislativo->id;
\$proposicao->ultima_modificacao = now();
\$proposicao->save();

echo 'Edição do Legislativo salva em: ' . \$nomeArquivo . PHP_EOL;
"

# 6. Retornar para o Parlamentar
echo ""
log_info "6. RETORNANDO PROPOSIÇÃO PARA O PARLAMENTAR..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
\$proposicao->status = 'retornado_legislativo';
\$proposicao->parecer_tecnico = 'Documento revisado e aprovado pelo Legislativo';
\$proposicao->save();
echo 'Proposição retornada - Status: ' . \$proposicao->status . PHP_EOL;
"

# 7. Verificar arquivos salvos
echo ""
log_info "7. VERIFICANDO ARQUIVOS SALVOS..."
echo "Arquivos da proposição 1:"
find /home/bruno/legisinc/storage/app -name "proposicao_1_*.docx" -o -name "proposicao_1_*.rtf" 2>/dev/null | while read file; do
    echo "  📄 $(basename $file) - $(stat --format='%y' $file | cut -d' ' -f2) - $(stat --format='%s' $file) bytes"
done

# 8. Gerar PDF para assinatura
echo ""
log_info "8. GERANDO PDF PARA ASSINATURA..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
\$controller = new App\Http\Controllers\ProposicaoAssinaturaController();

try {
    \$reflection = new ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    
    \$method->invoke(\$controller, \$proposicao);
    echo 'PDF gerado com sucesso!' . PHP_EOL;
    
    if (\$proposicao->arquivo_pdf_path) {
        echo 'Caminho do PDF: ' . \$proposicao->arquivo_pdf_path . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'ERRO: ' . \$e->getMessage() . PHP_EOL;
}
"

# 9. Verificar conteúdo do PDF
echo ""
log_info "9. VERIFICANDO CONTEÚDO DO PDF..."

# Encontrar o PDF mais recente
PDF_PATH=$(find /home/bruno/legisinc/storage/app/proposicoes/pdfs/1 -name "*.pdf" -type f 2>/dev/null | head -1)

if [ -n "$PDF_PATH" ]; then
    log_success "PDF encontrado: $(basename $PDF_PATH)"
    echo "  Tamanho: $(stat --format='%s' $PDF_PATH) bytes"
    echo "  Criado: $(stat --format='%y' $PDF_PATH)"
    
    # Tentar extrair texto do PDF
    if command -v pdftotext >/dev/null 2>&1; then
        pdftotext "$PDF_PATH" /tmp/pdf_test_content.txt 2>/dev/null
        if [ -f /tmp/pdf_test_content.txt ]; then
            echo ""
            echo "📄 CONTEÚDO DO PDF:"
            echo "==================="
            cat /tmp/pdf_test_content.txt | head -20
            echo "==================="
            
            # Verificar se contém texto do Legislativo
            if grep -q "LEGISLATIVO" /tmp/pdf_test_content.txt; then
                log_success "PDF contém edições do LEGISLATIVO!"
            elif grep -q "PARLAMENTAR" /tmp/pdf_test_content.txt; then
                log_warning "PDF contém apenas edições do PARLAMENTAR"
            else
                log_error "PDF não contém marcadores de edição esperados"
            fi
            
            rm -f /tmp/pdf_test_content.txt
        fi
    else
        log_warning "pdftotext não disponível para verificar conteúdo"
    fi
else
    log_error "PDF não foi gerado"
fi

# 10. Verificar logs
echo ""
log_info "10. VERIFICANDO LOGS DO SISTEMA..."
if [ -f /home/bruno/legisinc/storage/logs/laravel.log ]; then
    echo "Últimas entradas relacionadas ao PDF:"
    tail -20 /home/bruno/legisinc/storage/logs/laravel.log | grep -E "(PDF Assinatura|arquivo mais recente)" | tail -5
fi

# Resumo final
echo ""
echo "================================================================"
log_info "RESUMO DO TESTE:"
echo ""

docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
echo '📋 Proposição ID: ' . \$proposicao->id . PHP_EOL;
echo '📝 Status: ' . \$proposicao->status . PHP_EOL;
echo '👤 Autor: ' . (\$proposicao->autor->name ?? 'N/A') . PHP_EOL;
echo '👥 Revisor: ' . (\$proposicao->revisor->name ?? 'N/A') . PHP_EOL;
echo '📁 Arquivo: ' . (\$proposicao->arquivo_path ?: 'Nenhum') . PHP_EOL;
echo '📄 PDF: ' . (\$proposicao->arquivo_pdf_path ?: 'Nenhum') . PHP_EOL;
echo '🕐 Última modificação: ' . (\$proposicao->ultima_modificacao ?: 'N/A') . PHP_EOL;
"

echo ""
log_success "TESTE CONCLUÍDO!"
echo ""
echo "🎯 PRÓXIMOS PASSOS:"
echo "1. Acessar: http://localhost:8001"
echo "2. Login como Parlamentar: jessica@sistema.gov.br / 123456"
echo "3. Ir para: /proposicoes/1/assinar"
echo "4. Verificar se o PDF mostra: 'CONTEÚDO FINAL EDITADO PELO LEGISLATIVO'"