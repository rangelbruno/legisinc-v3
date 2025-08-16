# 🔧 SOLUÇÃO: PDF Aberto em Nova Aba com Template Correto

## ❌ PROBLEMA IDENTIFICADO

**Situação**: Na tela de assinatura (`/proposicoes/1/assinar`):
- O PDF mostrado no iframe está CORRETO (com template OnlyOffice)
- Mas ao clicar "Abrir PDF" em nova aba, mostra PDF DIFERENTE (sem formatação)

**Causa Raiz**:
- Ambos usam a mesma rota: `proposicoes.serve-pdf`
- Mas o campo `arquivo_path` (DOCX do OnlyOffice) estava NULL
- PDF foi gerado do template sem o conteúdo editado pelo Legislativo

---

## 🔍 DIAGNÓSTICO

### 1. **Verificação do Campo arquivo_path**
```bash
# Proposição criada via seeder
arquivo_path: NULL  # ❌ Sem arquivo DOCX
arquivo_pdf_path: proposicoes/pdfs/1/proposicao_1.pdf  # ✅ PDF existe
```

### 2. **OnlyOffice Callbacks Funcionando**
- Arquivos DOCX ERAM salvos quando editados
- 10+ arquivos DOCX encontrados: `proposicao_1_*.docx`
- Mas campo `arquivo_path` não era atualizado no seeder

### 3. **Geração do PDF**
- Se `arquivo_path` é NULL → PDF gerado do template
- Se `arquivo_path` existe → PDF gerado do DOCX editado

---

## ✅ SOLUÇÃO IMPLEMENTADA

### 1. **Atualizar arquivo_path com DOCX do OnlyOffice**
```php
$proposicao->arquivo_path = 'proposicoes/proposicao_1_1755296796.docx';
$proposicao->save();
```

### 2. **Regenerar PDF do DOCX Correto**
```php
$controller = new ProposicaoAssinaturaController();
$method->invoke($controller, $proposicao);
// PDF regenerado: 63KB (formatação preservada)
```

---

## 🎯 CORREÇÃO PERMANENTE NECESSÁRIA

### **ProposicaoTesteAssinaturaSeeder.php**

O seeder deve simular que a proposição foi editada no OnlyOffice:

```php
// Após criar a proposição
$proposicao = Proposicao::create([...]);

// Simular edição no OnlyOffice criando arquivo DOCX
$docxContent = $this->gerarDocxDoTemplate($proposicao, $template);
$docxPath = "proposicoes/proposicao_{$proposicao->id}_" . time() . ".docx";
Storage::disk('local')->put($docxPath, $docxContent);

// Atualizar proposição com arquivo_path
$proposicao->update(['arquivo_path' => $docxPath]);

// Agora gerar PDF do DOCX (não do template)
$this->gerarPDFParaAssinatura($proposicao);
```

---

## 📊 RESULTADO

### ✅ **ANTES DA CORREÇÃO**
- PDF no iframe: Correto (template OnlyOffice)
- PDF em nova aba: Incorreto (sem formatação)
- arquivo_path: NULL

### ✅ **DEPOIS DA CORREÇÃO**
- PDF no iframe: Correto
- PDF em nova aba: Correto (mesmo PDF)
- arquivo_path: proposicoes/proposicao_1_*.docx
- PDF size: 63KB (formatação preservada)

---

## 🔄 FLUXO CORRETO

1. **Parlamentar** cria proposição
2. **OnlyOffice** salva DOCX em `proposicoes/`
3. **Campo** `arquivo_path` é atualizado
4. **Legislativo** edita no OnlyOffice
5. **Callback** atualiza DOCX existente
6. **PDF** é gerado do DOCX (não do template)
7. **Ambos PDFs** (iframe e nova aba) são idênticos

---

## 🚨 IMPORTANTE

**O problema só ocorre com proposições criadas via seeder** porque:
- Seeder não simula edição no OnlyOffice
- Campo `arquivo_path` fica NULL
- PDF é gerado do template sem edições

**Proposições criadas normalmente** (via interface):
- OnlyOffice salva DOCX automaticamente
- Campo `arquivo_path` é preenchido
- PDF sempre gerado do DOCX editado

---

## 📝 CHECKLIST DE VERIFICAÇÃO

✅ `arquivo_path` não é NULL  
✅ Arquivo DOCX existe em `storage/app/`  
✅ PDF gerado do DOCX (não do template)  
✅ PDF size > 50KB (formatação preservada)  
✅ Mesmo PDF no iframe e nova aba  

---

**Data**: 15/08/2025  
**Status**: ✅ RESOLVIDO (necessita ajuste no seeder)