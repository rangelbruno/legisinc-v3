# Documentação de Parâmetros do Sistema LegisInc

## Visão Geral

O sistema LegisInc utiliza um sistema modular de parâmetros para configurar diversos aspectos da aplicação. Os parâmetros estão organizados em **módulos** e **submódulos**, permitindo uma gestão organizada e escalável das configurações.

## Estrutura de Parâmetros

### 1. Módulo: Templates
**Descrição:** Configurações e parâmetros para templates de documentos  
**Rota:** `/admin/parametros/1`  
**Ícone:** ki-document  

#### 1.1 Submódulo: Cabeçalho
**Tipo:** form  
**Descrição:** Configurações do cabeçalho dos templates

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Valor Atual |
|-------|-------|------|-------------|--------------|-------------|
| cabecalho_imagem | Logo/Brasão da Câmara | file | Não | - | - |
| cabecalho_nome_camara | Nome da Câmara | text | Sim | CÂMARA MUNICIPAL | CÂMARA MUNICIPAL DE SÃO PAULO |
| cabecalho_endereco | Endereço | textarea | Não | - | Viaduto Jacareí, 100<br>Bela Vista - São Paulo/SP<br>CEP: 01319-900 |
| cabecalho_telefone | Telefone | text | Não | - | (11) 3396-4000 |
| cabecalho_website | Website | text | Não | - | www.saopaulo.sp.leg.br |

#### 1.2 Submódulo: Rodapé
**Tipo:** form  
**Descrição:** Configurações do rodapé dos templates

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Valor Atual |
|-------|-------|------|-------------|--------------|-------------|
| rodape_texto | Texto do Rodapé | textarea | Não | - | Documento oficial da Câmara Municipal |
| rodape_numeracao | Exibir Numeração de Página | checkbox | Não | 1 | 1 |

#### 1.3 Submódulo: Variáveis Dinâmicas
**Tipo:** form  
**Descrição:** Variáveis que podem ser usadas nos templates

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Valor Atual |
|-------|-------|------|-------------|--------------|-------------|
| var_prefixo_numeracao | Prefixo de Numeração | text | Não | PROP | PROP |
| var_formato_data | Formato de Data | select | Sim | d/m/Y | d/m/Y |
| var_assinatura_padrao | Texto de Assinatura Padrão | textarea | Não | Sala das Sessões... | Sala das Sessões, em _____ de _____________ de _______.<br><br>_________________________________<br>Vereador(a) |

#### 1.4 Submódulo: Formatação
**Tipo:** form  
**Descrição:** Configurações de formatação dos documentos

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Valor Atual |
|-------|-------|------|-------------|--------------|-------------|
| format_fonte | Fonte Padrão | select | Sim | Arial | Arial |
| format_tamanho_fonte | Tamanho da Fonte | number | Sim | 12 | 12 |
| format_espacamento | Espaçamento entre Linhas | select | Sim | 1.5 | 1.5 |
| format_margens | Margens (cm) | text | Sim | 2.5, 2.5, 3, 2 | 2.5, 2.5, 3, 2 |

---

### 2. Módulo: Dados Gerais
**Descrição:** Informações gerais da Câmara Municipal  
**Rota:** `/parametros-dados-gerais-camara`  
**Ícone:** ki-building  
**Controller:** `DadosGeraisCamaraController`

#### 2.1 Submódulo: Identificação
**Tipo:** form  
**Descrição:** Dados de identificação da Câmara

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Descrição |
|-------|-------|------|-------------|--------------|-----------|
| nome_camara | Nome da Câmara | text | Sim | Câmara Municipal | Nome completo da Câmara Municipal |
| sigla_camara | Sigla da Câmara | text | Sim | CM | Sigla ou abreviação da Câmara |
| cnpj | CNPJ | text | Não | - | CNPJ da Câmara Municipal |

