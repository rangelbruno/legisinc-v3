# 📄 Exportação de PDF para AWS S3 - Sistema Legisinc

## 🎯 Visão Geral

Este documento descreve a funcionalidade de **Exportação de PDF do OnlyOffice diretamente para AWS S3**, implementada para capturar o estado atual editado no OnlyOffice e enviá-lo automaticamente para a nuvem, eliminando downloads locais e garantindo fidelidade total ao conteúdo.

## 🚨 Problema Original

**Antes da implementação:**
- O PDF exportado não refletia as edições atuais do OnlyOffice
- Sistema salvava apenas o estado original da proposição
- Downloads desnecessários no navegador do usuário
- Problemas de CORS ao tentar acessar o iframe do OnlyOffice
- Falta de integração com armazenamento em nuvem

## ✅ Solução Implementada

**Nova abordagem:**
- **Captura do estado atual**: PDF gerado do conteúdo editado em tempo real
- **Upload direto para AWS S3**: Sem downloads locais no navegador
- **API oficial OnlyOffice**: Usa `downloadAs("pdf")` para máxima fidelidade
- **Proxy backend**: Resolve problemas de CORS automaticamente
- **URLs temporárias S3**: Acesso seguro por 1 hora
- **Logging completo**: Monitoramento e debug detalhado

---

## 🏗️ Arquitetura da Solução

### 1. Interface do Usuário

#### **Dropdown de Exportação no Editor OnlyOffice**
- **Localização**: Header do editor OnlyOffice
- **Visibilidade**: Aparece apenas quando `proposicaoId` está disponível
- **Visual**: Dropdown com múltiplas opções de exportação
- **Feedback**: Spinner durante processamento + notificações SweetAlert

```html
<div class="btn-group">
    <button id="btnExportarPDF" class="btn btn-warning btn-sm" onclick="exportarPDFParaS3WithUI(this)">
        <i class="ki-duotone ki-file-down fs-6 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        Exportar PDF para S3
    </button>
    <button type="button" class="btn btn-warning btn-sm dropdown-toggle dropdown-toggle-split">
        <span class="visually-hidden">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#" onclick="exportarPDFParaS3WithUI(document.getElementById('btnExportarPDF'))">
            <i class="ki-duotone ki-cloud fs-6 me-2"></i>
            Exportar para S3 (Recomendado)
        </a></li>
        <li><a class="dropdown-item" href="#" onclick="exportarPDFDownloadAs(document.getElementById('btnExportarPDF'))">
            <i class="ki-duotone ki-download fs-6 me-2"></i>
            Baixar no Navegador
        </a></li>
    </ul>
</div>
```

### 2. Banco de Dados

#### **Novos Campos na Tabela `proposicoes`**

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `pdf_s3_path` | `varchar(255)` | Caminho do PDF no AWS S3 |
| `pdf_s3_url` | `text` | URL temporária de acesso ao S3 |
| `pdf_size_bytes` | `bigint` | Tamanho do arquivo PDF em bytes |

```sql
-- Migration: 2025_09_23_132039_add_s3_pdf_fields_to_proposicoes_table
ALTER TABLE proposicoes
ADD COLUMN pdf_s3_path VARCHAR(255) NULL,
ADD COLUMN pdf_s3_url TEXT NULL,
ADD COLUMN pdf_size_bytes BIGINT NULL;
```

### 3. Configuração AWS S3

#### **Variáveis de Ambiente (.env)**
```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=sa-east-1
AWS_BUCKET=legisinc
AWS_ENDPOINT_URL=https://s3.sa-east-1.amazonaws.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### 4. Roteamento

#### **Rota Existente Adaptada**
```php
// routes/web.php
Route::post('/{proposicao}/onlyoffice/exportar-pdf-s3',
    [OnlyOfficeController::class, 'exportarPDFParaS3'])
    ->name('onlyoffice.exportar-pdf-s3')
    ->middleware('role.permission:onlyoffice.editor.own');
