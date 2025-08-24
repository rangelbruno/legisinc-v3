# 🔐 Solução: Assinatura Digital e Protocolo em PDFs

## 📋 **Descrição do Problema**

### **Sintomas Identificados**
- ✅ Proposição **assinada digitalmente** com sucesso
- ✅ Proposição **protocolada** com número `mocao/2025/0001`
- ❌ **PDF ainda mostra** `[AGUARDANDO PROTOCOLO]` em vez do número real
- ❌ **Assinatura digital não aparece** no PDF final

### **Comportamento Esperado vs. Real**
```
ESPERADO: MOÇÃO Nº mocao/2025/0001
REAL:    MOÇÃO Nº [AGUARDANDO PROTOCOLO]
```

## 🔍 **Análise Técnica**

### **Investigação Realizada**
1. **Verificação de Status da Proposição**
   - Status: `protocolado` ✅
   - Número Protocolo: `mocao/2025/0001` ✅
   - Assinatura Digital: `SIM` ✅
   - Data Assinatura: `21/08/2025 22:52:12` ✅

2. **Verificação de Arquivos PDF**
   - PDFs assinados existem em: `storage/app/proposicoes/pdfs/2/` ✅
   - PDFs antigos existem em: `storage/app/private/proposicoes/pdfs/2/` ✅

3. **Teste de Processo de Substituição**
   - Substituição de placeholders funciona ✅
   - Conversão DOCX → HTML funciona ✅
   - Assinatura digital é adicionada corretamente ✅

### **Causa Raiz Identificada**
O método `encontrarPDFMaisRecente` no `ProposicaoController` estava buscando PDFs no **diretório errado**:

```php
// ❌ ANTES: Buscava apenas em um diretório
$diretorioPDFs = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}/");

// ✅ DEPOIS: Busca em múltiplos diretórios
$diretoriosParaBuscar = [
    storage_path("app/proposicoes/pdfs/{$proposicao->id}/"),      // PDFs assinados
    storage_path("app/private/proposicoes/pdfs/{$proposicao->id}/"), // Fallback
    storage_path("app/public/proposicoes/pdfs/{$proposicao->id}/"),  // Fallback
];
```

## 🛠️ **Solução Implementada**

### **Arquivo Modificado**
- **Arquivo:** `app/Http/Controllers/ProposicaoController.php`
- **Método:** `encontrarPDFMaisRecente()`
- **Linhas:** 6220-6280

### **Lógica da Solução**
1. **Busca em Múltiplos Diretórios**
   - Prioriza diretório onde PDFs assinados são criados
   - Mantém compatibilidade com diretórios antigos
   - Inclui diretório público como fallback

2. **Priorização de PDFs Assinados**
   - Identifica PDFs com `_assinado_` no nome
   - Retorna o PDF assinado mais recente
   - Fallback para PDF mais recente se não houver assinado

3. **Consolidação de Resultados**
   - Combina PDFs de todos os diretórios
   - Aplica ordenação por data de modificação
   - Garante que o PDF correto seja retornado

## 📁 **Estrutura de Diretórios**

### **Antes da Correção**
```
storage/
├── app/
│   ├── private/proposicoes/pdfs/2/     ← Buscava aqui (PDFs antigos)
│   └── proposicoes/pdfs/2/             ← PDFs assinados (ignorados)
```

### **Depois da Correção**
```
storage/
├── app/
│   ├── proposicoes/pdfs/2/             ← Prioridade 1 (PDFs assinados)
│   ├── private/proposicoes/pdfs/2/     ← Prioridade 2 (fallback)
│   └── public/proposicoes/pdfs/2/      ← Prioridade 3 (fallback)
```

## 🔄 **Fluxo de Funcionamento**

### **Processo de Assinatura e Protocolação**
1. **Usuário assina digitalmente** → PDF assinado criado em `proposicoes/pdfs/2/`
2. **Usuário protocola** → Número de protocolo atribuído
3. **PDF regenerado** → Placeholders substituídos corretamente
4. **Usuário acessa PDF** → Método corrigido encontra PDF correto

### **Busca de PDF (Método Corrigido)**
1. **Verifica campo `arquivo_pdf_path`** (se existir)
2. **Busca em múltiplos diretórios** (ordem de prioridade)
3. **Prioriza PDFs assinados** (`_assinado_` no nome)
4. **Retorna PDF mais recente** encontrado

## ✅ **Resultados Esperados**

### **Após a Correção**
- ✅ **PDF mostra número de protocolo correto**: `mocao/2025/0001`
- ✅ **Assinatura digital aparece** no PDF
- ✅ **QR Code de verificação** incluído
- ✅ **Compatibilidade mantida** com PDFs antigos

### **Verificação de Funcionamento**
```bash
# Acessar PDF da proposição #2
GET /proposicoes/2/pdf

# Resultado esperado:
# - Número: "MOÇÃO Nº mocao/2025/0001"
# - Assinatura: "Assinatura Digital - Jessica Santos"
# - QR Code: "Verificação Online"
```

## 🧪 **Testes Realizados**

### **Testes de Validação**
1. **Teste de Substituição de Placeholders** ✅
   - `[AGUARDANDO PROTOCOLO]` → `mocao/2025/0001`

2. **Teste de Conversão DOCX → HTML** ✅
   - LibreOffice converte corretamente
   - Placeholders preservados no HTML

3. **Teste de Adição de Assinatura** ✅
   - HTML expande de 115 para 41.886 caracteres
   - Assinatura digital incluída corretamente

4. **Teste de Método Corrigido** ✅
   - Busca em múltiplos diretórios
   - Prioriza PDFs assinados
   - Retorna PDF correto

## 🔧 **Manutenção e Monitoramento**

### **Logs de Debug**
- **Arquivo:** `storage/logs/laravel.log`
- **Filtros:** `PDF Assinatura`, `encontrarPDFMaisRecente`
- **Monitoramento:** Verificar se PDFs corretos são encontrados

### **Indicadores de Sucesso**
- PDFs mostram número de protocolo correto
- Assinaturas digitais aparecem em todos os PDFs
- Sem erros de "PDF não encontrado"

### **Possíveis Problemas Futuros**
- Mudanças na estrutura de diretórios
- Alterações no formato de nomes de arquivos
- Problemas de permissões de acesso

## 📚 **Referências Técnicas**

### **Arquivos Relacionados**
- `app/Http/Controllers/ProposicaoController.php` - Método corrigido
- `app/Http/Controllers/ProposicaoAssinaturaController.php` - Geração de PDFs
- `app/Services/Template/AssinaturaQRService.php` - Assinatura digital

### **Métodos Principais**
- `encontrarPDFMaisRecente()` - Busca de PDFs (CORRIGIDO)
- `servePDF()` - Servir PDF para usuário
- `regenerarPDFAtualizado()` - Regenerar PDF com dados atualizados

### **Dependências**
- **LibreOffice:** Conversão DOCX → HTML
- **DomPDF:** Conversão HTML → PDF
- **Laravel Storage:** Gerenciamento de arquivos

## 🎯 **Conclusão**

A solução implementada resolve o problema fundamental de **busca de PDFs no diretório incorreto**. O sistema agora:

1. **Encontra corretamente** os PDFs assinados e atualizados
2. **Mantém compatibilidade** com estrutura de diretórios existente
3. **Prioriza PDFs mais recentes** e assinados
4. **Garante que usuários vejam** o conteúdo correto

A correção é **não-intrusiva** e **retrocompatível**, não afetando outras funcionalidades do sistema.




