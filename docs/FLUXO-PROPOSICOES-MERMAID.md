# Diagrama de Fluxo de Proposições - Sistema Legisinc

## 📅 Última Atualização: 30/08/2025
## ✅ Status: Produção com Melhores Práticas Implementadas

## 📝 Template Universal - Fluxo Completo

### Visão Geral do Template Universal

O **Template Universal** é um sistema inovador que elimina a necessidade de manter 23 templates separados para cada tipo de proposição. Ele se adapta automaticamente ao tipo selecionado, aplicando variáveis dinâmicas e estrutura específica conforme a legislação brasileira (LC 95/1998).

```mermaid
flowchart TB
    subgraph TemplateSystem["🎨 Sistema Template Universal"]
        TU_Creation[Template Universal<br/>📄 RTF com 29 variáveis]
        TU_Variables["Variáveis Dinâmicas<br/>tipo_proposicao<br/>ementa<br/>texto<br/>autor_nome"]
        TU_Processing[Processamento RTF<br/>✅ UTF-8 correto<br/>✅ OnlyOffice compatível]
        
        TU_Creation --> TU_Variables
        TU_Variables --> TU_Processing
    end
    
    subgraph PropositionCreation["👤 Criação pelo Parlamentar"]
        PC_Select[Parlamentar seleciona tipo]
        PC_Template[Sistema aplica Template Universal]
        PC_Variables[Substitui variáveis automáticas<br/>Nome, cargo, câmara, data]
        PC_Editor[Abre OnlyOffice com template]
        
        PC_Select --> PC_Template
        PC_Template --> PC_Variables
        PC_Variables --> PC_Editor
    end
    
    subgraph LegalAnalysis["⚖️ Análise Jurídica"]
        LA_Receive[Jurídico recebe proposição]
        LA_OpenEditor[Abre OnlyOffice para revisão]
        LA_ContentCheck[Verifica conteúdo salvo<br/>🔍 Não usa template]
        LA_Edit[Edita documento final]
        
        LA_Receive --> LA_OpenEditor
        LA_OpenEditor --> LA_ContentCheck
        LA_ContentCheck --> LA_Edit
    end
    
    TU_Processing --> PC_Select
    PC_Editor --> LA_Receive
    LA_Edit --> End([Documento final<br/>pronto para assinatura])
    
    style TemplateSystem fill:#e3f2fd
    style PropositionCreation fill:#f3e5f5
    style LegalAnalysis fill:#e8f5e8
```

### Detalhamento do Template Universal

```mermaid
flowchart LR
    subgraph Admin["🔧 Administrador"]
        A1[Configura Template Universal<br/>em /admin/templates/universal]
        A2[29 variáveis disponíveis]
        A3[Estrutura RTF válida]
        A4[Imagem cabeçalho processada]
        
        A1 --> A2 --> A3 --> A4
    end
    
    subgraph Variables["📊 Variáveis do Sistema"]
        V1[Proposição: tipo, número, ementa]
        V2[Autor: nome, cargo, partido]
        V3[Instituição: câmara, endereço, CNPJ]
        V4[Datas: atual, criação, protocolo]
        V5[Dinâmicas: preâmbulo adaptável]
        
        V1 --> V2 --> V3 --> V4 --> V5
    end
    
    subgraph Process["⚙️ Processamento"]
        P1[Template base RTF]
        P2["Substitui imagem_cabecalho"]
        P3[Mantém outras como placeholder]
        P4[Encoding UTF-8 correto]
        P5[OnlyOffice compatível]
        
        P1 --> P2 --> P3 --> P4 --> P5
    end
    
    A4 --> V1
    V5 --> P1
    P5 --> Output([Template pronto para<br/>uso pelo Parlamentar])
    
    style Admin fill:#fff3e0
    style Variables fill:#e8f5e8
    style Process fill:#f3e5f5
```

## Fluxo Principal Completo

```mermaid
flowchart TB
    Start([Início]) --> CreateProposal[Parlamentar cria proposição]
    
    CreateProposal --> ChooseType{Escolhe tipo de<br/>preenchimento}
    ChooseType -->|Template| UseTemplate[Aplica template<br/>com variáveis]
    ChooseType -->|Manual| ManualText[Digita texto<br/>manualmente]
    ChooseType -->|IA| AIGenerate[Gera conteúdo<br/>com IA]
    
    UseTemplate --> SaveDraft
    ManualText --> SaveDraft
    AIGenerate --> SaveDraft
    
    SaveDraft[Salva como rascunho<br/>Status: 'rascunho'] --> EditOnlyOffice[Edita no OnlyOffice<br/>Status: 'em_edicao']
    
    EditOnlyOffice --> ValidateContent[Validação de<br/>conteúdo RTF]
    ValidateContent --> AddAttachments{Adicionar<br/>anexos?}
    AddAttachments -->|Sim| UploadFiles[Upload de arquivos<br/>PDF, DOC, imagens]
    AddAttachments -->|Não| SendToLegislative
    UploadFiles --> SendToLegislative
    
    SendToLegislative[Envia para Legislativo<br/>Status: 'enviado_legislativo'] --> LegislativeReceives[Legislativo recebe<br/>proposição]
    
    LegislativeReceives --> StartReview[Inicia revisão<br/>Status: 'em_revisao']
    
    StartReview --> TechnicalAnalysis[Análise técnica:<br/>- Constitucionalidade<br/>- Juridicidade<br/>- Regimentalidade<br/>- Técnica legislativa]
    
    TechnicalAnalysis --> EditContent{Precisa<br/>editar?}
    EditContent -->|Sim| LegislativeEdit[Legislativo edita<br/>no OnlyOffice]
    EditContent -->|Não| MakeDecision
    LegislativeEdit --> MakeDecision
    
    MakeDecision{Decisão do<br/>Legislativo}
    MakeDecision -->|Aprovar| ApproveForSignature[Aprova para assinatura<br/>Status: 'aprovado_assinatura']
    MakeDecision -->|Devolver| ReturnForCorrection[Devolve para correção<br/>Status: 'devolvido_correcao']
    
    ReturnForCorrection --> ParliamentaryCorrects[Parlamentar faz<br/>correções solicitadas]
    ParliamentaryCorrects --> SendToLegislative
    
    ApproveForSignature --> ParliamentaryViews[Parlamentar visualiza<br/>versão final]
    ParliamentaryViews --> ConfirmReading[Confirma leitura<br/>confirmacao_leitura = true]
    
    ConfirmReading --> DigitalSignature[Assina digitalmente<br/>Status: 'assinado']
    
    DigitalSignature --> GeneratePDFSigned[Gera PDF otimizado<br/>com assinatura QR]
    
    GeneratePDFSigned --> CleanOldPDFs[Limpa PDFs antigos<br/>mantém 3 últimos]
    CleanOldPDFs --> SendToProtocol[Envia para protocolo<br/>Status: 'enviado_protocolo']
    
    SendToProtocol --> ProtocolQueue[Fila do protocolo]
    
    ProtocolQueue --> ProtocolVerifications[Verificações do protocolo:<br/>- Documento assinado<br/>- Conteúdo completo<br/>- Anexos presentes]
    
    ProtocolVerifications --> AssignNumber[Atribui número de protocolo<br/>Ex: 2025/0001]
    
    AssignNumber --> DefineCommissions[Define comissões<br/>de destino]
    
    DefineCommissions --> Protocolize[Protocoliza oficialmente<br/>Status: 'protocolado']
    
    Protocolize --> GenerateFinalPDF[Gera PDF final otimizado<br/>com número de protocolo<br/>e QR Code]
    
    GenerateFinalPDF --> End([Fim - Proposição<br/>Protocolada])
    
    style Start fill:#e1f5fe
    style End fill:#c8e6c9
    style CreateProposal fill:#fff3e0
    style SaveDraft fill:#fce4ec
    style SendToLegislative fill:#f3e5f5
    style ApproveForSignature fill:#e8f5e9
    style ReturnForCorrection fill:#ffebee
    style DigitalSignature fill:#e0f2f1
    style Protocolize fill:#f1f8e9
```

## 👥 Fluxo Parlamentar → Jurídico (Detalhado)

### Sequência Completa de Interação

```mermaid
sequenceDiagram
    participant P as 👤 Parlamentar
    participant TU as 🎨 Template Universal
    participant S as 🖥️ Sistema
    participant OO as 📝 OnlyOffice
    participant J as ⚖️ Jurídico
    participant DB as 🗄️ Database
    
    Note over P,DB: Fase 1: Criação com Template Universal
    P->>S: Acessa /proposicoes/create?tipo=mocao
    S->>TU: Busca Template Universal padrão
    TU-->>S: Template RTF com variáveis
    S->>S: Substitui variáveis automáticas<br/>(nome, cargo, câmara, data)
    S->>DB: Salva rascunho com template aplicado
    DB-->>S: ID da proposição criada
    S->>P: Redireciona para tela da proposição
    
    Note over P,DB: Fase 2: Edição pelo Parlamentar
    P->>S: Clique "Adicionar Conteúdo" (OnlyOffice)
    S->>OO: Gera config com template processado
    OO-->>P: Editor carregado com template
    P->>OO: Edita conteúdo da proposição
    OO->>S: Callback salva alterações (RTF)
    S->>DB: Atualiza arquivo_path e conteudo
    DB-->>S: Confirmação
    S-->>OO: Status = 0 (sucesso)
    P->>S: Finaliza edição
    S->>DB: Status → 'em_edicao'
    
    Note over P,DB: Fase 3: Envio para Análise
    P->>S: Clique "Enviar para Legislativo"
    S->>S: Valida conteúdo mínimo
    S->>DB: Status → 'enviado_legislativo'
    S->>J: Notifica nova proposição disponível
    DB-->>S: Log da tramitação
    
    Note over J,DB: Fase 4: Análise Jurídica
    J->>S: Acessa lista de proposições
    S->>DB: Lista proposições 'enviado_legislativo'
    DB-->>S: Proposições para análise
    S->>J: Exibe proposições pendentes
    J->>S: Abre proposição específica
    S->>DB: Busca dados completos
    DB-->>S: Proposição + arquivos
    S->>J: Tela de análise jurídica
    
    Note over J,DB: Fase 5: Revisão no OnlyOffice
    J->>S: Clique "Revisar no Editor"
    S->>S: Verifica arquivo salvo pelo Parlamentar
    S->>OO: Config para carregar arquivo existente<br/>(NÃO template)
    OO-->>J: Editor com conteúdo do Parlamentar
    J->>OO: Faz revisões e correções
    OO->>S: Callback salva versão revisada
    S->>DB: Atualiza com versão do Jurídico
    DB-->>S: Confirmação
    S-->>OO: Status = 0 (sucesso)
    
    Note over J,DB: Fase 6: Decisão Final
    J->>S: Escolhe ação (Aprovar/Devolver)
    alt Aprovar
        S->>DB: Status → 'aprovado_assinatura'
        S->>P: Notifica aprovação
        DB-->>S: Log de aprovação
    else Devolver
        S->>DB: Status → 'devolvido_correcao'
        S->>P: Notifica devolução + motivos
        DB-->>S: Log de devolução
        P->>S: Acessa proposição devolvida
        Note over P,OO: Volta para Fase 2 (Edição)
    end
    
    Note over P,DB: Fase 7: Pós-Aprovação
    P->>S: Visualiza versão final aprovada
    S->>DB: Busca arquivo mais recente (do Jurídico)
    DB-->>S: Versão final revisada
    S->>P: Exibe documento para confirmação
    P->>S: Confirma leitura
    S->>DB: confirmacao_leitura = true
    P->>S: Assina digitalmente
    S->>DB: Status → 'assinado'
    S->>S: Gera PDF final com QR Code
    DB-->>S: Documento finalizado
```

### Estados e Transições Template Universal

```mermaid
stateDiagram-v2
    [*] --> template_aplicado: Template Universal aplicado
    
    template_aplicado --> parlamentar_editando: OnlyOffice carrega template
    parlamentar_editando --> rascunho_salvo: Parlamentar salva
    rascunho_salvo --> enviado_juridico: Envia para análise
    
    enviado_juridico --> juridico_analisando: Jurídico inicia análise
    juridico_analisando --> juridico_editando: Abre OnlyOffice (arquivo salvo)
    juridico_editando --> versao_revisada: Jurídico salva revisões
    
    versao_revisada --> aprovado: Jurídico aprova
    versao_revisada --> devolvido: Jurídico devolve
    
    devolvido --> parlamentar_editando: Volta para edição
    
    aprovado --> aguardando_assinatura: Parlamentar pode assinar
    aguardando_assinatura --> assinado: Assinatura digital
    
    assinado --> [*]: Fluxo concluído
    
    note right of template_aplicado: Template com variáveis<br/>automáticas aplicadas
    note right of juridico_editando: NÃO usa template,<br/>carrega arquivo existente
    note right of versao_revisada: Versão final com<br/>revisões jurídicas
```

### Comparação: Template vs Arquivo Salvo

```mermaid
flowchart LR
    subgraph Parlamentar["👤 Parlamentar (Primeira vez)"]
        P1[Template Universal aplicado]
        P2[Variáveis substituídas]
        P3[OnlyOffice carrega template]
        P4[Edita e salva arquivo]
        
        P1 --> P2 --> P3 --> P4
    end
    
    subgraph Juridico["⚖️ Jurídico (Revisão)"]
        J1[Sistema detecta arquivo salvo]
        J2[OnlyOffice carrega arquivo<br/>NÃO template]
        J3[Jurídico vê conteúdo real]
        J4[Faz revisões e salva]
        
        J1 --> J2 --> J3 --> J4
    end
    
    P4 --> J1
    
    subgraph Decision["🤔 Lógica de Detecção"]
        D1{Existe arquivo_path?}
        D2[Carrega arquivo existente]
        D3[Aplica Template Universal]
        
        D1 -->|Sim| D2
        D1 -->|Não| D3
    end
    
    style Parlamentar fill:#e3f2fd
    style Juridico fill:#e8f5e8
    style Decision fill:#fff3e0
```

## Fluxo por Perfil de Usuário

```mermaid
flowchart LR
    subgraph Parlamentar
        P1[Cria proposição]
        P2[Edita conteúdo]
        P3[Envia para legislativo]
        P4[Faz correções]
        P5[Assina digitalmente]
        P1 --> P2 --> P3
        P4 --> P3
        P5
    end
    
    subgraph Legislativo
        L1[Recebe proposições]
        L2[Analisa tecnicamente]
        L3[Edita se necessário]
        L4[Aprova ou devolve]
        L1 --> L2 --> L3 --> L4
    end
    
    subgraph Protocolo
        PR1[Recebe assinadas]
        PR2[Realiza verificações]
        PR3[Atribui número]
        PR4[Define comissões]
        PR5[Protocoliza]
        PR1 --> PR2 --> PR3 --> PR4 --> PR5
    end
    
    P3 -.-> L1
    L4 -.->|Devolve| P4
    L4 -.->|Aprova| P5
    P5 -.-> PR1
```

## Estados (Status) da Proposição

### Fluxo com Validações e Otimizações

```mermaid
stateDiagram-v2
    [*] --> rascunho: Criação
    rascunho --> em_edicao: Abre no OnlyOffice
    em_edicao --> enviado_legislativo: Envia para análise
    rascunho --> enviado_legislativo: Envia direto
    
    enviado_legislativo --> em_revisao: Legislativo inicia revisão
    em_revisao --> aprovado_assinatura: Aprova
    em_revisao --> devolvido_correcao: Devolve
    
    devolvido_correcao --> em_edicao: Parlamentar corrige
    
    aprovado_assinatura --> assinado: Parlamentar assina
    
    assinado --> enviado_protocolo: Automático
    
    enviado_protocolo --> protocolado: Protocolo atribui número
    
    protocolado --> [*]: Fim do fluxo inicial
```

## Fluxo de Dados entre Tabelas

```mermaid
erDiagram
    PROPOSICOES ||--o{ USERS : "autor_id"
    PROPOSICOES ||--o{ USERS : "revisor_id"
    PROPOSICOES ||--o{ USERS : "funcionario_protocolo_id"
    PROPOSICOES ||--o{ TIPO_PROPOSICAO_TEMPLATES : "template_id"
    PROPOSICOES ||--o{ PARECER_JURIDICOS : "parecer_id"
    PROPOSICOES ||--o{ TRAMITACAO_LOGS : "proposicao_id"
    PROPOSICOES ||--o{ ITENS_PAUTA : "proposicao_id"
    
    TIPO_PROPOSICAO_TEMPLATES ||--|| TIPO_PROPOSICOES : "tipo_proposicao_id"
    
    PROPOSICOES {
        bigint id PK
        string tipo
        text ementa
        longtext conteudo
        bigint autor_id FK
        string status
        bigint template_id FK
        string arquivo_path
        string arquivo_pdf_path
        timestamp data_assinatura
        string numero_protocolo
        timestamp data_protocolo
        bigint funcionario_protocolo_id FK
    }
    
    USERS {
        bigint id PK
        string name
        string email
        string role
    }
    
    TIPO_PROPOSICOES {
        bigint id PK
        string codigo
        string nome
        boolean ativo
    }
    
    TIPO_PROPOSICAO_TEMPLATES {
        bigint id PK
        bigint tipo_proposicao_id FK
        string nome
        string arquivo_path
        text conteudo
    }
    
    TRAMITACAO_LOGS {
        bigint id PK
        bigint proposicao_id FK
        string acao
        string status_anterior
        string status_novo
        bigint user_id FK
        timestamp created_at
    }
```

