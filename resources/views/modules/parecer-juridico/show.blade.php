@extends('components.layouts.app')

@section('title', 'Visualizar Parecer Jurídico')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Parecer Jurídico
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parecer-juridico.index') }}" class="text-muted text-hover-primary">Parecer Jurídico</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Visualizar</li>
                </ul>
            </div>
            <!--end::Page title-->
            
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if($parecerJuridico->assessor_id === auth()->id())
                    <a href="{{ route('parecer-juridico.edit', $parecerJuridico) }}" class="btn btn-sm btn-primary">
                        <i class="ki-duotone ki-pencil fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Editar Parecer
                    </a>
                @endif
                
                <a href="{{ route('parecer-juridico.pdf', $parecerJuridico) }}" class="btn btn-sm btn-light-primary">
                    <i class="ki-duotone ki-printer fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                    Gerar PDF
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!--begin::Row-->
            <div class="row gy-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12">
                    
                    <!--begin::Header Card-->
                    <div class="card card-flush mb-5">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="text-gray-900 fw-bold">
                                        Parecer Jurídico nº {{ $parecerJuridico->id }}
                                    </h2>
                                    <div class="text-muted fs-6">
                                        Emitido em {{ $parecerJuridico->data_emissao->format('d/m/Y \à\s H:i') }}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-{{ $parecerJuridico->getCorTipoParecer() }} fs-3">
                                        {{ $parecerJuridico->getTipoParecerFormatado() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Header Card-->

                    <!--begin::Proposição Card-->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">Dados da Proposição</h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ route('proposicoes.show', $parecerJuridico->proposicao) }}" 
                                   class="btn btn-sm btn-light-info">
                                    <i class="ki-duotone ki-document fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Ver Proposição Completa
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Tipo:</label>
                                        <div class="fs-6 text-gray-800">{{ $parecerJuridico->proposicao->tipo }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Número:</label>
                                        <div class="fs-6 text-gray-800">{{ $parecerJuridico->proposicao->numero ?? 'Não informado' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Protocolo:</label>
                                        <div class="fs-6 text-gray-800">
                                            @if($parecerJuridico->proposicao->numero_protocolo)
                                                <span class="badge badge-light-info">{{ $parecerJuridico->proposicao->numero_protocolo }}</span>
                                            @else
                                                Não protocolado
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Autor:</label>
                                        <div class="fs-6 text-gray-800">{{ $parecerJuridico->proposicao->autor->name ?? 'Não informado' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Data Protocolo:</label>
                                        <div class="fs-6 text-gray-800">
                                            {{ $parecerJuridico->proposicao->data_protocolo ? $parecerJuridico->proposicao->data_protocolo->format('d/m/Y H:i') : 'Não informado' }}
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Status:</label>
                                        <div class="fs-6 text-gray-800">
                                            <span class="badge badge-light-info">{{ $parecerJuridico->proposicao->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Ementa:</label>
                                        <div class="fs-6 text-gray-800 p-4 bg-light rounded">
                                            {{ $parecerJuridico->proposicao->ementa }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Proposição Card-->

                    <!--begin::Parecer Card-->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">Parecer Jurídico</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <!--begin::Assessor-->
                            <div class="mb-8">
                                <label class="form-label fw-bold text-gray-600">Assessor Jurídico:</label>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-3">
                                        <div class="symbol-label bg-light-primary text-primary fs-6 fw-bold">
                                            {{ strtoupper(substr($parecerJuridico->assessor->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fs-6 text-gray-800 fw-semibold">{{ $parecerJuridico->assessor->name }}</div>
                                        <div class="text-muted fs-7">{{ $parecerJuridico->assessor->email }}</div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Assessor-->

                            <!--begin::Tipo de Parecer-->
                            <div class="mb-8">
                                <label class="form-label fw-bold text-gray-600">Tipo de Parecer:</label>
                                <div class="mt-2">
                                    <span class="badge badge-{{ $parecerJuridico->getCorTipoParecer() }} fs-4 px-4 py-2">
                                        {{ $parecerJuridico->getTipoParecerFormatado() }}
                                    </span>
                                </div>
                            </div>
                            <!--end::Tipo de Parecer-->

                            <!--begin::Fundamentação-->
                            <div class="mb-8">
                                <label class="form-label fw-bold text-gray-600">Fundamentação Legal:</label>
                                <div class="bg-light-primary p-6 rounded mt-3">
                                    <div class="fs-6 text-gray-800" style="white-space: pre-wrap;">{{ $parecerJuridico->fundamentacao }}</div>
                                </div>
                            </div>
                            <!--end::Fundamentação-->

                            <!--begin::Conclusão-->
                            <div class="mb-8">
                                <label class="form-label fw-bold text-gray-600">Conclusão:</label>
                                <div class="bg-light-success p-6 rounded mt-3">
                                    <div class="fs-6 text-gray-800" style="white-space: pre-wrap;">{{ $parecerJuridico->conclusao }}</div>
                                </div>
                            </div>
                            <!--end::Conclusão-->

                            <!--begin::Emendas-->
                            @if($parecerJuridico->emendas)
                                <div class="mb-8">
                                    <label class="form-label fw-bold text-gray-600">Emendas Sugeridas:</label>
                                    <div class="bg-light-warning p-6 rounded mt-3">
                                        <div class="fs-6 text-gray-800" style="white-space: pre-wrap;">{{ $parecerJuridico->emendas }}</div>
                                    </div>
                                </div>
                            @endif
                            <!--end::Emendas-->

                            <!--begin::Metadados-->
                            <div class="separator separator-dashed my-8"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Data de Criação:</label>
                                        <div class="fs-6 text-gray-800">
                                            {{ $parecerJuridico->created_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-gray-600">Última Atualização:</label>
                                        <div class="fs-6 text-gray-800">
                                            {{ $parecerJuridico->updated_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Metadados-->
                        </div>
                    </div>
                    <!--end::Parecer Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection