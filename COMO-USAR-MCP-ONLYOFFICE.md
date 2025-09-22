# Como Usar o MCP OnlyOffice DocumentServer no Claude Code

Este guia explica como usar o MCP (Model Context Protocol) do OnlyOffice DocumentServer que foi configurado no projeto LegisInc.

## 🎯 O que é o MCP OnlyOffice?

O MCP OnlyOffice é uma integração personalizada que permite ao Claude Code interagir diretamente com o OnlyOffice DocumentServer para:
- Converter documentos entre diferentes formatos
- Extrair informações de documentos
- Verificar o status do servidor OnlyOffice
- Automatizar tarefas de processamento de documentos

## 🚀 Ativando o MCP

### 1. Verificar se está configurado
```bash
claude mcp list
```

Se o MCP não aparecer, reinicie o Claude Code ou verifique se o arquivo `.mcp.json` está correto.

### 2. Verificar status de conexão
No Claude Code, digite:
```
/mcp
```

Você deve ver `onlyoffice-documentserver` como "Connected".

## 📝 Usando as Ferramentas

### 1. **Health Check** - Verificar status do OnlyOffice

**Comando no Claude Code:**
```
Use a ferramenta health_check do MCP OnlyOffice para verificar se o servidor está funcionando
```

**Resultado esperado:**
```json
{
  "status": "healthy",
  "baseUrl": "http://localhost:8080",
  "response": true,
  "timestamp": "2025-09-21T20:51:48.456Z"
}
```

### 2. **Get Document Info** - Informações do documento

**Comando no Claude Code:**
```
Use get_document_info para analisar o arquivo /caminho/para/documento.pdf
```

**Exemplo prático:**
```
Analise o documento em /home/bruno/legisinc-v2/storage/app/public/proposicoes/proposicao_1.rtf
```

**Resultado esperado:**
```json
{
  "fileName": "proposicao_1.rtf",
  "filePath": "/home/bruno/legisinc-v2/storage/app/public/proposicoes/proposicao_1.rtf",
  "fileSize": 15420,
  "fileExtension": ".rtf",
  "lastModified": "2025-09-21T18:30:15.123Z",
  "supportedConversions": ["pdf", "docx", "odt", "txt"]
}
```

### 3. **Convert Document** - Converter documentos

**Comando no Claude Code:**
```
Converta o documento X para formato Y usando o MCP OnlyOffice
```

**Exemplos práticos:**

#### Converter RTF para PDF (caso comum no LegisInc)
```
Converta /home/bruno/legisinc-v2/storage/app/public/proposicoes/proposicao_1.rtf para PDF, salvando em /tmp/proposicao_1.pdf
```

#### Converter DOCX para ODT
```
Use convert_document para converter /path/to/documento.docx para odt, salvando em /path/to/documento.odt
```

**Resultado esperado:**
```
Document successfully converted from .rtf to pdf
Output saved to: /tmp/proposicao_1.pdf
```

## 🎯 Casos de Uso Específicos do LegisInc

### 1. **Processar Proposições RTF**
```
Analise todos os arquivos RTF na pasta /home/bruno/legisinc-v2/storage/app/public/proposicoes/ e converta-os para PDF
```

### 2. **Verificar Status do OnlyOffice antes de Operações**
```
Antes de processar documentos, verifique se o OnlyOffice está funcionando usando health_check
```

### 3. **Extrair Metadados de Documentos em Lote**
```
Use get_document_info para analisar todos os arquivos .rtf na pasta de proposições e me dê um relatório
```

### 4. **Conversão Automática RTF → PDF**
```
Para cada arquivo RTF encontrado em storage/app/public/proposicoes/, converta para PDF na mesma pasta
```

## 🔧 Comandos Úteis do Claude Code

### Listar ferramentas disponíveis
```
@onlyoffice-documentserver
```

### Ver recursos disponíveis
```
/resources
```

### Verificar logs do MCP
```
/mcp logs onlyoffice-documentserver
```

## 📊 Formatos Suportados

### Documentos de Texto
- **RTF** → PDF, DOCX, ODT, TXT ✅ **(Mais usado no LegisInc)**
- **DOCX** → PDF, RTF, ODT, TXT
- **ODT** → PDF, DOCX, RTF, TXT
- **TXT** → PDF, DOCX, RTF, ODT

### Planilhas
- **XLSX** → PDF, ODS, CSV
- **XLS** → PDF, XLSX, ODS, CSV
- **CSV** → PDF, XLSX, ODS

### Apresentações
- **PPTX** → PDF, ODP
- **PPT** → PDF, PPTX, ODP

## 🛠️ Troubleshooting

### MCP não aparece na lista
```bash
# Verificar se o arquivo .mcp.json existe
cat /home/bruno/legisinc-v2/.mcp.json

# Recompilar o MCP se necessário
cd /home/bruno/legisinc-v2/mcp-onlyoffice
npm run build
```

### OnlyOffice não responde
```bash
# Verificar se o container está rodando
docker ps | grep onlyoffice

# Verificar logs
docker logs legisinc-onlyoffice

# Testar conectividade
curl http://localhost:8080/healthcheck
```

### Conversão falha
```bash
# Verificar logs do OnlyOffice
docker exec legisinc-onlyoffice tail -f /var/log/onlyoffice/documentserver/converter/out.log

# Verificar se o arquivo de entrada existe e tem permissões corretas
ls -la /caminho/para/arquivo
```

## 💡 Dicas de Uso

### 1. **Sempre verificar saúde primeiro**
Antes de converter muitos documentos, use `health_check` para garantir que o OnlyOffice está funcionando.

### 2. **Usar caminhos absolutos**
Sempre use caminhos completos nos comandos:
```
/home/bruno/legisinc-v2/storage/app/public/proposicoes/arquivo.rtf
```

### 3. **Verificar espaço em disco**
Para conversões em lote, verifique se há espaço suficiente:
```bash
df -h /home/bruno/legisinc-v2/storage/
```

### 4. **Processar em lotes pequenos**
Para muitos arquivos, processe em grupos de 10-20 documentos por vez.

## 📋 Exemplos Completos

### Exemplo 1: Workflow Completo de Proposição
```
1. Primeiro, verifique se o OnlyOffice está funcionando
2. Analise o arquivo RTF da proposição em storage/app/public/proposicoes/
3. Converta para PDF na pasta storage/app/public/pdfs/
4. Verifique se a conversão foi bem-sucedida
```

### Exemplo 2: Auditoria de Documentos
```
1. Use health_check para verificar o servidor
2. Liste todos os RTF em storage/app/public/proposicoes/
3. Para cada arquivo, use get_document_info
4. Gere um relatório com tamanhos, datas e formatos suportados
```

### Exemplo 3: Migração de Formato
```
1. Encontre todos os arquivos .doc antigos
2. Converta para .docx moderno
3. Depois converta para PDF para arquivo
4. Verifique integridade dos arquivos convertidos
```

## 🔗 Integração com Sistema LegisInc

O MCP pode ser usado para automatizar partes do fluxo documentado em `FLUXO-DOCUMENTO-ONLYOFFICE-PDF.md`:

- **Etapa de Conversão**: Automatizar a conversão RTF → PDF após aprovação
- **Verificação de Qualidade**: Validar documentos antes do envio
- **Backup e Arquivo**: Gerar versões PDF para arquivo histórico
- **Relatórios**: Extrair metadados para relatórios de produtividade

O MCP funciona como uma ponte entre o Claude Code e o OnlyOffice, permitindo automação inteligente do processamento de documentos no sistema LegisInc.