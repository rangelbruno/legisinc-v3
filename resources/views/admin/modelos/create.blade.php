@extends('components.layouts.app')

@section('title', 'Criar Modelo de Projeto')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Criar Modelo de Projeto
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('modelos.index') }}" class="text-muted text-hover-primary">Modelos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Criar</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('modelos.index') }}" class="btn btn-sm btn-light">
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
            
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <h3 class="fw-bold m-0">Selecionar Tipo de Projeto</h3>
                        </div>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    <div class="mb-10">
                        <div class="text-gray-600 fw-semibold fs-6 mb-2">
                            Escolha o tipo de projeto para o qual deseja criar um modelo. 
                            Cada tipo possui estruturas e campos específicos.
                        </div>
                    </div>

                    <!--begin::Row-->
                    <div class="row g-6 g-xl-9">
                        @foreach($tipos as $key => $tipo)
                            <div class="col-md-6 col-lg-4">
                                <!--begin::Card-->
                                <div class="card card-flush h-md-100 tipo-card" data-tipo="{{ $key }}">
                                    <!--begin::Card header-->
                                    <div class="card-header">
                                        <!--begin::Card title-->
                                        <div class="card-title d-flex flex-column">
                                            <div class="d-flex align-items-center">
                                                <!--begin::Icon-->
                                                <div class="symbol symbol-60px me-4">
                                                    <div class="symbol-label bg-light-primary">
                                                        @switch($key)
                                                            @case('projeto_lei_ordinaria')
                                                                <i class="ki-duotone ki-document fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                @break
                                                            @case('projeto_lei_complementar')
                                                                <i class="ki-duotone ki-file-added fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                </i>
                                                                @break
                                                            @case('emenda_constitucional')
                                                                <i class="ki-duotone ki-security-user fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                @break
                                                            @case('decreto_legislativo')
                                                                <i class="ki-duotone ki-notepad fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                    <span class="path4"></span>
                                                                    <span class="path5"></span>
                                                                </i>
                                                                @break
                                                            @case('resolucao')
                                                                <i class="ki-duotone ki-verify fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                @break
                                                            @case('indicacao')
                                                                <i class="ki-duotone ki-arrow-up-right fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                @break
                                                            @case('requerimento')
                                                                <i class="ki-duotone ki-questionnaire-tablet fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                @break
                                                            @default
                                                                <i class="ki-duotone ki-document fs-2x text-primary">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                        @endswitch
                                                    </div>
                                                </div>
                                                <!--end::Icon-->
                                                <div class="d-flex flex-column">
                                                    <h2 class="fs-6 fw-bold text-dark">{{ $tipo }}</h2>
                                                    <div class="fs-7 fw-semibold text-gray-400 mt-1">
                                                        Criar modelo legislativo
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Card title-->
                                    </div>
                                    <!--end::Card header-->
                                    
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <div class="d-flex flex-column">
                                            <!--begin::Description-->
                                            <div class="mb-7">
                                                <div class="text-muted fs-7 mb-3">
                                                    @switch($key)
                                                        @case('projeto_lei_ordinaria')
                                                            Projetos de lei que tratam de matérias de competência da União, estados ou municípios, conforme estabelecido na Constituição.
                                                            @break
                                                        @case('projeto_lei_complementar')
                                                            Projetos que regulamentam dispositivos constitucionais específicos, exigindo quórum qualificado para aprovação.
                                                            @break
                                                        @case('emenda_constitucional')
                                                            Propostas de modificação do texto constitucional, seguindo procedimento especial de tramitação.
                                                            @break
                                                        @case('decreto_legislativo')
                                                            Atos normativos destinados a regular matérias de competência exclusiva do Poder Legislativo.
                                                            @break
                                                        @case('resolucao')
                                                            Atos destinados a disciplinar matérias de caráter político, processual, legislativo ou administrativo.
                                                            @break
                                                        @case('indicacao')
                                                            Proposições que sugerem aos Poderes competentes a adoção de providências ou medidas de interesse público.
                                                            @break
                                                        @case('requerimento')
                                                            Proposições que visam solicitar informações ou providências de interesse da atividade legislativa.
                                                            @break
                                                        @default
                                                            Modelo genérico para documentos legislativos diversos.
                                                    @endswitch
                                                </div>
                                            </div>
                                            <!--end::Description-->
                                            
                                            <!--begin::Features-->
                                            <div class="mb-5">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="bullet bullet-dot bg-primary me-2"></span>
                                                    <span class="fw-semibold text-gray-600 fs-7">Estrutura pré-definida</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="bullet bullet-dot bg-success me-2"></span>
                                                    <span class="fw-semibold text-gray-600 fs-7">Campos personalizáveis</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="bullet bullet-dot bg-info me-2"></span>
                                                    <span class="fw-semibold text-gray-600 fs-7">Variáveis dinâmicas</span>
                                                </div>
                                            </div>
                                            <!--end::Features-->
                                        </div>
                                    </div>
                                    <!--end::Card body-->
                                    
                                    <!--begin::Card footer-->
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('modelos.editor', ['tipo' => $key]) }}" 
                                               class="btn btn-primary btn-sm w-100">
                                                <i class="ki-duotone ki-plus fs-4 me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Criar {{ $tipo }}
                                            </a>
                                        </div>
                                    </div>
                                    <!--end::Card footer-->
                                </div>
                                <!--end::Card-->
                            </div>
                        @endforeach
                    </div>
                    <!--end::Row-->
                    
                    <!--begin::Info section-->
                    <div class="row mt-10">
                        <div class="col-12">
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                                <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Dica Importante</h4>
                                        <div class="fs-6 text-gray-700">
                                            Após criar o modelo, você poderá personalizá-lo com variáveis dinâmicas como 
                                            <code>@{{NOME_AUTOR}}</code>, <code>@{{DATA_HOJE}}</code>, <code>@{{NUMERO_PROJETO}}</code>, 
                                            entre outras. O editor oferece recursos avançados de formatação e estruturação.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Info section-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<script>
// Adicionar efeito hover aos cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.tipo-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Adicionar click no card inteiro
        card.addEventListener('click', function() {
            const link = this.querySelector('a[href]');
            if (link) {
                window.location.href = link.href;
            }
        });
    });
});
</script>

<style>
.tipo-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.tipo-card:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.symbol-60px {
    width: 60px;
    height: 60px;
}

.bullet-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.notice {
    min-height: 70px;
}

@media (max-width: 768px) {
    .col-md-6.col-lg-4 {
        margin-bottom: 1.5rem;
    }
    
    .symbol-60px {
        width: 50px;
        height: 50px;
    }
    
    .fs-2x {
        font-size: 1.5rem !important;
    }
}
</style>
@endsection