# ANÁLISE COMPLETA DO FLUXO DE PROPOSIÇÕES - Sistema Legisinc

**Data da Análise**: 21/08/2025  
**Proposição Analisada**: ID 8 (Moção)  
**Status**: PROBLEMA IDENTIFICADO na geração de PDF para assinatura  

---

## 📋 FLUXO COMPLETO DOCUMENTADO

### **1. CRIAÇÃO DA PROPOSIÇÃO** 
**Rota**: `/proposicoes/create?tipo=mocao&nome=Moção`  
**Usuário**: Parlamentar (jessica@sistema.gov.br - ID: 6)  
**Timestamp**: 2025-08-21 00:29:05  

#### **Dados Inseridos**:
- **Ementa**: "Editado pelo Parlamentar"
- **Tipo**: Moção
- **Forma de criação**: Personalizado (não IA)
- **Anexo**: Opcional (não usado neste caso)

#### **Logs de Criação**:
```log
[2025-08-21 00:29:05] local.INFO: Proposição criada {"id":8,"autor_id":6,"tipo":"mocao"}
[2025-08-21 00:29:05] local.INFO: Status da proposição alterado {"id":8,"status_anterior":"rascunho","status_novo":"em_edicao"}
```

#### **Resultado**:
- ✅ Proposição criada com ID: 8
- ✅ Status inicial: `rascunho` → `em_edicao`
- ✅ Autor: Parlamentar Jessica Santos (ID: 6)

---

### **2. EDIÇÃO NO ONLYOFFICE (PARLAMENTAR)**
**Rota**: `/proposicoes/8` → Botão "Continuar Editando"  
**Timestamp**: 2025-08-21 00:30:24 - 00:30:47  

#### **Processo de Carregamento do Editor**:
```log
[2025-08-21 00:30:24] local.INFO: OnlyOffice Editor Access - Parlamentar {
    "user_id":6,
    "proposicao_id":8,
    "ai_content":false,
    "manual_content":false,
    "proposicao_status":"em_edicao",
    "proposicao_conteudo_length":192
}
```

#### **Template Aplicado**:
- **Template ID**: 6 (Template de Moção)
- **Arquivo**: `private/templates/template_mocao_seeder.rtf`
- **Tamanho**: 922.718 bytes (template do banco)

#### **Variáveis Processadas** (15 variáveis detectadas):
```log
Variáveis encontradas:
- $rodape_texto
- $assinatura_padrao  
- $municipio, $ano, $mes, $dia, $mes_extenso
- ${numero_proposicao} → [AGUARDANDO PROTOCOLO]
- ${ementa} → "Editado pelo Parlamentar"
- ${texto} → Conteúdo da proposição
- ${justificativa}
- $autor_nome, $autor_cargo
- $assinatura_digital_info
- $qrcode_html
```

#### **Document Key Gerado**:
- **Key**: `8_1755736145_1a688633`
- **URL de Download**: `http://legisinc-app/proposicoes/8/onlyoffice/download?token=OHwxNzU1NzM2MTQ1&v=1755736145&_=1755736145`
- **Callback URL**: `http://legisinc-app/api/onlyoffice/callback/legislativo/8/8_1755736145_1a688633`

#### **Salvamento do Documento**:
```log
[2025-08-21 00:30:47] local.INFO: OnlyOffice callback received {
    "status":2,
    "url":"http://localhost:8080/cache/files/data/8_1755736145_1a688633_5856/output.docx/output.docx",
    "actions":[{"type":0,"userid":"6"}],
    "lastsave":"2025-08-21T00:30:34.000Z",
    "notmodified":false,
    "filetype":"docx"
}
```

#### **Conteúdo Extraído**:
```log
[2025-08-21 00:30:47] local.INFO: Conteúdo extraído do documento {
    "file_type":"docx",
    "content_length":453,
    "content_preview":"MOÇÃO Nº [AGUARDANDO PROTOCOLO]\n\nEMENTA: Editado pelo Parlamentar\n\nA Câmara Municipal manifes"
}
```

#### **Arquivo Salvo**:
- **Localização**: `proposicoes/proposicao_8_1755736247.docx`
- **Tamanho do conteúdo**: 453 caracteres
- **Status callback**: Sucesso (0.05s de processamento)

#### **Resultado**:
- ✅ Template de moção aplicado corretamente
- ✅ Documento editado pelo parlamentar
- ✅ Arquivo salvo em: `storage/app/proposicoes/proposicao_8_1755736247.docx`
- ✅ Conteúdo extraído e armazenado no banco

---

### **3. ENVIO PARA LEGISLATIVO**
**Ação**: Parlamentar clica em "Enviar para Legislativo"  
**Timestamp**: 2025-08-21 00:31:08  

```log
[2025-08-21 00:31:08] local.INFO: Status da proposição alterado {
    "id":8,
    "status_anterior":"em_edicao",
    "status_novo":"enviado_legislativo"
}
```

#### **Resultado**:
- ✅ Status: `em_edicao` → `enviado_legislativo`
- ✅ Proposição disponível para revisão do Legislativo

---

### **4. APROVAÇÃO PELO LEGISLATIVO**
**Ação**: Legislativo revisa e aprova a proposição  
**Timestamp**: 2025-08-21 00:32:19  

```log
[2025-08-21 00:32:19] local.INFO: Status da proposição alterado {
    "id":8,
    "status_anterior":"enviado_legislativo",
    "status_novo":"aprovado"
}
```

