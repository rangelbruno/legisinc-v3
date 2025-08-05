
# Sistema de Busca Inteligente de Tipos de Proposição

## Visão Geral

O sistema de busca inteligente permite encontrar e cadastrar tipos de proposição usando abreviações comuns, siglas e nomes completos. O sistema reconhece automaticamente variações e sugere configurações pré-definidas.

## Como Funciona

### 1. Campo de Busca
No formulário de criação (`/admin/tipo-proposicoes/create`), há um campo de busca que aceita:
- Siglas: PL, PEC, PDL, PLC, REQ, IND, MOC
- Nomes parciais: "lei", "emenda", "moção"
- Nomes completos: "Projeto de Lei Ordinária"

### 2. Resultados
Os resultados mostram:
- **Badge Verde (Cadastrado)**: Tipo já existe no banco de dados
- **Badge Azul (Sugestão)**: Tipo sugerido com configurações padrão

### 3. Auto-preenchimento
Ao clicar em uma sugestão, todos os campos são preenchidos automaticamente:
- Nome completo
- Código único
- Ícone apropriado
- Cor do tema
- Ordem de exibição
- Configurações em JSON

## Tipos Reconhecidos

### Normativas (criam norma)
- **PEC/PELOM**: Proposta de Emenda à Constituição/Lei Orgânica
- **PL**: Projeto de Lei Ordinária
- **PLC/PLP**: Projeto de Lei Complementar
- **PLD**: Projeto de Lei Delegada
- **MP**: Medida Provisória
- **PCL**: Projeto de Consolidação das Leis
- **PDL/PDC**: Projeto de Decreto Legislativo
- **PR**: Projeto de Resolução

### Processuais/Acessórias
- **REQ**: Requerimento (+ de 20 subespécies)
- **IND**: Indicação
- **MOC**: Moção (Aplauso, Pesar, Repúdio, etc.)
- **EME**: Emenda (supressiva, aditiva, etc.)
- **SUB**: Subemenda
- **Substitutivo**: Texto alternativo global

### Outras
- **PAR**: Parecer de Comissão
- **REL**: Relatório
- **REC**: Recurso
- **VETO**: Veto (total ou parcial)
- **Destaque**: Votação separada
- **OFI**: Ofício
- **MSG**: Mensagem do Executivo

## Configurações Automáticas

Cada tipo vem com configurações pré-definidas:
```json
{
    "numeracao_automatica": true,
    "quorum_especial": "3/5",
    "tramitacao_especial": true,
    "campos_obrigatorios": ["ementa", "justificativa"],
    "prazos": {
        "discussao": 60,
        "emendas": 30
    }
}
```

## Desenvolvimento

### Arquivo de Mapeamento
`config/tipo_proposicao_mapping.php` contém:
- Todos os tipos e suas configurações
- Aliases e variações de nomes
- Configurações padrão

### API de Busca
`GET /admin/tipo-proposicoes/ajax/buscar-sugestoes?q=termo`

Retorna JSON com sugestões baseadas no termo pesquisado.

## Solução de Problemas

### Erro de Conexão com Banco
Se estiver desenvolvendo localmente e receber erro de conexão:
1. Use `.env.local` com `DB_HOST=localhost`
2. Para Docker, use `.env.docker` com `DB_HOST=db`

### Adicionar Novos Tipos
Edite `config/tipo_proposicao_mapping.php` e adicione:
1. Nova entrada em `mappings`
2. Aliases em `aliases` se necessário