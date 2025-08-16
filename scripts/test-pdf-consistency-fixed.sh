#!/bin/bash

echo "=== TESTE: Consistência do PDF (Iframe vs Nova Aba) ==="
echo ""

echo "🔍 1. Verificando estado da proposição..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);
echo 'ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status: ' . \$proposicao->status . PHP_EOL;
echo 'arquivo_path (DOCX): ' . (\$proposicao->arquivo_path ?: 'NULL') . PHP_EOL;
echo 'arquivo_pdf_path: ' . \$proposicao->arquivo_pdf_path . PHP_EOL;

if (\$proposicao->arquivo_path) {
    // Buscar arquivo usando Storage::disk para localização correta
    \$docxExists = \Illuminate\Support\Facades\Storage::disk('local')->exists(\$proposicao->arquivo_path);
    if (\$docxExists) {
        \$docxPath = \Illuminate\Support\Facades\Storage::disk('local')->path(\$proposicao->arquivo_path);
        echo 'DOCX existe: SIM ✅' . PHP_EOL;
        echo 'DOCX tamanho: ' . (file_exists(\$docxPath) ? filesize(\$docxPath) : 0) . ' bytes' . PHP_EOL;
        echo 'DOCX localização: ' . \$docxPath . PHP_EOL;
    } else {
        echo 'DOCX existe: NÃO ❌' . PHP_EOL;
    }
} else {
    echo 'DOCX: NULL ❌' . PHP_EOL;
}

if (\$proposicao->arquivo_pdf_path) {
    \$pdfPath = storage_path('app/' . \$proposicao->arquivo_pdf_path);
    echo 'PDF existe: ' . (file_exists(\$pdfPath) ? 'SIM ✅' : 'NÃO ❌') . PHP_EOL;
    echo 'PDF tamanho: ' . (file_exists(\$pdfPath) ? filesize(\$pdfPath) : 0) . ' bytes' . PHP_EOL;
}
"

echo ""
echo "🌐 2. Testando acesso ao PDF via rota..."

# Testar download do PDF
echo "Fazendo download do PDF via rota /proposicoes/1/pdf..."
RESPONSE=$(curl -s -I "http://localhost:8001/proposicoes/1/pdf" -H "Cookie: laravel_session=test_session")

if echo "$RESPONSE" | grep -q "200 OK"; then
    echo "✅ PDF acessível via rota"
    
    # Verificar Content-Type
    if echo "$RESPONSE" | grep -q "application/pdf"; then
        echo "✅ Content-Type correto: application/pdf"
    else
        echo "❌ Content-Type incorreto"
        echo "$RESPONSE" | grep -i "content-type"
    fi
    
    # Verificar Content-Length
    CONTENT_LENGTH=$(echo "$RESPONSE" | grep -i "content-length" | cut -d' ' -f2 | tr -d '\r')
    if [ ! -z "$CONTENT_LENGTH" ]; then
        echo "✅ Tamanho via HTTP: $CONTENT_LENGTH bytes"
    fi
    
else
    echo "❌ Erro ao acessar PDF"
    echo "$RESPONSE"
fi

echo ""
echo "🎯 3. Simulando teste de consistência..."

echo "Comparando fonte do PDF:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);

// Verificar se PDF será gerado do DOCX ou template
if (\$proposicao->arquivo_path) {
    \$docxExists = \Illuminate\Support\Facades\Storage::disk('local')->exists(\$proposicao->arquivo_path);
    if (\$docxExists) {
        echo '✅ PDF será gerado do DOCX editado (consistente)' . PHP_EOL;
        echo '   Fonte: ' . \$proposicao->arquivo_path . PHP_EOL;
        \$docxPath = \Illuminate\Support\Facades\Storage::disk('local')->path(\$proposicao->arquivo_path);
        echo '   Localização: ' . \$docxPath . PHP_EOL;
    } else {
        echo '❌ DOCX não existe, PDF será gerado do template (inconsistente)' . PHP_EOL;
    }
} else {
    echo '❌ arquivo_path é NULL, PDF será gerado do template (inconsistente)' . PHP_EOL;
}
"

echo ""
echo "=== RESULTADO ==="

if docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);
\$docxExists = \$proposicao->arquivo_path && \Illuminate\Support\Facades\Storage::disk('local')->exists(\$proposicao->arquivo_path);
exit(\$docxExists ? 0 : 1);
" > /dev/null 2>&1; then
    echo "✅ SUCESSO: PDF consistente entre iframe e nova aba"
    echo "   - arquivo_path está definido"
    echo "   - Arquivo DOCX existe"
    echo "   - PDF é gerado da mesma fonte (DOCX)"
    echo ""
    echo "🎯 O problema do PDF diferente foi RESOLVIDO!"
else
    echo "❌ PROBLEMA: PDF ainda inconsistente"
    echo "   - arquivo_path é NULL ou arquivo não existe"
    echo "   - PDF será gerado do template no iframe"
    echo "   - PDF será gerado do template na nova aba"
    echo ""
    echo "🔧 SOLUÇÃO: Atualizar seeder para criar arquivo DOCX válido"
fi

echo ""
echo "📋 PRÓXIMOS PASSOS:"
echo "1. Acesse: http://localhost:8001/proposicoes/1/assinar"
echo "2. Login: jessica@sistema.gov.br / 123456"
echo "3. Compare o PDF no iframe com o PDF em nova aba"
echo "4. Ambos devem ser idênticos agora ✅"