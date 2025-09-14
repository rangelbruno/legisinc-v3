# 🛡️ Diagramas de Fluxo PyHanko - Visualização Mermaid

## 📋 Visão Geral

Esta documentação contém **diagramas interativos Mermaid** que mostram visualmente como funciona o sistema de **Assinatura Digital PyHanko** no **Sistema Legisinc v2.2**.

---

## 🔄 1. Fluxo Principal de Assinatura

```mermaid
graph TD
    A[👤 Usuário Solicita Assinatura] --> B[📁 Upload Certificado PFX]
    B --> C[🔑 Informa Senha PFX]
    C --> D{🔒 Validação OpenSSL}
    D -->|✅ Válido| E[📄 Preparar PDF Base]
    D -->|❌ Inválido| F[⚠️ Erro: Certificado Inválido]
    
    E --> G{📋 PDF tem Campo Assinatura?}
    G -->|❌ Não| H[➕ Criar Campo AssinaturaDigital]
    G -->|✅ Sim| I[🐳 Docker Run --rm PyHanko]
    H --> I
    
    I --> J[🛡️ PyHanko Container Efêmero]
    J --> K[📝 Processar PAdES B-LT]
    K --> L[⏰ Adicionar Timestamp TSA]
    L --> M[📦 Embarcar CRL/OCSP]
    M --> N[✅ PDF Assinado Gerado]
    
    N --> O{🔍 Validação Automática}
    O -->|✅ Válido| P[💾 Salvar PDF Final]
    O -->|❌ Inválido| Q[⚠️ Erro na Assinatura]
    
    P --> R[🎉 Assinatura Concluída]
    
    style A fill:#e1f5fe,stroke:#01579b
    style J fill:#fff3e0,stroke:#f57c00
    style N fill:#e8f5e8,stroke:#2e7d32
    style R fill:#f3e5f5,stroke:#7b1fa2
```

---

## 🏗️ 2. Arquitetura do Container Efêmero

```mermaid
graph TB
    %% Sistema Host
    A[🖥️ Laravel App]
    B[AssinaturaDigitalService]
    C[Docker Command]

    %% Container Efêmero PyHanko
    D[🐳 pyhanko CLI]
    E[Carregar pyhanko.yml]
    F[Ler Certificado PFX]
    G[Processar PDF]
    H[Aplicar Assinatura PAdES]
    I[Adicionar Timestamp]
    J[Embarcar CRL/OCSP]
    K[Gerar PDF Final]

    %% Volumes Montados
    L[📁 /work - Documentos]
    M[🔐 /certs:ro - Certificados]
    N[⚙️ pyhanko.yml - Config]

    %% PDF Final
    O[PDF Assinado]
    P[Container Destruído]

    A --> B
    B --> C
    D --> E
    E --> F
    F --> G
    G --> H
    H --> I
    I --> J
    J --> K
    C -->|docker run --rm| D
    D -.-> L
    D -.-> M
    E -.-> N
    K --> O
    O --> P

    style D fill:#fff3e0,stroke:#f57c00
    style K fill:#e8f5e8,stroke:#2e7d32
    style P fill:#ffebee,stroke:#c62828
```

---

## ⚙️ 3. Ciclo de Vida do Container

```mermaid
sequenceDiagram
    participant L as Laravel App
    participant D as Docker Engine
    participant P as PyHanko Container
    participant T as TSA FreeTSA
    participant F as Filesystem
    
    Note over L,F: 🔄 Processo de Assinatura Digital
    
    L->>+D: docker run --rm legisinc-pyhanko
    D->>+P: Criar container efêmero
    
    P->>F: Ler pyhanko.yml
    P->>F: Carregar certificado.pfx
    P->>F: Processar documento.pdf
    
    P->>+T: Solicitar timestamp
    T-->>-P: Token timestamp válido
    
    P->>F: Aplicar assinatura PAdES B-LT
    P->>F: Embarcar CRL/OCSP
    P->>F: Salvar PDF assinado
    
    P-->>-D: Exit code 0 (sucesso)
    D->>L: Container finalizado
    D->>D: Container destruído automaticamente
    
    Note over L,F: ✅ PDF PAdES B-LT Compliant Gerado
```

---

## 🧪 4. Fluxo de Testes Disponíveis

```mermaid
graph LR
    A[🧪 Scripts de Teste] --> B[📋 Funcional Básico]
    A --> C[🐳 Docker Compose]
    A --> D[🛡️ Produção Blindada]
    
    B --> B1[Gerar Certificado Teste]
    B --> B2[Copiar PDF Real]
    B --> B3[Executar Assinatura]
    B --> B4[Validar Resultado]
    
    C --> C1[Usar docker compose run]
    C --> C2[Testar Profiles]
    C --> C3[Verificar Volumes]
    C --> C4[Validar Organização]
    
    D --> D1[Modo Não-Interativo]
    D --> D2[PAdES B-LT + CRL/OCSP]
    D --> D3[Validation Contexts]
    D --> D4[Segurança Blindada]
    
    B4 --> E[✅ Resultado Final]
    C4 --> E
    D4 --> E
    
    style A fill:#e1f5fe,stroke:#01579b
    style E fill:#e8f5e8,stroke:#2e7d32
```

---

## 🔒 5. Níveis de Segurança Implementados

```mermaid
mindmap
  root((🛡️ Segurança PyHanko))
    🔐 Certificado
      Validação OpenSSL Nativa
      Senha via Environment Variable
      Verificação PKCS#12
    🐳 Container
      Efêmero (--rm)
      Read-Only Mounts (:ro)
      Sem Acesso Host
      Network Bridge Isolado
    📝 Logs
      Senhas Filtradas ([REDACTED])
      Comandos Limpos
      Debug Controlado
    🔄 Processo  
      Timeout 3 minutos
      Error Handling Robusto
      Fallback Simulado
      Validação Dupla
```

---

## 📊 6. Estados do Sistema PyHanko

```mermaid
stateDiagram-v2
    [*] --> Inativo: Sistema Inicializado
    
    Inativo --> ValidandoCertificado: Upload PFX + Senha
    ValidandoCertificado --> CertificadoInvalido: Falha OpenSSL
    ValidandoCertificado --> PreparandoAssinatura: Certificado Válido
    
    CertificadoInvalido --> Inativo: Corrigir Certificado
    
    PreparandoAssinatura --> CriandoCampo: PDF sem campo
    PreparandoAssinatura --> ExecutandoPyHanko: PDF com campo
    CriandoCampo --> ExecutandoPyHanko: Campo criado
    
    ExecutandoPyHanko --> AssinaturaSucesso: Container exit 0
    ExecutandoPyHanko --> AssinaturaFalha: Container exit ≠ 0
    ExecutandoPyHanko --> TimeoutError: Timeout 3min
    
    AssinaturaSucesso --> ValidandoResultado: PDF gerado
    AssinaturaFalha --> Inativo: Log erro
    TimeoutError --> Inativo: Process killed
    
    ValidandoResultado --> Concluido: Validação OK
    ValidandoResultado --> Inativo: PDF inválido
    
    Concluido --> Inativo: Nova assinatura
    
    note right of ExecutandoPyHanko
        Container efêmero:
        - Criado sob demanda
        - Executado e destruído
        - Zero overhead
    end note
```

---

## 🏛️ 7. Integração com Sistema Legisinc

```mermaid
graph TB
    %% Sistema de Assinatura Digital - Context Diagram
    User[👤 Usuário<br/>Parlamentar/Legislativo]
    Legisinc[🏛️ Sistema Legisinc<br/>Plataforma legislativa v2.2]
    PyHanko[🛡️ PyHanko Container<br/>Assinatura PAdES efêmera]
    TSA[⏰ TSA FreeTSA<br/>Servidor de timestamp]
    CRL[📋 CRL/OCSP<br/>Validação certificados]

    User -->|Acessa proposições| Legisinc
    Legisinc -->|docker run --rm| PyHanko
    PyHanko -->|HTTPS timestamp| TSA
    PyHanko -->|Validação cadeia| CRL

    style User fill:#e1f5fe
    style Legisinc fill:#f3e5f5
    style PyHanko fill:#fff3e0
    style TSA fill:#e8f5e8
    style CRL fill:#fce4ec
```

---

## 📱 8. Interface Administrativa

```mermaid
journey
    title 📱 Jornada do Administrador - Página PyHanko
    section Acessar Sistema
      Login Admin: 5: Administrador
      Menu Lateral: 4: Administrador
      Clicar PyHanko: 5: Administrador
    section Verificar Status
      Página Carrega: 5: Administrador
      Status Automático: 4: Administrador
      Verificar Manual: 5: Administrador
    section Executar Testes
      Escolher Tipo: 4: Administrador
      Executar Script: 3: Administrador
      Ver Resultado: 5: Administrador
    section Documentação
      Links Integrados: 5: Administrador
      Scripts Listados: 4: Administrador
      Troubleshooting: 3: Administrador
```

---

## 🚀 9. Deploy e Monitoramento

```mermaid
gitgraph
    commit id: "v2.1 PyHanko Base"
    branch funcional
    checkout funcional
    commit id: "Implementação Inicial"
    commit id: "Testes Validados"
    checkout main
    merge funcional
    commit id: "v2.2 Funcional ✅"
    
    branch blindado
    checkout blindado
    commit id: "Modo Não-Interativo"
    commit id: "PAdES B-LT + CRL/OCSP"
    commit id: "Validation Contexts"
    commit id: "Segurança Blindada"
    checkout main
    merge blindado
    commit id: "v2.2 Blindado 🛡️"
    
    branch final
    checkout final
    commit id: "Docker Compose Profiles"
    commit id: "5 Arquiteturas Deploy"
    commit id: "Scripts de Teste"
    commit id: "Documentação Completa"
    commit id: "Página Administrativa"
    checkout main
    merge final
    commit id: "v2.2 Final 🎉"
```

---

## 💻 10. Comandos em Execução

```mermaid
graph TB
    %% Comandos PyHanko
    A[💻 Comandos PyHanko<br/>🔍 Verificar Imagem]
    B[docker images | grep pyhanko]
    C[legisinc-pyhanko latest 397MB]

    %% Testar Binário
    D[🧪 Testar Binário<br/>docker run --rm legisinc-pyhanko --version]
    E[pyHanko, version 0.29.1 (CLI 0.1.2)]

    %% Assinatura Efêmera
    F[🖋️ Assinatura Efêmera<br/>docker run --rm]
    G[-v /dados:/work]
    H[-v /certs:/certs:ro]
    I[-e PFX_PASS='senha']
    J[legisinc-pyhanko sign addsig...]

    %% Monitorar Execução
    K[👁️ Monitorar Execução<br/>watch docker ps]
    L[PyHanko aparece temporariamente]
    M[Container destruído automaticamente]

    A --> B --> C
    D --> E
    F --> G --> H --> I --> J
    K --> L --> M

    style A fill:#e1f5fe,stroke:#01579b
    style D fill:#fff3e0,stroke:#f57c00
    style F fill:#e8f5e8,stroke:#2e7d32
    style K fill:#f3e5f5,stroke:#7b1fa2
```

---

## 🎯 Como Usar os Diagramas

### **📱 Na Interface Web**
Os diagramas Mermaid são renderizados automaticamente na página administrativa:
- **URL**: `http://localhost:8001/admin/pyhanko-fluxo`
- **Seção**: "Visualização do Fluxo"
- **Interativo**: Zoom, pan, export

### **📚 Na Documentação**
Visualizar em editores que suportam Mermaid:
- **GitHub**: Renderização automática em `.md`
- **VS Code**: Extensão "Mermaid Preview"  
- **GitLab**: Suporte nativo
- **Notion**: Import como diagrama

### **🔧 Editar Diagramas**
- **Online**: [mermaid.live](https://mermaid.live)
- **Local**: Mermaid CLI ou extensões
- **Integrado**: VS Code + Mermaid extension

---

## 🎊 Benefícios dos Diagramas

### **✅ Visualização Clara**
- **Fluxo completo** em um diagrama
- **Estados do sistema** facilmente identificáveis
- **Pontos de falha** destacados visualmente

### **✅ Documentação Viva**
- **Atualização fácil** conforme evolução
- **Versionamento** junto com código
- **Colaboração** entre equipes técnicas

### **✅ Debugging Visual**
- **Identificar gargalos** no processo
- **Entender dependências** entre componentes
- **Planejar melhorias** arquiteturais

---

**📝 Autor**: Sistema Legisinc PyHanko Team  
**📅 Criado em**: 08/09/2025  
**🔧 Versão**: v2.2 Final  
**🎯 Diagramas**: 10 tipos diferentes para visualização completa

---

> **💡 Dica**: Use [mermaid.live](https://mermaid.live) para editar e testar diagramas antes de integrar na documentação!