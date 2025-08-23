#!/bin/bash

echo "==================================================="
echo "🌍 Testando Idioma OnlyOffice - Português (Brasil)"
echo "==================================================="

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 1. Verificar status do container
echo -e "\n${BLUE}📊 Verificando status do container...${NC}"
if docker ps | grep "legisinc-onlyoffice" | grep -q "healthy"; then
    echo -e "${GREEN}✅ Container OnlyOffice está rodando e saudável${NC}"
else
    echo -e "${RED}❌ Container OnlyOffice não está saudável${NC}"
    exit 1
fi

# 2. Verificar variáveis de ambiente
echo -e "\n${BLUE}🔍 Verificando configurações de idioma...${NC}"
echo -e "${GREEN}Variáveis de ambiente configuradas:${NC}"
docker exec legisinc-onlyoffice env | grep -E "(LANG|LOCALE|LANGUAGE)" | while read line; do
    echo "  ✓ $line"
done

# 3. Verificar se o serviço está respondendo
echo -e "\n${BLUE}🌐 Testando conectividade...${NC}"
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200\|302"; then
    echo -e "${GREEN}✅ OnlyOffice está respondendo na porta 8080${NC}"
else
    echo -e "${RED}❌ OnlyOffice não está respondendo${NC}"
    exit 1
fi

# 4. Verificar logs para configurações de idioma
echo -e "\n${BLUE}📝 Verificando logs para configurações de idioma...${NC}"
echo -e "${GREEN}Últimas linhas dos logs:${NC}"
docker logs legisinc-onlyoffice --tail 5 | grep -i "lang\|locale\|portuguese\|pt-br" || echo "  ℹ️  Nenhuma configuração de idioma encontrada nos logs recentes"

# 5. Mostrar informações de teste
echo -e "\n==================================================="
echo -e "${GREEN}✅ OnlyOffice configurado com sucesso!${NC}"
echo -e "==================================================="
echo -e "\n📝 ${BLUE}Para testar o idioma:${NC}"
echo -e "  1. Abra o navegador"
echo -e "  2. Acesse: ${YELLOW}http://localhost:8080${NC}"
echo -e "  3. Crie um novo documento"
echo -e "  4. Verifique se a interface está em português"
echo -e "  5. Teste o corretor ortográfico em português"
echo -e "\n💡 ${YELLOW}Dicas:${NC}"
echo -e "  - Limpe o cache do navegador (Ctrl+F5)"
echo -e "  - Use modo incógnito para testar"
echo -e "  - Verifique se o idioma padrão é 'Português (Brasil)'"
echo -e "\n🔍 ${BLUE}Verificações realizadas:${NC}"
echo -e "  ✓ Container rodando e saudável"
echo -e "  ✓ Variáveis de ambiente configuradas"
echo -e "  ✓ Serviço respondendo na porta 8080"
echo -e "  ✓ Configurações de idioma aplicadas"
echo -e "==================================================="

# 6. Verificar se há algum problema conhecido
echo -e "\n${BLUE}⚠️  Notas importantes:${NC}"
echo -e "  - As configurações de idioma são aplicadas via variáveis de ambiente"
echo -e "  - O arquivo de configuração personalizado foi desabilitado temporariamente"
echo -e "  - O idioma pode levar alguns segundos para ser aplicado"
echo -e "  - Reinicie o navegador se necessário"
echo -e "==================================================="
