# FLUXO DE PROPOSIÇÕES - CAMPOS POR PERFIL

## 🔄 VISÃO GERAL DO PROCESSO

```
PARLAMENTAR → LEGISLATIVO → PARLAMENTAR → PROTOCOLO → VOTAÇÃO
   (Cria)     (Revisa)      (Assina)     (Protocola)  (Tramita)
```

## 📋 ETAPA 1: PARLAMENTAR - CRIAÇÃO DA PROPOSIÇÃO

### Campos de Entrada:
- **Tipo de Proposição*** (obrigatório)
  - Dropdown: PL, PLP, PEC, PDC, PRC, Moção, Indicação, Requerimento
- **Ementa*** (obrigatório)
  - Textarea: Resumo claro do objeto da proposição
- **Seleciona Modelo*** (obrigatório)
  - Dropdown dinâmico baseado no tipo de proposição selecionado

### Campos Gerados Automaticamente:
- **Autor**: Parlamentar logado
- **Data de Criação**: Timestamp atual
- **Status**: "Em Elaboração"
- **Número Temporário**: Para referência interna

### Fluxo da Interface:

1. **Tela Inicial - Dados Básicos**
   ```
   [ Tipo de Proposição ▼ ]
   [ Ementa (texto) ]
   [ Selecionar Modelo ▼ ]
   
   [Salvar Rascunho] [Continuar →]
   ```

2. **Tela de Preenchimento do Modelo**
   ```
   Modelo: [Nome do modelo selecionado]
   
   [Campos dinâmicos baseados no modelo]
   - Objetivo
   - Justificativa
   - Artigos propostos
   - Vigência
   
   [← Voltar] [Salvar] [Gerar Texto Editável]
   ```

3. **Tela de Edição Final**
   ```
   [Editor de texto WYSIWYG com o documento gerado]
   
   Status: Em Elaboração
   
   [Salvar] [Enviar para Legislativo]
   ```

### Estados dos Botões:
- **Gerar Texto Editável**: Habilitado após salvar modelo preenchido
- **Enviar para Legislativo**: Habilitado após gerar texto editável

---

## 🏛️ ETAPA 2: LEGISLATIVO - REVISÃO TÉCNICA

### Campos Disponíveis para Visualização:
- **Dados da Proposição** (somente leitura)
  - Tipo, Ementa, Autor, Data
- **Texto Completo** (somente leitura)
- **Análise Técnica** (editável pelo Legislativo)
  - Constitucionalidade
  - Juridicidade  
  - Regimentalidade
  - Técnica Legislativa

### Campos para Preenchimento:
- **Parecer Técnico*** (obrigatório para devolver)
  - Textarea: Observações e correções necessárias
- **Tipo de Retorno** (obrigatório)
  - Radio: "Aprovado para Assinatura" | "Devolver para Correção"
- **Observações Internas** (opcional)
  - Textarea: Notas para arquivo

### Fluxo da Interface:

1. **Tela de Revisão**
   ```
   === DADOS DA PROPOSIÇÃO ===
   Tipo: [PL]  Autor: [Nome]  Data: [dd/mm/aaaa]
   Ementa: [Texto da ementa]
   
   === TEXTO DA PROPOSIÇÃO ===
   [Visualizador do documento completo]
   
   === ANÁLISE TÉCNICA ===
   ☐ Constitucionalidade: [ ] Aprovado [ ] Reprovado
   ☐ Juridicidade: [ ] Aprovado [ ] Reprovado  
   ☐ Regimentalidade: [ ] Aprovado [ ] Reprovado
   ☐ Técnica Legislativa: [ ] Aprovado [ ] Reprovado
   
   [ Parecer Técnico (obrigatório) ]
   
   Ação:
   ( ) Aprovar para Assinatura
   ( ) Devolver para Correção
   
   [Observações Internas]
   
   [Salvar Análise] [Processar Decisão]
   ```

### Estados dos Botões:
- **Devolver para Parlamentar**: Habilitado se "Devolver para Correção"
- **Aprovar para Assinatura**: Habilitado se "Aprovado para Assinatura"

---

## ✍️ ETAPA 3: PARLAMENTAR - ASSINATURA

### Campos Disponíveis:
- **Documento Revisado** (somente leitura)
- **Parecer do Legislativo** (somente leitura)
- **Correções Realizadas** (se houver)

### Campos de Ação:
- **Aceitar Correções** (se documento foi devolvido)
- **Assinatura Digital*** (obrigatório)
  - Certificado digital ou biometria
- **Confirmação Final** (checkbox obrigatório)

### Fluxo da Interface:

**Se documento aprovado pelo Legislativo:**
```
=== DOCUMENTO APROVADO PARA ASSINATURA ===
Status: Aprovado pelo Legislativo
Parecer: [Texto do parecer técnico]

[Visualizador do documento final]

☐ Confirmo que li e concordo com o texto final
[Assinar Digitalmente] [Enviar para Protocolo]
```

**Se documento devolvido para correção:**
```
=== DOCUMENTO DEVOLVIDO PARA CORREÇÃO ===
Status: Devolvido pelo Legislativo
Motivo: [Parecer técnico com correções]

[Editor para correções]

[Salvar Correções] [Reenviar para Legislativo]
```

### Estados dos Botões:
- **Assinar Digitalmente**: Habilitado após confirmar leitura
- **Enviar para Protocolo**: Habilitado após assinatura digital

---

## 📋 ETAPA 4: PROTOCOLO - PROTOCOLAÇÃO

