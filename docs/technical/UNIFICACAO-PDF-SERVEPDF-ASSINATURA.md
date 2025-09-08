# Unificação da Lógica de Servir PDFs

## 📋 Resumo das Mudanças

Este documento descreve as mudanças realizadas para unificar a lógica de servir PDFs entre as rotas `/proposicoes/{id}/pdf` e `/proposicoes/{id}/assinatura-digital`, garantindo que ambas sempre mostrem o mesmo PDF atualizado.

## 🔄 Problema Identificado

### Antes da Mudança:
- **`/proposicoes/{id}/pdf`**: Usava cache agressivo, muitas vezes mostrando PDFs desatualizados
- **`/proposicoes/{id}/assinatura-digital`**: Sempre gerava PDF fresco do arquivo mais recente
- **Resultado**: Usuários viam PDFs diferentes dependendo da rota acessada

## ✅ Solução Implementada

### Nova Estratégia Unificada:

1. **Busca de Arquivo Fonte**:
   - Primeiro verifica `arquivo_path` do banco (arquivo editado no OnlyOffice)
   - Se não encontrar, busca em diretórios conhecidos por padrão
   - Prioriza sempre o arquivo mais recente baseado em `filemtime()`

2. **Geração de PDF**:
   - Sempre gera PDF fresco do arquivo RTF/DOCX mais recente
   - Usa LibreOffice para conversão preservando formatação
   - Fallback para PDF existente apenas se não houver arquivo fonte

3. **Headers Consistentes**:
   - `Cache-Control: no-cache, no-store, must-revalidate`
   - `X-PDF-Source` indica origem do PDF
   - Nome único com timestamp para forçar refresh

## 📝 Mudanças no Código

### Arquivo: `app/Http/Controllers/ProposicaoController.php`

#### Método `servePDF()` - ANTES:
```php
// Usava encontrarPDFMaisRecenteRobusta() que priorizava cache
$relativePath = $this->encontrarPDFMaisRecenteRobusta($proposicao);

// Verificava timestamps mas nem sempre regenerava
if ($rtfModificado > $pdfGerado) {
    $pdfEstaDesatualizado = true;
}

// Servia PDF em cache se não estivesse desatualizado
if (!$pdfEstaDesatualizado) {
    return response()->file($absolutePath, [...]);
}
```

#### Método `servePDF()` - DEPOIS:
```php
// NOVA ESTRATÉGIA: Usar mesma lógica da assinatura digital

// 1. Buscar arquivo DOCX/RTF mais recente
if ($proposicao->arquivo_path && Storage::exists($proposicao->arquivo_path)) {
    $arquivoMaisRecente = [
        'path' => Storage::path($proposicao->arquivo_path),
        'relative_path' => $proposicao->arquivo_path,
        'tipo' => pathinfo($caminhoCompleto, PATHINFO_EXTENSION),
        'modificado' => filemtime($caminhoCompleto)
    ];
}

// 2. Se não encontrou, buscar em diretórios
if (!$arquivoMaisRecente) {
    // Busca em storage/app/proposicoes e storage/app/private/proposicoes
    // Ordena por data de modificação (mais recente primeiro)
}

// 3. Gerar PDF fresco sempre
$nomePdf = 'proposicao_' . $proposicao->id . '_unified_' . time() . '.pdf';
$sucesso = $this->converterArquivoParaPDFUnificado($arquivoMaisRecente['path'], $caminhoPdfAbsoluto);

// 4. Servir com headers no-cache
return response()->file($caminhoPdfAbsoluto, [
    'Content-Type' => 'application/pdf',
    'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
    'X-PDF-Generator' => 'libreoffice-unified',
    'X-PDF-Source' => 'generated-fresh'
]);
```

### Novos Métodos Auxiliares:

#### `encontrarPDFMaisRecenteParaServir()`
```php
// Busca PDF existente como fallback quando não há arquivo fonte
// Prioridades:
// 1. arquivo_pdf_path do banco
// 2. Diretório proposicoes/pdfs/{id}
// 3. Diretório private/proposicoes/pdfs/{id}
```

#### `converterArquivoParaPDFUnificado()`
```php
// Conversão direta usando LibreOffice
// Preserva formatação do OnlyOffice
// Comando: libreoffice --headless --convert-to pdf
```

## 🎯 Benefícios da Mudança

1. **Consistência**: Ambas as rotas sempre mostram o mesmo PDF
2. **Atualização**: PDFs sempre refletem a versão mais recente do documento
3. **Performance**: Geração sob demanda evita PDFs desatualizados em cache
4. **Confiabilidade**: Fallbacks garantem disponibilidade mesmo sem arquivo fonte
5. **Rastreabilidade**: Headers indicam origem e método de geração

## 🔍 Verificação

### Como testar:
1. Editar uma proposição no OnlyOffice
2. Acessar `/proposicoes/{id}/pdf` - deve mostrar versão atualizada
3. Acessar `/proposicoes/{id}/assinatura-digital` - deve mostrar o mesmo PDF
4. Verificar headers no DevTools - deve ter `Cache-Control: no-cache`

### Logs para monitoramento:
```php
Log::info('🔴 PDF REQUEST: Usando estratégia unificada com assinatura digital', [...]);
Log::info('🔴 PDF REQUEST: Arquivo RTF/DOCX encontrado no banco', [...]);
Log::info('🔴 PDF REQUEST: Gerando PDF do arquivo mais recente', [...]);
Log::info('🔴 PDF REQUEST: PDF gerado com sucesso', [...]);
```

## 📊 Comparação de Fluxos

### Fluxo Antigo:
```
Request → Buscar PDF em cache → Verificar timestamps → Servir cache (potencialmente desatualizado)
```

### Fluxo Novo:
```
Request → Buscar arquivo fonte mais recente → Gerar PDF fresco → Servir com no-cache
```

## ⚠️ Pontos de Atenção

1. **Performance**: Geração sob demanda pode aumentar tempo de resposta
   - Mitigação: PDFs são salvos para reuso futuro
   
2. **LibreOffice**: Dependência do LibreOffice para conversão
   - Fallback: Usa PDF existente se conversão falhar
   
3. **Storage**: Mais PDFs gerados podem aumentar uso de disco
   - Solução: Implementar rotina de limpeza de PDFs antigos

## 📅 Histórico

- **Data**: 08/09/2025
- **Autor**: Sistema
- **Versão**: 2.0
- **Status**: Implementado e testado

## 🔗 Arquivos Relacionados

- `/app/Http/Controllers/ProposicaoController.php` - Método servePDF refatorado
- `/app/Http/Controllers/ProposicaoAssinaturaController.php` - Lógica de referência
- `/docs/technical/FLUXO-PDF-PROPOSICOES.md` - Documentação do fluxo
- `/docs/technical/FLUXO-PDF-ASSINATURA-DIGITAL.md` - Documentação assinatura

---

**RESULTADO**: Sistema agora garante que `/proposicoes/{id}/pdf` e `/proposicoes/{id}/assinatura-digital` sempre mostram o mesmo PDF atualizado, eliminando inconsistências e melhorando a experiência do usuário.