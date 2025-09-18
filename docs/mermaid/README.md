# 🧭 Diagramas Mermaid - Sistema LegisInc

Esta pasta contém os diagramas Mermaid individuais do sistema LegisInc, organizados para fácil acesso e edição no **Mermaid Live Editor**.

## 📋 Diagramas Disponíveis

### 🏗️ 01. Arquitetura Geral
**Arquivo:** `01-arquitetura-geral.mmd`
**Descrição:** Visão completa da arquitetura do sistema com gateway, múltiplos backends, serviços e integrações.
**Tipo:** `graph TB`

### 📊 02. Fluxo de Proposições - Estados
**Arquivo:** `02-fluxo-proposicoes-estados.mmd`
**Descrição:** Estados e transições das proposições no ciclo de vida legislativo.
**Tipo:** `stateDiagram-v2`

### 🔄 03. Fluxo de Proposições - Completo
**Arquivo:** `03-fluxo-proposicoes-completo.mmd`
**Descrição:** Processo completo desde criação até protocolação, incluindo validações e aprovações.
**Tipo:** `graph TB`

### 🔏 04. Assinatura Digital - PyHanko
**Arquivo:** `04-assinatura-digital-pyhanko.mmd`
**Descrição:** Processo de assinatura digital com certificados PFX usando container PyHanko.
**Tipo:** `graph TD`

### 📄 05. Fluxo de Documento - Completo
**Arquivo:** `05-fluxo-documento-completo.mmd`
**Descrição:** Fluxo completo incluindo todas as operações de banco de dados e services.
**Tipo:** `graph TB`

## 🎨 Como Usar

### Opção 1: Mermaid Live Editor Local
1. Acesse: `http://localhost:8083`
2. Copie o conteúdo de qualquer arquivo `.mmd`
3. Cole no editor para visualização interativa

### Opção 2: Via Sistema Admin
1. Acesse: `/admin/system-diagram`
2. Clique no botão **"Mermaid"** em qualquer diagrama
3. O código será carregado automaticamente no editor local

### Opção 3: URLs Diretas
Os arquivos podem ser acessados diretamente pelo container:
- `http://localhost:8083/diagrams/01-arquitetura-geral.mmd`
- `http://localhost:8083/diagrams/02-fluxo-proposicoes-estados.mmd`
- etc.

## 🔧 Recursos do Editor

- ✅ **Edição em tempo real** - Visualize mudanças instantaneamente
- ✅ **Export múltiplos formatos** - PNG, SVG, PDF
- ✅ **Zoom e pan** - Navegação fácil em diagramas grandes
- ✅ **Tema customizável** - Dark/Light mode
- ✅ **Compartilhamento** - URLs com código embarcado

## 📁 Estrutura dos Arquivos

```
docs/mermaid/
├── README.md                           # Este arquivo
├── 01-arquitetura-geral.mmd           # Arquitetura completa
├── 02-fluxo-proposicoes-estados.mmd   # Estados das proposições
├── 03-fluxo-proposicoes-completo.mmd  # Fluxo completo de proposições
├── 04-assinatura-digital-pyhanko.mmd  # Processo de assinatura digital
└── 05-fluxo-documento-completo.mmd    # Fluxo com operações de DB
```

## 🌐 Links Úteis

- **Container Local:** http://localhost:8083
- **Sistema Admin:** /admin/system-diagram
- **Documentação Completa:** /docs/project-overview.md
- **Editor Online:** https://mermaid.live (alternativo)

---

**Última atualização:** 18/09/2025
**Versão dos diagramas:** v2.2 - Multi-Backend Architecture