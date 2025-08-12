@extends('components.layouts.app')

@section('title', 'Configurar Templates - Rodapé')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-scroll-down fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Configurar Rodapé dos Templates
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
                    <li class="breadcrumb-item text-muted">Rodapé</li>
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
                                <h3 class="fs-2 fw-bold text-gray-900 mb-1">Configurações do Rodapé</h3>
                                <span class="text-gray-500 fs-6">Configure o rodapé que aparecerá nos documentos</span>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <form id="rodape-form" method="POST">
                                @csrf
                                
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="usar_rodape" name="usar_rodape" value="1" {{ ($configuracoes['usar_rodape'] ?? true) ? 'checked' : '' }} />
                                        <label class="form-check-label fw-semibold fs-6" for="usar_rodape">
                                            Usar Rodapé
                                        </label>
                                    </div>
                                    <div class="form-text">Quando ativado, todos os documentos incluirão o rodapé configurado</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Tipo de Rodapé</label>
                                    <select class="form-select form-select-solid" name="rodape_tipo" id="rodape_tipo" required>
                                        <option value="texto" {{ ($configuracoes['tipo'] ?? 'texto') === 'texto' ? 'selected' : '' }}>Texto</option>
                                        <option value="imagem" {{ ($configuracoes['tipo'] ?? 'texto') === 'imagem' ? 'selected' : '' }}>Imagem</option>
                                        <option value="misto" {{ ($configuracoes['tipo'] ?? 'texto') === 'misto' ? 'selected' : '' }}>Texto + Imagem</option>
                                    </select>
                                    <div class="form-text">Tipo de conteúdo do rodapé</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7" id="texto-container" style="display: {{ ($configuracoes['tipo'] ?? 'texto') === 'imagem' ? 'none' : 'block' }}">
                                    <label class="form-label fw-semibold fs-6">Texto do Rodapé</label>
                                    <textarea class="form-control form-control-solid" name="rodape_texto" rows="3" maxlength="500" placeholder="Texto do rodapé">{{ $configuracoes['texto'] ?? 'Este documento foi gerado automaticamente pelo Sistema Legislativo.' }}</textarea>
                                    <div class="form-text">Texto que aparecerá no rodapé (máximo 500 caracteres)</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7" id="imagem-container" style="display: {{ ($configuracoes['tipo'] ?? 'texto') === 'texto' ? 'none' : 'block' }}">
                                    <label class="form-label fw-semibold fs-6">Imagem do Rodapé</label>
                                    <div class="mt-1">
                                        <!--begin::Image input placeholder-->
                                        <style>
                                            .rodape-placeholder { 
                                                background-image: url('{{ asset($configuracoes['imagem'] ?? 'template/rodape.png') }}'); 
                                                background-size: contain;
                                                background-repeat: no-repeat;
                                                background-position: center;
                                            } 
                                        </style>
                                        <!--end::Image input placeholder-->

                                        <!--begin::Image input-->
                                        <div class="image-input image-input-outline rodape-placeholder" data-kt-image-input="true">
                                            <!--begin::Preview existing image-->
                                            <div class="image-input-wrapper w-200px h-100px" id="rodape-preview" style="background-image: url('{{ asset($configuracoes['imagem'] ?? 'template/rodape.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                                            <!--end::Preview existing image-->

                                            <!--begin::Edit-->
                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Alterar imagem">
                                                <i class="ki-duotone ki-pencil fs-7">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <!--begin::Inputs-->
                                                <input type="file" name="rodape_image" accept=".png,.jpg,.jpeg" id="rodape-input" />
                                                <input type="hidden" name="rodape_imagem_atual" value="{{ $configuracoes['imagem'] ?? '' }}" />
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
                                        <div class="form-text">Formatos aceitos: PNG, JPG, JPEG. Tamanho máximo: 2MB. Recomendado: imagem com fundo transparente</div>
                                        <!--end::Hint-->
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Posição</label>
                                    <select class="form-select form-select-solid" name="rodape_posicao" required>
                                        <option value="rodape" {{ ($configuracoes['posicao'] ?? 'rodape') === 'rodape' ? 'selected' : '' }}>Rodapé da página</option>
                                        <option value="final" {{ ($configuracoes['posicao'] ?? 'rodape') === 'final' ? 'selected' : '' }}>Final do documento</option>
                                        <option value="todas_paginas" {{ ($configuracoes['posicao'] ?? 'rodape') === 'todas_paginas' ? 'selected' : '' }}>Todas as páginas</option>
                                    </select>
                                    <div class="form-text">Onde posicionar o rodapé no documento</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Alinhamento</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="rodape_alinhamento" id="align_left" value="esquerda" {{ ($configuracoes['alinhamento'] ?? 'centro') === 'esquerda' ? 'checked' : '' }} />
                                                <label class="form-check-label" for="align_left">
                                                    <i class="ki-duotone ki-text-align-left fs-2 me-2">
                                                        <span class="path1"></span>
                                                    </i>
                                                    Esquerda
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="rodape_alinhamento" id="align_center" value="centro" {{ ($configuracoes['alinhamento'] ?? 'centro') === 'centro' ? 'checked' : '' }} />
                                                <label class="form-check-label" for="align_center">
                                                    <i class="ki-duotone ki-text-align-center fs-2 me-2">
                                                        <span class="path1"></span>
                                                    </i>
                                                    Centro
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="rodape_alinhamento" id="align_right" value="direita" {{ ($configuracoes['alinhamento'] ?? 'centro') === 'direita' ? 'checked' : '' }} />
                                                <label class="form-check-label" for="align_right">
                                                    <i class="ki-duotone ki-text-align-right fs-2 me-2">
                                                        <span class="path1"></span>
                                                    </i>
                                                    Direita
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="rodape_numeracao" name="rodape_numeracao" value="1" {{ ($configuracoes['numeracao'] ?? true) ? 'checked' : '' }} />
                                        <label class="form-check-label fw-semibold fs-6" for="rodape_numeracao">
                                            Incluir Numeração de Páginas
                                        </label>
                                    </div>
                                    <div class="form-text">Adicionar numeração de páginas no rodapé</div>
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
                                <span class="text-gray-500 fs-6">Visualização do rodapé</span>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Preview area-->
                            <div class="position-relative bg-light-gray border border-dashed border-gray-300 rounded p-5" style="height: 300px; overflow: hidden;">
                                <div class="position-relative h-100 d-flex flex-column">
                                    <!-- Document content area -->
                                    <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                                        <div class="text-center">
                                            <p class="text-gray-700 mb-3">Conteúdo do documento</p>
                                            <p class="text-gray-500 fs-7">Esta área representa o conteúdo principal do documento.</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Footer preview area -->
                                    <div class="border-top border-gray-200 pt-3 mt-3" id="footer-preview-container">
                                        <div class="d-flex justify-content-center align-items-center" id="footer-content">
                                            <div class="text-center">
                                                <div id="footer-text" class="text-gray-600 fs-8">Este documento foi gerado automaticamente pelo Sistema Legislativo.</div>
                                                <div id="footer-image" class="mt-2" style="display: none;">
                                                    <img src="{{ asset($configuracoes['imagem'] ?? 'template/rodape.png') }}" alt="Rodapé" style="max-height: 30px; width: auto;" />
                                                </div>
                                                <div id="footer-pagination" class="text-gray-500 fs-9 mt-2">Página 1 de 1</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Preview area-->

                            <!--begin::Info-->
                            <div class="mt-5">
                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-geolocation fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Posição Atual</div>
                                            <div class="fs-7 text-gray-500" id="info-posicao">Rodapé da página</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-text-align-center fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Alinhamento</div>
                                            <div class="fs-7 text-gray-500" id="info-alinhamento">Centro</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-category fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Tipo</div>
                                            <div class="fs-7 text-gray-500" id="info-tipo">Texto</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Info-->

                            <!--begin::Notice-->
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mt-5">
                                <i class="ki-duotone ki-information fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Dica</h4>
                                        <div class="fs-6 text-gray-700">
                                            O rodapé será aplicado automaticamente em todos os documentos gerados quando a opção estiver ativada.
                                            <br><br>
                                            Use textos concisos e informativos para uma melhor apresentação.
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
            console.log('🦶 Rodapé carregado');
            
            const form = document.getElementById('rodape-form');
            const tipoSelect = document.getElementById('rodape_tipo');
            const textoContainer = document.getElementById('texto-container');
            const imagemContainer = document.getElementById('imagem-container');
            const footerText = document.getElementById('footer-text');
            const footerImage = document.getElementById('footer-image');
            const footerPagination = document.getElementById('footer-pagination');
            const footerContent = document.getElementById('footer-content');
            const fileInput = document.getElementById('rodape-input');
            const preview = document.getElementById('rodape-preview');
            
            // Toggle containers based on type
            function toggleContainers() {
                const tipo = tipoSelect.value;
                
                switch(tipo) {
                    case 'texto':
                        textoContainer.style.display = 'block';
                        imagemContainer.style.display = 'none';
                        footerText.style.display = 'block';
                        footerImage.style.display = 'none';
                        break;
                    case 'imagem':
                        textoContainer.style.display = 'none';
                        imagemContainer.style.display = 'block';
                        footerText.style.display = 'none';
                        footerImage.style.display = 'block';
                        break;
                    case 'misto':
                        textoContainer.style.display = 'block';
                        imagemContainer.style.display = 'block';
                        footerText.style.display = 'block';
                        footerImage.style.display = 'block';
                        break;
                }
                
                document.getElementById('info-tipo').textContent = tipo.charAt(0).toUpperCase() + tipo.slice(1);
            }
            
            tipoSelect.addEventListener('change', toggleContainers);
            
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
                fetch('{{ route("images.upload.rodape") }}', {
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
                        preview.style.backgroundImage = `url('${data.url}')`;
                        footerImage.querySelector('img').src = data.url;
                        
                        // Update hidden input
                        document.querySelector('input[name="rodape_imagem_atual"]').value = data.path || data.url;
                        
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
            
            // Update preview text
            document.querySelector('textarea[name="rodape_texto"]').addEventListener('input', function() {
                footerText.textContent = this.value || 'Este documento foi gerado automaticamente pelo Sistema Legislativo.';
            });
            
            // Update position info
            document.querySelector('select[name="rodape_posicao"]').addEventListener('change', function() {
                const posicoes = {
                    'rodape': 'Rodapé da página',
                    'final': 'Final do documento',
                    'todas_paginas': 'Todas as páginas'
                };
                document.getElementById('info-posicao').textContent = posicoes[this.value] || 'Rodapé da página';
            });
            
            // Update alignment info and preview
            document.querySelectorAll('input[name="rodape_alinhamento"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const alinhamentos = {
                        'esquerda': 'Esquerda',
                        'centro': 'Centro',
                        'direita': 'Direita'
                    };
                    document.getElementById('info-alinhamento').textContent = alinhamentos[this.value] || 'Centro';
                    
                    // Update preview alignment
                    footerContent.className = 'd-flex align-items-center justify-content-' + 
                        (this.value === 'esquerda' ? 'start' : this.value === 'direita' ? 'end' : 'center');
                });
            });
            
            // Toggle pagination
            document.getElementById('rodape_numeracao').addEventListener('change', function() {
                footerPagination.style.display = this.checked ? 'block' : 'none';
            });
            
            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                // Show loading
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add('btn-loading');
                
                fetch('{{ route("parametros.templates.rodape.store") }}', {
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
                            text: 'Configurações do rodapé salvas com sucesso!',
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
            
            // Initialize
            toggleContainers();
        });
    </script>
@endpush