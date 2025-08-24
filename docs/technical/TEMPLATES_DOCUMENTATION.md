# Documentação dos Templates - Sistema LegisInc

## Visão Geral

O sistema de templates do LegisInc é um framework sofisticado de geração de documentos que suporta múltiplos tipos de templates, variáveis dinâmicas e geração automática de documentos para diferentes tipos de proposições. O sistema integra-se com OnlyOffice para edição de documentos e suporta formatos RTF e DOCX.

## Arquitetura do Sistema

### Componentes Principais

1. **Modelos de Template**
   - `DocumentoModelo` - Sistema de templates legado
   - `TipoProposicaoTemplate` - Novo sistema automatizado de templates
   - `VariavelDinamica` - Gerenciamento de variáveis dinâmicas

2. **Serviços**
   - `DocumentoModeloService` - Criação e gerenciamento de templates
   - `TemplateParametrosService` - Substituição de variáveis e processamento
   - `OnlyOfficeService` - Integração com editor de documentos

3. **Comandos**
   - `GerarTemplatesProposicoes` - Geração automática de templates

## Tipos de Proposições e Templates

### Tipos Básicos Suportados

Baseado no `DocumentoModeloTemplateSeeder.php`, o sistema suporta:

1. **Projeto de Lei Ordinária** (`projeto_lei_ordinaria`)
2. **Projeto de Lei Complementar** (`projeto_lei_complementar`)
3. **Resolução da Mesa** (`resolucao_mesa`)
4. **Requerimento** (`requerimento`)
5. **Indicação** (`indicacao`)
6. **Moção** (`mocao`)
7. **Emenda** (`emenda`)
8. **Decreto Legislativo** (`decreto_legislativo`)

### Tipos Adicionais (Gerador Automático)

O comando `GerarTemplatesProposicoes` também gera templates para:

- **Medida Provisória** (`medida-provisoria`)
- **Mensagem Executivo** (`mensagem-executivo`)
- **Ofício** (`oficio`)
- **Parecer Comissão** (`parecer-comissao`)
- **Projeto Consolidação Leis** (`projeto-consolidacao-leis`)
- **Projeto Decreto Congresso** (`projeto-decreto-congresso`)
- **Proposta Emenda Constituição** (`proposta-emenda-constituicao`)
- **Proposta Emenda Lei Orgânica** (`proposta-emenda-lei-organica`)
- **Recurso** (`recurso`)
- **Relatório** (`relatorio`)
- **Subemenda** (`subemenda`)
- **Substitutivo** (`substitutivo`)
- **Veto** (`veto`)

## Variáveis Disponíveis nos Templates

### Dados da Proposição
- `${numero_proposicao}` - Número da proposição
- `${tipo_proposicao}` - Tipo da proposição
- `${ementa}` - Ementa da proposição
- `${texto}` - Texto principal do conteúdo
- `${justificativa}` - Justificativa
- `${ano}` - Ano
- `${protocolo}` - Número do protocolo

### Dados do Autor
- `${autor_nome}` - Nome do autor
- `${autor_cargo}` - Cargo do autor
- `${autor_partido}` - Partido do autor

### Datas
- `${data_atual}` - Data atual
- `${data_criacao}` - Data de criação
- `${data_protocolo}` - Data do protocolo
- `${dia}`, `${mes}`, `${ano_atual}` - Componentes de data
- `${mes_extenso}` - Mês por extenso

### Dados da Câmara
- `${nome_camara}` - Nome da câmara
- `${municipio}` - Município
- `${endereco_camara}` - Endereço da câmara
- `${telefone_camara}` - Telefone da câmara
- `${website_camara}` - Website da câmara
- `${imagem_cabecalho}` - Imagem do cabeçalho

### Formatação
- `${assinatura_padrao}` - Área de assinatura padrão
- `${rodape}` - Texto do rodapé

## Sistema de Variáveis Dinâmicas

O modelo `VariavelDinamica` fornece variáveis dinâmicas em todo o sistema:

1. **NOME_CAMARA** - Nome completo da câmara
2. **SIGLA_CAMARA** - Sigla da câmara
3. **DATA_ATUAL** - Data atual do sistema
4. **ANO_ATUAL** - Ano atual
5. **USUARIO_LOGADO** - Usuário logado atual

## Configuração de Parâmetros de Template

### Módulos de Parâmetros (ParametrosTemplatesSeeder)

#### 1. Cabeçalho
- `cabecalho_imagem` - Logo/brasão (upload de arquivo)
- `cabecalho_nome_camara` - Nome da câmara (texto)
- `cabecalho_endereco` - Endereço (textarea)
- `cabecalho_telefone` - Telefone (texto)
- `cabecalho_website` - Website (texto)

#### 2. Rodapé
- `rodape_texto` - Texto do rodapé (textarea)
- `rodape_numeracao` - Mostrar numeração de páginas (checkbox)

#### 3. Variáveis Dinâmicas
- `var_prefixo_numeracao` - Prefixo da numeração (texto)
- `var_formato_data` - Formato de data (select)
- `var_assinatura_padrao` - Texto de assinatura padrão (textarea)

#### 4. Formatação
- `format_fonte` - Fonte padrão (select)
- `format_tamanho_fonte` - Tamanho da fonte (número)
- `format_espacamento` - Espaçamento entre linhas (select)
- `format_margens` - Margens (texto)

## Estrutura do Banco de Dados

### Tabelas Principais

#### tipo_proposicao_templates
```sql
- id (Chave Primária)
- tipo_proposicao_id (Chave Estrangeira, única)
- document_key (único)
- arquivo_path (nullable)
- variaveis (JSON, nullable)
- ativo (boolean, padrão true)
- updated_by (Chave Estrangeira para users, nullable)
- timestamps
```

#### variaveis_dinamicas
```sql
- id, nome, valor, descricao
- tipo, escopo, formato, validacao
- sistema (boolean), ativo (boolean), ordem
- created_by, updated_by, timestamps
```

#### documento_modelos (Sistema legado)
```sql
- id, nome, descricao, tipo_proposicao_id
- document_key, arquivo_nome, arquivo_path
- variaveis (JSON), metadata (JSON)
- is_template, template_id, categoria, ordem
- created_by, timestamps
```

### Tabelas do Sistema de Parâmetros
- `parametros_modulos` - Módulos de parâmetros
- `parametros_submodulos` - Submódulos de parâmetros
- `parametros_campos` - Campos de parâmetros
- `parametros_valores` - Valores de parâmetros

## Processo de Geração de Templates

### Comando de Geração Automática

O comando `GerarTemplatesProposicoes` (`php artisan templates:gerar-automaticos`) cria templates automaticamente:

1. **Carrega parâmetros do sistema** do ParametrosTemplatesService
2. **Encontra tipos de proposição ativos** no banco de dados
3. **Gera conteúdo do template** baseado em estruturas específicas do tipo
4. **Aplica formatação** com codificação RTF adequada
5. **Salva arquivos de template** em `storage/app/templates/`
6. **Cria registros no banco** na tabela tipo_proposicao_templates

### Estrutura do Conteúdo do Template

Cada tipo de template tem uma estrutura específica. Exemplo para Projeto de Lei:

```rtf
{{NOME_CAMARA}}
{{ENDERECO_CAMARA}}
{{TELEFONE_CAMARA}}

LEI ORDINÁRIA Nº ${numero_proposicao}

EMENTA: ${ementa}

Art. 1º ${texto}

Art. 2º Esta Lei entra em vigor na data de sua publicação.

${municipio}, ${data_atual}.

${assinatura_padrao}
```

## Estrutura de Armazenamento de Arquivos

### Localização dos Arquivos de Template

```
storage/app/
├── templates/
│   ├── template_projeto-lei-ordinaria_[timestamp].rtf
│   ├── template_requerimento_[timestamp].rtf
│   └── ... (outros templates gerados)
├── private/templates/
│   ├── template_[id].rtf (templates individuais)
│   └── ...
└── public/
    ├── proposicoes/
    │   ├── proposicao_[id]_template_[template_id].docx
    │   └── ...
    └── templates/
        └── padrao/
```

