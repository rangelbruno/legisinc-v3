#!/bin/bash

echo "🔄 Reiniciando OnlyOffice com configurações em Português (Brasil)..."

# Parar o container do OnlyOffice
echo "⏹️  Parando container do OnlyOffice..."
docker stop legisinc-onlyoffice

# Remover o container
echo "🗑️  Removendo container do OnlyOffice..."
docker rm legisinc-onlyoffice

# Limpar volumes de cache (opcional, mas recomendado para mudanças de idioma)
echo "🧹 Limpando cache do OnlyOffice..."
docker volume rm legisinc_onlyoffice_cache legisinc_onlyoffice_forgotten 2>/dev/null || true

# Reconstruir e iniciar o serviço
echo "🚀 Reconstruindo e iniciando OnlyOffice..."
docker-compose up -d onlyoffice-documentserver

# Aguardar o serviço estar saudável
echo "⏳ Aguardando OnlyOffice estar pronto..."
timeout=120
counter=0
while [ $counter -lt $timeout ]; do
    if docker exec legisinc-onlyoffice curl -f http://localhost/healthcheck >/dev/null 2>&1; then
        echo "✅ OnlyOffice está funcionando!"
        break
    fi
    echo "⏳ Aguardando... ($counter/$timeout segundos)"
    sleep 5
    counter=$((counter + 5))
done

if [ $counter -ge $timeout ]; then
    echo "❌ Timeout aguardando OnlyOffice"
    exit 1
fi

echo "🎉 OnlyOffice reiniciado com sucesso!"
echo "🌍 Idioma padrão configurado para Português (Brasil)"
echo "🔗 Acesse: http://localhost:8080"
echo ""
echo "💡 Dica: Limpe o cache do navegador para ver as mudanças"
