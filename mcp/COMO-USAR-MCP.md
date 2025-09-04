# Como Usar o MCP Server do Legisinc

## 📋 Pré-requisitos

- Node.js 18+ instalado
- PostgreSQL rodando (local ou Docker)
- Claude Desktop instalado

## 🚀 Instalação Rápida

### 1. Instalar dependências
```bash
cd /home/bruno/legisinc/mcp
npm install
```

### 2. Compilar o código
```bash
npm run build
```

### 3. Configurar variáveis de ambiente
```bash
# Edite o arquivo .env com suas credenciais
nano .env
```

```env
DB_HOST=localhost       # ou legisinc-postgres para Docker
DB_PORT=5432
DB_DATABASE=legisinc
DB_USERNAME=postgres
DB_PASSWORD=123456
```

## 🔧 Configuração no Claude Desktop

### Windows
Edite: `%APPDATA%\Claude\claude_desktop_config.json`

### macOS
Edite: `~/Library/Application Support/Claude/claude_desktop_config.json`

### Linux
Edite: `~/.config/Claude/claude_desktop_config.json`

### Adicione esta configuração:

```json
{
  "mcpServers": {
    "legisinc-db": {
      "command": "node",
      "args": ["/home/bruno/legisinc/mcp/dist/index.js"],
      "env": {
        "DB_HOST": "localhost",
        "DB_PORT": "5432",
        "DB_DATABASE": "legisinc",
        "DB_USERNAME": "postgres",
        "DB_PASSWORD": "123456"
      }
    }
  }
}
```

**⚠️ IMPORTANTE:** Use o caminho absoluto completo para o arquivo `index.js`

## 🎮 Como Usar no Claude

### 1. Reinicie o Claude Desktop
Após adicionar a configuração, reinicie completamente o Claude Desktop.

### 2. Verifique se o MCP está ativo
No Claude, você pode perguntar:
```
"Liste as tabelas do banco legisinc usando o MCP"
```

### 3. Exemplos de Comandos

#### Listar todas as tabelas
```
"Use o MCP para listar todas as tabelas do banco de dados"
```

#### Buscar usuários
```
"Use o MCP para buscar os primeiros 5 usuários da tabela users"
```

#### Descrever estrutura de tabela
```
"Use o MCP para descrever a estrutura da tabela proposicoes"
```

#### Pesquisar registros
```
"Use o MCP para pesquisar proposições que contenham 'educação' na ementa"
```

#### Contar registros
```
"Use o MCP para contar quantas proposições existem no banco"
```

## 📝 Sintaxe dos Comandos MCP

### Comandos de Leitura

#### db_list_tables
```javascript
// Lista todas as tabelas
{ }
```

#### db_describe_table
```javascript
// Mostra estrutura da tabela
{
  "table": "proposicoes"
}
```

#### db_get_records
```javascript
// Busca registros com paginação
{
  "table": "users",
  "limit": 10,
  "offset": 0
}
```

#### db_get_record
```javascript
// Busca um registro por ID
{
  "table": "users",
  "id": 1
}
```

#### db_count
```javascript
// Conta registros
{
  "table": "proposicoes"
}
```

#### db_search
```javascript
// Pesquisa em coluna específica
{
  "table": "proposicoes",
  "column": "ementa",
  "value": "educação",
  "operator": "ILIKE",
  "limit": 20
}
```

#### db_query
```javascript
// Query SQL customizada (apenas leitura)
{
  "query": "SELECT * FROM users WHERE role = $1",
  "params": ["parlamentar"]
}
```

### Comandos de Escrita

#### db_insert
```javascript
// Inserir novo registro
{
  "table": "tipos_proposicao",
  "data": {
    "nome": "Requerimento",
    "descricao": "Requerimento parlamentar"
  },
  "returning": ["id", "nome"]
}
```

#### db_update
```javascript
// Atualizar registro
{
  "table": "users",
  "id": 1,
  "data": {
    "name": "Novo Nome"
  },
  "returning": ["*"]
}
```

