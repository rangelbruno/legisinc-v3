#!/bin/bash

echo "Testando salvamento do OnlyOffice..."

echo "1. Verificando conectividade entre containers..."
if docker exec legisinc-app curl -s http://legisinc-onlyoffice/healthcheck > /dev/null; then
    echo "✓ Conectividade OK"
else
    echo "✗ Problema de conectividade"
    exit 1
fi

echo ""
echo "2. Verificando arquivo de template atual..."
docker exec legisinc-app ls -la /var/www/html/storage/app/templates/template_1_simple.txt

echo ""
echo "3. Verificando logs do OnlyOffice callback (últimas 10 linhas)..."
docker exec legisinc-app tail -n 10 /var/www/html/storage/logs/laravel.log | grep -E "(OnlyOffice|callback)"

echo ""
echo "4. Testando download direto do template..."
curl -s -I http://localhost:8001/api/templates/4/download

echo ""
echo "Agora faça uma alteração no template via OnlyOffice e observe os logs:"
echo "docker logs -f legisinc-app"