#!/bin/bash

echo "🔧 FORÇANDO REGENERAÇÃO DO PDF DE ASSINATURA"
echo "============================================"

echo "📋 1. Dados da proposição 1:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
echo 'ID: ' . \$proposicao->id . PHP_EOL;
echo 'Status: ' . \$proposicao->status . PHP_EOL;
echo 'Arquivo Path: ' . \$proposicao->arquivo_path . PHP_EOL;
"

echo ""
echo "🗑️  2. Removendo PDF antigo para forçar regeneração:"
sudo rm -f /home/bruno/legisinc/storage/app/proposicoes/pdfs/1/proposicao_1.pdf
echo "PDF antigo removido"

echo ""
echo "🔧 3. Executando geração de PDF via código PHP:"
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
\$controller = new App\Http\Controllers\ProposicaoAssinaturaController();

try {
    // Usar reflexão para acessar método privado
    \$reflection = new ReflectionClass(\$controller);
    \$method = \$reflection->getMethod('gerarPDFParaAssinatura');
    \$method->setAccessible(true);
    
    echo 'Iniciando geração de PDF...' . PHP_EOL;
    \$method->invoke(\$controller, \$proposicao);
    echo 'PDF gerado com sucesso!' . PHP_EOL;
    
} catch (Exception \$e) {
    echo 'ERRO: ' . \$e->getMessage() . PHP_EOL;
    echo 'Trace: ' . \$e->getTraceAsString() . PHP_EOL;
}
"

echo ""
echo "📁 4. Verificando se PDF foi gerado:"
PDF_PATH="/home/bruno/legisinc/storage/app/proposicoes/pdfs/1/proposicao_1.pdf"
if [ -f "$PDF_PATH" ]; then
    echo "✅ PDF gerado: $PDF_PATH"
    echo "📏 Tamanho: $(stat --format=%s "$PDF_PATH") bytes"
    echo "📅 Criado: $(stat --format=%y "$PDF_PATH")"
else
    echo "❌ PDF não foi gerado"
fi

echo ""
echo "📋 5. Verificando logs para debug:"
tail -10 /home/bruno/legisinc/storage/logs/laravel.log | grep -E "(PDF Assinatura|Arquivo encontrado|erro|Erro|ERROR)" || echo "Nenhum log de erro encontrado"

echo ""
echo "✅ TESTE CONCLUÍDO!"