# Solução: Erro de Gravação OnlyOffice - "O documento não pode ser gravado"

## 🚨 Problema

Quando acessando o editor OnlyOffice em `/proposicoes/{id}/onlyoffice/editor-parlamentar`, o seguinte erro aparecia:

```
Aviso
O documento não pode ser gravado. Verifique as configurações de conexão ou entre em contato com o administrador.
Quando você clicar no botão 'OK', você será solicitado a baixar o documento.
```

## 🔍 Diagnóstico Realizado

### 1. Verificação de Conectividade
- ✅ OnlyOffice container (`legisinc-onlyoffice`) funcionando
- ✅ Aplicação container (`legisinc-app`) funcionando
- ✅ Comunicação entre containers funcionando

### 2. Análise dos Logs
**Logs do OnlyOffice mostraram o problema real:**
```
[ERROR] postData error: url = http://legisinc-app/api/onlyoffice/callback/proposicao/1
Error: Error response: statusCode:302; headers:{"location":"http://legisinc-app/login"}
```

**O OnlyOffice estava sendo REDIRECIONADO para login** quando tentava enviar callbacks de salvamento.

### 3. Problemas Identificados

#### Problema Principal: Middleware de Autenticação
- O `ProposicaoController` tinha middleware `auth` aplicado a TODOS os métodos
- Incluindo os métodos de callback (`onlyOfficeCallback`, `onlyOfficeCallbackInstance`)
- OnlyOffice não conseguia acessar os callbacks sem autenticação

#### Problema Secundário: Configuração de URL Interna
- `ONLYOFFICE_INTERNAL_URL` estava configurado incorretamente no `.env.local`
- Estava usando `http://onlyoffice-documentserver:80` (nome incorreto)
- Deveria ser `http://legisinc-onlyoffice:80` (nome real do container)

## ✅ Soluções Aplicadas

### 1. Correção do Middleware de Autenticação

**Arquivo:** `/app/Http/Controllers/ProposicaoController.php`

**Antes:**
```php
public function __construct(
    private TemplateUniversalService $templateUniversalService,
    private OnlyOfficeConversionService $conversionService
) {
    $this->middleware('auth'); // ❌ APLICADO A TODOS OS MÉTODOS
    // ... outros middlewares
}
```

**Depois:**
```php
public function __construct(
    private TemplateUniversalService $templateUniversalService,
    private OnlyOfficeConversionService $conversionService
) {
    $this->middleware('auth')->except(['onlyOfficeCallback', 'onlyOfficeCallbackInstance']); // ✅ EXCLUINDO CALLBACKS
    // ... outros middlewares
}
```

### 2. Correção da Configuração de URL Interna

**Arquivo:** `/.env.local`

**Antes:**
```env
ONLYOFFICE_INTERNAL_URL=http://onlyoffice-documentserver:80
```

**Depois:**
```env
ONLYOFFICE_INTERNAL_URL=http://legisinc-onlyoffice:80
```

**Comando para aplicar:**
```bash
docker restart legisinc-app
```

## 🧪 Testes de Validação

### 1. Teste de Callback
```bash
# Antes da correção: HTTP 302 (Redirect to login)
curl -X POST -H "Content-Type: application/json" -d '{"status": 1}' \
  http://localhost:8001/api/onlyoffice/callback/proposicao/1

# Depois da correção: HTTP 200 {"error":0}
```

### 2. Teste de Conectividade entre Containers
```bash
# OnlyOffice consegue acessar o app
docker exec legisinc-onlyoffice curl http://legisinc-app/api/onlyoffice/callback/proposicao/1
```

### 3. Teste de Download de Documento
```bash
# OnlyOffice consegue baixar documentos
docker exec legisinc-onlyoffice curl http://legisinc-app/proposicoes/1/onlyoffice/download
```

## 📋 Checklist de Verificação

Para verificar se a solução está funcionando:

- [ ] OnlyOffice container está rodando (`docker ps | grep onlyoffice`)
- [ ] App container está rodando (`docker ps | grep legisinc-app`)
- [ ] Configuração `ONLYOFFICE_INTERNAL_URL` está correta no `.env.local`
- [ ] Middleware de autenticação exclui métodos de callback
- [ ] Callback retorna `{"error":0}` em testes diretos
- [ ] Editor OnlyOffice carrega sem erros
- [ ] Salvamento funciona sem mostrar erro de gravação

## 🔧 Comandos Úteis para Diagnóstico

### Verificar logs do OnlyOffice em tempo real:
```bash
docker logs legisinc-onlyoffice --tail 20 -f
```

### Verificar logs da aplicação:
```bash
docker logs legisinc-app --tail 20 -f
```

### Testar callback diretamente:
```bash
curl -X POST -H "Content-Type: application/json" -d '{"status": 1}' \
  http://localhost:8001/api/onlyoffice/callback/proposicao/1
```

### Verificar conectividade entre containers:
```bash
docker network inspect legisinc-v2_legisinc_network
```

## 📝 Rotas Envolvidas

### Rotas de Callback (devem estar SEM autenticação):
- `POST /api/onlyoffice/callback/proposicao/{proposicao}` → `ProposicaoController::onlyOfficeCallback`
- `POST /api/onlyoffice/callback/instance/{instance}` → `ProposicaoController::onlyOfficeCallbackInstance`
- `POST /api/onlyoffice/callback/legislativo/{proposicao}/{documentKey}` → `OnlyOfficeController::callback`

### Rotas de Download (devem estar SEM autenticação):
- `GET /proposicoes/{id}/onlyoffice/download` → `OnlyOfficeController::downloadById`

### Rotas de Editor (COM autenticação):
- `GET /proposicoes/{proposicao}/onlyoffice/editor-parlamentar` → `OnlyOfficeController::editorParlamentar`
- `GET /proposicoes/{proposicao}/onlyoffice/editor-legislativo` → `OnlyOfficeController::editorLegislativo`

## 🎯 Resultado

Após aplicar as correções:
- ✅ Editor OnlyOffice carrega corretamente
- ✅ Documentos podem ser editados
- ✅ Salvamento automático funciona (30s)
- ✅ Salvamento manual funciona (Ctrl+S)
- ✅ Não há mais erro "O documento não pode ser gravado"
- ✅ Callbacks são processados corretamente

## 📅 Data da Solução

**Resolvido em:** 22 de setembro de 2025
**Ambiente:** Docker (legisinc-v2)
**Versão OnlyOffice:** 8.0.1-31
**Framework:** Laravel com PostgreSQL

---

💡 **Dica:** Sempre verifique se rotas de callback de serviços externos (como OnlyOffice) estão excluídas da autenticação, pois esses serviços não possuem sessões de usuário.