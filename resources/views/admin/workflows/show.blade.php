@extends('components.layouts.app')

@section('title', 'Workflow: ' . $workflow->nome)

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ $workflow->nome }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.workflows.index') }}" class="text-muted text-hover-primary">Workflows</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $workflow->nome }}</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.workflows.designer.edit', $workflow) }}" class="btn btn-sm fw-bold btn-success">
                        <i class="ki-duotone ki-design-1 fs-2"></i>
                        Designer
                    </a>
                    <a href="{{ route('admin.workflows.edit', $workflow) }}" class="btn btn-sm fw-bold btn-primary">
                        <i class="ki-duotone ki-pencil fs-2"></i>
                        Editar
                    </a>
                @endif
                <a href="{{ route('admin.workflows.index') }}" class="btn btn-sm fw-bold btn-light">
                    <i class="ki-duotone ki-black-left fs-2"></i>
                    Voltar
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
        
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-gray-900">Sucesso!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!--begin::Layout-->
            <div class="d-flex flex-column flex-lg-row">
                <!--begin::Sidebar-->
                <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                    <!--begin::Card-->
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Card body-->
                        <div class="card-body">
                            <!--begin::Summary-->
                            <div class="d-flex flex-center flex-column py-5">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-100px symbol-circle mb-7">
                                    <div class="symbol-label fs-3 bg-light-primary text-primary">
                                        <i class="ki-duotone ki-route fs-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Name-->
                                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $workflow->nome }}</a>
                                <!--end::Name-->
                                <!--begin::Position-->
                                <div class="mb-9">
                                    <div class="badge badge-lg badge-light-{{ $workflow->ativo ? 'success' : 'secondary' }} d-inline">
                                        <i class="ki-duotone ki-abstract-25 fs-4 ms-n1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ $workflow->ativo ? 'Ativo' : 'Inativo' }}
                                    </div>
                                    @if($workflow->is_default)
                                        <div class="badge badge-lg badge-light-warning d-inline ms-2">
                                            <i class="ki-duotone ki-star fs-4 ms-n1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Padrão
                                        </div>
                                    @endif
                                </div>
                                <!--end::Position-->
                            </div>
                            <!--end::Summary-->
                            
                            <!--begin::Details toggle-->
                            <div class="d-flex flex-stack fs-4 py-3">
                                <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">
                                    Detalhes
                                    <span class="ms-2 rotate-180">
                                        <i class="ki-duotone ki-down fs-3"></i>
                                    </span>
                                </div>
                            </div>
                            <!--end::Details toggle-->
                            
                            <div class="separator"></div>
                            
                            <!--begin::Details content-->
                            <div id="kt_user_view_details" class="collapse show">
                                <div class="pb-5 fs-6">
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Tipo de Documento</div>
                                    <div class="text-gray-600">
                                        <span class="badge badge-light-info">
                                            {{ ucfirst(str_replace('_', ' ', $workflow->tipo_documento)) }}
                                        </span>
                                    </div>
                                    <!--begin::Details item-->
                                    
                                    @if($workflow->descricao)
                                        <!--begin::Details item-->
                                        <div class="fw-bold mt-5">Descrição</div>
                                        <div class="text-gray-600">{{ $workflow->descricao }}</div>
                                        <!--begin::Details item-->
                                    @endif

                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Ordem de Prioridade</div>
                                    <div class="text-gray-600">{{ $workflow->ordem ?? 'Não definida' }}</div>
                                    <!--begin::Details item-->
                                    
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Criado em</div>
                                    <div class="text-gray-600">
                                        {{ $workflow->created_at->format('d/m/Y H:i') }}
                                        <div class="text-muted fs-7">({{ $workflow->created_at->diffForHumans() }})</div>
                                    </div>
                                    <!--begin::Details item-->
                                    
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Última atualização</div>
                                    <div class="text-gray-600">
                                        {{ $workflow->updated_at->format('d/m/Y H:i') }}
                                        <div class="text-muted fs-7">({{ $workflow->updated_at->diffForHumans() }})</div>
                                    </div>
                                    <!--begin::Details item-->
                                </div>
                            </div>
                            <!--end::Details content-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                    
                    <!--begin::Statistics-->
                    <div class="card mb-5 mb-xl-8">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">Estatísticas</h3>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-2">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-element-11 fs-1 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">{{ $workflow->etapas->count() }}</a>
                                    <div class="fs-7 text-muted fw-bold">Etapas</div>
                                </div>
                            </div>
                            <!--end::Item-->
                            
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-arrows-loop fs-1 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">{{ $workflow->transicoes->count() }}</a>
                                    <div class="fs-7 text-muted fw-bold">Transições</div>
                                </div>
                            </div>
                            <!--end::Item-->
                            
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-document fs-1 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">{{ $stats['documentos_em_uso'] }}</a>
                                    <div class="fs-7 text-muted fw-bold">Documentos em Uso</div>
                                </div>
                            </div>
                            <!--end::Item-->
                            
                            <!--begin::Item-->
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-check-circle fs-1 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">{{ $stats['documentos_finalizados'] }}</a>
                                    <div class="fs-7 text-muted fw-bold">Documentos Finalizados</div>
                                </div>
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Statistics-->
                </div>
                <!--end::Sidebar-->

                <!--begin::Content-->
                <div class="flex-lg-row-fluid ms-lg-15">
                    <!--begin::Tab content-->
                    <div class="tab-content" id="myTabContent">
                        <!--begin::Tab pane Workflow-->
                        <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card card-flush mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header mt-6">
                                    <!--begin::Card title-->
                                    <div class="card-title flex-column">
                                        <h2 class="mb-1">Fluxo do Workflow</h2>
                                        <div class="fs-6 fw-semibold text-muted">Visualize as etapas e transições deste workflow</div>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.workflows.designer.edit', $workflow) }}" class="btn btn-light-primary btn-sm">
                                                <i class="ki-duotone ki-design-1 fs-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                Designer Visual
                                            </a>
                                        @endif
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    @if($workflow->etapas->count() > 0)
                                        <!--begin::Timeline-->
                                        <div class="timeline-label">
                                            @foreach($workflow->etapas->sortBy('ordem') as $etapa)
                                                <!--begin::Item-->
                                                <div class="timeline-item">
                                                    <!--begin::Label-->
                                                    <div class="timeline-label fw-bold text-gray-800 fs-6">{{ $loop->iteration }}</div>
                                                    <!--end::Label-->
                                                    <!--begin::Badge-->
                                                    <div class="timeline-badge">
                                                        <i class="ki-duotone ki-abstract-8 text-gray-600 fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                    <!--end::Badge-->
                                                    <!--begin::Text-->
                                                    <div class="fw-mormal timeline-content text-muted ps-3">
                                                        <div class="d-flex flex-stack">
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-2 text-gray-800">{{ $etapa->nome }}</h5>
                                                                @if($etapa->descricao)
                                                                    <div class="text-muted mb-3">{{ $etapa->descricao }}</div>
                                                                @endif
                                                                
                                                                <!--begin::Badges-->
                                                                <div class="d-flex gap-2 flex-wrap mb-3">
                                                                    <span class="badge badge-light-secondary">{{ $etapa->key }}</span>
                                                                    @if($etapa->tipo)
                                                                        <span class="badge badge-light-info">{{ ucfirst($etapa->tipo) }}</span>
                                                                    @endif
                                                                    @if($etapa->acao)
                                                                        <span class="badge badge-light-success">{{ ucfirst($etapa->acao) }}</span>
                                                                    @endif
                                                                    @if($etapa->is_final)
                                                                        <span class="badge badge-light-warning">Final</span>
                                                                    @endif
                                                                </div>
                                                                <!--end::Badges-->

                                                                @if($etapa->roles_permitidos && count($etapa->roles_permitidos) > 0)
                                                                    <div class="mb-2">
                                                                        <span class="text-muted fs-7 fw-semibold">Roles permitidos:</span>
                                                                        @foreach($etapa->roles_permitidos as $role)
                                                                            <span class="badge badge-light ms-1">{{ $role }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="badge badge-light-primary">Ordem: {{ $etapa->ordem }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Text-->
                                                </div>
                                                <!--end::Item-->
                                            @endforeach
                                        </div>
                                        <!--end::Timeline-->
                                    @else
                                        <!--begin::Empty state-->
                                        <div class="text-center py-15">
                                            <div class="symbol symbol-100px mx-auto mb-7">
                                                <div class="symbol-label bg-light-primary">
                                                    <i class="ki-duotone ki-element-11 fs-1 text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <h3 class="text-gray-800 mb-3">Nenhuma etapa configurada</h3>
                                            <div class="text-gray-600 mb-5">Este workflow ainda não possui etapas definidas.</div>
                                            @if(auth()->user()->isAdmin())
                                                <a href="{{ route('admin.workflows.designer.edit', $workflow) }}" class="btn btn-primary">
                                                    <i class="ki-duotone ki-plus fs-3"></i>
                                                    Configurar Etapas
                                                </a>
                                            @endif
                                        </div>
                                        <!--end::Empty state-->
                                    @endif
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->

                            <!--begin::Card Transições-->
                            @if($workflow->transicoes->count() > 0)
                                <div class="card card-flush mb-6 mb-xl-9">
                                    <!--begin::Card header-->
                                    <div class="card-header mt-6">
                                        <div class="card-title">
                                            <h2>Transições</h2>
                                        </div>
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <!--begin::Table-->
                                        <div class="table-responsive">
                                            <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                                                <thead>
                                                    <tr class="border-0">
                                                        <th class="p-0 w-50px"></th>
                                                        <th class="p-0 min-w-150px">De</th>
                                                        <th class="p-0 min-w-150px">Para</th>
                                                        <th class="p-0 min-w-100px">Ação</th>
                                                        <th class="p-0 min-w-100px">Condições</th>
                                                        <th class="p-0 min-w-100px text-center">Automática</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($workflow->transicoes->sortBy('ordem') as $transicao)
                                                        <tr>
                                                            <td>
                                                                <div class="symbol symbol-45px">
                                                                    <span class="symbol-label bg-light-success text-success fw-bold">
                                                                        {{ $loop->iteration }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-light-secondary fs-7 fw-bold">
                                                                    {{ $transicao->etapaOrigem->nome ?? 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-light-primary fs-7 fw-bold">
                                                                    {{ $transicao->etapaDestino->nome ?? 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($transicao->acao)
                                                                    <span class="badge badge-light-success fs-7 fw-bold">{{ ucfirst($transicao->acao) }}</span>
                                                                @else
                                                                    <span class="text-muted fs-8">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($transicao->condicoes && count($transicao->condicoes) > 0)
                                                                    <i class="ki-duotone ki-check-circle fs-2 text-success">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                @else
                                                                    <span class="text-muted fs-8">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($transicao->automatica)
                                                                    <i class="ki-duotone ki-check fs-2 text-success">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                @else
                                                                    <i class="ki-duotone ki-cross fs-2 text-muted">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                            @endif

                            <!--begin::Card Configuração-->
                            @if($workflow->configuracao && count($workflow->configuracao) > 0)
                                <div class="card card-flush">
                                    <!--begin::Card header-->
                                    <div class="card-header mt-6">
                                        <div class="card-title">
                                            <h2>Configuração</h2>
                                        </div>
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <div class="bg-light-secondary p-6 rounded">
                                            <pre class="text-gray-700 fw-semibold fs-6 mb-0"><code>{{ json_encode($workflow->configuracao, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                        </div>
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                            @endif
                        </div>
                        <!--end::Tab pane-->
                    </div>
                    <!--end::Tab content-->
                </div>
                <!--end::Content-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

@endsection