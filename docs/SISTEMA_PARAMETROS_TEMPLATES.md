# Sistema de Parâmetros Modulares para Templates - LegisInc

## Resumo da Implementação

Foi implementado um sistema completo de parâmetros modulares para templates no LegisInc, permitindo que as configurações dos documentos sejam gerenciadas dinamicamente através de uma interface administrativa.

## Estrutura Implementada

### 1. Banco de Dados
- **parametros_modulos**: Armazena módulos de parâmetros (Templates, Dados da Câmara, etc.)
- **parametros_submodulos**: Subseções dentro de cada módulo
- **parametros_campos**: Campos específicos com tipos e validações
- **parametros_valores**: Valores atuais de cada campo

### 2. Models
- `ParametroModulo`: Gerencia módulos de parâmetros
- `ParametroSubmodulo`: Gerencia submódulos
- `ParametroCampo`: Gerencia campos individuais com tipos e validações
- `ParametroValor`: Armazena e formata valores dos campos

### 3. Serviços
- `TemplateParametrosService`: Processamento e substituição de variáveis nos templates
- Integração com `OnlyOfficeService` para aplicação de parâmetros

### 4. Interface Administrativa
- **Rota**: `/admin/templates/parametros`
- Formulário para edição de todos os parâmetros do módulo Templates
- Teste de substituição de variáveis em tempo real
- Reset para valores padrão

## Módulo Templates Configurado

O seeder criou automaticamente os seguintes submódulos:

### Cabeçalho
- Logo/Brasão da Câmara (arquivo)
- Nome da Câmara (texto)
- Endereço (textarea)
- Telefone (texto)
- Website (texto)

### Rodapé
- Texto do Rodapé (textarea)
- Exibir Numeração de Página (checkbox)

### Variáveis Dinâmicas
- Prefixo de Numeração (texto)
- Formato de Data (select)
- Texto de Assinatura Padrão (textarea)

### Formatação
- Fonte Padrão (select)
- Tamanho da Fonte (number)
- Espaçamento entre Linhas (select)
- Margens (texto)

## Variáveis Disponíveis

O sistema suporta 24+ variáveis que são automaticamente substituídas:

### Dados da Proposição
- `${numero_proposicao}` - Número da proposição
- `${tipo_proposicao}` - Tipo da proposição
- `${ementa}` - Ementa da proposição
- `${texto}` - Texto principal
- `${justificativa}` - Justificativa
- `${ano}` - Ano da proposição
- `${protocolo}` - Número do protocolo

### Dados do Autor
- `${autor_nome}` - Nome do autor
- `${autor_cargo}` - Cargo do autor
- `${autor_partido}` - Partido do autor

### Datas
- `${data_atual}` - Data atual
- `${data_criacao}` - Data de criação
- `${data_protocolo}` - Data do protocolo
- `${dia}` - Dia atual
- `${mes}` - Mês atual
- `${ano_atual}` - Ano atual
- `${mes_extenso}` - Mês por extenso

### Dados da Câmara
- `${nome_camara}` - Nome da Câmara
- `${municipio}` - Nome do município
- `${endereco_camara}` - Endereço da Câmara
- `${telefone_camara}` - Telefone da Câmara
- `${website_camara}` - Website da Câmara

### Formatação
- `${assinatura_padrao}` - Área de assinatura
- `${rodape}` - Texto do rodapé

## Como Usar

### Para Administradores
1. Acesse `/admin/templates`
2. Clique em "Parâmetros"
3. Configure os valores desejados
4. Salve as alterações
5. Use o testador de variáveis para verificar

### Para Desenvolvedores
```php
// Injetar serviço
$parametrosService = app(TemplateParametrosService::class);

// Processar template com variáveis
$textoProcessado = $parametrosService->processarTemplate($template, [
    'proposicao' => $proposicao,
    'autor' => $autor,
    'variaveis' => $variaveisCustomizadas
]);
```

### Em Templates OnlyOffice
- Use as variáveis diretamente no texto: `${nome_camara}`
- As variáveis são substituídas automaticamente durante a criação
- Parâmetros são aplicados quando o template é aberto no OnlyOffice

## Integração com OnlyOffice

O sistema se integra automaticamente com o OnlyOffice:
1. Quando uma proposição é criada, os parâmetros são aplicados
2. O `ProposicaoController` usa o `TemplateParametrosService`
3. As variáveis são substituídas antes do documento ser aberto
4. O resultado é aplicado no template que vai para o OnlyOffice

## Cache e Performance

- Os parâmetros são armazenados em cache por 1 hora
- Cache é limpo automaticamente ao salvar parâmetros
- Método `limparCache()` disponível para limpeza manual

## Testes

O sistema foi testado com o script `/scripts/test-template-parametros.php` que demonstra:
- Carregamento de parâmetros do banco
- Substituição de variáveis
- Processamento completo de template

### Resultado do Teste
```
Template Original:
${nome_camara}
${endereco_camara}
PROPOSIÇÃO Nº ${numero_proposicao}
Autor: ${autor_nome}

Template Processado:
CÂMARA MUNICIPAL DE SÃO PAULO
Viaduto Jacareí, 100
Bela Vista - São Paulo/SP
PROPOSIÇÃO Nº TEST-001/2025
Autor: João da Silva
```

## Extensibilidade

O sistema foi projetado para ser facilmente extensível:
- Novos módulos podem ser adicionados via seeder
- Novos tipos de campo suportados
- Validações customizadas por campo
- Cache configurável
- API de variáveis expansível

## Arquivos Principais

### Controllers
- `app/Http/Controllers/Admin/ParametrosTemplatesController.php`
- Integração em `app/Http/Controllers/ProposicaoController.php`

### Services
- `app/Services/Template/TemplateParametrosService.php`

### Models
- `app/Models/Parametro/ParametroModulo.php`
- `app/Models/Parametro/ParametroSubmodulo.php`
- `app/Models/Parametro/ParametroCampo.php`
- `app/Models/Parametro/ParametroValor.php`

### Views
- `resources/views/admin/templates/parametros.blade.php`

### Migrations
- `database/migrations/2025_08_06_100000_create_parametros_modulos_table.php`
- `database/migrations/2025_08_06_100001_create_parametros_submodulos_table.php`
- `database/migrations/2025_08_06_100002_create_parametros_campos_table.php`
- `database/migrations/2025_08_06_100003_create_parametros_valores_table.php`

### Seeders
- `database/seeders/ParametrosTemplatesSeeder.php`

### Routes
- Adicionadas em `routes/web.php` dentro do grupo `admin/templates`

O sistema está totalmente funcional e pronto para uso em produção.