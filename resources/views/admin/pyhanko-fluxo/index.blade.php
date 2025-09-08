@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    🛡️ Fluxo Assinatura Digital PyHanko
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Administração</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Sistema PyHanko</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <!-- Status do Sistema -->
            <div class="row g-5 g-xl-10 mb-5">
                <!-- Card Principal -->
                <div class="col-xl-12">
                    <div class="card card-flush">
                        <div class="card-header align-items-center border-0 mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="fw-bold mb-2 text-dark">🏗️ Arquitetura Container Efêmero</span>
                                <span class="text-muted fw-semibold fs-7">Sistema Legisinc v2.2 Final</span>
                            </h3>
                            <div class="card-toolbar">
                                <button class="btn btn-primary btn-sm" onclick="verificarStatus()">
                                    <i class="ki-duotone ki-arrows-circle fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Verificar Status
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="row g-5">
                                <!-- Informações do Sistema -->
                                <div class="col-md-6">
                                    <div class="bg-light-primary rounded border-dashed border-primary p-6">
                                        <h4 class="text-primary mb-3">📊 Informações do Sistema</h4>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-between py-2">
                                                <span class="text-gray-700">Versão:</span>
                                                <span class="fw-bold">{{ $systemInfo['versao_sistema'] }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2">
                                                <span class="text-gray-700">PyHanko:</span>
                                                <span class="fw-bold text-success" id="pyhanko-version">
                                                    {{ $systemInfo['pyhanko_version'] ?? 'Verificando...' }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2">
                                                <span class="text-gray-700">Arquitetura:</span>
                                                <span class="fw-bold text-info">{{ $systemInfo['arquitetura_atual'] }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between py-2">
                                                <span class="text-gray-700">Última Atualização:</span>
                                                <span class="fw-bold">{{ $systemInfo['ultima_atualizacao'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status Componentes -->
                                <div class="col-md-6">
                                    <div class="bg-light-success rounded border-dashed border-success p-6">
                                        <h4 class="text-success mb-3">✅ Status Componentes</h4>
                                        <div id="status-componentes">
                                            <div class="d-flex align-items-center py-2">
                                                <div class="bullet bullet-dot bg-warning me-3"></div>
                                                <span class="text-gray-700">Verificando componentes...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Como Funciona -->
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-xl-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <h3 class="card-title">🔄 Como Funciona o Fluxo</h3>
                        </div>
                        <div class="card-body">
                            <div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row flex-row-fluid">
                                <!-- Step 1 -->
                                <div class="stepper-item mx-8 my-4 current">
                                    <div class="stepper-wrapper">
                                        <div class="stepper-icon">
                                            <i class="stepper-check fas fa-check"></i>
                                            <span class="stepper-number">1</span>
                                        </div>
                                        <div class="stepper-label">
                                            <h3 class="stepper-title fs-5">Upload Certificado PFX</h3>
                                            <div class="stepper-desc fw-normal">
                                                Usuário faz upload do certificado .pfx e informa senha
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Step 2 -->
                                <div class="stepper-item mx-8 my-4">
                                    <div class="stepper-wrapper">
                                        <div class="stepper-icon">
                                            <i class="stepper-check fas fa-check"></i>
                                            <span class="stepper-number">2</span>
                                        </div>
                                        <div class="stepper-label">
                                            <h3 class="stepper-title fs-5">Validação OpenSSL</h3>
                                            <div class="stepper-desc fw-normal">
                                                Sistema valida certificado e senha com OpenSSL nativo
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Step 3 -->
                                <div class="stepper-item mx-8 my-4">
                                    <div class="stepper-wrapper">
                                        <div class="stepper-icon">
                                            <i class="stepper-check fas fa-check"></i>
                                            <span class="stepper-number">3</span>
                                        </div>
                                        <div class="stepper-label">
                                            <h3 class="stepper-title fs-5">PyHanko Container</h3>
                                            <div class="stepper-desc fw-normal">
                                                <code>docker run --rm</code> executa container efêmero
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Step 4 -->
                                <div class="stepper-item mx-8 my-4">
                                    <div class="stepper-wrapper">
                                        <div class="stepper-icon">
                                            <i class="stepper-check fas fa-check"></i>
                                            <span class="stepper-number">4</span>
                                        </div>
                                        <div class="stepper-label">
                                            <h3 class="stepper-title fs-5">PDF PAdES B-LT</h3>
                                            <div class="stepper-desc fw-normal">
                                                Gera PDF assinado com timestamp e CRL/OCSP embarcados
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verificações Técnicas -->
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-xl-6">
                    <div class="card card-flush">
                        <div class="card-header">
                            <h3 class="card-title">🔍 Verificações Técnicas</h3>
                        </div>
                        <div class="card-body">
                            <div class="notice bg-light-info rounded border-info border border-dashed p-6">
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Container Efêmero</h4>
                                        <div class="fs-6 text-gray-700">
                                            <strong>✅ Correto:</strong> PyHanko NÃO aparece no <code>docker-compose up -d</code><br>
                                            <strong>🐳 Execução:</strong> Apenas durante assinatura via <code>docker run --rm</code><br>
                                            <strong>📊 Monitorar:</strong> <code>watch docker ps</code> durante assinatura
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="separator my-6"></div>
                            
                            <div class="row g-5">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <span class="bullet bg-success me-3"></span>
                                        <span class="text-gray-700 fw-semibold fs-7">Zero overhead quando inativo</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <span class="bullet bg-success me-3"></span>
                                        <span class="text-gray-700 fw-semibold fs-7">Ambiente limpo por assinatura</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <span class="bullet bg-success me-3"></span>
                                        <span class="text-gray-700 fw-semibold fs-7">Segurança máxima</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <span class="bullet bg-success me-3"></span>
                                        <span class="text-gray-700 fw-semibold fs-7">Escalabilidade paralela</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6">
                    <div class="card card-flush">
                        <div class="card-header">
                            <h3 class="card-title">🧪 Scripts de Teste</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-4">
                                <!-- Teste Funcional -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark">Funcional Básico</div>
                                        <div class="text-muted fs-7">Teste completo com certificado auto-assinado</div>
                                    </div>
                                    <button class="btn btn-light-primary btn-sm" onclick="executarTeste('funcional')">
                                        <i class="ki-duotone ki-play fs-4"></i>
                                        Executar
                                    </button>
                                </div>
                                
                                <!-- Teste Compose -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark">Docker Compose</div>
                                        <div class="text-muted fs-7">Teste via compose run com profiles</div>
                                    </div>
                                    <button class="btn btn-light-info btn-sm" onclick="executarTeste('compose')">
                                        <i class="ki-duotone ki-play fs-4"></i>
                                        Executar
                                    </button>
                                </div>
                                
                                <!-- Teste Blindado -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark">Produção Blindada</div>
                                        <div class="text-muted fs-7">Teste com todas as otimizações</div>
                                    </div>
                                    <button class="btn btn-light-success btn-sm" onclick="executarTeste('blindado')">
                                        <i class="ki-duotone ki-play fs-4"></i>
                                        Executar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Output de Testes -->
            <div class="row g-5 g-xl-10 mb-5" id="teste-output-section" style="display: none;">
                <div class="col-xl-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <h3 class="card-title">📊 Resultado do Teste</h3>
                            <div class="card-toolbar">
                                <button class="btn btn-light-dark btn-sm" onclick="fecharTeste()">
                                    <i class="ki-duotone ki-cross fs-3"></i>
                                    Fechar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="teste-loading" class="text-center py-10">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Executando teste...</span>
                                </div>
                                <div class="text-muted mt-3">Executando teste PyHanko...</div>
                            </div>
                            <pre id="teste-output" class="bg-dark text-light p-6 rounded" style="display: none; max-height: 500px; overflow-y: auto;"></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visualização do Fluxo -->
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-xl-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <h3 class="card-title">📊 Visualização do Fluxo - Diagramas Interativos</h3>
                            <div class="card-toolbar">
                                <div class="btn-group" data-kt-buttons="true">
                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary btn-sm active">
                                        <input class="btn-check" type="radio" name="diagram_type" value="principal" checked onclick="showDiagram('principal')"/>
                                        Fluxo Principal
                                    </label>
                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary btn-sm">
                                        <input class="btn-check" type="radio" name="diagram_type" value="arquitetura" onclick="showDiagram('arquitetura')"/>
                                        Arquitetura
                                    </label>
                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary btn-sm">
                                        <input class="btn-check" type="radio" name="diagram_type" value="seguranca" onclick="showDiagram('seguranca')"/>
                                        Segurança
                                    </label>
                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary btn-sm">
                                        <input class="btn-check" type="radio" name="diagram_type" value="estados" onclick="showDiagram('estados')"/>
                                        Estados
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Diagrama Fluxo Principal -->
                            <div id="diagram-principal" class="mermaid-diagram">
                                <div class="mermaid">
flowchart TD
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
                                </div>
                            </div>
                            
                            <!-- Diagrama Arquitetura -->
                            <div id="diagram-arquitetura" class="mermaid-diagram" style="display: none;">
                                <div class="mermaid">
graph TB
    subgraph "Sistema Host"
        A[Laravel App] --> B[AssinaturaDigitalService]
        B --> C[Docker Command]
    end
    
    subgraph "Container Efêmero PyHanko"
        D[pyhanko CLI] --> E[Carregar pyhanko.yml]
        E --> F[Ler Certificado PFX]
        F --> G[Processar PDF]
        G --> H[Aplicar Assinatura PAdES]
        H --> I[Adicionar Timestamp]
        I --> J[Embarcar CRL/OCSP]
        J --> K[Gerar PDF Final]
    end
    
    subgraph "Volumes Montados"
        L[/work - Documentos]
        M[/certs:ro - Certificados]
        N[pyhanko.yml - Config]
    end
    
    C -->|docker run --rm| D
    D -.-> L
    D -.-> M
    E -.-> N
    K --> O[PDF Assinado]
    O --> P[Container Destruído]
    
    style D fill:#fff3e0,stroke:#f57c00
    style K fill:#e8f5e8,stroke:#2e7d32
    style P fill:#ffebee,stroke:#c62828
                                </div>
                            </div>
                            
                            <!-- Diagrama Segurança -->
                            <div id="diagram-seguranca" class="mermaid-diagram" style="display: none;">
                                <div class="mermaid">
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
                                </div>
                            </div>
                            
                            <!-- Diagrama Estados -->
                            <div id="diagram-estados" class="mermaid-diagram" style="display: none;">
                                <div class="mermaid">
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
                                </div>
                            </div>
                            
                            <div class="notice bg-light-primary rounded border-primary border border-dashed p-6 mt-6">
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">📋 Como usar os Diagramas</h4>
                                        <div class="fs-6 text-gray-700">
                                            <strong>🔄 Fluxo Principal:</strong> Visualiza o processo completo de assinatura<br>
                                            <strong>🏗️ Arquitetura:</strong> Mostra a estrutura de containers e volumes<br>
                                            <strong>🛡️ Segurança:</strong> Demonstra as camadas de proteção implementadas<br>
                                            <strong>📊 Estados:</strong> Acompanha os estados do sistema durante o processo
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentação Rápida -->
            <div class="row g-5 g-xl-10 mb-5">
                <div class="col-xl-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <h3 class="card-title">📚 Documentação Técnica</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-5">
                                <div class="col-md-3">
                                    <div class="d-flex flex-stack">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-document fs-2 text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1 flex-wrap">
                                            <div class="text-dark fw-bold text-hover-primary fs-6">
                                                Implementação Completa
                                            </div>
                                            <span class="text-muted fw-semibold">docs/ASSINATURA-DIGITAL-PYHANKO.md</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="d-flex flex-stack">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-setting-3 fs-2 text-success"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1 flex-wrap">
                                            <div class="text-dark fw-bold text-hover-primary fs-6">
                                                Opções de Deploy
                                            </div>
                                            <span class="text-muted fw-semibold">docs/technical/OPCOES-DEPLOY-PYHANKO.md</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="d-flex flex-stack">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-warning">
                                                <i class="ki-duotone ki-code fs-2 text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1 flex-wrap">
                                            <div class="text-dark fw-bold text-hover-primary fs-6">
                                                Scripts de Teste
                                            </div>
                                            <span class="text-muted fw-semibold">scripts/teste-pyhanko-*.sh</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="d-flex flex-stack">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-info">
                                                <i class="ki-duotone ki-chart fs-2 text-info"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1 flex-wrap">
                                            <div class="text-dark fw-bold text-hover-primary fs-6">
                                                Diagramas Mermaid
                                            </div>
                                            <span class="text-muted fw-semibold">docs/DIAGRAMAS-FLUXO-PYHANKO-MERMAID.md</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end::Content-->
</div>

<script>
// Verificar status do sistema
async function verificarStatus() {
    try {
        const response = await fetch('{{ route("admin.pyhanko-fluxo.testar-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            atualizarStatusComponentes(data.dados);
            toastr.success('Status verificado com sucesso!');
        } else {
            toastr.error(data.message || 'Erro ao verificar status');
        }
    } catch (error) {
        console.error('Erro:', error);
        toastr.error('Erro na comunicação com o servidor');
    }
}

// Atualizar status dos componentes
function atualizarStatusComponentes(dados) {
    const container = document.getElementById('status-componentes');
    
    let html = '';
    
    // Imagem PyHanko
    const imgStatus = dados.imagem_pyhanko.existe ? 'success' : 'danger';
    const imgIcon = dados.imagem_pyhanko.existe ? 'check' : 'cross';
    html += `
        <div class="d-flex align-items-center py-2">
            <div class="bullet bullet-dot bg-${imgStatus} me-3"></div>
            <span class="text-gray-700">Imagem PyHanko: ${dados.imagem_pyhanko.detalhes}</span>
        </div>
    `;
    
    // Binário funcionando
    const binStatus = dados.binario_funcionando.funcionando ? 'success' : 'danger';
    html += `
        <div class="d-flex align-items-center py-2">
            <div class="bullet bullet-dot bg-${binStatus} me-3"></div>
            <span class="text-gray-700">Binário: ${dados.binario_funcionando.versao || 'Não funciona'}</span>
        </div>
    `;
    
    // Scripts
    const scriptsOk = Object.values(dados.scripts_teste).every(script => script.existe);
    const scriptsStatus = scriptsOk ? 'success' : 'warning';
    html += `
        <div class="d-flex align-items-center py-2">
            <div class="bullet bullet-dot bg-${scriptsStatus} me-3"></div>
            <span class="text-gray-700">Scripts de teste: ${scriptsOk ? 'Disponíveis' : 'Alguns faltando'}</span>
        </div>
    `;
    
    // Timestamp
    html += `
        <div class="d-flex align-items-center py-2">
            <div class="bullet bullet-dot bg-info me-3"></div>
            <span class="text-gray-700">Verificado em: ${dados.timestamp}</span>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Atualizar versão PyHanko
    if (dados.binario_funcionando.versao) {
        document.getElementById('pyhanko-version').textContent = dados.binario_funcionando.versao;
    }
}

// Executar teste
async function executarTeste(tipo) {
    // Mostrar seção de output
    document.getElementById('teste-output-section').style.display = 'block';
    document.getElementById('teste-loading').style.display = 'block';
    document.getElementById('teste-output').style.display = 'none';
    
    // Scroll para a seção
    document.getElementById('teste-output-section').scrollIntoView({ behavior: 'smooth' });
    
    try {
        const response = await fetch('{{ route("admin.pyhanko-fluxo.executar-teste") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ tipo_teste: tipo })
        });
        
        const data = await response.json();
        
        // Ocultar loading
        document.getElementById('teste-loading').style.display = 'none';
        
        // Mostrar output
        const outputElement = document.getElementById('teste-output');
        outputElement.style.display = 'block';
        outputElement.textContent = data.output || 'Sem output';
        
        if (data.status === 'success') {
            toastr.success(`Teste ${tipo} executado com sucesso!`);
        } else {
            toastr.warning(`Teste ${tipo} concluído com avisos`);
        }
        
    } catch (error) {
        console.error('Erro:', error);
        document.getElementById('teste-loading').style.display = 'none';
        toastr.error('Erro ao executar teste');
    }
}

// Fechar teste
function fecharTeste() {
    document.getElementById('teste-output-section').style.display = 'none';
}

// Função para mostrar diagramas
function showDiagram(type) {
    // Ocultar todos os diagramas
    document.querySelectorAll('.mermaid-diagram').forEach(function(diagram) {
        diagram.style.display = 'none';
    });
    
    // Mostrar o diagrama selecionado
    document.getElementById('diagram-' + type).style.display = 'block';
    
    // Re-renderizar Mermaid se necessário
    if (window.mermaid) {
        window.mermaid.init();
    }
}

// Verificar status automaticamente ao carregar página
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(verificarStatus, 1000);
    
    // Inicializar Mermaid se disponível
    if (window.mermaid) {
        window.mermaid.initialize({
            startOnLoad: true,
            theme: 'default',
            flowchart: {
                useMaxWidth: true,
                htmlLabels: true
            }
        });
    }
});
</script>

<!-- Mermaid.js para renderização de diagramas -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@10.6.1/dist/mermaid.min.js"></script>
<style>
.mermaid-diagram {
    text-align: center;
    overflow-x: auto;
}

.mermaid {
    width: 100%;
    min-height: 400px;
}

/* Estilos para botões de diagrama */
.btn-group label.btn-outline.btn-outline-dashed.btn-active-light-primary {
    border: 1px dashed #009ef7;
    color: #009ef7;
}

.btn-group label.btn-outline.btn-outline-dashed.btn-active-light-primary.active {
    background-color: #f1faff;
    border-color: #009ef7;
    color: #009ef7;
}
</style>
@endsection