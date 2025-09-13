# 🔍 Guia de Filtros Avançados - Sistema de Monitoramento

## 🎯 Visão Geral

O sistema de monitoramento de atividades do banco de dados agora inclui **filtros avançados** que permitem análises granulares das operações realizadas no sistema.

## 📍 Como Acessar

1. Navegue para: `/admin/monitoring/database-activity/detailed`
2. Selecione uma tabela
3. A seção "Filtros Avançados" será exibida automaticamente
4. Configure os filtros desejados e clique em "Aplicar Filtros"

## 🎛️ Tipos de Filtros Disponíveis

### 1. **Métodos HTTP**
Filtre por tipo de requisição HTTP:
- ✅ **GET** - Consultas e visualizações
- ✅ **POST** - Criação de recursos
- ✅ **PUT** - Atualizações completas
- ✅ **PATCH** - Atualizações parciais
- ✅ **DELETE** - Remoções

**Exemplo de Uso:**
- Desmarcar "GET" para ver apenas operações que modificam dados
- Selecionar apenas "POST" para ver criações de novos registros

### 2. **Tipos de Operação SQL**
Filtre por operação no banco de dados:
- ✅ **SELECT** - Consultas de dados
- ✅ **INSERT** - Inserção de registros
- ✅ **UPDATE** - Atualização de registros
- ✅ **DELETE** - Exclusão de registros

**Exemplo de Uso:**
- Selecionar apenas "INSERT" e "UPDATE" para ver mudanças de dados
- Focar em "SELECT" para analisar performance de consultas

### 3. **Usuários Específicos**
Filtre por usuário que executou a operação:
- Dropdown com usuários mais ativos
- Mostra quantidade de atividades por usuário
- Opção "Todos os usuários" para não filtrar

**Exemplo de Uso:**
- Analisar atividades de um parlamentar específico
- Verificar operações realizadas pelo administrador

### 4. **Endpoints (Palavras-chave)**
Filtre por partes da URL acessada:
- Busca parcial no endpoint
- Útil para encontrar operações específicas

**Exemplo de Uso:**
- Digitar "salvar" para ver operações de salvamento
- Digitar "proposicoes" para focar em operações de proposições

## 💡 Exemplos de Filtros Combinados

### Caso 1: Analisar Criações de Proposições
```
✅ Tabela: proposicoes
✅ Métodos: POST
✅ Operações: INSERT
✅ Período: 7 dias
```
**Resultado:** Todas as novas proposições criadas na última semana

### Caso 2: Verificar Edições de um Usuário
```
✅ Tabela: proposicoes
✅ Métodos: PUT, PATCH
✅ Operações: UPDATE
✅ Usuário: #2 (Jessica)
✅ Período: 24 horas
```
**Resultado:** Todas as edições feitas pela Jessica nas últimas 24h

### Caso 3: Monitorar Operações Críticas
```
✅ Tabela: users
✅ Métodos: POST, PUT, DELETE
✅ Operações: INSERT, UPDATE, DELETE
✅ Endpoint: admin
```
**Resultado:** Operações administrativas que alteram usuários

## 🎨 Interface Visual

### Badges Coloridos
- 🔵 **GET** - Azul (consultas)
- 🟢 **POST** - Verde (criações)
- 🟡 **PUT** - Amarelo (atualizações)
- 🟣 **PATCH** - Roxo (edições)
- 🔴 **DELETE** - Vermelho (exclusões)

### Visualização de Detalhes
Cada linha de atividade possui botão "Detalhes" que mostra:
- 🆔 **ID do Registro** afetado
- 📝 **Campos alterados** com valores antigo → novo
- ⏰ **Timestamp** da operação
- 👤 **Usuário** responsável

## 📊 Dados Carregados Dinamicamente

O sistema carrega automaticamente:

### Métodos HTTP Reais
- Apenas métodos que realmente existem nos logs
- Quantidade de uso de cada método

### Tabelas com Atividade
- Apenas tabelas que têm atividades registradas
- Contador de atividades por tabela
- Emojis identificadores por tipo

### Usuários Ativos
- Top 20 usuários com mais atividades
- Contador de ações por usuário

## ⚡ Funcionalidades Avançadas

### 1. **Auto-carregamento**
- Filtros são carregados do banco em tempo real
- Cache de 5 minutos para otimização

### 2. **Filtros Persistentes**
- Configurações mantidas durante a sessão
- Botão "Limpar Filtros" restaura configuração padrão

### 3. **Combinação Flexível**
- Todos os filtros podem ser combinados
- Lógica E (AND) entre diferentes tipos de filtro
- Lógica OU (OR) dentro do mesmo tipo de filtro

### 4. **Performance Otimizada**
- Limite de 500 registros por consulta
- Índices otimizados nas tabelas
- Cache inteligente

## 🚀 Casos de Uso Práticos

### Para Administradores
```
Cenário: Verificar se há operações suspeitas
Filtros:
- Métodos: DELETE
- Operações: DELETE
- Período: 24 horas
- Usuário: Todos

Resultado: Lista de todas as exclusões nas últimas 24h
```

### Para Auditoria
```
Cenário: Rastrear mudanças em proposição específica
Filtros:
- Tabela: proposicoes
- Métodos: PUT, PATCH
- Operações: UPDATE
- Endpoint: proposicoes/123

Resultado: Histórico completo de edições na proposição #123
```

### Para Análise de Performance
```
Cenário: Identificar consultas lentas
Filtros:
- Operações: SELECT
- Período: 1 hora
- Ordenar por: Tempo de execução

Resultado: Queries SELECT mais demoradas da última hora
```

## 🔧 API Endpoints

### Obter Opções de Filtro
```
GET /admin/monitoring/database-activity/filter-options

Response:
{
  "success": true,
  "options": {
    "http_methods": ["GET", "POST", "PUT", "PATCH", "DELETE"],
    "operation_types": ["SELECT", "INSERT", "UPDATE", "DELETE"],
    "tables_with_activity": [...],
    "active_users": [...]
  }
}
```

### Filtrar Atividades
```
GET /admin/monitoring/database-activity/filter?table=proposicoes&methods=POST,PUT&operations=INSERT,UPDATE&period=7days

Response:
{
  "success": true,
  "activities": [...],
  "total": 42,
  "filters_applied": {...}
}
```

## ⚠️ Limitações e Considerações

### Limitações
- Máximo 500 registros por consulta (para performance)
- Cache de 5 minutos nas opções de filtro
- Apenas atividades via HTTP (não CLI/Tinker)

### Melhores Práticas
- Use períodos menores para consultas mais rápidas
- Combine filtros para análises específicas
- Exporte dados para análises offline quando necessário

## 🎯 Próximos Passos

O sistema está pronto para:
1. ✅ **Filtros múltiplos** - Implementado
2. ✅ **Interface visual** - Implementado
3. ✅ **Carregamento dinâmico** - Implementado
4. ✅ **Cache otimizado** - Implementado

---

**Versão**: v2.0
**Última atualização**: 13/09/2025
**Status**: 🟢 Produção