#### **Resultado**:
- ✅ Status: `enviado_legislativo` → `aprovado`
- ✅ Proposição aprovada e disponível para assinatura

---

### **5. 🚨 PROBLEMA: GERAÇÃO DE PDF PARA ASSINATURA**
**Rota**: `/proposicoes/8/assinar`  
**Timestamp**: 2025-08-21 00:33:41 - 00:33:42  

#### **Processo de Extração (2x executado)**:
```log
[2025-08-21 00:33:41] local.INFO: Iniciando extração avançada OnlyOffice para proposição 8
[2025-08-21 00:33:41] local.INFO: Extraindo conteúdo fiel do arquivo: /var/www/html/storage/app/private/proposicoes/proposicao_8_1755736247.docx
[2025-08-21 00:33:41] local.INFO: Extração avançada concluída {"palavras":69,"parágrafos":1,"tamanho_final":562}

# SEGUNDA EXECUÇÃO (DUPLICADA)
[2025-08-21 00:33:42] local.INFO: Iniciando extração avançada OnlyOffice para proposição 8
[2025-08-21 00:33:42] local.INFO: Extraindo conteúdo fiel do arquivo: /var/www/html/storage/app/private/proposicoes/proposicao_8_1755736247.docx
[2025-08-21 00:33:42] local.INFO: Extração avançada concluída {"palavras":69,"parágrafos":1,"tamanho_final":562}
```

#### **❌ PROBLEMAS IDENTIFICADOS**:

1. **Execução Duplicada**: O processo de extração roda 2 vezes consecutivas
2. **Limpeza de Duplicações**: Logs mostram limpeza de conteúdo duplicado
3. **Falta de logs de PDF**: Não há logs de geração/exibição do PDF final
4. **Possível problema de formatação**: Conteúdo pode estar sendo corrompido

---

## 🎯 ANÁLISE TÉCNICA DO PROBLEMA

### **Arquivo Correto Disponível**:
- ✅ **Localização**: `/var/www/html/storage/app/private/proposicoes/proposicao_8_1755736247.docx`
- ✅ **Conteúdo válido**: 453 caracteres de texto
- ✅ **Formato**: DOCX editado pelo OnlyOffice
- ✅ **Timestamp**: 2025-08-21 00:30:47

### **Processo de PDF**:
- ❓ **Controller**: `ProposicaoAssinaturaController`
- ❓ **Método**: Provavelmente `encontrarArquivoMaisRecente()`
- ❓ **Conversão**: DOCX → PDF (LibreOffice/OnlyOffice)
- ❓ **Exibição**: PDF embedado na view de assinatura

### **Possíveis Causas**:
1. **Cache de arquivo**: PDF sendo gerado de versão antiga
2. **Caminho incorreto**: Buscando arquivo em localização errada
3. **Processo de conversão**: DOCX → PDF com problemas
4. **Limpeza excessiva**: Conteúdo sendo alterado durante extração
5. **Template vs Arquivo**: Usando template em vez do arquivo editado

---

## 🔍 PRÓXIMOS PASSOS PARA RESOLUÇÃO

### **1. Investigar ProposicaoAssinaturaController**
- Verificar método `encontrarArquivoMaisRecente()`
- Analisar processo de conversão DOCX → PDF
- Validar caminhos de busca de arquivos

### **2. Verificar Geração de PDF**
- Confirmar se está usando arquivo correto: `proposicao_8_1755736247.docx`
- Validar processo de conversão
- Verificar se formatação está sendo preservada

### **3. Eliminar Execução Duplicada**
- Identificar por que extração roda 2 vezes
- Otimizar processo para execução única
- Adicionar logs específicos para PDF

### **4. Testar Fluxo Completo**
- Criar nova proposição de teste
- Seguir fluxo parlamentar → legislativo → assinatura
- Validar PDF gerado na tela de assinatura

---

## 📊 STATUS ATUAL

### **✅ FUNCIONANDO CORRETAMENTE**:
- Criação de proposições
- Aplicação de templates
- Edição no OnlyOffice (Parlamentar)
- Salvamento de arquivos DOCX
- Mudanças de status
- Fluxo parlamentar → legislativo

### **❌ PROBLEMAS IDENTIFICADOS**:
- **PDF na tela de assinatura não reflete edições do legislativo**
- **Execução duplicada do processo de extração**
- **Possível uso de template em vez de arquivo editado**

### **🎯 PRIORIDADE DE CORREÇÃO**:
1. **ALTA**: Corrigir geração de PDF para assinatura
2. **MÉDIA**: Eliminar execução duplicada de extração
3. **BAIXA**: Otimizar logs e performance

---

## 🏗️ ARQUITETURA ATUAL

### **Fluxo de Dados**:
```
Parlamentar → OnlyOffice → DOCX Salvo → Legislativo → Aprovação → PDF Assinatura
     ↓              ↓           ↓              ↓           ↓            ↓
   Banco      Callback    Storage/app    Revisão     Status      PROBLEMA
```

### **Arquivos Chave**:
- **Template**: `private/templates/template_mocao_seeder.rtf`
- **Documento Editado**: `proposicoes/proposicao_8_1755736247.docx`
- **Controller PDF**: `ProposicaoAssinaturaController.php`
- **Service**: `OnlyOfficeService.php`

---

**🚨 CONCLUSÃO**: O fluxo está funcionando perfeitamente até a aprovação. O problema está especificamente na geração/exibição do PDF na tela de assinatura, que não está refletindo o conteúdo correto do arquivo editado pelo legislativo.