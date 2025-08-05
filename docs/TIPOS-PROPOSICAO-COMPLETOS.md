# Tipos de Proposição - Seeder Completo

## Resumo
Foram criados **23 tipos de proposição** com base na classificação oficial do sistema legislativo brasileiro.

## Categorias

### 1. Normativas (criam norma) - 7 tipos
| Ordem | Código | Nome | Sigla | Ícone | Cor |
|-------|--------|------|-------|-------|-----|
| 1 | proposta_emenda_constituicao | Proposta de Emenda à Constituição | PEC | ki-shield-tick | danger |
| 2 | proposta_emenda_lei_organica | Proposta de Emenda à Lei Orgânica Municipal | PELOM | ki-shield-tick | danger |
| 3 | projeto_lei_ordinaria | Projeto de Lei Ordinária | PL | ki-document | primary |
| 4 | projeto_lei_complementar | Projeto de Lei Complementar | PLC/PLP | ki-document-edit | info |
| 5 | projeto_lei_delegada | Projeto de Lei Delegada | PLD | ki-document-folder | secondary |
| 6 | medida_provisoria | Medida Provisória | MP | ki-time | warning |
| 23 | projeto_consolidacao_leis | Projeto de Consolidação das Leis | PCL | ki-book | secondary |

### 2. Decretos e Resoluções - 3 tipos
| Ordem | Código | Nome | Sigla | Ícone | Cor |
|-------|--------|------|-------|-------|-----|
| 7 | projeto_decreto_legislativo | Projeto de Decreto Legislativo | PDL | ki-shield-search | success |
| 8 | projeto_decreto_congresso | Projeto de Decreto do Congresso | PDC | ki-shield-search | success |
| 9 | projeto_resolucao | Projeto de Resolução | PR | ki-home | dark |

### 3. Processuais/Acessórias - 6 tipos
| Ordem | Código | Nome | Sigla | Ícone | Cor |
|-------|--------|------|-------|-------|-----|
| 10 | requerimento | Requerimento | REQ | ki-questionnaire-tablet | info |
| 11 | indicacao | Indicação | IND | ki-send | primary |
| 12 | mocao | Moção | MOC | ki-message-text-2 | warning |
| 13 | emenda | Emenda | EME | ki-pencil | secondary |
| 14 | subemenda | Subemenda | SUB | ki-pencil | light |
| 15 | substitutivo | Substitutivo | - | ki-arrows-circle | info |

### 4. Outros - 7 tipos
| Ordem | Código | Nome | Sigla | Ícone | Cor |
|-------|--------|------|-------|-------|-----|
| 16 | parecer_comissao | Parecer de Comissão | PAR | ki-clipboard-check | success |
| 17 | relatorio | Relatório | REL | ki-document-text | dark |
| 18 | recurso | Recurso | REC | ki-arrow-circle-right | danger |
| 19 | veto | Veto | - | ki-cross-circle | danger |
| 20 | destaque | Destaque | - | ki-filter-search | warning |
| 21 | oficio | Ofício | OFI | ki-sms | primary |
| 22 | mensagem_executivo | Mensagem do Executivo | MSG | ki-message-programming | info |

## Configurações Especiais

### Quórum Especial
- **PEC**: 3/5 dos membros (federal) ou 2/3 (municipal)
- **PLC/PLP**: Maioria absoluta
- **MP**: Força de lei por 120 dias

### Tramitação Especial
- **PDL/PDC**: Efeitos externos, competência exclusiva
- **PR**: Efeitos internos (regimento, organização)
- **REQ**: Mais de 20 subespécies (informação, urgência, CPI, etc.)

### Características Únicas
- **EME**: Pode ser supressiva, aditiva, substitutiva, modificativa
- **MOC**: Tipos: aplauso, pesar, repúdio, louvor, congratulação
- **VETO**: Pode ser total ou parcial
- **PAR**: Tipos: constitucionalidade, mérito, finanças

## Comandos Úteis

### Executar Seeder
```bash
# Seeder individual
php artisan db:seed --class=TipoProposicaoCompletoSeeder

# Comando customizado
php artisan seed:tipos-proposicao

# Comando com limpeza prévia
php artisan seed:tipos-proposicao --fresh
```

### Verificar Dados
```sql
-- Contar tipos por status
SELECT ativo, COUNT(*) FROM tipo_proposicoes GROUP BY ativo;

-- Buscar por categoria
SELECT * FROM tipo_proposicoes WHERE nome LIKE '%Projeto%';

-- Ver configurações JSON
SELECT codigo, configuracoes FROM tipo_proposicoes WHERE codigo = 'projeto_lei_ordinaria';
```

## Integração com Busca

Todos os tipos criados são automaticamente reconhecidos pelo sistema de busca inteligente através de:
- Siglas (PL, PEC, REQ, etc.)
- Nomes parciais ("lei", "emenda", "moção")
- Nomes completos
- Aliases configurados no mapeamento

O sistema diferencia entre tipos já cadastrados (badge verde) e sugestões do mapeamento (badge azul).