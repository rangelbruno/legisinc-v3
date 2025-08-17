#!/bin/bash

echo "========================================"
echo "TESTE FINAL: Correções do PDF"
echo "========================================"
echo ""

# Testar proposição SEM protocolo (ID: 3)
echo "1. Testando proposição SEM protocolo (ID: 3)..."
docker exec legisinc-app php artisan tinker --execute="
use App\Models\Proposicao;
use App\Http\Controllers\ProposicaoAssinaturaController;

\$p = Proposicao::find(3);
if (\$p) {
    echo 'Proposição 3: ' . \$p->tipo . PHP_EOL;
    echo 'Número Protocolo: ' . (\$p->numero_protocolo ?: '[AGUARDANDO PROTOCOLO]') . PHP_EOL;
    
    // Gerar PDF
    \$controller = new ProposicaoAssinaturaController();
    \$reflection = new ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    \$method->invoke(\$controller, \$p);
    
    \$pdfPath = storage_path('app/proposicoes/pdfs/3/proposicao_3.pdf');
    if (file_exists(\$pdfPath)) {
        echo 'PDF gerado: SIM (' . filesize(\$pdfPath) . ' bytes)' . PHP_EOL;
    } else {
        echo 'PDF gerado: NÃO' . PHP_EOL;
    }
}
"

# Verificar conteúdo do PDF
echo ""
echo "2. Verificando conteúdo do PDF da proposição 3..."
docker exec legisinc-app sh -c "
if [ -f storage/app/proposicoes/pdfs/3/proposicao_3.pdf ]; then
    pdftotext storage/app/proposicoes/pdfs/3/proposicao_3.pdf - 2>/dev/null | head -20
    echo ''
    echo 'Verificando [AGUARDANDO PROTOCOLO]...'
    pdftotext storage/app/proposicoes/pdfs/3/proposicao_3.pdf - 2>/dev/null | grep -c 'AGUARDANDO PROTOCOLO' | xargs -I {} echo 'Ocorrências de AGUARDANDO PROTOCOLO: {}'
else
    echo 'PDF não encontrado'
fi
"

echo ""
echo "========================================"
echo "3. Testando proposição COM protocolo (ID: 5)..."
echo "========================================"

# Regenerar PDF da proposição 5
docker exec legisinc-app rm -f storage/app/proposicoes/pdfs/5/proposicao_5.pdf 2>/dev/null

docker exec legisinc-app php artisan tinker --execute="
use App\Models\Proposicao;
use App\Http\Controllers\ProposicaoAssinaturaController;

\$p = Proposicao::find(5);
if (\$p) {
    echo 'Proposição 5: ' . \$p->tipo . PHP_EOL;
    echo 'Número Protocolo: ' . \$p->numero_protocolo . PHP_EOL;
    echo 'Assinatura Digital: ' . (\$p->assinatura_digital ? 'SIM' : 'NÃO') . PHP_EOL;
    
    // Gerar PDF
    \$controller = new ProposicaoAssinaturaController();
    \$reflection = new ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    \$method->invoke(\$controller, \$p);
    
    \$pdfPath = storage_path('app/proposicoes/pdfs/5/proposicao_5.pdf');
    if (file_exists(\$pdfPath)) {
        echo 'PDF gerado: SIM (' . filesize(\$pdfPath) . ' bytes)' . PHP_EOL;
    } else {
        echo 'PDF gerado: NÃO' . PHP_EOL;
    }
}
"

# Verificar conteúdo do PDF
echo ""
echo "4. Verificando conteúdo do PDF da proposição 5..."
docker exec legisinc-app sh -c "
if [ -f storage/app/proposicoes/pdfs/5/proposicao_5.pdf ]; then
    pdftotext storage/app/proposicoes/pdfs/5/proposicao_5.pdf - 2>/dev/null | head -20
    echo ''
    echo 'Verificando número do protocolo...'
    pdftotext storage/app/proposicoes/pdfs/5/proposicao_5.pdf - 2>/dev/null | grep '001/2025' | head -1
    echo ''
    echo 'Verificando assinatura digital...'
    pdftotext storage/app/proposicoes/pdfs/5/proposicao_5.pdf - 2>/dev/null | grep -A3 'Assinatura Digital'
else
    echo 'PDF não encontrado'
fi
"

echo ""
echo "========================================"
echo "5. Verificando imagem do cabeçalho..."
echo "========================================"
docker exec legisinc-app sh -c "
if [ -f public/template/cabecalho.png ]; then
    echo '✅ Imagem do cabeçalho existe'
    ls -la public/template/cabecalho.png
else
    echo '❌ Imagem do cabeçalho não encontrada'
fi
"

echo ""
echo "========================================"
echo "TESTE CONCLUÍDO"
echo "========================================"