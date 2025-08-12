@extends('components.layouts.app')

@section('title', 'Configurar Templates - Cabeçalho')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-document fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Configurar Cabeçalho dos Templates
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
                        <a href="{{ route('parametros.show', $moduloId) }}" class="text-muted text-hover-primary">Templates</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Templates</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Cabeçalho</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Secondary button-->
                <a href="{{ route('parametros.show', $moduloId) }}" class="btn btn-sm btn-flex btn-secondary">
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
                                <h3 class="fs-2 fw-bold text-gray-900 mb-1">Configurações do Cabeçalho</h3>
                                <span class="text-gray-500 fs-6">Configure a imagem e propriedades do cabeçalho padrão das proposições</span>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <form id="template-config-form" method="POST" action="{{ route('parametros.templates.cabecalho.store') }}">
                                @csrf
                                
                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <label class="form-label fw-semibold fs-6">Imagem do Cabeçalho</label>
                                    <div class="mt-1">
                                        <!--begin::Image input placeholder-->
                                        <style>
                                            .image-input-placeholder { 
                                                background-image: url('{{ asset('template/cabecalho.png') }}'); 
                                                background-size: contain;
                                                background-repeat: no-repeat;
                                                background-position: center;
                                            } 
                                        </style>
                                        <!--end::Image input placeholder-->

                                        <!--begin::Image input-->
                                        <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
                                            <!--begin::Preview existing image-->
                                            <div class="image-input-wrapper w-400px h-150px" id="cabecalho-preview" style="background-image: url('{{ asset('template/cabecalho.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                                            <!--end::Preview existing image-->

                                            <!--begin::Edit-->
                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Alterar imagem">
                                                <i class="ki-duotone ki-pencil fs-7">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <!--begin::Inputs-->
                                                <input type="file" name="cabecalho_image" accept=".png,.jpg,.jpeg" id="cabecalho-input" />
                                                <!--end::Inputs-->
                                            </label>
                                            <!--end::Edit-->

                                            <!--begin::Cancel-->
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancelar">
                                                <i class="ki-duotone ki-cross fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <!--end::Cancel-->

                                            <!--begin::Remove-->
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remover imagem">
                                                <i class="ki-duotone ki-cross fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <!--end::Remove-->
                                        </div>
                                        <!--end::Image input-->
                                        
                                        <!--begin::Hint-->
                                        <div class="form-text">Formatos aceitos: PNG, JPG, JPEG. Tamanho máximo: 2MB. Dimensões recomendadas: 800x200px</div>
                                        <!--end::Hint-->
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="usar_cabecalho_padrao" name="usar_cabecalho_padrao" value="1" {{ ($configuracoes['usar_padrao'] ?? true) ? 'checked' : '' }} />
                                        <label class="form-check-label fw-semibold fs-6" for="usar_cabecalho_padrao">
                                            Usar Cabeçalho Padrão
                                        </label>
                                    </div>
                                    <div class="form-text">Quando ativado, todas as proposições incluirão automaticamente o cabeçalho configurado</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Altura do Cabeçalho</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-solid" name="cabecalho_altura" placeholder="150" value="{{ $configuracoes['altura'] ?? 150 }}" min="50" max="300" required />
                                        <span class="input-group-text">px</span>
                                    </div>
                                    <div class="form-text">Altura em pixels do cabeçalho no documento (mínimo: 50px, máximo: 300px)</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <label class="form-label fw-semibold fs-6 required">Posição do Cabeçalho</label>
                                    <select class="form-select form-select-solid" name="cabecalho_posicao" required>
                                        <option value="topo" {{ ($configuracoes['posicao'] ?? 'topo') === 'topo' ? 'selected' : '' }}>Topo do documento</option>
                                        <option value="header" {{ ($configuracoes['posicao'] ?? 'topo') === 'header' ? 'selected' : '' }}>Cabeçalho da página</option>
                                        <option value="marca_dagua" {{ ($configuracoes['posicao'] ?? 'topo') === 'marca_dagua' ? 'selected' : '' }}>Marca d'água</option>
                                    </select>
                                    <div class="form-text">Onde posicionar o cabeçalho no documento</div>
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
                                <h3 class="fs-4 fw-bold text-gray-900 mb-1">Informações</h3>
                                <span class="text-gray-500 fs-6">Detalhes sobre o cabeçalho padrão</span>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Info-->
                            <div class="d-flex flex-stack py-3">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-picture fs-1 text-gray-400 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <div class="fs-6 fw-semibold text-gray-800">Imagem Atual</div>
                                        <div class="fs-7 text-gray-500">template/cabecalho.png</div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Info-->

                            <!--begin::Info-->
                            <div class="d-flex flex-stack py-3">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-switch fs-1 text-gray-400 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <div class="fs-6 fw-semibold text-gray-800">Status</div>
                                        <div class="fs-7 text-gray-500">Ativo</div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Info-->

                            <!--begin::Info-->
                            <div class="d-flex flex-stack py-3">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-resize fs-1 text-gray-400 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <div class="fs-6 fw-semibold text-gray-800">Dimensões</div>
                                        <div class="fs-7 text-gray-500">Recomendado: 800x200px</div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Info-->

                            <!--begin::Notice-->
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Dica</h4>
                                        <div class="fs-6 text-gray-700">
                                            O cabeçalho configurado aqui será usado automaticamente em todas as proposições quando a opção "Usar Cabeçalho Padrão" estiver ativada.
                                            <br><br>
                                            Para trocar a imagem por uma específica da sua câmara, simplesmente faça upload de uma nova imagem.
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
            console.log('🎨 Template cabeçalho carregado');
            
            const form = document.getElementById('template-config-form');
            const fileInput = document.getElementById('cabecalho-input');
            const preview = document.getElementById('cabecalho-preview');
            
            // Handle file upload
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    handleImageUpload(file);
                }
            });
            
            // Handle image upload
            function handleImageUpload(file) {
                // Validate file
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        title: 'Erro',
                        text: 'Por favor, selecione um arquivo de imagem válido.',
                        icon: 'error'
                    });
                    return;
                }
                
                if (file.size > 2048000) { // 2MB
                    Swal.fire({
                        title: 'Erro',
                        text: 'O arquivo deve ter no máximo 2MB.',
                        icon: 'error'
                    });
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'Enviando imagem...',
                    text: 'Por favor, aguarde.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Create FormData
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Upload file
                fetch('{{ route("images.upload.cabecalho") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    console.log('Upload response:', data);
                    
                    if (data.success) {
                        // Update preview
                        console.log('Updating preview with URL:', data.url);
                        preview.style.backgroundImage = `url('${data.url}')`;
                        
                        Swal.fire({
                            title: 'Sucesso!',
                            text: data.message,
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro',
                            text: data.message || 'Erro ao enviar imagem.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Erro',
                        text: 'Erro de conexão ao enviar imagem.',
                        icon: 'error'
                    });
                });
            }
            
            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                // Show loading
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add('btn-loading');
                
                fetch(form.action, {
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
                            text: 'Configurações salvas com sucesso!',
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