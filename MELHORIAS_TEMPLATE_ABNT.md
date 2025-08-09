# Melhorias no Sistema de Templates - Padrão ABNT

## Visão Geral

Este documento descreve as melhorias implementadas no sistema de criação de proposições, com foco na padronização conforme as normas ABNT NBR 14724:2023 e NBR 6022:2018, seguindo as melhores práticas de redação legislativa.

## Principais Melhorias Implementadas

### 1. Template Único Padronizado ABNT

**Arquivo:** `/storage/templates/template_padrao_abnt.html`

#### Características:
- **Fonte:** Times New Roman (recomendada para impressão) com opção Arial
- **Corpo principal:** 12pt com espaçamento 1,5
- **Elementos secundários:** 10pt (citações, notas, legendas)
- **Margens:** 3cm (superior/esquerda), 2cm (inferior/direita)
- **Estrutura:** Cabeçalho, epígrafe, ementa, preâmbulo, articulado, justificativa, assinatura

#### Benefícios:
- Conformidade total com normas ABNT
- Uniformidade visual entre documentos
- Acessibilidade aprimorada
- Compatibilidade impressão/digital

### 2. Serviço de Validação ABNT Automática

**Arquivo:** `app/Services/Template/ABNTValidationService.php`

#### Funcionalidades:
- **Validação tipográfica:** Fonte, tamanhos, espaçamentos
- **Estrutura HTML:** DOCTYPE, charset, idioma
- **Margens e layout:** Conformidade com padrões ABNT
- **Acessibilidade:** Contraste, estrutura semântica
- **Score de conformidade:** Avaliação percentual (0-100%)
- **Correções automáticas:** Ajustes simples automatizados

#### Categorias de Validação:
1. **Estrutura HTML** - DOCTYPE, charset UTF-8, lang="pt-BR"
2. **Tipografia** - Fontes, tamanhos, hierarquia
3. **Espaçamentos** - Line-height, margens, padding
4. **Margens** - Conformidade ABNT (3cm-2cm-2cm-3cm)
5. **Acessibilidade** - Contraste, semântica
6. **Estrutura Legislativa** - Elementos obrigatórios

### 3. Serviço de Processamento de Template Padrão

**Arquivo:** `app/Services/Template/TemplatePadraoABNTService.php`

#### Recursos:
- **Processamento automático de variáveis**
- **Integração com parâmetros do sistema**
- **Formatação automática de articulado**
- **Geração de metadados**
- **Validação integrada**
- **Estatísticas de uso**

#### Variáveis Suportadas:
- **Proposição:** tipo, número, ementa, texto, status
- **Autor:** nome, cargo, partido, email
- **Datas:** atual, por extenso, criação
- **Instituição:** câmara, município, legislatura
- **Imagens:** cabeçalho institucional

### 4. IA Aprimorada com Diretrizes ABNT

**Arquivo:** `app/Services/AI/AITextGenerationService.php`

#### Melhorias no Prompt:
- **Normas tipográficas:** Diretrizes específicas ABNT
- **Estrutura legislativa:** Epígrafe, preâmbulo, articulado
- **Técnica legislativa:** Numeração, hierarquia, linguagem
- **Conformidade legal:** Competências, hierarquia normativa
- **Qualidade textual:** Clareza, objetividade, coesão
- **Acessibilidade:** Linguagem inclusiva, compreensibilidade

### 5. Controller de Validação ABNT

**Arquivo:** `app/Http/Controllers/Template/ABNTValidationController.php`

#### Endpoints API:
- `POST /proposicoes/abnt/validar` - Validar documento
- `POST /proposicoes/abnt/corrigir` - Aplicar correções automáticas
- `GET /proposicoes/abnt/estatisticas` - Estatísticas do template
- `POST /proposicoes/abnt/relatorio` - Relatório detalhado
- `GET /proposicoes/abnt/painel` - Interface de validação

### 6. Integração com Sistema Existente

**Arquivo:** `app/Http/Controllers/ProposicaoController.php`

