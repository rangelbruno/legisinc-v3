# Estrutura do Banco de Dados PostgreSQL - LegisInc

## Visão Geral

Este documento apresenta a estrutura completa do banco de dados do sistema LegisInc, um sistema de gestão legislativa com controle de permissões, gestão de projetos parlamentares e sistema de parâmetros configuráveis.

## Tabelas do Sistema

### 1. Core Laravel Tables

#### 1.1 users
**Propósito**: Autenticação e gestão de perfis de usuários
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- name: VARCHAR(255) NOT NULL
- email: VARCHAR(255) UNIQUE NOT NULL
- email_verified_at: TIMESTAMP NULL
- password: VARCHAR(255) NOT NULL
- remember_token: VARCHAR(100) NULL
- documento: VARCHAR(255) NULL (CPF ou documento)
- telefone: VARCHAR(255) NULL
- data_nascimento: DATE NULL
- profissao: VARCHAR(255) NULL
- cargo_atual: VARCHAR(255) NULL
- partido: VARCHAR(255) NULL
- preferencias: JSON NULL
- ativo: BOOLEAN DEFAULT TRUE
- ultimo_acesso: TIMESTAMP NULL
- avatar: VARCHAR(255) NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: documento, partido, ativo, ultimo_acesso

#### 1.2 password_reset_tokens
**Propósito**: Funcionalidade de redefinição de senha
```sql
- email: VARCHAR(255) PRIMARY KEY
- token: VARCHAR(255) NOT NULL
- created_at: TIMESTAMP NULL
```

#### 1.3 sessions
**Propósito**: Gestão de sessões de usuário
```sql
- id: VARCHAR(255) PRIMARY KEY
- user_id: BIGINT NULL INDEX
- ip_address: VARCHAR(45) NULL
- user_agent: TEXT NULL
- payload: LONGTEXT NOT NULL
- last_activity: INTEGER INDEX
```

### 2. Cache e Queue Tables

#### 2.1 cache
**Propósito**: Cache da aplicação
```sql
- key: VARCHAR(255) PRIMARY KEY
- value: MEDIUMTEXT NOT NULL
- expiration: INTEGER NOT NULL
```

#### 2.2 cache_locks
**Propósito**: Mecanismo de bloqueio de cache
```sql
- key: VARCHAR(255) PRIMARY KEY
- owner: VARCHAR(255) NOT NULL
- expiration: INTEGER NOT NULL
```

#### 2.3 jobs
**Propósito**: Gestão de filas de trabalho
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- queue: VARCHAR(255) INDEX
- payload: LONGTEXT NOT NULL
- attempts: TINYINT UNSIGNED NOT NULL
- reserved_at: INTEGER UNSIGNED NULL
- available_at: INTEGER UNSIGNED NOT NULL
- created_at: INTEGER UNSIGNED NOT NULL
```

#### 2.4 job_batches
**Propósito**: Gestão de lotes de trabalhos
```sql
- id: VARCHAR(255) PRIMARY KEY
- name: VARCHAR(255) NOT NULL
- total_jobs: INTEGER NOT NULL
- pending_jobs: INTEGER NOT NULL
- failed_jobs: INTEGER NOT NULL
- failed_job_ids: LONGTEXT NOT NULL
- options: MEDIUMTEXT NULL
- cancelled_at: INTEGER NULL
- created_at: INTEGER NOT NULL
- finished_at: INTEGER NULL
```

#### 2.5 failed_jobs
**Propósito**: Trabalhos de fila falhados
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- uuid: VARCHAR(255) UNIQUE
- connection: TEXT NOT NULL
- queue: TEXT NOT NULL
- payload: LONGTEXT NOT NULL
- exception: LONGTEXT NOT NULL
- failed_at: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```

### 3. Sistema de Permissões (Spatie Package)