## Timeline do Processo

```mermaid
gantt
    title Timeline de uma Proposição
    dateFormat HH:mm
    axisFormat %H:%M
    
    section Parlamentar
    Cria proposição           :done, create, 09:00, 15m
    Edita no OnlyOffice       :done, edit, 09:15, 30m
    Envia para Legislativo    :done, send, 09:45, 5m
    
    section Legislativo
    Recebe proposição         :done, receive, 10:00, 5m
    Análise técnica          :done, analyze, 10:05, 45m
    Edita conteúdo           :active, legedit, 10:50, 30m
    Aprova proposição        :crit, approve, 11:20, 10m
    
    section Parlamentar
    Visualiza versão final    :view, 11:30, 10m
    Assina digitalmente      :crit, sign, 11:40, 10m
    
    section Protocolo
    Recebe para protocolo    :protreceive, 11:50, 5m
    Verifica documentos      :verify, 11:55, 15m
    Atribui número          :number, 12:10, 5m
    Protocoliza             :milestone, protocol, 12:15, 5m
```

## Fluxo de Decisões Detalhado

```mermaid
flowchart TD
    subgraph Criação
        A1[Parlamentar acessa sistema]
        A2{Tem permissão?}
        A3[Acesso negado]
        A4[Tela de criação]
        A5[Escolhe tipo proposição]
        A6[Preenche ementa]
        
        A1 --> A2
        A2 -->|Não| A3
        A2 -->|Sim| A4
        A4 --> A5
        A5 --> A6
    end
    
    subgraph Preenchimento
        B1{Método de preenchimento}
        B2[Seleciona template]
        B3[Sistema aplica variáveis]
        B4[Digita manualmente]
        B5[Solicita geração IA]
        B6[IA gera conteúdo]
        
        A6 --> B1
        B1 -->|Template| B2
        B2 --> B3
        B1 -->|Manual| B4
        B1 -->|IA| B5
        B5 --> B6
    end
    
    subgraph Edição
        C1[Abre OnlyOffice]
        C2[Edita documento]
        C3{Adiciona anexos?}
        C4[Upload arquivos]
        C5[Salva alterações]
        
        B3 --> C1
        B4 --> C1
        B6 --> C1
        C1 --> C2
        C2 --> C3
        C3 -->|Sim| C4
        C3 -->|Não| C5
        C4 --> C5
    end
    
    subgraph Envio
        D1{Validações OK?}
        D2[Mostra erros]
        D3[Envia para Legislativo]
        D4[Notifica Legislativo]
        
        C5 --> D1
        D1 -->|Não| D2
        D2 --> C2
        D1 -->|Sim| D3
        D3 --> D4
    end
```

## Validações por Etapa

```mermaid
flowchart LR
    subgraph Validações_Criação
        VC1[Tipo válido]
        VC2[Ementa presente]
        VC3[Autor autenticado]
        VC1 --> VC2 --> VC3
    end
    
    subgraph Validações_Envio
        VE1[Status correto]
        VE2[Conteúdo mínimo]
        VE3[É o autor]
        VE1 --> VE2 --> VE3
    end
    
    subgraph Validações_Revisão
        VR1[Status enviado_legislativo]
        VR2[Análises técnicas]
        VR3[Parecer presente]
        VR1 --> VR2 --> VR3
    end
    
    subgraph Validações_Assinatura
        VA1[Status aprovado]
        VA2[Leitura confirmada]
        VA3[Certificado válido]
        VA1 --> VA2 --> VA3
    end
    
    subgraph Validações_Protocolo
        VP1[Assinatura presente]
        VP2[Verificações OK]
        VP3[Número disponível]
        VP1 --> VP2 --> VP3
    end
```

## Integração com OnlyOffice

```mermaid
sequenceDiagram
    participant P as Parlamentar
    participant S as Sistema
    participant O as OnlyOffice
    participant DB as Database
    
    P->>S: Abre proposição para editar
    S->>DB: Busca dados da proposição
    DB-->>S: Retorna proposição
    S->>O: Gera token e config
    O-->>S: Token válido
    S->>P: Abre editor OnlyOffice
    P->>O: Edita documento
    O->>S: Callback com alterações
    S->>S: Valida RTF e converte parágrafos
    S->>S: Cache timestamp único
    S->>DB: Salva arquivo_path
    DB-->>S: Confirmação
    S-->>O: Status = 0 (sucesso)
    O-->>P: Documento salvo
```

## Fluxo de Assinatura Digital

```mermaid
sequenceDiagram
    participant P as Parlamentar
    participant S as Sistema
    participant PDF as Gerador PDF
    participant DB as Database
    
    P->>S: Acessa tela de assinatura
    S->>DB: Busca proposição aprovada
    DB-->>S: Dados da proposição
    S->>PDF: Gera PDF para visualização
    PDF-->>S: PDF gerado
    S->>P: Exibe PDF
    P->>S: Confirma leitura
    S->>DB: confirmacao_leitura = true
    P->>S: Aplica assinatura digital
    S->>S: Gera hash assinatura
    S->>DB: Salva assinatura_digital
    S->>S: Otimiza PDF (dompdf config)
    S->>PDF: Regenera PDF com QR Code
    PDF-->>S: PDF assinado
    S->>DB: Atualiza arquivo_pdf_path
    S->>DB: status = 'assinado'
    S->>P: Assinatura concluída
```

---

## Legenda

- 🟦 **Azul**: Ações do Parlamentar
- 🟩 **Verde**: Aprovações/Sucesso
- 🟥 **Vermelho**: Devoluções/Correções
- 🟨 **Amarelo**: Processamento/Espera
- 🟪 **Roxo**: Ações do Legislativo
- 🟧 **Laranja**: Ações do Protocolo

---

## 🔧 Template Universal - Especificações Técnicas

### Arquitetura do Sistema

```mermaid
C4Context
    Person(parlamentar, "Parlamentar", "Cria proposições usando Template Universal")
    Person(juridico, "Jurídico", "Analisa e revisa proposições")
    Person(admin, "Administrador", "Configura Template Universal")

    System_Boundary(legisinc, "Sistema Legisinc") {
        System(template_universal, "Template Universal", "Sistema único de templates adaptativos")
        System(onlyoffice, "OnlyOffice", "Editor de documentos RTF")
        System(proposicoes, "Módulo Proposições", "Gestão do fluxo legislativo")
    }

    System_Ext(database, "PostgreSQL", "Armazenamento de dados")

    Rel(parlamentar, template_universal, "Usa templates")
    Rel(juridico, proposicoes, "Analisa proposições")
    Rel(admin, template_universal, "Configura")
    
    Rel(template_universal, onlyoffice, "Processa RTF")
    Rel(proposicoes, database, "Persiste dados")
    Rel(template_universal, database, "Armazena templates")
```

### Variáveis do Template Universal

```mermaid
mindmap
  root((Template Universal<br/>29 Variáveis))
    Proposição
      tipo_proposicao
      numero_proposicao
      ementa
      texto
      justificativa
      protocolo
    Autor
      autor_nome
      autor_cargo
      autor_partido
    Datas
      data_atual
      data_criacao
      data_protocolo
      dia
      mes
      ano_atual
      mes_extenso
    Instituição
      municipio
      nome_camara
      endereco_camara
      telefone_camara
      email_camara
      cnpj_camara
    Estrutura
      imagem_cabecalho
      assinatura_padrao
      rodape_texto
      preambulo_dinamico
      clausula_vigencia
      categoria_tipo
    Status
      status
```

### Fluxo Técnico de Processamento RTF

```mermaid
sequenceDiagram
    participant Admin as 🔧 Admin
    participant TU as 📝 TemplateUniversal
    participant Proc as ⚙️ ProcessorService
    participant OO as 📄 OnlyOffice
    participant Fix as 🛠️ FixSeeder
    
    Note over Admin,Fix: Configuração Inicial
    Admin->>TU: Acessa /admin/templates/universal
    TU->>Proc: Cria template base RTF
    Proc->>Proc: Valida estrutura RTF básica
    Proc->>Proc: Processa imagem_cabecalho
    Proc->>TU: Template válido com imagem
    TU->>Admin: Interface de edição
    
    Note over Admin,Fix: Durante migrate:fresh --seed
    Fix->>TU: TemplateUniversalFixSeeder executa
    TU->>Proc: Gera conteúdo RTF correto
    Proc->>Proc: corrigirRTFCorrompido()
    Proc->>Proc: gerarCodigoRTFImagem()
    Proc->>Proc: validarEstruturaRTF()
    TU->>Fix: Template criado/corrigido
    
    Note over Admin,Fix: Uso pelo Parlamentar
    TU->>OO: Download RTF válido
    OO->>OO: Abre sem diálogo "Choose TXT options"
    OO->>Proc: Callback com alterações
    Proc->>Proc: Preserva parágrafos (\n → \\par)
    Proc->>TU: Salva versão final
```

### Resolução de Problemas Técnicos

```mermaid
flowchart TD
    Problem[❌ Problema: Choose TXT options]
    
    Problem --> Analysis{Análise do RTF}
    Analysis --> Corrupted[RTF corrompido<br/>Headers malformados]
    Analysis --> Encoding[Encoding incorreto<br/>text/plain vs application/rtf]
    Analysis --> Structure[Estrutura inválida<br/>Headers malformados]
    
    Corrupted --> Fix1[corrigirRTFCorrompido<br/>Corrige headers RTF<br/>Valida estrutura<br/>Ajusta formatação]
    
    Encoding --> Fix2[Headers HTTP corretos<br/>Content-Type: application/rtf<br/>charset=utf-8<br/>fileType: rtf]
    
    Structure --> Fix3[garantirRTFValido<br/>Validar início RTF<br/>Codificação UTF-8<br/>Estrutura completa]
    
    Fix1 --> Test[🧪 Teste Automático<br/>debug_template_universal.php]
    Fix2 --> Test
    Fix3 --> Test
    
    Test --> Success[✅ Editor OnlyOffice<br/>abre sem diálogos]
    Test --> Fail[❌ Ainda com problemas]
    
    Fail --> Analysis
    
    Success --> Seeder[TemplateUniversalFixSeeder<br/>preserva correções]
    Seeder --> Production[🚀 Produção]
    
    style Problem fill:#ffebee
    style Success fill:#e8f5e8
    style Production fill:#c8e6c9
```

### Benefícios do Template Universal

```mermaid
flowchart LR
    subgraph Antes["📝 Antes: 23 Templates"]
        A1[23 arquivos RTF separados]
        A2[Manutenção complexa]
        A3[Inconsistência entre tipos]
        A4[Templates podem corromper]
        A5[Difícil padronização]
        
        A1 --> A2 --> A3 --> A4 --> A5
    end
    
    subgraph Agora["🎨 Agora: Template Universal"]
        B1[1 template adaptativo]
        B2[Manutenção centralizada]
        B3[Consistência garantida]
        B4[Auto-correção automática]
        B5[Padrão LC 95/1998]
        
        B1 --> B2 --> B3 --> B4 --> B5
    end
    
    Antes -.->|Evolução| Agora
    
    style Antes fill:#ffebee
    style Agora fill:#e8f5e8
```

## 🚀 Melhorias Implementadas

### Performance
- ✅ **Cache inteligente** com timestamps únicos
- ✅ **PDF otimizado** com configurações dompdf
- ✅ **Limpeza automática** de arquivos antigos
- ✅ **Polling adaptativo** no OnlyOffice

### Qualidade
- ✅ **Validação RTF** com codificação UTF-8
- ✅ **Conversão de parágrafos** preservada
- ✅ **QR Code** nas assinaturas digitais
- ✅ **Backup automático** de dados críticos

### Segurança
- ✅ **Middleware de permissões** por role
- ✅ **Validação contextual** de acesso
- ✅ **Assinatura digital** com certificado
- ✅ **Logs detalhados** de todas as ações

---

*Diagramas gerados para o Sistema Legisinc v2.0*  
*Data: 30/08/2025*  
*Status: Produção com Melhores Práticas*