@extends('components.layouts.app')

@section('title', 'Configurações do Editor')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Configurações do Editor
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('parametros.index') }}" class="text-muted text-hover-primary">Parâmetros</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Editor</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('parametros.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::Alert-->
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-check fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Sucesso</h5>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Erro</h5>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(!isset($exibirBotaoPDF->id))
                <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1">Aviso</h5>
                        <span>Parâmetro não encontrado no banco de dados. As configurações serão salvas temporariamente. Execute as migrações para persistir os dados.</span>
                    </div>
                </div>
            @endif
            <!--end::Alert-->

            <!--begin::Form-->
            <form method="POST" action="{{ route('parametros.editor.config.store') }}" class="form">
                @csrf

                <!--begin::Card-->
                <div class="card card-flush">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="fw-bold">Configurações do OnlyOffice</h2>
                        </div>
                    </div>
                    <!--end::Card header-->

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Section - Exportação PDF-->
                        <div class="mb-10">
                            <!--begin::Title-->
                            <h3 class="text-gray-900 fw-bold mb-7">
                                <i class="ki-duotone ki-file-down fs-2 me-2 text-warning">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Exportação de PDF
                            </h3>
                            <!--end::Title-->

                            <!--begin::Row-->
                            <div class="row mb-7">
                                <!--begin::Col-->
                                <div class="col-md-12">
                                    <!--begin::Option-->
                                    <div class="bg-light-warning border border-warning border-dashed rounded p-6">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h4 class="text-gray-900 fw-bold mb-1">
                                                    Exibir Botão "Exportar PDF para S3"
                                                </h4>
                                                <div class="text-gray-700 fw-semibold fs-6">
                                                    Controla a exibição do botão de exportação de PDF para S3 no editor OnlyOffice
                                                </div>
                                                <div class="text-muted fs-7 mt-2">
                                                    <i class="ki-duotone ki-information-5 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    Quando habilitado, o botão aparece na barra de ferramentas do editor com opções de exportação
                                                </div>
                                            </div>
                                            <div>
                                                <!--begin::Switch-->
                                                <div class="form-check form-switch form-switch-sm">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="exibir_botao_pdf"
                                                           value="1"
                                                           id="exibir_botao_pdf"
                                                           {{ $exibirBotaoPDF->valor == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="exibir_botao_pdf">
                                                        <span class="badge {{ $exibirBotaoPDF->valor == '1' ? 'badge-success' : 'badge-secondary' }}" id="status_label">
                                                            {{ $exibirBotaoPDF->valor == '1' ? 'Habilitado' : 'Desabilitado' }}
                                                        </span>
                                                    </label>
                                                </div>
                                                <!--end::Switch-->
                                            </div>
                                        </div>

                                        <!--begin::Features list-->
                                        <div class="mt-4 pt-4 border-top border-warning">
                                            <h5 class="text-gray-800 fw-semibold mb-3">Funcionalidades do botão:</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <ul class="text-gray-700 fs-7">
                                                        <li><i class="ki-duotone ki-check-circle text-success fs-6 me-1"><span class="path1"></span><span class="path2"></span></i> Exportar para S3 (Recomendado)</li>
                                                        <li><i class="ki-duotone ki-check-circle text-success fs-6 me-1"><span class="path1"></span><span class="path2"></span></i> Baixar no Navegador</li>
                                                        <li><i class="ki-duotone ki-check-circle text-success fs-6 me-1"><span class="path1"></span><span class="path2"></span></i> Método Tradicional (Servidor)</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="alert alert-warning py-2 px-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ki-duotone ki-information-5 fs-2 text-warning me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            <div>
                                                                <div class="fw-bold text-gray-800">Nota:</div>
                                                                <div class="text-gray-700 fs-7">
                                                                    Desabilitar este botão oculta todas as opções de exportação de PDF do editor
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Features list-->
                                    </div>
                                    <!--end::Option-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Section-->

                        <!--begin::Info Section-->
                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                            <i class="ki-duotone ki-information-5 fs-2x text-primary me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Configurações Adicionais</h4>
                                    <div class="fs-6 text-gray-700">
                                        Mais configurações do editor OnlyOffice serão adicionadas em breve, incluindo:
                                        <ul class="mt-2">
                                            <li>Configurações de colaboração em tempo real</li>
                                            <li>Personalização da interface do editor</li>
                                            <li>Configurações de auto-save</li>
                                            <li>Permissões de edição por tipo de usuário</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Info Section-->
                    </div>
                    <!--end::Card body-->

                    <!--begin::Card footer-->
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('parametros.index') }}" class="btn btn-light btn-active-light-primary me-2">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-check fs-2 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Salvar Configurações
                        </button>
                    </div>
                    <!--end::Card footer-->
                </div>
                <!--end::Card-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const switchBtn = document.getElementById('exibir_botao_pdf');
        const statusLabel = document.getElementById('status_label');

        switchBtn.addEventListener('change', function() {
            if (this.checked) {
                statusLabel.textContent = 'Habilitado';
                statusLabel.classList.remove('badge-secondary');
                statusLabel.classList.add('badge-success');
            } else {
                statusLabel.textContent = 'Desabilitado';
                statusLabel.classList.remove('badge-success');
                statusLabel.classList.add('badge-secondary');
            }
        });
    });
</script>
@endpush