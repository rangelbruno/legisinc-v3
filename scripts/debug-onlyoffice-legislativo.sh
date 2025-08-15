#!/bin/bash

echo "🔍 === DEBUG ONLYOFFICE LEGISLATIVO ==="
echo ""

echo "📋 1. Status da proposição 1..."
docker exec legisinc-app php artisan tinker --execute="
\$p = App\Models\Proposicao::find(1);
echo 'ID: ' . \$p->id . PHP_EOL;
echo 'Status: ' . \$p->status . PHP_EOL;
echo 'Arquivo Path: ' . \$p->arquivo_path . PHP_EOL;
echo 'Template ID: ' . (\$p->template_id ?? 'null') . PHP_EOL;
"

echo ""
echo "📁 2. Verificando arquivo físico..."
docker exec legisinc-app ls -la "storage/app/public/proposicoes/proposicao_1_template_4.rtf"

echo ""
echo "🌐 3. Testando URL de download interna..."
docker exec legisinc-app curl -s -I "http://127.0.0.1/proposicoes/1/onlyoffice/download?token=test123" | head -5

echo ""
echo "🔗 4. Testando URL para OnlyOffice..."
docker exec legisinc-onlyoffice curl -s -I "http://legisinc-app/proposicoes/1/onlyoffice/download?token=test123" | head -5

echo ""
echo "⚙️ 5. Gerando configuração OnlyOffice..."
docker exec legisinc-app php artisan tinker --execute="
\$proposicao = App\Models\Proposicao::find(1);
\$controller = new App\Http\Controllers\OnlyOfficeController(new App\Services\OnlyOffice\OnlyOfficeService());
\$method = new ReflectionMethod(\$controller, 'generateOnlyOfficeConfig');
\$method->setAccessible(true);
\$config = \$method->invoke(\$controller, \$proposicao);
echo 'Document URL: ' . \$config['document']['url'] . PHP_EOL;
echo 'Callback URL: ' . \$config['editorConfig']['callbackUrl'] . PHP_EOL;
echo 'Document Key: ' . \$config['document']['key'] . PHP_EOL;
echo 'File Type: ' . \$config['document']['fileType'] . PHP_EOL;
"

echo ""
echo "🏥 6. Status OnlyOffice..."
curl -s http://localhost:8080/healthcheck 2>/dev/null || echo "OnlyOffice health endpoint indisponível"

echo ""
echo "📊 7. Logs do OnlyOffice (últimas 10 linhas)..."
docker logs legisinc-onlyoffice --tail 10 2>/dev/null || echo "Sem logs disponíveis"

echo ""
echo "✅ Debug concluído!"