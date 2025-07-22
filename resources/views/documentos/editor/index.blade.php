@extends('components.layouts.app')

@section('title', 'Editor de Documentos')

@push('styles')
<style>
.template-card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-color: var(--bs-primary) !important;
}

.quick-action {
    transition: all 0.2s ease;
    cursor: pointer;
}

.quick-action:hover {
    transform: translateY(-1px);
    background-color: #f8f9fa;
}

.layout-config-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.layout-config-card .symbol-label {
    background: rgba(255,255,255,0.2) !important;
}

.stats-card {
    border-left: 4px solid var(--bs-primary);
}

.feature-highlight {
    background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
}
</style>
@endpush

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
                                            <div class="template-card d-flex align-items-center p-4 border border-gray-300 border-dashed rounded position-relative" onclick="window.location='{{ route('documentos.editor.create', ['modelo_id' => $modelo->id]) }}'">
                                                <div class="symbol symbol-50px me-5">
                                                    <div class="symbol-label bg-light-{{ $modelo->tipoProposicao->cor ?? 'primary' }}">
                                                        <i class="ki-duotone {{ $modelo->tipoProposicao->icone ?? 'ki-document' }} fs-2x text-{{ $modelo->tipoProposicao->cor ?? 'primary' }}">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="text-gray-900 fw-bold fs-6 mb-1">{{ $modelo->nome }}</div>
                                                    <span class="text-muted fw-semibold d-block">
                                                        {{ $modelo->tipoProposicao->nome ?? 'Modelo Geral' }}
                                                    </span>
                                                    @if($modelo->descricao)
                                                        <span class="text-gray-500 fw-normal fs-7 d-block mt-1">{{ Str::limit($modelo->descricao, 80) }}</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column align-items-end gap-2">
                                                    <span class="badge badge-light-success">{{ count($modelo->variaveis ?? []) }} variáveis</span>
                                                    <div class="d-flex align-items-center text-muted fs-7">
                                                        <i class="ki-duotone ki-arrow-right fs-4 text-primary"></i>
                                                    </div>
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
                    <!--begin::Layout Config Card-->
                    <div class="card card-flush h-xl-100 layout-config-card">
                        <!--begin::Header-->
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-white">Configurações de Layout</span>
                                <span class="text-white opacity-75 mt-1 fw-semibold fs-6">Configure cabeçalho, rodapé e marca d'água padrão</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-6">
                            <div class="d-flex flex-column gap-4">
                                <!--begin::Quick Actions-->
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="quick-action d-flex align-items-center p-3 bg-white bg-opacity-10 rounded">
                                            <div class="symbol symbol-30px me-3">
                                                <div class="symbol-label bg-white bg-opacity-20">
                                                    <i class="ki-duotone ki-design-1 fs-3 text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-white fw-semibold fs-7">Cabeçalho Oficial</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action d-flex align-items-center p-3 bg-white bg-opacity-10 rounded">
                                            <div class="symbol symbol-30px me-3">
                                                <div class="symbol-label bg-white bg-opacity-20">
                                                    <i class="ki-duotone ki-design-1 fs-3 text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-white fw-semibold fs-7">Rodapé Padrão</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action d-flex align-items-center p-3 bg-white bg-opacity-10 rounded">
                                            <div class="symbol symbol-30px me-3">
                                                <div class="symbol-label bg-white bg-opacity-20">
                                                    <i class="ki-duotone ki-picture fs-3 text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-white fw-semibold fs-7">Marca d'Água</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action d-flex align-items-center p-3 bg-white bg-opacity-10 rounded">
                                            <div class="symbol symbol-30px me-3">
                                                <div class="symbol-label bg-white bg-opacity-20">
                                                    <i class="ki-duotone ki-setting-3 fs-3 text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-white fw-semibold fs-7">Configurar</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Quick Actions-->

                                <!--begin::Main Action-->
                                <a href="{{ route('documentos.editor.create') }}" class="btn btn-light btn-active-light-primary w-100">
                                    <i class="ki-duotone ki-plus fs-2 me-2"></i>
                                    Criar Novo Documento
                                </a>
                                <!--end::Main Action-->
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Layout Config Card-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!--begin::Features and Stats-->
            <div class="row g-5 mb-5">
                <!--begin::Stats-->
                <div class="col-xl-8">
                    <div class="card stats-card">
                        <div class="card-header">
                            <h3 class="card-title">Recursos do Editor</h3>
                            <div class="card-toolbar">
                                <a href="{{ route('documentos.modelos.index') }}" class="btn btn-sm btn-light">
                                    <i class="ki-duotone ki-setting-3 fs-2"></i>
                                    Gerenciar Modelos
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-check fs-2 text-success"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-gray-900 fs-6">Inserção por @</div>
                                            <div class="text-muted fs-7">Use @ para inserir variáveis rapidamente</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-design-1 fs-2 text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-gray-900 fs-6">Layout Personalizado</div>
                                            <div class="text-muted fs-7">Cabeçalho, rodapé e marca d'água</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-warning">
                                                <i class="ki-duotone ki-file-down fs-2 text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-gray-900 fs-6">Exportação Múltipla</div>
                                            <div class="text-muted fs-7">Word (.docx) e PDF (.pdf)</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-4">
                                            <div class="symbol-label bg-light-info">
                                                <i class="ki-duotone ki-arrows-circle fs-2 text-info"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-gray-900 fs-6">Variáveis Dinâmicas</div>
                                            <div class="text-muted fs-7">Preenchimento automático de dados</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Stats-->

                <!--begin::Quick Tips-->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body feature-highlight text-center">
                            <i class="ki-duotone ki-information-5 fs-3x text-white mb-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <h4 class="text-white fw-bold mb-3">Novo! Layout Oficial</h4>
                            <p class="text-white opacity-75 mb-4">Configure uma vez o cabeçalho e rodapé oficial da Câmara e aplique em todos os documentos</p>
                            <a href="{{ route('documentos.editor.create') }}" class="btn btn-light btn-sm">
                                Experimentar Agora
                            </a>
                        </div>
                    </div>
                </div>
                <!--end::Quick Tips-->
            </div>
            <!--end::Features and Stats-->

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection