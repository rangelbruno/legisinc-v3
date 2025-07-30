# Processo Legislativo - Fluxo Completo

## Visão Geral

Este documento descreve o processo completo do sistema legislativo, desde a criação de templates pelo Administrador até o protocolo final das proposições. O sistema é baseado em Laravel e utiliza OnlyOffice para edição de documentos.

## Estrutura do Banco de Dados

### Tabelas Principais

#### `tipo_proposicoes`
Armazena os tipos de proposições disponíveis no sistema:
- `id`: Identificador único
- `codigo`: Código único do tipo (ex: projeto_lei_ordinaria)
- `nome`: Nome amigável do tipo
- `descricao`: Descrição detalhada
- `icone`: Ícone ki-duotone utilizado
- `cor`: Cor do badge (primary, success, etc.)
- `ativo`: Status ativo/inativo
- `ordem`: Ordem de exibição
- `configuracoes`: JSON com configurações específicas

#### `tipo_proposicao_templates`
Templates de documentos para cada tipo de proposição:
- `id`: Identificador único
- `tipo_proposicao_id`: FK para tipo_proposicoes
- `document_key`: Chave única para OnlyOffice
- `arquivo_path`: Caminho do arquivo template
- `variaveis`: JSON com variáveis do template
- `ativo`: Status ativo/inativo
- `updated_by`: FK para users (quem atualizou)

#### `proposicoes`
Tabela principal das proposições:
- `id`: Identificador único
- `tipo`: Código do tipo de proposição
- `ementa`: Ementa da proposição
- `conteudo`: Conteúdo textual
- `arquivo_path`: Caminho do arquivo DOCX
- `autor_id`: FK para users (parlamentar autor)
- `status`: Enum com estados do fluxo
- `ano`: Ano da proposição
- `modelo_id`: ID do modelo usado
- `template_id`: FK para tipo_proposicao_templates
- `ultima_modificacao`: Timestamp da última alteração

**Campos adicionados para Legislativo:**
- `observacoes_edicao`: Observações do setor legislativo
- `observacoes_retorno`: Observações de retorno
- `data_retorno_legislativo`: Data de retorno do legislativo

**Campos adicionados para Assinatura:**
- `confirmacao_leitura`: Boolean de confirmação de leitura
- `assinatura_digital`: Dados da assinatura digital
- `certificado_digital`: Dados do certificado
- `data_assinatura`: Data/hora da assinatura
- `ip_assinatura`: IP de onde foi assinado
- `data_aprovacao_autor`: Data de aprovação pelo autor

**Campos adicionados para Protocolo:**
- `numero_protocolo`: Número do protocolo
- `data_protocolo`: Data/hora do protocolo
- `funcionario_protocolo_id`: FK para users (funcionário do protocolo)
- `comissoes_destino`: JSON com comissões de destino
- `observacoes_protocolo`: Observações do protocolo
- `verificacoes_realizadas`: JSON com verificações

### Status da Proposição

O campo `status` segue o fluxo:
1. `rascunho` - Criada pelo parlamentar, ainda não finalizada
2. `em_edicao` - Em processo de edição
3. `salvando` - Estado temporário durante salvamento
4. `enviado_legislativo` - Enviada para revisão do setor legislativo
5. `retornado_legislativo` - Retornada do legislativo com observações
6. `assinado` - Assinada digitalmente pelo parlamentar
7. `protocolado` - Protocolada oficialmente no sistema

## Fluxo Completo do Processo

### 1. Criação do Template pelo Administrador

**Rota:** `/admin/templates`
**Controller:** `TemplateController`
**View:** `resources/views/admin/templates/`

#### Processo:
1. Administrador acessa a listagem de templates
2. Seleciona um tipo de proposição para criar/editar template
3. Sistema cria registro na tabela `tipo_proposicao_templates`
4. Abre editor OnlyOffice integrado
5. Administrador edita o template com variáveis específicas
6. Sistema salva automaticamente via callback do OnlyOffice
7. Template fica disponível para uso pelos parlamentares

#### Tecnologias Envolvidas:
- OnlyOffice Document Server para edição
- Laravel Storage para persistência de arquivos
- Sistema de callback para sincronização

### 2. Criação da Proposição pelo Parlamentar

**Rota:** `/proposicoes/criar`
**Controller:** `ProposicaoController@create`
**View:** `resources/views/proposicoes/create.blade.php`
**Middleware:** `check.parlamentar.access`

#### Processo:
1. Parlamentar acessa formulário de criação
2. Seleciona tipo de proposição
3. Preenche ementa inicial
4. Sistema cria registro na tabela `proposicoes` com status `rascunho`
5. Redireciona para seleção de template (se disponível)

#### Métodos Relacionados:
- `ProposicaoController@salvarRascunho`: Salva dados básicos
- `ProposicaoController@buscarModelos`: Lista templates disponíveis

### 3. Seleção e Preenchimento do Template

**Rota:** `/proposicoes/{proposicao}/preencher-modelo/{modeloId}`
**Controller:** `ProposicaoController@preencherModelo`

#### Processo:
1. Sistema apresenta templates disponíveis para o tipo
2. Parlamentar seleciona template desejado
3. Sistema cria instância do template
4. Apresenta formulário para variáveis específicas
5. Após preenchimento, redireciona para editor

### 4. Edição da Proposição

**Rota:** `/proposicoes/{proposicao}/editar-onlyoffice/{template}`
**Controller:** `ProposicaoController@editarOnlyOffice`
**Nova Arquitetura:** `ProposicaoController@editarOnlyOfficeNovaArquitetura`

#### Processo:
1. Sistema configura OnlyOffice para edição do documento
2. Processa template com variáveis preenchidas
3. Abre editor integrado na interface
4. Parlamentar edita conteúdo da proposição
5. Sistema salva automaticamente via callback
6. Status atualizado para `em_edicao`

#### Funcionalidades:
- Edição colaborativa (se configurado)
- Salvamento automático
- Versionamento de documentos
- Integração com sistema de arquivos

### 5. Finalização e Envio para Legislativo

**Rota:** `/proposicoes/{proposicao}/enviar-legislativo`
**Controller:** `ProposicaoController@enviarLegislativo`

#### Processo:
1. Parlamentar finaliza edição do documento
2. Sistema valida completude da proposição
3. Atualiza status para `enviado_legislativo`
4. Notifica setor legislativo
5. Documento torna-se apenas leitura para o parlamentar

### 6. Revisão pelo Setor Legislativo

**Rota:** `/proposicoes/legislativo`
**Controller:** `ProposicaoLegislativoController`
**Middleware:** `check.proposicao.permission`

#### Processo de Listagem:
1. Setor legislativo acessa dashboard de proposições
2. Visualiza proposições com status `enviado_legislativo`
3. Seleciona proposição para revisão

#### Processo de Revisão Individual:
**Rota:** `/proposicoes/{proposicao}/legislativo/editar`
**Controller:** `ProposicaoLegislativoController@editar`

1. Sistema abre documento em modo de edição para legislativo
2. Funcionário pode fazer correções/sugestões
3. Adiciona observações no campo `observacoes_edicao`
4. Pode aprovar ou devolver para o parlamentar

#### Opções de Finalização:
- **Aprovar:** `ProposicaoLegislativoController@aprovar`
  - Status → `retornado_legislativo` (aguardando aprovação do autor)
- **Devolver:** `ProposicaoLegislativoController@devolver`
  - Status → `retornado_legislativo` (necessita correções)
  - Preenche `observacoes_retorno` e `data_retorno_legislativo`

### 7. Retorno ao Parlamentar (Aprovação/Correção)

**Rota:** `/proposicoes/assinatura`
**Controller:** `ProposicaoAssinaturaController`

#### Para Proposições Aprovadas pelo Legislativo:
1. Parlamentar visualiza proposição com aprovação do legislativo
2. Pode revisar alterações feitas
3. Confirma leitura das alterações
4. Prossegue para assinatura

#### Para Proposições Devolvidas:
**Rota:** `/proposicoes/{proposicao}/corrigir`
1. Parlamentar visualiza observações de retorno
2. Pode editar novamente o documento
3. Implementa correções solicitadas
4. Reenvia para o legislativo

### 8. Assinatura Digital

**Rota:** `/proposicoes/{proposicao}/assinar`
**Controller:** `ProposicaoAssinaturaController@assinar`

#### Processo:
1. Sistema apresenta documento final para assinatura
2. Parlamentar confirma leitura integral
3. Utiliza certificado digital para assinar
4. Sistema registra:
   - `assinatura_digital`: Dados da assinatura
   - `certificado_digital`: Informações do certificado
   - `data_assinatura`: Timestamp
   - `ip_assinatura`: IP de origem
5. Status atualizado para `assinado`

#### Validações:
- Verificação de integridade do documento
- Validação do certificado digital
- Confirmação de leitura obrigatória

### 9. Protocolo Final

**Rota:** `/proposicoes/protocolar`
**Controller:** `ProposicaoProtocoloController`
**Middleware:** `block.protocolo.access` (apenas funcionários específicos)

#### Processo de Listagem:
1. Funcionário do protocolo acessa dashboard
2. Visualiza proposições com status `assinado`
3. Seleciona proposição para protocolar

#### Processo de Protocolo:
**Rota:** `/proposicoes/{proposicao}/protocolar`

1. Sistema apresenta dados da proposição
2. Funcionário revisa documentação
3. Realiza verificações obrigatórias:
   - Integridade da assinatura
   - Completude dos dados
   - Conformidade com regimentos
4. Atribui número de protocolo único
5. Define comissões de destino
6. Adiciona observações se necessário
7. Efetiva o protocolo

#### Finalização do Protocolo:
**Rota:** `/proposicoes/{proposicao}/efetivar-protocolo`

Campos atualizados:
- `numero_protocolo`: Número sequencial único
- `data_protocolo`: Data/hora do protocolo
- `funcionario_protocolo_id`: ID do funcionário
- `comissoes_destino`: Array com comissões
- `observacoes_protocolo`: Observações finais
- `verificacoes_realizadas`: Checklist de verificações
- Status final: `protocolado`