### Exemplo de Conteúdo de Template (RTF)

Baseado na análise real dos arquivos de template:
```rtf
{\rtf1\ansi\ansicpg65001\deff0\deflang1046{\fonttbl{\f0\froman\fcharset0 Arial;}}{\colortbl;\red0\green0\blue0;}\viewkind4\uc1\pard\cf1\f0\fs24 
${imagem_cabecalho}\par \par \par \par 
LEI ORDINÁRIA Nº ${numero_proposicao}\par \par 
EMENTA: ${ementa}\par \par 
Art. 1º ${texto}\par \par 
Art. 2º Esta Lei entra em vigor na data de sua publicação.\par \par 
${municipio}, ${data_atual}.\par \par 
${assinatura_padrao}\par 
}
```

## Pontos de Integração

### Integração OnlyOffice
- Templates são processados antes da abertura no OnlyOffice
- Documentos são criados em formato DOCX para edição
- Substituição de variáveis ocorre antes da inicialização do editor
- Sistema de callback salva alterações automaticamente

### Integração com Controllers
- `ProposicaoController` - Gerenciamento principal de proposições
- `DocumentoTemplateController` - Administração de templates
- `OnlyOfficeController` - Integração com editor

### Interface Administrativa
- Gerenciamento de templates em `/admin/templates`
- Configuração de parâmetros em `/admin/templates/parametros`
- Interface de teste de variáveis incluída

## Comandos do Sistema

### Comandos Disponíveis

1. **templates:gerar-automaticos**
   - Gera templates para todos os tipos de proposição ativos
   - Opções: `--tipo` (tipos específicos), `--force` (sobrescrever existentes)
   - Usa padrões legais (LC 95/1998) para formatação

### Exemplos de Uso

```bash
# Gerar todos os templates
php artisan templates:gerar-automaticos

# Gerar tipos específicos
php artisan templates:gerar-automaticos --tipo=projeto_lei_ordinaria --tipo=requerimento

# Forçar sobrescrita de templates existentes
php artisan templates:gerar-automaticos --force
```

## Arquivos de Configuração

### Variáveis de Ambiente
```env
ONLYOFFICE_SERVER_URL=http://localhost:8080
ONLYOFFICE_JWT_SECRET=
ONLYOFFICE_STORAGE_PATH=/storage/onlyoffice
ONLYOFFICE_CALLBACK_URL=http://host.docker.internal:8001/api/onlyoffice/callback
```

## Status Atual do Sistema

### Arquivos de Template Existentes

Com base na análise do armazenamento, o sistema atualmente contém:

- 23+ arquivos de template gerados automaticamente
- Templates para todos os tipos principais de proposição
- Versões com timestamp e backup mantidas
- Formato RTF com codificação UTF-8 adequada

### Seeders de Templates

#### Registrados no DatabaseSeeder:
- `TipoProposicaoCompletoSeeder` - Tipos de proposição

#### Não Registrados (execução manual necessária):
- `DocumentoModeloTemplateSeeder` - Templates padrão
- `TemplateProposicaoParametroSeeder` - Parâmetros de configuração
- `ParametrosTemplatesSeeder` - Módulos de parâmetros
- `VariaveisDinamicasSeeder` - Variáveis dinâmicas
- `ProposicaoPermissionsSeeder` - Permissões do sistema

## Considerações Importantes

1. **Codificação**: Todos os templates usam UTF-8 para suporte completo a caracteres portugueses
2. **Backup**: Sistema mantém versões anteriores dos templates
3. **Integração**: Templates são processados em tempo real durante a edição
4. **Personalização**: Sistema permite personalização completa via parâmetros administrativos
5. **Padrões Legais**: Templates seguem padrões da LC 95/1998 para legislação brasileira

Este sistema abrangente de templates fornece uma base flexível e configurável para gerar documentos legislativos com formatação adequada, substituição de variáveis e integração perfeita com o editor de documentos OnlyOffice.