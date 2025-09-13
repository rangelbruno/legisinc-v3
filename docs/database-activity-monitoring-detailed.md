# Sistema de Monitoramento Detalhado de Atividades do Banco de Dados

## 🎯 Objetivo

Implementação de um sistema completo para monitorar atividades do banco de dados com **detalhamento das mudanças** em cada operação, permitindo visualizar exatamente o que foi alterado em cada interação com as tabelas.

## ✅ Funcionalidades Implementadas

### 1. **Captura Detalhada de Mudanças**
- ✅ **INSERT**: Captura todos os campos e valores inseridos
- ✅ **UPDATE**: Captura campos alterados com valores novos
- ✅ **DELETE**: Captura ID do registro deletado
- ✅ **Normalização**: Nomes de tabelas sempre em minúsculas para consistência

### 2. **Interface Web Aprimorada** (`/admin/monitoring/database-activity/detailed`)
- ✅ **Tabela de Atividades**: Lista cronológica com botão "Detalhes"
- ✅ **Visualização de Mudanças**: Expandir linha para ver campos alterados
- ✅ **Badges Coloridos**: Identificação visual por tipo de operação
- ✅ **Valores Antigo vs Novo**: Comparação visual com cores (vermelho/verde)

### 3. **Estrutura de Dados**

#### Tabela `database_activities`
```sql
-- Nova coluna adicionada
change_details JSON NULL -- Detalhes das mudanças em formato JSON
```

#### Estrutura do JSON `change_details`
```json
{
  "fields": {
    "campo_alterado_1": {
      "old": "valor_antigo",
      "new": "valor_novo"
    },
    "campo_alterado_2": {
      "old": null,
      "new": "valor_inserido"
    }
  },
  "record_id": 123
}
```

## 🔍 Como Visualizar

### Interface Web
1. Acesse: `/admin/monitoring/database-activity/detailed`
2. Selecione a tabela (ex: "proposicoes")
3. Escolha o período (24h, 7d, 30d)
4. Clique em "Analisar Tabela"
5. Na seção "Atividades Detalhadas", clique em "Detalhes" em qualquer linha

### Exemplo de Visualização

```
Horário              Tabela        Operação    Tempo     Método    Endpoint                           Usuário
13/09, 12:52:39     proposicoes   UPDATE      1.50ms    POST      proposicoes/1/confirmar-leitura   #2
                    ▼ DETALHES:
                    ID do Registro: #1

                    Campos Alterados:
                    ┌─ confirmacao_leitura ────────────────────┐
                    │ false → true                             │
                    └─────────────────────────────────────────┘

                    ┌─ data_aprovacao_autor ───────────────────┐
                    │ NULL → 2025-09-13 12:52:39             │
                    └─────────────────────────────────────────┘

13/09, 12:51:56     proposicoes   UPDATE      1.65ms    PATCH     proposicoes/1/status              #3
                    ▼ DETALHES:
                    ID do Registro: #1

                    Campos Alterados:
                    ┌─ status ─────────────────────────────────┐
                    │ rascunho → aprovado                      │
                    └─────────────────────────────────────────┘

                    ┌─ observacoes_legislativo ────────────────┐
                    │ NULL → Proposição aprovada conforme...   │
                    └─────────────────────────────────────────┘
```

## 🚀 Benefícios

### 1. **Auditoria Completa**
- **Rastreabilidade**: Histórico completo de todas as mudanças
- **Transparência**: Visualizar exatamente o que mudou em cada operação
- **Responsabilidade**: Identificar quem fez cada alteração

### 2. **Debugging Avançado**
- **Diagnóstico de Problemas**: Ver exatamente quais campos foram alterados
- **Análise de Performance**: Identificar operações que alteram muitos campos
- **Validação de Fluxos**: Confirmar se as alterações seguem o workflow esperado