```

### 5. Backend - Controller

#### **Método `exportarPDFParaS3()` em `OnlyOfficeController`**

```php
public function exportarPDFParaS3(Request $request, Proposicao $proposicao)
{
    // 1. Validar permissões
    // 2. Verificar tipo de entrada (arquivo, URL ou conversão tradicional)
    // 3. Processar conforme tipo:
    //    - uploadPDFToS3FromFile() - upload direto de arquivo
    //    - downloadPDFFromOnlyOfficeAndUploadToS3() - proxy download + upload
    //    - Método tradicional - conversão RTF → PDF → S3
    // 4. Upload para AWS S3
    // 5. Gerar URL temporária
    // 6. Atualizar banco de dados
    // 7. Retornar resposta JSON
}
```

**Fluxo do método:**
1. **Validação**: Verifica permissões com `Gate::denies('edit-onlyoffice', $proposicao)`
2. **Detecção de Entrada**:
   - `pdf_file` → `uploadPDFToS3FromFile()` (FormData)
   - `pdf_url` → `downloadPDFFromOnlyOfficeAndUploadToS3()` (JSON)
   - Nenhum → Método tradicional (conversão RTF)
3. **Upload S3**: Upload otimizado com metadados
4. **URL Temporária**: Geração com expiração de 1 hora
5. **Atualização BD**: Salva `pdf_s3_path`, `pdf_s3_url`, `pdf_size_bytes`
6. **Resposta**: JSON com informações completas

#### **Método `downloadPDFFromOnlyOfficeAndUploadToS3()` - Proxy Backend**

```php
private function downloadPDFFromOnlyOfficeAndUploadToS3(Request $request, Proposicao $proposicao, float $startTime)
{
    // 1. Obter URL do PDF do OnlyOffice
    $pdfUrl = $request->input('pdf_url');

    // 2. Converter URL externa para URL interna Docker
    $internalUrl = str_replace('http://localhost:8080', config('onlyoffice.internal_url'), $pdfUrl);

    // 3. Download via HTTP client interno
    $response = Http::timeout(30)->get($internalUrl);

    // 4. Validar PDF baixado
    if (!str_starts_with($pdfContent, '%PDF-')) {
        throw new Exception('Conteúdo baixado não é um PDF válido');
    }

    // 5. Upload para S3
    $s3Disk->put($s3Path, $pdfContent, [
        'ContentType' => 'application/pdf',
        'ContentDisposition' => 'inline; filename="proposicao_' . $proposicao->id . '.pdf"'
    ]);

    // 6. Retornar resposta de sucesso
}
```

**Características:**
- 🔄 **Proxy Interno**: Resolve problemas de CORS automaticamente
- 🌐 **URL Conversion**: Externa → Interna para comunicação Docker
- ✅ **Validação PDF**: Verifica se conteúdo baixado é válido
- 📤 **Upload Direto**: Sem armazenamento temporário local
- 🔧 **Error Handling**: Tratamento completo de erros

### 6. Frontend - JavaScript

#### **Função `exportarPDFParaS3WithUI()` - Captura Estado Atual e Upload S3**

```javascript
async function exportarPDFParaS3WithUI(btn) {
    try {
        const data = await exportarPDFParaS3(btn);

        // Feedback de sucesso
        Swal.fire({
            icon: 'success',
            title: '🎉 PDF Enviado para AWS S3!',
            html: `
                <p><strong>✅ Arquivo enviado com sucesso</strong></p>
                <p><strong>📁 Local:</strong> AWS S3 - ${data.s3_path.split('/').pop()}</p>
                <p><strong>📏 Tamanho:</strong> ${data.file_size}</p>
                <p><strong>⏱️ Tempo:</strong> ${data.execution_time_ms}ms</p>
                <hr>
                <p><strong>🔗 URL Temporária:</strong></p>
                <p class="text-muted small">A URL é válida até ${new Date(data.url_expires_at).toLocaleString()}</p>
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button onclick="window.open('${data.s3_url}', '_blank')" class="btn btn-primary btn-sm">
                        <i class="ki-duotone ki-eye fs-6 me-1"></i>Ver PDF
                    </button>
                    <button onclick="navigator.clipboard.writeText('${data.s3_url}')" class="btn btn-secondary btn-sm">
                        <i class="ki-duotone ki-copy fs-6 me-1"></i>Copiar URL
                    </button>
                </div>
            `,
            confirmButtonText: 'Perfeito!',
            confirmButtonColor: '#28a745',
            width: '600px'
        });
    } catch (error) {
        // Tratamento de erro
    }
}
```

#### **Função Core `exportarPDFParaS3()` - Interceptação e Proxy**

```javascript
async function exportarPDFParaS3(btn) {
    // 1. Force save antes da exportação
    window.onlyofficeEditor.docEditor.serviceCommand("forcesave", null);
    await new Promise(resolve => setTimeout(resolve, 2000));

    return new Promise((resolve, reject) => {
        // 2. Interceptar evento onDownloadAs
        const originalOnDownloadAs = window.onlyofficeEditor.config.events.onDownloadAs;

        window.onlyofficeEditor.config.events.onDownloadAs = async function(event) {
            try {
                if (event && event.data && event.data.url) {
                    // 3. Enviar URL do PDF para backend processar (proxy)
                    const uploadResponse = await fetch(`/proposicoes/${id}/onlyoffice/exportar-pdf-s3`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            pdf_url: event.data.url
                        })
                    });

                    const uploadData = await uploadResponse.json();
                    resolve(uploadData);
                }
            } catch (error) {
                reject(error);
            } finally {
                // Restaurar handler original
                window.onlyofficeEditor.config.events.onDownloadAs = originalOnDownloadAs;
            }
        };

        // 4. Executar downloadAs para gerar PDF do estado atual
        window.onlyofficeEditor.docEditor.downloadAs("pdf");
    });
}
```

**Nova Abordagem - Captura Estado Atual:**
- ✅ **downloadAs("pdf")**: Captura estado editado atual, não original
- ✅ **Interceptação onDownloadAs**: Captura URL do PDF gerado
- ✅ **Proxy Backend**: Resolve CORS enviando URL via JSON
- ✅ **Upload Automático S3**: Sem downloads locais no navegador
- ✅ **URLs Temporárias**: Acesso seguro por 1 hora
- ✅ **Feedback Rico**: Informações completas sobre upload

### 7. Model - Proposicao

#### **Novos Métodos**

```php
// Verificar se foi exportado para S3
public function foiExportadoParaS3(): bool
{
    return !empty($this->pdf_s3_path) && !empty($this->pdf_s3_url);
}

