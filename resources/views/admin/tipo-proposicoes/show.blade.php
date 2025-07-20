@extends('components.layouts.app')

@section('title', 'Visualizar Tipo de Proposição')

@section('content')
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                {{ $tipoProposicao->nome }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Admin</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.tipo-proposicoes.index') }}" class="text-muted text-hover-primary">Tipos de Proposição</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Visualizar</li>
            </ul>
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('admin.tipo-proposicoes.edit', $tipoProposicao) }}" class="btn btn-sm fw-bold btn-primary">
                <i class="ki-duotone ki-pencil fs-2"></i>
                Editar
            </a>
            <a href="{{ route('admin.tipo-proposicoes.index') }}" class="btn btn-sm fw-bold btn-secondary">
                <i class="ki-duotone ki-arrow-left fs-2"></i>
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
        <div class="d-flex flex-column flex-lg-row">
            <!--begin::Aside-->
            <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                <!--begin::Card-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Summary-->
                        <!--begin::User Info-->
                        <div class="d-flex flex-center flex-column py-5">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-100px symbol-circle mb-7">
                                <div class="symbol-label fs-2x fw-semibold text-{{ $tipoProposicao->cor }} bg-light-{{ $tipoProposicao->cor }}">
                                    <i class="{{ $tipoProposicao->icone_classe }}"></i>
                                </div>
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Name-->
                            <span class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $tipoProposicao->nome }}</span>
                            <!--end::Name-->
                            <!--begin::Position-->
                            <div class="mb-9">
                                <span class="badge badge-lg {{ $tipoProposicao->cor_badge }}">
                                    {{ $tipoProposicao->status_formatado['texto'] }}
                                </span>
                            </div>
                            <!--end::Position-->
                        </div>
                        <!--end::User Info-->
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
                                <div class="fw-bold mt-5">Código</div>
                                <div class="text-gray-600">
                                    <span class="badge badge-light-info">{{ $tipoProposicao->codigo }}</span>
                                </div>
                                <!--begin::Details item-->

                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Ordem</div>
                                <div class="text-gray-600">{{ $tipoProposicao->ordem }}</div>
                                <!--begin::Details item-->

                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Ícone</div>
                                <div class="text-gray-600">
                                    <i class="{{ $tipoProposicao->icone_classe }} me-2"></i>
                                    {{ $tipoProposicao->icone }}
                                </div>
                                <!--begin::Details item-->

                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Cor</div>
                                <div class="text-gray-600">
                                    <span class="badge badge-{{ $tipoProposicao->cor }} me-2"></span>
                                    {{ ucfirst($tipoProposicao->cor) }}
                                </div>
                                <!--begin::Details item-->

                                @if($tipoProposicao->template_padrao)
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Template Padrão</div>
                                <div class="text-gray-600">{{ $tipoProposicao->template_padrao }}</div>
                                <!--begin::Details item-->
                                @endif

                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Criado em</div>
                                <div class="text-gray-600">{{ $tipoProposicao->created_at->format('d/m/Y H:i') }}</div>
                                <!--begin::Details item-->

                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">Atualizado em</div>
                                <div class="text-gray-600">{{ $tipoProposicao->updated_at->format('d/m/Y H:i') }}</div>
                                <!--begin::Details item-->
                            </div>
                        </div>
                        <!--end::Details content-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Aside-->

            <!--begin::Content-->
            <div class="flex-lg-row-fluid ms-lg-15">
                <!--begin:::Tabs-->
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_view_overview_tab">Informações</a>
                    </li>
                    <!--end:::Tab item-->
                    @if($tipoProposicao->configuracoes)
                    <!--begin:::Tab item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_overview_config">Configurações</a>
                    </li>
                    <!--end:::Tab item-->
                    @endif
                </ul>
                <!--end:::Tabs-->

                <!--begin:::Tab content-->
                <div class="tab-content" id="myTabContent">
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Descrição</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                @if($tipoProposicao->descricao)
                                    <div class="fs-6 text-gray-600">
                                        {!! nl2br(e($tipoProposicao->descricao)) !!}
                                    </div>
                                @else
                                    <div class="text-muted fst-italic">
                                        Nenhuma descrição fornecida para este tipo de proposição.
                                    </div>
                                @endif
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Estatísticas de Uso</h2>
                                    <div class="fs-6 fw-semibold text-muted">Estatísticas de proposições deste tipo</div>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                <div class="row">
                                    <div class="col-sm-6 col-xl-3">
                                        <!--begin::Statistics-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <!--begin::Number-->
                                            <div class="d-flex align-items-center">
                                                <div class="fs-2 fw-bold counted" data-kt-countup="true" data-kt-countup-value="0">0</div>
                                            </div>
                                            <!--end::Number-->
                                            <!--begin::Label-->
                                            <div class="fw-semibold fs-6 text-gray-500">Total de Proposições</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Statistics-->
                                    </div>
                                    <div class="col-sm-6 col-xl-3">
                                        <!--begin::Statistics-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <!--begin::Number-->
                                            <div class="d-flex align-items-center">
                                                <div class="fs-2 fw-bold counted text-success" data-kt-countup="true" data-kt-countup-value="0">0</div>
                                            </div>
                                            <!--end::Number-->
                                            <!--begin::Label-->
                                            <div class="fw-semibold fs-6 text-gray-500">Aprovadas</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Statistics-->
                                    </div>
                                    <div class="col-sm-6 col-xl-3">
                                        <!--begin::Statistics-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <!--begin::Number-->
                                            <div class="d-flex align-items-center">
                                                <div class="fs-2 fw-bold counted text-warning" data-kt-countup="true" data-kt-countup-value="0">0</div>
                                            </div>
                                            <!--end::Number-->
                                            <!--begin::Label-->
                                            <div class="fw-semibold fs-6 text-gray-500">Em Tramitação</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Statistics-->
                                    </div>
                                    <div class="col-sm-6 col-xl-3">
                                        <!--begin::Statistics-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                            <!--begin::Number-->
                                            <div class="d-flex align-items-center">
                                                <div class="fs-2 fw-bold counted text-danger" data-kt-countup="true" data-kt-countup-value="0">0</div>
                                            </div>
                                            <!--end::Number-->
                                            <!--begin::Label-->
                                            <div class="fw-semibold fs-6 text-gray-500">Rejeitadas</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Statistics-->
                                    </div>
                                </div>
                                
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mt-6">
                                    <i class="ki-duotone ki-information fs-2tx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">
                                                As estatísticas serão exibidas quando o sistema de proposições estiver completamente implementado.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->

                    @if($tipoProposicao->configuracoes)
                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade" id="kt_user_view_overview_config" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header mt-6">
                                <!--begin::Card title-->
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">Configurações</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body p-9 pt-4">
                                <pre class="bg-light p-5 rounded"><code>{{ json_encode($tipoProposicao->configuracoes, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->
                    @endif
                </div>
                <!--end:::Tab content-->
            </div>
            <!--end::Content-->
        </div>
    </div>
</div>
<!--end::Content-->
@endsection