#### 2.2 Submódulo: Endereço
**Tipo:** form  
**Descrição:** Informações de localização

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Descrição |
|-------|-------|------|-------------|--------------|-----------|
| endereco | Endereço | text | Sim | - | Logradouro da Câmara |
| numero | Número | text | Não | - | Número do endereço |
| complemento | Complemento | text | Não | - | Complemento do endereço |
| bairro | Bairro | text | Sim | - | Bairro da Câmara |
| cidade | Cidade | text | Sim | - | Cidade onde está localizada |
| estado | Estado | text | Sim | SP | UF do estado |
| cep | CEP | text | Sim | - | CEP do endereço |

#### 2.3 Submódulo: Contatos
**Tipo:** form  
**Descrição:** Meios de contato com a Câmara

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Descrição |
|-------|-------|------|-------------|--------------|-----------|
| telefone | Telefone Principal | text | Sim | - | Telefone principal de contato |
| telefone_secundario | Telefone Secundário | text | Não | - | Telefone alternativo |
| email_institucional | E-mail Institucional | email | Sim | - | E-mail oficial da Câmara |
| email_contato | E-mail de Contato | email | Não | - | E-mail para contato público |
| website | Website | text | Não | - | Site oficial da Câmara |

#### 2.4 Submódulo: Funcionamento
**Tipo:** form  
**Descrição:** Horários e dias de funcionamento

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Descrição |
|-------|-------|------|-------------|--------------|-----------|
| horario_funcionamento | Horário de Funcionamento | text | Sim | Segunda a Sexta, 8h às 17h | Horário de funcionamento administrativo |
| horario_atendimento | Horário de Atendimento | text | Sim | Segunda a Sexta, 8h às 16h | Horário de atendimento ao público |

#### 2.5 Submódulo: Gestão
**Tipo:** form  
**Descrição:** Informações sobre a gestão atual

| Campo | Label | Tipo | Obrigatório | Valor Padrão | Descrição |
|-------|-------|------|-------------|--------------|-----------|
| presidente_nome | Nome do Presidente | text | Sim | - | Nome completo do presidente da Câmara |
| presidente_partido | Partido do Presidente | text | Sim | - | Partido político do presidente |
| legislatura_atual | Legislatura Atual | text | Sim | 2021-2024 | Período da legislatura atual |
| numero_vereadores | Número de Vereadores | number | Sim | 9 | Quantidade total de vereadores |

---

## Arquitetura do Sistema de Parâmetros

### Estrutura do Banco de Dados

#### Tabelas Principais:

1. **parametros_modulos**
   - Armazena os módulos principais (Templates, Dados Gerais, etc.)
   - Campos: id, nome, descricao, icon, ordem, ativo

2. **parametros_submodulos**  
   - Armazena os submódulos de cada módulo
   - Campos: id, modulo_id, nome, descricao, tipo, ordem, ativo
   - Tipos válidos: form, checkbox, select, toggle, custom

3. **parametros_campos**
   - Define os campos de cada submódulo
   - Campos: id, submodulo_id, nome, label, tipo_campo, descricao, obrigatorio, valor_padrao, opcoes, validacao, ordem, ativo
   - Tipos de campo: text, email, number, textarea, select, checkbox, radio, file, date, datetime

4. **parametros_valores**
   - Armazena os valores atuais de cada campo
   - Campos: id, campo_id, valor, created_at, updated_at

### Controllers e Services

#### DadosGeraisCamaraController
- **Localização:** `/app/Http/Controllers/DadosGeraisCamaraController.php`
- **Responsabilidade:** Gerenciar os dados gerais da Câmara Municipal
- **Métodos principais:**
  - `index()`: Exibe o formulário com as configurações atuais
  - `store(Request $request)`: Salva as configurações
  - `obterConfiguracoes()`: Busca as configurações atuais do banco

#### ParametroService
- **Localização:** `/app/Services/Parametro/ParametroService.php`
- **Responsabilidade:** Gerenciar operações CRUD dos parâmetros
- **Métodos principais:**
  - `salvarValor($modulo, $submodulo, $campo, $valor)`: Salva um valor de parâmetro
  - `obterValor($modulo, $submodulo, $campo)`: Obtém o valor de um parâmetro
  - `listarParametros($filtros)`: Lista parâmetros com filtros

### Rotas