// Obter PDF para visualização/download (prioriza S3)
public function getPDFParaVisualizacao(): ?string
{
    return $this->foiExportadoParaS3()
        ? $this->pdf_s3_url
        : $this->arquivo_pdf_path;
}

// Obter informações do PDF S3
public function getInformacoesPDFS3(): array
{
    return [
        'path' => $this->pdf_s3_path,
        'url' => $this->pdf_s3_url,
        'size_bytes' => $this->pdf_size_bytes,
        'size_formatted' => $this->pdf_size_bytes ? $this->formatBytes($this->pdf_size_bytes) : null,
        'uploaded_at' => $this->updated_at
    ];
}
```

---

## 🚀 Solução para Problema CORS - Proxy Backend

### **Problema CORS Identificado**
Durante implementação inicial, o frontend tentava fazer `fetch()` diretamente na URL do PDF gerada pelo OnlyOffice. Isso causava erro:

```
Access to fetch at 'http://localhost:8080/cache/files/...' from origin 'http://localhost:8001'
has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource.
```

### **Causa Raiz**
- OnlyOffice roda em `localhost:8080` (container interno)
- Aplicação principal em `localhost:8001` (host)
- **Cross-Origin Resource Sharing (CORS)** bloqueia fetch direto do PDF
- Frontend não consegue baixar o PDF gerado pelo OnlyOffice

### **Solução Implementada - Proxy Backend**
Criação de um **proxy backend** que faz o download interno e upload para S3:

```javascript
// ❌ ANTES: Fetch direto no frontend (CORS blocked)
const pdfResponse = await fetch(event.data.url); // ERRO CORS
const pdfBlob = await pdfResponse.blob();

