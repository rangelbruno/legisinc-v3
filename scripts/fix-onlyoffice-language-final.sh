#!/bin/bash

echo "==================================================="
echo "🌍 Configuração Final OnlyOffice PT-BR"
echo "==================================================="

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "\n${YELLOW}1. Instalando pacote de idioma PT-BR no container...${NC}"
docker exec legisinc-onlyoffice bash -c '
# Instalar locale pt_BR se não existir
if ! locale -a | grep -q "pt_BR.utf8"; then
    apt-get update && apt-get install -y locales
    locale-gen pt_BR.UTF-8
    update-locale LANG=pt_BR.UTF-8
fi
'

echo -e "\n${YELLOW}2. Configurando documentserver para PT...${NC}"
docker exec legisinc-onlyoffice bash -c '
# Configurar o arquivo de configuração principal
cat > /etc/onlyoffice/documentserver/local.json << EOF
{
  "services": {
    "CoAuthoring": {
      "sql": {
        "type": "postgres",
        "dbHost": "db",
        "dbPort": "5432",
        "dbName": "legisinc",
        "dbUser": "postgres",
        "dbPass": "123456"
      },
      "token": {
        "enable": {
          "request": {
            "inbox": false,
            "outbox": false
          },
          "browser": false
        },
        "inbox": {
          "header": "Authorization",
          "inBody": true
        },
        "outbox": {
          "header": "Authorization",
          "inBody": true
        }
      },
      "secret": {
        "inbox": {
          "string": "MySecretKey123"
        },
        "outbox": {
          "string": "MySecretKey123"
        },
        "session": {
          "string": "MySecretKey123"
        }
      },
      "requestDefaults": {
        "rejectUnauthorized": false
      },
      "request-filtering-agent": {
        "allowMetaIPAddress": true,
        "allowPrivateIPAddress": true
      },
      "server": {
        "port": 8000
      }
    }
  },
  "rabbitmq": {
    "url": "amqp://guest:guest@localhost"
  },
  "storage": {
    "fs": {
      "secretString": "MySecretKey123"
    }
  }
}
EOF
echo "local.json configurado"
'

echo -e "\n${YELLOW}3. Criando arquivo de configuração de idioma customizado...${NC}"
docker exec legisinc-onlyoffice bash -c '
# Criar configuração customizada para forçar PT
cat > /var/www/onlyoffice/documentserver/web-apps/apps/common/locale.json << EOF
{
  "defaultLang": "pt",
  "langs": {
    "pt": "Português",
    "en": "English"
  }
}
EOF
echo "locale.json criado"

# Adicionar script de inicialização para forçar PT
cat > /var/www/onlyoffice/documentserver/web-apps/apps/api/documents/api-lang.js << EOF
// Force Portuguese language
if (window.DocsAPI) {
    window.DocsAPI.DocEditor.defaultConfig = window.DocsAPI.DocEditor.defaultConfig || {};
    window.DocsAPI.DocEditor.defaultConfig.lang = "pt";
    window.DocsAPI.DocEditor.defaultConfig.region = "pt-BR";
}
EOF
echo "api-lang.js criado"
'

echo -e "\n${YELLOW}4. Modificando configuração do nginx...${NC}"
docker exec legisinc-onlyoffice bash -c '
# Adicionar header para forçar idioma PT
if ! grep -q "add_header X-Default-Language" /etc/nginx/includes/onlyoffice-documentserver-*.conf 2>/dev/null; then
    for conf in /etc/nginx/includes/onlyoffice-documentserver-*.conf; do
        if [ -f "$conf" ]; then
            sed -i "/location.*{/a\        add_header X-Default-Language pt;" "$conf"
        fi
    done
    echo "Headers nginx configurados"
fi
'

echo -e "\n${YELLOW}5. Reiniciando serviços...${NC}"
docker exec legisinc-onlyoffice supervisorctl restart all
docker exec legisinc-onlyoffice nginx -s reload

sleep 5

echo -e "\n${YELLOW}6. Verificando configuração...${NC}"
echo -e "${BLUE}Serviços rodando:${NC}"
docker exec legisinc-onlyoffice supervisorctl status

echo -e "\n${BLUE}Variáveis de ambiente:${NC}"
docker exec legisinc-onlyoffice env | grep -E "LANG|LOCALE" | sort

echo -e "\n==================================================="
echo -e "${GREEN}✅ Configuração completa aplicada!${NC}"
echo -e "==================================================="
echo -e "${YELLOW}IMPORTANTE - Para ver as mudanças:${NC}"
echo ""
echo "1. ${BLUE}Limpe COMPLETAMENTE o cache do navegador:${NC}"
echo "   - Chrome/Edge: Ctrl+Shift+Delete"
echo "   - Selecione: 'Imagens e arquivos em cache'"
echo "   - Período: 'Todo o período'"
echo ""
echo "2. ${BLUE}Ou use uma aba anônima/privada${NC}"
echo ""
echo "3. ${BLUE}Adicione parâmetro na URL:${NC}"
echo "   http://localhost:8001/proposicoes/2/editar?lang=pt"
echo ""
echo "4. ${BLUE}Configure o navegador para PT-BR:${NC}"
echo "   - Chrome: Configurações → Idiomas → Português (Brasil)"
echo "   - Mover PT-BR para o topo da lista"
echo ""
echo -e "${GREEN}Se ainda mostrar em inglês, é cache do navegador!${NC}"
echo "==================================================="