#### 3.1 permissions
**Propósito**: Permissões do sistema
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- name: VARCHAR(255) NOT NULL
- guard_name: VARCHAR(255) NOT NULL
- created_at, updated_at: TIMESTAMPS
```
**Unique**: `name, guard_name`

#### 3.2 roles
**Propósito**: Papéis de usuário
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- name: VARCHAR(255) NOT NULL
- guard_name: VARCHAR(255) NOT NULL
- created_at, updated_at: TIMESTAMPS
```
**Unique**: `name, guard_name`

#### 3.3 model_has_permissions
**Propósito**: Permissões diretas atribuídas a modelos
```sql
- permission_id: BIGINT UNSIGNED NOT NULL
- model_type: VARCHAR(255) NOT NULL
- model_id: BIGINT UNSIGNED NOT NULL
```
**Primary Key**: `permission_id, model_id, model_type`

#### 3.4 model_has_roles
**Propósito**: Papéis atribuídos a modelos
```sql
- role_id: BIGINT UNSIGNED NOT NULL
- model_type: VARCHAR(255) NOT NULL
- model_id: BIGINT UNSIGNED NOT NULL
```
**Primary Key**: `role_id, model_id, model_type`

#### 3.5 role_has_permissions
**Propósito**: Permissões atribuídas a papéis
```sql
- permission_id: BIGINT UNSIGNED NOT NULL
- role_id: BIGINT UNSIGNED NOT NULL
```
**Primary Key**: `permission_id, role_id`
**Foreign Keys**:
- `permission_id` → `permissions.id` ON DELETE CASCADE
- `role_id` → `roles.id` ON DELETE CASCADE

### 4. Gestão de Projetos Legislativos

#### 4.1 projetos
**Propósito**: Projetos/proposições legislativas
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- titulo: VARCHAR(255) NOT NULL
- numero: VARCHAR(255) NULL
- ano: YEAR NOT NULL
- tipo: ENUM('projeto_lei_ordinaria', 'projeto_lei_complementar', 'emenda_constitucional', 'decreto_legislativo', 'resolucao', 'indicacao', 'requerimento')
- autor_id: BIGINT NOT NULL
- relator_id: BIGINT NULL
- comissao_id: BIGINT UNSIGNED NULL
- status: ENUM('rascunho', 'protocolado', 'em_tramitacao', 'na_comissao', 'em_votacao', 'aprovado', 'rejeitado', 'retirado', 'arquivado') DEFAULT 'rascunho'
- urgencia: ENUM('normal', 'urgente', 'urgentissima') DEFAULT 'normal'
- resumo: TEXT NULL
- ementa: TEXT NOT NULL
- conteudo: LONGTEXT NULL
- version_atual: INTEGER DEFAULT 1
- palavras_chave: TEXT NULL
- observacoes: TEXT NULL
- data_protocolo: DATE NULL
- data_limite_tramitacao: DATE NULL
- numero_protocolo: VARCHAR(255) NULL
- data_assinatura: TIMESTAMP NULL
- ativo: BOOLEAN DEFAULT TRUE
- metadados: JSON NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: status,tipo | autor_id,ano | comissao_id,status | numero_protocolo
**Unique**: `numero, ano, tipo`
**Foreign Keys**:
- `autor_id` → `users.id` ON DELETE CASCADE
- `relator_id` → `users.id` ON DELETE SET NULL

#### 4.2 projeto_versions
**Propósito**: Controle de versões de projetos
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- projeto_id: BIGINT NOT NULL
- version_number: INTEGER NOT NULL
- conteudo: LONGTEXT NOT NULL
- changelog: TEXT NULL
- comentarios: TEXT NULL
- author_id: BIGINT NOT NULL
- tipo_alteracao: ENUM('criacao', 'revisao', 'emenda', 'correcao', 'formatacao') DEFAULT 'revisao'
- is_current: BOOLEAN DEFAULT FALSE
- is_published: BOOLEAN DEFAULT FALSE
- diff_data: JSON NULL
- tamanho_bytes: INTEGER NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: projeto_id,is_current | author_id,created_at
**Unique**: `projeto_id, version_number`
**Foreign Keys**:
- `projeto_id` → `projetos.id` ON DELETE CASCADE
- `author_id` → `users.id` ON DELETE CASCADE

#### 4.3 projeto_anexos
**Propósito**: Anexos de projetos
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- projeto_id: BIGINT NOT NULL
- nome_original: VARCHAR(255) NOT NULL
- nome_arquivo: VARCHAR(255) NOT NULL
- path: VARCHAR(255) NOT NULL
- mime_type: VARCHAR(255) NOT NULL
- tamanho: INTEGER NOT NULL
- tipo: ENUM('documento_base', 'emenda', 'parecer', 'justificativa', 'estudo_tecnico', 'manifestacao', 'outro') DEFAULT 'outro'
- descricao: TEXT NULL
- ordem: INTEGER DEFAULT 0
- uploaded_by: BIGINT NOT NULL
- publico: BOOLEAN DEFAULT TRUE
- ativo: BOOLEAN DEFAULT TRUE
- metadados: JSON NULL
- hash_arquivo: VARCHAR(255) NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: projeto_id,tipo | uploaded_by,created_at | hash_arquivo
**Foreign Keys**:
- `projeto_id` → `projetos.id` ON DELETE CASCADE
- `uploaded_by` → `users.id` ON DELETE CASCADE

#### 4.4 projeto_tramitacao
**Propósito**: Acompanhamento de fluxo de projetos
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- projeto_id: BIGINT NOT NULL
- usuario_id: BIGINT NOT NULL
- status_anterior: ENUM('rascunho', 'enviado', 'em_analise', 'aprovado', 'rejeitado', 'assinado', 'protocolado', 'em_sessao', 'votado') NULL
- status_atual: ENUM('rascunho', 'enviado', 'em_analise', 'aprovado', 'rejeitado', 'assinado', 'protocolado', 'em_sessao', 'votado') NOT NULL
- acao: ENUM('criou', 'enviou', 'analisou', 'aprovou', 'rejeitou', 'assinou', 'protocolou', 'incluiu_sessao', 'votou') NOT NULL
- observacoes: TEXT NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: projeto_id,created_at | usuario_id,acao | status_atual
**Foreign Keys**:
- `projeto_id` → `projetos.id` ON DELETE CASCADE
- `usuario_id` → `users.id` ON DELETE CASCADE

#### 4.5 modelo_projetos
**Propósito**: Modelos de projetos
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- nome: VARCHAR(255) NOT NULL
- descricao: TEXT NULL
- tipo_projeto: ENUM('projeto_lei_ordinaria', 'projeto_lei_complementar', 'emenda_constitucional', 'decreto_legislativo', 'resolucao', 'indicacao', 'requerimento')
- conteudo_modelo: LONGTEXT NOT NULL
- campos_variaveis: JSON NULL
- ativo: BOOLEAN DEFAULT TRUE
- criado_por: BIGINT NOT NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: tipo_projeto | ativo
**Foreign Keys**:
- `criado_por` → `users.id`

#### 4.6 tipo_projetos
**Propósito**: Configuração de tipos de projeto
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- nome: VARCHAR(255) UNIQUE NOT NULL
- descricao: TEXT NULL
- template_conteudo: TEXT NULL
- ativo: BOOLEAN DEFAULT TRUE
- metadados: JSON NULL
- created_at, updated_at: TIMESTAMPS
```
**Índice**: ativo

### 5. Gestão Parlamentar

#### 5.1 parlamentars
**Propósito**: Gestão de membros parlamentares
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- nome: VARCHAR(255) NOT NULL
- nome_politico: VARCHAR(255) NULL
- partido: VARCHAR(50) NOT NULL
- cargo: VARCHAR(100) NOT NULL
- status: VARCHAR(255) DEFAULT 'ativo'
- email: VARCHAR(255) UNIQUE NOT NULL
- cpf: VARCHAR(14) NULL
- telefone: VARCHAR(20) NOT NULL
- data_nascimento: DATE NOT NULL
- profissao: VARCHAR(100) NULL
- escolaridade: VARCHAR(100) NULL
- foto: VARCHAR(255) NULL
- comissoes: JSON NULL
- mandatos: JSON NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: status,partido | nome | cpf

### 6. Extensão do Sistema de Permissões

#### 6.1 screen_permissions
**Propósito**: Gestão de permissões por tela
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- role_name: VARCHAR(255) NOT NULL
- screen_route: VARCHAR(255) NOT NULL
- screen_name: VARCHAR(255) NOT NULL
- screen_module: VARCHAR(255) NOT NULL
- can_access: BOOLEAN DEFAULT FALSE
- can_create: BOOLEAN DEFAULT FALSE
- can_edit: BOOLEAN DEFAULT FALSE
- can_delete: BOOLEAN DEFAULT FALSE
- created_at, updated_at: TIMESTAMPS
```
**Índices**: role_name,screen_route | role_name,screen_module | screen_route,can_access | screen_module,can_access,can_create,can_edit,can_delete
**Unique**: `role_name, screen_route`

