@extends('components.layouts.app')

@section('title', 'Configurar Templates - Marca D\'água')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-water fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Configurar Marca D'água dos Templates
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
                    <li class="breadcrumb-item text-muted">Marca D'água</li>
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
                                <h3 class="fs-2 fw-bold text-gray-900 mb-1">Configurações da Marca D'água</h3>
                                <span class="text-gray-500 fs-6">Configure a marca d'água que aparecerá nos documentos</span>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <form id="marca-dagua-form" method="POST">
                                @csrf
                                
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="usar_marca_dagua" name="usar_marca_dagua" value="1" {{ ($configuracoes['usar_marca_dagua'] ?? false) ? 'checked' : '' }} />
                                        <label class="form-check-label fw-semibold fs-6" for="usar_marca_dagua">
                                            Usar Marca D'água
                                        </label>
                                    </div>
                                    <div class="form-text">Quando ativado, todos os documentos incluirão a marca d'água configurada</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Tipo de Marca D'água</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="marca_dagua_tipo" id="tipo_imagem" value="imagem" {{ ($configuracoes['tipo'] ?? 'imagem') === 'imagem' ? 'checked' : '' }} />
                                                <label class="form-check-label" for="tipo_imagem">
                                                    <i class="ki-duotone ki-picture fs-2 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Imagem
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="marca_dagua_tipo" id="tipo_texto" value="texto" {{ ($configuracoes['tipo'] ?? 'imagem') === 'texto' ? 'checked' : '' }} />
                                                <label class="form-check-label" for="tipo_texto">
                                                    <i class="ki-duotone ki-text fs-2 me-2">
                                                        <span class="path1"></span>
                                                    </i>
                                                    Texto
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7" id="imagem-container" style="display: {{ ($configuracoes['tipo'] ?? 'imagem') === 'imagem' ? 'block' : 'none' }}">
                                    <label class="form-label fw-semibold fs-6">Imagem da Marca D'água</label>
                                    <div class="mt-1">
                                        <!--begin::Image input placeholder-->
                                        <style>
                                            .marca-dagua-placeholder { 
                                                background-image: url('{{ asset($configuracoes['imagem'] ?? 'template/marca-dagua.png') }}'); 
                                                background-size: contain;
                                                background-repeat: no-repeat;
                                                background-position: center;
                                            } 
                                        </style>
                                        <!--end::Image input placeholder-->

                                        <!--begin::Image input-->
                                        <div class="image-input image-input-outline marca-dagua-placeholder" data-kt-image-input="true">
                                            <!--begin::Preview existing image-->
                                            <div class="image-input-wrapper w-200px h-200px" id="marca-dagua-preview" style="background-image: url('{{ asset($configuracoes['imagem'] ?? 'template/marca-dagua.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                                            <!--end::Preview existing image-->

                                            <!--begin::Edit-->
                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Alterar imagem">
                                                <i class="ki-duotone ki-pencil fs-7">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <!--begin::Inputs-->
                                                <input type="file" name="marca_dagua_image" accept=".png,.jpg,.jpeg" id="marca-dagua-input" />
                                                <input type="hidden" name="marca_dagua_imagem_atual" value="{{ $configuracoes['imagem'] ?? '' }}" />
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
                                        <div class="form-text">Formatos aceitos: PNG, JPG, JPEG. Tamanho máximo: 2MB. Recomendado: imagem com transparência</div>
                                        <!--end::Hint-->
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7" id="texto-container" style="display: {{ ($configuracoes['tipo'] ?? 'imagem') === 'texto' ? 'block' : 'none' }}">
                                    <label class="form-label fw-semibold fs-6">Texto da Marca D'água</label>
                                    <input type="text" class="form-control form-control-solid" name="marca_dagua_texto" placeholder="CONFIDENCIAL" value="{{ $configuracoes['texto'] ?? 'CONFIDENCIAL' }}" maxlength="255" />
                                    <div class="form-text">Texto que aparecerá como marca d'água</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Opacidade</label>
                                    <div class="input-group">
                                        <input type="range" class="form-range" name="marca_dagua_opacidade" id="opacidade-range" min="10" max="100" value="{{ $configuracoes['opacidade'] ?? 30 }}" />
                                        <span class="input-group-text" id="opacidade-valor">{{ $configuracoes['opacidade'] ?? 30 }}%</span>
                                    </div>
                                    <div class="form-text">Transparência da marca d'água (10% = muito transparente, 100% = opaco)</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Posição</label>
                                    <select class="form-select form-select-solid" name="marca_dagua_posicao" required>
                                        <option value="centro" {{ ($configuracoes['posicao'] ?? 'centro') === 'centro' ? 'selected' : '' }}>Centro</option>
                                        <option value="superior_direita" {{ ($configuracoes['posicao'] ?? 'centro') === 'superior_direita' ? 'selected' : '' }}>Superior Direita</option>
                                        <option value="superior_esquerda" {{ ($configuracoes['posicao'] ?? 'centro') === 'superior_esquerda' ? 'selected' : '' }}>Superior Esquerda</option>
                                        <option value="inferior_direita" {{ ($configuracoes['posicao'] ?? 'centro') === 'inferior_direita' ? 'selected' : '' }}>Inferior Direita</option>
                                        <option value="inferior_esquerda" {{ ($configuracoes['posicao'] ?? 'centro') === 'inferior_esquerda' ? 'selected' : '' }}>Inferior Esquerda</option>
                                    </select>
                                    <div class="form-text">Onde posicionar a marca d'água no documento</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-10">
                                    <label class="form-label fw-semibold fs-6 required">Tamanho</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-solid" name="marca_dagua_tamanho" placeholder="100" value="{{ $configuracoes['tamanho'] ?? 100 }}" min="50" max="300" required />
                                        <span class="input-group-text">px</span>
                                    </div>
                                    <div class="form-text">Tamanho da marca d'água em pixels (mínimo: 50px, máximo: 300px)</div>
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
                                <span class="text-gray-500 fs-6">Visualização da marca d'água</span>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Preview area-->
                            <div class="position-relative bg-light-gray border border-dashed border-gray-300 rounded p-5" style="height: 300px; overflow: hidden;">
                                <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" id="watermark-preview-container" style="opacity: {{ ($configuracoes['opacidade'] ?? 30) / 100 }}; z-index: 1;">
                                    <!-- Text watermark -->
                                    <span class="text-gray-600 fs-1 fw-bold" id="preview-text" style="display: {{ ($configuracoes['tipo'] ?? 'imagem') === 'texto' ? 'block' : 'none' }};">{{ $configuracoes['texto'] ?? 'CONFIDENCIAL' }}</span>
                                    <!-- Image watermark -->
                                    <div id="preview-image" style="display: {{ ($configuracoes['tipo'] ?? 'imagem') === 'imagem' ? 'block' : 'none' }}; width: {{ $configuracoes['tamanho'] ?? 100 }}px; height: {{ $configuracoes['tamanho'] ?? 100 }}px; background-image: url('{{ asset($configuracoes['imagem'] ?? 'template/marca-dagua.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                                </div>
                                <div class="position-relative" style="z-index: 2;">
                                    <p class="text-gray-700 mb-3">Este é um exemplo de documento com marca d'água.</p>
                                    <p class="text-gray-700 mb-3">A marca d'água aparecerá conforme as configurações definidas.</p>
                                    <p class="text-gray-700">Ajuste a opacidade, posição e tamanho conforme necessário.</p>
                                </div>
                            </div>
                            <!--end::Preview area-->

                            <!--begin::Info-->
                            <div class="mt-5">
                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-eye fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Opacidade Atual</div>
                                            <div class="fs-7 text-gray-500" id="info-opacidade">30%</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-geolocation fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Posição</div>
                                            <div class="fs-7 text-gray-500" id="info-posicao">Centro</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-stack py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-resize fs-2 text-gray-400 me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <div class="fs-6 fw-semibold text-gray-800">Tamanho</div>
                                            <div class="fs-7 text-gray-500" id="info-tamanho">100px</div>
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
                                            A marca d'água será aplicada automaticamente em todos os documentos gerados quando a opção estiver ativada.
                                            <br><br>
                                            Use uma opacidade baixa (10-30%) para não interferir na legibilidade do texto.
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
            console.log('🎨 Marca d\'água carregada');
            
            const form = document.getElementById('marca-dagua-form');
            const tipoImagem = document.getElementById('tipo_imagem');
            const tipoTexto = document.getElementById('tipo_texto');
            const textoContainer = document.getElementById('texto-container');
            const imagemContainer = document.getElementById('imagem-container');
            const opacidadeRange = document.getElementById('opacidade-range');
            const opacidadeValor = document.getElementById('opacidade-valor');
            const previewText = document.getElementById('preview-text');
            const previewImage = document.getElementById('preview-image');
            const watermarkContainer = document.getElementById('watermark-preview-container');
            const fileInput = document.getElementById('marca-dagua-input');
            const preview = document.getElementById('marca-dagua-preview');
            
            // Toggle containers
            function toggleContainers() {
                if (tipoTexto.checked) {
                    textoContainer.style.display = 'block';
                    imagemContainer.style.display = 'none';
                    previewText.style.display = 'block';
                    previewImage.style.display = 'none';
                } else {
                    textoContainer.style.display = 'none';
                    imagemContainer.style.display = 'block';
                    previewText.style.display = 'none';
                    previewImage.style.display = 'block';
                }
            }
            
            tipoImagem.addEventListener('change', toggleContainers);
            tipoTexto.addEventListener('change', toggleContainers);
            
            // Update preview position function
            function updatePreviewPosition(posicao) {
                watermarkContainer.className = 'position-absolute w-100 h-100 d-flex';
                watermarkContainer.style.zIndex = '1';
                
                switch(posicao) {
                    case 'centro':
                        watermarkContainer.style.alignItems = 'center';
                        watermarkContainer.style.justifyContent = 'center';
                        break;
                    case 'superior_direita':
                        watermarkContainer.style.alignItems = 'flex-start';
                        watermarkContainer.style.justifyContent = 'flex-end';
                        watermarkContainer.style.padding = '20px';
                        break;
                    case 'superior_esquerda':
                        watermarkContainer.style.alignItems = 'flex-start';
                        watermarkContainer.style.justifyContent = 'flex-start';
                        watermarkContainer.style.padding = '20px';
                        break;
                    case 'inferior_direita':
                        watermarkContainer.style.alignItems = 'flex-end';
                        watermarkContainer.style.justifyContent = 'flex-end';
                        watermarkContainer.style.padding = '20px';
                        break;
                    case 'inferior_esquerda':
                        watermarkContainer.style.alignItems = 'flex-end';
                        watermarkContainer.style.justifyContent = 'flex-start';
                        watermarkContainer.style.padding = '20px';
                        break;
                    default:
                        watermarkContainer.style.alignItems = 'center';
                        watermarkContainer.style.justifyContent = 'center';
                        watermarkContainer.style.padding = '0';
                }
            }
            
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
                fetch('{{ route("images.upload.marca-dagua") }}', {
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
                        previewImage.style.backgroundImage = `url('${data.url}')`;
                        
                        // Update hidden input
                        document.querySelector('input[name="marca_dagua_imagem_atual"]').value = data.path || data.url;
                        
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
            
            // Update opacity value display
            opacidadeRange.addEventListener('input', function() {
                const valor = this.value;
                opacidadeValor.textContent = valor + '%';
                document.getElementById('info-opacidade').textContent = valor + '%';
                watermarkContainer.style.opacity = valor / 100;
            });
            
            // Update preview text
            document.querySelector('input[name="marca_dagua_texto"]').addEventListener('input', function() {
                previewText.textContent = this.value || 'CONFIDENCIAL';
            });
            
            // Update position info
            document.querySelector('select[name="marca_dagua_posicao"]').addEventListener('change', function() {
                const posicoes = {
                    'centro': 'Centro',
                    'superior_direita': 'Superior Direita',
                    'superior_esquerda': 'Superior Esquerda',
                    'inferior_direita': 'Inferior Direita',
                    'inferior_esquerda': 'Inferior Esquerda'
                };
                document.getElementById('info-posicao').textContent = posicoes[this.value] || 'Centro';
                
                // Update preview position
                updatePreviewPosition(this.value);
            });
            
            // Update size info and preview
            function updatePreviewSize(tamanho) {
                const tamanhoPixels = tamanho + 'px';
                document.getElementById('info-tamanho').textContent = tamanhoPixels;
                
                // Update preview image size
                previewImage.style.width = tamanhoPixels;
                previewImage.style.height = tamanhoPixels;
                
                // Update text size proportionally (text uses font-size)
                const textSizeClass = tamanho < 80 ? 'fs-3' : tamanho < 120 ? 'fs-1' : 'fs-2x';
                previewText.className = `text-gray-600 fw-bold ${textSizeClass}`;
            }
            
            document.querySelector('input[name="marca_dagua_tamanho"]').addEventListener('input', function() {
                updatePreviewSize(parseInt(this.value));
            });
            
            // Initialize preview with current values
            function initializePreview() {
                // Apply initial position
                const posicaoAtual = document.querySelector('select[name="marca_dagua_posicao"]').value;
                updatePreviewPosition(posicaoAtual);
                
                // Apply initial size
                const tamanhoAtual = parseInt(document.querySelector('input[name="marca_dagua_tamanho"]').value);
                updatePreviewSize(tamanhoAtual);
                
                // Apply initial opacity
                const opacidadeAtual = parseInt(document.querySelector('input[name="marca_dagua_opacidade"]').value);
                watermarkContainer.style.opacity = opacidadeAtual / 100;
                
                console.log('🎨 Preview inicializado', {
                    posicao: posicaoAtual,
                    tamanho: tamanhoAtual,
                    opacidade: opacidadeAtual
                });
            }
            
            // Initialize when page loads
            initializePreview();
            
            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                // Show loading
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add('btn-loading');
                
                fetch('{{ route("parametros.templates.marca-dagua.store") }}', {
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
                            text: 'Configurações da marca d\'água salvas com sucesso!',
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