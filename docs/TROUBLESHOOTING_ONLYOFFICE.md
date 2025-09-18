# Guia de Solução de Problemas - OnlyOffice Integration 

Este documento descreve como diagnosticar e corrigir erros de comunicação com o OnlyOffice Document Server no sistema Legisinc.

## Arquitetura da Integração

### Componentes principais:
- **Laravel App Container** (`legisinc-app`) - IP: `172.24.0.2`
- **OnlyOffice Container** (`legisinc-onlyoffice`) - IP: `172.24.0.3`
- **Rede Docker customizada** (`legisinc-network`)

### Fluxo de comunicação:
1. **Browser** → Laravel (criar/abrir documento)
2. **Browser** → OnlyOffice Server (carregar editor via JavaScript)
3. **OnlyOffice Server** → Laravel (baixar arquivo do documento)
4. **OnlyOffice Server** → Laravel (callbacks de salvamento)

## Tipos de Erros Mais Comuns

### 1. Erro -4: "Erro ao baixar arquivo"

**Causa**: OnlyOffice não consegue acessar o arquivo no Laravel.

**Diagnóstico**:
```bash
# Testar conectividade de dentro do container OnlyOffice
docker exec legisinc-onlyoffice curl -I http://172.24.0.2:80/onlyoffice/file/proposicao/ID/arquivo.docx
```

**Soluções**:
- Verificar se ambos containers estão na mesma rede Docker
- Confirmar IPs corretos nos URLs de configuração
- Verificar se arquivo existe no storage do Laravel

### 2. Erro -20: "Token de segurança não formado corretamente"

**Causa**: OnlyOffice esperando JWT válido mas recebendo configuração inválida.

**Diagnóstico**:
```bash
# Verificar logs do container OnlyOffice
docker logs legisinc-onlyoffice | grep -i jwt
```

**Soluções**:
- Desabilitar JWT: `JWT_ENABLED=false` no container
- Ou implementar JWT válido no Laravel

### 3. Erro 419: "Page Expired" no callback

**Causa**: CSRF protection bloqueando callbacks POST do OnlyOffice.

**Diagnóstico**:
```bash
# Testar callback manualmente
docker exec legisinc-onlyoffice curl -X POST -H "Content-Type: application/json" \
  -d '{"status":0}' http://172.24.0.2:80/api/onlyoffice/callback/proposicao/ID
```

**Soluções**:
- Adicionar `'onlyoffice/*'` no `$except` do `VerifyCsrfToken.php`
- Usar rotas da API (`/api/`) que não têm CSRF por padrão

### 4. documentType inválido

**Causa**: Configuração incorreta do tipo de documento.

**Soluções**:
- Para arquivos DOCX: `"documentType": "word"`
- Para arquivos XLSX: `"documentType": "cell"`
- Para arquivos PPTX: `"documentType": "slide"`

## Scripts de Diagnóstico

### Verificar Status dos Containers
```bash
#!/bin/bash
echo "=== Status dos Containers ==="
docker ps | grep -E "(legisinc-app|legisinc-onlyoffice)"

echo -e "\n=== Rede Docker ==="
docker network inspect legisinc-network

echo -e "\n=== IPs dos Containers ==="
docker inspect legisinc-app | grep IPAddress
docker inspect legisinc-onlyoffice | grep IPAddress
```

### Testar Conectividade
```bash
#!/bin/bash
PROPOSICAO_ID="4169"
TEMPLATE_ID="11"
ARQUIVO="proposicao_${PROPOSICAO_ID}_template_${TEMPLATE_ID}.docx"

echo "=== Teste de Conectividade ==="
echo "1. Testando acesso ao arquivo..."
docker exec legisinc-onlyoffice curl -I "http://172.24.0.2:80/onlyoffice/file/proposicao/${PROPOSICAO_ID}/${ARQUIVO}"

echo -e "\n2. Testando callback..."
docker exec legisinc-onlyoffice curl -X POST -H "Content-Type: application/json" \
  -d '{"status":0}' "http://172.24.0.2:80/api/onlyoffice/callback/proposicao/${PROPOSICAO_ID}"

echo -e "\n3. Testando OnlyOffice web interface..."
curl -I http://localhost:8080
```

### Verificar Logs
```bash
#!/bin/bash
echo "=== Logs do Laravel ==="
docker exec legisinc-app tail -f storage/logs/laravel.log &

echo "=== Logs do OnlyOffice ==="
docker logs -f legisinc-onlyoffice &

# Parar com Ctrl+C
wait
```

## Checklist de Solução de Problemas

### Quando o editor não carrega:

- [ ] Containers estão rodando (`docker ps`)
- [ ] OnlyOffice acessível em `http://localhost:8080`
- [ ] JavaScript do OnlyOffice carregando sem erro no browser
- [ ] URLs corretos na configuração (IPs dos containers)
- [ ] Arquivo sendo criado corretamente no storage

### Quando o editor carrega mas não salva:

- [ ] Callback URL acessível do container OnlyOffice
- [ ] CSRF desabilitado para rotas OnlyOffice
- [ ] Status codes de callback sendo processados corretamente
- [ ] Logs do Laravel mostram callbacks sendo recebidos

### Quando há erros de formato:

- [ ] Arquivo DOCX válido sendo gerado
- [ ] MIME type correto (`application/vnd.openxmlformats-officedocument.wordprocessingml.document`)
- [ ] `documentType` correto na configuração JavaScript
- [ ] `fileType` correto na configuração JavaScript

## Configuração de Rede Docker

### Criar rede customizada:
```bash
docker network create legisinc-network
```

### Conectar containers:
```bash
docker network connect legisinc-network legisinc-app
docker network connect legisinc-network legisinc-onlyoffice
```

### Recriar OnlyOffice com configurações corretas:
```bash
docker stop legisinc-onlyoffice
docker rm legisinc-onlyoffice
docker run -d --name legisinc-onlyoffice --network legisinc-network \
  -p 8080:80 \
  -e JWT_ENABLED=false \
  -e JWT_SECRET="" \
  -e ALLOW_PRIVATE_IP_ADDRESS=true \
  onlyoffice/documentserver:8.0
```

## Status Codes do OnlyOffice Callback

| Status | Descrição | Ação Necessária |
|--------|-----------|-----------------|
| 0 | Editor inicializado | Nenhuma |
| 1 | Documento sendo editado | Nenhuma |
| 2 | Documento pronto para salvar | Salvar arquivo |
| 3 | Erro ao salvar | Investigar erro |
| 4 | Documento fechado sem mudanças | Nenhuma |
| 6 | Documento sendo editado por múltiplos usuários | Nenhuma |
| 7 | Erro forçado | Investigar erro |

## Logs Importantes

### Laravel - Sucesso:
```
[2025-07-24 14:50:24] local.INFO: OnlyOffice callback recebido {"proposicao_id":"4169","callback_data":{"status":1,"users":["7"]}}
[2025-07-24 14:50:24] local.INFO: Arquivo servido com sucesso {"proposicao_id":"4169","arquivo":"proposicao_4169_template_11.docx","tamanho":737}
```

### OnlyOffice - Erro de IP:
```
Error: DNS lookup 192.168.65.254 is not allowed. Because, It is private IP address
```

### Browser - Erro de CORS:
```
Access to fetch at 'http://172.24.0.2:80/...' from origin 'http://localhost:8080' has been blocked by CORS policy
```

## Arquivos de Configuração Importantes

### `/app/Http/Middleware/VerifyCsrfToken.php`
```php
protected $except = [
    'mock-api/*',
    'admin/parametros/ajax/*',
    'onlyoffice/*',  // Importante para callbacks
];
```

### `/routes/api.php`
```php
// Callback específico para proposições (sem CSRF)
Route::post('onlyoffice/callback/proposicao/{proposicaoId}', 
    [App\Http\Controllers\ProposicaoController::class, 'onlyOfficeCallback'])
    ->name('api.onlyoffice.callback.proposicao');
```

### Configuração JavaScript do OnlyOffice
```javascript
{
    "documentType": "word",  // word/cell/slide
    "document": {
        "fileType": "docx",
        "url": "http://172.24.0.2:80/onlyoffice/file/proposicao/{id}/{arquivo}",
    },
    "editorConfig": {
        "callbackUrl": "http://172.24.0.2:80/api/onlyoffice/callback/proposicao/{id}",
    }
}
```

## Comandos de Manutenção

### Limpar cache do Laravel:
```bash
docker exec legisinc-app php artisan config:clear
docker exec legisinc-app php artisan route:clear
docker exec legisinc-app php artisan cache:clear
```

### Reiniciar integração:
```bash
# Reiniciar containers
docker restart legisinc-app legisinc-onlyoffice

# Verificar logs
docker logs legisinc-onlyoffice | tail -20
docker exec legisinc-app tail storage/logs/laravel.log
```

## Monitoramento Contínuo

### Verificar saúde da integração:
```bash
#!/bin/bash
# Script para verificar se OnlyOffice está funcionando
set -e

echo "Verificando OnlyOffice Integration..."

# 1. Containers rodando
docker ps | grep -q legisinc-app || { echo "❌ Laravel app não está rodando"; exit 1; }
docker ps | grep -q legisinc-onlyoffice || { echo "❌ OnlyOffice não está rodando"; exit 1; }

# 2. OnlyOffice acessível
curl -f http://localhost:8080/welcome/ > /dev/null || { echo "❌ OnlyOffice não acessível"; exit 1; }

# 3. Conectividade entre containers
docker exec legisinc-onlyoffice curl -f http://172.24.0.2:80 > /dev/null || { echo "❌ Conectividade entre containers falhou"; exit 1; }

echo "✅ OnlyOffice Integration está funcionando!"
```

---

**Última atualização**: 24/07/2025
**Versão OnlyOffice**: 8.0
**Versão Laravel**: 10.x