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
- **Exemplo**: Templates, Sistema, Segurança
- **Campos principais**:
  - `nome`: Nome do módulo
  - `descricao`: Descrição detalhada
  - `icon`: Ícone do módulo (classes CSS)
  - `ordem`: Ordem de exibição
  - `ativo`: Status ativo/inativo

### 2. Submódulos (`parametros_submodulos`)
- **Propósito**: Subdivide módulos em categorias específicas
- **Exemplo**: No módulo Templates: Cabeçalho, Rodapé, Marca D'água
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

## Exemplo Prático: Módulo Templates

O módulo Templates (ID: 6) é um exemplo completo de implementação:

### Estrutura do Módulo Templates

```
Templates (Módulo)
├── Cabeçalho (Submódulo)
│   ├── cabecalho_imagem (Campo: file)
│   ├── cabecalho_altura (Campo: number)
│   └── cabecalho_alinhamento (Campo: select)
├── Rodapé (Submódulo)
│   ├── rodape_texto (Campo: text)
│   ├── rodape_altura (Campo: number)
│   └── rodape_fonte_tamanho (Campo: number)
├── Marca D'água (Submódulo)
│   ├── marca_dagua_texto (Campo: text)
│   ├── marca_dagua_opacidade (Campo: number)
│   └── marca_dagua_posicao (Campo: select)
└── Texto Padrão (Submódulo)
    ├── texto_fonte_familia (Campo: select)
    ├── texto_fonte_tamanho (Campo: number)
    └── texto_espacamento_linha (Campo: number)
```

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

// Visualizar módulo específico (ex: Templates)
GET /admin/parametros/6

// Configurar submódulo
GET /admin/parametros/templates/cabecalho
POST /admin/parametros/templates/cabecalho/salvar

// APIs
GET /api/parametros/{modulo}/{submodulo}
POST /api/parametros/{modulo}/{submodulo}/salvar
```

## Exemplo de Uso: Configurando Cabeçalho de Templates

1. **Acesso**: Navegue para `/admin/parametros/6`
2. **Seleção**: Clique no card "Cabeçalho"
3. **Configuração**: 
   - Upload da imagem do cabeçalho
   - Definir altura (em pixels)
   - Escolher alinhamento (esquerda, centro, direita)
4. **Salvamento**: Clique em "Salvar Configurações"
5. **Aplicação**: Os templates gerados utilizarão automaticamente essas configurações

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

## Conclusão

O sistema de parâmetros do LegisInc oferece uma solução robusta e flexível para gerenciar configurações dinâmicas. Sua arquitetura modular permite fácil extensão e manutenção, enquanto recursos como versionamento, cache e auditoria garantem confiabilidade e performance.