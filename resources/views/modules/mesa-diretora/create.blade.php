@extends('components.layouts.app')

@section('title', $title ?? 'Novo Membro - Mesa Diretora')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Novo Membro
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
                    <li class="breadcrumb-item text-muted">Novo Membro</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
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
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <div class="alert-text">{{ session('error') }}</div>
                </div>
            @endif

            <!--begin::Form-->
            <form id="kt_mesa_diretora_form" class="form d-flex flex-column flex-lg-row" action="{{ route('mesa-diretora.store') }}" method="POST">
                @csrf
                
                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <!--begin::General options-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações do Membro</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            @include('modules.mesa-diretora.components.form', ['isEdit' => false])
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::General options-->

                    <div class="d-flex justify-content-end">
                        <!--begin::Button-->
                        <a href="{{ route('mesa-diretora.index') }}" id="kt_mesa_diretora_cancel" class="btn btn-light me-5">Cancelar</a>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="submit" id="kt_mesa_diretora_submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Por favor aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!--end::Button-->
                    </div>
                </div>
                <!--end::Main column-->
            </form>
            <!--end::Form-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
@include('modules.mesa-diretora.components.form-scripts', ['isEdit' => false])
@endpush