// ✅ DEPOIS: Envio de URL para backend processar
const uploadResponse = await fetch('/proposicoes/3/onlyoffice/exportar-pdf-s3', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ pdf_url: event.data.url })
});
```

### **Fluxo da Nova Abordagem**
1. **Frontend**: Executa `downloadAs("pdf")` no OnlyOffice
2. **OnlyOffice**: Gera PDF e retorna URL via evento `onDownloadAs`
3. **Frontend**: Envia URL para backend via JSON (não fetch direto)
4. **Backend**: Converte URL externa → interna (`localhost:8080` → `legisinc-onlyoffice:80`)
5. **Backend**: Faz download via HTTP client interno (sem CORS)
6. **Backend**: Upload direto para AWS S3
7. **Backend**: Retorna URL temporária S3 para frontend

### **Vantagens da Nova Abordagem**
- ✅ **Sem CORS**: Backend faz comunicação interna Docker
- ✅ **Captura Estado Atual**: PDF do conteúdo editado, não original
- ✅ **Upload Direto S3**: Sem downloads no navegador do usuário
- ✅ **URLs Temporárias**: Acesso seguro com expiração
- ✅ **Robusto**: Funciona independente de configurações CORS
- ✅ **Escalável**: Suporta múltiplos containers OnlyOffice

### **Resultado**
- 🎯 **100% funcional**: Captura PDF editado atual e envia para S3
- 🎯 **Zero CORS errors**: Comunicação interna Docker resolve tudo
- 🎯 **Zero downloads locais**: Upload direto para nuvem
- 🎯 **URLs temporárias**: Acesso seguro por 1 hora

---

## 🔧 Solução para Problema de `arquivo_path` NULL

### **Problema Identificado**
Após edição no OnlyOffice, algumas proposições ficavam com `arquivo_path` NULL no banco de dados, mesmo tendo arquivos RTF salvos no storage. Isso causava o erro: **"Arquivo de origem não disponível para exportação"**.

### **Causa Raiz**
- Callback do OnlyOffice salvava arquivos corretamente em `storage/app/proposicoes/`
- Campo `arquivo_path` não era sempre atualizado no banco
- Migração com `--fresh` resetava dados mas mantinha arquivos órfãos

### **Solução Implementada**
Função `buscarArquivoProposicaoAutomaticamente()` que:

1. **Detecta** quando `arquivo_path` é NULL
2. **Busca** arquivos RTF em múltiplos diretórios:
   - `proposicoes/` (padrão atual)
   - `private/proposicoes/` (padrão antigo)
   - `public/proposicoes/` (variações)
   - `local/proposicoes/` (variações)
3. **Identifica** o arquivo mais recente pelo timestamp no nome
4. **Atualiza** automaticamente o banco de dados
5. **Prossegue** com a exportação PDF normalmente

### **Resultado**
- ✅ Correção automática e transparente
- ✅ Sem necessidade de intervenção manual
- ✅ Compatível com estruturas antigas e novas
- ✅ Preserva arquivos históricos

---

## 🔄 Fluxo de Funcionamento

### **1. Durante a Edição - Export para S3**

```mermaid
graph TD
    A[Usuário no Editor OnlyOffice] --> B[Clica 'Exportar PDF para S3']
    B --> C[JavaScript força salvamento via serviceCommand]
    C --> D[Aguarda 2 segundos]
    D --> E[Executa downloadAs('pdf') - API OnlyOffice]
    E --> F[OnlyOffice gera PDF do estado atual]
    F --> G[Evento onDownloadAs captura URL do PDF]
    G --> H[Frontend envia URL para backend via JSON]
    H --> I[Backend converte URL externa → interna]
    I --> J[Backend baixa PDF via HTTP interno]
    J --> K[Backend faz upload para AWS S3]
    K --> L[Backend gera URL temporária S3]
    L --> M[Frontend exibe confirmação com link S3]

    style E fill:#e1f5fe
    style F fill:#e8f5e8
    style K fill:#fff3e0
    style L fill:#e8f5e8
