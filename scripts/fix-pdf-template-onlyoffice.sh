#!/bin/bash

echo "=== FIX: PDF com Template OnlyOffice Correto ==="
echo ""

echo "1. Verificando situação atual..."
CURRENT_STATUS=$(docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);
echo 'ID: ' . \$proposicao->id . PHP_EOL;
echo 'arquivo_path (DOCX): ' . (\$proposicao->arquivo_path ?: 'NULL') . PHP_EOL;
echo 'arquivo_pdf_path: ' . \$proposicao->arquivo_pdf_path . PHP_EOL;

if (\$proposicao->arquivo_pdf_path) {
    \$pdfPath = storage_path('app/' . \$proposicao->arquivo_pdf_path);
    echo 'PDF size atual: ' . (file_exists(\$pdfPath) ? filesize(\$pdfPath) : 0) . ' bytes' . PHP_EOL;
}
")

echo "$CURRENT_STATUS"
echo ""

echo "2. Criando arquivo DOCX simulando edição do OnlyOffice..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);

// Simular que o OnlyOffice salvou um arquivo DOCX
\$docxPath = 'proposicoes/proposicao_1_' . time() . '.docx';

// Copiar um template DOCX existente ou criar um novo
\$sourceTemplate = storage_path('app/private/templates/template_mocao_seeder.rtf');
\$destPath = storage_path('app/' . \$docxPath);

// Criar diretório se não existir
\$dir = dirname(\$destPath);
if (!file_exists(\$dir)) {
    mkdir(\$dir, 0755, true);
}

// Para simular, vamos criar um arquivo DOCX vazio (na prática, seria o arquivo editado do OnlyOffice)
// Aqui usamos um truque: copiar o próprio PDF como se fosse DOCX para ter conteúdo
\$pdfSource = storage_path('app/' . \$proposicao->arquivo_pdf_path);
if (file_exists(\$pdfSource)) {
    // Criar um DOCX real usando um template base
    // Como não temos DOCX real, vamos criar um arquivo placeholder
    file_put_contents(\$destPath, 'Documento DOCX simulado com conteúdo do template editado pelo Legislativo');
    
    \$proposicao->arquivo_path = \$docxPath;
    \$proposicao->save();
    
    echo 'DOCX criado: ' . \$docxPath . PHP_EOL;
    echo 'Tamanho: ' . filesize(\$destPath) . ' bytes' . PHP_EOL;
} else {
    echo 'Erro: PDF fonte não encontrado' . PHP_EOL;
}
"

echo ""
echo "3. Regenerando PDF a partir do DOCX 'editado'..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = \App\Models\Proposicao::find(1);

// Forçar regeneração do PDF
\$controller = new \App\Http\Controllers\ProposicaoAssinaturaController();
\$reflection = new \ReflectionClass(\$controller);
\$method = \$reflection->getMethod('gerarPDFParaAssinatura');
\$method->setAccessible(true);

try {
    \$method->invoke(\$controller, \$proposicao);
    \$proposicao->refresh();
    
    if (\$proposicao->arquivo_pdf_path) {
        \$pdfPath = storage_path('app/' . \$proposicao->arquivo_pdf_path);
        echo 'PDF regenerado: ' . \$proposicao->arquivo_pdf_path . PHP_EOL;
        echo 'Novo tamanho: ' . (file_exists(\$pdfPath) ? filesize(\$pdfPath) : 0) . ' bytes' . PHP_EOL;
    }
} catch (\Exception \$e) {
    echo 'Erro ao gerar PDF: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "=== RESULTADO ==="
echo "O problema é que quando o Legislativo edita no OnlyOffice,"
echo "o sistema deveria salvar o arquivo DOCX mas não está salvando."
echo ""
echo "SOLUÇÃO NECESSÁRIA:"
echo "1. Garantir que OnlyOffice callback salve o arquivo DOCX"
echo "2. Usar esse DOCX para gerar o PDF (não o template)"
echo ""
echo "WORKAROUND TEMPORÁRIO:"
echo "Para testar, criamos um arquivo DOCX simulado."
echo "Agora o PDF deveria ser gerado deste arquivo."