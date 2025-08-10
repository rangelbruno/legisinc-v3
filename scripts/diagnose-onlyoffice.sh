#!/bin/bash

echo "🔍 Diagnóstico do OnlyOffice DocumentServer"
echo "=========================================="
echo

# Verificar se o container está rodando
echo "📦 Status do Container:"
if docker ps | grep -q "legisinc-onlyoffice"; then
    echo "✅ Container OnlyOffice está rodando"
else
    echo "❌ Container OnlyOffice NÃO está rodando"
    echo "Execute: docker start legisinc-onlyoffice"
    exit 1
fi

# Verificar conectividade
echo
echo "🌐 Conectividade:"
if curl -s -I http://localhost:8080 > /dev/null; then
    echo "✅ OnlyOffice responde em http://localhost:8080"
else
    echo "❌ OnlyOffice NÃO responde em http://localhost:8080"
fi

# Verificar configurações JWT
echo
echo "🔐 Configurações de Segurança:"
JWT_ENABLED=$(docker exec legisinc-onlyoffice grep -A 5 '"browser"' /etc/onlyoffice/documentserver/local.json | grep -o 'false\|true' | head -n 1)
echo "JWT Browser Enabled: $JWT_ENABLED"

ALLOW_PRIVATE=$(docker exec legisinc-onlyoffice grep -A 5 '"allowPrivateIPAddress"' /etc/onlyoffice/documentserver/local.json | grep -o 'true\|false')
echo "Allow Private IP: $ALLOW_PRIVATE"

# Verificar logs recentes
echo
echo "📋 Logs Recentes:"
docker logs legisinc-onlyoffice --tail=5 2>/dev/null | tail -3

# Verificar templates com problemas
echo
echo "📄 Status dos Templates:"
docker exec legisinc-app php artisan templates:fix-files | grep -E "(Template|Total|faltando|Corrigidos)"

echo
echo "✅ Diagnóstico concluído!"
echo
echo "💡 Próximos passos para usar o editor:"
echo "1. Faça login no sistema: http://localhost:8001"
echo "2. Vá para: Administração > Templates"
echo "3. Clique em 'Editar' no template desejado"
echo "4. O editor OnlyOffice deve abrir automaticamente"
echo
echo "❓ Se ainda tiver problemas:"
echo "- Verifique se está logado no sistema"
echo "- Limpe o cache do navegador"
echo "- Verifique os logs: docker logs legisinc-onlyoffice"