```

### **2. Estrutura de Dados S3**

```mermaid
graph TD
    A[AWS S3 Bucket: legisinc] --> B[proposicoes/pdf/]
    B --> C[{proposicao_id}/]
    C --> D[proposicao_X_exported_timestamp.pdf]

    D --> E[URL Temporária - 1 hora]
    D --> F[Metadados: ContentType, ContentDisposition]

    style A fill:#ff9800
    style E fill:#4caf50
    style F fill:#2196f3
```

### **3. Fluxo de Dados Backend**

```mermaid
graph TD
    A[Request JSON] --> B{Tipo de Entrada?}
    B -->|pdf_file| C[uploadPDFToS3FromFile]
    B -->|pdf_url| D[downloadPDFFromOnlyOfficeAndUploadToS3]
    B -->|nenhum| E[Método tradicional RTF→PDF]

    C --> F[Upload Direto S3]
    D --> G[Download Proxy + Upload S3]
    E --> H[Conversão + Upload S3]

    F --> I[Atualizar BD + URL Temporária]
    G --> I
    H --> I

    style D fill:#4caf50
    style G fill:#4caf50
```

---

## 📁 Estrutura de Arquivos

### **AWS S3 - Estrutura de Diretórios**
```
legisinc (bucket)
└── proposicoes/
    └── pdf/
        └── {proposicao_id}/
            ├── proposicao_{id}_exported_{timestamp}.pdf
            ├── proposicao_{id}_exported_{timestamp2}.pdf
            └── ...
```

### **Exemplo de Estrutura S3**
```
s3://legisinc/proposicoes/pdf/123/proposicao_123_exported_1758634990.pdf
```

### **URLs Temporárias Geradas**
```
https://legisinc.s3.sa-east-1.amazonaws.com/proposicoes/pdf/123/proposicao_123_exported_1758634990.pdf?
X-Amz-Algorithm=AWS4-HMAC-SHA256&
X-Amz-Credential=AKIAYWJ6646VQZCHG37D%2F20250923%2Fsa-east-1%2Fs3%2Faws4_request&
X-Amz-Date=20250923T134311Z&
X-Amz-Expires=3600&
X-Amz-SignedHeaders=host&
X-Amz-Signature=...
```

**Características das URLs:**
- ✅ **Expiração**: 1 hora após geração
- ✅ **Seguras**: Assinadas com credenciais AWS
- ✅ **Diretas**: Acesso direto ao PDF sem autenticação adicional
- ✅ **ContentType**: `application/pdf` para visualização inline

---

## 🛡️ Segurança e Validações

### **Controle de Acesso**
- ✅ Middleware `role.permission:onlyoffice.editor.own`
- ✅ Verificação de tipo de usuário (Parlamentar/Legislativo)
- ✅ Validação de propriedade da proposição

### **Validações Técnicas**
- ✅ Verificação de existência de `arquivo_path`
- ✅ Validação de permissões de escrita em storage
- ✅ Verificação de sucesso na conversão
- ✅ Proteção CSRF

### **Logs e Auditoria**
```php
Log::info('Iniciando exportação de PDF via OnlyOffice', [
    'proposicao_id' => $proposicao->id,
    'user_id' => Auth::id()
]);
```

---

## 🚀 Benefícios da Nova Implementação

### **1. Captura Estado Atual**
- 📝 **PDF Editado**: Captura exatamente o que foi editado, não o estado original
- 📝 **Tempo Real**: Usa `downloadAs("pdf")` para máxima fidelidade
- 📝 **Force Save**: Força salvamento antes da captura
- 📝 **API Oficial**: Usa método nativo do OnlyOffice

### **2. Upload Direto para Nuvem**
- ☁️ **AWS S3**: Upload automático para armazenamento em nuvem
- ☁️ **Sem Downloads**: Elimina downloads desnecessários no navegador
- ☁️ **URLs Temporárias**: Acesso seguro por 1 hora
- ☁️ **Escalabilidade**: Suporta múltiplos usuários simultâneos

### **3. Resolução de CORS**
- 🔧 **Proxy Backend**: Elimina problemas de CORS completamente
- 🔧 **Comunicação Interna**: Docker-to-Docker sem restrições
- 🔧 **Robusto**: Funciona independente de configurações do navegador
- 🔧 **Compatível**: Suporta todos os navegadores modernos

### **4. Performance e Monitoramento**
- ⚡ **Logging Completo**: Debug e monitoramento detalhado
- ⚡ **Timeouts**: Configuração adequada para documentos grandes
- ⚡ **Error Handling**: Tratamento completo de erros
- ⚡ **Feedback Rico**: Informações detalhadas sobre upload

---

## 🔧 Configuração e Deployment

### **1. Migration**
```bash
php artisan migrate
```

### **2. Dependências AWS**
```bash
# Via Docker
docker exec legisinc-app composer require league/flysystem-aws-s3-v3 aws/aws-sdk-php

