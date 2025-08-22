# 🌍 Solução: OnlyOffice em Português (Brasil)

## 📋 Problema Identificado

O OnlyOffice estava configurado para usar **English (United States)** como idioma padrão, mesmo com algumas configurações de região em `pt-BR`.

## 🔧 Solução Implementada

### 1. **Configurações de Ambiente Atualizadas**

```yaml
# docker-compose.yml - Serviço OnlyOffice
environment:
  - DOCUMENT_SERVER_REGION=pt-BR
  - DOCUMENT_SERVER_LOCALE=pt_BR.UTF-8
  - LC_ALL=pt_BR.UTF-8
  - LANG=pt_BR.UTF-8
  - LANGUAGE=pt_BR:pt
  - ONLYOFFICE_DOCSERV_LANG=pt-BR
  - ONLYOFFICE_DOCSERV_LOCALE=pt_BR.UTF-8
```

### 2. **Arquivos de Configuração Personalizados**

#### `docker/onlyoffice/default.json`
```json
{
  "services": {
    "CoAuthoring": {
      "request": {
        "defaultLang": "pt-BR"
      }
    }
  },
  "common": {
    "lang": "pt-BR",
    "locale": "pt_BR.UTF-8"
  },
  "format": {
    "defaultLang": "pt-BR"
  }
}
```

#### `docker/onlyoffice/editor-config.json`
```json
{
  "editor": {
    "lang": "pt-BR",
    "locale": "pt_BR.UTF-8",
    "defaultLanguage": "pt-BR",
    "spellcheckerLanguage": "pt-BR"
  },
  "plugins": {
    "spellchecker": {
      "language": "pt-BR"
    }
  }
}
```

### 3. **Volumes Montados**

```yaml
volumes:
  - ./docker/onlyoffice/default.json:/etc/onlyoffice/documentserver/default.json
  - ./docker/onlyoffice/editor-config.json:/etc/onlyoffice/documentserver/editor-config.json
```

## 🚀 Como Aplicar a Solução

### **Opção 1: Script Automatizado (Recomendado)**

```bash
# Executar o script de reinicialização
./scripts/restart-onlyoffice-pt-br.sh
```

### **Opção 2: Comandos Manuais**

```bash
# 1. Parar o container
docker stop legisinc-onlyoffice

# 2. Remover o container
docker rm legisinc-onlyoffice

# 3. Limpar cache (opcional)
docker volume rm legisinc_onlyoffice_cache legisinc_onlyoffice_forgotten

# 4. Reconstruir e iniciar
docker-compose up -d onlyoffice-documentserver

# 5. Verificar status
docker-compose ps onlyoffice-documentserver
```

## ✅ Verificação da Solução

### **1. Verificar Configurações do Container**

```bash
# Verificar variáveis de ambiente
docker exec legisinc-onlyoffice env | grep -E "(LANG|LOCALE|LANGUAGE)"

# Verificar arquivos de configuração
docker exec legisinc-onlyoffice cat /etc/onlyoffice/documentserver/default.json
docker exec legisinc-onlyoffice cat /etc/onlyoffice/documentserver/editor-config.json
```

### **2. Testar no Navegador**

1. Acesse: `http://localhost:8080`
2. Abra um documento para edição
3. Verifique se o idioma padrão é **Português (Brasil)**
4. Teste o corretor ortográfico em português

### **3. Verificar Logs**

```bash
# Ver logs do OnlyOffice
docker logs legisinc-onlyoffice | grep -i "lang\|locale\|portuguese"

# Ver logs em tempo real
docker logs -f legisinc-onlyoffice
```

## 🔍 Troubleshooting

### **Problema: Idioma não mudou após reinicialização**

**Solução:**
```bash
# 1. Limpar completamente o cache
docker volume rm legisinc_onlyoffice_cache legisinc_onlyoffice_forgotten

# 2. Reconstruir imagem
docker-compose build --no-cache onlyoffice-documentserver

# 3. Reiniciar
docker-compose up -d onlyoffice-documentserver
```

### **Problema: Configurações não são aplicadas**

**Solução:**
```bash
# 1. Verificar se os arquivos estão montados corretamente
docker exec legisinc-onlyoffice ls -la /etc/onlyoffice/documentserver/

# 2. Verificar permissões dos arquivos
ls -la docker/onlyoffice/

# 3. Reaplicar configurações
docker-compose restart onlyoffice-documentserver
```

### **Problema: Cache do navegador**

**Solução:**
- Limpar cache do navegador (Ctrl+Shift+Delete)
- Usar modo incógnito para testar
- Forçar refresh (Ctrl+F5)

## 📚 Configurações Adicionais

### **Personalizar Mais Idiomas**

Para adicionar suporte a outros idiomas, edite `docker/onlyoffice/default.json`:

```json
{
  "services": {
    "CoAuthoring": {
      "request": {
        "defaultLang": "pt-BR",
        "supportedLanguages": ["pt-BR", "en-US", "es-ES"]
      }
    }
  }
}
```

### **Configurar Corretor Ortográfico**

```json
{
  "plugins": {
    "spellchecker": {
      "language": "pt-BR",
      "dictionaries": ["pt-BR", "pt-PT"]
    }
  }
}
```

## 🎯 Resultado Esperado

Após aplicar a solução:

- ✅ **Idioma padrão**: Português (Brasil)
- ✅ **Interface**: Em português
- ✅ **Corretor ortográfico**: Funcionando em português
- ✅ **Formatação**: Padrões brasileiros
- ✅ **Região**: Configurada para Brasil

## 📝 Notas Importantes

1. **Cache**: Sempre limpe o cache após mudanças de idioma
2. **Navegador**: Limpe o cache do navegador para ver as mudanças
3. **Reinicialização**: Necessária após alterações de configuração
4. **Volumes**: Os arquivos de configuração são montados como volumes
5. **Logs**: Monitore os logs para identificar problemas

## 🔄 Manutenção

### **Atualizações Regulares**

```bash
# Verificar atualizações da imagem
docker pull onlyoffice/documentserver:8.0

# Reconstruir com novas configurações
docker-compose up -d --build onlyoffice-documentserver
```

### **Backup de Configurações**

```bash
# Fazer backup das configurações
cp -r docker/onlyoffice/ backup/onlyoffice-$(date +%Y%m%d)/

# Restaurar configurações
cp -r backup/onlyoffice-YYYYMMDD/* docker/onlyoffice/
```

---

**Última Atualização**: $(date)
**Versão da Solução**: 1.0
**Status**: ✅ Implementada e Testada
