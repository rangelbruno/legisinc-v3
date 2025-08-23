#!/bin/bash

echo "==================================================="
echo "🌍 Reiniciando OnlyOffice com Português (Brasil)"
echo "==================================================="

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Parar o container OnlyOffice
echo -e "\n${YELLOW}📦 Parando container OnlyOffice...${NC}"
docker stop legisinc-onlyoffice
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Container parado com sucesso${NC}"
else
    echo -e "${RED}❌ Erro ao parar container${NC}"
fi

# 2. Remover o container
echo -e "\n${YELLOW}🗑️ Removendo container OnlyOffice...${NC}"
docker rm legisinc-onlyoffice
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Container removido com sucesso${NC}"
else
    echo -e "${RED}⚠️ Container já removido ou não existe${NC}"
fi

# 3. Limpar volumes de cache (opcional - descomente se necessário)
echo -e "\n${YELLOW}🧹 Limpando cache do OnlyOffice...${NC}"
docker volume rm legisinc_onlyoffice_cache legisinc_onlyoffice_forgotten 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Cache limpo com sucesso${NC}"
else
    echo -e "${YELLOW}⚠️ Cache já limpo ou não existe${NC}"
fi

# 4. Reconstruir e iniciar o container
echo -e "\n${YELLOW}🚀 Iniciando OnlyOffice com configuração em Português...${NC}"
docker-compose up -d onlyoffice-documentserver

# 5. Aguardar o container ficar pronto
echo -e "\n${YELLOW}⏳ Aguardando OnlyOffice inicializar (pode levar até 2 minutos)...${NC}"
sleep 10

# Verificar se o container está rodando
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if docker exec legisinc-onlyoffice curl -f http://localhost/healthcheck > /dev/null 2>&1; then
        echo -e "${GREEN}✅ OnlyOffice está pronto!${NC}"
        break
    fi
    echo -n "."
    sleep 5
    attempt=$((attempt + 1))
done

if [ $attempt -eq $max_attempts ]; then
    echo -e "\n${RED}❌ Timeout: OnlyOffice não respondeu após 2.5 minutos${NC}"
    exit 1
fi

# 6. Verificar configurações aplicadas
echo -e "\n${YELLOW}🔍 Verificando configurações de idioma...${NC}"
echo -e "\n${GREEN}Variáveis de ambiente:${NC}"
docker exec legisinc-onlyoffice env | grep -E "(LANG|LOCALE|LANGUAGE)" | while read line; do
    echo "  ✓ $line"
done

echo -e "\n${GREEN}Configuração default.json:${NC}"
docker exec legisinc-onlyoffice cat /etc/onlyoffice/documentserver/default.json | grep -E "(lang|locale)" | while read line; do
    echo "  ✓ $line"
done

# 7. Mostrar status final
echo -e "\n${YELLOW}📊 Status do container:${NC}"
docker ps | grep legisinc-onlyoffice

echo -e "\n==================================================="
echo -e "${GREEN}✅ OnlyOffice reiniciado com sucesso!${NC}"
echo -e "==================================================="
echo -e "\n📝 ${GREEN}Para testar:${NC}"
echo -e "  1. Acesse: http://localhost:8080"
echo -e "  2. Abra um documento para edição"
echo -e "  3. Verifique se o idioma padrão é Português (Brasil)"
echo -e "  4. Teste o corretor ortográfico em português"
echo -e "\n💡 ${YELLOW}Dica:${NC} Limpe o cache do navegador (Ctrl+F5) para ver as mudanças"
echo -e "==================================================="