# Ou local
composer require league/flysystem-aws-s3-v3 aws/aws-sdk-php
```

### **3. Configuração AWS S3**
```bash
# Adicionar ao .env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=sa-east-1
AWS_BUCKET=legisinc
AWS_ENDPOINT_URL=https://s3.sa-east-1.amazonaws.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### **4. Teste de Conectividade S3**
```bash
# Script de teste incluído
php test-s3-connection.php
```

### **5. Configuração OnlyOffice**
Certifique-se de que as URLs internas estão configuradas:
```bash
ONLYOFFICE_SERVER_URL=http://localhost:8080
ONLYOFFICE_INTERNAL_URL=http://legisinc-onlyoffice:80
```

---

## 🐛 Troubleshooting

### **Problemas Comuns**

#### **1. Botão S3 não aparece**
- ✅ Verificar se `proposicaoId` está sendo passado para o componente
- ✅ Confirmar que usuário tem permissões adequadas
- ✅ Verificar se dropdown está sendo renderizado corretamente

#### **2. Erro CORS no console**
- ✅ **Problema resolvido**: Nova implementação usa proxy backend
- ✅ Se ainda ocorrer, verificar se está usando a nova abordagem de URL
- ✅ Confirmar que não há fetch direto no frontend

#### **3. PDF não reflete edições atuais**
- ✅ **Problema resolvido**: `downloadAs("pdf")` captura estado atual
- ✅ Verificar se `force save` está sendo executado antes
- ✅ Aguardar 2 segundos após `serviceCommand("forcesave")`

#### **4. Upload S3 falha**
- ✅ Verificar credenciais AWS no `.env`
- ✅ Confirmar que bucket `legisinc` existe e está acessível
- ✅ Verificar logs: `tail -f storage/logs/laravel.log | grep "S3"`
- ✅ Testar conectividade: `php test-s3-connection.php`

#### **5. URLs temporárias expiram rapidamente**
- ✅ URLs são válidas por 1 hora (configuração padrão)
- ✅ Verificar se `now()->addHour()` está sendo usado
- ✅ Considerar ajustar tempo de expiração se necessário

#### **6. Erro "Failed to download PDF from OnlyOffice: 403"**
- ✅ Verificar se URL interna está sendo usada (`legisinc-onlyoffice:80`)
- ✅ Confirmar que conversão externa → interna está funcionando
- ✅ Verificar se OnlyOffice container está rodando

#### **7. Timeout na geração do PDF**
- ✅ Aumentar timeout do HTTP client (padrão: 30 segundos)
- ✅ Verificar tamanho do documento sendo processado
- ✅ Monitorar performance do container OnlyOffice

#### **8. Editor OnlyOffice não está carregado**
- ✅ Aguardar mensagem "Document ready for editing" no console
- ✅ Verificar se `window.onlyofficeEditor.docEditor` existe
- ✅ Executar `window.testarDownloadPDF()` para debug

