# Correção: PDF S3 na Página de Assinatura Digital

## 🚨 Problema

Quando um PDF está armazenado na S3 mas a página de assinatura digital (`/proposicoes/{id}/assinatura-digital`) mostra um PDF local corrompido com texto substituído por "AAAAA".

## 🔍 Sintomas

1. **PDF na S3 está correto**: Ao acessar diretamente a URL S3, o PDF mostra o conteúdo real
2. **Página de assinatura mostra PDF corrompido**: Texto aparece como "AAA. 1A AAAAA AA AAAAAA..."
3. **Logs mostram fallback**: Sistema usa PDF local em vez do S3

## 📋 Diagnóstico

### Verificar se o problema existe:

1. **Acessar página de assinatura**: `/proposicoes/{id}/assinatura-digital`
2. **Verificar logs**: Procurar por `ℹ️ ASSINATURA: Nenhum PDF na S3 para esta proposição`
3. **Verificar status S3**: Acessar `/debug/proposicoes/{id}/s3-status`

Se `pdf_s3_path` for `null`, o problema existe.

## ✅ Solução

### 1. Implementar Sistema de Prioridade S3

**Arquivo**: `app/Http/Controllers/AssinaturaDigitalController.php`

```php
// 1) PRIORIDADE MÁXIMA: PDF na S3 (mais recente após exportação)
if ($proposicao->pdf_s3_path) {
    try {
        // Verificar se arquivo existe na S3
        if (Storage::disk('s3')->exists($proposicao->pdf_s3_path)) {
            // Gerar nova URL assinada (válida por 1 hora)
            $newS3Url = Storage::disk('s3')->temporaryUrl($proposicao->pdf_s3_path, now()->addHour());

            // Atualizar URL na proposição
            $proposicao->update(['pdf_s3_url' => $newS3Url]);

            // Redirecionar para S3
            return redirect($newS3Url);
        }
    } catch (\Exception $e) {
        Log::error('Erro ao acessar S3', ['error' => $e->getMessage()]);
    }
}

// 2) FALLBACK: PDF local
return app(ProposicaoController::class)->servePDF($proposicao);
```

### 2. Atualizar Middleware de Verificação

**Arquivo**: `app/Http/Middleware/CheckAssinaturaPermission.php`

```php
private function existePDFParaAssinatura(Proposicao $proposicao): bool
{
    // PRIORIDADE 1: Verificar se existe PDF na S3
    if ($proposicao->pdf_s3_path) {
        try {
            if (Storage::disk('s3')->exists($proposicao->pdf_s3_path)) {
                return true;
            }
        } catch (\Exception $e) {
            Log::warning('Erro ao verificar S3', ['error' => $e->getMessage()]);
        }
    }

    // PRIORIDADE 2: PDF local
    // ... resto da lógica existente
}
```

### 3. Atualizar Rota Específica

**Arquivo**: `routes/web.php`

```php
Route::prefix('proposicoes/{proposicao}/assinatura-digital')->name('proposicoes.assinatura-digital.')->middleware(['auth', 'check.assinatura.permission'])->group(function () {
    // Nova rota específica para PDF
    Route::get('/pdf', [AssinaturaDigitalController::class, 'servirPDFParaAssinatura'])->name('pdf');
    // ... outras rotas
});
```

### 4. Atualizar JavaScript da View

**Arquivo**: `resources/views/proposicoes/assinatura/assinar-vue.blade.php`

```javascript
async initializePdf() {
    try {
        // NOVA ROTA: Usar endpoint específico que prioriza S3
        const pdfRoute = `/proposicoes/${this.proposicaoId}/assinatura-digital/pdf`;
        this.pdfUrl = pdfRoute;

        const response = await fetch(pdfRoute, { method: 'HEAD' });
        if (response.ok) {
            this.pdfLoading = false;
        } else {
            this.generatePdf();
        }
    } catch (error) {
        console.error('Erro ao inicializar PDF:', error);
        this.pdfError = 'Erro ao carregar PDF';
        this.pdfLoading = false;
    }
}
```

## 🔧 Comando de Correção Manual

Se o PDF já existe na S3 mas não está sendo referenciado no banco:

### 1. Criar Endpoint de Debug/Fix

```php
public function fixProposicaoS3(Proposicao $proposicao)
{
    $s3Path = 'caminho/do/pdf/na/s3.pdf';

    DB::transaction(function () use ($proposicao, $s3Path) {
        if (Storage::disk('s3')->exists($s3Path)) {
            $size = Storage::disk('s3')->size($s3Path);
            $tempUrl = Storage::disk('s3')->temporaryUrl($s3Path, now()->addHour());

            $proposicao->pdf_s3_path = $s3Path;
            $proposicao->pdf_s3_url = $tempUrl;
            $proposicao->pdf_size_bytes = $size;
            $proposicao->save();
        }
    });

    $proposicao->refresh();
}
```

### 2. Executar Correção

```bash
# Via navegador (já logado)
http://localhost:8001/debug/proposicoes/{id}/fix-s3
```

## 🧪 Testes

### 1. Verificar Status
```bash
curl http://localhost:8001/debug/proposicoes/{id}/s3-status
```

### 2. Testar Assinatura
```bash
# Deve mostrar PDF correto da S3
http://localhost:8001/proposicoes/{id}/assinatura-digital
```

### 3. Logs Esperados
```
🔍 ASSINATURA: Verificando PDF na S3
🌐 ASSINATURA: PDF S3 encontrado, verificando disponibilidade
✅ ASSINATURA: Arquivo confirmado na S3
🔄 ASSINATURA: Gerando nova URL S3
✅ ASSINATURA: Nova URL S3 gerada - redirecionando
```

## 📊 Campos da Tabela

A tabela `proposicoes` deve ter os campos:

```sql
ALTER TABLE proposicoes ADD COLUMN pdf_s3_path VARCHAR(500) NULL COMMENT 'Caminho do PDF no AWS S3';
ALTER TABLE proposicoes ADD COLUMN pdf_s3_url TEXT NULL COMMENT 'URL assinada temporária do PDF no S3';
ALTER TABLE proposicoes ADD COLUMN pdf_size_bytes BIGINT NULL COMMENT 'Tamanho do PDF em bytes';
```

## 🎯 Resultado

- ✅ **Performance melhorada**: PDFs servidos diretamente da S3
- ✅ **URLs sempre válidas**: Regeneração automática quando expiram
- ✅ **Fallback robusto**: Nunca falha, sempre tem alternativa
- ✅ **Logs detalhados**: Facilita debug em produção
- ✅ **Zero downtime**: Mantém compatibilidade com sistema atual

## 🛠️ Troubleshooting

### Problema: PDF ainda mostra conteúdo corrompido
**Solução**: Verificar se `pdf_s3_path` foi atualizado no banco

### Problema: URL S3 expira rapidamente
**Solução**: URLs são válidas por 1 hora e regeneradas automaticamente

### Problema: Erro de permissão S3
**Solução**: Verificar credenciais AWS no `.env`

---

**Data da Correção**: 24/09/2025
**Versão**: v2.0
**Status**: ✅ Implementado e Testado