#!/bin/bash

echo "Corrigindo problema de encoding RTF no OnlyOffice..."

# Reiniciar o container do OnlyOffice com configurações corretas
echo "Parando container OnlyOffice..."
docker stop legisinc-onlyoffice

echo "Removendo container OnlyOffice..."
docker rm legisinc-onlyoffice

echo "Iniciando OnlyOffice com configurações de encoding corrigidas..."
docker run -d \
  --name legisinc-onlyoffice \
  --restart always \
  -p 8080:80 \
  --network legisinc_legisinc_network \
  -e WOPI_ENABLED=false \
  -e USE_UNAUTHORIZED_STORAGE=true \
  -e ALLOW_PRIVATE_IP_ADDRESS=true \
  -e ALLOW_META_IP_ADDRESS=true \
  -e JWT_ENABLED=false \
  -e JWT_SECRET=MySecretKey123 \
  -e DB_TYPE=postgres \
  -e DB_HOST=db \
  -e DB_PORT=5432 \
  -e DB_NAME=legisinc \
  -e DB_USER=postgres \
  -e DB_PASS=123456 \
  -e FONTS_ENCODING=utf-8 \
  -e DOCUMENT_ENCODING=utf-8 \
  -e RTF_ENCODING_FIX=true \
  onlyoffice/documentserver:8.0

echo "Aguardando OnlyOffice inicializar..."
sleep 30

echo "Verificando se o OnlyOffice está rodando..."
if docker ps | grep -q legisinc-onlyoffice; then
    echo "✓ OnlyOffice reiniciado com sucesso!"
    echo "✓ Configurações de encoding aplicadas"
    
    # Verificar se está respondendo
    echo "Testando conectividade..."
    if curl -s http://localhost:8080/healthcheck > /dev/null; then
        echo "✓ OnlyOffice está respondendo"
    else
        echo "⚠ OnlyOffice pode ainda estar inicializando. Aguarde mais alguns instantes."
    fi
else
    echo "✗ Erro ao reiniciar OnlyOffice"
    exit 1
fi

echo ""
echo "Configurações aplicadas:"
echo "- Content-Type: application/rtf (sem charset)"
echo "- Encoding: UTF-8 interno"
echo "- RTF fix: Habilitado"
echo ""
echo "Teste novamente a edição do template!"