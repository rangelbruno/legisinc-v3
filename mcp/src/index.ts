import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { z } from 'zod';
import pg from 'pg';
import dotenv from 'dotenv';
import {
  CallToolRequestSchema,
  ErrorCode,
  ListToolsRequestSchema,
  McpError,
} from '@modelcontextprotocol/sdk/types.js';

dotenv.config();

const { Pool } = pg;

// Database connection configuration
const dbConfig = {
  host: process.env.DB_HOST || 'localhost',
  port: parseInt(process.env.DB_PORT || '5432'),
  database: process.env.DB_DATABASE || 'legisinc',
  user: process.env.DB_USERNAME || 'postgres',
  password: process.env.DB_PASSWORD || '123456',
  max: 10,
  idleTimeoutMillis: 30000,
  connectionTimeoutMillis: 10000,
};

const pool = new Pool(dbConfig);

// Schemas for tool parameters
const QuerySchema = z.object({
  query: z.string().describe('SQL query to execute'),
  params: z.array(z.any()).optional().describe('Query parameters for prepared statements'),
});

const TableSchema = z.object({
  table: z.string().describe('Table name'),
  limit: z.number().optional().default(100).describe('Number of records to return'),
  offset: z.number().optional().default(0).describe('Number of records to skip'),
});

const RecordSchema = z.object({
  table: z.string().describe('Table name'),
  id: z.union([z.string(), z.number()]).describe('Record ID'),
});

const InsertSchema = z.object({
  table: z.string().describe('Table name'),
  data: z.record(z.any()).describe('Data to insert'),
  returning: z.array(z.string()).optional().describe('Columns to return after insert'),
});

const UpdateSchema = z.object({
  table: z.string().describe('Table name'),
  id: z.union([z.string(), z.number()]).describe('Record ID'),
  data: z.record(z.any()).describe('Data to update'),
  returning: z.array(z.string()).optional().describe('Columns to return after update'),
});

const DeleteSchema = z.object({
  table: z.string().describe('Table name'),
  id: z.union([z.string(), z.number()]).describe('Record ID'),
});

const server = new Server(
  {
    name: 'legisinc-db-mcp',
    version: '1.0.0',
  },
  {
    capabilities: {
      tools: {},
    },
  }
);

// Helper function to sanitize table names
function sanitizeTableName(table: string): string {
  if (!/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(table)) {
    throw new Error('Invalid table name');
  }
  return table;
}

// Helper function to build WHERE clause
function buildWhereClause(conditions: Record<string, any>): { text: string; values: any[] } {
  const keys = Object.keys(conditions);
  const values = Object.values(conditions);
  const whereClause = keys.map((key, index) => `"${key}" = $${index + 1}`).join(' AND ');
  return { text: whereClause ? `WHERE ${whereClause}` : '', values };
}

// Tool: List all tables
server.setRequestHandler(ListToolsRequestSchema, async () => {
  return {
    tools: [
      {
        name: 'db_query',
        description: 'Execute a raw SQL query (read-only by default)',
        inputSchema: {
          type: 'object',
          properties: {
            query: { type: 'string', description: 'SQL query to execute' },
            params: { type: 'array', items: {}, description: 'Query parameters' },
          },
          required: ['query'],
        },
      },
      {
        name: 'db_list_tables',
        description: 'List all tables in the database',
        inputSchema: {
          type: 'object',
          properties: {},
        },
      },
      {
        name: 'db_describe_table',
        description: 'Get table structure and column information',
        inputSchema: {
          type: 'object',
          properties: {
            table: { type: 'string', description: 'Table name' },
          },
          required: ['table'],
        },
      },
      {
        name: 'db_get_records',
        description: 'Get records from a table',
        inputSchema: {
          type: 'object',
          properties: {
            table: { type: 'string', description: 'Table name' },
            limit: { type: 'number', description: 'Number of records', default: 100 },
            offset: { type: 'number', description: 'Offset', default: 0 },
          },
          required: ['table'],
        },
      },
      {
        name: 'db_get_record',
        description: 'Get a single record by ID',
        inputSchema: {
          type: 'object',
          properties: {
            table: { type: 'string', description: 'Table name' },
            id: { type: ['string', 'number'], description: 'Record ID' },
          },
          required: ['table', 'id'],
        },
      },
      {
        name: 'db_insert',
        description: 'Insert a new record',
        inputSchema: {
          type: 'object',
          properties: {
            table: { type: 'string', description: 'Table name' },
            data: { type: 'object', description: 'Data to insert' },
            returning: { type: 'array', items: { type: 'string' }, description: 'Columns to return' },
          },
          required: ['table', 'data'],
        },
      },
      {
        name: 'db_update',
        description: 'Update an existing record',
        inputSchema: {
          type: 'object',
          properties: {
            table: { type: 'string', description: 'Table name' },
            id: { type: ['string', 'number'], description: 'Record ID' },
            data: { type: 'object', description: 'Data to update' },
            returning: { type: 'array', items: { type: 'string' }, description: 'Columns to return' },
          },
          required: ['table', 'id', 'data'],
        },
      },
      {
        name: 'db_delete',
        description: 'Delete a record',
        inputSchema: {
          type: 'object',
          properties: {
            table: { type: 'string', description: 'Table name' },
            id: { type: ['string', 'number'], description: 'Record ID' },
          },
          required: ['table', 'id'],
        },
      },
      {
        name: 'db_count',
        description: 'Count records in a table',
        inputSchema: {
          type: 'object',
          properties: {
            table: { type: 'string', description: 'Table name' },
          },
          required: ['table'],
        },
      },
      {
        name: 'db_search',
        description: 'Search records in a table',
        inputSchema: {
          type: 'object',
          properties: {
            table: { type: 'string', description: 'Table name' },
            column: { type: 'string', description: 'Column to search' },
            value: { type: 'string', description: 'Search value' },
            operator: { 
              type: 'string', 
              enum: ['=', 'LIKE', 'ILIKE', '>', '<', '>=', '<=', '!='],
              default: 'ILIKE',
              description: 'Search operator' 
            },
            limit: { type: 'number', description: 'Number of records', default: 100 },
          },
          required: ['table', 'column', 'value'],
        },
      },
    ],
  };
});

