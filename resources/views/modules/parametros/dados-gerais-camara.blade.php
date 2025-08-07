@extends('components.layouts.app')

@section('title', 'Configurar Dados Gerais da Câmara')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-bank fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Dados Gerais da Câmara
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
                    <li class="breadcrumb-item text-muted">Dados Gerais da Câmara</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::API Status button-->
                <button type="button" id="check-api-status-btn" class="btn btn-sm btn-flex btn-light-primary" onclick="checkApiStatus()">
                    <i class="ki-duotone ki-wifi fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Status APIs
                </button>
                <!--end::API Status button-->
                <!--begin::Secondary button-->
                <a href="{{ route('parametros.index') }}" class="btn btn-sm btn-flex btn-secondary">
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

            <!--begin::Form-->
            <form id="dados-gerais-form" method="POST" action="{{ route('parametros.dados-gerais-camara.store') }}">
                @csrf
                
                <div class="row g-6">
                    <!--begin::Col-->
                    <div class="col-lg-6">
                        <!--begin::Card-->
                        <div class="card card-flush">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">Identificação</h3>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Nome da Câmara</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control form-control-solid" name="nome_camara" 
                                               id="nome_camara_input"
                                               value="" 
                                               placeholder="Ex: Câmara Municipal de São Paulo ou digite apenas: São Paulo"
                                               autocomplete="off"
                                               required />
                                        <div id="busca_loading" class="position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Buscando...</span>
                                            </div>
                                        </div>
                                        <!-- Dropdown de sugestões -->
                                        <div id="camara_suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm" 
                                             style="display: none; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <i class="ki-duotone ki-information fs-6 text-warning me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Digite a cidade para preenchimento automático (dados são modelos genéricos - sempre verificar antes de salvar)
                                    </div>
                                    <!-- Alert de busca -->
                                    <div id="busca_alert" class="alert alert-info d-none mt-3" role="alert">
                                        <i class="ki-duotone ki-information-4 fs-2 text-info me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <h5 class="mb-1">Dados encontrados!</h5>
                                            <span id="busca_message">Os campos serão preenchidos automaticamente</span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Sigla</label>
                                            <input type="text" class="form-control form-control-solid" name="sigla_camara" 
                                                   value="{{ $configuracoes['sigla_camara'] ?? '' }}" maxlength="20" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">CNPJ</label>
                                            <input type="text" class="form-control form-control-solid" name="cnpj" 
                                                   data-mask="00.000.000/0000-00"
                                                   value="{{ $configuracoes['cnpj'] ?? '' }}" required />
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        <!--begin::Card-->
                        <div class="card card-flush mt-6">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">Endereço</h3>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Endereço</label>
                                            <input type="text" class="form-control form-control-solid" name="endereco" 
                                                   value="{{ $configuracoes['endereco'] ?? '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6">Número</label>
                                            <input type="text" class="form-control form-control-solid" name="numero" 
                                                   value="{{ $configuracoes['numero'] ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6">Complemento</label>
                                    <input type="text" class="form-control form-control-solid" name="complemento" 
                                           value="{{ $configuracoes['complemento'] ?? '' }}" />
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Bairro</label>
                                            <input type="text" class="form-control form-control-solid" name="bairro" 
                                                   value="{{ $configuracoes['bairro'] ?? '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">CEP</label>
                                            <input type="text" class="form-control form-control-solid" name="cep" 
                                                   data-mask="00000-000"
                                                   value="{{ $configuracoes['cep'] ?? '' }}" required />
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Cidade</label>
                                            <input type="text" class="form-control form-control-solid" name="cidade" 
                                                   value="{{ $configuracoes['cidade'] ?? '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Estado</label>
                                            <select class="form-select form-select-solid" name="estado" required>
                                                <option value="">Selecione</option>
                                                @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                                    <option value="{{ $uf }}" {{ ($configuracoes['estado'] ?? '') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-lg-6">
                        <!--begin::Card-->
                        <div class="card card-flush">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">Contatos</h3>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Telefone Principal</label>
                                            <input type="text" class="form-control form-control-solid" name="telefone" 
                                                   data-mask="(00) 0000-0000"
                                                   value="{{ $configuracoes['telefone'] ?? '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6">Telefone Secundário</label>
                                            <input type="text" class="form-control form-control-solid" name="telefone_secundario" 
                                                   data-mask="(00) 0000-0000"
                                                   value="{{ $configuracoes['telefone_secundario'] ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">E-mail Institucional</label>
                                    <input type="email" class="form-control form-control-solid" name="email_institucional" 
                                           value="{{ $configuracoes['email_institucional'] ?? '' }}" required />
                                    <div class="form-text">E-mail principal da instituição</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6">E-mail de Contato</label>
                                    <input type="email" class="form-control form-control-solid" name="email_contato" 
                                           value="{{ $configuracoes['email_contato'] ?? '' }}" />
                                    <div class="form-text">E-mail para atendimento ao público</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6">Website</label>
                                    <input type="url" class="form-control form-control-solid" name="website" 
                                           placeholder="https://"
                                           value="{{ $configuracoes['website'] ?? '' }}" />
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        <!--begin::Card-->
                        <div class="card card-flush mt-6">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">Funcionamento</h3>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Horário de Funcionamento</label>
                                    <input type="text" class="form-control form-control-solid" name="horario_funcionamento" 
                                           value="{{ $configuracoes['horario_funcionamento'] ?? '' }}" required />
                                    <div class="form-text">Ex: Segunda a Sexta, 8h às 17h</div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold fs-6 required">Horário de Atendimento ao Público</label>
                                    <input type="text" class="form-control form-control-solid" name="horario_atendimento" 
                                           value="{{ $configuracoes['horario_atendimento'] ?? '' }}" required />
                                    <div class="form-text">Ex: Segunda a Sexta, 8h às 16h</div>
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->

                        <!--begin::Card-->
                        <div class="card card-flush mt-6">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">Gestão Atual</h3>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Presidente da Câmara</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control form-control-solid" 
                                                       id="presidente_nome_input"
                                                       name="presidente_nome" 
                                                       value="{{ $configuracoes['presidente_nome'] ?? '' }}" 
                                                       placeholder="Digite o nome do presidente para buscar..."
                                                       autocomplete="off"
                                                       required />
                                                <input type="hidden" name="presidente_id" id="presidente_id" />
                                                
                                                <!-- Dropdown de sugestões -->
                                                <div id="presidente_suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm" 
                                                     style="display: none; z-index: 1000; max-height: 300px; overflow-y: auto; top: 100%;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Partido</label>
                                            <input type="text" class="form-control form-control-solid" 
                                                   id="presidente_partido_input"
                                                   name="presidente_partido" 
                                                   value="{{ $configuracoes['presidente_partido'] ?? '' }}" 
                                                   placeholder="Partido será preenchido automaticamente"
                                                   required />
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Legislatura Atual</label>
                                            <input type="text" class="form-control form-control-solid" name="legislatura_atual" 
                                                   placeholder="2021-2024"
                                                   value="{{ $configuracoes['legislatura_atual'] ?? '' }}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fv-row mb-7">
                                            <label class="form-label fw-semibold fs-6 required">Número de Vereadores</label>
                                            <input type="number" class="form-control form-control-solid" name="numero_vereadores" 
                                                   min="5" max="55"
                                                   value="{{ $configuracoes['numero_vereadores'] ?? '' }}" required />
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                </div>

                <!--begin::Actions-->
                <div class="card mt-6">
                    <div class="card-body">
                        <div class="text-center">
                            <button type="reset" class="btn btn-light me-3">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Salvar Configurações</span>
                                <span class="indicator-progress">Salvando...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📋 Dados Gerais da Câmara carregado');
            
            // Apply masks safely
            $('[data-mask]').each(function() {
                const $field = $(this);
                const mask = $field.data('mask');
                const value = $field.val();
                
                // Only apply mask if field has a value and it's a string
                if (mask && value && typeof value === 'string' && value.trim() !== '') {
                    console.log('Aplicando máscara', mask, 'para campo', $field.attr('name'), 'com valor', value);
                    $field.mask(mask);
                } else {
                    console.log('Pulando máscara para campo', $field.attr('name'), '- valor:', value, 'tipo:', typeof value);
                }
            });
            
            // Sistema de busca automática de câmaras (com delay para garantir DOM)
            setTimeout(() => {
                initCamaraBuscaAutomatica();
            }, 500);
            
            const form = document.getElementById('dados-gerais-form');
            
            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                // Show loading
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add('btn-loading');
                submitBtn.querySelector('.indicator-label').style.display = 'none';
                submitBtn.querySelector('.indicator-progress').style.display = 'inline-block';
                
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
                    submitBtn.querySelector('.indicator-label').style.display = 'inline-block';
                    submitBtn.querySelector('.indicator-progress').style.display = 'none';
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro',
                            text: data.message || 'Erro ao salvar configurações.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    submitBtn.classList.remove('btn-loading');
                    submitBtn.querySelector('.indicator-label').style.display = 'inline-block';
                    submitBtn.querySelector('.indicator-progress').style.display = 'none';
                    
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Erro',
                        text: 'Erro de conexão ao salvar configurações.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
            
            // Sistema de Busca Automática de Câmaras
            function initCamaraBuscaAutomatica() {
                const input = document.getElementById('nome_camara_input');
                const loading = document.getElementById('busca_loading');
                const suggestions = document.getElementById('camara_suggestions');
                const alert = document.getElementById('busca_alert');
                const message = document.getElementById('busca_message');
                
                let searchTimeout;
                let currentSuggestions = [];
                let isSearching = false; // Flag para evitar requisições simultâneas
                
                // Debounced search function
                function debouncedSearch(query) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (query.length >= 2) {
                            searchCamarasInteligente(query);
                        } else {
                            hideSuggestions();
                        }
                    }, 300);
                }
                
                // Search câmaras function inteligente
                async function searchCamarasInteligente(query) {
                    // Evitar requisições simultâneas
                    if (isSearching) {
                        console.log('⏸️ Busca já em andamento, pulando...');
                        return;
                    }
                    
                    try {
                        isSearching = true;
                        loading.style.display = 'block';
                        
                        // Detectar se está digitando apenas nome da cidade
                        const isJustCity = detectCityOnly(query);
                        const searchQuery = isJustCity ? query : query;
                        
                        const url = `/api/camaras/buscar?nome=${encodeURIComponent(searchQuery)}`;
                        
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json',
                                'Cache-Control': 'no-cache'
                            },
                            cache: 'no-store'
                        });
                        
                        console.log('🌐 Status da resposta:', response.status, response.statusText);
                        
                        // Verificar se a resposta foi bem-sucedida
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('❌ Erro HTTP:', response.status, errorText.substring(0, 200));
                            throw new Error(`Erro HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        // Verificar se a resposta é JSON válido
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            console.error('❌ Resposta não é JSON:', contentType);
                            const textResponse = await response.text();
                            console.error('📄 Conteúdo da resposta:', textResponse.substring(0, 200));
                            throw new Error('Resposta do servidor não é JSON válido');
                        }
                        
                        const data = await response.json();
                        
                        loading.style.display = 'none';
                        
                        if (data.success && data.camaras && data.camaras.length > 0) {
                            console.log('📊 Fonte dos dados:', data.fonte || 'desconhecida');
                            console.log('⚠️ Aviso:', data.aviso || 'Sem aviso');
                            
                            // Mostrar informação sobre a fonte dos dados
                            showDataSourceInfo(data.fonte, data.message, data.aviso);
                            
                            // Se está digitando só a cidade, mostrar sugestões de autocomplete
                            if (isJustCity) {
                                showAutocompleteSuggestions(data.camaras, query, data.fonte);
                            } else {
                                showSuggestions(data.camaras, data.fonte);
                            }
                        } else {
                            showNoResults(data.message || 'Nenhuma câmara encontrada');
                        }
                    } catch (error) {
                        loading.style.display = 'none';
                        console.error('💥 Erro na busca:', error);
                        
                        // Tratar diferentes tipos de erro
                        let userMessage = 'Erro ao buscar câmaras. Tente novamente.';
                        
                        if (error.message.includes('HTTP 419') || error.message.includes('HTTP 401')) {
                            userMessage = 'Sessão expirada. Recarregue a página.';
                        } else if (error.message.includes('HTTP 500')) {
                            userMessage = 'Erro interno do servidor. Tente novamente em alguns momentos.';
                        } else if (error.message.includes('JSON')) {
                            userMessage = 'Erro de comunicação com servidor. Verifique sua conexão.';
                        }
                        
                        showError(userMessage);
                    } finally {
                        isSearching = false; // Liberar flag de busca
                    }
                }
                
                // Detectar se está digitando apenas nome da cidade
                function detectCityOnly(query) {
                    const lowerQuery = query.toLowerCase().trim();
                    
                    // Se não contém "câmara" ou "municipal", provavelmente é só o nome da cidade
                    const hasInstitutionalTerms = lowerQuery.includes('câmara') || 
                                                lowerQuery.includes('camara') || 
                                                lowerQuery.includes('municipal');
                    
                    return !hasInstitutionalTerms && lowerQuery.length <= 20;
                }
                
                // Show autocomplete suggestions (for city names)
                function showAutocompleteSuggestions(camaras, query) {
                    currentSuggestions = camaras;
                    let html = '';
                    
                    camaras.forEach((camara, index) => {
                        // Destacar a parte que coincide
                        const cityName = camara.cidade;
                        const nomeCamara = camara.nome_camara;
                        const highlightedCity = highlightMatch(cityName, query);
                        const highlightedNome = highlightMatch(nomeCamara, query);
                        
                        html += `
                            <div class="suggestion-item p-3 border-bottom cursor-pointer hover-bg-light autocomplete-item" 
                                 data-index="${index}" 
                                 data-completion="${nomeCamara}"
                                 style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-bank fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-0 text-gray-800">${highlightedNome}</h6>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-muted fs-7 me-2">${highlightedCity}/${camara.estado}</span>
                                                    <span class="badge badge-light-primary fs-8">Complete automaticamente</span>
                                                </div>
                                                ${camara.cnpj ? `<span class="text-muted fs-8">CNPJ: ${camara.cnpj}</span>` : ''}
                                            </div>
                                            <div class="text-end">
                                                <i class="ki-duotone ki-arrow-right fs-6 text-muted">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    suggestions.innerHTML = html;
                    suggestions.style.display = 'block';
                    
                    // Add click handlers
                    suggestions.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', () => {
                            const index = parseInt(item.dataset.index);
                            const completion = item.dataset.completion;
                            
                            // Completar o nome no input primeiro
                            input.value = completion;
                            
                            // Depois selecionar a câmara
                            selectCamara(camaras[index]);
                        });
                        
                        // Adicionar hover para mostrar preview
                        item.addEventListener('mouseenter', () => {
                            const completion = item.dataset.completion;
                            showCompletionPreview(completion);
                        });
                    });
                }
                
                // Destacar texto que coincide com a busca
                function highlightMatch(text, query) {
                    if (!query.trim()) return text;
                    
                    const regex = new RegExp(`(${query.trim()})`, 'gi');
                    return text.replace(regex, '<mark class="bg-warning text-dark">$1</mark>');
                }
                
                // Mostrar preview do que será completado
                function showCompletionPreview(completion) {
                    const currentValue = input.value;
                    if (currentValue !== completion) {
                        // Criar efeito visual de preview
                        input.style.backgroundColor = '#f0f8ff';
                        input.setAttribute('data-preview', completion);
                    }
                }
                
                // Resetar preview
                function resetCompletionPreview() {
                    input.style.backgroundColor = '';
                    input.removeAttribute('data-preview');
                }
                
                // Show suggestions dropdown
                function showSuggestions(camaras) {
                    currentSuggestions = camaras;
                    let html = '';
                    
                    camaras.forEach((camara, index) => {
                        html += `
                            <div class="suggestion-item p-3 border-bottom cursor-pointer hover-bg-light" 
                                 data-index="${index}" 
                                 style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-bank fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div>
                                        <h6 class="mb-0 text-gray-800">${camara.nome_camara}</h6>
                                        <span class="text-muted fs-7">${camara.cidade}/${camara.estado}</span>
                                        ${camara.cnpj ? `<span class="text-muted fs-7 ms-2">CNPJ: ${camara.cnpj}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    suggestions.innerHTML = html;
                    suggestions.style.display = 'block';
                    
                    // Add click handlers
                    suggestions.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', () => {
                            const index = parseInt(item.dataset.index);
                            selectCamara(camaras[index]);
                        });
                    });
                }
                
                // Show no results
                function showNoResults(message) {
                    suggestions.innerHTML = `
                        <div class="p-4 text-center">
                            <i class="ki-duotone ki-information-2 fs-2x text-muted mb-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <p class="text-muted mb-2">${message}</p>
                            <small class="text-muted">Você pode preencher os dados manualmente</small>
                        </div>
                    `;
                    suggestions.style.display = 'block';
                }
                
                // Show error
                function showError(message) {
                    suggestions.innerHTML = `
                        <div class="p-4 text-center">
                            <i class="ki-duotone ki-cross-circle fs-2x text-danger mb-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <p class="text-danger">${message}</p>
                        </div>
                    `;
                    suggestions.style.display = 'block';
                }
                
                // Hide suggestions
                function hideSuggestions() {
                    suggestions.style.display = 'none';
                    currentSuggestions = [];
                }
                
                // Select câmara and fill form
                function selectCamara(camara) {
                    // Fill all form fields
                    fillFormField('nome_camara', camara.nome_camara);
                    fillFormField('sigla_camara', camara.sigla_camara);
                    fillFormField('cnpj', camara.cnpj);
                    fillFormField('endereco', camara.endereco);
                    fillFormField('numero', camara.numero);
                    fillFormField('complemento', camara.complemento);
                    fillFormField('bairro', camara.bairro);
                    fillFormField('cidade', camara.cidade);
                    fillFormField('estado', camara.estado);
                    fillFormField('cep', camara.cep);
                    fillFormField('telefone', camara.telefone);
                    fillFormField('telefone_secundario', camara.telefone_secundario);
                    fillFormField('email_institucional', camara.email_institucional);
                    fillFormField('email_contato', camara.email_contato);
                    fillFormField('website', camara.website);
                    fillFormField('horario_funcionamento', camara.horario_funcionamento);
                    fillFormField('horario_atendimento', camara.horario_atendimento);
                    fillFormField('presidente_nome', camara.presidente_nome);
                    fillFormField('presidente_partido', camara.presidente_partido);
                    fillFormField('legislatura_atual', camara.legislatura_atual);
                    fillFormField('numero_vereadores', camara.numero_vereadores);
                    
                    // Hide suggestions and show success
                    hideSuggestions();
                    showSuccessAlert(camara.nome_camara);
                    
                    // Note: Máscaras são aplicadas apenas na inicialização para evitar conflitos
                }
                
                // Fill form field helper
                function fillFormField(name, value) {
                    const field = document.querySelector(`[name="${name}"]`);
                    if (field && value !== undefined && value !== null && value !== '') {
                        field.value = String(value); // Ensure it's a string
                        // Trigger change event for any listeners
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
                
                // Show success alert
                function showSuccessAlert(camaraNome) {
                    message.textContent = `Dados da ${camaraNome} foram carregados automaticamente`;
                    alert.classList.remove('d-none', 'alert-info');
                    alert.classList.add('alert-success');
                    
                    // Auto-hide after 5 seconds
                    setTimeout(() => {
                        alert.classList.add('d-none');
                    }, 5000);
                }

                // Mostrar informação sobre a fonte dos dados
                function showDataSourceInfo(fonte, message, aviso) {
                    const alertBox = document.getElementById('busca_alert');
                    const messageBox = document.getElementById('busca_message');
                    
                    if (alertBox && messageBox) {
                        let badgeClass = 'badge-light-info';
                        let iconClass = 'ki-information-2';
                        
                        // Definir cor e ícone baseado na fonte
                        switch(fonte) {
                            case 'ibge':
                                badgeClass = 'badge-light-success';
                                iconClass = 'ki-check-circle';
                                break;
                            case 'viacep':
                                badgeClass = 'badge-light-primary';
                                iconClass = 'ki-geolocation';
                                break;
                            case 'dadosgovbr':
                                badgeClass = 'badge-light-warning';
                                iconClass = 'ki-data';
                                break;
                            case 'apilib_sp':
                                badgeClass = 'badge-light-success';
                                iconClass = 'ki-verify';
                                break;
                            case 'generico':
                                badgeClass = 'badge-light-danger';
                                iconClass = 'ki-warning-2';
                                break;
                        }
                        
                        const sourceName = getSourceDisplayName(fonte);
                        messageBox.innerHTML = `
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge ${badgeClass} me-2">${sourceName}</span>
                                <span class="text-gray-700 fs-6">${message}</span>
                            </div>
                            ${aviso ? `<div class="text-muted fs-7"><i class="ki-duotone ${iconClass} fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>${aviso}</div>` : ''}
                        `;
                        
                        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
                        alertBox.classList.add('alert-info');
                    }
                }

                // Obter nome amigável da fonte
                function getSourceDisplayName(fonte) {
                    const names = {
                        'ibge': 'IBGE',
                        'viacep': 'ViaCEP',
                        'dadosgovbr': 'Dados.gov.br',
                        'apilib_sp': 'APILIB SP',
                        'generico': 'Dados Genéricos',
                        'local': 'Base Local'
                    };
                    return names[fonte] || 'API Externa';
                }

                // Atualizar função showAutocompleteSuggestions para incluir fonte
                function showAutocompleteSuggestions(camaras, query, fonte = null) {
                    currentSuggestions = camaras;
                    let html = '';
                    
                    camaras.forEach((camara, index) => {
                        // Detectar qual parte da cidade está sendo digitada
                        const completion = getSmartCompletion(camara.cidade, query);
                        
                        // Destacar a parte que coincide
                        const cityName = camara.cidade;
                        const nomeCamara = camara.nome_camara;
                        const highlightedCity = highlightMatch(cityName, query);
                        const highlightedNome = highlightMatch(nomeCamara, query);
                        
                        // Obter badge da fonte
                        const sourceBadge = fonte ? `<span class="badge badge-light-${getSourceBadgeColor(fonte)} fs-8 ms-2">${getSourceDisplayName(fonte)}</span>` : '';
                        
                        html += `
                            <div class="suggestion-item p-3 border-bottom cursor-pointer hover-bg-light autocomplete-item" 
                                 data-index="${index}" 
                                 data-completion="${completion}"
                                 style="cursor: pointer; transition: all 0.2s;">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-bank fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-0 text-gray-800">${highlightedNome}</h6>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-muted fs-7 me-2">${highlightedCity}/${camara.estado}</span>
                                                    <span class="badge badge-light-primary fs-8">Complete automaticamente</span>
                                                    ${sourceBadge}
                                                </div>
                                                ${camara.cnpj ? `<span class="text-muted fs-8">CNPJ: ${camara.cnpj}</span>` : ''}
                                            </div>
                                            <div class="text-end">
                                                <i class="ki-duotone ki-arrow-right fs-6 text-muted">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    suggestions.innerHTML = html;
                    suggestions.style.display = 'block';
                    
                    // Add click handlers
                    suggestions.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', () => {
                            const index = parseInt(item.dataset.index);
                            const completion = item.dataset.completion;
                            
                            // Completar o nome no input primeiro
                            input.value = completion;
                            
                            // Depois selecionar a câmara
                            selectCamara(camaras[index]);
                        });
                        
                        // Adicionar hover para mostrar preview
                        item.addEventListener('mouseenter', () => {
                            const completion = item.dataset.completion;
                            showCompletionPreview(completion);
                        });
                    });
                }

                // Obter cor do badge baseado na fonte
                function getSourceBadgeColor(fonte) {
                    const colors = {
                        'ibge': 'success',
                        'viacep': 'primary',
                        'dadosgovbr': 'warning',
                        'apilib_sp': 'success',
                        'generico': 'danger',
                        'local': 'secondary'
                    };
                    return colors[fonte] || 'info';
                }

                // Atualizar função showSuggestions para incluir fonte
                function showSuggestions(camaras, fonte = null) {
                    currentSuggestions = camaras;
                    let html = '';
                    
                    camaras.forEach((camara, index) => {
                        const sourceBadge = fonte ? `<span class="badge badge-light-${getSourceBadgeColor(fonte)} fs-8 ms-2">${getSourceDisplayName(fonte)}</span>` : '';
                        
                        html += `
                            <div class="suggestion-item p-3 border-bottom cursor-pointer hover-bg-light" 
                                 data-index="${index}" 
                                 style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-bank fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div>
                                        <h6 class="mb-0 text-gray-800">${camara.nome_camara}</h6>
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted fs-7 me-2">${camara.cidade}/${camara.estado}</span>
                                            ${sourceBadge}
                                        </div>
                                        ${camara.cnpj ? `<span class="text-muted fs-7">CNPJ: ${camara.cnpj}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    suggestions.innerHTML = html;
                    suggestions.style.display = 'block';
                    
                    // Add click handlers
                    suggestions.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', () => {
                            const index = parseInt(item.dataset.index);
                            selectCamara(camaras[index]);
                        });
                    });
                }
                
                // Função para completar inteligentemente o nome da cidade
                function getSmartCompletion(cidade, query) {
                    const queryLower = query.toLowerCase().trim();
                    const cidadeLower = cidade.toLowerCase();
                    
                    // Se o query já está na cidade, retornar a cidade completa
                    if (cidadeLower.includes(queryLower)) {
                        return cidade;
                    }
                    
                    // Se não encontrar match, retornar o nome completo da câmara
                    return `Câmara Municipal de ${cidade}`;
                }
                
                // Event listeners
                input.addEventListener('input', (e) => {
                    const query = e.target.value.trim();
                    resetCompletionPreview();
                    
                    if (query.length >= 2) {
                        debouncedSearch(query);
                    } else {
                        hideSuggestions();
                        alert.classList.add('d-none');
                    }
                });
                
                // Adicionar listener para quando sair do campo
                input.addEventListener('blur', () => {
                    // Delay para permitir click nas sugestões
                    setTimeout(() => {
                        resetCompletionPreview();
                    }, 200);
                });
                
                // Hide suggestions when clicking outside
                document.addEventListener('click', (e) => {
                    if (!input.contains(e.target) && !suggestions.contains(e.target)) {
                        hideSuggestions();
                    }
                });
                
                // Keyboard navigation
                input.addEventListener('keydown', (e) => {
                    const items = suggestions.querySelectorAll('.suggestion-item');
                    const selected = suggestions.querySelector('.suggestion-item.active');
                    let newIndex = -1;
                    
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (selected) {
                            const currentIndex = Array.from(items).indexOf(selected);
                            newIndex = Math.min(currentIndex + 1, items.length - 1);
                        } else {
                            newIndex = 0;
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (selected) {
                            const currentIndex = Array.from(items).indexOf(selected);
                            newIndex = Math.max(currentIndex - 1, 0);
                        } else {
                            newIndex = items.length - 1;
                        }
                    } else if (e.key === 'Enter' && selected) {
                        e.preventDefault();
                        const index = Array.from(items).indexOf(selected);
                        selectCamara(currentSuggestions[index]);
                    } else if (e.key === 'Escape') {
                        hideSuggestions();
                    }
                    
                    if (newIndex >= 0 && items[newIndex]) {
                        items.forEach(item => item.classList.remove('active', 'bg-light'));
                        items[newIndex].classList.add('active', 'bg-light');
                    }
                });
                
                console.log('🔍 Sistema de busca automática inicializado');
                
                // Debug inicial - verificar se elementos existem
                console.log('🔧 Debug elementos:');
                console.log('- Input:', input ? 'OK' : 'ERRO');
                console.log('- Loading:', loading ? 'OK' : 'ERRO');
                console.log('- Suggestions:', suggestions ? 'OK' : 'ERRO');
                console.log('- Alert:', alert ? 'OK' : 'ERRO');
            }
            
            // Inicializar sistema de autocomplete do Presidente
            setTimeout(() => {
                initPresidenteAutocomplete();
            }, 500);
        });

        // Sistema de Autocomplete do Presidente da Câmara
        function initPresidenteAutocomplete() {
            const presidenteInput = document.getElementById('presidente_nome_input');
            const presidentePartidoInput = document.getElementById('presidente_partido_input');
            const presidenteIdInput = document.getElementById('presidente_id');
            const presidenteSuggestions = document.getElementById('presidente_suggestions');
            
            if (!presidenteInput || !presidenteSuggestions) {
                console.error('❌ Elementos do autocomplete do presidente não encontrados');
                return;
            }
            
            console.log('👤 Sistema de autocomplete do presidente inicializado');
            
            let searchTimeout;
            let currentParlamentares = [];
            let isSearching = false;
            
            // Debounced search function
            function debouncedSearchParlamentares(query) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (query.length >= 2) {
                        searchParlamentares(query);
                    } else {
                        hidePresidenteSuggestions();
                    }
                }, 300);
            }
            
            // Buscar parlamentares via API
            async function searchParlamentares(query) {
                if (isSearching) {
                    return;
                }
                
                try {
                    isSearching = true;
                    
                    const url = `/api/parlamentares/buscar?q=${encodeURIComponent(query)}`;
                    
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'Cache-Control': 'no-cache'
                        },
                        cache: 'no-store'
                    });
                    
                    if (!response.ok) {
                        throw new Error(`Erro HTTP ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.success && data.parlamentares && data.parlamentares.length > 0) {
                        showPresidenteSuggestions(data.parlamentares, query);
                    } else {
                        showNoPresidenteResults(query);
                    }
                    
                } catch (error) {
                    console.error('💥 Erro na busca de parlamentares:', error);
                    showPresidenteError('Erro ao buscar parlamentares. Tente novamente.');
                } finally {
                    isSearching = false;
                }
            }
            
            // Mostrar sugestões de parlamentares
            function showPresidenteSuggestions(parlamentares, query) {
                currentParlamentares = parlamentares;
                let html = '';
                
                parlamentares.forEach((parlamentar, index) => {
                    // Destacar texto que coincide com a busca
                    const highlightedName = highlightMatch(parlamentar.display_name, query);
                    const highlightedPartido = highlightMatch(parlamentar.partido_cargo, query);
                    
                    // Definir ícone baseado no status
                    const statusIcon = parlamentar.status === 'ativo' ? 'ki-check-circle' : 'ki-information-2';
                    const statusColor = parlamentar.status === 'ativo' ? 'text-success' : 'text-warning';
                    
                    html += `
                        <div class="suggestion-item p-3 border-bottom cursor-pointer hover-bg-light parlamentar-item" 
                             data-index="${index}" 
                             style="cursor: pointer; transition: all 0.2s;">
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ${statusIcon} fs-2 ${statusColor} me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-0 text-gray-800">${highlightedName}</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted fs-7 me-2">${highlightedPartido}</span>
                                                <span class="badge badge-light-${parlamentar.status === 'ativo' ? 'success' : 'warning'} fs-8">
                                                    ${parlamentar.status === 'ativo' ? 'Ativo' : 'Inativo'}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <i class="ki-duotone ki-arrow-right fs-6 text-muted">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                presidenteSuggestions.innerHTML = html;
                presidenteSuggestions.style.display = 'block';
                
                // Adicionar event listeners para clique
                presidenteSuggestions.querySelectorAll('.parlamentar-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const index = parseInt(item.dataset.index);
                        selectParlamentar(parlamentares[index]);
                    });
                });
            }
            
            // Mostrar quando nenhum parlamentar é encontrado
            function showNoPresidenteResults(query) {
                presidenteSuggestions.innerHTML = `
                    <div class="p-4 text-center">
                        <i class="ki-duotone ki-information-2 fs-2x text-muted mb-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <p class="text-muted mb-2">Nenhum parlamentar encontrado para "${query}"</p>
                        <div class="mt-3">
                            <a href="/parlamentares/create" class="btn btn-sm btn-light-primary" target="_blank">
                                <i class="ki-duotone ki-plus fs-6 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Cadastrar Novo Parlamentar
                            </a>
                        </div>
                        <small class="text-muted d-block mt-2">Você pode continuar digitando ou cadastrar um novo parlamentar</small>
                    </div>
                `;
                presidenteSuggestions.style.display = 'block';
            }
            
            // Mostrar erro
            function showPresidenteError(message) {
                presidenteSuggestions.innerHTML = `
                    <div class="p-4 text-center">
                        <i class="ki-duotone ki-cross-circle fs-2x text-danger mb-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <p class="text-danger">${message}</p>
                    </div>
                `;
                presidenteSuggestions.style.display = 'block';
            }
            
            // Esconder sugestões
            function hidePresidenteSuggestions() {
                presidenteSuggestions.style.display = 'none';
                currentParlamentares = [];
            }
            
            // Selecionar parlamentar
            function selectParlamentar(parlamentar) {
                presidenteInput.value = parlamentar.display_name;
                presidentePartidoInput.value = parlamentar.partido;
                
                if (presidenteIdInput) {
                    presidenteIdInput.value = parlamentar.id;
                }
                
                hidePresidenteSuggestions();
                
                // Mostrar feedback visual
                presidenteInput.style.borderColor = '#50cd89';
                presidentePartidoInput.style.borderColor = '#50cd89';
                
                setTimeout(() => {
                    presidenteInput.style.borderColor = '';
                    presidentePartidoInput.style.borderColor = '';
                }, 2000);
                
                console.log('✅ Parlamentar selecionado:', parlamentar.display_name, '-', parlamentar.partido);
            }
            
            // Função para destacar texto que coincide com a busca
            function highlightMatch(text, query) {
                if (!query.trim()) return text;
                
                const regex = new RegExp(`(${query.trim()})`, 'gi');
                return text.replace(regex, '<mark class="bg-warning text-dark">$1</mark>');
            }
            
            // Event listeners
            presidenteInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                
                // Limpar campo oculto quando usuário digita
                if (presidenteIdInput) {
                    presidenteIdInput.value = '';
                }
                
                // Limpar partido quando usuário altera o nome
                if (query !== presidentePartidoInput.dataset.lastSelectedName) {
                    presidentePartidoInput.value = '';
                }
                
                if (query.length >= 2) {
                    debouncedSearchParlamentares(query);
                } else {
                    hidePresidenteSuggestions();
                }
            });
            
            // Esconder sugestões quando clicar fora
            document.addEventListener('click', (e) => {
                if (!presidenteInput.contains(e.target) && !presidenteSuggestions.contains(e.target)) {
                    hidePresidenteSuggestions();
                }
            });
            
            // Navegação por teclado
            presidenteInput.addEventListener('keydown', (e) => {
                const items = presidenteSuggestions.querySelectorAll('.parlamentar-item');
                const selected = presidenteSuggestions.querySelector('.parlamentar-item.active');
                let newIndex = -1;
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (selected) {
                        const currentIndex = Array.from(items).indexOf(selected);
                        newIndex = Math.min(currentIndex + 1, items.length - 1);
                    } else {
                        newIndex = 0;
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (selected) {
                        const currentIndex = Array.from(items).indexOf(selected);
                        newIndex = Math.max(currentIndex - 1, 0);
                    } else {
                        newIndex = items.length - 1;
                    }
                } else if (e.key === 'Enter' && selected) {
                    e.preventDefault();
                    const index = Array.from(items).indexOf(selected);
                    selectParlamentar(currentParlamentares[index]);
                } else if (e.key === 'Escape') {
                    hidePresidenteSuggestions();
                }
                
                if (newIndex >= 0 && items[newIndex]) {
                    items.forEach(item => item.classList.remove('active', 'bg-light'));
                    items[newIndex].classList.add('active', 'bg-light');
                }
            });
            
            console.log('👤 Autocomplete do presidente configurado com sucesso');
        }

        // Função para verificar status das APIs
        async function checkApiStatus() {
            const btn = document.getElementById('check-api-status-btn');
            const originalText = btn.innerHTML;
            
            // Mostrar loading
            btn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Verificando...
            `;
            btn.disabled = true;
            
            try {
                const response = await fetch('/api/camaras/status', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Cache-Control': 'no-cache'
                    },
                    cache: 'no-store'
                });
                const data = await response.json();
                
                if (data.success && data.status_apis) {
                    showApiStatusModal(data.status_apis, data.timestamp);
                } else {
                    throw new Error('Erro ao obter status das APIs');
                }
            } catch (error) {
                console.error('Erro ao verificar status das APIs:', error);
                
                // Mostrar alerta de erro
                Swal.fire({
                    title: 'Erro',
                    text: 'Não foi possível verificar o status das APIs. Tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } finally {
                // Restaurar botão
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        // Mostrar modal com status das APIs
        function showApiStatusModal(statusApis, timestamp) {
            let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
            html += '<thead class="table-light"><tr><th>API</th><th>Status</th></tr></thead><tbody>';
            
            for (const [api, status] of Object.entries(statusApis)) {
                const isOnline = status.includes('✅');
                const badgeClass = isOnline ? 'badge-light-success' : 'badge-light-danger';
                const icon = isOnline ? 'ki-check-circle' : 'ki-close-circle';
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ${icon} fs-5 me-2 text-${isOnline ? 'success' : 'danger'}">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <strong>${api}</strong>
                            </div>
                        </td>
                        <td>
                            <span class="badge ${badgeClass}">${status}</span>
                        </td>
                    </tr>
                `;
            }
            
            html += '</tbody></table></div>';
            html += `<div class="text-muted fs-7 mt-3">
                <i class="ki-duotone ki-time fs-6 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Verificado em: ${new Date(timestamp).toLocaleString('pt-BR')}
            </div>`;
            
            Swal.fire({
                title: 'Status das APIs Externas',
                html: html,
                width: 600,
                showConfirmButton: true,
                confirmButtonText: 'Fechar',
                customClass: {
                    popup: 'swal2-api-status'
                }
            });
        }
    </script>
    
    <style>
        .suggestion-item:hover {
            background-color: #f8f9fa !important;
        }
        .suggestion-item.active {
            background-color: #e9ecef !important;
        }
        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
        }
        
        /* Autocomplete specific styles */
        .autocomplete-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .autocomplete-item:hover {
            background-color: #f0f8ff !important;
            border-left-color: #007bff;
            transform: translateX(2px);
        }
        
        /* Highlight matches */
        mark {
            padding: 1px 2px;
            border-radius: 2px;
            font-weight: 600;
        }
        
        /* Input preview effect */
        input[data-preview] {
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
            transition: all 0.2s ease;
        }
        
        /* Loading spinner in dropdown */
        #camara_suggestions .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Badge styling */
        .badge.fs-8 {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endpush