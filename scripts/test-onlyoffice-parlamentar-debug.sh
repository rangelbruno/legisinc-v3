#!/bin/bash

echo "=== Testing OnlyOffice Parlamentar Editor - Debug ==="
echo "Date: $(date)"

# Test if proposição 1 exists and is in correct status
echo -e "\n1. Checking proposição status..."
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT id, tipo, ementa, status, arquivo_path, template_id, LENGTH(conteudo) as conteudo_length FROM proposicoes WHERE id = 1;"

# Test the download URL endpoint from OnlyOffice container perspective
echo -e "\n2. Testing document download URL from OnlyOffice container..."
TOKEN=$(echo -n "1|$(date +%s)" | base64)
echo "Generated token: $TOKEN"

echo -e "\n3. Testing download endpoint from OnlyOffice container..."
docker exec legisinc-onlyoffice curl -v -L "http://legisinc-app/proposicoes/1/onlyoffice/download?token=$TOKEN" 2>&1 | head -20

echo -e "\n4. Testing OnlyOffice API accessibility..."
docker exec legisinc-onlyoffice curl -s -I "http://localhost/web-apps/apps/api/documents/api.js"

echo -e "\n5. Testing direct document generation via console..."
docker exec legisinc-app php artisan tinker --execute="
\$prop = App\Models\Proposicao::find(1);
if (\$prop) {
    echo 'Proposição encontrada: ' . \$prop->tipo . ' - ' . \$prop->ementa . PHP_EOL;
    echo 'Status: ' . \$prop->status . PHP_EOL;
    echo 'Template ID: ' . \$prop->template_id . PHP_EOL;
    echo 'Arquivo Path: ' . \$prop->arquivo_path . PHP_EOL;
    echo 'Conteúdo length: ' . strlen(\$prop->conteudo ?? '') . PHP_EOL;
    
    try {
        \$service = app(App\Services\OnlyOffice\OnlyOfficeService::class);
        echo 'Testando geração de documento...' . PHP_EOL;
        \$result = \$service->gerarDocumentoProposicao(\$prop);
        echo 'Tipo de resultado: ' . get_class(\$result) . PHP_EOL;
    } catch (Exception \$e) {
        echo 'ERRO: ' . \$e->getMessage() . PHP_EOL;
        echo 'Arquivo: ' . \$e->getFile() . ':' . \$e->getLine() . PHP_EOL;
    }
} else {
    echo 'Proposição não encontrada' . PHP_EOL;
}
"

echo -e "\n=== Test completed ==="