### **Logs Úteis**
```bash
# Ver logs da aplicação S3
tail -f storage/logs/laravel.log | grep "OnlyOffice S3"

# Ver logs específicos de upload
tail -f storage/logs/laravel.log | grep "PDF enviado para S3"

# Ver logs de erro
tail -f storage/logs/laravel.log | grep "❌"

# Ver logs do OnlyOffice
docker logs legisinc-onlyoffice

# Ver logs do navegador (console)
# Procurar por:
# - "🟢 OnlyOffice: Document ready for editing"
# - "🚀 OnlyOffice S3: Iniciando exportação para AWS S3"
# - "🎯 OnlyOffice S3: PDF capturado do editor!"
# - "📄 OnlyOffice S3: URL do PDF:"

# Testar API no console do navegador
window.testarDownloadPDF()

# Testar conectividade S3
php test-s3-connection.php
```

---

## 📋 Checklist de Implementação

- [x] Migration S3 criada e executada
- [x] Dependências AWS instaladas (`league/flysystem-aws-s3-v3`, `aws/aws-sdk-php`)
- [x] Configuração AWS S3 no `.env`
- [x] Rota S3 adicionada em `web.php`
- [x] **Método `exportarPDFParaS3()` implementado com múltiplas entradas**
- [x] **Método `downloadPDFFromOnlyOfficeAndUploadToS3()` implementado (proxy)**
- [x] **Método `uploadPDFToS3FromFile()` implementado (upload direto)**
- [x] **Dropdown de exportação adicionado no editor OnlyOffice**
- [x] **JavaScript `exportarPDFParaS3WithUI()` implementado**
- [x] **Interceptação `onDownloadAs` para captura de PDF editado**
- [x] **Proxy backend para resolver CORS**
- [x] **URLs temporárias S3 com expiração de 1 hora**
- [x] Model `Proposicao` atualizado com campos S3
- [x] **Problema CORS resolvido com proxy backend**
- [x] **Captura do estado atual editado garantida**
- [x] **Upload direto S3 sem downloads locais**
- [x] **Logging completo para debug e monitoramento**
- [x] Testes de conectividade S3 realizados
- [x] Documentação completa atualizada

---

## 🔮 Próximos Passos

### **Melhorias Futuras**
1. **Múltiplas Versões**: Manter histórico de PDFs exportados por proposição
2. **Preview S3**: Integração de visualizador PDF direto das URLs S3
3. **Assinatura Digital**: Integração com assinatura digital diretamente no S3
4. **Compressão**: Otimizar tamanho dos PDFs antes do upload
5. **Async Processing**: Exportação em background para documentos muito grandes
6. **CDN Integration**: Distribuição via CloudFront para melhor performance global

### **Monitoramento e Analytics**
1. **Métricas S3**: Acompanhar uso de storage e transfer
2. **Performance**: Monitorar tempo de upload e download
3. **Alertas**: Notificar falhas na conversão ou upload S3
4. **Usage Analytics**: Estatísticas de uso por usuário/proposição
5. **Cost Monitoring**: Acompanhar custos AWS S3

### **Segurança Avançada**
1. **Encryption at Rest**: Criptografia adicional no S3
2. **Access Logs**: Log detalhado de acessos às URLs temporárias
3. **IP Restrictions**: Limitação de acesso por IP se necessário
4. **Audit Trail**: Trilha completa de auditoria de exportações

---

## 📚 Referências

- [OnlyOffice Document Server API](https://api.onlyoffice.com/editors/conversion)
- [AWS S3 PHP SDK Documentation](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-examples.html)
- [Laravel Storage S3 Configuration](https://laravel.com/docs/filesystem#s3-driver-configuration)
- [Laravel Flysystem S3 Adapter](https://flysystem.thephpleague.com/docs/adapter/aws-s3-v3/)
- [SweetAlert2 Documentation](https://sweetalert2.github.io/)

---

**Implementado em**: Setembro 2025
**Versão**: 2.0 - AWS S3 Integration
**Status**: ✅ Produção
**Última Atualização**: 23/09/2025

### **Changelog**
- **v2.0**: Implementação completa S3 com captura de estado atual editado
- **v1.5**: Resolução problemas CORS via proxy backend
- **v1.0**: Versão inicial com export local