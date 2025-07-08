@extends('components.layouts.app')

@section('title', $title ?? 'Detalhes da Comissão')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ $comissao['nome'] }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('comissoes.index') }}" class="text-muted text-hover-primary">Comissões</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $comissao['nome'] }}</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @can('comissoes.edit')
                <a href="{{ route('comissoes.edit', $comissao['id']) }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-pencil fs-2"></i>
                    Editar
                </a>
                @endcan
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ki-duotone ki-check-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ki-duotone ki-cross-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ session('error') }}
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
                                    <div class="symbol-label fs-1 fw-bold text-inverse-primary bg-light-primary">
                                        {{ strtoupper(substr($comissao['nome'], 0, 2)) }}
                                    </div>
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Name-->
                                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $comissao['nome'] }}</a>
                                <!--end::Name-->
                                <!--begin::Position-->
                                <div class="mb-9">
                                    @php
                                        $tipoBadge = match($comissao['tipo']) {
                                            'permanente' => 'primary',
                                            'temporaria' => 'warning',
                                            'especial' => 'info',
                                            'cpi' => 'danger',
                                            'mista' => 'secondary',
                                            default => 'light'
                                        };
                                    @endphp
                                    <div class="badge badge-lg badge-light-{{ $tipoBadge }} d-inline">{{ $comissao['tipo_formatado'] }}</div>
                                    @if($comissao['status'] === 'ativa')
                                        <div class="badge badge-lg badge-light-success d-inline">Ativa</div>
                                    @elseif($comissao['status'] === 'inativa')
                                        <div class="badge badge-lg badge-light-secondary d-inline">Inativa</div>
                                    @elseif($comissao['status'] === 'suspensa')
                                        <div class="badge badge-lg badge-light-warning d-inline">Suspensa</div>
                                    @else
                                        <div class="badge badge-lg badge-light-danger d-inline">Encerrada</div>
                                    @endif
                                </div>
                                <!--end::Position-->
                                <!--begin::Info-->
                                <div class="fw-bold mb-3">
                                    Membros: 
                                    <span class="badge badge-success">{{ $comissao['total_membros'] }}</span>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::Summary-->
                            <!--begin::Details toggle-->
                            <div class="d-flex flex-stack fs-4 py-3">
                                <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_comissao_view_details" role="button" aria-expanded="false" aria-controls="kt_comissao_view_details">
                                    Detalhes
                                    <span class="ms-2 rotate-180">
                                        <i class="ki-duotone ki-down fs-3"></i>
                                    </span>
                                </div>
                            </div>
                            <!--end::Details toggle-->
                            <div class="separator"></div>
                            <!--begin::Details content-->
                            <div id="kt_comissao_view_details" class="collapse show">
                                <div class="pb-5 fs-6">
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Finalidade</div>
                                    <div class="text-gray-600">{{ $comissao['finalidade'] }}</div>
                                    <!--end::Details item-->
                                    
                                    @if($comissao['descricao'])
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Descrição</div>
                                    <div class="text-gray-600">{{ $comissao['descricao'] }}</div>
                                    <!--end::Details item-->
                                    @endif
                                    
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Data de Criação</div>
                                    <div class="text-gray-600">{{ $comissao['data_criacao'] ?? 'Não informado' }}</div>
                                    <!--end::Details item-->
                                </div>
                            </div>
                            <!--end::Details content-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Sidebar-->
                <!--begin::Content-->
                <div class="flex-lg-row-fluid ms-lg-15">
                    <!--begin:::Tabs-->
                    <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_comissao_view_overview_tab">Visão Geral</a>
                        </li>
                        <!--end:::Tab item-->
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_comissao_view_membros_tab">Membros</a>
                        </li>
                        <!--end:::Tab item-->
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_comissao_view_reunioes_tab">Reuniões</a>
                        </li>
                        <!--end:::Tab item-->
                    </ul>
                    <!--end:::Tabs-->
                    <!--begin:::Tab content-->
                    <div class="tab-content" id="myTabContent">
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade show active" id="kt_comissao_view_overview_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Informações Gerais</h2>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0 pb-5">
                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed gy-5">
                                            <tbody class="fs-6 fw-semibold text-gray-600">
                                                <tr>
                                                    <td class="text-muted">Nome</td>
                                                    <td class="fw-bold text-end">{{ $comissao['nome'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Tipo</td>
                                                    <td class="fw-bold text-end">
                                                        <span class="badge badge-light-{{ $tipoBadge }}">{{ $comissao['tipo_formatado'] }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Status</td>
                                                    <td class="fw-bold text-end">
                                                        @if($comissao['status'] === 'ativa')
                                                            <span class="badge badge-success">{{ $comissao['status_formatado'] }}</span>
                                                        @elseif($comissao['status'] === 'inativa')
                                                            <span class="badge badge-secondary">{{ $comissao['status_formatado'] }}</span>
                                                        @elseif($comissao['status'] === 'suspensa')
                                                            <span class="badge badge-warning">{{ $comissao['status_formatado'] }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $comissao['status_formatado'] }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if($comissao['presidente'])
                                                <tr>
                                                    <td class="text-muted">Presidente</td>
                                                    <td class="fw-bold text-end">{{ $comissao['presidente']['nome'] }} ({{ $comissao['presidente']['partido'] }})</td>
                                                </tr>
                                                @endif
                                                @if($comissao['vice_presidente'])
                                                <tr>
                                                    <td class="text-muted">Vice-Presidente</td>
                                                    <td class="fw-bold text-end">{{ $comissao['vice_presidente']['nome'] }} ({{ $comissao['vice_presidente']['partido'] }})</td>
                                                </tr>
                                                @endif
                                                @if($comissao['relator'])
                                                <tr>
                                                    <td class="text-muted">Relator</td>
                                                    <td class="fw-bold text-end">{{ $comissao['relator']['nome'] }} ({{ $comissao['relator']['partido'] }})</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td class="text-muted">Total de Membros</td>
                                                    <td class="fw-bold text-end">
                                                        <span class="badge badge-light">{{ $comissao['total_membros'] }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Criada em</td>
                                                    <td class="fw-bold text-end">{{ $comissao['data_criacao'] ?? 'Não informado' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Última atualização</td>
                                                    <td class="fw-bold text-end">{{ $comissao['updated_at'] ?? 'Não informado' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table wrapper-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end:::Tab pane-->
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade" id="kt_comissao_view_membros_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Membros da Comissão</h2>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    @if(count($comissao['membros']) > 0)
                                        <div class="row g-6 g-xl-9">
                                            @foreach($comissao['membros'] as $membro)
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="card border-hover-primary">
                                                        <div class="card-body text-center pt-10 pb-9">
                                                            <div class="symbol symbol-60px symbol-circle mb-5">
                                                                <div class="symbol-label fs-2 fw-bold bg-light-primary text-primary">
                                                                    {{ strtoupper(substr($membro, 0, 2)) }}
                                                                </div>
                                                            </div>
                                                            <h3 class="text-gray-800 fw-bold">{{ $membro }}</h3>
                                                            <p class="text-gray-400 fw-semibold fs-6 mt-3">Membro</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <img src="{{ asset('assets/media/illustrations/sketchy-1/2.png') }}" class="mw-300px">
                                            <div class="fs-1 fw-bolder text-dark mb-4">Nenhum membro</div>
                                            <div class="fs-6">Esta comissão ainda não possui membros atribuídos.</div>
                                        </div>
                                    @endif
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end:::Tab pane-->
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade" id="kt_comissao_view_reunioes_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Reuniões</h2>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    @if(count($reunioes) > 0)
                                        <div class="timeline">
                                            @foreach($reunioes as $reuniao)
                                                <div class="timeline-item">
                                                    <div class="timeline-line w-40px"></div>
                                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                        <div class="symbol-label bg-light">
                                                            <i class="ki-duotone ki-calendar fs-2 text-gray-500">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-content mb-10 mt-n1">
                                                        <div class="pe-3 mb-5">
                                                            <div class="fs-5 fw-semibold mb-2">
                                                                Reunião - {{ $reuniao['data'] }} às {{ $reuniao['hora'] }}
                                                                @if($reuniao['status'] === 'realizada')
                                                                    <span class="badge badge-success ms-2">Realizada</span>
                                                                @else
                                                                    <span class="badge badge-warning ms-2">Agendada</span>
                                                                @endif
                                                            </div>
                                                            <div class="overflow-auto pb-5">
                                                                <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                                                    <div class="d-flex flex-column pe-10 pe-lg-20">
                                                                        <div class="fs-6 fw-bold">Local:</div>
                                                                        <div class="fs-7 text-muted">{{ $reuniao['local'] }}</div>
                                                                    </div>
                                                                    <div class="d-flex flex-column">
                                                                        <div class="fs-6 fw-bold">Pauta:</div>
                                                                        <div class="fs-7 text-muted">{{ $reuniao['pauta'] }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <img src="{{ asset('assets/media/illustrations/sketchy-1/2.png') }}" class="mw-300px">
                                            <div class="fs-1 fw-bolder text-dark mb-4">Nenhuma reunião</div>
                                            <div class="fs-6">Não há reuniões agendadas ou realizadas para esta comissão.</div>
                                        </div>
                                    @endif
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end:::Tab pane-->
                    </div>
                    <!--end:::Tab content-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Layout-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection