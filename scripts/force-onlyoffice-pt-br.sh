#!/bin/bash

echo "==================================================="
echo "🌍 Forçando OnlyOffice para Português (Brasil)"
echo "==================================================="

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "\n${YELLOW}📝 Aplicando configurações de idioma PT-BR...${NC}"

# 1. Configurar local.json com todas as opções de PT-BR
docker exec legisinc-onlyoffice bash -c '
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
      "request": {
        "defaultLang": "pt"
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
'

echo -e "${GREEN}✅ local.json configurado${NC}"

# 2. Criar link simbólico para pt-BR baseado em pt
docker exec legisinc-onlyoffice bash -c '
for app in documenteditor spreadsheeteditor presentationeditor; do
  cd /var/www/onlyoffice/documentserver/web-apps/apps/$app/main/locale/
  if [ -f pt.json ] && [ ! -f pt-br.json ]; then
    cp pt.json pt-br.json
    echo "Criado pt-br.json para $app"
  fi
done
'

echo -e "${GREEN}✅ Arquivos de idioma PT-BR criados${NC}"

# 3. Configurar nginx para servir PT como padrão
docker exec legisinc-onlyoffice bash -c '
if ! grep -q "Accept-Language" /etc/nginx/nginx.conf; then
  sed -i "/http {/a \    map \$http_accept_language \$lang {\n        default pt;\n        ~en en;\n        ~pt pt;\n    }" /etc/nginx/nginx.conf
  echo "Nginx configurado para PT como padrão"
fi
'

# 4. Reiniciar serviços
echo -e "\n${YELLOW}🔄 Reiniciando serviços...${NC}"
docker exec legisinc-onlyoffice supervisorctl restart all
docker exec legisinc-onlyoffice nginx -s reload

sleep 5

# 5. Verificar status
echo -e "\n${YELLOW}📊 Verificando status...${NC}"
docker exec legisinc-onlyoffice supervisorctl status

echo -e "\n${GREEN}✅ Configuração aplicada!${NC}"
echo -e "\n${YELLOW}⚠️ IMPORTANTE:${NC}"
echo "1. Limpe o cache do navegador (Ctrl+Shift+Delete)"
echo "2. Use modo incógnito para testar"
echo "3. Ou adicione ?lang=pt na URL do editor"
echo ""
echo "Se ainda mostrar em inglês, o editor pode estar pegando"
echo "o idioma do navegador. Configure seu navegador para PT-BR."
echo "==================================================="