# Legisinc Database MCP Server

MCP (Model Context Protocol) server para acesso controlado ao banco de dados PostgreSQL do sistema Legisinc.

## Funcionalidades

### Comandos de Leitura
- `db_query` - Executa queries SQL (somente leitura ou com RETURNING)
- `db_list_tables` - Lista todas as tabelas do banco
- `db_describe_table` - Descreve estrutura de uma tabela
- `db_get_records` - Busca registros com paginação
- `db_get_record` - Busca um registro por ID
- `db_count` - Conta registros em uma tabela
- `db_search` - Pesquisa registros por coluna

### Comandos de Escrita
- `db_insert` - Insere novo registro
- `db_update` - Atualiza registro existente
- `db_delete` - Remove registro

## Instalação

```bash
cd mcp
npm install
npm run build
```

## Configuração

### Variáveis de Ambiente (.env)
```env
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=legisinc
DB_USERNAME=postgres
DB_PASSWORD=123456
```

### Para uso com Docker
```env
DB_HOST=legisinc-postgres
```

## Uso

### Desenvolvimento
```bash
npm run dev
```

### Produção
```bash
npm start
```

## Integração com Claude Desktop

Adicione ao arquivo de configuração do Claude Desktop:

```json
{
  "mcpServers": {
    "legisinc-db": {
      "command": "node",
      "args": ["/caminho/para/legisinc/mcp/dist/index.js"],
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

## Exemplos de Uso

### Listar tabelas
```javascript
await mcp.call('db_list_tables', {});
```

### Buscar registros
```javascript
await mcp.call('db_get_records', {
  table: 'users',
  limit: 10,
  offset: 0
});
```

### Inserir registro
```javascript
await mcp.call('db_insert', {
  table: 'proposicoes',
  data: {
    numero: '0001/2025',
    ementa: 'Nova proposição',
    texto: 'Conteúdo da proposição'
  },
  returning: ['id', 'numero', 'created_at']
});
```

### Pesquisar registros
```javascript
await mcp.call('db_search', {
  table: 'proposicoes',
  column: 'ementa',
  value: 'educação',
  operator: 'ILIKE',
  limit: 20
});
```

## Segurança

- Validação de nomes de tabelas para prevenir SQL injection
- Queries parametrizadas com prepared statements
- Limite de conexões configurável
- Timeout de conexão configurável
- Restrição de queries perigosas (apenas SELECT/SHOW/EXPLAIN ou com RETURNING)

## Estrutura do Projeto

```
mcp/
├── src/
│   └── index.ts        # Código principal do servidor
├── dist/               # Código compilado
├── .env                # Configuração local
├── mcp.json            # Configuração do MCP
├── package.json        # Dependências
├── tsconfig.json       # Configuração TypeScript
└── README.md           # Documentação
```

## Desenvolvimento

### Adicionar novo comando

1. Adicione o schema de validação
2. Registre o comando em `ListToolsRequestSchema`
3. Implemente o handler em `CallToolRequestSchema`

### Testes

Para testar o servidor localmente:

```bash
# Terminal 1 - Iniciar servidor
npm run dev

# Terminal 2 - Testar com cliente MCP
npx @modelcontextprotocol/cli connect stdio "node dist/index.js"
```

## Troubleshooting

### Erro de conexão
- Verifique se o PostgreSQL está rodando
- Confirme credenciais no .env
- Para Docker, use o nome do container como host

### Erro de permissão
- Verifique permissões do usuário no PostgreSQL
- Confirme que o usuário tem acesso ao banco

## Licença

MIT