#### db_delete
```javascript
// Deletar registro
{
  "table": "proposicoes",
  "id": 123
}
```

## 🔍 Operadores de Pesquisa

Para o comando `db_search`, você pode usar:

- `=` - Igualdade exata
- `!=` - Diferente
- `>` - Maior que
- `<` - Menor que
- `>=` - Maior ou igual
- `<=` - Menor ou igual
- `LIKE` - Padrão SQL (case sensitive)
- `ILIKE` - Padrão SQL (case insensitive) - **Padrão**

## 🛠️ Desenvolvimento e Debug

### Modo desenvolvimento
```bash
cd /home/bruno/legisinc/mcp
npm run dev
```

### Testar conexão
```bash
# Teste direto do Node.js
node -e "
const pg = require('pg');
const pool = new pg.Pool({
  host: 'localhost',
  port: 5432,
  database: 'legisinc',
  user: 'postgres',
  password: '123456'
});
pool.query('SELECT NOW()').then(r => {
  console.log('Conexão OK:', r.rows[0]);
  pool.end();
}).catch(e => console.error('Erro:', e.message));
"
```

### Ver logs do MCP
O MCP envia logs para stderr. No Claude Desktop, erros aparecem no console do desenvolvedor.

## 🐳 Uso com Docker

Se o PostgreSQL está rodando no Docker:

1. **Verifique o container:**
```bash
docker ps | grep postgres
```

2. **Para acessar de dentro do container do app:**
```env
DB_HOST=legisinc-postgres
```

3. **Para acessar do host (fora do Docker):**
```env
DB_HOST=localhost
```

## ❌ Solução de Problemas

### MCP não aparece no Claude

1. **Verifique o arquivo de config:**
```bash
# Linux
cat ~/.config/Claude/claude_desktop_config.json

# macOS
cat ~/Library/Application\ Support/Claude/claude_desktop_config.json

# Windows (PowerShell)
Get-Content $env:APPDATA\Claude\claude_desktop_config.json
```

2. **Reinicie o Claude Desktop completamente**

3. **Verifique se o caminho está correto:**
```bash
ls -la /home/bruno/legisinc/mcp/dist/index.js
```

### Erro de conexão com banco

1. **Teste conexão direta:**
```bash
psql -h localhost -U postgres -d legisinc -c "SELECT 1"
```

2. **Verifique se o PostgreSQL está rodando:**
```bash
docker ps | grep postgres
# ou
systemctl status postgresql
```

3. **Confirme as credenciais no .env**

### Comando não encontrado

Certifique-se de que o Node.js está instalado:
```bash
node --version  # Deve ser 18+
```

## 📊 Casos de Uso Práticos

### Relatório de Proposições
```
"Use o MCP para:
1. Contar total de proposições
2. Listar as 5 últimas proposições criadas
3. Buscar proposições do tipo 'Moção'"
```

### Gestão de Usuários
```
"Use o MCP para:
1. Listar todos os usuários do sistema
2. Mostrar quantos usuários de cada role existem
3. Buscar usuários com email do domínio sistema.gov.br"
```

### Análise de Templates
```
"Use o MCP para:
1. Listar todos os tipos de proposição
2. Verificar quais tipos têm templates associados
3. Buscar o template da Moção"
```

## 🔒 Segurança

- **Validação:** Nomes de tabelas são validados contra SQL injection
- **Prepared Statements:** Todas as queries usam parâmetros seguros
- **Restrições:** Queries diretas são limitadas a SELECT/SHOW/EXPLAIN
- **Timeout:** Conexões têm timeout configurável
- **Pool:** Limite de conexões simultâneas

## 📚 Referências

- [MCP Documentation](https://modelcontextprotocol.io/docs)
- [PostgreSQL Node.js Driver](https://node-postgres.com/)
- [Zod Validation](https://zod.dev/)

---

**💡 Dica:** Sempre use o MCP para operações no banco quando estiver no Claude. É mais seguro e estruturado que queries diretas!