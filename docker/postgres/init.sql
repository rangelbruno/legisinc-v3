-- Configuração inicial do banco de dados LegisInc
-- Este script é executado automaticamente na primeira inicialização

-- Configurar encoding e locale
CREATE DATABASE legisinc 
    WITH ENCODING 'UTF8' 
    LC_COLLATE='C' 
    LC_CTYPE='C' 
    TEMPLATE=template0;

-- Conceder privilégios ao usuário postgres
GRANT ALL PRIVILEGES ON DATABASE legisinc TO postgres;

-- Conectar ao banco legisinc
\c legisinc;

-- Configurar extensões necessárias
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Configurar timezone
SET timezone = 'America/Sao_Paulo';