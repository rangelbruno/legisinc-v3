@extends('components.layouts.app')

@section('title', 'Documentação - ' . $moduleData['name'])

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_toolbar" class="toolbar py-2">
        <div id="kt_toolbar_container" class="container-fluid d-flex align-items-center">
            <div class="flex-grow-1 flex-shrink-0 me-5">
                <!--begin::Page title-->
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                     data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                     class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <h1 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">
                        <a href="{{ route('admin.technical-doc.index') }}" class="text-muted text-hover-primary me-3">
                            <i class="ki-duotone ki-arrow-left fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                        <i class="ki-duotone {{ $moduleData['icon'] }} fs-1 text-{{ $moduleData['color'] }} me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        {{ $moduleData['name'] }}
                    </h1>
                </div>
                <!--end::Page title-->
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">

            <!--begin::Module Info-->
            <div class="card mb-8">
                <div class="card-body p-8">
                    <div class="d-flex align-items-center mb-6">
                        <i class="ki-duotone {{ $moduleData['icon'] }} fs-2x text-{{ $moduleData['color'] }} me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div>
                            <h2 class="text-gray-900 fw-bolder mb-2">{{ $moduleData['name'] }}</h2>
                            <p class="text-gray-600 mb-0 fs-5">{{ $moduleData['description'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Module Info-->

            <!--begin::Tabs-->
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                        <li class="nav-item">
                            <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab" href="#kt_tab_flow">
                                <i class="ki-duotone ki-chart-line-up fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Fluxo do Processo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#kt_tab_files">
                                <i class="ki-duotone ki-folder fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Arquivos Envolvidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#kt_tab_endpoints">
                                <i class="ki-duotone ki-router fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Endpoints & APIs
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">

                        <!--begin::Flow Tab-->
                        <div class="tab-pane fade show active" id="kt_tab_flow" role="tabpanel">
                            @if($moduleData['flow'])
                            <div class="mb-10">
                                <h4 class="text-gray-900 fw-bolder mb-6">Fluxo do Processo</h4>

                                @if($module == 'proposicoes')
                                <!--begin::Proposições Flow-->
                                <div class="mermaid">
flowchart TD
    A[👤 Parlamentar<br/>Acessa Sistema] --> B[📝 Criar Proposição<br/>Formulário]
    B --> C{🎯 Seleciona Tipo<br/>Proposição}
    C --> D[📄 Template Aplicado<br/>Automaticamente]
    D --> E[✏️ Edição OnlyOffice<br/>Tempo Real]
    E --> F[💾 Salvamento<br/>Automático]
    F --> G[📤 Enviar para<br/>Legislativo]
    G --> H[👨‍💼 Revisão<br/>Legislativo]
    H --> I{✅ Aprovado?}
    I -->|Sim| J[✍️ Assinatura<br/>Digital]
    I -->|Não| K[🔄 Retorna para<br/>Parlamentar]
    K --> E
    J --> L[📋 Protocolo<br/>Oficial]
    L --> M[📚 Arquivo<br/>Legislativo]

    style A fill:#e1f5fe
    style J fill:#e8f5e8
    style L fill:#fff3e0
    style M fill:#fce4ec
                </div>
                <!--end::Proposições Flow-->

                @elseif($module == 'onlyoffice')
                <!--begin::OnlyOffice Sequence-->
                <div class="mermaid">
sequenceDiagram
    participant U as 👤 Usuário
    participant S as 🖥️ Sistema
    participant O as 📝 OnlyOffice
    participant DB as 💾 Database

    U->>S: Abre editor
    S->>S: Gera document key único
    S->>O: Configura editor com RTF
    O->>U: Interface de edição

    loop Edição em tempo real
        U->>O: Edita documento
        O->>S: Callback de salvamento
        S->>DB: Atualiza proposição
        S->>S: Polling check (15s)
    end

    U->>O: Salva e fecha
    O->>S: Callback final
    S->>DB: Salvamento definitivo
    S->>U: Confirma fechamento
                </div>
                <!--end::OnlyOffice Sequence-->

                @elseif($module == 'templates')
                <!--begin::Templates Flow-->
                <div class="mermaid">
flowchart LR
    A[📋 Tipo Proposição<br/>Selecionado] --> B[🔍 Busca Template<br/>Específico]
    B --> C{📄 Template<br/>Encontrado?}
    C -->|Sim| D[📝 Template<br/>Específico]
    C -->|Não| E[🌐 Template<br/>Universal]
    D --> F[🔧 Processa<br/>Variáveis]
    E --> F
    F --> G[💾 Gera RTF<br/>Final]
    G --> H[📤 Entrega para<br/>OnlyOffice]

    style D fill:#e8f5e8
    style E fill:#fff3e0
    style F fill:#e1f5fe
                </div>
                <!--end::Templates Flow-->

                @elseif($module == 'assinatura')
                <!--begin::Assinatura Flow-->
                <div class="mermaid">
flowchart TD
    A[📋 Proposição<br/>Aprovada] --> B[🔐 Upload<br/>Certificado]
    B --> C[🔑 Validação<br/>Senha]
    C --> D{✅ Certificado<br/>Válido?}
    D -->|Não| E[❌ Erro de<br/>Validação]
    D -->|Sim| F[📄 Gera PDF<br/>Final]
    F --> G[✍️ Assina<br/>Digitalmente]
    G --> H[🔗 QR Code<br/>Validação]
    H --> I[📋 Protocolo<br/>Disponível]
    E --> B

    style D fill:#e1f5fe
    style G fill:#e8f5e8
    style H fill:#fff3e0
    style I fill:#fce4ec
                </div>
                <!--end::Assinatura Flow-->
                @endif

                <div class="separator my-8"></div>

                <div class="row">
                    @foreach($moduleData['flow']['steps'] as $step => $description)
                    <div class="col-md-6 col-lg-4 mb-6">
                        <div class="d-flex align-items-start">
                            <div class="symbol symbol-40px symbol-circle me-4">
                                <div class="symbol-label bg-light-{{ $moduleData['color'] }} text-{{ $moduleData['color'] }} fw-bold fs-6">
                                    {{ explode('.', $step)[0] }}
                                </div>
                            </div>
                            <div>
                                <h6 class="text-gray-900 fw-bolder mb-2">{{ substr($step, 3) }}</h6>
                                <p class="text-gray-600 mb-0 fs-7">{{ $description }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            @else
                <div class="alert alert-info">
                    <h6 class="alert-heading">Fluxo não documentado</h6>
                    <p class="mb-0">O fluxo para este módulo ainda não foi documentado.</p>
                </div>
            @endif
            </div>
        </div>
        <!--end::Flow Tab-->

        <!--begin::Files Tab-->
        <div class="tab-pane fade" id="kt_tab_files" role="tabpanel">
            <div class="mb-10">
                <h4 class="text-gray-900 fw-bolder mb-6">Arquivos do Módulo</h4>

                @foreach($moduleData['files'] as $type => $files)
                <div class="mb-8">
                    <h6 class="text-gray-800 fw-bold text-uppercase mb-4">
                        <i class="ki-duotone ki-folder fs-2 text-{{ $moduleData['color'] }} me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ ucfirst($type) }}
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                            <tbody>
                                @foreach($files as $file)
                                <tr>
                                    <td class="p-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-code fs-2 text-primary me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            <div>
                                                <span class="text-gray-900 fw-bolder d-block">{{ basename($file) }}</span>
                                                <span class="text-muted fs-7">{{ $file }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if(file_exists(base_path($file)))
                                            <span class="badge badge-light-success">✓ Existe</span>
                                        @else
                                            <span class="badge badge-light-danger">✗ Não encontrado</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <!--end::Files Tab-->

        <!--begin::Endpoints Tab-->
        <div class="tab-pane fade" id="kt_tab_endpoints" role="tabpanel">
            <div class="mb-10">
                <h4 class="text-gray-900 fw-bolder mb-6">Endpoints & APIs</h4>

                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">Método + Rota</th>
                                <th>Descrição</th>
                                <th class="min-w-100px text-center">Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moduleData['endpoints'] as $endpoint => $description)
                            <tr>
                                <td class="p-4">
                                    @php
                                        preg_match('/^(GET|POST|PUT|DELETE|PATCH)\s+(.+)$/', $endpoint, $matches);
                                        $method = $matches[1] ?? '';
                                        $route = $matches[2] ?? $endpoint;

                                        $methodColor = [
                                            'GET' => 'success',
                                            'POST' => 'primary',
                                            'PUT' => 'warning',
                                            'DELETE' => 'danger',
                                            'PATCH' => 'info'
                                        ][$method] ?? 'secondary';
                                    @endphp

                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light-{{ $methodColor }} me-3 fw-bold">{{ $method }}</span>
                                        <code class="text-gray-800">{{ $route }}</code>
                                    </div>
                                </td>
                                <td class="text-gray-700">{{ $description }}</td>
                                <td class="text-center">
                                    @if(strpos($route, '/api/') === 0)
                                        <span class="badge badge-light-info">API</span>
                                    @else
                                        <span class="badge badge-light-primary">Web</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(count($moduleData['routes']) > 0)
                <div class="mt-10">
                    <h6 class="text-gray-800 fw-bold mb-4">Rotas Completas Registradas</h6>

                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th>Nome da Rota</th>
                                    <th>URI</th>
                                    <th>Métodos</th>
                                    <th>Controller</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_slice($moduleData['routes'], 0, 10) as $route)
                                <tr>
                                    <td><code class="text-primary">{{ $route['name'] }}</code></td>
                                    <td><code>{{ $route['uri'] }}</code></td>
                                    <td>
                                        @foreach($route['methods'] as $method)
                                            @if($method !== 'HEAD')
                                            <span class="badge badge-light-secondary me-1">{{ $method }}</span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="text-gray-600 fs-7">{{ $route['action'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(count($moduleData['routes']) > 10)
                    <div class="alert alert-info mt-4">
                        <strong>{{ count($moduleData['routes']) - 10 }} rotas adicionais</strong> não mostradas por limitação de espaço.
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        <!--end::Endpoints Tab-->

    </div>
</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    mermaid.initialize({
        startOnLoad: true,
        theme: 'default',
        flowchart: {
            useMaxWidth: true,
            htmlLabels: true
        },
        sequence: {
            useMaxWidth: true,
            showSequenceNumbers: true
        }
    });
});
</script>

<style>
.mermaid {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin: 1rem 0;
}

.tab-content {
    min-height: 400px;
}

code {
    background-color: #f1f3f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.875em;
}
</style>
@endsection