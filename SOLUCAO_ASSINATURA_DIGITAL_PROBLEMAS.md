# 🔧 Solução: Problemas na Tela de Assinatura Digital

## 📋 Resumo do Problema

O sistema de assinatura digital apresentava múltiplos problemas que impediam o acesso à tela de assinatura:

1. **LibreOffice não encontrado** no container Docker
2. **DIVs com fundo cinza** aparecendo sobre o PDF (drag-and-drop)
3. **Arquivos HTML temporários** não encontrados pelo Laravel Storage
4. **Discrepâncias de caminhos** entre Docker containers
5. **Volume mapping complexo** entre OnlyOffice e aplicação

## 🎯 Solução Implementada

### 1. Remoção Completa do Sistema Drag-and-Drop

**Problema**: Interface criava DIVs com `background: rgba(0,0,0,0.1)` que apareciam como fundo cinza sobre o PDF.

**Arquivos modificados**:
- `resources/js/components/AssinaturaDigital.vue`
- `resources/views/proposicoes/assinatura/assinar-vue.blade.php`

**Mudanças**:
```vue
<!-- REMOVIDO: Sistema de posicionamento -->
<div v-if="modoPositionamento && !pdfLoading"
     class="position-absolute top-0 start-0 w-100 h-100"
     style="background: rgba(0,0,0,0.1); z-index: 10;">
</div>

<!-- ADICIONADO: Interface automática -->
<div class="alert alert-info py-2">
  <div class="d-flex align-items-center">
    <i class="fas fa-magic me-2 text-primary"></i>
    <div>
      <strong>Assinatura Automática v2.0</strong>
      <div class="small text-muted mt-1">
        O carimbo será aplicado automaticamente na lateral direita do documento com QR Code de verificação
      </div>
    </div>
  </div>
</div>
```

**Propriedades removidas**:
```javascript
// REMOVIDO do data():
modoPositionamento: false,
assinaturaPosition: null,
assinaturaConfirmada: null,
isDragging: false,
dragOffset: { x: 0, y: 0 },

// REMOVIDOS os métodos:
iniciarPositionamento()
cancelarPositionamento()
handlePDFClick()
startDragSignature()
removerAssinatura()
confirmarPositionamento()
signaturePreviewStyle()
```

### 2. Correção do Problema LibreOffice

**Problema**: Container Docker não possuía LibreOffice instalado.

**Solução**: Substituir chamadas diretas ao LibreOffice pelo `DocumentConversionService`.

**Arquivo**: `app/Http/Controllers/AssinaturaDigitalController.php`

```php
// ANTES: Chamada direta ao LibreOffice
$comando = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($diretorioDestino) . " " . escapeshellarg($caminhoHtml) . " 2>&1";
exec($comando, $output, $returnCode);

// DEPOIS: Usar DocumentConversionService
private function converterHtmlParaPdf(string $caminhoHtml, string $caminhoPdf): void
{
    $conversionService = app(\App\Services\DocumentConversionService::class);
    $resultado = $conversionService->convertToPDF($relativePath, $relativeOutputPath, 'rascunho');
}
```

### 3. Solução Final: DomPDF Direto em Memória

**Problema**: Arquivos temporários causavam problemas de path entre containers Docker.

**Solução**: Eliminar arquivos temporários e usar DomPDF diretamente.

**Implementação**:

```php
/**
 * Converter HTML diretamente para PDF usando DomPDF (sem arquivos temporários)
 */
private function converterHtmlParaPdfDireto(string $html, string $caminhoPdf): void
{
    try {
        // Usar DomPDF diretamente com o HTML em memória
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Obter conteúdo do PDF
        $pdfContent = $dompdf->output();

        // Salvar o PDF usando Laravel Storage
        $relativePdfPath = str_replace(storage_path('app/'), '', $caminhoPdf);
        Storage::put($relativePdfPath, $pdfContent);

        Log::info('HTML convertido para PDF com DomPDF', [
            'pdf_path' => $caminhoPdf,
            'pdf_relative_path' => $relativePdfPath,
            'pdf_size' => strlen($pdfContent)
        ]);

    } catch (\Exception $e) {
        Log::error('Erro ao converter HTML para PDF com DomPDF', [
            'pdf_path' => $caminhoPdf,
            'error' => $e->getMessage()
        ]);
        throw new \Exception('Falha na conversão HTML para PDF: ' . $e->getMessage());
    }
}
```

**Método principal atualizado**:

```php
private function gerarPdfDoConteudo(Proposicao $proposicao, string $caminhoPdf): void
{
    // Gerar HTML completo com charset UTF-8
    $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposição - ' . htmlspecialchars($proposicao->numero ?? 'S/N') . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
    <h1>Proposição Nº ' . htmlspecialchars($proposicao->numero ?? 'S/N') . '</h1>
    <pre>' . htmlspecialchars($conteudo) . '</pre>
</body>
</html>';

    // Usar DomPDF diretamente para converter HTML em memória
    $this->converterHtmlParaPdfDireto($html, $caminhoPdf);
}
```

### 4. Configuração de Prioridade de Conversores

**Arquivo**: `.env`

```env
# PDF Converter Priority (prioritize DomPDF for simple HTML)
PDF_CONVERTER_PRIORITY=dompdf,onlyoffice
```

## 📊 Resultados Obtidos

### ✅ Problemas Resolvidos

1. **Acesso à tela**: ✅ Usuários conseguem acessar `/proposicoes/X/assinatura-digital`
2. **Interface limpa**: ✅ Não há mais DIVs cinzas sobre o PDF
3. **Conversão funcional**: ✅ HTML → PDF funciona com DomPDF
4. **Docker independente**: ✅ Não depende mais de LibreOffice no container
5. **Arquivos temporários**: ✅ Eliminados (tudo em memória)

### 📈 Benefícios da Solução

- **Simplicidade**: DomPDF é mais leve que OnlyOffice para HTML simples
- **Confiabilidade**: Sem dependência de arquivos temporários ou volume mappings
- **Performance**: Conversão em memória é mais rápida
- **Manutenibilidade**: Código mais simples e direto
- **Compatibilidade**: Funciona em qualquer ambiente Docker

## 🚀 Sistema v2.0 Automático

Com os problemas de acesso resolvidos, o sistema agora utiliza:

1. **Detecção automática de perfil** baseada no tipo de proposição
2. **Coordenadas determinísticas** para carimbo lateral (120pt sidebar)
3. **QR Code + texto vertical** aplicados automaticamente
4. **Integração com PAdES** para assinatura digital válida

## 📝 Logs de Sucesso

Quando funcionando corretamente, os logs mostram:

```
[INFO] Criando arquivo HTML temporário
[INFO] Iniciando conversão HTML para PDF
[INFO] HTML convertido para PDF com DomPDF
[INFO] PDF gerado com sucesso
```

## 🔍 Troubleshooting

### Se o problema retornar:

1. **Verificar dependências**:
   ```bash
   composer show | grep dompdf
   ```

2. **Limpar caches**:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Verificar logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Testar DomPDF isoladamente**:
   ```php
   $dompdf = new \Dompdf\Dompdf();
   $dompdf->loadHtml('<h1>Teste</h1>');
   $dompdf->render();
   echo strlen($dompdf->output()) . " bytes gerados";
   ```

## 📚 Referências

- [DomPDF Documentation](https://github.com/dompdf/dompdf)
- [Laravel Storage](https://laravel.com/docs/filesystem)
- [Docker Volume Mapping](https://docs.docker.com/storage/volumes/)

---

**Desenvolvido em**: 25/09/2025
**Versão**: 2.0
**Status**: ✅ Resolvido e testado