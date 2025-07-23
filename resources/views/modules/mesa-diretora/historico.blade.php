@extends('components.layouts.app')

@section('title', $title ?? 'Histórico - Mesa Diretora')

@section('content')
<style>
.timeline-badge-presidente { background-color: #F1416C !important; }
.timeline-badge-vice { background-color: #7239EA !important; }
.timeline-badge-secretario { background-color: #17C653 !important; }
.timeline-badge-tesoureiro { background-color: #FFC700 !important; }
.timeline-label { min-width: 130px; }
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Histórico de Mandatos
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
                    <li class="breadcrumb-item text-muted">Histórico</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('mesa-diretora.atual') }}" class="btn btn-sm fw-bold btn-info">
                    <i class="ki-duotone ki-eye fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Composição Atual
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

            @if($historico->count() > 0)
                <!--begin::Timeline-->
                <div class="card">
                    <div class="card-body">
                        <!--begin::Tab Content-->
                        <div class="tab-content">
                            <!--begin::Tab panel-->
                            <div id="kt_timeline_widget_1_tab_content_1" class="tab-pane fade show active">
                                <!--begin::Timeline-->
                                <div class="timeline-label">
                                    @foreach($historico as $ano => $mandatos)
                                        <!--begin::Timeline item-->
                                        <div class="timeline-item">
                                            <!--begin::Timeline line-->
                                            <div class="timeline-line w-40px"></div>
                                            <!--end::Timeline line-->

                                            <!--begin::Timeline icon-->
                                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                <div class="symbol-label bg-light">
                                                    <i class="ki-duotone ki-calendar fs-2 text-gray-500">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <!--end::Timeline icon-->

                                            <!--begin::Timeline content-->
                                            <div class="timeline-content mb-10 mt-n2">
                                                <!--begin::Timeline heading-->
                                                <div class="overflow-auto pe-3">
                                                    <!--begin::Title-->
                                                    <div class="fs-5 fw-semibold mb-2">Ano {{ $ano }}</div>
                                                    <!--end::Title-->

                                                    <!--begin::Description-->
                                                    <div class="d-flex align-items-center mt-1 fs-6">
                                                        <!--begin::Info-->
                                                        <div class="text-muted me-2 fs-7">{{ $mandatos->count() }} mandatos registrados</div>
                                                        <!--end::Info-->
                                                    </div>
                                                    <!--end::Description-->
                                                </div>
                                                <!--end::Timeline heading-->

                                                <!--begin::Timeline details-->
                                                <div class="overflow-auto pb-5">
                                                    <!--begin::Notice-->
                                                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed min-w-lg-600px flex-shrink-0 p-6 mt-5">
                                                        <!--begin::Wrapper-->
                                                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                                            <!--begin::Content-->
                                                            <div class="mb-3 mb-md-0 fw-semibold">
                                                                @foreach($mandatos as $membro)
                                                                    <div class="d-flex align-items-center mb-5">
                                                                        <!--begin::Flag-->
                                                                        <div class="symbol symbol-30px me-5">
                                                                            <span class="symbol-label 
                                                                                @if(str_contains(strtolower($membro->cargo_mesa), 'presidente'))
                                                                                    timeline-badge-presidente
                                                                                @elseif(str_contains(strtolower($membro->cargo_mesa), 'vice'))
                                                                                    timeline-badge-vice
                                                                                @elseif(str_contains(strtolower($membro->cargo_mesa), 'secretário'))
                                                                                    timeline-badge-secretario
                                                                                @elseif(str_contains(strtolower($membro->cargo_mesa), 'tesoureiro'))
                                                                                    timeline-badge-tesoureiro
                                                                                @else
                                                                                    bg-secondary
                                                                                @endif
                                                                            ">
                                                                                <i class="ki-duotone ki-abstract-8 fs-3 text-white">
                                                                                    <span class="path1"></span>
                                                                                    <span class="path2"></span>
                                                                                </i>
                                                                            </span>
                                                                        </div>
                                                                        <!--end::Flag-->

                                                                        <!--begin::Text-->
                                                                        <div class="d-flex flex-column">
                                                                            <a href="{{ route('mesa-diretora.show', $membro->id) }}" class="text-gray-800 text-hover-primary fw-bold">
                                                                                {{ $membro->parlamentar->nome ?? 'N/A' }}
                                                                            </a>
                                                                            <span class="text-muted fs-7">
                                                                                {{ $membro->cargo_mesa }} • 
                                                                                {{ $membro->mandato_inicio->format('d/m/Y') }} - {{ $membro->mandato_fim->format('d/m/Y') }}
                                                                                @if($membro->status === 'ativo')
                                                                                    <span class="badge badge-light-success ms-2">Ativo</span>
                                                                                @else
                                                                                    <span class="badge badge-light-secondary ms-2">Finalizado</span>
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                        <!--end::Text-->
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <!--end::Content-->
                                                        </div>
                                                        <!--end::Wrapper-->
                                                    </div>
                                                    <!--end::Notice-->
                                                </div>
                                                <!--end::Timeline details-->
                                            </div>
                                            <!--end::Timeline content-->
                                        </div>
                                        <!--end::Timeline item-->
                                    @endforeach
                                </div>
                                <!--end::Timeline-->
                            </div>
                            <!--end::Tab panel-->
                        </div>
                        <!--end::Tab Content-->
                    </div>
                </div>
                <!--end::Timeline-->
            @else
                <!--begin::Empty state-->
                <div class="card">
                    <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="{{ asset('assets/media/illustrations/sigma-1/4.png') }}" />
                        </div>
                        <div class="text-center pt-10 mb-20">
                            <h2 class="fs-2 fw-bold mb-7">Nenhum Histórico Encontrado</h2>
                            <p class="text-gray-400 fs-6 fw-semibold mb-10">
                                Ainda não há registros históricos da mesa diretora.<br />
                                Comece criando uma nova composição.
                            </p>
                            <a href="{{ route('mesa-diretora.create') }}" class="btn btn-primary">
                                <i class="ki-duotone ki-plus fs-2"></i>
                                Criar Primeira Composição
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