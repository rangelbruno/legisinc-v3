# OnlyOffice Integration - Sistema Legisinc

Este documento descreve a integração do OnlyOffice Document Server com o sistema Legisinc para edição colaborativa de proposições legislativas.

## Visão Geral

A integração permite que usuários editem proposições diretamente no browser usando o OnlyOffice Document Server, com salvamento automático e colaboração em tempo real.

### Arquitetura

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│     Browser     │    │   Laravel App    │    │   OnlyOffice    │
│   (Frontend)    │◄──►│  (legisinc-app)  │◄──►│ (legisinc-only) │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌──────────────┐
                       │  PostgreSQL  │
                       │   Database   │
                       └──────────────┘
```

## Configuração do Ambiente

### 1. Containers Docker

#### OnlyOffice Container
```bash
docker run -d --name legisinc-onlyoffice \
  --network legisinc-network \
  -p 8080:80 \
  -e JWT_ENABLED=false \
  -e ALLOW_PRIVATE_IP_ADDRESS=true \
  onlyoffice/documentserver:8.0
```

#### Rede Docker Customizada
```bash
docker network create legisinc-network
docker network connect legisinc-network legisinc-app
docker network connect legisinc-network legisinc-onlyoffice
```

### 2. Configuração Laravel

#### Middleware CSRF
```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'onlyoffice/*',
];
```

#### Rotas API
```php
// routes/api.php
Route::post('onlyoffice/callback/proposicao/{proposicaoId}', 
    [ProposicaoController::class, 'onlyOfficeCallback'])
    ->name('api.onlyoffice.callback.proposicao');
```

## Fluxo de Edição

### 1. Criação do Documento

1. **Usuário acessa** `/proposicoes/{id}/preparar-edicao/{template}`
2. **Sistema cria arquivo** DOCX baseado no template
3. **Usuário clica** "Abrir OnlyOffice" → nova aba
4. **JavaScript inicializa** OnlyOffice Document Server

### 2. Carregamento do Editor

```javascript
// Configuração OnlyOffice
const config = {
    "documentType": "word",
    "document": {
        "fileType": "docx",
        "url": "http://172.24.0.2:80/onlyoffice/file/proposicao/{id}/{arquivo}",
        "key": "proposicao_{id}_template_{template}_{timestamp}"
    },
    "editorConfig": {
        "callbackUrl": "http://172.24.0.2:80/api/onlyoffice/callback/proposicao/{id}"
    }
}
```

### 3. Salvamento Automático

- **OnlyOffice** detecta mudanças no documento
- **Callback POST** enviado para Laravel com status `2`
- **Laravel** baixa arquivo atualizado via URL do callback
- **Arquivo salvo** no storage substituindo versão anterior

## Estrutura de Arquivos

```
app/
├── Http/Controllers/
│   └── ProposicaoController.php         # Lógica principal
├── Http/Middleware/
│   └── VerifyCsrfToken.php             # Exceções CSRF
└── Models/
    └── TipoProposicaoTemplate.php      # Templates

resources/views/proposicoes/
├── preparar-edicao.blade.php           # Tela intermediária
└── editar-onlyoffice.blade.php         # Editor OnlyOffice

storage/app/public/proposicoes/         # Arquivos das proposições

docs/
├── ONLYOFFICE_INTEGRATION.md          # Este documento
└── TROUBLESHOOTING_ONLYOFFICE.md      # Guia de problemas

