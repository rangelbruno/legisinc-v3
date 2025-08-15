#!/bin/bash

echo "🧪 === SIMULAÇÃO COMPLETA FLUXO ONLYOFFICE ==="
echo ""

# Limpar logs
docker exec legisinc-app truncate -s 0 storage/logs/laravel.log

echo "📋 1. Verificando proposição..."
docker exec legisinc-app php artisan tinker --execute="
\$p = App\Models\Proposicao::find(1);
echo 'ID: ' . \$p->id . PHP_EOL;
echo 'Status: ' . \$p->status . PHP_EOL;
echo 'Arquivo: ' . \$p->arquivo_path . PHP_EOL;
"

echo ""
echo "📥 2. Simulando download pelo OnlyOffice..."
DOCUMENT_URL="http://legisinc-app/proposicoes/1/onlyoffice/download?token=test_sim"

# Simular exatamente como o OnlyOffice baixa
docker exec legisinc-onlyoffice curl -s \
  -H "User-Agent: ASC.DocService" \
  -H "Accept: */*" \
  "$DOCUMENT_URL" \
  -o /tmp/document_test.rtf

echo "Arquivo baixado:"
docker exec legisinc-onlyoffice ls -la /tmp/document_test.rtf
docker exec legisinc-onlyoffice file /tmp/document_test.rtf

echo ""
echo "📤 3. Simulando callback de salvamento..."
CALLBACK_URL="http://legisinc-app/api/onlyoffice/callback/legislativo/1/simulation_key"

# Simular callback exatamente como o OnlyOffice envia
docker exec legisinc-onlyoffice curl -s -X POST \
  -H "Content-Type: application/json" \
  -H "User-Agent: Node.js/Document Server" \
  "$CALLBACK_URL" \
  -d '{
    "key": "simulation_key",
    "status": 2,
    "url": "'"$DOCUMENT_URL"'",
    "users": ["7"],
    "actions": [{"type": 0, "userid": "7"}]
  }'

echo ""
echo ""
echo "📊 4. Verificando logs gerados..."
docker exec legisinc-app cat storage/logs/laravel.log

echo ""
echo "✅ Simulação concluída!"
echo ""
echo "🔍 Se tudo funcionou, o problema pode estar:"
echo "   1. Na configuração do editor JavaScript"
echo "   2. No token de documento"
echo "   3. Na comunicação inicial OnlyOffice -> Laravel"