#### Modificações:
- **buscarModelos()** - Template ABNT como primeira opção
- **preencherModelo()** - Suporte ao template padrão
- **processarTemplatePadraoABNT()** - Novo método de processamento
- **Campos adicionais:** validacao_abnt, template_usado

## Fluxo de Uso

### 1. Criação de Proposição
1. Usuário acessa `/proposicoes/criar`
2. Seleciona tipo de proposição
3. Preenche ementa
4. Opcionalmente usa IA para gerar texto (com diretrizes ABNT)
5. Salva rascunho

### 2. Seleção de Template
1. Sistema lista templates disponíveis
2. **Template Padrão ABNT** aparece como primeira opção (recomendada)
3. Usuário seleciona template desejado
4. Redirecionado para preenchimento

### 3. Processamento ABNT
1. Sistema processa dados da proposição
2. Aplica template padrão ABNT
3. Substitui variáveis automaticamente
4. Executa validação ABNT completa
5. Aplica correções automáticas se necessário
6. Salva documento final

### 4. Resultado
1. Documento gerado com conformidade ABNT
2. Score de qualidade exibido
3. Relatório de validação disponível
4. Documento pronto para edição ou finalização

## Benefícios das Melhorias

### Para Usuários:
- **Simplicidade:** Um template único para todos os tipos
- **Qualidade:** Documentos sempre conformes às normas
- **Produtividade:** Geração automática com IA aprimorada
- **Confiabilidade:** Validação automática de qualidade

### Para Administradores:
- **Padronização:** Uniformidade em todos os documentos
- **Manutenção:** Um template central para manter
- **Auditoria:** Relatórios de conformidade ABNT
- **Flexibilidade:** Sistema adaptável a novas normas

### Para o Sistema:
- **Performance:** Menos templates para processar
- **Consistência:** Regras centralizadas
- **Evolução:** Fácil atualização de normas
- **Integração:** APIs para validação externa

## Configurações e Parâmetros

### Parâmetros do Sistema:
- **Templates > Cabeçalho > cabecalho_imagem:** Logo institucional
- **Dados Gerais > Informações da Câmara:** Nome, endereço, município
- **Dados Gerais > Legislatura:** Legislatura atual, sessão legislativa
- **Configuração de IA:** Provider, modelo, parâmetros

### Variáveis de Ambiente:
```env
# Template ABNT
TEMPLATE_ABNT_FONT_FAMILY=Times New Roman
TEMPLATE_ABNT_VALIDATION=true

# IA com diretrizes ABNT
AI_ABNT_GUIDELINES=true
AI_CUSTOM_PROMPT_ABNT=true
```

## Monitoramento e Métricas

### Métricas Disponíveis:
- **Score médio ABNT** por tipo de proposição
- **Taxa de conformidade** por usuário
- **Problemas mais comuns** identificados
- **Uso do template padrão** vs templates específicos
- **Efetividade das correções automáticas**

### Logs e Auditoria:
- Validações ABNT executadas
- Correções aplicadas automaticamente
- Templates utilizados por proposição
- Scores de qualidade ao longo do tempo

## Próximos Passos

### Melhorias Futuras:
1. **Interface visual** para validação ABNT
2. **Relatórios gerenciais** de conformidade
3. **Templates especializados** por tipo específico
4. **Integração com editores** externos (Word, LibreOffice)
5. **Validação em tempo real** durante edição
6. **Treinamento de IA** com base histórica
7. **Exportação em múltiplos formatos** (PDF/A, DOCX, ODT)

### Manutenção:
- Revisão anual das normas ABNT
- Atualização de diretrizes legislativas
- Monitoramento de performance
- Feedback dos usuários
- Correção de bugs reportados

## Conclusão

As melhorias implementadas transformam o sistema de criação de proposições em uma ferramenta moderna, padronizada e eficiente, que garante conformidade com as normas ABNT e melhores práticas legislativas, proporcionando documentos de alta qualidade de forma automática e consistente.