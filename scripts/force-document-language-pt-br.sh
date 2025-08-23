#!/bin/bash

echo "==================================================="
echo "🇧🇷 FORÇANDO IDIOMA DO DOCUMENTO - PT-BR"
echo "==================================================="

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${YELLOW}📝 Aplicando configurações de idioma do documento...${NC}"

# 1. Configurar idioma do documento no OnlyOffice
docker exec legisinc-onlyoffice bash -c '
echo "=== Configurando idioma do documento para PT-BR ==="

# Configurar local.json para forçar idioma do documento
cat > /etc/onlyoffice/documentserver/local.json << EOF
{
  "services": {
    "CoAuthoring": {
      "request": {
        "defaultLang": "pt-BR"
      }
    }
  },
  "editor": {
    "lang": "pt-BR",
    "locale": "pt_BR.UTF-8",
    "defaultLanguage": "pt-BR",
    "spellcheckerLanguage": "pt-BR"
  },
  "document": {
    "lang": "pt-BR",
    "defaultLanguage": "pt-BR"
  },
  "plugins": {
    "spellchecker": {
      "language": "pt-BR",
      "dictionaries": ["pt-BR", "pt-PT"]
    }
  }
}
EOF

# 2. Configurar default.json para sobrescrever configurações
cat > /etc/onlyoffice/documentserver/default.json << EOF
{
  "services": {
    "CoAuthoring": {
      "request": {
        "defaultLang": "pt-BR"
      }
    }
  },
  "editor": {
    "lang": "pt-BR",
    "locale": "pt_BR.UTF-8",
    "region": "pt-BR",
    "defaultLanguage": "pt-BR",
    "spellcheckerLanguage": "pt-BR"
  },
  "document": {
    "lang": "pt-BR",
    "defaultLanguage": "pt-BR"
  },
  "common": {
    "lang": "pt-BR",
    "locale": "pt_BR.UTF-8"
  }
}
EOF

# 3. Configurar variáveis de ambiente do sistema
export LANG=pt_BR.UTF-8
export LC_ALL=pt_BR.UTF-8
export LANGUAGE=pt_BR:pt

# 4. Configurar locale do sistema
locale-gen pt_BR.UTF-8
update-locale LANG=pt_BR.UTF-8 LC_ALL=pt_BR.UTF-8

echo "Configurações aplicadas!"
'

echo -e "${GREEN}✅ Configuração de idioma aplicada${NC}"

# 2. Reiniciar serviços com configuração de PT-BR
echo -e "${YELLOW}🔄 Reiniciando serviços com configuração PT-BR...${NC}"
docker exec legisinc-onlyoffice bash -c '
export LANG=pt_BR.UTF-8
export LC_ALL=pt_BR.UTF-8

supervisorctl restart all
nginx -s reload

sleep 5
supervisorctl status
'

echo -e "${GREEN}✅ Serviços reiniciados${NC}"

# 3. Verificar configurações aplicadas
echo -e "${YELLOW}🔍 Verificando configurações...${NC}"
docker exec legisinc-onlyoffice bash -c '
echo "=== Verificando configurações de idioma ==="
echo "LANG: $LANG"
echo "LC_ALL: $LC_ALL"
echo "LANGUAGE: $LANGUAGE"
echo ""
echo "=== Verificando arquivos de configuração ==="
echo "local.json:"
cat /etc/onlyoffice/documentserver/local.json | grep -E "(lang|locale|defaultLanguage)" || echo "Arquivo não encontrado"
echo ""
echo "default.json:"
cat /etc/onlyoffice/documentserver/default.json | grep -E "(lang|locale|defaultLanguage)" || echo "Arquivo não encontrado"
'

echo -e "\n${GREEN}✅ Configuração aplicada!${NC}"
echo -e "\n${YELLOW}⚠️ IMPORTANTE:${NC}"
echo "1. Limpe o cache do navegador (Ctrl+Shift+Delete)"
echo "2. Use modo incógnito para testar"
echo "3. Acesse: /proposicoes/3/onlyoffice/editor-parlamentar"
echo ""
echo "O idioma do documento deve aparecer como 'Português (Brasil)'"
echo "na barra de status inferior direita do editor."
echo "==================================================="
