# Criação de Modelos de Documentos - LegisInc

## Visão Geral

O sistema de modelos permite criar templates profissionais em Microsoft Word (.docx) que serão automaticamente preenchidos com dados das proposições. Este guia explica como criar documentos com variáveis dinâmicas.

## Formato das Variáveis

### Sintaxe Padrão
As variáveis devem ser inseridas no documento Word usando a sintaxe:
```
${nome_da_variavel}
```

**Exemplos:**
- `${numero_proposicao}` → "001/2024"
- `${autor_nome}` → "João Silva" 
- `${ementa}` → "Dispõe sobre..."

### Sintaxe Alternativa (Compatível)
Também é suportada a sintaxe com chaves duplas:
```
{{nome_da_variavel}}
```

## Variáveis Obrigatórias

### 🔴 **Essenciais** (Sempre incluir)
```
${numero_proposicao}    - Número da proposição (ex: 001/2024)
${tipo_proposicao}      - Tipo da proposição (ex: Projeto de Lei Ordinária)
${ementa}              - Ementa/resumo da proposição
${autor_nome}          - Nome completo do autor/parlamentar
```

### 🟡 **Recomendadas** (Alta utilidade)
```
${autor_cargo}         - Cargo do autor (ex: Vereador)
${data_criacao}        - Data de criação (ex: 20/07/2025)
${legislatura}         - Legislatura atual (ex: 2025)
${sessao_legislativa}  - Sessão legislativa (ex: 2025)
```

## Variáveis Disponíveis

### 📋 **Identificação da Proposição**
```
${numero_proposicao}     - Número oficial da proposição
${tipo_proposicao}       - Tipo da proposição legislativa
${ementa}               - Ementa/descrição da proposição
${justificativa}        - Justificativa da proposição
${artigos}              - Artigos da proposição
${vigencia}             - Data de vigência
```

### 👤 **Dados do Autor**
```
${autor_nome}           - Nome completo do parlamentar
${autor_cargo}          - Cargo (Vereador, Deputado, etc.)
${gabinete}             - Número do gabinete
${municipio}            - Município do parlamentar
${estado}               - Estado (sigla - ex: SP)
```

### 📅 **Datas e Período**
```
${data_criacao}         - Data de criação da proposição
${data_atual}           - Data atual do sistema
${legislatura}          - Ano da legislatura
${sessao_legislativa}   - Ano da sessão legislativa
```

## Exemplos de Modelos

### 📄 **Modelo Básico - Projeto de Lei**

```docx
PROJETO DE LEI N° ${numero_proposicao}

Autor: ${autor_nome} - ${autor_cargo}
Data: ${data_criacao}

EMENTA: ${ementa}

A Câmara Municipal de ${municipio}, Estado de ${estado}, 
no uso de suas atribuições legais, APROVA:

Art. 1° - [Conteúdo do artigo]

Art. 2° - Esta Lei entra em vigor na data de sua publicação.

${municipio}, ${data_criacao}

${autor_nome}
${autor_cargo}
```

### 📋 **Modelo Requerimento**

```docx
REQUERIMENTO N° ${numero_proposicao}

Senhor Presidente,

${autor_nome}, ${autor_cargo}, vem respeitosamente requerer 
a Vossa Excelência que seja solicitado ao Poder Executivo 
Municipal informações sobre:

${ementa}

JUSTIFICATIVA:
${justificativa}

Termo em que,
Pede Deferimento.

Gabinete ${gabinete}, ${data_criacao}

${autor_nome}
${autor_cargo}
```

### 🏛️ **Modelo Indicação**

```docx
INDICAÇÃO N° ${numero_proposicao}

Excelentíssimo Senhor Prefeito,

${autor_nome}, ${autor_cargo} da Câmara Municipal de ${municipio}, 
no uso de suas prerrogativas regimentais, vem respeitosamente 
INDICAR a Vossa Excelência:

${ementa}

A presente indicação visa ${justificativa}

${municipio} - ${estado}, ${data_criacao}

${autor_nome}
${autor_cargo}
Gabinete ${gabinete}
```

## Boas Práticas

### ✅ **Formatação Recomendada**