#### 6.2 permission_audit_log
**Propósito**: Auditoria de mudanças de permissão
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- user_id: BIGINT UNSIGNED NULL
- admin_user_id: BIGINT UNSIGNED NULL
- action: ENUM('grant', 'revoke', 'modify', 'reset') INDEX
- permission_type: VARCHAR(50) INDEX
- old_value: JSON NULL
- new_value: JSON NULL
- ip_address: VARCHAR(45) NULL
- user_agent: TEXT NULL
- created_at: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```
**Índices**: user_id,action | admin_user_id,created_at | ip_address,created_at | permission_type,created_at
**Foreign Keys**:
- `user_id` → `users.id` ON DELETE SET NULL
- `admin_user_id` → `users.id` ON DELETE SET NULL

#### 6.3 permission_access_log
**Propósito**: Log de acesso de permissões
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- user_id: BIGINT UNSIGNED NULL
- screen_route: VARCHAR(100) INDEX
- action: VARCHAR(20) INDEX
- status: ENUM('granted', 'denied') INDEX
- ip_address: VARCHAR(45) NULL
- user_agent: TEXT NULL
- created_at: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```
**Índices**: user_id,status,created_at | screen_route,status,created_at | status,created_at | ip_address,created_at
**Foreign Keys**:
- `user_id` → `users.id` ON DELETE SET NULL

#### 6.4 user_permission_cache
**Propósito**: Cache de permissões para performance
```sql
- user_id: BIGINT UNSIGNED PRIMARY KEY
- permissions_hash: VARCHAR(64) INDEX
- cached_permissions: JSON NOT NULL
- expires_at: TIMESTAMP INDEX
- created_at, updated_at: TIMESTAMPS
```
**Índices**: expires_at,user_id | permissions_hash,expires_at
**Foreign Keys**:
- `user_id` → `users.id` ON DELETE CASCADE
**Recursos Especiais**:
- MySQL Event: `cleanup_expired_permission_cache` (executa a cada hora)

#### 6.5 permission_performance_log
**Propósito**: Monitoramento de performance do sistema de permissões
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- operation: VARCHAR(50) INDEX
- response_time_ms: DECIMAL(8,3) NOT NULL
- cache_hits: INTEGER DEFAULT 0
- cache_misses: INTEGER DEFAULT 0
- database_queries: INTEGER DEFAULT 0
- metadata: JSON NULL
- created_at: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
```
**Índices**: operation,created_at | response_time_ms,created_at | created_at
**Recursos Especiais**:
- MySQL Event: `cleanup_old_performance_log` (executa diariamente, exclui registros com mais de 30 dias)

### 7. Sistema de Parâmetros (Primeira Geração)

#### 7.1 grupos_parametros
**Propósito**: Grupos de parâmetros (hierárquico)
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- nome: VARCHAR(100) NOT NULL
- codigo: VARCHAR(50) UNIQUE NOT NULL
- descricao: TEXT NULL
- icone: VARCHAR(50) NULL
- cor: VARCHAR(7) NULL (cor hex)
- ordem: INTEGER DEFAULT 0
- ativo: BOOLEAN DEFAULT TRUE
- grupo_pai_id: BIGINT UNSIGNED NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: ativo,ordem | codigo | grupo_pai_id
**Foreign Keys**:
- `grupo_pai_id` → `grupos_parametros.id` ON DELETE SET NULL

#### 7.2 tipos_parametros
**Propósito**: Definição de tipos de parâmetro
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- nome: VARCHAR(100) NOT NULL
- codigo: VARCHAR(50) UNIQUE NOT NULL
- classe_validacao: VARCHAR(255) NULL
- configuracao_padrao: JSON NULL
- ativo: BOOLEAN DEFAULT TRUE
- created_at, updated_at: TIMESTAMPS
```
**Índices**: ativo | codigo

#### 7.3 parametros
**Propósito**: Parâmetros do sistema
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- nome: VARCHAR(150) NOT NULL
- codigo: VARCHAR(100) UNIQUE NOT NULL
- descricao: TEXT NULL
- grupo_parametro_id: BIGINT UNSIGNED NOT NULL
- tipo_parametro_id: BIGINT UNSIGNED NOT NULL
- valor: TEXT NULL
- valor_padrao: TEXT NULL
- configuracao: JSON NULL
- regras_validacao: JSON NULL
- obrigatorio: BOOLEAN DEFAULT FALSE
- editavel: BOOLEAN DEFAULT TRUE
- visivel: BOOLEAN DEFAULT TRUE
- ativo: BOOLEAN DEFAULT TRUE
- ordem: INTEGER DEFAULT 0
- help_text: TEXT NULL
- created_at, updated_at: TIMESTAMPS
```
**Índices**: ativo,visivel,ordem | codigo | grupo_parametro_id | tipo_parametro_id | grupo_parametro_id,ordem
**Foreign Keys**:
- `grupo_parametro_id` → `grupos_parametros.id` ON DELETE CASCADE
- `tipo_parametro_id` → `tipos_parametros.id` ON DELETE CASCADE

#### 7.4 historico_parametros
**Propósito**: Histórico de mudanças de parâmetros
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- parametro_id: BIGINT UNSIGNED NOT NULL
- user_id: BIGINT UNSIGNED NOT NULL
- acao: ENUM('create', 'update', 'delete') NOT NULL
- valor_anterior: TEXT NULL
- valor_novo: TEXT NULL
- dados_contexto: JSON NULL
- ip_address: VARCHAR(45) NULL
- user_agent: TEXT NULL
- data_acao: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- created_at, updated_at: TIMESTAMPS
```
**Índices**: parametro_id,data_acao | user_id | acao | data_acao
**Foreign Keys**:
- `parametro_id` → `parametros.id` ON DELETE CASCADE
- `user_id` → `users.id` ON DELETE CASCADE

### 8. Sistema de Parâmetros (Segunda Geração - Modular)

#### 8.1 parametros_modulos
**Propósito**: Organização de módulos de parâmetros
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- nome: VARCHAR(255) NOT NULL
- descricao: TEXT NULL
- icon: VARCHAR(255) NULL (ícones ki-duotone)
- ordem: INTEGER DEFAULT 0
- ativo: BOOLEAN DEFAULT TRUE
- created_at, updated_at: TIMESTAMPS
```
**Índice**: ativo,ordem

#### 8.2 parametros_submodulos
**Propósito**: Submódulos de parâmetros
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- modulo_id: BIGINT NOT NULL
- nome: VARCHAR(255) NOT NULL
- descricao: TEXT NULL
- tipo: ENUM('form', 'checkbox', 'select', 'toggle', 'custom') NOT NULL
- config: JSON NULL
- ordem: INTEGER DEFAULT 0
- ativo: BOOLEAN DEFAULT TRUE
- created_at, updated_at: TIMESTAMPS
```
**Índice**: modulo_id,ativo,ordem
**Foreign Keys**:
- `modulo_id` → `parametros_modulos.id` ON DELETE CASCADE

#### 8.3 parametros_campos
**Propósito**: Definição de campos de parâmetros
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- submodulo_id: BIGINT NOT NULL
- nome: VARCHAR(255) NOT NULL
- label: VARCHAR(255) NOT NULL
- tipo_campo: ENUM('text', 'email', 'number', 'textarea', 'select', 'checkbox', 'radio', 'file', 'date', 'datetime') NOT NULL
- descricao: TEXT NULL
- obrigatorio: BOOLEAN DEFAULT FALSE
- valor_padrao: TEXT NULL
- opcoes: JSON NULL
- validacao: JSON NULL
- placeholder: VARCHAR(255) NULL
- classe_css: VARCHAR(255) NULL
- ordem: INTEGER DEFAULT 0
- ativo: BOOLEAN DEFAULT TRUE
- created_at, updated_at: TIMESTAMPS
```
**Índice**: submodulo_id,ativo,ordem
**Foreign Keys**:
- `submodulo_id` → `parametros_submodulos.id` ON DELETE CASCADE

#### 8.4 parametros_valores
**Propósito**: Armazenamento de valores de parâmetros
```sql
- id: BIGINT PRIMARY KEY AUTO_INCREMENT
- campo_id: BIGINT NOT NULL
- valor: TEXT NOT NULL
- tipo_valor: VARCHAR(255) DEFAULT 'string'
- user_id: BIGINT NULL
- valido_ate: TIMESTAMP NULL
- created_at, updated_at: TIMESTAMPS
```
**Índice**: campo_id,valido_ate
**Foreign Keys**:
- `campo_id` → `parametros_campos.id` ON DELETE CASCADE
- `user_id` → `users.id` ON DELETE SET NULL

## Resumo de Relacionamentos do Banco

### Principais Cadeias de Relacionamento:

1. **Gestão de Usuários**: `users` ← `parlamentars`, `permissions/roles`
2. **Gestão de Projetos**: `users` → `projetos` → `projeto_versions`, `projeto_anexos`, `projeto_tramitacao`
3. **Sistema de Permissões**: `users` ← `roles` ← `permissions`, `screen_permissions`
4. **Sistema de Parâmetros (Gen 1)**: `grupos_parametros` → `parametros` → `historico_parametros`
5. **Sistema de Parâmetros (Gen 2)**: `parametros_modulos` → `parametros_submodulos` → `parametros_campos` → `parametros_valores`
6. **Sistema de Auditoria**: Todas as tabelas → Tabelas de Audit/Log

### Recursos Principais:
- **Grupos Hierárquicos**: `grupos_parametros` suporta relacionamentos pai-filho
- **Controle de Versões**: Projetos têm histórico completo de versões
- **Trilha de Auditoria**: Log abrangente para permissões e parâmetros
- **Otimização de Performance**: Indexação extensa e tabelas de cache
- **Soft Deletes**: A maioria dos relacionamentos usa CASCADE ou SET NULL para integridade de dados
- **Campos JSON**: Armazenamento flexível de metadados em todo o sistema

## Características Técnicas

### Tipos de Dados Utilizados:
- **BIGINT**: IDs e chaves primárias
- **VARCHAR**: Strings com tamanhos específicos
- **TEXT/LONGTEXT**: Conteúdo extenso
- **JSON**: Metadados e configurações flexíveis
- **ENUM**: Valores controlados com domínio específico
- **BOOLEAN**: Flags de controle
- **TIMESTAMP/DATE**: Controle temporal
- **DECIMAL**: Valores monetários e de precisão

### Padrões de Indexação:
- **Índices Compostos**: Para consultas multi-campo
- **Índices de Performance**: Em campos frequentemente consultados
- **Índices de Foreign Key**: Para otimizar JOINs
- **Índices Temporais**: Para consultas por data/período

### Estratégias de Cache:
- **Cache de Permissões**: `user_permission_cache`
- **Cache de Aplicação**: `cache` e `cache_locks`
- **Cache de Performance**: Logs de performance para monitoramento

Esta estrutura de banco de dados suporta um sistema abrangente de gestão legislativa com gerenciamento robusto de permissões, configuração de parâmetros, gestão de ciclo de vida de projetos e capacidades extensas de auditoria.