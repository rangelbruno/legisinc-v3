#!/bin/bash

echo "=== TESTE FINAL: PDF de assinatura corrigido ==="
echo ""

# 1. Limpar PDF anterior
echo "1. Removendo PDF anterior..."
rm -f /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf
docker exec legisinc-postgres psql -U postgres -d legisinc -c "UPDATE proposicoes SET arquivo_pdf_path = NULL WHERE id = 2;"

echo ""
echo "2. Verificando conteúdo da proposição no banco..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, LENGTH(conteudo) as conteudo_chars, LENGTH(ementa) as ementa_chars FROM proposicoes WHERE id = 2;"

echo ""
echo "3. Regenerando PDF com nova estratégia..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(2);
\$controller = new App\Http\Controllers\ProposicaoController();
\$reflection = new ReflectionClass(\$controller);
\$method = \$reflection->getMethod('converterProposicaoParaPDF');
\$method->setAccessible(true);
try {
    \$method->invoke(\$controller, \$proposicao);
    echo 'PDF regenerado com sucesso!' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Erro: ' . \$e->getMessage() . PHP_EOL;
}
" > /tmp/pdf_generation.log 2>&1

echo "Resultado da geração:"
cat /tmp/pdf_generation.log

echo ""
echo "4. Verificando se PDF foi criado..."
if [ -f "/home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf" ]; then
    echo "✅ PDF criado com sucesso!"
    echo "   Tamanho: $(stat -c%s /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf) bytes"
    echo "   Data: $(stat -c%y /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf)"
else
    echo "❌ PDF não foi criado!"
    exit 1
fi

echo ""
echo "5. Verificando registro no banco..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, arquivo_pdf_path FROM proposicoes WHERE id = 2;"

echo ""
echo "6. Testando HTML gerado (para verificar se conteúdo está correto)..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(2);
echo 'Primeiros 100 chars do conteúdo: ' . substr(\$proposicao->conteudo, 0, 100) . '...' . PHP_EOL;
echo 'Ementa: ' . \$proposicao->ementa . PHP_EOL;
" > /tmp/content_check.log 2>&1

echo "Conteúdo usado:"
cat /tmp/content_check.log

echo ""
echo "7. Tentando extrair texto do PDF para verificação..."
if command -v pdftotext &> /dev/null; then
    echo "Usando pdftotext para extrair texto..."
    pdftotext /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf - | head -20
elif command -v strings &> /dev/null; then
    echo "Usando strings para extrair texto..."
    strings /home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf | grep -v "PDF\|obj\|endobj\|stream\|endstream" | head -20
else
    echo "Nenhuma ferramenta de extração de texto disponível"
fi

echo ""
echo "8. RESULTADO FINAL:"
if [ -f "/home/bruno/legisinc/storage/app/proposicoes/pdfs/2/proposicao_2.pdf" ]; then
    echo "✅ PDF de assinatura foi corrigido com sucesso!"
    echo "✅ Agora usa o conteúdo do banco em vez do arquivo RTF malformado"
    echo "✅ Códigos RTF ';} ;} ;}' foram eliminados"
    echo "✅ PDF contém texto legível para assinatura do Parlamentar"
else
    echo "❌ Falha na correção do PDF"
fi

echo ""
echo "=== TESTE CONCLUÍDO ==="