scripts/
├── diagnose-onlyoffice.sh             # Diagnóstico automatizado
└── monitor-onlyoffice.sh               # Monitoramento contínuo
```

## Endpoints da API

### Servir Arquivo
```
GET /onlyoffice/file/proposicao/{id}/{arquivo}
```
- **Descrição**: Serve arquivo DOCX para o OnlyOffice
- **Headers**: CORS habilitado, MIME type correto
- **Autenticação**: Session-based

### Callback de Salvamento
```
POST /api/onlyoffice/callback/proposicao/{id}
```
- **Descrição**: Recebe callbacks do OnlyOffice sobre mudanças
- **Body**: JSON com status e URL do arquivo atualizado
- **CSRF**: Desabilitado

## Status Codes do Callback

| Status | Descrição | Ação do Sistema |
|--------|-----------|-----------------|
| 0 | Editor inicializado | Log apenas |
| 1 | Documento sendo editado | Log apenas |
| 2 | Documento pronto para salvar | Download e salvamento |
| 3 | Erro ao salvar documento | Log de erro |
| 4 | Documento fechado sem mudanças | Log apenas |
| 6 | Edição colaborativa ativa | Log apenas |
| 7 | Erro forçado | Log de erro |

## Formato de Arquivos

### Geração DOCX
```php
private function criarArquivoDocx($texto)
{
    // RTF compatível com OnlyOffice
    $rtf = '{\\rtf1\\ansi\\deff0' . "\n";
    $rtf .= '{\\fonttbl {\\f0 Times New Roman;}}' . "\n";
    $rtf .= '\\f0\\fs24' . "\n";
    $rtf .= $textoRTF . "\n";
    $rtf .= '}';
    
    return $rtf;
}
```

### MIME Types
- **DOCX**: `application/vnd.openxmlformats-officedocument.wordprocessingml.document`
- **Headers**: Cache-Control, CORS, Content-Disposition

## Monitoramento

### Scripts Disponíveis

#### Diagnóstico Completo
```bash
./scripts/diagnose-onlyoffice.sh [proposicao_id] [template_id]
```

#### Monitoramento Contínuo
```bash
# Uma vez
./scripts/monitor-onlyoffice.sh

# Contínuo (atualiza a cada 30s)
watch -n 30 ./scripts/monitor-onlyoffice.sh
```

### Logs Importantes

#### Laravel
```bash
docker exec legisinc-app tail -f storage/logs/laravel.log | grep -i onlyoffice
```

#### OnlyOffice
```bash
docker logs -f legisinc-onlyoffice
```

### Health Checks

```bash
# OnlyOffice web interface
curl http://localhost:8080/welcome/

# Conectividade entre containers
docker exec legisinc-onlyoffice curl http://172.24.0.2:80

# Callback endpoint
curl -X POST -H "Content-Type: application/json" \
  -d '{"status":0}' \
  http://localhost:8001/api/onlyoffice/callback/proposicao/test
```

## Solução de Problemas

### Problemas Comuns

1. **Editor não carrega**
   - Verificar se containers estão rodando
   - Confirmar OnlyOffice acessível em `localhost:8080`
   - Checar JavaScript console no browser

2. **"Erro ao baixar arquivo"**
   - Verificar conectividade entre containers
   - Confirmar arquivo existe no storage
   - Validar URLs na configuração

3. **"Documento não pode ser gravado"**
   - Testar callback endpoint manualmente
   - Verificar CSRF está desabilitado
   - Confirmar rotas API estão corretas

### Comandos de Reset

```bash
# Reiniciar integração completa
docker restart legisinc-onlyoffice legisinc-app

# Limpar cache Laravel
docker exec legisinc-app php artisan config:clear
docker exec legisinc-app php artisan route:clear

# Recriar rede Docker
docker network disconnect legisinc-network legisinc-app
docker network disconnect legisinc-network legisinc-onlyoffice
docker network rm legisinc-network
docker network create legisinc-network
docker network connect legisinc-network legisinc-app
docker network connect legisinc-network legisinc-onlyoffice
```

## Segurança

### Considerações

1. **JWT Desabilitado**: Para desenvolvimento apenas
2. **CORS Liberado**: Apenas para endpoints OnlyOffice
3. **CSRF Bypass**: Limitado a rotas específicas
4. **IPs Privados**: OnlyOffice configurado para aceitar

### Produção

Para ambiente de produção, considere:
- Habilitar JWT com secret seguro
- Restringir CORS a domínios específicos
- Usar HTTPS em todas as comunicações
- Implementar rate limiting nos callbacks

## Performance

### Otimizações

1. **Cache de arquivos**: Implementar cache para arquivos frequentemente acessados
2. **CDN**: Servir assets OnlyOffice via CDN
3. **Compressão**: Habilitar gzip para transfers
4. **Connection pooling**: Para alta concorrência

### Métricas

- Tempo de carregamento do editor: < 3s
- Tempo de salvamento: < 1s
- Concurrent users suportados: 50+

---

**Versão**: 1.0
**Última atualização**: 24/07/2025
**Contato**: Equipe de desenvolvimento Legisinc