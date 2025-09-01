@extends('layouts.app')

@section('title', 'Templates de Documentos')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-document fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Templates de Documentos
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Administração</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Templates</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Stats-->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10" style="background-color: #F1416C;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ $tipos->count() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Tipos de Proposição</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Total</span>
                            <span class="fw-bold fs-6 text-white">{{ $tipos->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10" style="background-color: #7239EA;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ $tipos->filter(fn($t) => $t->hasTemplate())->count() }}</span>
                            @if($tipos->filter(fn($t) => $t->hasTemplate())->count() === $tipos->count())
                                <i class="ki-duotone ki-check-circle fs-3 text-white ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            @endif
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Com Template</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Ativos</span>
                            <span class="fw-bold fs-6 text-white">{{ $tipos->filter(fn($t) => $t->hasTemplate())->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10" style="background-color: #F1BC00;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ $tipos->filter(fn($t) => !$t->hasTemplate())->count() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Sem Template</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Pendentes</span>
                            <span class="fw-bold fs-6 text-white">{{ $tipos->filter(fn($t) => !$t->hasTemplate())->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10" style="background-color: #17C653;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ round($tipos->count() > 0 ? ($tipos->filter(fn($t) => $t->hasTemplate())->count() / $tipos->count()) * 100 : 0) }}%</span>
                            @if($tipos->count() > 0 && ($tipos->filter(fn($t) => $t->hasTemplate())->count() / $tipos->count()) * 100 == 100)
                                <i class="ki-duotone ki-crown fs-3 text-white ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            @endif
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Cobertura</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Templates</span>
                            <span class="fw-bold fs-6 text-white">{{ round($tipos->count() > 0 ? ($tipos->filter(fn($t) => $t->hasTemplate())->count() / $tipos->count()) * 100 : 0) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Stats-->

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" data-kt-templates-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar tipos..." />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-templates-table-toolbar="base">
                    <a href="/admin/parametros/1" class="btn btn-light-success me-3">
                        <i class="ki-duotone ki-setting-2 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Parâmetros
                    </a>
                    <button type="button" class="btn btn-light-warning me-3" onclick="regenerarTodosTemplates()">
                        <i class="ki-duotone ki-arrows-circle fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Regenerar Todos
                    </button>
                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_help_templates">
                        <i class="ki-duotone ki-information-5 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Como Usar
                    </button>
                    <a href="{{ route('templates.create') }}" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Novo Template
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-bordered align-middle gs-7 gy-4">
                    <thead class="border-bottom border-gray-200 fs-7 text-uppercase">
                        <tr class="fw-bold text-gray-600">
                            <th class="min-w-200px">Tipo de Proposição</th>
                            <th class="min-w-100px text-center">Status</th>
                            <th class="min-w-150px">Última Atualização</th>
                            <th class="min-w-150px text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tipos as $tipo)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-document fs-3 text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>
                                            <div class="fw-bold">{{ $tipo->nome }}</div>
                                            <div class="text-muted fs-7">
                                                Código: {{ $tipo->codigo ?? 'N/A' }}
                                                @if($tipo->template?->variaveis)
                                                    • {{ count($tipo->template->variaveis) }} variáveis
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($tipo->hasTemplate())
                                        <span class="badge badge-light-success fw-bold fs-7 px-3 py-2">
                                            <i class="ki-duotone ki-check fs-8 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Ativo
                                        </span>
                                    @else
                                        <span class="badge badge-light-warning fw-bold fs-7 px-3 py-2">
                                            <i class="ki-duotone ki-information fs-8 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Sem Template
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($tipo->template)
                                        <div class="fw-bold text-gray-800 fs-6">{{ $tipo->template->updated_at->format('d/m/Y H:i') }}</div>
                                        <div class="text-muted fs-7 d-flex align-items-center">
                                            <i class="ki-duotone ki-profile-user fs-8 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            {{ $tipo->template->updatedBy->name ?? 'Sistema' }}
                                        </div>
                                    @else
                                        <span class="text-muted fs-6">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end flex-shrink-0">
                                        <!-- Ação Principal -->
                                        <a href="{{ route('templates.editor', $tipo) }}" 
                                           class="btn btn-sm btn-primary me-2">
                                            @if($tipo->hasTemplate())
                                                <i class="ki-duotone ki-pencil fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Editar
                                            @else
                                                <i class="ki-duotone ki-plus fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Criar
                                            @endif
                                        </a>
                                        
                                        @if($tipo->hasTemplate())
                                            <!-- Preview (Ação Secundária Importante) -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-light-info me-2"
                                                    onclick="previewTemplate({{ $tipo->id }}, {{ json_encode($tipo->nome, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }})"
                                                    title="Visualizar preview do template">
                                                <i class="ki-duotone ki-eye fs-6">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </button>
                                        @endif
                                        
                                        <!-- Menu de Ações -->
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ki-duotone ki-dots-horizontal fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <!-- Geração com Padrões Legais -->
                                                <li>
                                                    <button class="dropdown-item" type="button" 
                                                            onclick="gerarComPadroesLegais({{ $tipo->id }}, {{ json_encode($tipo->nome, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }})">
                                                        <i class="ki-duotone ki-law fs-6 me-2 text-success">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Gerar com LC 95/1998
                                                    </button>
                                                </li>
                                                
                                                @if($tipo->hasTemplate())
                                                    <li><hr class="dropdown-divider"></li>
                                                    
                                                    <!-- Download -->
                                                    <li>
                                                        <a class="dropdown-item" 
                                                           href="{{ route('api.templates.download', $tipo->template) }}?v={{ $tipo->template->updated_at->timestamp }}">
                                                            <i class="ki-duotone ki-exit-down fs-6 me-2 text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            Download Template
                                                        </a>
                                                    </li>
                                                    
                                                    <!-- Variáveis -->
                                                    <li>
                                                        <button class="dropdown-item" type="button"
                                                                onclick="visualizarVariaveis({{ $tipo->id }}, {{ json_encode($tipo->nome, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }})">
                                                            <i class="ki-duotone ki-code fs-6 me-2 text-info">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                            </i>
                                                            Ver Variáveis
                                                        </button>
                                                    </li>
                                                    
                                                    <!-- Validação -->
                                                    <li>
                                                        <button class="dropdown-item" type="button"
                                                                onclick="validarTemplate({{ $tipo->id }}, {{ json_encode($tipo->nome, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }})">
                                                            <i class="ki-duotone ki-shield-tick fs-6 me-2 text-success">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            Validar Conformidade
                                                        </button>
                                                    </li>
                                                    
                                                    <li><hr class="dropdown-divider"></li>
                                                    
                                                    <!-- Excluir -->
                                                    <li>
                                                        <button class="dropdown-item text-danger" type="button"
                                                                onclick="confirmarExclusaoTemplate({{ $tipo->template->id }}, {{ json_encode($tipo->nome, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }})">
                                                            <i class="ki-duotone ki-trash fs-6 me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                            </i>
                                                            Excluir Template
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!--begin::Modal - Como Usar-->
<div class="modal fade" id="kt_modal_help_templates" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Como Usar Templates de Documentos</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-rocket fs-2 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Passo a Passo
                            </h3>
                            <div class="timeline timeline-border-dashed">
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">1</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Criar Template</div>
                                            <div class="text-gray-500">Clique em "Novo Template" ou "Criar Template" para um tipo específico</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">2</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Editar no ONLYOFFICE</div>
                                            <div class="text-gray-500">Use o editor integrado para criar seu documento template</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">3</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Usar Variáveis</div>
                                            <div class="text-gray-500">Adicione variáveis como <code>${numero_proposicao}</code>, <code>${ementa}</code>, etc.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">4</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Salvar Automaticamente</div>
                                            <div class="text-gray-500">O template é salvo automaticamente a cada alteração</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-code fs-2 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                Variáveis Disponíveis
                            </h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3"><code>${numero_proposicao}</code> - Número da proposição</div>
                                    <div class="mb-3"><code>${ementa}</code> - Ementa da proposição</div>
                                    <div class="mb-3"><code>${texto}</code> - Texto principal</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3"><code>${autor_nome}</code> - Nome do autor</div>
                                    <div class="mb-3"><code>${data_atual}</code> - Data atual</div>
                                    <div class="mb-3"><code>${municipio}</code> - Nome do município</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Como Usar-->

<!--begin::Modal - Visualizar Variáveis-->
<div class="modal fade" id="kt_modal_variables" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="modal_variables_title">Variáveis Disponíveis</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-code fs-2 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                Variáveis da Proposição
                            </h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4 p-3 bg-light-primary rounded">
                                        <code class="fs-6 fw-bold text-primary">${numero_proposicao}</code>
                                        <div class="text-muted fs-7 mt-1">Número da proposição</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-primary rounded">
                                        <code class="fs-6 fw-bold text-primary">${tipo_proposicao}</code>
                                        <div class="text-muted fs-7 mt-1">Tipo da proposição</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-primary rounded">
                                        <code class="fs-6 fw-bold text-primary">${ementa}</code>
                                        <div class="text-muted fs-7 mt-1">Ementa da proposição</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-primary rounded">
                                        <code class="fs-6 fw-bold text-primary">${texto}</code>
                                        <div class="text-muted fs-7 mt-1">Texto principal do conteúdo</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-primary rounded">
                                        <code class="fs-6 fw-bold text-primary">${justificativa}</code>
                                        <div class="text-muted fs-7 mt-1">Justificativa</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4 p-3 bg-light-success rounded">
                                        <code class="fs-6 fw-bold text-success">${autor_nome}</code>
                                        <div class="text-muted fs-7 mt-1">Nome do autor</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-success rounded">
                                        <code class="fs-6 fw-bold text-success">${autor_cargo}</code>
                                        <div class="text-muted fs-7 mt-1">Cargo do autor</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-success rounded">
                                        <code class="fs-6 fw-bold text-success">${autor_partido}</code>
                                        <div class="text-muted fs-7 mt-1">Partido do autor</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-info rounded">
                                        <code class="fs-6 fw-bold text-info">${data_atual}</code>
                                        <div class="text-muted fs-7 mt-1">Data atual</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-info rounded">
                                        <code class="fs-6 fw-bold text-info">${ano}</code>
                                        <div class="text-muted fs-7 mt-1">Ano atual</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-home fs-2 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Variáveis da Câmara
                            </h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4 p-3 bg-light-warning rounded">
                                        <code class="fs-6 fw-bold text-warning">${nome_camara}</code>
                                        <div class="text-muted fs-7 mt-1">Nome da câmara</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-warning rounded">
                                        <code class="fs-6 fw-bold text-warning">${municipio}</code>
                                        <div class="text-muted fs-7 mt-1">Município</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-warning rounded">
                                        <code class="fs-6 fw-bold text-warning">${endereco_camara}</code>
                                        <div class="text-muted fs-7 mt-1">Endereço da câmara</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4 p-3 bg-light-warning rounded">
                                        <code class="fs-6 fw-bold text-warning">${telefone_camara}</code>
                                        <div class="text-muted fs-7 mt-1">Telefone da câmara</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-warning rounded">
                                        <code class="fs-6 fw-bold text-warning">${website_camara}</code>
                                        <div class="text-muted fs-7 mt-1">Website da câmara</div>
                                    </div>
                                    <div class="mb-4 p-3 bg-light-secondary rounded">
                                        <code class="fs-6 fw-bold text-secondary">${imagem_cabecalho}</code>
                                        <div class="text-muted fs-7 mt-1">Logo/brasão para cabeçalho</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-setting-2 fs-2 text-dark me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Variáveis de Formatação
                            </h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4 p-3 bg-light-dark rounded">
                                        <code class="fs-6 fw-bold text-dark">${assinatura_padrao}</code>
                                        <div class="text-muted fs-7 mt-1">Área de assinatura padrão</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4 p-3 bg-light-dark rounded">
                                        <code class="fs-6 fw-bold text-dark">${rodape}</code>
                                        <div class="text-muted fs-7 mt-1">Texto do rodapé</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="copiarTodasVariaveis()">
                    <i class="ki-duotone ki-copy fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Copiar Lista de Variáveis
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Visualizar Variáveis-->

<!--begin::Modal - Preview Template-->
<div class="modal fade" id="kt_modal_preview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="modal_preview_title">Preview do Template</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="row">
                    <div class="col-12">
                        <div id="preview_loading" class="text-center py-10">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Carregando...</span>
                            </div>
                            <div class="mt-3">Gerando preview do template...</div>
                        </div>
                        
                        <div id="preview_content" style="display: none;">
                            <div class="mb-5">
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                                    <i class="ki-duotone ki-information-5 fs-2 text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">Este é um preview com dados de exemplo</div>
                                            <div class="fs-7 text-muted">As variáveis foram substituídas por valores fictícios para demonstração</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card card-bordered">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="ki-duotone ki-document fs-3 text-primary me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Documento Gerado
                                    </h3>
                                    <div class="card-toolbar">
                                        <span class="badge badge-light-primary" id="preview_file_type"></span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <pre id="preview_text" class="text-gray-800 fs-6" style="white-space: pre-wrap; font-family: 'Times New Roman', serif; line-height: 1.6;"></pre>
                                </div>
                            </div>
                            
                            <div class="separator my-8"></div>
                            
                            <div class="card card-bordered">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="ki-duotone ki-information-5 fs-3 text-success me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Dados de Exemplo Utilizados
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div id="preview_dados_exemplo"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="preview_error" style="display: none;">
                            <div class="alert alert-danger">
                                <div class="alert-text" id="preview_error_message"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btn_editar_template" onclick="editarTemplateFromPreview()">
                    <i class="ki-duotone ki-pencil fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Editar Template
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Preview Template-->

@endsection

@push('styles')
<style>
    /* Melhor espaçamento da tabela */
    .table td {
        padding: 1rem 0.75rem !important;
        vertical-align: middle !important;
    }
    
    .table th {
        padding: 1rem 0.75rem !important;
        font-weight: 600 !important;
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #e9ecef !important;
    }
    
    /* Hover effect nas linhas */
    .table tbody tr:hover {
        background-color: #f8f9fa !important;
        transition: background-color 0.15s ease;
    }
    
    /* Melhor aparência dos badges */
    .badge {
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        padding: 0.5rem 0.75rem !important;
    }
    
    /* Botões mais compactos e alinhados */
    .btn-sm {
        padding: 0.4rem 0.8rem !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
    }
    
    /* Dropdown menu melhorado */
    .dropdown-menu {
        border: 1px solid #e9ecef !important;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .dropdown-item {
        padding: 0.6rem 1rem !important;
        font-size: 0.85rem !important;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table td, .table th {
            padding: 0.75rem 0.5rem !important;
        }
        
        .btn-sm {
            padding: 0.3rem 0.6rem !important;
            font-size: 0.7rem !important;
        }
        
        .min-w-200px {
            min-width: 150px !important;
        }
        
        .min-w-150px {
            min-width: 100px !important;
        }
    }
    
    /* Ícones com melhor alinhamento */
    .ki-duotone {
        display: inline-flex !important;
        align-items: center !important;
    }
</style>
@endpush

@push('scripts')
<script>
// Filtro de busca na tabela
document.querySelector('[data-kt-templates-table-filter="search"]').addEventListener('input', function(e) {
    const searchValue = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        const tipoNome = row.querySelector('td:first-child .fw-bold').textContent.toLowerCase();
        if (tipoNome.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Função para confirmar exclusão de template
function confirmarExclusaoTemplate(templateId, tipoNome) {
    Swal.fire({
        title: 'Confirmar Exclusão',
        html: 'Tem certeza que deseja excluir o template do tipo:<br><strong>' + tipoNome + '</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, Excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f1416c',
        cancelButtonColor: '#7e8299',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-light'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Excluindo...',
                text: 'Aguarde enquanto o template é removido.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Criar formulário para enviar DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/templates/${templateId}`;
            form.style.display = 'none';
            
            // Token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Method DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Adicionar ao DOM e submeter
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Cache-busting nos links de download já resolve o problema
// O link sempre terá o timestamp correto do banco de dados

function regenerarTodosTemplates() {
    Swal.fire({
        title: 'Regenerar Todos os Templates?',
        html: `
            <p>Esta ação irá:</p>
            <ul class="text-start">
                <li>Regenerar templates para todos os 23 tipos de proposição</li>
                <li>Aplicar os <strong>padrões legais LC 95/1998</strong></li>
                <li>Aplicar os parâmetros atualizados (cabeçalho, rodapé, formatação)</li>
                <li>Sobrescrever templates existentes</li>
            </ul>
            <p class="text-success mt-3"><strong>✅ Conformidade total com padrões jurídicos brasileiros</strong></p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, Aplicar Padrões LC 95/1998',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#17c653',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("templates.regenerar-todos") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Erro na regeneração');
                }
                return response;
            }).catch(error => {
                Swal.showValidationMessage(`Erro: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Templates Regenerados com Padrões Legais!',
                html: `
                    <div class="text-success mb-3">
                        <i class="ki-duotone ki-shield-tick fs-2x mb-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <p><strong>Todos os templates agora seguem:</strong></p>
                        <ul class="text-start">
                            <li>✅ LC 95/1998 - Estrutura obrigatória</li>
                            <li>✅ Numeração unificada por tipo e ano</li>
                            <li>✅ Metadados Dublin Core</li>
                            <li>✅ Formatação padronizada</li>
                            <li>✅ Acessibilidade WCAG 2.1</li>
                        </ul>
                    </div>
                `,
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Função para gerar template com padrões legais LC 95/1998
function gerarComPadroesLegais(tipoId, tipoNome) {
    Swal.fire({
        title: 'Gerar Template LC 95/1998',
        html: '<div class="text-center mb-4">' +
                '<i class="ki-duotone ki-law fs-3x text-success mb-3">' +
                    '<span class="path1"></span>' +
                    '<span class="path2"></span>' +
                '</i>' +
                '<p>Gerar template estruturado para:</p>' +
                '<p class="fw-bold fs-5">' + tipoNome + '</p>' +
            '</div>' +
            '<div class="alert alert-info">' +
                '<h6>O template incluirá:</h6>' +
                '<ul class="text-start mb-0">' +
                    '<li>Epígrafe formatada (TIPO Nº 000/AAAA)</li>' +
                    '<li>Ementa conforme padrões</li>' +
                    '<li>Preâmbulo legal</li>' +
                    '<li>Corpo articulado (Art. 1º, 2º...)</li>' +
                    '<li>Cláusula de vigência</li>' +
                    '<li>Variáveis dinâmicas</li>' +
                '</ul>' +
            '</div>',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Gerar Template Estruturado',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#17c653',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/admin/templates/${tipoId}/gerar-padroes-legais`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Erro na geração do template');
                }
                return response.json();
            }).catch(error => {
                Swal.showValidationMessage(`Erro: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value.success) {
            const estrutura = result.value.estrutura;
            Swal.fire({
                title: 'Template LC 95/1998 Gerado!',
                html: `
                    <div class="text-success mb-4">
                        <i class="ki-duotone ki-shield-tick fs-2x mb-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                    <div class="text-start">
                        <p class="fw-bold">Estrutura gerada:</p>
                        <ul>
                            <li><strong>Epígrafe:</strong> ${estrutura.epigrafe}</li>
                            <li><strong>Ementa:</strong> ${estrutura.ementa}</li>
                            <li><strong>Artigos:</strong> ${estrutura.artigos} estruturados</li>
                            <li><strong>Validação:</strong> ${estrutura.validacoes}</li>
                        </ul>
                        <div class="alert alert-success mt-3">
                            ✅ <strong>Conforme LC 95/1998 e padrões técnicos</strong>
                        </div>
                    </div>
                `,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Editar Template',
                cancelButtonText: 'Fechar',
                confirmButtonColor: '#007bff'
            }).then((editResult) => {
                if (editResult.isConfirmed) {
                    window.location.href = `/admin/templates/${tipoId}/editor`;
                } else {
                    location.reload();
                }
            });
        } else if (result.isConfirmed) {
            Swal.fire({
                title: 'Erro na Geração',
                text: result.value.message || 'Erro desconhecido',
                icon: 'error'
            });
        }
    });
}

// Função para validar template conforme padrões legais
function validarTemplate(tipoId, tipoNome) {
    Swal.fire({
        title: 'Validando Template...',
        text: 'Verificando conformidade com padrões legais',
        icon: 'info',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`/admin/templates/${tipoId}/validar`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const validacao = data.validacao;
                const detalhes = data.detalhes;
                
                const statusColor = validacao.status === 'aprovado' ? 'success' : 
                                   validacao.status === 'rejeitado' ? 'danger' : 'warning';
                                   
                const statusIcon = validacao.status === 'aprovado' ? 'shield-tick' : 
                                  validacao.status === 'rejeitado' ? 'shield-cross' : 'shield';

                Swal.fire({
                    title: 'Relatório de Validação',
                    html: `
                        <div class="text-center mb-4">
                            <i class="ki-duotone ki-${statusIcon} fs-3x text-${statusColor} mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                ${statusIcon === 'shield-tick' ? '<span class="path3"></span>' : ''}
                            </i>
                            <h4 class="text-${statusColor}">${tipoNome}</h4>
                        </div>
                        
                        <div class="row text-start">
                            <div class="col-md-6">
                                <div class="card card-bordered">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3">Conformidade Geral</h6>
                                        <div class="mb-2">
                                            <span class="badge badge-${statusColor} fs-7">${validacao.status.toUpperCase()}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Qualidade:</strong> ${validacao.qualidade_percentual}%
                                        </div>
                                        <div class="row">
                                            <div class="col-4 text-center">
                                                <div class="fs-6 fw-bold text-success">${validacao.total_aprovado}</div>
                                                <div class="fs-7 text-muted">Aprovado</div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="fs-6 fw-bold text-warning">${validacao.total_avisos}</div>
                                                <div class="fs-7 text-muted">Avisos</div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="fs-6 fw-bold text-danger">${validacao.total_erros}</div>
                                                <div class="fs-7 text-muted">Erros</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-bordered">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3">Conformidades</h6>
                                        <div class="mb-2">
                                            ${detalhes.lc95_conforme ? '✅' : '❌'} LC 95/1998
                                        </div>
                                        <div class="mb-2">
                                            ${detalhes.estrutura_adequada ? '✅' : '❌'} Estrutura Textual
                                        </div>
                                        <div class="mb-2">
                                            ${validacao.metadados_completos ? '✅' : '❌'} Metadados
                                        </div>
                                        <div class="mb-2">
                                            ${validacao.numeracao_conforme ? '✅' : '❌'} Numeração
                                        </div>
                                        <div class="mb-2">
                                            ${validacao.acessivel ? '✅' : '❌'} Acessibilidade
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${validacao.status !== 'aprovado' ? `
                            <div class="alert alert-warning mt-4">
                                <h6>Recomendações:</h6>
                                <ul class="mb-0">
                                    ${validacao.recomendacoes.map(rec => '<li>' + rec + '</li>').join('')}
                                </ul>
                            </div>
                        ` : `
                            <div class="alert alert-success mt-4">
                                ✅ <strong>Template está em total conformidade com os padrões legais!</strong>
                            </div>
                        `}
                    `,
                    icon: validacao.status === 'aprovado' ? 'success' : 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Corrigir Template',
                    cancelButtonText: 'Fechar',
                    confirmButtonColor: '#007bff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        gerarComPadroesLegais(tipoId, tipoNome);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Erro na Validação',
                    text: data.message,
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Erro na Validação',
                text: 'Erro ao conectar com o servidor: ' + error.message,
                icon: 'error'
            });
        });
}

// Função para visualizar variáveis do template
function visualizarVariaveis(tipoId, tipoNome) {
    // Atualizar título do modal
    document.getElementById('modal_variables_title').textContent = 'Variáveis do Template: ' + tipoNome;
    
    // Exibir modal
    const modal = new bootstrap.Modal(document.getElementById('kt_modal_variables'));
    modal.show();
}

// Função para copiar todas as variáveis
function copiarTodasVariaveis() {
    const variaveis = [
        // Variáveis da Proposição
        '${numero_proposicao}',
        '${tipo_proposicao}',
        '${ementa}',
        '${texto}',
        '${justificativa}',
        
        // Variáveis do Autor
        '${autor_nome}',
        '${autor_cargo}',
        '${autor_partido}',
        
        // Variáveis de Data
        '${data_atual}',
        '${ano}',
        
        // Variáveis da Câmara
        '${nome_camara}',
        '${municipio}',
        '${endereco_camara}',
        '${telefone_camara}',
        '${website_camara}',
        '${imagem_cabecalho}',
        
        // Variáveis de Formatação
        '${assinatura_padrao}',
        '${rodape}'
    ];
    
    const textoVariaveis = variaveis.join('\n');
    
    navigator.clipboard.writeText(textoVariaveis).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'Copiado!',
            text: 'Lista de variáveis copiada para a área de transferência.',
            timer: 2000,
            showConfirmButton: false
        });
    }, function(err) {
        // Fallback para browsers mais antigos
        const textArea = document.createElement('textarea');
        textArea.value = textoVariaveis;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        Swal.fire({
            icon: 'success',
            title: 'Copiado!',
            text: 'Lista de variáveis copiada para a área de transferência.',
            timer: 2000,
            showConfirmButton: false
        });
    });
}

// Variáveis globais para o preview
let currentPreviewTipoId = null;

// Função para gerar preview do template
function previewTemplate(tipoId, tipoNome) {
    currentPreviewTipoId = tipoId;
    
    // Atualizar título do modal
    document.getElementById('modal_preview_title').textContent = 'Preview: ' + tipoNome;
    
    // Resetar modal
    document.getElementById('preview_loading').style.display = 'block';
    document.getElementById('preview_content').style.display = 'none';
    document.getElementById('preview_error').style.display = 'none';
    
    // Exibir modal
    const modal = new bootstrap.Modal(document.getElementById('kt_modal_preview'));
    modal.show();
    
    // Fazer requisição para gerar preview
    fetch(`/templates/${tipoId}/preview`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('preview_loading').style.display = 'none';
            
            if (data.success) {
                // Exibir conteúdo do preview
                document.getElementById('preview_text').textContent = data.preview.conteudo;
                document.getElementById('preview_file_type').textContent = data.preview.arquivo_tipo.toUpperCase();
                
                // Gerar tabela com dados de exemplo
                const dadosExemplo = data.preview.dados_exemplo;
                let tabelaHtml = '<div class="table-responsive"><table class="table table-row-bordered align-middle"><thead><tr class="fw-bold fs-6 text-gray-800"><th>Variável</th><th>Valor Exemplo</th></tr></thead><tbody>';
                
                for (const [key, value] of Object.entries(dadosExemplo)) {
                    tabelaHtml += `<tr>
                        <td><code class="text-primary">\$\{${key}\}</code></td>
                        <td class="text-gray-700">${String(value).substring(0, 50)}${String(value).length > 50 ? '...' : ''}</td>
                    </tr>`;
                }
                
                tabelaHtml += '</tbody></table></div>';
                document.getElementById('preview_dados_exemplo').innerHTML = tabelaHtml;
                
                document.getElementById('preview_content').style.display = 'block';
            } else {
                // Exibir erro
                document.getElementById('preview_error_message').textContent = data.message;
                document.getElementById('preview_error').style.display = 'block';
            }
        })
        .catch(error => {
            document.getElementById('preview_loading').style.display = 'none';
            document.getElementById('preview_error_message').textContent = 'Erro ao conectar com o servidor: ' + error.message;
            document.getElementById('preview_error').style.display = 'block';
        });
}

// Função para editar template a partir do preview
function editarTemplateFromPreview() {
    if (currentPreviewTipoId) {
        window.location.href = `/templates/${currentPreviewTipoId}/editor`;
    }
}
</script>
@endpush