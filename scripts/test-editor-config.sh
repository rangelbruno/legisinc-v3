#!/bin/bash

echo "🧪 === TESTE CONFIGURAÇÃO EDITOR ==="
echo ""

echo "🔄 1. Limpando logs..."
docker exec legisinc-app truncate -s 0 storage/logs/laravel.log

echo ""
echo "⚙️ 2. Gerando configuração do editor..."
docker exec legisinc-app php artisan tinker --execute="
use App\Http\Controllers\OnlyOfficeController;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Services\Template\TemplateProcessorService;

\$proposicao = App\Models\Proposicao::find(1);
\$service = new OnlyOfficeService(new TemplateProcessorService());
\$controller = new OnlyOfficeController(\$service);

\$method = new ReflectionMethod(\$controller, 'generateOnlyOfficeConfig');
\$method->setAccessible(true);
\$config = \$method->invoke(\$controller, \$proposicao);

echo 'Document URL: ' . \$config['document']['url'] . PHP_EOL;
echo 'Callback URL: ' . \$config['editorConfig']['callbackUrl'] . PHP_EOL;
echo 'Document Key: ' . \$config['document']['key'] . PHP_EOL;
echo 'File Type: ' . \$config['document']['fileType'] . PHP_EOL;
echo 'Permissions Edit: ' . (\$config['document']['permissions']['edit'] ? 'true' : 'false') . PHP_EOL;
"

echo ""
echo "📋 3. Verificando logs gerados..."
docker exec legisinc-app cat storage/logs/laravel.log

echo ""
echo "🔗 4. Testando URL do documento gerada..."
# Extrair URL do documento dos logs
URL=$(docker exec legisinc-app grep "document_url" storage/logs/laravel.log | sed -n 's/.*"document_url":"\([^"]*\)".*/\1/p')
if [ -n "$URL" ]; then
    echo "URL encontrada: $URL"
    echo "Testando acesso..."
    docker exec legisinc-onlyoffice curl -I "$URL" | head -5
else
    echo "URL não encontrada nos logs"
fi

echo ""
echo "✅ Teste concluído!"