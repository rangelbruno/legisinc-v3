# Melhorias Automáticas Detectadas

## Melhoria #2 - 2025-09-17 14:36:36

**Arquivos alterados:** 33

- `app/Http/Controllers/ProposicaoAssinaturaController.php` (novo)
- `app/Http/Controllers/ProposicaoProtocoloController.php` (novo)
- `app/Services/OnlyOffice/OnlyOfficeService.php` (novo)
- `app/Services/Template/TemplateProcessorService.php` (novo)
- `app/Services/Template/TemplateVariableService.php` (novo)
- `app/Models/Proposicao.php` (novo)
- `config/dompdf.php` (novo)
- `resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php` (novo)
- `resources/views/proposicoes/assinatura/assinar-vue.blade.php` (novo)
- `resources/views/proposicoes/assinatura/assinar.blade.php` (novo)
- `resources/views/proposicoes/assinatura/historico.blade.php` (novo)
- `resources/views/proposicoes/assinatura/index.blade.php` (novo)
- `resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php` (novo)
- `resources/views/proposicoes/consulta/nao-encontrada.blade.php` (novo)
- `resources/views/proposicoes/consulta/publica.blade.php` (novo)
- `resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php` (novo)
- `resources/views/proposicoes/legislativo/editar.blade.php` (novo)
- `resources/views/proposicoes/legislativo/index.blade.php` (novo)
- `resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php` (novo)
- `resources/views/proposicoes/legislativo/relatorio-dados.blade.php` (novo)
- `resources/views/proposicoes/legislativo/relatorio-pdf.blade.php` (novo)
- `resources/views/proposicoes/legislativo/relatorio.blade.php` (novo)
- `resources/views/proposicoes/legislativo/revisar.blade.php` (novo)
- `resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php` (novo)
- `resources/views/proposicoes/pdf/protocolo-otimizado.blade.php` (novo)
- `resources/views/proposicoes/pdf/template-optimized.blade.php` (novo)
- `resources/views/proposicoes/pdf/template.blade.php` (novo)
- `resources/views/proposicoes/protocolo/index-melhorado.blade.php` (novo)
- `resources/views/proposicoes/protocolo/index-original.blade.php` (novo)
- `resources/views/proposicoes/protocolo/index.blade.php` (novo)
- `resources/views/proposicoes/protocolo/protocolar-simples.blade.php` (novo)
- `resources/views/proposicoes/protocolo/protocolar.blade.php` (novo)
- `resources/views/proposicoes/protocolo/protocolos-hoje.blade.php` (novo)

**Seeder criado:** `PreservarMelhorias2Seeder`

---

## Melhoria #18 - 2025-09-15 21:56:09

**Arquivos alterados:** 33

- `app/Http/Controllers/ProposicaoAssinaturaController.php` (modificado)
- `app/Http/Controllers/ProposicaoProtocoloController.php` (modificado)
- `app/Services/OnlyOffice/OnlyOfficeService.php` (modificado)
- `app/Services/Template/TemplateProcessorService.php` (modificado)
- `app/Services/Template/TemplateVariableService.php` (modificado)
- `app/Models/Proposicao.php` (modificado)
- `config/dompdf.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar-vue.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/historico.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/index.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php` (modificado)
- `resources/views/proposicoes/consulta/nao-encontrada.blade.php` (modificado)
- `resources/views/proposicoes/consulta/publica.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/editar.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/index.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio-dados.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio-pdf.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/revisar.blade.php` (modificado)
- `resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php` (modificado)
- `resources/views/proposicoes/pdf/protocolo-otimizado.blade.php` (modificado)
- `resources/views/proposicoes/pdf/template-optimized.blade.php` (modificado)
- `resources/views/proposicoes/pdf/template.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index-melhorado.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index-original.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolar-simples.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolar.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolos-hoje.blade.php` (modificado)

**Seeder criado:** `PreservarMelhorias18Seeder`

---

## Melhoria #17 - 2025-09-15 21:30:00

**Correção Crítica: PDF não refletia edições do Legislativo**

**Problema Identificado:**
- Quando o Legislativo editava documento no OnlyOffice e o Parlamentar aprovava
- O PDF gerado não mostrava as alterações feitas pelo Legislativo
- PDF usava conteúdo antigo do banco ao invés do RTF atualizado

**Arquivos alterados:** 2
- `app/Http/Controllers/ProposicaoController.php` (modificado - métodos servePDF e aprovarEdicoesLegislativo)
- `database/seeders/CorrecaoPDFLegislativoSeeder.php` (criado)

