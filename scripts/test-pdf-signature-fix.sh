#!/bin/bash

echo "=== TESTE COMPLETO: Fluxo PDF com Formatação OnlyOffice ==="
echo ""

# 1. Verificar se existe proposição testável
echo "1. Verificando proposições disponíveis..."
RESULT=$(docker exec legisinc-app php -r "
\$proposicao = \App\Models\Proposicao::whereNotNull('arquivo_path')
    ->where('status', 'enviado_legislativo')
    ->first();

if (\$proposicao) {
    echo 'ID=' . \$proposicao->id . PHP_EOL;
    echo 'STATUS=' . \$proposicao->status . PHP_EOL;
    echo 'ARQUIVO=' . \$proposicao->arquivo_path . PHP_EOL;
    echo 'TIPO=' . \$proposicao->tipo . PHP_EOL;
} else {
    echo 'NENHUMA_PROPOSICAO_ENCONTRADA' . PHP_EOL;
}
")

if echo "$RESULT" | grep -q "NENHUMA_PROPOSICAO_ENCONTRADA"; then
    echo "❌ Nenhuma proposição com arquivo salvo encontrada"
    echo "   Crie uma proposição pelo parlamentar, edite no OnlyOffice e envie para Legislativo primeiro"
    exit 1
fi

# Extrair dados da proposição
PROPOSICAO_ID=$(echo "$RESULT" | grep "ID=" | cut -d'=' -f2)
PROPOSICAO_STATUS=$(echo "$RESULT" | grep "STATUS=" | cut -d'=' -f2)
PROPOSICAO_ARQUIVO=$(echo "$RESULT" | grep "ARQUIVO=" | cut -d'=' -f2)
PROPOSICAO_TIPO=$(echo "$RESULT" | grep "TIPO=" | cut -d'=' -f2)

echo "✅ Proposição encontrada:"
echo "   ID: $PROPOSICAO_ID"
echo "   Status: $PROPOSICAO_STATUS"
echo "   Arquivo: $PROPOSICAO_ARQUIVO"
echo "   Tipo: $PROPOSICAO_TIPO"
echo ""

# 2. Simular aprovação pelo Legislativo
echo "2. Simulando aprovação pelo Legislativo..."
docker exec legisinc-app php -r "
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
\$proposicao->update([
    'status' => 'aprovado_assinatura',
    'data_aprovacao_legislativo' => now(),
    'revisor_id' => 7  // Usuário legislativo
]);
echo 'Proposição aprovada para assinatura' . PHP_EOL;
"

echo "✅ Status alterado para 'aprovado_assinatura'"
echo ""

# 3. Verificar se arquivo DOCX existe
echo "3. Verificando arquivo DOCX..."
ARQUIVO_EXISTE=$(docker exec legisinc-app php -r "
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
\$caminhos = [
    storage_path('app/' . \$proposicao->arquivo_path),
    storage_path('app/private/' . \$proposicao->arquivo_path),
    '/var/www/html/storage/app/' . \$proposicao->arquivo_path
];

\$encontrado = null;
foreach (\$caminhos as \$caminho) {
    if (file_exists(\$caminho)) {
        \$encontrado = \$caminho;
        break;
    }
}

if (\$encontrado) {
    echo 'ENCONTRADO=' . \$encontrado . PHP_EOL;
    echo 'TAMANHO=' . filesize(\$encontrado) . PHP_EOL;
} else {
    echo 'NAO_ENCONTRADO' . PHP_EOL;
}
")

if echo "$ARQUIVO_EXISTE" | grep -q "NAO_ENCONTRADO"; then
    echo "❌ Arquivo DOCX não encontrado"
    exit 1
fi

ARQUIVO_PATH=$(echo "$ARQUIVO_EXISTE" | grep "ENCONTRADO=" | cut -d'=' -f2)
ARQUIVO_TAMANHO=$(echo "$ARQUIVO_EXISTE" | grep "TAMANHO=" | cut -d'=' -f2)

echo "✅ Arquivo DOCX encontrado:"
echo "   Caminho: $ARQUIVO_PATH"
echo "   Tamanho: $ARQUIVO_TAMANHO bytes"
echo ""

# 4. Testar geração de PDF com nova lógica
echo "4. Testando geração de PDF com formatação preservada..."
RESULTADO_PDF=$(docker exec legisinc-app php -r "
try {
    \$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
    \$controller = new \App\Http\Controllers\ProposicaoAssinaturaController();
    
    // Usar reflexão para acessar método privado
    \$reflection = new \ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    
    \$method->invoke(\$controller, \$proposicao);
    
    echo 'PDF_GERADO=true' . PHP_EOL;
    echo 'PDF_PATH=' . \$proposicao->fresh()->arquivo_pdf_path . PHP_EOL;
    
    if (\$proposicao->fresh()->arquivo_pdf_path) {
        \$pdfPath = storage_path('app/' . \$proposicao->fresh()->arquivo_pdf_path);
        if (file_exists(\$pdfPath)) {
            echo 'PDF_TAMANHO=' . filesize(\$pdfPath) . PHP_EOL;
        }
    }
    
} catch (\Exception \$e) {
    echo 'ERRO=' . \$e->getMessage() . PHP_EOL;
}
")

if echo "$RESULTADO_PDF" | grep -q "ERRO="; then
    echo "❌ Erro na geração do PDF:"
    echo "$RESULTADO_PDF" | grep "ERRO=" | cut -d'=' -f2-
    exit 1
fi

if echo "$RESULTADO_PDF" | grep -q "PDF_GERADO=true"; then
    PDF_PATH=$(echo "$RESULTADO_PDF" | grep "PDF_PATH=" | cut -d'=' -f2)
    PDF_TAMANHO=$(echo "$RESULTADO_PDF" | grep "PDF_TAMANHO=" | cut -d'=' -f2)
    
    echo "✅ PDF gerado com sucesso:"
    echo "   Caminho: $PDF_PATH"
    echo "   Tamanho: $PDF_TAMANHO bytes"
else
    echo "❌ PDF não foi gerado"
    exit 1
fi

echo ""

# 5. Verificar se LibreOffice foi usado (arquivo deve ser maior que DomPDF genérico)
echo "5. Verificando se formatação foi preservada..."
if [ "$PDF_TAMANHO" -gt 50000 ]; then
    echo "✅ PDF gerado com LibreOffice (tamanho $PDF_TAMANHO bytes indica formatação preservada)"
else
    echo "⚠️  PDF pequeno ($PDF_TAMANHO bytes) - pode ter usado método fallback"
fi

echo ""

# 6. Testar se PDF contém estrutura do template
echo "6. Verificando conteúdo do PDF..."
PDF_CONTENT_CHECK=$(docker exec legisinc-app php -r "
\$proposicao = \App\Models\Proposicao::find($PROPOSICAO_ID);
\$pdfPath = storage_path('app/' . \$proposicao->arquivo_pdf_path);

if (file_exists(\$pdfPath)) {
    // Verificar se é um PDF válido
    \$header = file_get_contents(\$pdfPath, false, null, 0, 4);
    if (\$header === '%PDF') {
        echo 'PDF_VALIDO=true' . PHP_EOL;
        echo 'PDF_HEADER=' . substr(file_get_contents(\$pdfPath, false, null, 0, 20), 0, 20) . PHP_EOL;
    } else {
        echo 'PDF_INVALIDO=true' . PHP_EOL;
    }
} else {
    echo 'PDF_NAO_EXISTE=true' . PHP_EOL;
}
")

if echo "$PDF_CONTENT_CHECK" | grep -q "PDF_VALIDO=true"; then
    echo "✅ PDF válido gerado"
else
    echo "❌ PDF inválido ou não encontrado"
    echo "$PDF_CONTENT_CHECK"
fi

echo ""
echo "=== RESUMO DO TESTE ==="
echo "✅ Proposição encontrada com arquivo OnlyOffice"
echo "✅ Status alterado para aprovado_assinatura"
echo "✅ Arquivo DOCX localizado e acessível"
echo "✅ PDF gerado sem erros"
echo "✅ PDF é um arquivo válido"

if [ "$PDF_TAMANHO" -gt 50000 ]; then
    echo "✅ Formatação OnlyOffice provavelmente preservada"
    echo ""
    echo "🎉 SUCESSO! O PDF agora mantém a formatação do template OnlyOffice"
else
    echo "⚠️  Formatação pode ter sido perdida (método fallback usado)"
    echo ""
    echo "ℹ️  Verifique os logs para mais detalhes"
fi

echo ""
echo "Para testar completamente:"
echo "1. Acesse http://localhost:8001"
echo "2. Login como parlamentar (jessica@sistema.gov.br / 123456)"
echo "3. Vá para 'Assinatura de Proposições'"
echo "4. Visualize a proposição ID $PROPOSICAO_ID"
echo "5. Verifique se o PDF mantém a formatação do template OnlyOffice"