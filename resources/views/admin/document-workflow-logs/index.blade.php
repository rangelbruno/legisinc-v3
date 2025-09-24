@extends('components.layouts.app')

@section('title', 'Logs do Fluxo de Documentos')

@section('content')

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="fas fa-file-alt fs-2 me-3"></i>
                    Logs do Fluxo de Documentos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Administração</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Logs do Fluxo</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-sm btn-light-danger" data-bs-toggle="modal" data-bs-target="#modal_limpar_logs">
                    <i class="fas fa-trash-alt fs-2 me-2"></i>
                    Limpar Logs
                </button>
                <a href="{{ route('admin.document-workflow-logs.export-json', request()->query()) }}" class="btn btn-sm btn-light-primary">
                    <i class="fas fa-download fs-2 me-2"></i>
                    Exportar JSON
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Estatísticas Resumidas -->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <!--begin::Col PDF-->
                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #F1416C;">
                        <div class="card-header pt-5">
                            <div class="card-icon">
                                <i class="fas fa-file-pdf fs-2hx text-white"></i>
                            </div>
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $estatisticas['pdf_stats']['pdf_exports_hoje'] ?? 0 }}</span>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">PDFs Exportados Hoje</span>
                            </div>
                        </div>
                        <div class="card-body d-flex align-items-end pt-0">
                            <div class="d-flex align-items-center flex-column mt-3 w-100">
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-bolder fs-6 text-white opacity-75">Sucesso</span>
                                    <span class="fw-bold fs-6 text-white">{{ $estatisticas['pdf_stats']['pdf_exports_sucesso_hoje'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-bolder fs-6 text-white opacity-75">Erro</span>
                                    <span class="fw-bold fs-6 text-white">{{ $estatisticas['pdf_stats']['pdf_exports_erro_hoje'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Col Assinaturas-->
                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #7239EA;">
                        <div class="card-header pt-5">
                            <div class="card-icon">
                                <i class="fas fa-signature fs-2hx text-white"></i>
                            </div>
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $estatisticas['signature_stats']['assinaturas_hoje'] ?? 0 }}</span>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Assinaturas Hoje</span>
                            </div>
                        </div>
                        <div class="card-body d-flex align-items-end pt-0">
                            <div class="d-flex align-items-center flex-column mt-3 w-100">
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-bolder fs-6 text-white opacity-75">Sucesso</span>
                                    <span class="fw-bold fs-6 text-white">{{ $estatisticas['signature_stats']['assinaturas_sucesso'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-bolder fs-6 text-white opacity-75">Erro</span>
                                    <span class="fw-bold fs-6 text-white">{{ $estatisticas['signature_stats']['assinaturas_erro'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Col Protocolos-->
                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #17C653;">
                        <div class="card-header pt-5">
                            <div class="card-icon">
                                <i class="fas fa-clipboard-list fs-2hx text-white"></i>
                            </div>
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $estatisticas['protocol_stats']['protocolos_hoje'] ?? 0 }}</span>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Protocolos Hoje</span>
                            </div>
                        </div>
                        <div class="card-body d-flex align-items-end pt-0">
                            <div class="d-flex align-items-center flex-column mt-3 w-100">
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-bolder fs-6 text-white opacity-75">Total</span>
                                    <span class="fw-bold fs-6 text-white">{{ $estatisticas['protocol_stats']['protocolos_total'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Col Total Logs-->
                <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100" style="background-color: #FFC700;">
                        <div class="card-header pt-5">
                            <div class="card-icon">
                                <i class="fas fa-list fs-2hx text-white"></i>
                            </div>
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $estatisticas['logs_hoje'] ?? 0 }}</span>
                                <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Logs Hoje</span>
                            </div>
                        </div>
                        <div class="card-body d-flex align-items-end pt-0">
                            <div class="d-flex align-items-center flex-column mt-3 w-100">
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-bolder fs-6 text-white opacity-75">Total Geral</span>
                                    <span class="fw-bold fs-6 text-white">{{ $estatisticas['total_logs'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Filtros de Pesquisa</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-light" id="toggle_filtros">
                            <i class="fas fa-filter"></i>
                            {{ count(array_filter($filtros)) > 0 ? 'Ocultar' : 'Mostrar' }} Filtros
                        </button>
                    </div>
                </div>
                <div class="card-body py-3" id="filtros_container" style="{{ count(array_filter($filtros)) > 0 ? '' : 'display: none;' }}">
                    <form method="GET" action="{{ route('admin.document-workflow-logs.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Proposição</label>
                                <select name="proposicao_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Selecione uma proposição">
                                    <option value="">Todas</option>
                                    @foreach($proposicoes as $proposicao)
                                        <option value="{{ $proposicao->id }}" {{ ($filtros['proposicao_id'] ?? '') == $proposicao->id ? 'selected' : '' }}>
                                            #{{ $proposicao->numero ?? 'S/N' }}/{{ $proposicao->ano ?? 'S/A' }} - {{ $proposicao->tipo ?? 'Tipo não informado' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Usuário</label>
                                <select name="user_id" class="form-select form-select-sm" data-control="select2" data-placeholder="Selecione um usuário">
                                    <option value="">Todos</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}" {{ ($filtros['user_id'] ?? '') == $usuario->id ? 'selected' : '' }}>
                                            {{ $usuario->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo de Evento</label>
                                <select name="event_type" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    @foreach($eventTypes as $type)
                                        <option value="{{ $type }}" {{ ($filtros['event_type'] ?? '') == $type ? 'selected' : '' }}>
                                            {{ str_replace('_', ' ', ucfirst($type)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Etapa</label>
                                <select name="stage" class="form-select form-select-sm">
                                    <option value="">Todas</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage }}" {{ ($filtros['stage'] ?? '') == $stage ? 'selected' : '' }}>
                                            {{ ucfirst($stage) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <option value="success" {{ ($filtros['status'] ?? '') == 'success' ? 'selected' : '' }}>Sucesso</option>
                                    <option value="error" {{ ($filtros['status'] ?? '') == 'error' ? 'selected' : '' }}>Erro</option>
                                    <option value="warning" {{ ($filtros['status'] ?? '') == 'warning' ? 'selected' : '' }}>Aviso</option>
                                    <option value="pending" {{ ($filtros['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Data Início</label>
                                <input type="date" name="data_inicio" class="form-control form-control-sm" value="{{ $filtros['data_inicio'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data Fim</label>
                                <input type="date" name="data_fim" class="form-control form-control-sm" value="{{ $filtros['data_fim'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Por Página</label>
                                <select name="per_page" class="form-select form-select-sm">
                                    <option value="25" {{ ($filtros['per_page'] ?? 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($filtros['per_page'] ?? 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($filtros['per_page'] ?? 25) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-search"></i>
                                    Filtrar
                                </button>
                                <a href="{{ route('admin.document-workflow-logs.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                    Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card de Status do S3 -->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">
                            <i class="fab fa-aws text-warning me-2"></i>
                            Status do Armazenamento S3
                        </span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Monitoramento de uploads e operações do S3</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3">
                        @php
                            // Usar as estatísticas S3 do controller
                            $s3StatsData = $estatisticas['s3_stats'] ?? [];
                            $isS3Active = ($s3StatsData['status_configuracao'] ?? 'inativo') === 'ativo';
                        @endphp

                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-3">
                                    <span class="symbol-label {{ $isS3Active ? 'bg-light-success' : 'bg-light-danger' }}">
                                        <i class="fas fa-cloud {{ $isS3Active ? 'text-success' : 'text-danger' }} fs-2"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">Status S3</div>
                                    <div class="text-{{ $isS3Active ? 'success' : 'danger' }} fs-7">
                                        {{ $isS3Active ? 'Ativo' : 'Inativo' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-3">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="fas fa-upload text-primary fs-2"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">Uploads Hoje</div>
                                    <div class="text-primary fs-7">{{ $s3StatsData['uploads_hoje'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-3">
                                    <span class="symbol-label bg-light-info">
                                        <i class="fas fa-hdd text-info fs-2"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">Tamanho Total</div>
                                    <div class="text-info fs-7">
                                        {{ $s3StatsData['tamanho_total_mb'] ?? '0.00' }} MB
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-3">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="fas fa-clock text-warning fs-2"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6">Tempo Médio</div>
                                    <div class="text-warning fs-7">
                                        {{ $s3StatsData['tempo_medio_ms'] ?? 0 }} ms
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isS3Active)
                        <div class="separator separator-dashed my-5"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-light-primary rounded p-3">
                                    <div class="fw-bold text-primary mb-2">Configuração S3</div>
                                    <div class="fs-8">
                                        <div>Bucket: <span class="text-dark fw-bold">{{ config('filesystems.disks.s3.bucket') }}</span></div>
                                        <div>Região: <span class="text-dark fw-bold">{{ config('filesystems.disks.s3.region') }}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light-success rounded p-3">
                                    <div class="fw-bold text-success mb-2">Taxa de Sucesso</div>
                                    <div class="fs-8">
                                        @php
                                            $totalUploads = ($s3StatsData['uploads_sucesso'] ?? 0) + ($s3StatsData['uploads_erro'] ?? 0);
                                            $taxaSucesso = $totalUploads > 0
                                                ? (($s3StatsData['uploads_sucesso'] ?? 0) / $totalUploads) * 100
                                                : 0;
                                        @endphp
                                        <div class="progress h-20px">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                 style="width: {{ $taxaSucesso }}%;"
                                                 aria-valuenow="{{ $taxaSucesso }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ number_format($taxaSucesso, 1) }}%
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            Sucesso: {{ $s3StatsData['uploads_sucesso'] ?? 0 }} |
                                            Erro: {{ $s3StatsData['uploads_erro'] ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progresso do Workflow por Proposição -->
            @if($workflowLogs->count() > 0)
                @php
                    $proposicoesProgresso = [];
                    foreach($workflowLogs as $log) {
                        if (!isset($proposicoesProgresso[$log->proposicao_id])) {
                            $proposicoesProgresso[$log->proposicao_id] = [
                                'proposicao' => $log->proposicao,
                                'etapas' => [],
                                'usuarios' => [],
                                'ultimo_log' => null
                            ];
                        }

                        $proposicoesProgresso[$log->proposicao_id]['etapas'][$log->stage] = $log;
                        $proposicoesProgresso[$log->proposicao_id]['usuarios'][] = $log->user;

                        if (!$proposicoesProgresso[$log->proposicao_id]['ultimo_log'] ||
                            $log->created_at > $proposicoesProgresso[$log->proposicao_id]['ultimo_log']->created_at) {
                            $proposicoesProgresso[$log->proposicao_id]['ultimo_log'] = $log;
                        }
                    }
                @endphp

                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Progresso do Workflow por Documento</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Visualização do andamento de cada proposição</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        @foreach($proposicoesProgresso as $progresso)
                            @php
                                $etapasWorkflow = [
                                    'creation' => ['nome' => 'Criação', 'icon' => 'fas fa-plus-circle', 'color' => 'primary'],
                                    'editing' => ['nome' => 'Edição', 'icon' => 'fas fa-edit', 'color' => 'info'],
                                    'review' => ['nome' => 'Revisão', 'icon' => 'fas fa-search', 'color' => 'warning'],
                                    'approval' => ['nome' => 'Aprovação', 'icon' => 'fas fa-check-circle', 'color' => 'success'],
                                    'export' => ['nome' => 'Exportação PDF', 'icon' => 'fas fa-file-pdf', 'color' => 'danger'],
                                    'signature' => ['nome' => 'Assinatura', 'icon' => 'fas fa-signature', 'color' => 'warning'],
                                    'protocol' => ['nome' => 'Protocolo', 'icon' => 'fas fa-clipboard-list', 'color' => 'success']
                                ];

                                $etapasCompletas = array_keys($progresso['etapas']);
                                $ultimaEtapa = $progresso['ultimo_log'];

                                // Determinar próxima etapa
                                $fluxoWorkflow = ['creation', 'editing', 'review', 'approval', 'export', 'signature', 'protocol'];
                                $proximaEtapa = null;
                                foreach ($fluxoWorkflow as $etapa) {
                                    if (!in_array($etapa, $etapasCompletas)) {
                                        $proximaEtapa = $etapa;
                                        break;
                                    }
                                }
                            @endphp

                            <div class="border border-gray-300 border-dashed rounded p-5 mb-5">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-light-primary text-primary fw-bold">
                                            {{ $progresso['proposicao']->numero ?? 'S/N' }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">
                                            #{{ $progresso['proposicao']->numero ?? 'S/N' }}/{{ $progresso['proposicao']->ano ?? date('Y') }}
                                            - {{ $progresso['proposicao']->tipo ?? 'Tipo não informado' }}
                                        </h5>
                                        <p class="text-muted mb-0">{{ Str::limit($progresso['proposicao']->ementa ?? 'Ementa não informada', 100) }}</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-light-{{ $ultimaEtapa->status === 'success' ? 'success' : ($ultimaEtapa->status === 'error' ? 'danger' : 'warning') }} fs-7">
                                            {{ ucfirst($ultimaEtapa->status) }}
                                        </span>
                                        <div class="text-muted fs-8 mt-1">{{ $ultimaEtapa->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>

                                <!-- Progresso das Etapas -->
                                <div class="stepper stepper-pills stepper-column d-flex flex-stack">
                                    @foreach($etapasWorkflow as $etapaKey => $etapaInfo)
                                        @php
                                            $foiConcluida = isset($progresso['etapas'][$etapaKey]);
                                            $logEtapa = $progresso['etapas'][$etapaKey] ?? null;
                                        @endphp

                                        <div class="stepper-item {{ $foiConcluida ? 'current' : '' }}" style="flex: 1;">
                                            <div class="stepper-wrapper d-flex align-items-center">
                                                <div class="stepper-icon w-40px h-40px">
                                                    <i class="{{ $etapaInfo['icon'] }} {{ $foiConcluida ? 'text-' . $etapaInfo['color'] : 'text-muted' }} fs-2"></i>
                                                </div>
                                                <div class="stepper-content ms-3">
                                                    <h6 class="stepper-title {{ $foiConcluida ? 'text-' . $etapaInfo['color'] : 'text-muted' }} mb-1">
                                                        {{ $etapaInfo['nome'] }}
                                                    </h6>
                                                    @if($foiConcluida && $logEtapa)
                                                        <div class="stepper-desc text-muted fs-8">
                                                            <strong>{{ $logEtapa->user->name ?? 'Sistema' }}</strong>
                                                            @if($logEtapa->user)
                                                                @php $userRoles = $logEtapa->user->getRoleNames()->toArray(); @endphp
                                                                <span class="badge badge-light-secondary fs-9 ms-1">
                                                                    {{ !empty($userRoles) ? implode(', ', $userRoles) : 'Usuário' }}
                                                                </span>
                                                            @endif
                                                            <div class="mt-1">{{ $logEtapa->created_at->format('d/m/Y H:i') }}</div>
                                                            @if($logEtapa->status === 'error')
                                                                <div class="text-danger mt-1">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    {{ Str::limit($logEtapa->error_message, 50) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="stepper-desc text-muted fs-8">Pendente</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if(!$loop->last)
                                            <div class="stepper-line h-40px border-gray-300 border-dashed"></div>
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Erros e Problemas -->
                                @php
                                    $logsComErro = array_filter($progresso['etapas'], function($log) {
                                        return $log->status === 'error';
                                    });
                                @endphp

                                @if(count($logsComErro) > 0)
                                    <div class="alert alert-danger d-flex align-items-start mt-4">
                                        <i class="fas fa-exclamation-triangle fs-2x me-3 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <strong>Problemas identificados:</strong>
                                            @foreach($logsComErro as $logErro)
                                                <div class="mt-2 p-3 bg-light-danger rounded">
                                                    <div class="fw-bold">{{ $etapasWorkflow[$logErro->stage]['nome'] ?? $logErro->stage }}</div>
                                                    <div class="text-muted fs-8">{{ $logErro->created_at->format('d/m/Y H:i:s') }}</div>
                                                    <div class="mt-1">{{ $logErro->error_message }}</div>
                                                    @if($logErro->user)
                                                        <div class="mt-1">
                                                            <span class="badge badge-light-danger fs-9">{{ $logErro->user->name }}</span>
                                                            @php $userRoles = $logErro->user->getRoleNames()->toArray(); @endphp
                                                            @if(!empty($userRoles))
                                                                <span class="badge badge-light-secondary fs-9">{{ implode(', ', $userRoles) }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Próxima Ação Esperada -->
                                @if($proximaEtapa)
                                    <div class="alert alert-primary d-flex align-items-center mt-4">
                                        <i class="fas fa-arrow-right fs-2x me-3"></i>
                                        <div>
                                            <strong>Próxima ação esperada:</strong>
                                            {{ $etapasWorkflow[$proximaEtapa]['nome'] ?? 'Etapa desconhecida' }}
                                            @php
                                                $responsavel = match($proximaEtapa) {
                                                    'editing' => 'PARLAMENTAR ou ASSESSOR_JURIDICO',
                                                    'review' => 'LEGISLATIVO',
                                                    'approval' => 'ADMIN ou LEGISLATIVO',
                                                    'export' => 'Sistema (Automático)',
                                                    'signature' => 'PARLAMENTAR',
                                                    'protocol' => 'PROTOCOLO ou EXPEDIENTE',
                                                    default => 'Definir responsável'
                                                };
                                            @endphp
                                            <div class="text-muted fs-8">Responsável: {{ $responsavel }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-success d-flex align-items-center mt-4">
                                        <i class="fas fa-check-circle fs-2x me-3"></i>
                                        <div>
                                            <strong>Workflow concluído!</strong>
                                            <div class="text-muted fs-8">Todas as etapas foram executadas com sucesso.</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Logs do Workflow (Novos) -->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Logs Detalhados do Workflow</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Rastreamento completo de todas as ações executadas</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    @if($workflowLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-50px">Status</th>
                                        <th class="min-w-120px">Data/Hora</th>
                                        <th class="min-w-100px">Proposição</th>
                                        <th class="min-w-150px">Evento</th>
                                        <th class="min-w-100px">Etapa</th>
                                        <th class="min-w-120px">Usuário</th>
                                        <th class="min-w-200px">Descrição</th>
                                        <th class="min-w-100px">Arquivo</th>
                                        <th class="min-w-80px">Tempo</th>
                                        <th class="min-w-50px text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workflowLogs as $log)
                                        <tr>
                                            <td>
                                                <span class="badge badge-{{ $log->status_badge_class }} fs-7">
                                                    <i class="{{ $log->status_icon }} me-1"></i>
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bold text-hover-primary fs-6">
                                                        {{ $log->created_at->format('d/m/Y') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-muted fw-semibold fs-7 me-2">
                                                            {{ $log->created_at->format('H:i:s') }}
                                                        </span>
                                                        <span class="badge badge-light-secondary fs-8">
                                                            {{ $log->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($log->proposicao)
                                                    <a href="{{ route('admin.document-workflow-logs.show', $log->proposicao->id) }}" class="text-dark fw-bold text-hover-primary d-block mb-1 fs-6">
                                                        #{{ $log->proposicao->numero ?? 'S/N' }}/{{ $log->proposicao->ano ?? 'S/A' }}
                                                    </a>
                                                    <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $log->proposicao->tipo ?? 'N/A' }}</span>
                                                @else
                                                    <span class="text-muted">Proposição não encontrada</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $eventIcon = match($log->event_type) {
                                                        'document_created' => 'fas fa-plus-circle text-success',
                                                        'pdf_exported' => 'fas fa-file-pdf text-danger',
                                                        'document_signed' => 'fas fa-signature text-warning',
                                                        'protocol_assigned' => 'fas fa-clipboard-list text-info',
                                                        'document_edited' => 'fas fa-edit text-primary',
                                                        'document_deleted' => 'fas fa-trash text-danger',
                                                        default => 'fas fa-info-circle text-secondary'
                                                    };
                                                    $eventColor = match($log->event_type) {
                                                        'document_created' => 'success',
                                                        'pdf_exported' => 'danger',
                                                        'document_signed' => 'warning',
                                                        'protocol_assigned' => 'info',
                                                        'document_edited' => 'primary',
                                                        'document_deleted' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <div class="d-flex align-items-center">
                                                    <i class="{{ $eventIcon }} fs-5 me-2"></i>
                                                    <div class="d-flex flex-column">
                                                        <span class="badge badge-light-{{ $eventColor }} fs-7">{{ str_replace('_', ' ', ucfirst($log->event_type)) }}</span>
                                                        <div class="text-muted fw-semibold fs-7 mt-1">{{ ucfirst($log->action) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info fs-7">{{ ucfirst($log->stage) }}</span>
                                            </td>
                                            <td>
                                                @if($log->user)
                                                    @php
                                                        $userRoles = $log->user->getRoleNames()->toArray();
                                                        $userType = !empty($userRoles) ? implode(', ', $userRoles) : 'Usuário';
                                                        $userColor = match(true) {
                                                            in_array('ADMIN', $userRoles) => 'danger',
                                                            in_array('PARLAMENTAR', $userRoles) => 'primary',
                                                            in_array('LEGISLATIVO', $userRoles) => 'success',
                                                            in_array('PROTOCOLO', $userRoles) => 'info',
                                                            in_array('ASSESSOR_JURIDICO', $userRoles) => 'warning',
                                                            in_array('EXPEDIENTE', $userRoles) => 'dark',
                                                            default => 'light'
                                                        };
                                                        $userIcon = match(true) {
                                                            in_array('ADMIN', $userRoles) => 'fas fa-user-shield',
                                                            in_array('PARLAMENTAR', $userRoles) => 'fas fa-user-tie',
                                                            in_array('LEGISLATIVO', $userRoles) => 'fas fa-gavel',
                                                            in_array('PROTOCOLO', $userRoles) => 'fas fa-clipboard-list',
                                                            in_array('ASSESSOR_JURIDICO', $userRoles) => 'fas fa-balance-scale',
                                                            in_array('EXPEDIENTE', $userRoles) => 'fas fa-file-alt',
                                                            default => 'fas fa-user'
                                                        };
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                            <div class="symbol-label bg-light-{{ $userColor }}">
                                                                <i class="{{ $userIcon }} text-{{ $userColor }} fs-4"></i>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-gray-800 fw-bold">{{ $log->user->name }}</span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge badge-light-{{ $userColor }} fs-8 me-2">{{ $userType }}</span>
                                                                @if($log->ip_address)
                                                                    <span class="text-muted fw-semibold fs-8 me-1" title="IP: {{ $log->ip_address }}">
                                                                        <i class="fas fa-globe fs-9 me-1"></i>{{ substr($log->ip_address, 0, 8) }}...
                                                                    </span>
                                                                @endif
                                                                <span class="text-muted fw-semibold fs-8">{{ $log->created_at->format('H:i:s') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                            <div class="symbol-label bg-light-secondary">
                                                                <i class="fas fa-robot text-secondary fs-4"></i>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-gray-800 fw-bold">Sistema</span>
                                                            <span class="badge badge-light-secondary fs-8">Automático</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-dark fw-semibold">{{ $log->description }}</div>
                                                @if($log->metadata && isset($log->metadata['manipulation_context']))
                                                    <div class="text-info fs-7 mt-1">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        {{ $log->metadata['manipulation_context'] }}
                                                    </div>
                                                @endif
                                                @if($log->protocol_number)
                                                    <div class="text-success fs-7 mt-1">
                                                        <i class="fas fa-hashtag me-1"></i>
                                                        Protocolo: {{ $log->protocol_number }}
                                                    </div>
                                                @endif
                                                @if($log->signature_type)
                                                    <div class="text-warning fs-7 mt-1">
                                                        <i class="fas fa-certificate me-1"></i>
                                                        Assinatura: {{ ucfirst($log->signature_type) }}
                                                    </div>
                                                @endif
                                                @if($log->error_message)
                                                    <div class="text-danger fs-7 mt-1">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        {{ Str::limit($log->error_message, 100) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->file_path)
                                                    <div class="d-flex flex-column">
                                                        <span class="text-dark fw-semibold fs-7">
                                                            <i class="fas fa-file-{{ $log->file_type === 'pdf' ? 'pdf text-danger' : ($log->file_type === 'rtf' ? 'text text-primary' : 'alt text-secondary') }} me-1"></i>
                                                            {{ basename($log->file_path) }}
                                                        </span>
                                                        @if($log->file_size)
                                                            <span class="text-muted fs-8">{{ $log->formatted_file_size }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->execution_time_ms)
                                                    <span class="badge badge-light-secondary fs-7">{{ $log->formatted_execution_time }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-bs-toggle="modal" data-bs-target="#modal_detalhes_{{ $log->id }}">
                                                    <i class="fas fa-eye fs-5"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal de Detalhes -->
                                        <div class="modal fade" id="modal_detalhes_{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detalhes do Log #{{ $log->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>Evento:</strong> {{ str_replace('_', ' ', ucfirst($log->event_type)) }}<br>
                                                                <strong>Etapa:</strong> {{ ucfirst($log->stage) }}<br>
                                                                <strong>Ação:</strong> {{ ucfirst($log->action) }}<br>
                                                                <strong>Status:</strong>
                                                                <span class="badge badge-{{ $log->status_badge_class }}">{{ ucfirst($log->status) }}</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Data/Hora:</strong> {{ $log->created_at->format('d/m/Y H:i:s') }}<br>
                                                                <strong>Usuário:</strong> {{ $log->user->name ?? 'Sistema' }}<br>
                                                                <strong>IP:</strong> {{ $log->ip_address ?? 'N/A' }}<br>
                                                                @if($log->execution_time_ms)
                                                                    <strong>Tempo de Execução:</strong> {{ $log->formatted_execution_time }}<br>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if($log->file_path)
                                                            <hr>
                                                            <h6>Informações do Arquivo</h6>
                                                            <strong>Caminho:</strong> {{ $log->file_path }}<br>
                                                            <strong>Tipo:</strong> {{ strtoupper($log->file_type) }}<br>
                                                            @if($log->file_size)
                                                                <strong>Tamanho:</strong> {{ $log->formatted_file_size }}<br>
                                                            @endif
                                                            @if($log->file_hash)
                                                                <strong>Hash:</strong> <code>{{ $log->file_hash }}</code><br>
                                                            @endif
                                                        @endif

                                                        @if($log->protocol_number)
                                                            <hr>
                                                            <h6>Informações do Protocolo</h6>
                                                            <strong>Número:</strong> {{ $log->protocol_number }}<br>
                                                            <strong>Data:</strong> {{ $log->protocol_date->format('d/m/Y H:i:s') }}<br>
                                                        @endif

                                                        @if($log->signature_type)
                                                            <hr>
                                                            <h6>Informações da Assinatura</h6>
                                                            <strong>Tipo:</strong> {{ ucfirst($log->signature_type) }}<br>
                                                            @if($log->certificate_info)
                                                                <strong>Certificado:</strong> {{ $log->certificate_info }}<br>
                                                            @endif
                                                            @if($log->signature_date)
                                                                <strong>Data da Assinatura:</strong> {{ $log->signature_date->format('d/m/Y H:i:s') }}<br>
                                                            @endif
                                                        @endif

                                                        <hr>
                                                        <h6>Descrição</h6>
                                                        <p>{{ $log->description }}</p>

                                                        @if($log->metadata)
                                                            <hr>
                                                            <h6>Metadados</h6>
                                                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        @endif

                                                        @if($log->error_message)
                                                            <hr>
                                                            <h6 class="text-danger">Erro</h6>
                                                            <div class="alert alert-danger">{{ $log->error_message }}</div>
                                                            @if($log->stack_trace)
                                                                <details>
                                                                    <summary>Stack Trace</summary>
                                                                    <pre class="bg-dark text-light p-3 rounded mt-2"><code>{{ $log->stack_trace }}</code></pre>
                                                                </details>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($workflowLogs, 'links'))
                            <div class="d-flex justify-content-center mt-5">
                                {{ $workflowLogs->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="d-flex flex-column flex-center">
                            <div class="text-center">
                                <i class="fas fa-inbox fs-2x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum log encontrado</h5>
                                <p class="text-muted">Não há logs de workflow com os filtros selecionados.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!-- Modal de Limpar Logs -->
<div class="modal fade" id="modal_limpar_logs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt text-danger me-2"></i>
                    Limpar Logs do Sistema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fs-2x me-3"></i>
                    <div>
                        <strong>Atenção!</strong> Esta ação removerá permanentemente os logs selecionados do sistema.
                        Esta operação não pode ser desfeita.
                    </div>
                </div>

                <form id="form_limpar_logs" method="POST" action="{{ route('admin.document-workflow-logs.delete') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="confirmar" value="1">

                    <div class="mb-4">
                        <label class="form-label required">Período dos logs a serem removidos:</label>
                        <select name="periodo" class="form-select" id="periodo_limpeza" required>
                            <option value="">Selecione o período</option>
                            <option value="hoje">Apenas logs de hoje</option>
                            <option value="semana">Logs da última semana</option>
                            <option value="mes">Logs do último mês</option>
                            <option value="3_meses">Logs dos últimos 3 meses</option>
                            <option value="6_meses">Logs dos últimos 6 meses</option>
                            <option value="1_ano">Logs do último ano</option>
                            <option value="todos" class="text-danger">TODOS os logs (CUIDADO!)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </button>
                <button type="submit" form="form_limpar_logs" class="btn btn-danger" id="btn_confirmar_limpeza">
                    <i class="fas fa-trash-alt me-1"></i>
                    Limpar Logs
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle filtros
    const toggleFiltros = document.getElementById('toggle_filtros');
    const filtrosContainer = document.getElementById('filtros_container');

    if (toggleFiltros) {
        toggleFiltros.addEventListener('click', function() {
            if (filtrosContainer.style.display === 'none') {
                filtrosContainer.style.display = 'block';
                this.innerHTML = '<i class="fas fa-filter"></i> Ocultar Filtros';
            } else {
                filtrosContainer.style.display = 'none';
                this.innerHTML = '<i class="fas fa-filter"></i> Mostrar Filtros';
            }
        });
    }

    // Modal de limpar logs
    const periodoSelect = document.getElementById('periodo_limpeza');
    const btnConfirmar = document.getElementById('btn_confirmar_limpeza');

    if (periodoSelect && btnConfirmar) {
        // Habilitar/desabilitar botão confirmar
        function checkFormValidity() {
            const periodoSelected = periodoSelect.value !== '';
            if (periodoSelected) {
                btnConfirmar.disabled = false;
                btnConfirmar.classList.remove('btn-secondary');
                btnConfirmar.classList.add('btn-danger');
            } else {
                btnConfirmar.disabled = true;
                btnConfirmar.classList.remove('btn-danger');
                btnConfirmar.classList.add('btn-secondary');
            }
        }

        // Event listener para mudança de período
        periodoSelect.addEventListener('change', function() {
            checkFormValidity();

            // Confirmação adicional para opção "todos"
            if (this.value === 'todos') {
                const confirma = confirm('ATENÇÃO: Você está prestes a remover TODOS os logs do sistema!\n\nEsta ação é irreversível e pode afetar auditoria e rastreabilidade.\n\nTem certeza absoluta?');
                if (!confirma) {
                    this.value = '';
                    checkFormValidity();
                }
            }
        });

        // Reset modal ao fechar
        const modalElement = document.getElementById('modal_limpar_logs');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function() {
                periodoSelect.value = '';
                checkFormValidity();
            });
        }

        // Inicializar estado quando modal abre
        const modalElement2 = document.getElementById('modal_limpar_logs');
        if (modalElement2) {
            modalElement2.addEventListener('shown.bs.modal', function() {
                checkFormValidity();
            });
        }

        // Inicializar estado imediatamente
        checkFormValidity();
    }
});
</script>
@endsection