#### Rotas do Módulo Templates:
- GET `/admin/parametros/1` - Lista todos os parâmetros de templates
- GET `/parametros-templates-cabecalho` - Configurações do cabeçalho
- POST `/parametros-templates-cabecalho` - Salva configurações do cabeçalho

#### Rotas do Módulo Dados Gerais:
- GET `/parametros-dados-gerais-camara` - Formulário de dados gerais
- POST `/parametros-dados-gerais-camara` - Salva dados gerais

### Views

#### Dados Gerais da Câmara
- **Localização:** `/resources/views/modules/parametros/dados-gerais-camara.blade.php`
- **Componentes:**
  - Formulário dividido em abas (Identificação, Endereço, Contatos, Funcionamento, Gestão)
  - Validação client-side e server-side
  - Feedback visual de sucesso/erro

#### Templates
- **Localização:** `/resources/views/modules/parametros/templates/`
- **Componentes:**
  - Formulários específicos para cada submódulo
  - Upload de arquivos para logos/brasões
  - Pré-visualização de configurações

---

## Como Usar os Parâmetros

### 1. Acessando Valores no Código

```php
// Exemplo de uso no controller
use App\Services\Parametro\ParametroService;

public function __construct(ParametroService $parametroService) 
{
    $this->parametroService = $parametroService;
}

// Obter valor de um parâmetro
$nomeCamara = $this->parametroService->obterValor('Dados Gerais', 'Identificação', 'nome_camara');

// Salvar valor de um parâmetro
$this->parametroService->salvarValor('Dados Gerais', 'Identificação', 'nome_camara', 'Nova Câmara Municipal');
```

### 2. Usando em Templates Blade

```blade
{{-- Exemplo de uso em views --}}
@inject('parametroService', 'App\Services\Parametro\ParametroService')

<h1>{{ $parametroService->obterValor('Dados Gerais', 'Identificação', 'nome_camara') }}</h1>
```

### 3. Validações

Todos os campos possuem validações configuráveis:
- **Obrigatório**: Define se o campo deve ser preenchido
- **Tipo de campo**: Valida formato (email, número, etc.)
- **Validação customizada**: Regras específicas em JSON

---

## Manutenção e Extensão

### Adicionando Novo Módulo

1. Inserir registro em `parametros_modulos`
2. Criar submódulos em `parametros_submodulos`
3. Definir campos em `parametros_campos`
4. Criar controller específico se necessário
5. Adicionar rotas em `/routes/web.php`
6. Criar views correspondentes

### Adicionando Novo Campo

```sql
INSERT INTO parametros_campos (
    submodulo_id, nome, label, tipo_campo, 
    descricao, obrigatorio, valor_padrao, 
    ordem, ativo, created_at, updated_at
) VALUES (
    [ID_SUBMODULO], 'nome_campo', 'Label do Campo', 'text',
    'Descrição do campo', true, 'Valor padrão',
    [ORDEM], true, NOW(), NOW()
);
```

### Backup de Configurações

Para fazer backup dos parâmetros atuais:

```sql
-- Exportar valores atuais
SELECT 
    m.nome as modulo,
    s.nome as submodulo,
    c.nome as campo,
    pv.valor
FROM parametros_valores pv
JOIN parametros_campos c ON pv.campo_id = c.id
JOIN parametros_submodulos s ON c.submodulo_id = s.id
JOIN parametros_modulos m ON s.modulo_id = m.id
ORDER BY m.ordem, s.ordem, c.ordem;
```

---

## Observações Importantes

1. **Auto-login temporário**: Atualmente configurado para desenvolvimento nas rotas
2. **Valores padrão**: Sempre definir valores padrão para campos obrigatórios
3. **Cache**: Considerar implementar cache para parâmetros frequentemente acessados
4. **Auditoria**: Sistema registra todas as alterações com timestamps
5. **Permissões**: Verificar roles e permissões antes de permitir edição

---

## Próximos Passos

1. Implementar cache de parâmetros
2. Adicionar histórico de alterações
3. Criar interface de importação/exportação
4. Implementar validações avançadas
5. Adicionar parâmetros de e-mail e notificações
6. Criar parâmetros para integração com APIs externas

---

*Última atualização: 11/08/2025*