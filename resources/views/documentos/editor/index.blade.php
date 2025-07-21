@extends('components.layouts.app')

@section('title', 'Editor de Documentos')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Editor de Documentos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Documentos</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Editor</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('documentos.editor.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo Documento
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Row-->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <!--begin::Col-->
                <div class="col-xxl-6">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Começar com Modelo</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Selecione um modelo para começar</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-6">
                            @if($modelos->count() > 0)
                                <div class="row g-3">
                                    @foreach($modelos as $modelo)
                                        <div class="col-12">
                                            <div class="d-flex align-items-center p-4 border border-gray-300 border-dashed rounded">
                                                <div class="symbol symbol-50px me-5">
                                                    <div class="symbol-label bg-light-{{ $modelo->tipoProposicao->cor ?? 'primary' }}">
                                                        <i class="ki-duotone {{ $modelo->tipoProposicao->icone ?? 'ki-document' }} fs-2x text-{{ $modelo->tipoProposicao->cor ?? 'primary' }}">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('documentos.editor.create', ['modelo_id' => $modelo->id]) }}" 
                                                       class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $modelo->nome }}</a>
                                                    <span class="text-muted fw-semibold d-block">
                                                        {{ $modelo->tipoProposicao->nome ?? 'Modelo Geral' }}
                                                    </span>
                                                    @if($modelo->descricao)
                                                        <span class="text-gray-500 fw-normal fs-7 d-block mt-1">{{ Str::limit($modelo->descricao, 80) }}</span>
                                                    @endif
                                                </div>
                                                <div class="ms-3">
                                                    <span class="badge badge-light-success">{{ count($modelo->variaveis ?? []) }} variáveis</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="text-gray-500 fs-6 fw-semibold mb-4">Nenhum modelo disponível</div>
                                    <a href="{{ route('documentos.modelos.create') }}" class="btn btn-sm btn-light-primary">
                                        Criar Primeiro Modelo
                                    </a>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xxl-6">
                    <!--begin::Card-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Início Rápido</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Criar documento do zero</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-6">
                            <div class="d-flex flex-column gap-5">
                                <!--begin::Option-->
                                <div class="d-flex align-items-center p-4 border border-gray-300 border-dashed rounded">
                                    <div class="symbol symbol-50px me-5">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-document fs-2x text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="{{ route('documentos.editor.create') }}" 
                                           class="text-gray-900 fw-bold text-hover-primary fs-6">Documento em Branco</a>
                                        <span class="text-muted fw-semibold d-block">
                                            Começar com um documento vazio
                                        </span>
                                    </div>
                                </div>
                                <!--end::Option-->

                                <!--begin::Tips-->
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                                    <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Dica do Editor</h4>
                                            <div class="fs-6 text-gray-700">
                                                Use <code>${variavel}</code> para inserir variáveis dinâmicas no seu documento. 
                                                O editor oferece sugestões automáticas baseadas nos modelos disponíveis.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Tips-->
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Recent Documents-->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Documentos Recentes</h3>
                    <div class="card-toolbar">
                        <a href="{{ route('documentos.modelos.index') }}" class="btn btn-sm btn-light">
                            Ver Todos os Modelos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center py-10">
                        <div class="text-gray-500 fs-6 fw-semibold mb-4">Seus documentos recentes aparecerão aqui</div>
                        <a href="{{ route('documentos.editor.create') }}" class="btn btn-sm btn-primary">
                            Criar Primeiro Documento
                        </a>
                    </div>
                </div>
            </div>
            <!--end::Recent Documents-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection