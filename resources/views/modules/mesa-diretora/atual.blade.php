@extends('components.layouts.app')

@section('title', $title ?? 'Composição Atual - Mesa Diretora')

@section('content')
<style>
.member-card {
    transition: all 0.3s ease;
    border: 1px solid #e1e3ea;
}
.member-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.cargo-presidente { border-left: 5px solid #F1416C; }
.cargo-vice { border-left: 5px solid #7239EA; }
.cargo-secretario { border-left: 5px solid #17C653; }
.cargo-tesoureiro { border-left: 5px solid #FFC700; }
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Composição Atual
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('mesa-diretora.index') }}" class="text-muted text-hover-primary">Mesa Diretora</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Composição Atual</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('mesa-diretora.historico') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-time fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Histórico
                </a>
                <a href="{{ route('mesa-diretora.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo Membro
                </a>
                <a href="{{ route('mesa-diretora.index') }}" class="btn btn-sm fw-bold btn-secondary">
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
            
            @if(isset($error))
                <div class="alert alert-danger">
                    <div class="alert-text">{{ $error }}</div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <div class="alert-text">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <div class="alert-text">{{ session('error') }}</div>
                </div>
            @endif

            <!--begin::Info section-->
            <div class="card mb-7">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-primary">
                                <i class="ki-duotone ki-people fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                        </div>
                        <div class="d-flex flex-column flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="me-2">
                                    <a href="#" class="text-gray-800 text-decoration-none fs-3 fw-bold me-1">Mesa Diretora Atual</a>
                                    <span class="text-muted fs-7 fw-semibold">
                                        {{ $estatisticas['mandato_atual_inicio'] ?? 'N/A' }} - {{ $estatisticas['mandato_atual_fim'] ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="d-flex">
                                    <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 me-3">
                                        <div class="fs-6 text-gray-800 fw-bold">{{ $estatisticas['membros_ativos'] ?? 0 }}</div>
                                        <div class="fw-semibold text-gray-400">Membros Ativos</div>
                                    </div>
                                    <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4">
                                        <div class="fs-6 text-gray-800 fw-bold">{{ $estatisticas['total_membros'] ?? 0 }}</div>
                                        <div class="fw-semibold text-gray-400">Total de Membros</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Info section-->

            @if($composicao->count() > 0)
                <!--begin::Members Grid-->
                <div class="row g-6 g-xl-9">
                    @foreach($composicao as $membro)
                        <div class="col-md-6 col-xl-4">
                            <!--begin::Card-->
                            <div class="card member-card h-100 
                                @if(str_contains(strtolower($membro['cargo_mesa']), 'presidente'))
                                    cargo-presidente
                                @elseif(str_contains(strtolower($membro['cargo_mesa']), 'vice'))
                                    cargo-vice
                                @elseif(str_contains(strtolower($membro['cargo_mesa']), 'secretário'))
                                    cargo-secretario
                                @elseif(str_contains(strtolower($membro['cargo_mesa']), 'tesoureiro'))
                                    cargo-tesoureiro
                                @endif
                            ">
                                <!--begin::Card body-->
                                <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-65px symbol-circle mb-5">
                                        <span class="symbol-label fs-2x fw-semibold 
                                            @if(str_contains(strtolower($membro['cargo_mesa']), 'presidente'))
                                                text-inverse-primary bg-primary
                                            @elseif(str_contains(strtolower($membro['cargo_mesa']), 'vice'))
                                                text-inverse-info bg-info
                                            @elseif(str_contains(strtolower($membro['cargo_mesa']), 'secretário'))
                                                text-inverse-success bg-success
                                            @elseif(str_contains(strtolower($membro['cargo_mesa']), 'tesoureiro'))
                                                text-inverse-warning bg-warning
                                            @else
                                                text-inverse-secondary bg-secondary
                                            @endif
                                        ">
                                            {{ substr($membro['parlamentar_nome'], 0, 1) }}
                                        </span>
                                    </div>
                                    <!--end::Avatar-->

                                    <!--begin::Name-->
                                    <a href="{{ route('mesa-diretora.show', $membro['id']) }}" class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">
                                        {{ $membro['parlamentar_nome'] }}
                                    </a>
                                    <!--end::Name-->

                                    <!--begin::Position-->
                                    <div class="fw-semibold text-gray-400 mb-6">{{ $membro['parlamentar_partido'] }}</div>
                                    <!--end::Position-->

                                    <!--begin::Info-->
                                    <div class="d-flex flex-center flex-wrap mb-5">
                                        <!--begin::Stats-->
                                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                                            <div class="fs-6 fw-bold text-gray-700">Cargo</div>
                                            <div class="fw-semibold text-gray-400 fs-7">{{ $membro['cargo_formatado'] }}</div>
                                        </div>
                                        <!--end::Stats-->

                                        <!--begin::Stats-->
                                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                                            <div class="fs-6 fw-bold text-gray-700">Status</div>
                                            <div class="fw-semibold fs-7">
                                                @if($membro['is_mandato_ativo'])
                                                    <span class="badge badge-light-success badge-sm">Ativo</span>
                                                @else
                                                    <span class="badge badge-light-secondary badge-sm">Inativo</span>
                                                @endif
                                            </div>
                                        </div>
                                        <!--end::Stats-->
                                    </div>
                                    <!--end::Info-->

                                    <!--begin::Mandate period-->
                                    <div class="text-center mb-5">
                                        <div class="fs-7 text-muted">Mandato</div>
                                        <div class="fs-6 fw-bold text-gray-800">{{ $membro['mandato_formatado'] }}</div>
                                    </div>
                                    <!--end::Mandate period-->

                                    <!--begin::Actions-->
                                    <div class="d-flex">
                                        <a href="{{ route('mesa-diretora.show', $membro['id']) }}" class="btn btn-sm btn-light btn-active-primary me-2">
                                            <i class="ki-duotone ki-eye fs-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Ver
                                        </a>
                                        <a href="{{ route('mesa-diretora.edit', $membro['id']) }}" class="btn btn-sm btn-light btn-active-primary">
                                            <i class="ki-duotone ki-pencil fs-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Editar
                                        </a>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                    @endforeach
                </div>
                <!--end::Members Grid-->
            @else
                <!--begin::Empty state-->
                <div class="card">
                    <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="{{ asset('assets/media/illustrations/sigma-1/4.png') }}" />
                        </div>
                        <div class="text-center pt-10 mb-20">
                            <h2 class="fs-2 fw-bold mb-7">Nenhuma Composição Ativa</h2>
                            <p class="text-gray-400 fs-6 fw-semibold mb-10">
                                Não há uma composição ativa da mesa diretora no momento.<br />
                                Crie uma nova composição para começar.
                            </p>
                            <a href="{{ route('mesa-diretora.create') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-plus fs-2"></i>
                                Criar Composição
                            </a>
                        </div>
                    </div>
                </div>
                <!--end::Empty state-->
            @endif
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection