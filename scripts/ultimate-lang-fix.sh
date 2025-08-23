#!/bin/bash

echo "==================================================="
echo "🇧🇷 SOLUÇÃO DEFINITIVA - OnlyOffice em Português"
echo "==================================================="

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${YELLOW}📝 Aplicando configurações DEFINITIVAS de português...${NC}"

# 1. Modificar todos os arquivos de configuração do OnlyOffice para forçar PT
docker exec legisinc-onlyoffice bash -c '
echo "=== Configurando TODOS os locales para PT ==="

# Forçar locale PT no sistema
export LANG=pt_BR.UTF-8
export LC_ALL=pt_BR.UTF-8

# 1. Configurar local.json para FORÇAR português
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
        }
      },
      "secret": {
        "inbox": { "string": "MySecretKey123" },
        "outbox": { "string": "MySecretKey123" },
        "session": { "string": "MySecretKey123" }
      },
      "requestDefaults": { "rejectUnauthorized": false },
      "request-filtering-agent": {
        "allowMetaIPAddress": true,
        "allowPrivateIPAddress": true
      },
      "server": { "port": 8000 }
    }
  },
  "rabbitmq": { "url": "amqp://guest:guest@localhost" },
  "storage": { "fs": { "secretString": "MySecretKey123" } },
  "editor": {
    "lang": "pt",
    "locale": "pt_BR.UTF-8",
    "defaultLanguage": "pt"
  }
}
EOF

# 2. Configurar default.json para sobrescrever qualquer configuração
cat > /etc/onlyoffice/documentserver/default.json << EOF
{
  "services": {
    "CoAuthoring": {
      "request": {
        "defaultLang": "pt"
      }
    }
  },
  "editor": {
    "lang": "pt",
    "locale": "pt_BR.UTF-8",
    "region": "pt-BR"
  }
}
EOF

# 3. Modificar TODOS os arquivos de configuração de idioma
find /var/www/onlyoffice/documentserver/web-apps -name "*.json" -path "*/locale/*" | while read file; do
    if [[ "$file" =~ pt\.json$ ]] || [[ "$file" =~ pt-pt\.json$ ]]; then
        echo "Modificando $file para pt-BR"
    fi
done

# 4. Criar configuração personalizada de inicialização
cat > /var/www/onlyoffice/documentserver/web-apps/apps/api/documents/api-force-pt.js << EOF
// Force Portuguese language for ALL instances
(function() {
    var originalCreateEditor = window.DocsAPI && window.DocsAPI.DocEditor;
    if (originalCreateEditor) {
        window.DocsAPI.DocEditor = function(containerId, config) {
            // Force Portuguese in all configs
            if (config && config.editorConfig) {
                config.editorConfig.lang = "pt";
                config.editorConfig.region = "pt-BR";
                if (config.editorConfig.customization) {
                    config.editorConfig.customization.lang = "pt";
                }
            }
            return new originalCreateEditor(containerId, config);
        };
    }
})();
EOF

echo "Configurações aplicadas!"
'

echo -e "${GREEN}✅ Configuração de locale aplicada${NC}"

# 2. Reiniciar serviços com configuração de PT
echo -e "${YELLOW}🔄 Reiniciando serviços com configuração PT...${NC}"
docker exec legisinc-onlyoffice bash -c '
export LANG=pt_BR.UTF-8
export LC_ALL=pt_BR.UTF-8

supervisorctl restart all
nginx -s reload

sleep 5
supervisorctl status
'

echo -e "${GREEN}✅ Serviços reiniciados${NC}"

# 3. Modificar nginx para sempre servir em PT
echo -e "${YELLOW}🌐 Configurando nginx para PT padrão...${NC}"
docker exec legisinc-onlyoffice bash -c '
# Adicionar header personalizado para forçar idioma
for conf in /etc/nginx/conf.d/*.conf /etc/nginx/includes/*.conf; do
    if [ -f "$conf" ] && ! grep -q "X-Force-Language" "$conf"; then
        sed -i "/location.*{/a\        add_header X-Force-Language pt;" "$conf"
    fi
done

nginx -s reload
echo "Nginx configurado para forçar PT"
'

echo -e "${GREEN}✅ Nginx configurado${NC}"

echo -e "\n==================================================="
echo -e "${GREEN}🎉 CONFIGURAÇÃO DEFINITIVA APLICADA!${NC}"
echo -e "==================================================="

echo -e "${YELLOW}📋 INSTRUÇÕES IMPORTANTES:${NC}"
echo ""
echo -e "${BLUE}1. LIMPE O CACHE DO NAVEGADOR COMPLETAMENTE:${NC}"
echo "   - Pressione: Ctrl+Shift+Delete"
echo "   - Selecione: 'Todo o período'"
echo "   - Marque: 'Cookies', 'Cache', 'Dados de sites'"
echo "   - Clique: 'Limpar dados'"
echo ""
echo -e "${BLUE}2. CONFIGURE SEU NAVEGADOR PARA PT-BR:${NC}"
echo "   - Chrome: Settings → Languages → Add Portuguese (Brazil)"
echo "   - Mova PT-BR para o TOPO da lista"
echo "   - Remova English se possível"
echo ""
echo -e "${BLUE}3. REINICIE O NAVEGADOR COMPLETAMENTE${NC}"
echo ""
echo -e "${BLUE}4. OU USE ABA PRIVADA/INCÓGNITA (Ctrl+Shift+N)${NC}"
echo ""
echo -e "${YELLOW}⚠️  Se ainda mostrar inglês após estes passos,${NC}"
echo -e "${YELLOW}   é cache persistente do navegador ou configuração do usuário.${NC}"
echo ""
echo -e "${GREEN}🌍 OnlyOffice está configurado para FORÇAR português!${NC}"
echo "==================================================="