### 3. **Compliance e Conformidade**
- **Registro de Auditoria**: Atende requisitos de auditoria governamental
- **Histórico de Mudanças**: Documentação automática de todas as alterações
- **Relatórios**: Possibilidade de gerar relatórios detalhados

## 🔧 Implementação Técnica

### 1. **Middleware** (`DatabaseActivityLogger`)
```php
// Captura automática em todas as requisições HTTP
private function extractQueryDetails(string $sql, array $bindings, string $tableName, string $operationType)
{
    // Extrai campos e valores de INSERTs, UPDATEs e DELETEs
    // Armazena em formato JSON estruturado
}
```

### 2. **Controller** (`DatabaseActivityController`)
```php
// API endpoints para carregar atividades com detalhes
public function filterActivities(Request $request)
{
    // Inclui campo change_details nas consultas
    // Permite filtrar por tabela, período, usuário, etc.
}
```

### 3. **Frontend** (JavaScript)
```javascript
// Interface interativa para expandir/recolher detalhes
function toggleActivityDetails(event, activityId)
{
    // Mostra/oculta detalhes das mudanças
    // Renderiza campos alterados com cores diferenciadas
}
```

## 📊 Métricas e Análises

### Dados Capturados por Operação
- ✅ **INSERT**: Todos os campos inseridos
- ✅ **UPDATE**: Apenas campos alterados
- ✅ **DELETE**: ID do registro removido
- ✅ **SELECT**: Informações básicas (sem detalhes de campos)

### Informações Contextuais
- 🔍 **Usuário**: Quem executou a operação
- 🔍 **Endpoint**: Qual rota/ação foi usada
- 🔍 **Timestamp**: Quando ocorreu
- 🔍 **Performance**: Tempo de execução da query
- 🔍 **Método HTTP**: GET, POST, PUT, PATCH, DELETE

## 🎨 Interface Visual

### Cores e Identificação
- 🔵 **INSERT**: Badge azul
- 🟡 **UPDATE**: Badge amarelo
- 🔴 **DELETE**: Badge vermelho
- ⚫ **SELECT**: Badge cinza

### Valores Alterados
- 🟥 **Valor Antigo**: Fundo vermelho claro
- 🟩 **Valor Novo**: Fundo verde claro
- ➡️ **Seta**: Indica transição old → new

## 🔐 Segurança e Performance

### Otimizações
- ✅ **Cache**: Queries em cache por 5 segundos
- ✅ **Limitação**: Máximo 500 registros por consulta
- ✅ **Índices**: Otimização para consultas por tabela e data
- ✅ **Exclusões**: Ignora tabelas de sistema e assets

### Privacidade
- ✅ **Dados Sensíveis**: Valores longos truncados (>100 chars)
- ✅ **Sanitização**: Escape HTML nos valores exibidos
- ✅ **Acesso Restrito**: Disponível apenas para administradores

## 📈 Casos de Uso

### 1. **Análise de Proposições**
```
Ver histórico completo de uma proposição:
- Criação inicial (INSERT)
- Edições do parlamentar (UPDATEs)
- Aprovação do legislativo (UPDATE)
- Assinatura digital (UPDATE)
```

### 2. **Debug de Problemas**
```
Identificar quando um campo foi alterado incorretamente:
- Ver valor anterior vs atual
- Identificar usuário responsável
- Rastrear endpoint usado
```

### 3. **Auditoria de Compliance**
```
Gerar relatório completo de mudanças:
- Todas as alterações em período específico
- Mudanças por usuário/role
- Operações em tabelas críticas
```

## 🚦 Status do Sistema

✅ **Implementado e Funcional**
- Middleware de captura ativo
- Interface web completa
- Estrutura de dados configurada
- Visualização detalhada operacional

⚠️ **Observações**
- Sistema captura apenas operações via HTTP (não Tinker/CLI)
- Detalhes são armazenados para INSERT/UPDATE/DELETE
- Queries SELECT registram apenas informações básicas

---

**Última atualização**: 13/09/2025
**Versão**: v1.0
**Status**: 🟢 Produção