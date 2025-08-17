#!/bin/bash

echo "🎯 TESTE FINAL: PDF com Conteúdo do Arquivo Editado"
echo "=================================================="

echo "🗑️  1. Limpando PDFs antigos para forçar regeneração..."
rm -f /home/bruno/legisinc/storage/app/proposicoes/pdfs/1/*.pdf

echo "🔍 2. Verificando proposição 1 no banco..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
if (\$proposicao) {
    echo 'Proposição ID: ' . \$proposicao->id . PHP_EOL;
    echo 'Status: ' . \$proposicao->status . PHP_EOL;
    echo 'Arquivo path (banco): ' . (\$proposicao->arquivo_path ?: 'NULL') . PHP_EOL;
    echo 'Conteúdo (banco): ' . substr(\$proposicao->conteudo ?: 'VAZIO', 0, 100) . '...' . PHP_EOL;
} else {
    echo 'Proposição não encontrada' . PHP_EOL;
}
"

echo ""
echo "📁 3. Listando todos os arquivos da proposição 1..."
find /home/bruno/legisinc/storage/app -name "*proposicao_1_*" -type f 2>/dev/null | sort -t_ -k3 -n | tail -5 | while read file; do
    echo "  📄 $(basename $file) - $(stat --format='%y' $file | cut -d' ' -f2) - $(stat --format='%s' $file) bytes"
done

echo ""
echo "🔧 4. Gerando PDF com método otimizado..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
\$controller = new App\Http\Controllers\ProposicaoAssinaturaController();

try {
    \$reflection = new ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    
    echo 'Gerando PDF com método otimizado...' . PHP_EOL;
    \$method->invoke(\$controller, \$proposicao);
    echo 'PDF gerado com sucesso!' . PHP_EOL;
    
    // Recarregar proposição para ver se PDF foi salvo
    \$proposicao->refresh();
    if (\$proposicao->arquivo_pdf_path) {
        echo 'PDF salvo em: ' . \$proposicao->arquivo_pdf_path . PHP_EOL;
    }
    
} catch (Exception \$e) {
    echo 'ERRO: ' . \$e->getMessage() . PHP_EOL;
    echo 'Trace: ' . \$e->getTraceAsString() . PHP_EOL;
}
"

echo ""
echo "📄 5. Verificando PDF gerado..."
PDF_PATH=$(find /home/bruno/legisinc/storage/app/proposicoes/pdfs/1 -name "*.pdf" -type f -printf '%T@ %p\n' | sort -n | tail -1 | cut -d' ' -f2-)

if [ -n "$PDF_PATH" ]; then
    echo "✅ PDF mais recente: $(basename $PDF_PATH)"
    echo "   Tamanho: $(stat --format='%s' $PDF_PATH) bytes"
    echo "   Criado: $(stat --format='%y' $PDF_PATH)"
    
    # Verificar se tem pdftotext ou alternativa
    if command -v pdftotext >/dev/null 2>&1; then
        echo ""
        echo "📖 CONTEÚDO DO PDF:"
        echo "==================="
        pdftotext "$PDF_PATH" /tmp/pdf_content_test.txt 2>/dev/null
        if [ -f /tmp/pdf_content_test.txt ]; then
            cat /tmp/pdf_content_test.txt
            echo ""
            echo "==================="
            
            # Verificar conteúdo específico
            if grep -qi "editado pelo legislativo" /tmp/pdf_content_test.txt; then
                echo "🎉 SUCESSO: PDF contém 'Editado pelo Legislativo'!"
            elif grep -qi "legislativo" /tmp/pdf_content_test.txt; then
                echo "✅ PDF contém referências ao Legislativo"
            elif grep -qi "parlamentar" /tmp/pdf_content_test.txt; then
                echo "⚠️  PDF contém apenas referências ao Parlamentar"
            else
                echo "❓ Conteúdo do PDF não contém marcadores esperados"
            fi
            
            rm -f /tmp/pdf_content_test.txt
        else
            echo "❌ Erro ao extrair texto do PDF"
        fi
    else
        echo "⚠️  pdftotext não disponível para verificar conteúdo textual"
        echo "   PDF foi criado, verificar manualmente"
    fi
else
    echo "❌ Nenhum PDF foi gerado"
fi

echo ""
echo "📋 6. Verificando logs de extração..."
if [ -f /home/bruno/legisinc/storage/logs/laravel.log ]; then
    echo "Últimas 10 linhas dos logs relacionados ao PDF:"
    tail -20 /home/bruno/legisinc/storage/logs/laravel.log | grep -E "(PDF Assinatura|Arquivo mais recente|Conteúdo extraído)" | tail -10
fi

echo ""
echo "✅ TESTE CONCLUÍDO!"
echo ""
echo "📋 PRÓXIMOS PASSOS:"
echo "1. Se aparecer 'SUCESSO: PDF contém Editado pelo Legislativo' ✅"
echo "2. Acesse http://localhost:8001/proposicoes/1/assinar"
echo "3. Verifique se o PDF da tela mostra conteúdo do Legislativo"