# Guia de Uso do MCP PostgreSQL - Projeto Legisinc

## 📋 Visão Geral

O MCP (Model Context Protocol) PostgreSQL está configurado para conectar diretamente ao banco de dados do projeto Legisinc, permitindo consultas SQL, análise de estrutura e manipulação de dados diretamente pelo Claude Desktop.

## 🔧 Configuração Atual

**Arquivo de configuração**: `~/.config/claude/config.json`

```json
{
  "mcpServers": {
    "postgres-legisinc": {
      "command": "npx",
      "args": ["--yes", "@benborla29/pg-mcp-server", "--transport", "stdio"],
      "env": {
        "DATABASE_URL": "postgresql://postgres:123456@localhost:5432/legisinc"
      }
    }
  }
}
```

### Detalhes da Conexão
- **Host**: localhost (porta 5432)
- **Database**: legisinc
- **Usuário**: postgres
- **Senha**: 123456
- **Container Docker**: legisinc-postgres

## 🚀 Como Usar

### 1. Reiniciar o Claude Desktop
Após configurar o MCP, reinicie o Claude Desktop para carregar as novas configurações.

### 2. Verificar Conexão
Para verificar se o MCP está funcionando, peça ao Claude:
```
"Liste as tabelas do banco de dados"
"Mostre a estrutura da tabela proposicoes"
```

## 📊 Casos de Uso Práticos

### Consultas de Análise

#### 1. Estatísticas de Proposições
```sql
-- Total de proposições por status
SELECT status, COUNT(*) as total 
FROM proposicoes 
GROUP BY status 
ORDER BY total DESC;

-- Proposições criadas nos últimos 30 dias
SELECT * FROM proposicoes 
WHERE created_at >= NOW() - INTERVAL '30 days'
ORDER BY created_at DESC;
```

#### 2. Análise de Usuários
```sql
-- Usuários mais ativos (parlamentares)
SELECT u.name, u.email, COUNT(p.id) as total_proposicoes
FROM users u
LEFT JOIN proposicoes p ON u.id = p.autor_id
WHERE u.role = 'parlamentar'
GROUP BY u.id, u.name, u.email
ORDER BY total_proposicoes DESC;
```

#### 3. Workflow e Tramitação
```sql
-- Status atual do workflow das proposições
SELECT 
    p.numero_proposicao,
    p.titulo,
    dws.etapa_atual,
    we.nome as nome_etapa,
    dws.updated_at as ultima_atualizacao
FROM proposicoes p
JOIN documento_workflow_status dws ON p.id = dws.documento_id
JOIN workflow_etapas we ON dws.etapa_atual = we.id
WHERE dws.documento_tipo = 'proposicao'
ORDER BY dws.updated_at DESC;
```

### Manutenção e Debugging

#### 1. Verificar Integridade de Arquivos
```sql
-- Proposições com arquivos RTF/PDF faltando
SELECT 
    id, 
    numero_proposicao,
    arquivo_path,
    arquivo_pdf_path,
    CASE 
        WHEN arquivo_path IS NULL THEN 'RTF faltando'
        WHEN arquivo_pdf_path IS NULL THEN 'PDF faltando'
        ELSE 'OK'
    END as status_arquivo
FROM proposicoes
WHERE arquivo_path IS NULL OR arquivo_pdf_path IS NULL;
```

#### 2. Logs de Assinatura Digital
```sql
-- Últimas tentativas de assinatura
SELECT 
    proposicao_id,
    status,
    erro_mensagem,
    created_at
FROM assinatura_logs
ORDER BY created_at DESC
LIMIT 20;
```

#### 3. Verificar Templates
```sql
-- Templates disponíveis por tipo de proposição
SELECT 
    tp.nome as tipo_proposicao,
    t.nome as template,
    t.ativo,
    t.updated_at
FROM templates t
JOIN tipo_proposicoes tp ON t.tipo_proposicao_id = tp.id
WHERE t.ativo = true
ORDER BY tp.nome, t.ordem;
```

### Relatórios Gerenciais