1. **Use estilos do Word** para manter consistência
2. **Aplique formatação** nas variáveis (negrito, itálico, etc.)
3. **Configure margens** e espaçamento adequados
4. **Use quebras de página** quando necessário
5. **Inclua cabeçalho/rodapé** se aplicável

### 🎨 **Exemplo de Formatação**

```
Título: Arial 16pt, Negrito, Centralizado
Corpo: Times New Roman 12pt, Justificado
Variáveis: Podem ter formatação específica
Assinatura: Arial 11pt, Direita
```

### 🔍 **Validação de Variáveis**

**Antes de fazer upload, verifique:**
- ✅ Todas as variáveis usam a sintaxe correta `${variavel}`
- ✅ Nomes das variáveis estão corretos (sem espaços)
- ✅ Variáveis obrigatórias estão incluídas
- ✅ Não há caracteres especiais nos nomes das variáveis

## Processo de Criação

### 1️⃣ **Criar o Documento**
1. Abra Microsoft Word
2. Configure o layout (margens, fonte, etc.)
3. Digite o conteúdo do modelo
4. Insira as variáveis com sintaxe `${variavel}`
5. Aplique formatação desejada
6. Salve como arquivo .docx

### 2️⃣ **Upload no Sistema**
1. Acesse `/admin/documentos/modelos/create`
2. Preencha nome e descrição do modelo
3. Selecione tipo de proposição (opcional)
4. Faça upload do arquivo .docx
5. Verifique variáveis detectadas automaticamente
6. Salve o modelo

### 3️⃣ **Teste e Validação**
1. Crie uma proposição de teste
2. Gere documento usando o modelo
3. Verifique se todas as variáveis foram substituídas
4. Ajuste o modelo se necessário

## Variáveis por Tipo de Proposição

### 📜 **Projeto de Lei**
**Obrigatórias:** `numero_proposicao`, `tipo_proposicao`, `ementa`, `autor_nome`, `artigos`
**Recomendadas:** `justificativa`, `vigencia`, `municipio`, `data_criacao`

### 📝 **Requerimento**
**Obrigatórias:** `numero_proposicao`, `autor_nome`, `autor_cargo`, `ementa`
**Recomendadas:** `justificativa`, `gabinete`, `data_criacao`

### 📋 **Indicação**
**Obrigatórias:** `numero_proposicao`, `autor_nome`, `autor_cargo`, `ementa`
**Recomendadas:** `justificativa`, `municipio`, `estado`, `gabinete`

### 🏛️ **Moção**
**Obrigatórias:** `numero_proposicao`, `autor_nome`, `ementa`
**Recomendadas:** `data_criacao`, `municipio`, `autor_cargo`

## Solução de Problemas

### ❌ **Variável não substituída**
- Verifique sintaxe: `${variavel}` (com chaves e cifrão)
- Confirme nome da variável na lista disponível
- Certifique-se que não há espaços ou caracteres especiais

### ❌ **Formatação perdida**
- Use formatação direta nas variáveis no Word
- Evite formatação condicional complexa
- Teste com dados reais antes de finalizar

### ❌ **Arquivo não aceito**
- Salve como .docx (não .doc ou .pdf)
- Tamanho máximo: 10MB
- Verifique se não há macros ou conteúdo restrito

## Dicas Avançadas

### 🎯 **Modelos Reutilizáveis**
- Crie modelos genéricos sem tipo específico
- Use variáveis condicionais quando possível
- Mantenha estrutura simples e clara

### 🔄 **Versionamento**
- O sistema mantém versões automaticamente
- Documente mudanças significativas
- Teste nova versão antes de ativar

### 📊 **Relatórios**
- Monitore uso dos modelos
- Colete feedback dos usuários
- Ajuste baseado em estatísticas de uso

---

## ✨ Resultado Final

Seguindo este guia, você criará modelos profissionais que:
- ✅ **Automatizam** o preenchimento de documentos
- ✅ **Mantêm consistência** na formatação
- ✅ **Reduzem erros** de digitação
- ✅ **Aceleram** o processo legislativo
- ✅ **Padronizam** a documentação oficial