// Tool handler
server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args } = request.params;

  try {
    switch (name) {
      case 'db_query': {
        const { query, params } = QuerySchema.parse(args);
        
        // Basic safety check - only allow SELECT, SHOW, EXPLAIN for read-only
        const isReadOnly = /^\s*(SELECT|SHOW|EXPLAIN|WITH)/i.test(query);
        if (!isReadOnly && !query.toLowerCase().includes('returning')) {
          throw new McpError(
            ErrorCode.InvalidRequest,
            'Only read queries or mutations with RETURNING clause are allowed'
          );
        }
        
        const result = await pool.query(query, params);
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                rows: result.rows,
                rowCount: result.rowCount,
                fields: result.fields?.map(f => ({ name: f.name, dataType: f.dataTypeID })),
              }, null, 2),
            },
          ],
        };
      }

      case 'db_list_tables': {
        const query = `
          SELECT 
            schemaname,
            tablename,
            tableowner
          FROM pg_tables
          WHERE schemaname NOT IN ('pg_catalog', 'information_schema')
          ORDER BY schemaname, tablename;
        `;
        const result = await pool.query(query);
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify(result.rows, null, 2),
            },
          ],
        };
      }

      case 'db_describe_table': {
        const { table } = TableSchema.parse(args);
        const safeTable = sanitizeTableName(table);
        
        const query = `
          SELECT 
            column_name,
            data_type,
            character_maximum_length,
            is_nullable,
            column_default,
            udt_name
          FROM information_schema.columns
          WHERE table_name = $1
          ORDER BY ordinal_position;
        `;
        
        const result = await pool.query(query, [safeTable]);
        
        // Get indexes
        const indexQuery = `
          SELECT 
            indexname,
            indexdef
          FROM pg_indexes
          WHERE tablename = $1;
        `;
        const indexes = await pool.query(indexQuery, [safeTable]);
        
        // Get foreign keys
        const fkQuery = `
          SELECT
            tc.constraint_name,
            kcu.column_name,
            ccu.table_name AS foreign_table_name,
            ccu.column_name AS foreign_column_name
          FROM information_schema.table_constraints AS tc
          JOIN information_schema.key_column_usage AS kcu
            ON tc.constraint_name = kcu.constraint_name
          JOIN information_schema.constraint_column_usage AS ccu
            ON ccu.constraint_name = tc.constraint_name
          WHERE tc.constraint_type = 'FOREIGN KEY'
            AND tc.table_name = $1;
        `;
        const foreignKeys = await pool.query(fkQuery, [safeTable]);
        
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                columns: result.rows,
                indexes: indexes.rows,
                foreignKeys: foreignKeys.rows,
              }, null, 2),
            },
          ],
        };
      }

      case 'db_get_records': {
        const { table, limit, offset } = TableSchema.parse(args);
        const safeTable = sanitizeTableName(table);
        
        const query = `SELECT * FROM "${safeTable}" LIMIT $1 OFFSET $2`;
        const result = await pool.query(query, [limit, offset]);
        
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                rows: result.rows,
                count: result.rowCount,
                limit,
                offset,
              }, null, 2),
            },
          ],
        };
      }

      case 'db_get_record': {
        const { table, id } = RecordSchema.parse(args);
        const safeTable = sanitizeTableName(table);
        
        const query = `SELECT * FROM "${safeTable}" WHERE id = $1`;
        const result = await pool.query(query, [id]);
        
        if (result.rows.length === 0) {
          throw new McpError(ErrorCode.InvalidRequest, 'Record not found');
        }
        
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify(result.rows[0], null, 2),
            },
          ],
        };
      }

      case 'db_insert': {
        const { table, data, returning } = InsertSchema.parse(args);
        const safeTable = sanitizeTableName(table);
        
        const columns = Object.keys(data);
        const values = Object.values(data);
        const placeholders = columns.map((_, i) => `$${i + 1}`).join(', ');
        const columnList = columns.map(c => `"${c}"`).join(', ');
        
        let query = `INSERT INTO "${safeTable}" (${columnList}) VALUES (${placeholders})`;
        if (returning && returning.length > 0) {
          query += ` RETURNING ${returning.map(c => `"${c}"`).join(', ')}`;
        } else {
          query += ' RETURNING *';
        }
        
        const result = await pool.query(query, values);
        
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                inserted: result.rows[0],
                rowCount: result.rowCount,
              }, null, 2),
            },
          ],
        };
      }

      case 'db_update': {
        const { table, id, data, returning } = UpdateSchema.parse(args);
        const safeTable = sanitizeTableName(table);
        
        const columns = Object.keys(data);
        const values = Object.values(data);
        const setClause = columns.map((col, i) => `"${col}" = $${i + 2}`).join(', ');
        
        let query = `UPDATE "${safeTable}" SET ${setClause} WHERE id = $1`;
        if (returning && returning.length > 0) {
          query += ` RETURNING ${returning.map(c => `"${c}"`).join(', ')}`;
        } else {
          query += ' RETURNING *';
        }
        
        const result = await pool.query(query, [id, ...values]);
        
        if (result.rowCount === 0) {
          throw new McpError(ErrorCode.InvalidRequest, 'Record not found');
        }
        
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                updated: result.rows[0],
                rowCount: result.rowCount,
              }, null, 2),
            },
          ],
        };
      }

      case 'db_delete': {
        const { table, id } = DeleteSchema.parse(args);
        const safeTable = sanitizeTableName(table);
        
        const query = `DELETE FROM "${safeTable}" WHERE id = $1 RETURNING *`;
        const result = await pool.query(query, [id]);
        
        if (result.rowCount === 0) {
          throw new McpError(ErrorCode.InvalidRequest, 'Record not found');
        }
        
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                deleted: result.rows[0],
                rowCount: result.rowCount,
              }, null, 2),
            },
          ],
        };
      }

      case 'db_count': {
        const { table } = TableSchema.parse(args);
        const safeTable = sanitizeTableName(table);
        
        const query = `SELECT COUNT(*) as count FROM "${safeTable}"`;
        const result = await pool.query(query);
        
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                table: safeTable,
                count: parseInt(result.rows[0].count),
              }, null, 2),
            },
          ],
        };
      }

      case 'db_search': {
        const parsed = z.object({
          table: z.string(),
          column: z.string(),
          value: z.string(),
          operator: z.enum(['=', 'LIKE', 'ILIKE', '>', '<', '>=', '<=', '!=']).default('ILIKE'),
          limit: z.number().default(100),
        }).parse(args);
        
        const safeTable = sanitizeTableName(parsed.table);
        
        let searchValue = parsed.value;
        if (parsed.operator === 'LIKE' || parsed.operator === 'ILIKE') {
          searchValue = `%${searchValue}%`;
        }
        
        const query = `SELECT * FROM "${safeTable}" WHERE "${parsed.column}" ${parsed.operator} $1 LIMIT $2`;
        const result = await pool.query(query, [searchValue, parsed.limit]);
        
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                rows: result.rows,
                count: result.rowCount,
                searchCriteria: {
                  table: safeTable,
                  column: parsed.column,
                  operator: parsed.operator,
                  value: parsed.value,
                },
              }, null, 2),
            },
          ],
        };
      }

      default:
        throw new McpError(ErrorCode.MethodNotFound, `Unknown tool: ${name}`);
    }
  } catch (error) {
    if (error instanceof McpError) {
      throw error;
    }
    
    const pgError = error as any;
    throw new McpError(
      ErrorCode.InternalError,
      `Database error: ${pgError.message}`,
      {
        code: pgError.code,
        detail: pgError.detail,
        hint: pgError.hint,
      }
    );
  }
});

// Graceful shutdown
process.on('SIGINT', async () => {
  await pool.end();
  process.exit(0);
});

// Start the server
async function main() {
  try {
    // Test database connection
    await pool.query('SELECT 1');
    console.error('Database connection successful');
    
    const transport = new StdioServerTransport();
    await server.connect(transport);
    console.error('Legisinc DB MCP Server started');
  } catch (error) {
    console.error('Failed to start server:', error);
    process.exit(1);
  }
}

main().catch(console.error);