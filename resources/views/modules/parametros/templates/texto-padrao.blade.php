@extends('components.layouts.app')

@section('title', 'Configurar Templates - Texto Padrão')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-text fs-2 me-3">
                        <span class="path1"></span>
                    </i>
                    Configurar Texto Padrão dos Templates
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
                        <a href="{{ route('parametros.show', 6) }}" class="text-muted text-hover-primary">Templates</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Templates</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Texto Padrão</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="{{ route('parametros.show', 6) }}" class="btn btn-sm btn-flex btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-3"></i>
                    Voltar
                </a>
                <!--end::Secondary button-->
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
            <!--end::Alert-->

            <div class="row g-6 g-xl-9">
                <!--begin::Col-->
                <div class="col-lg-8">
                    <!--begin::Card-->
                    <div class="card card-flush h-lg-100">
                        <!--begin::Card header-->
                        <div class="card-header pt-5">
                            <!--begin::Card title-->
                            <div class="card-title d-flex flex-column">
                                <h3 class="fs-2 fw-bold text-gray-900 mb-1">Configurações do Texto Padrão</h3>
                                <span class="text-gray-500 fs-6">Configure textos padrão que aparecerão nos documentos</span>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <form id="texto-padrao-form" method="POST">
                                @csrf
                                
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="usar_texto_padrao" name="usar_texto_padrao" value="1" {{ ($configuracoes['usar_texto_padrao'] ?? false) ? 'checked' : '' }} />
                                        <label class="form-check-label fw-semibold fs-6" for="usar_texto_padrao">
                                            Usar Texto Padrão
                                        </label>
                                    </div>
                                    <div class="form-text">Quando ativado, os textos configurados serão inseridos automaticamente nos documentos</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6">Texto de Introdução</label>
                                    <textarea class="form-control form-control-solid" name="texto_introducao" rows="3" placeholder="Este documento apresenta proposta de lei que visa..." maxlength="1000">{{ $configuracoes['texto_introducao'] ?? 'Este documento apresenta proposta de lei que visa...' }}</textarea>
                                    <div class="form-text">Texto que aparecerá no início dos documentos</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6">Texto de Justificativa</label>
                                    <textarea class="form-control form-control-solid" name="texto_justificativa" rows="4" placeholder="A presente proposição justifica-se pela necessidade de..." maxlength="2000">{{ $configuracoes['texto_justificativa'] ?? 'A presente proposição justifica-se pela necessidade de...' }}</textarea>
                                    <div class="form-text">Texto padrão para a seção de justificativa</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6">Texto de Conclusão</label>
                                    <textarea class="form-control form-control-solid" name="texto_conclusao" rows="3" placeholder="Diante do exposto, submetemos esta proposição..." maxlength="1000">{{ $configuracoes['texto_conclusao'] ?? 'Diante do exposto, submetemos esta proposição à apreciação dos nobres pares desta Casa.' }}</textarea>
                                    <div class="form-text">Texto que aparecerá no final dos documentos</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-8"></div>
                                <!--end::Separator-->

                                <!--begin::Section title-->
                                <h4 class="fs-4 fw-bold text-gray-900 mb-5">Configurações de Assinatura</h4>
                                <!--end::Section title-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6">Cargo</label>
                                    <input type="text" class="form-control form-control-solid" name="assinatura_cargo" placeholder="Vereador(a)" value="{{ $configuracoes['assinatura_cargo'] ?? 'Vereador(a)' }}" maxlength="255" />
                                    <div class="form-text">Cargo padrão para assinatura dos documentos</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6">Nome</label>
                                    <input type="text" class="form-control form-control-solid" name="assinatura_nome" placeholder="Nome do responsável" value="{{ $configuracoes['assinatura_nome'] ?? '' }}" maxlength="255" />
                                    <div class="form-text">Nome padrão para assinatura (deixe em branco para usar dinamicamente)</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <label class="form-label fw-semibold fs-6">Departamento/Órgão</label>
                                    <input type="text" class="form-control form-control-solid" name="assinatura_departamento" placeholder="Câmara Municipal" value="{{ $configuracoes['assinatura_departamento'] ?? 'Câmara Municipal' }}" maxlength="255" />
                                    <div class="form-text">Nome do departamento ou órgão responsável</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Actions-->
                                <div class="text-center">
                                    <button type="reset" class="btn btn-light me-3">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Salvar Configurações</span>
                                        <span class="indicator-progress">Salvando...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                                <!--end::Actions-->
                            </form>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-lg-4">
                    <!--begin::Card-->
                    <div class="card card-flush h-lg-100">
                        <!--begin::Card header-->
                        <div class="card-header pt-5">
                            <!--begin::Card title-->
                            <div class="card-title d-flex flex-column">
                                <h3 class="fs-4 fw-bold text-gray-900 mb-1">Preview</h3>
                                <span class="text-gray-500 fs-6">Visualização do documento</span>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Preview area-->
                            <div class="bg-light-gray border border-dashed border-gray-300 rounded p-5" style="max-height: 500px; overflow-y: auto;">
                                <div class="fs-7 text-gray-700">
                                    <div class="mb-4">
                                        <strong>INTRODUÇÃO:</strong>
                                        <p id="preview-introducao" class="mt-2">{{ $configuracoes['texto_introducao'] ?? 'Este documento apresenta proposta de lei que visa...' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <strong>JUSTIFICATIVA:</strong>
                                        <p id="preview-justificativa" class="mt-2">{{ $configuracoes['texto_justificativa'] ?? 'A presente proposição justifica-se pela necessidade de...' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <strong>CONCLUSÃO:</strong>
                                        <p id="preview-conclusao" class="mt-2">{{ $configuracoes['texto_conclusao'] ?? 'Diante do exposto, submetemos esta proposição à apreciação dos nobres pares desta Casa.' }}</p>
                                    </div>
                                    
                                    <div class="separator separator-solid my-5"></div>
                                    
                                    <div class="text-center">
                                        <div id="preview-cargo" class="fw-bold">{{ $configuracoes['assinatura_cargo'] ?? 'Vereador(a)' }}</div>
                                        <div id="preview-nome" class="mt-1">{{ $configuracoes['assinatura_nome'] ?? '[Nome será inserido dinamicamente]' }}</div>
                                        <div id="preview-departamento" class="mt-1 text-muted">{{ $configuracoes['assinatura_departamento'] ?? 'Câmara Municipal' }}</div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Preview area-->

                            <!--begin::Info-->
                            <div class="mt-5">
                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-text-align-left fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Seções Configuradas</div>
                                            <div class="fs-7 text-gray-500">Introdução, Justificativa, Conclusão</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-user fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Assinatura</div>
                                            <div class="fs-7 text-gray-500">Cargo, Nome, Departamento</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-document fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Aplicação</div>
                                            <div class="fs-7 text-gray-500">Todas as proposições</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Info-->

                            <!--begin::Notice-->
                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mt-5">
                                <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Dica</h4>
                                        <div class="fs-6 text-gray-700">
                                            Os textos padrão configurados aqui serão inseridos automaticamente nos documentos quando a opção estiver ativada.
                                            <br><br>
                                            Você pode usar variáveis dinâmicas nos textos que serão substituídas automaticamente durante a geração dos documentos.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Notice-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
            </div>
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📝 Texto padrão carregado');
            
            const form = document.getElementById('texto-padrao-form');
            
            // Update preview in real-time
            document.querySelector('textarea[name="texto_introducao"]').addEventListener('input', function() {
                document.getElementById('preview-introducao').textContent = this.value || 'Este documento apresenta proposta de lei que visa...';
            });
            
            document.querySelector('textarea[name="texto_justificativa"]').addEventListener('input', function() {
                document.getElementById('preview-justificativa').textContent = this.value || 'A presente proposição justifica-se pela necessidade de...';
            });
            
            document.querySelector('textarea[name="texto_conclusao"]').addEventListener('input', function() {
                document.getElementById('preview-conclusao').textContent = this.value || 'Diante do exposto, submetemos esta proposição à apreciação dos nobres pares desta Casa.';
            });
            
            document.querySelector('input[name="assinatura_cargo"]').addEventListener('input', function() {
                document.getElementById('preview-cargo').textContent = this.value || 'Vereador(a)';
            });
            
            document.querySelector('input[name="assinatura_nome"]').addEventListener('input', function() {
                document.getElementById('preview-nome').textContent = this.value || '[Nome será inserido dinamicamente]';
            });
            
            document.querySelector('input[name="assinatura_departamento"]').addEventListener('input', function() {
                document.getElementById('preview-departamento').textContent = this.value || 'Câmara Municipal';
            });
            
            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                // Show loading
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add('btn-loading');
                
                fetch('{{ route("parametros.templates.texto-padrao.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.classList.remove('btn-loading');
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Configurações do texto padrão salvas com sucesso!',
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro',
                            text: data.message || 'Erro ao salvar configurações.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    submitBtn.classList.remove('btn-loading');
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Erro',
                        text: 'Erro de conexão ao salvar configurações.',
                        icon: 'error'
                    });
                });
            });
        });
    </script>
@endpush