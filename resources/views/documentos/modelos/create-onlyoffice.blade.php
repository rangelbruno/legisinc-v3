@extends('components.layouts.app')

@section('title', 'Criar Modelo')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Criar Modelo
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('documentos.modelos.index') }}" class="text-muted text-hover-primary">Modelos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Criar documento</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('documentos.modelos.index') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Erro ao criar modelo!</h4>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!--begin::Form-->
            <form id="kt_modelo_online_form" class="form" action="{{ route('documentos.modelos.store-onlyoffice') }}" method="POST">
                @csrf
                
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2>
                                <i class="ki-duotone ki-document-edit text-success fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Criar Modelo com ONLYOFFICE
                            </h2>
                        </div>
                    </div>
                    <!--end::Card header-->

                    <!--begin::Card body-->
                    <div class="card-body pt-9 pb-0">
                        <!--begin::Alert-->
                        <div class="alert alert-info d-flex align-items-center p-5 mb-10">
                            <i class="ki-duotone ki-information-5 fs-2hx text-info me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-info">Editor Online</h4>
                                <span>Este modelo será criado diretamente no editor ONLYOFFICE. Você poderá editar colaborativamente em tempo real.</span>
                            </div>
                        </div>
                        <!--end::Alert-->

                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Nome do Modelo</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <input type="text" name="nome" class="form-control form-control-lg form-control-solid" 
                                       placeholder="Ex: Modelo de Projeto de Lei Ordinária" 
                                       value="{{ old('nome') }}" required />
                                <div class="form-text">Digite um nome descritivo para o modelo</div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->


                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Tipo de Proposição</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <select name="tipo_proposicao_id" class="form-select form-select-lg form-select-solid" data-control="select2" data-placeholder="Selecione um tipo">
                                    <option value="">Modelo Geral (todos os tipos)</option>
                                    @foreach($tiposProposicao as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_proposicao_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Associe o modelo a um tipo específico ou deixe como geral</div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->

                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Modelo Base</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <div class="d-flex flex-column">
                                    <div class="form-check form-check-custom form-check-solid mb-3">
                                        <input class="form-check-input" type="radio" name="tipo_base" value="vazio" id="tipo_vazio" checked />
                                        <label class="form-check-label fw-semibold" for="tipo_vazio">
                                            <i class="ki-duotone ki-document fs-4 text-muted me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Documento em branco
                                        </label>
                                    </div>
                                    <div class="form-check form-check-custom form-check-solid mb-3">
                                        <input class="form-check-input" type="radio" name="tipo_base" value="template" id="tipo_template" />
                                        <label class="form-check-label fw-semibold" for="tipo_template">
                                            <i class="ki-duotone ki-file-added fs-4 text-primary me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Usar template pré-configurado
                                        </label>
                                    </div>
                                </div>
                                
                                <!--begin::Template selector (hidden by default)-->
                                <div id="template_selector" style="display: none;" class="mt-5">
                                    <select name="template_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione um template">
                                        <option value="">Selecione um template...</option>
                                        <option value="projeto_lei">Projeto de Lei</option>
                                        <option value="decreto">Decreto</option>
                                        <option value="resolucao">Resolução</option>
                                        <option value="emenda">Emenda</option>
                                    </select>
                                </div>
                                <!--end::Template selector-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->

                        <!--begin::Variables Preview-->
                        <div class="row mb-7">
                            <!--begin::Label-->
                            <label class="col-lg-4 fw-semibold text-muted">Variáveis Disponíveis</label>
                            <!--end::Label-->
                            <!--begin::Col-->
                            <div class="col-lg-8">
                                <div class="alert alert-light-primary d-flex align-items-center p-5">
                                    <i class="ki-duotone ki-code fs-2hx text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-primary">Variáveis do Sistema</h4>
                                        <span>Use essas variáveis no seu documento. Elas serão automaticamente substituídas ao gerar documentos:</span>
                                        <div class="mt-3 d-flex flex-wrap gap-2">
                                            <span class="badge badge-light-primary">${numero_proposicao}</span>
                                            <span class="badge badge-light-primary">${tipo_proposicao}</span>
                                            <span class="badge badge-light-primary">${ementa}</span>
                                            <span class="badge badge-light-primary">${autor_nome}</span>
                                            <span class="badge badge-light-primary">${data_criacao}</span>
                                            <span class="badge badge-light-primary">${data_atual}</span>
                                            <span class="badge badge-light-primary">${casa_legislativa}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Variables Preview-->
                    </div>
                    <!--end::Card body-->

                    <!--begin::Actions-->
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2">Limpar</button>
                        <button type="submit" class="btn btn-success" id="kt_modelo_online_submit">
                            <span class="indicator-label">
                                <i class="ki-duotone ki-document-edit fs-4 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Criar e Abrir Editor
                            </span>
                            <span class="indicator-progress">Criando...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Card-->
            </form>
            <!--end::Form-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2
    $('[data-control="select2"]').select2();

    // Mostrar/ocultar seletor de template
    document.querySelectorAll('input[name="tipo_base"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const templateSelector = document.getElementById('template_selector');
            if (this.value === 'template') {
                templateSelector.style.display = 'block';
            } else {
                templateSelector.style.display = 'none';
            }
        });
    });

    // Validação e submit do formulário
    const form = document.getElementById('kt_modelo_online_form');
    const submitButton = document.getElementById('kt_modelo_online_submit');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevenir submit normal
        
        // Ativar indicador de loading
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
        
        // Coletar dados do formulário
        const formData = new FormData(form);
        
        // Fazer request AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Sucesso - abrir editor em nova aba
                window.open(data.editor_url, '_blank');
                
                // Escutar quando o OnlyOffice carregar
                const checkEditorLoaded = setInterval(() => {
                    const loadedTime = localStorage.getItem('onlyoffice_loaded');
                    if (loadedTime && (Date.now() - parseInt(loadedTime)) < 5000) {
                        // OnlyOffice carregou nos últimos 5 segundos
                        clearInterval(checkEditorLoaded);
                        localStorage.removeItem('onlyoffice_loaded');
                        
                        // Resetar botão
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                        
                        // Mostrar mensagem de sucesso e redirecionar
                        Swal.fire({
                            title: 'Sucesso!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Redirecionar para lista de modelos
                            window.location.href = "{{ route('documentos.modelos.index') }}";
                        });
                    }
                }, 500);
                
                // Fallback: resetar após 10 segundos se não receber notificação
                setTimeout(() => {
                    clearInterval(checkEditorLoaded);
                    if (submitButton.hasAttribute('data-kt-indicator')) {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                        
                        Swal.fire({
                            title: 'Editor Aberto',
                            text: 'O editor foi aberto em nova aba',
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Redirecionar para lista de modelos
                            window.location.href = "{{ route('documentos.modelos.index') }}";
                        });
                    }
                }, 10000);
                
            } else if (data.errors) {
                // Mostrar erros de validação
                let errorMessages = [];
                for (let field in data.errors) {
                    errorMessages.push(...data.errors[field]);
                }
                
                Swal.fire({
                    title: 'Erro de Validação',
                    html: errorMessages.join('<br>'),
                    icon: 'error'
                });
                
                // Resetar botão
                submitButton.removeAttribute('data-kt-indicator');
                submitButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            
            Swal.fire({
                title: 'Erro',
                text: 'Ocorreu um erro ao criar o documento',
                icon: 'error'
            });
            
            // Resetar botão
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
        });
    });
});
</script>
@endpush