### 10. Visualização e Acompanhamento

#### Para Parlamentares:
**Rota:** `/proposicoes/{proposicao}`
- Visualização completa da proposição
- Status atual da tramitação
- Histórico de alterações
- Download de documentos

**Rota:** `/proposicoes/minhas-proposicoes`
- Dashboard com todas as proposições do parlamentar
- Filtros por status
- Estatísticas pessoais

#### Para Administração:
- Relatórios estatísticos
- Acompanhamento de prazos
- Auditoria de alterações

### 11. Exclusão de Proposições

**Rota:** `/proposicoes/{proposicao}`
**Método:** DELETE
**Controller:** `ProposicaoController@destroy`

#### Regras:
- Apenas proposições em status `rascunho` ou `em_edicao`
- Apenas pelo autor da proposição
- Remove arquivos associados do storage
- Soft delete para auditoria

## Estrutura de Permissões

### Roles do Sistema:
- **ADMINISTRADOR**: Acesso total ao sistema
- **PARLAMENTAR**: Acesso às próprias proposições
- **LEGISLATIVO**: Acesso a revisão de proposições
- **PROTOCOLO**: Acesso ao protocolo final
- **PUBLICO**: Acesso apenas a consultas públicas

### Middlewares de Segurança:
- `check.parlamentar.access`: Verifica se usuário é parlamentar
- `check.proposicao.permission`: Verifica permissões específicas
- `block.protocolo.access`: Restringe acesso ao protocolo
- `check.screen.permission`: Sistema geral de permissões

## Integrações Técnicas

### OnlyOffice Document Server
- Edição colaborativa de documentos
- Callback automático para sincronização
- Suporte a múltiplos formatos
- Versionamento integrado

### Sistema de Arquivos
- Laravel Storage para persistência
- Organização por tipo e data
- Backup automático de versões
- Limpeza de arquivos temporários

### Auditoria e Logs
- Log completo de todas as operações
- Rastreamento de alterações
- Histórico de status
- Backup de versões anteriores

## Considerações de Segurança

### Assinatura Digital
- Validação de certificados ICP-Brasil
- Verificação de integridade
- Timestamp de assinatura
- Log de IP e sessão

### Controle de Acesso
- Autenticação obrigatória
- Autorização baseada em roles
- Auditoria de acessos
- Sessões seguras

### Integridade de Dados
- Validação de entrada
- Proteção CSRF
- Sanitização de conteúdo
- Backup automático

## Fluxo de Estados (Mermaid)

```mermaid
flowchart TD
    A[Parlamentar acessa /proposicoes/criar] --> B[Preenche dados básicos]
    B --> C[Status: rascunho]
    C --> D[Seleciona template em /admin/templates]
    D --> E[Preenche variáveis do template]
    E --> F[Edita documento no OnlyOffice]
    F --> G[Status: em_edicao]
    G --> H[Finaliza e envia para legislativo]
    H --> I[Status: enviado_legislativo]
    
    I --> J[Legislativo revisa em /proposicoes/legislativo]
    J --> K{Legislativo Aprova?}
    
    K -->|Sim| L[Adiciona observações de aprovação]
    K -->|Não| M[Adiciona observações de correção]
    
    L --> N[Status: retornado_legislativo]
    M --> N
    
    N --> O[Parlamentar acessa /proposicoes/assinatura]
    O --> P{Necessita Correção?}
    
    P -->|Sim| Q[Edita documento novamente]
    Q --> G
    
    P -->|Não| R[Confirma leitura das alterações]
    R --> S[Assina digitalmente]
    S --> T[Status: assinado]
    
    T --> U[Protocolo acessa /proposicoes/protocolar]
    U --> V[Realiza verificações]
    V --> W[Atribui número de protocolo]
    W --> X[Define comissões destino]
    X --> Y[Status: protocolado]
    
    Y --> Z[Processo concluído]
    
    %% Visualizações paralelas
    C -.-> AA[/proposicoes/minhas-proposicoes]
    G -.-> AA
    I -.-> AA
    N -.-> AA
    T -.-> AA
    Y -.-> AA
    
    Y -.-> BB[/proposicoes/{id} - Visualização]
    
    %% Exclusão
    C --> CC{Exclusão?}
    G --> CC
    CC -->|Sim| DD[DELETE /proposicoes/{id}]
    DD --> EE[Proposição excluída]
    
    style A fill:#e1f5fe
    style D fill:#fff3e0
    style J fill:#f3e5f5
    style U fill:#e8f5e8
    style Y fill:#ffebee
    style Z fill:#e8f5e8
```

## Conclusão

Este sistema implementa um fluxo legislativo completo e seguro, com rastreabilidade total do processo desde a criação até o protocolo final. A integração com OnlyOffice garante uma edição profissional dos documentos, enquanto o sistema de permissões assegura que cada usuário tenha acesso apenas às funcionalidades apropriadas ao seu papel no processo legislativo.

O uso de Laravel como framework base proporciona robustez, segurança e facilidade de manutenção, enquanto a estrutura modular permite futuras expansões e customizações conforme necessidades específicas de cada instituição legislativa.