#### 1. Produtividade Legislativa
```sql
-- Relatório mensal de produtividade
SELECT 
    DATE_TRUNC('month', p.created_at) as mes,
    u.name as parlamentar,
    COUNT(p.id) as total_proposicoes,
    COUNT(CASE WHEN p.status = 'aprovado' THEN 1 END) as aprovadas,
    COUNT(CASE WHEN p.status = 'rejeitado' THEN 1 END) as rejeitadas
FROM proposicoes p
JOIN users u ON p.autor_id = u.id
WHERE p.created_at >= NOW() - INTERVAL '6 months'
GROUP BY DATE_TRUNC('month', p.created_at), u.name
ORDER BY mes DESC, total_proposicoes DESC;
```

#### 2. Tempo Médio de Tramitação
```sql
-- Tempo médio entre criação e aprovação
SELECT 
    tp.nome as tipo_proposicao,
    COUNT(p.id) as total,
    AVG(EXTRACT(DAY FROM (p.data_aprovacao_autor - p.created_at))) as dias_medio_tramitacao
FROM proposicoes p
JOIN tipo_proposicoes tp ON p.tipo_proposicao_id = tp.id
WHERE p.data_aprovacao_autor IS NOT NULL
GROUP BY tp.nome
ORDER BY dias_medio_tramitacao;
```

## 🛠️ Comandos Úteis para Manutenção

### Limpeza de Dados

```sql
-- Limpar logs antigos (mais de 90 dias)
DELETE FROM assinatura_logs 
WHERE created_at < NOW() - INTERVAL '90 days';

-- Remover rascunhos abandonados (mais de 30 dias sem edição)
DELETE FROM proposicoes 
WHERE status = 'rascunho' 
AND updated_at < NOW() - INTERVAL '30 days';
```

### Backup e Restore

```bash
# Backup do banco via Docker
docker exec legisinc-postgres pg_dump -U postgres legisinc > backup_legisinc_$(date +%Y%m%d_%H%M%S).sql

# Restore do banco
docker exec -i legisinc-postgres psql -U postgres legisinc < backup_legisinc_20250910_120000.sql
```

## 📈 Monitoramento

### Queries para Monitorar Performance

```sql
-- Tabelas com mais registros
SELECT 
    schemaname,
    tablename,
    n_live_tup as registros_ativos,
    n_dead_tup as registros_mortos,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) as tamanho
FROM pg_stat_user_tables
ORDER BY n_live_tup DESC;

-- Queries mais lentas
SELECT 
    query,
    calls,
    total_time,
    mean_time,
    max_time
FROM pg_stat_statements
ORDER BY mean_time DESC
LIMIT 10;
```

## 🔍 Estrutura Principal do Banco

### Tabelas Principais
- **users**: Usuários do sistema (parlamentares, admin, protocolo, etc)
- **proposicoes**: Proposições legislativas
- **tipo_proposicoes**: Tipos de proposições (Moção, Projeto de Lei, etc)
- **templates**: Templates RTF para geração de documentos
- **workflow_etapas**: Etapas do fluxo de tramitação
- **workflow_transicoes**: Transições entre etapas
- **documento_workflow_status**: Status atual de cada documento no workflow
- **documento_workflow_historico**: Histórico de tramitação
- **assinatura_logs**: Logs de tentativas de assinatura digital

## 💡 Dicas Importantes

1. **Sempre use transações** para operações de UPDATE/DELETE em massa
2. **Evite queries sem WHERE** em tabelas grandes
3. **Use EXPLAIN** para analisar performance de queries complexas
4. **Faça backup** antes de operações críticas
5. **Monitore o espaço em disco** do container PostgreSQL

## 🚨 Troubleshooting

### Container PostgreSQL não está rodando
```bash
docker start legisinc-postgres
docker ps | grep postgres
```

### Erro de conexão no MCP
1. Verifique se o container está rodando
2. Confirme as credenciais no arquivo de configuração
3. Reinicie o Claude Desktop
4. Teste a conexão manualmente:
```bash
docker exec legisinc-postgres psql -U postgres -d legisinc -c "SELECT 1;"
```

### Banco de dados corrompido
```bash
# Recriar do zero com seed
docker exec -it legisinc-app php artisan migrate:fresh --seed
```

## 📚 Referências

- [Documentação PostgreSQL 15](https://www.postgresql.org/docs/15/)
- [MCP PostgreSQL Server](https://github.com/benborla29/pg-mcp-server)
- [Projeto Legisinc - CLAUDE.md](/home/bruno/legisinc/CLAUDE.md)

---

**Última atualização**: 10/09/2025
**Versão do PostgreSQL**: 15.13 (Alpine)
**Container**: legisinc-postgres