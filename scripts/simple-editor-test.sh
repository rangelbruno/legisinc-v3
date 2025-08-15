#!/bin/bash

echo "🧪 === TESTE SIMPLES EDITOR ==="
echo ""

echo "📋 1. Status atual da proposição..."
docker exec legisinc-app php artisan tinker --execute="
\$p = App\Models\Proposicao::find(1);
echo 'ID: ' . \$p->id . PHP_EOL;
echo 'Status: ' . \$p->status . PHP_EOL;
echo 'Arquivo Path: ' . \$p->arquivo_path . PHP_EOL;
"

echo ""
echo "🔗 2. Testando acesso direto ao editor..."
curl -s -I "http://localhost:8001/proposicoes/1/onlyoffice/editor" | head -10

echo ""
echo "📥 3. Testando download direto..."
curl -s -I "http://localhost:8001/proposicoes/1/onlyoffice/download?token=test" | head -5

echo ""
echo "🌐 4. Para testar manualmente:"
echo "   1. Abra: http://localhost:8001"
echo "   2. Login: joao@sistema.gov.br / 123456"
echo "   3. Acesse: http://localhost:8001/proposicoes/1/onlyoffice/editor"
echo "   4. Verifique console do navegador (F12)"

echo ""
echo "✅ URLs prontas para teste!"