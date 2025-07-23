@extends('components.layouts.app')

@section('title', $title ?? 'Visualizar Partido')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ $partido->nome }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('partidos.index') }}" class="text-muted text-hover-primary">Partidos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $partido->sigla }}</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('partidos.index') }}" class="btn btn-sm fw-bold btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <a href="{{ route('partidos.edit', $partido->id) }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-pencil fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
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
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row gy-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Dados Principais-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Dados Principais</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Sigla:</span>
                                <span class="badge badge-light-primary fs-6">{{ $partido->sigla }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Número:</span>
                                <span class="badge badge-light-info fs-6">{{ $partido->numero }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Status:</span>
                                @if($partido->status === 'ativo')
                                    <span class="badge badge-light-success fs-6">Ativo</span>
                                @else
                                    <span class="badge badge-light-danger fs-6">Inativo</span>
                                @endif
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Presidente:</span>
                                <span class="text-gray-600 fs-6">{{ $partido->presidente ?? '-' }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Fundação:</span>
                                <span class="text-gray-600 fs-6">{{ $partido->fundacao_formatada ?: '-' }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Parlamentares:</span>
                                <span class="badge badge-light-primary fs-6">{{ $partido->total_parlamentares }}</span>
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Dados Principais-->

                    @if($partido->site)
                    <!--begin::Site-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Site Oficial</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center mb-7">
                                <span class="fw-semibold fs-5 text-gray-800 flex-grow-1">Website:</span>
                                <a href="{{ $partido->site }}" target="_blank" class="text-primary fs-6">
                                    {{ $partido->site }}
                                </a>
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Site-->
                    @endif
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-8">

                    <!--begin::Parlamentares-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Parlamentares</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">{{ $parlamentares->count() }} parlamentares</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-5">
                            @if($parlamentares->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-rounded table-striped border gy-7 gs-7">
                                        <thead>
                                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                                <th>Nome</th>
                                                <th>Cargo</th>
                                                <th>Status</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($parlamentares as $parlamentar)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                                                            @if($parlamentar->foto)
                                                                <div class="symbol-label">
                                                                    <img src="{{ $parlamentar->foto_url }}" alt="{{ $parlamentar->nome }}" class="w-100" />
                                                                </div>
                                                            @else
                                                                <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                                    {{ substr($parlamentar->nome, 0, 1) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="text-gray-800 fw-bold">{{ $parlamentar->nome_exibicao }}</span>
                                                            @if($parlamentar->nome_politico && $parlamentar->nome_politico !== $parlamentar->nome)
                                                                <span class="text-muted fs-7">{{ $parlamentar->nome }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-gray-600">{{ $parlamentar->cargo }}</span>
                                                </td>
                                                <td>
                                                    @if($parlamentar->status === 'ativo')
                                                        <span class="badge badge-light-success">Ativo</span>
                                                    @elseif($parlamentar->status === 'licenciado')
                                                        <span class="badge badge-light-warning">Licenciado</span>
                                                    @else
                                                        <span class="badge badge-light-danger">Inativo</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('parlamentares.show', $parlamentar->id) }}" 
                                                       class="btn btn-light btn-active-light-primary btn-sm">
                                                        Visualizar
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ki-duotone ki-users fs-3x text-gray-400 mb-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="text-gray-400 fs-5">Nenhum parlamentar encontrado</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Parlamentares-->
                </div>
                <!--end::Col-->
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection