@extends('components.layouts.app')

@section('title', $title ?? 'Detalhes do Parlamentar')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ $parlamentar['nome'] }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parlamentares.index') }}" class="text-muted text-hover-primary">Parlamentares</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $parlamentar['nome'] }}</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('parlamentares.edit', $parlamentar['id']) }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-pencil fs-2"></i>
                    Editar
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
                                    <img src="{{ isset($parlamentar['foto']) && $parlamentar['foto'] ? asset('storage/parlamentares/fotos/' . $parlamentar['foto']) : asset('assets/media/avatars/blank.png') }}" alt="Foto de {{ $parlamentar['nome'] }}" />
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Name-->
                                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">
                                    {{ isset($parlamentar['nome_politico']) && $parlamentar['nome_politico'] ? $parlamentar['nome_politico'] : $parlamentar['nome'] }}
                                </a>
                                @if(isset($parlamentar['nome_politico']) && $parlamentar['nome_politico'] && $parlamentar['nome_politico'] !== $parlamentar['nome'])
                                    <div class="text-muted fs-6 mb-3">{{ $parlamentar['nome'] }}</div>
                                @endif
                                <!--end::Name-->
                                <!--begin::Position-->
                                <div class="mb-9">
                                    <div class="badge badge-lg badge-light-primary d-inline">{{ $parlamentar['cargo'] }}</div>
                                    <div class="badge badge-lg badge-light-primary d-inline">{{ $parlamentar['partido'] }}</div>
                                </div>
                                <!--end::Position-->
                                <!--begin::Info-->
                                <div class="fw-bold mb-3">
                                    Status: 
                                    @if($parlamentar['status'] == 'Ativo')
                                        <span class="badge badge-success">{{ $parlamentar['status'] }}</span>
                                    @elseif($parlamentar['status'] == 'Licenciado')
                                        <span class="badge badge-warning">{{ $parlamentar['status'] }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $parlamentar['status'] }}</span>
                                    @endif
                                </div>
                                <!--end::Info-->
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
                                    <div class="fw-bold mt-5">Email</div>
                                    <div class="text-gray-600">
                                        <a href="mailto:{{ $parlamentar['email'] }}" class="text-gray-600 text-hover-primary">{{ $parlamentar['email'] }}</a>
                                    </div>
                                    <!--begin::Details item-->
                                    @if(isset($parlamentar['cpf']) && $parlamentar['cpf'])
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">CPF</div>
                                    <div class="text-gray-600">{{ $parlamentar['cpf'] }}</div>
                                    <!--begin::Details item-->
                                    @endif
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Telefone</div>
                                    <div class="text-gray-600">{{ $parlamentar['telefone'] }}</div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Data de Nascimento</div>
                                    <div class="text-gray-600">{{ $parlamentar['data_nascimento'] }}</div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Profissão</div>
                                    <div class="text-gray-600">{{ $parlamentar['profissao'] }}</div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Escolaridade</div>
                                    <div class="text-gray-600">{{ $parlamentar['escolaridade'] }}</div>
                                    <!--begin::Details item-->
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">Comissões</div>
                                    <div class="text-gray-600">
                                        @if(count($parlamentar['comissoes']) > 0)
                                            @foreach($parlamentar['comissoes'] as $comissao)
                                                <span class="badge badge-light me-2 mb-2">{{ $comissao }}</span>
                                            @endforeach
                                        @else
                                            Nenhuma comissão atribuída
                                        @endif
                                    </div>
                                    <!--begin::Details item-->
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
                            <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_view_overview_tab">Visão Geral</a>
                        </li>
                        <!--end:::Tab item-->
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_comissoes_tab">Comissões</a>
                        </li>
                        <!--end:::Tab item-->
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_mandatos_tab">Mandatos</a>
                        </li>
                        <!--end:::Tab item-->
                    </ul>
                    <!--end:::Tabs-->
                    <!--begin:::Tab content-->
                    <div class="tab-content" id="myTabContent">
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
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
                                                    <td class="text-muted">Nome Completo</td>
                                                    <td class="fw-bold text-end">{{ $parlamentar['nome'] }}</td>
                                                </tr>
                                                @if(isset($parlamentar['nome_politico']) && $parlamentar['nome_politico'])
                                                <tr>
                                                    <td class="text-muted">Nome Político</td>
                                                    <td class="fw-bold text-end">
                                                        <span class="badge badge-light-info">{{ $parlamentar['nome_politico'] }}</span>
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td class="text-muted">Partido</td>
                                                    <td class="fw-bold text-end">
                                                        <span class="badge badge-light-primary">{{ $parlamentar['partido'] }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Cargo</td>
                                                    <td class="fw-bold text-end">{{ $parlamentar['cargo'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Status</td>
                                                    <td class="fw-bold text-end">
                                                        @if($parlamentar['status'] == 'Ativo')
                                                            <span class="badge badge-success">{{ $parlamentar['status'] }}</span>
                                                        @elseif($parlamentar['status'] == 'Licenciado')
                                                            <span class="badge badge-warning">{{ $parlamentar['status'] }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $parlamentar['status'] }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Certificado Digital</td>
                                                    <td class="fw-bold text-end">
                                                        @if($parlamentar['user'] && $parlamentar['user']['certificado_digital_ativo'])
                                                            <span class="badge badge-success">
                                                                <i class="ki-duotone ki-security-check fs-7 me-1">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                Ativo
                                                            </span>
                                                            <div class="fs-7 text-muted mt-1">
                                                                {{ $parlamentar['user']['certificado_digital_cn'] ?? 'N/A' }}
                                                            </div>
                                                            @if($parlamentar['user']['certificado_digital_validade'])
                                                                <div class="fs-8 text-muted">
                                                                    Válido até: {{ \Carbon\Carbon::parse($parlamentar['user']['certificado_digital_validade'])->format('d/m/Y') }}
                                                                </div>
                                                            @endif
                                                        @elseif($parlamentar['user'] && $parlamentar['user']['certificado_digital_path'])
                                                            <span class="badge badge-warning">
                                                                <i class="ki-duotone ki-security-time fs-7 me-1">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                Inativo
                                                            </span>
                                                        @else
                                                            <span class="badge badge-light-danger">
                                                                <i class="ki-duotone ki-cross-circle fs-7 me-1">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                Não cadastrado
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Total de Comissões</td>
                                                    <td class="fw-bold text-end">
                                                        <span class="badge badge-light">{{ $parlamentar['total_comissoes'] }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Cadastrado em</td>
                                                    <td class="fw-bold text-end">{{ $parlamentar['created_at'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Última atualização</td>
                                                    <td class="fw-bold text-end">{{ $parlamentar['updated_at'] }}</td>
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
                        <div class="tab-pane fade" id="kt_user_view_comissoes_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Comissões</h2>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    @if(count($comissoes) > 0)
                                        <div class="row g-6 g-xl-9">
                                            @foreach($comissoes as $comissao)
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="card border-hover-primary">
                                                        <div class="card-body text-center pt-10 pb-9">
                                                            <i class="ki-duotone ki-abstract-39 fs-3x text-primary mb-4">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <h3 class="text-gray-800 fw-bold">{{ $comissao }}</h3>
                                                            <p class="text-gray-400 fw-semibold fs-6 mt-3">Comissão Permanente</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <img src="{{ asset('assets/media/illustrations/sketchy-1/2.png') }}" class="mw-300px">
                                            <div class="fs-1 fw-bolder text-dark mb-4">Nenhuma comissão</div>
                                            <div class="fs-6">Este parlamentar ainda não foi atribuído a nenhuma comissão.</div>
                                        </div>
                                    @endif
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end:::Tab pane-->
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade" id="kt_user_view_mandatos_tab" role="tabpanel">
                            <!--begin::Card-->
                            <div class="card pt-4 mb-6 mb-xl-9">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <h2>Histórico de Mandatos</h2>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    @if(count($parlamentar['mandatos']) > 0)
                                        <div class="timeline">
                                            @foreach($parlamentar['mandatos'] as $mandato)
                                                <div class="timeline-item">
                                                    <div class="timeline-line w-40px"></div>
                                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                        <div class="symbol-label bg-light">
                                                            @if($mandato['status'] == 'atual')
                                                                <i class="ki-duotone ki-abstract-25 fs-2 text-gray-500">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            @else
                                                                <i class="ki-duotone ki-abstract-25 fs-2 text-muted">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="timeline-content mb-10 mt-n1">
                                                        <div class="pe-3 mb-5">
                                                            <div class="fs-5 fw-semibold mb-2">
                                                                Mandato {{ $mandato['ano_inicio'] }} - {{ $mandato['ano_fim'] }}
                                                                @if($mandato['status'] == 'atual')
                                                                    <span class="badge badge-success ms-2">Atual</span>
                                                                @else
                                                                    <span class="badge badge-light-primary ms-2">Anterior</span>
                                                                @endif
                                                            </div>
                                                            <div class="overflow-auto pb-5">
                                                                <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                                                    <div class="d-flex flex-aligns-center pe-10 pe-lg-20">
                                                                        <img alt="" class="w-30px me-3" src="{{ asset('assets/media/svg/misc/settings.svg') }}" />
                                                                        <div class="fs-5 fw-bold">{{ $mandato['ano_inicio'] }}</div>
                                                                    </div>
                                                                    <div class="d-flex flex-aligns-center">
                                                                        <img alt="" class="w-30px me-3" src="{{ asset('assets/media/svg/misc/settings.svg') }}" />
                                                                        <div class="fs-5 fw-bold">{{ $mandato['ano_fim'] }}</div>
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
                                            <div class="fs-1 fw-bolder text-dark mb-4">Nenhum mandato</div>
                                            <div class="fs-6">Não há informações de mandatos para este parlamentar.</div>
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