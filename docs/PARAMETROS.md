# Sistema de Parâmetros - Documentação

## Visão Geral

O sistema de parâmetros do LegisInc é uma solução modular e flexível para gerenciar configurações dinâmicas da aplicação. Ele permite que administradores configurem diversos aspectos do sistema sem necessidade de alterar código.

## Estrutura Hierárquica

O sistema está organizado em uma estrutura de 4 níveis:

```
Módulo
└── Submódulo
    └── Campo
        └── Valor
```

### 1. Módulos (`parametros_modulos`)
- **Propósito**: Agrupa configurações relacionadas de alto nível
- **Módulos Ativos**:
  1. **Dados Gerais** (`id: 7`) - Dados gerais da câmara municipal (ícone: ki-bank)
  2. **IA** (`id: 2`) - Configurações de Inteligência Artificial (ícone: ki-brain) 
  3. **Templates** (`id: 1`) - Configurações e parâmetros para templates de documentos (ícone: ki-document)
- **Campos principais**:
  - `nome`: Nome do módulo
  - `descricao`: Descrição detalhada
  - `icon`: Ícone do módulo (classes CSS)
  - `ordem`: Ordem de exibição
  - `ativo`: Status ativo/inativo

### 2. Submódulos (`parametros_submodulos`)
- **Propósito**: Subdivide módulos em categorias específicas
- **Submódulos por Módulo**:
  
  **Dados Gerais (7 submódulos)**:
  1. **Identificação** - Dados de identificação da câmara
  2. **Endereço** - Endereço completo da câmara
  3. **Contatos** - Informações de contato
  4. **Funcionamento** - Horários de funcionamento
  5. **Gestão** - Informações da gestão atual
  
  **Templates (4 submódulos)**:
  1. **Cabeçalho** - Configurações do cabeçalho dos templates
  2. **Rodapé** - Configurações do rodapé dos templates
  3. **Variáveis Dinâmicas** - Variáveis que podem ser usadas nos templates
  4. **Formatação** - Configurações de formatação dos documentos
  
  **IA (sem submódulos)** - Configurado diretamente via tabela `ai_configurations`

- **Campos principais**:
  - `modulo_id`: Referência ao módulo pai
  - `nome`: Nome do submódulo
  - `descricao`: Descrição da funcionalidade
  - `ordem`: Ordem dentro do módulo
  - `ativo`: Status

### 3. Campos (`parametros_campos`)
- **Propósito**: Define os parâmetros configuráveis específicos
- **Exemplo**: cabecalho_imagem, rodape_texto, marca_dagua_opacidade
- **Campos principais**:
  - `submodulo_id`: Referência ao submódulo
  - `nome`: Identificador único do campo
  - `label`: Rótulo exibido na interface
  - `tipo_campo`: Tipo de input (text, file, select, etc.)
  - `obrigatorio`: Se o campo é obrigatório
  - `validacao_regras`: Regras de validação JSON
  - `opcoes`: Opções para campos select/radio
  - `valor_padrao`: Valor padrão
  - `placeholder`: Texto de ajuda
  - `descricao`: Descrição detalhada
  - `classe_css`: Classes CSS customizadas

### 4. Valores (`parametros_valores`)
- **Propósito**: Armazena os valores configurados pelos usuários
- **Campos principais**:
  - `campo_id`: Referência ao campo
  - `valor_texto`: Para valores de texto
  - `valor_numero`: Para valores numéricos
  - `valor_boolean`: Para valores booleanos
  - `valor_json`: Para valores complexos (arrays, objetos)
  - `valor_arquivo`: Para caminhos de arquivos
  - `valido_desde`: Data de início da validade
  - `valido_ate`: Data de fim da validade (null = ativo)
  - `user_id`: Usuário que definiu o valor

## Detalhamento dos Módulos Implementados

### Módulo 1: Dados Gerais da Câmara (ID: 7)

Responsável por armazenar as informações institucionais da câmara municipal.

#### Submódulo: Identificação
- `nome_camara` (text, obrigatório) - Nome da Câmara
- `sigla_camara` (text, obrigatório) - Sigla
- `cnpj` (text, opcional) - CNPJ

#### Submódulo: Endereço
- `endereco` (text, obrigatório) - Endereço
- `numero` (text, opcional) - Número
- `complemento` (text, opcional) - Complemento
- `bairro` (text, obrigatório) - Bairro
- `cidade` (text, obrigatório) - Cidade
- `estado` (text, obrigatório) - Estado
- `cep` (text, obrigatório) - CEP

#### Submódulo: Contatos
- `telefone` (text, obrigatório) - Telefone Principal
- `telefone_secundario` (text, opcional) - Telefone Secundário
- `email_institucional` (email, obrigatório) - E-mail Institucional
- `email_contato` (email, opcional) - E-mail de Contato
- `website` (text, opcional) - Website

#### Submódulo: Funcionamento
- `horario_funcionamento` (text, obrigatório) - Horário de Funcionamento
- `horario_atendimento` (text, obrigatório) - Horário de Atendimento

#### Submódulo: Gestão
- `presidente_nome` (text, obrigatório) - Nome do Presidente
- `presidente_partido` (text, obrigatório) - Partido do Presidente
- `legislatura_atual` (text, obrigatório) - Legislatura Atual
- `numero_vereadores` (number, obrigatório) - Número de Vereadores

### Módulo 2: Configurações da IA (ID: 2)

Gerencia as configurações de Inteligência Artificial através da tabela `ai_configurations`.

#### Configurações Disponíveis:
- `name` - Nome da configuração
- `provider` - Provedor (google, openai, etc.)
- `api_key` - Chave da API (criptografada)
- `model` - Modelo específico (ex: gemini-1.5-flash)
- `base_url` - URL base do serviço
- `max_tokens` - Limite máximo de tokens
- `temperature` - Criatividade das respostas (0.0 - 1.0)
- `daily_token_limit` - Limite diário de tokens
- `cost_per_1k_tokens` - Custo por mil tokens

**Configuração Atual:**
- Google Gemini Pro (gemini-1.5-flash)
- Limite: 100.000 tokens/dia
- Temperatura: 0.70
- Custo: $0.0005 por 1k tokens

### Módulo 3: Templates (ID: 1)

Configurações para geração de documentos.

#### Submódulo: Cabeçalho
- `cabecalho_imagem` (file, opcional) - Logo/Brasão da Câmara
- `cabecalho_nome_camara` (text, obrigatório) - Nome da Câmara (padrão: "CÂMARA MUNICIPAL")
- `cabecalho_endereco` (textarea, opcional) - Endereço
- `cabecalho_telefone` (text, opcional) - Telefone
- `cabecalho_website` (text, opcional) - Website

#### Submódulo: Rodapé
- `rodape_texto` (textarea, opcional) - Texto do Rodapé
- `rodape_numeracao` (checkbox, opcional) - Exibir Numeração de Página (padrão: sim)

#### Submódulo: Variáveis Dinâmicas
- `var_prefixo_numeracao` (text, opcional) - Prefixo de Numeração (padrão: "PROP")
- `var_formato_data` (select, obrigatório) - Formato de Data (padrão: "d/m/Y")
- `var_assinatura_padrao` (textarea, opcional) - Texto de Assinatura Padrão

#### Submódulo: Formatação
- `format_fonte` (select, obrigatório) - Fonte Padrão (padrão: "Arial")
- `format_tamanho_fonte` (number, obrigatório) - Tamanho da Fonte (padrão: 12)
- `format_espacamento` (select, obrigatório) - Espaçamento entre Linhas (padrão: 1.5)
- `format_margens` (text, obrigatório) - Margens em cm (padrão: "2.5, 2.5, 3, 2")

## Exemplo Prático: Módulo Templates

O módulo Templates (ID: 1) é um exemplo completo de implementação:

### Estrutura do Módulo Templates

Conforme detalhado na seção anterior, o módulo Templates possui 4 submódulos:
- **Cabeçalho**: Logo, nome, endereço e contatos
- **Rodapé**: Texto e numeração de páginas
- **Variáveis Dinâmicas**: Prefixos, formatos e assinaturas
- **Formatação**: Fontes, tamanhos, espaçamento e margens

## Fluxo de Funcionamento

### 1. Acesso aos Parâmetros
```php
// Via Service
$parametroService = app(ParametroService::class);
$valor = $parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_imagem');
```

### 2. Salvamento de Valores
```php
$valores = [
    'cabecalho_imagem' => '/uploads/logo.png',
    'cabecalho_altura' => 150,
    'cabecalho_alinhamento' => 'center'
];

$parametroService->salvarValores($submoduloId, $valores, auth()->id());
```

### 3. Validação
- Validações são definidas no campo `validacao_regras` como JSON
- O sistema valida automaticamente antes de salvar
- Exemplo de regras:
```json
{
    "required": true,
    "max": 255,
    "mimes": ["jpg", "png", "pdf"]
}
```

## Arquitetura MVC

### Models
- `ParametroModulo`: Modelo para módulos
- `ParametroSubmodulo`: Modelo para submódulos
- `ParametroCampo`: Modelo para campos
- `ParametroValor`: Modelo para valores

### Controllers
- `ParametroController`: Controller principal
- `ModuloParametroController`: Gerencia módulos
- `SubmoduloParametroController`: Gerencia submódulos
- `CampoParametroController`: Gerencia campos

### Services
- `ParametroService`: Lógica de negócios principal
- `ValidacaoParametroService`: Validação de dados
- `CacheParametroService`: Gerenciamento de cache
- `AuditoriaParametroService`: Registro de auditoria
- `ConfiguracaoParametroService`: Configurações específicas

### Views
- `/modules/parametros/`: Views principais
- `/modules/parametros/templates/`: Views específicas de templates
- Cada submódulo tem sua própria view personalizada

## Características Importantes

### 1. Versionamento de Valores
- Valores antigos são mantidos com `valido_ate` preenchido
- Permite rastreabilidade e auditoria
- Possibilita rollback se necessário

### 2. Cache Inteligente
- Cache de 1 hora por padrão
- Invalidação automática ao salvar novos valores
- Melhora performance significativamente

### 3. Validação em Múltiplas Camadas
- Validação no frontend (JavaScript)
- Validação no backend (Laravel)
- Regras customizáveis por campo

### 4. Tipos de Campos Suportados
- `text`: Campo de texto simples
- `textarea`: Área de texto
- `number`: Campo numérico
- `select`: Lista de seleção
- `radio`: Botões de rádio
- `checkbox`: Caixas de seleção
- `file`: Upload de arquivo
- `color`: Seletor de cor
- `date`: Seletor de data
- `datetime`: Seletor de data e hora
- `json`: Editor JSON

### 5. Segurança
- Controle de acesso por roles
- Auditoria completa de mudanças
- Sanitização de inputs
- Validação rigorosa

## Rotas Disponíveis

```php
// Listagem de módulos
GET /admin/parametros

// Módulo Dados Gerais (ID: 7)
GET /admin/parametros/7
GET /admin/parametros/dados-gerais/identificacao
GET /admin/parametros/dados-gerais/endereco
GET /admin/parametros/dados-gerais/contatos
GET /admin/parametros/dados-gerais/funcionamento
GET /admin/parametros/dados-gerais/gestao

// Módulo IA (ID: 2)
GET /admin/ai-configurations
POST /admin/ai-configurations

// Módulo Templates (ID: 1)
GET /admin/parametros/1
GET /admin/parametros/templates/cabecalho
GET /admin/parametros/templates/rodape
GET /admin/parametros/templates/variaveis-dinamicas
GET /admin/parametros/templates/formatacao

// APIs gerais
GET /api/parametros/{modulo}/{submodulo}
POST /api/parametros/{modulo}/{submodulo}/salvar
```

## Exemplos de Uso dos Módulos

### Configurando Dados Gerais da Câmara

1. **Acesso**: Navegue para `/admin/parametros/7`
2. **Identificação**: Preencha nome, sigla e CNPJ da câmara
3. **Endereço**: Complete o endereço completo
4. **Contatos**: Configure telefones, e-mails e website
5. **Funcionamento**: Defina horários de funcionamento e atendimento
6. **Gestão**: Informe dados do presidente e legislatura atual

### Configurando IA

1. **Acesso**: Navegue para `/admin/ai-configurations`
2. **Provedor**: Selecione o provedor de IA (Google, OpenAI, etc.)
3. **API Key**: Configure a chave de acesso
4. **Modelo**: Escolha o modelo específico
5. **Limites**: Defina limites de tokens e custos
6. **Teste**: Valide a configuração

### Configurando Templates

1. **Acesso**: Navegue para `/admin/parametros/1`
2. **Cabeçalho**: Upload do logo, nome da câmara, endereço e contatos
3. **Rodapé**: Texto do rodapé e configuração de numeração
4. **Variáveis Dinâmicas**: Prefixos, formatos de data e assinaturas padrão
5. **Formatação**: Fonte, tamanho, espaçamento e margens dos documentos

## Parametrização Dinâmica

O sistema suporta variáveis dinâmicas nos valores dos parâmetros:

```
{{usuario_nome}} - Nome do usuário atual
{{data_atual}} - Data atual formatada
{{hora_atual}} - Hora atual
{{sistema_nome}} - Nome do sistema
```

Essas variáveis são substituídas automaticamente quando o parâmetro é utilizado.

## Boas Práticas

1. **Nomenclatura**: Use nomes descritivos e padronizados
   - Formato: `contexto_elemento_propriedade`
   - Exemplo: `cabecalho_imagem_altura`

2. **Validação**: Sempre defina regras de validação apropriadas

3. **Documentação**: Preencha descrições detalhadas nos campos

4. **Cache**: Use o cache service para melhor performance

5. **Auditoria**: Sempre registre mudanças importantes

## Troubleshooting

### Problema: Valor não está sendo salvo
- Verifique as regras de validação
- Confirme que o campo existe e está ativo
- Verifique logs em `storage/logs/`

### Problema: Cache não atualiza
- Execute: `php artisan cache:clear`
- Verifique configuração de cache em `.env`

### Problema: Parâmetro não aparece na interface
- Confirme que módulo e submódulo estão ativos
- Verifique permissões do usuário
- Confirme ordenação dos elementos

## Resumo dos Módulos Implementados

| Módulo | ID | Submódulos | Campos | Funcionalidade |
|--------|----|-----------:|-------:|----------------|
| **Dados Gerais** | 7 | 5 | 21 | Informações institucionais da câmara |
| **IA** | 2 | 0 | - | Configurações de Inteligência Artificial |
| **Templates** | 1 | 4 | 14 | Configurações para geração de documentos |
| **Total** | - | **9** | **35** | - |

### Estatísticas:
- **3 módulos ativos** com configurações específicas
- **35 campos de parâmetros** distribuídos em 9 submódulos
- **1 configuração de IA** ativa (Google Gemini Pro)
- **Sistema completo** de versionamento, cache e auditoria

## Conclusão

O sistema de parâmetros do LegisInc oferece uma solução robusta e flexível para gerenciar configurações dinâmicas. Com 3 módulos principais cobrindo desde dados institucionais até configurações de IA e templates, o sistema permite total personalização da aplicação sem necessidade de alterações no código. Sua arquitetura modular facilita extensão e manutenção, enquanto recursos como versionamento, cache e auditoria garantem confiabilidade e performance.