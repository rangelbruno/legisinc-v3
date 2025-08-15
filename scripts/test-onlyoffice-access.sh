#!/bin/bash

echo "🧪 === TESTE ACESSO ONLYOFFICE ==="
echo ""

echo "🔄 1. Limpando logs..."
docker exec legisinc-app truncate -s 0 storage/logs/laravel.log

echo ""
echo "📥 2. Simulando download pelo OnlyOffice..."
docker exec legisinc-onlyoffice curl -v -H "User-Agent: ASC.DocService" "http://legisinc-app/proposicoes/1/onlyoffice/download?token=test123" -o /tmp/teste.rtf

echo ""
echo "📋 3. Verificando logs do Laravel..."
docker exec legisinc-app tail -20 storage/logs/laravel.log

echo ""
echo "📄 4. Verificando arquivo baixado..."
docker exec legisinc-onlyoffice ls -la /tmp/teste.rtf 2>/dev/null || echo "Arquivo não encontrado"

echo ""
echo "✅ Teste concluído!"