### Campos Automáticos:
- **Número de Protocolo**: Gerado automaticamente
- **Data de Protocolo**: Timestamp atual
- **Funcionário Responsável**: Usuário logado (perfil LEGISLATIVO)

### Campos para Preenchimento:
- **Verificação de Documentos*** (obrigatório)
  - Checklist automático de validações
- **Observações do Protocolo** (opcional)
- **Comissões de Destino*** (obrigatório)
  - Multi-select baseado no tipo e assunto da proposição

### Fluxo da Interface:

```
=== PROTOCOLAÇÃO DE PROPOSIÇÃO ===

Proposição: [Tipo] nº [temporário] - [Ementa]
Autor: [Nome do Parlamentar]
Data de Envio: [dd/mm/aaaa]

=== VERIFICAÇÕES AUTOMÁTICAS ===
✓ Documento assinado digitalmente
✓ Texto completo presente
✓ Formato adequado
✓ Metadados completos

=== DADOS DO PROTOCOLO ===
Número: [Gerado automaticamente]
Data: [dd/mm/aaaa hh:mm]
Responsável: [Nome do usuário]

=== DISTRIBUIÇÃO ===
☐ Comissão de Constituição e Justiça (obrigatório)
☐ Comissão de [baseado no assunto]
☐ Comissão de Finanças (se impacto orçamentário)

[Observações do Protocolo]

[Protocolar e Distribuir]
```

### Estados dos Botões:
- **Protocolar**: Habilitado após verificações automáticas

---

## 🗃️ ESTRUTURA DE DADOS POR ETAPA

### Status da Proposição:
```
enum StatusProposicao {
  RASCUNHO = 'rascunho',
  EM_ELABORACAO = 'em_elaboracao', 
  ENVIADO_LEGISLATIVO = 'enviado_legislativo',
  EM_REVISAO = 'em_revisao',
  DEVOLVIDO_CORRECAO = 'devolvido_correcao',
  APROVADO_ASSINATURA = 'aprovado_assinatura',
  ASSINADO = 'assinado',
  ENVIADO_PROTOCOLO = 'enviado_protocolo',
  PROTOCOLADO = 'protocolado',
  EM_TRAMITACAO = 'em_tramitacao'
}
```

### Campos por Etapa:

**Parlamentar (Criação):**
```json
{
  "tipo_proposicao": "string",
  "ementa": "text", 
  "modelo_selecionado": "string",
  "conteudo_modelo": "json",
  "texto_gerado": "text",
  "status": "enum"
}
```

**Legislativo (Revisão):**
```json
{
  "analise_constitucionalidade": "boolean",
  "analise_juridicidade": "boolean", 
  "analise_regimentalidade": "boolean",
  "analise_tecnica_legislativa": "boolean",
  "parecer_tecnico": "text",
  "tipo_retorno": "enum",
  "observacoes_internas": "text",
  "revisor_id": "integer",
  "data_revisao": "timestamp"
}
```

**Parlamentar (Assinatura):**
```json
{
  "assinatura_digital": "string",
  "certificado_digital": "string",
  "data_assinatura": "timestamp",
  "ip_assinatura": "string",
  "confirmacao_leitura": "boolean"
}
```

**Protocolo:**
```json
{
  "numero_protocolo": "string",
  "data_protocolo": "timestamp", 
  "funcionario_protocolo_id": "integer",
  "comissoes_destino": "array",
  "observacoes_protocolo": "text",
  "verificacoes_realizadas": "json"
}
```

---

## 🔧 IMPLEMENTAÇÃO NO SISTEMA

### Rotas Necessárias:

**Parlamentar:**
- `GET /proposicoes/criar` - Tela inicial
- `POST /proposicoes/salvar-rascunho` - Salvar dados básicos
- `GET /proposicoes/modelos/{tipo}` - Buscar modelos
- `POST /proposicoes/gerar-texto` - Gerar documento
- `PUT /proposicoes/{id}/enviar-legislativo` - Enviar para revisão

**Legislativo:**
- `GET /proposicoes/revisar` - Lista para revisão
- `GET /proposicoes/{id}/revisar` - Tela de revisão
- `POST /proposicoes/{id}/parecer` - Salvar parecer
- `PUT /proposicoes/{id}/aprovar` - Aprovar para assinatura
- `PUT /proposicoes/{id}/devolver` - Devolver para correção

**Protocolo:**
- `GET /proposicoes/protocolar` - Lista para protocolo
- `GET /proposicoes/{id}/protocolar` - Tela de protocolo
- `POST /proposicoes/{id}/protocolar` - Efetivar protocolo

### Permissões por Perfil:

```php
// Permissions
PARLAMENTAR: ['proposicoes.create', 'proposicoes.edit_own', 'proposicoes.sign']
LEGISLATIVO: ['proposicoes.review', 'proposicoes.protocol', 'proposicoes.view_all']  
ADMIN: ['proposicoes.*']
```

---

## 📊 DASHBOARD POR PERFIL

### Parlamentar:
- Minhas Proposições em Elaboração
- Proposições Devolvidas para Correção
- Proposições Aguardando Assinatura
- Histórico de Proposições

### Legislativo:
- Proposições Aguardando Revisão
- Proposições em Análise (minhas)
- Proposições Aguardando Protocolo
- Relatório de Produtividade

### Protocolo:
- Proposições para Protocolar
- Protocolos Realizados Hoje
- Estatísticas de Protocolação

Quer que eu detalhe alguma etapa específica ou implemente algum componente no sistema?