**Solução Implementada:**
1. servePDF agora extrai conteúdo do RTF atualizado antes de gerar PDF
2. aprovarEdicoesLegislativo adiciona logs detalhados para rastreamento
3. PDF sempre reflete o conteúdo mais recente editado no OnlyOffice

**Seeder criado:** `CorrecaoPDFLegislativoSeeder`

---

## Melhoria #16 - 2025-09-15 21:04:25

**Arquivos alterados:** 33

- `app/Http/Controllers/ProposicaoAssinaturaController.php` (modificado)
- `app/Http/Controllers/ProposicaoProtocoloController.php` (modificado)
- `app/Services/OnlyOffice/OnlyOfficeService.php` (modificado)
- `app/Services/Template/TemplateProcessorService.php` (modificado)
- `app/Services/Template/TemplateVariableService.php` (modificado)
- `app/Models/Proposicao.php` (modificado)
- `config/dompdf.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar-vue.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/historico.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/index.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php` (modificado)
- `resources/views/proposicoes/consulta/nao-encontrada.blade.php` (modificado)
- `resources/views/proposicoes/consulta/publica.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/editar.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/index.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio-dados.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio-pdf.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/revisar.blade.php` (modificado)
- `resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php` (modificado)
- `resources/views/proposicoes/pdf/protocolo-otimizado.blade.php` (modificado)
- `resources/views/proposicoes/pdf/template-optimized.blade.php` (modificado)
- `resources/views/proposicoes/pdf/template.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index-melhorado.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index-original.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolar-simples.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolar.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolos-hoje.blade.php` (modificado)

**Seeder criado:** `PreservarMelhorias16Seeder`

---

## Melhoria #14 - 2025-09-15 19:17:22

**Arquivos alterados:** 33

- `app/Http/Controllers/ProposicaoAssinaturaController.php` (modificado)
- `app/Http/Controllers/ProposicaoProtocoloController.php` (modificado)
- `app/Services/OnlyOffice/OnlyOfficeService.php` (modificado)
- `app/Services/Template/TemplateProcessorService.php` (modificado)
- `app/Services/Template/TemplateVariableService.php` (modificado)
- `app/Models/Proposicao.php` (modificado)
- `config/dompdf.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar-vue.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/historico.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/index.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php` (modificado)
- `resources/views/proposicoes/consulta/nao-encontrada.blade.php` (modificado)
- `resources/views/proposicoes/consulta/publica.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/editar.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/index.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio-dados.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio-pdf.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/revisar.blade.php` (modificado)
- `resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php` (modificado)
- `resources/views/proposicoes/pdf/protocolo-otimizado.blade.php` (modificado)
- `resources/views/proposicoes/pdf/template-optimized.blade.php` (modificado)
- `resources/views/proposicoes/pdf/template.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index-melhorado.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index-original.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolar-simples.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolar.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolos-hoje.blade.php` (modificado)

**Seeder criado:** `PreservarMelhorias14Seeder`

---

## Melhoria #12 - 2025-09-15 18:38:28

**Arquivos alterados:** 33

- `app/Http/Controllers/ProposicaoAssinaturaController.php` (modificado)
- `app/Http/Controllers/ProposicaoProtocoloController.php` (modificado)
- `app/Services/OnlyOffice/OnlyOfficeService.php` (modificado)
- `app/Services/Template/TemplateProcessorService.php` (modificado)
- `app/Services/Template/TemplateVariableService.php` (modificado)
- `app/Models/Proposicao.php` (modificado)
- `config/dompdf.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar-pdf-vue.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar-vue.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/assinar.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/historico.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/index.blade.php` (modificado)
- `resources/views/proposicoes/assinatura/visualizar-pdf-otimizado.blade.php` (modificado)
- `resources/views/proposicoes/consulta/nao-encontrada.blade.php` (modificado)
- `resources/views/proposicoes/consulta/publica.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/aguardando-protocolo.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/editar.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/index.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/onlyoffice-editor.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio-dados.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio-pdf.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/relatorio.blade.php` (modificado)
- `resources/views/proposicoes/legislativo/revisar.blade.php` (modificado)
- `resources/views/proposicoes/parlamentar/onlyoffice-editor.blade.php` (modificado)
- `resources/views/proposicoes/pdf/protocolo-otimizado.blade.php` (modificado)
- `resources/views/proposicoes/pdf/template-optimized.blade.php` (modificado)
- `resources/views/proposicoes/pdf/template.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index-melhorado.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index-original.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/index.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolar-simples.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolar.blade.php` (modificado)
- `resources/views/proposicoes/protocolo/protocolos-hoje.blade.php` (modificado)

**Seeder criado:** `PreservarMelhorias12Seeder`

---

