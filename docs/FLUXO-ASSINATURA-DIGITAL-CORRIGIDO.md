# Fluxo de Assinatura Digital - Correções Aplicadas

## 📋 Resumo das Correções

### Problema Principal
O sistema apresentava erro "Failed to save signed PDF" ao tentar assinar digitalmente um documento após aprovação legislativa.

### Causas Identificadas

1. **Conversão de Paths Incorreta**
   - O Storage do Laravel usa sistema de "disks" 
   - O disk "private" automaticamente adiciona prefixo `private/`
   - Estava ocorrendo duplicação: `storage/app/private/private/proposicoes/...`

2. **Permissões no Container Docker**
   - Container não tem permissão para executar `chown` e `chgrp`
   - Erro: `chown(): Operation not permitted`

## ✅ Correções Aplicadas

### 1. PDFStampingService - Conversão de Paths
```php
// ANTES (incorreto)
$relativePath = 'private/' . str_replace(storage_path('app/private/'), '', $outputPath);

// DEPOIS (correto)
$storagePath = storage_path('app/');
if (strpos($outputPath, $storagePath) === 0) {
    $relativePath = substr($outputPath, strlen($storagePath));
    // Remove 'private/' se já existir (Storage adiciona automaticamente)
    if (strpos($relativePath, 'private/') === 0) {
        $relativePath = substr($relativePath, 8);
    }
}
```

### 2. PDFStampingService - Caminho Absoluto
```php
// ANTES (construção manual)
$absoluteSignedPath = storage_path('app/' . $relativePath);

// DEPOIS (usando Storage::path)
$absoluteSignedPath = Storage::path($relativePath);
```

### 3. PDFStampingService - Permissões
```php
// ANTES (falha no container)
chown($absoluteSignedPath, 'www-data');
chgrp($absoluteSignedPath, 'www-data');
chmod($absoluteSignedPath, 0666);

// DEPOIS (compatível com container)
@chmod($absoluteSignedPath, 0666);
```

## 🔄 Fluxo Completo Corrigido

### Fase 1: Aprovação Legislativa
1. Legislativo aprova edições
2. Sistema invalida PDFs antigos:
   ```php
   $proposicao->update([
       'status' => 'aprovado_assinatura',
       'arquivo_pdf_path' => null,
       'pdf_gerado_em' => null,
       'pdf_conversor_usado' => null
   ]);
   ```

### Fase 2: Geração do PDF
1. Sistema detecta necessidade de novo PDF
2. Converte RTF mais recente para PDF
3. Salva em `storage/app/private/proposicoes/pdfs/{id}/`

### Fase 3: Assinatura Digital
1. Parlamentar acessa `/proposicoes/{id}/assinatura-digital`
2. Upload do certificado PFX
3. **PDFStampingService::applySignatureStamp()**:
   - Carrega PDF original com FPDI
   - Adiciona carimbo visual de assinatura
   - Gera novo PDF assinado
   - Converte path corretamente (sem duplicar 'private/')
   - Salva usando Storage::put()
   - Ajusta permissões (apenas chmod, sem chown/chgrp)

### Fase 4: Arquivo Final
- PDF assinado salvo em: `proposicoes/pdfs/{id}/proposicao_{id}_onlyoffice_{timestamp}_assinado_{timestamp}.pdf`
- Permissões: 0666 (leitura/escrita para todos)
- Proposição atualizada com status `assinado`

## 🧪 Teste de Validação

```bash
# 1. Resetar banco e criar proposição teste
docker exec -it legisinc-app php artisan migrate:fresh --seed

# 2. Login como Parlamentar
# Email: jessica@sistema.gov.br
# Senha: 123456

# 3. Criar proposição e editar no OnlyOffice

# 4. Enviar para Legislativo

# 5. Login como Legislativo
# Email: joao@sistema.gov.br  
# Senha: 123456

# 6. Aprovar edições

# 7. Voltar como Parlamentar

# 8. Acessar assinatura digital

# 9. Upload do certificado PFX de teste

# 10. Verificar PDF assinado gerado com sucesso
```

## 📁 Arquivos Modificados

1. `/app/Services/PDFStampingService.php`:
   - Linha 58-70: Conversão de path relativo corrigida
   - Linha 101-103: Uso de Storage::path()
   - Linha 112-119: Remoção de chown/chgrp
   - Linhas equivalentes no método applyProtocolStamp()

## 🎯 Resultado

✅ **Assinatura digital funcionando corretamente**
- PDFs são gerados no local correto
- Não há mais duplicação de paths
- Compatível com ambiente Docker
- Permissões adequadas para leitura/escrita

## 📝 Observações

1. **Ambiente Docker**: O container roda com usuário não-root, impossibilitando mudança de ownership
2. **Storage Laravel**: Sempre usar métodos do Storage para manipulação de arquivos
3. **Paths Relativos**: Cuidado com o disk "private" que adiciona prefixo automaticamente

---

**Status**: ✅ Corrigido e Testado  
**Data**: 06/09/2025  
**Versão**: v2.1.1