@extends('components.layouts.app')

@section('title', 'Configurar: ' . $modulo->nome)

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone {{ $modulo->icon ?: 'ki-setting-2' }} fs-2 me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Configurar {{ $modulo->nome }}
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
                    <li class="breadcrumb-item text-muted">{{ $modulo->nome }}</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
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

            <!--begin::Row-->
            <div class="row g-6 g-xl-9">
                @forelse($submodulos as $submodulo)
                    <!--begin::Col-->
                    <div class="col-12">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6">
                            <!--begin::Card header-->
                            <div class="card-header pt-8">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h3 class="fw-bold text-gray-900">{{ $submodulo->nome }}</h3>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-light-primary me-3">
                                            {{ ucfirst($submodulo->tipo) }}
                                        </span>
                                        <span class="badge badge-light-{{ $submodulo->ativo ? 'success' : 'danger' }}">
                                            {{ $submodulo->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body">
                                @if($submodulo->descricao)
                                    <div class="mb-6">
                                        <p class="text-gray-600 fs-6 mb-0">{{ $submodulo->descricao }}</p>
                                    </div>
                                @endif

                                <!--begin::Form-->
                                <form action="{{ route('parametros.salvar-configuracoes', $submodulo->id) }}" method="POST" class="submodule-form">
                                    @csrf
                                    
                                    @if(isset($submodulo->campos) && $submodulo->campos->count() > 0)
                                        <!--begin::Dynamic fields-->
                                        <div class="row">
                                            @foreach($submodulo->campos->sortBy('ordem') as $campo)
                                                @php
                                                    $valorAtual = isset($submodulo->valores[$campo->nome]) ? $submodulo->valores[$campo->nome] : $campo->valor_padrao;
                                                @endphp
                                                
                                                @if($campo->tipo_campo === 'text')
                                                    <div class="col-md-6">
                                                        <div class="fv-row mb-7">
                                                            <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                {{ $campo->label }}
                                                            </label>
                                                            <input type="text" 
                                                                class="form-control form-control-solid" 
                                                                name="{{ $campo->nome }}" 
                                                                value="{{ $valorAtual }}"
                                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                                @if($campo->obrigatorio) required @endif />
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($campo->tipo_campo === 'select')
                                                    <div class="col-md-6">
                                                        <div class="fv-row mb-7">
                                                            <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                {{ $campo->label }}
                                                            </label>
                                                            <select class="form-select form-select-solid" 
                                                                name="{{ $campo->nome }}" 
                                                                @if($campo->obrigatorio) required @endif>
                                                                <option value="">Selecione...</option>
                                                                @if($campo->opcoes)
                                                                    @foreach($campo->opcoes as $value => $label)
                                                                        <option value="{{ $value }}" @if($valorAtual == $value) selected @endif>
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($campo->tipo_campo === 'number')
                                                    <div class="col-md-6">
                                                        <div class="fv-row mb-7">
                                                            <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                {{ $campo->label }}
                                                            </label>
                                                            <input type="number" 
                                                                class="form-control form-control-solid" 
                                                                name="{{ $campo->nome }}" 
                                                                value="{{ $valorAtual }}"
                                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                                step="any"
                                                                @if($campo->obrigatorio) required @endif />
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($campo->tipo_campo === 'textarea')
                                                    <div class="col-md-12">
                                                        <div class="fv-row mb-7">
                                                            <label class="fs-6 fw-semibold form-label mb-2 @if($campo->obrigatorio) required @endif">
                                                                {{ $campo->label }}
                                                            </label>
                                                            <textarea class="form-control form-control-solid" 
                                                                name="{{ $campo->nome }}" 
                                                                rows="4" 
                                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                                @if($campo->obrigatorio) required @endif>{{ $valorAtual }}</textarea>
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($campo->tipo_campo === 'checkbox')
                                                    <div class="col-md-6">
                                                        <div class="form-check form-check-custom form-check-solid mb-7">
                                                            <input class="form-check-input" 
                                                                type="checkbox" 
                                                                name="{{ $campo->nome }}" 
                                                                id="{{ $campo->nome }}" 
                                                                value="1"
                                                                @if($valorAtual == '1' || $valorAtual === 'true') checked @endif />
                                                            <label class="form-check-label" for="{{ $campo->nome }}">
                                                                {{ $campo->label }}
                                                            </label>
                                                            @if($campo->help_text)
                                                                <div class="form-text">{{ $campo->help_text }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        {{-- Adicionar funcionalidade de teste para IA --}}
                                        @if($modulo->nome === 'Configuração de IA')
                                            <div class="separator my-10"></div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card bg-light-info">
                                                        <div class="card-body">
                                                            <h5 class="card-title">
                                                                <i class="ki-duotone ki-technology-1 fs-2 text-info me-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                Testar Conexão com IA
                                                            </h5>
                                                            <p class="card-text text-gray-700 mb-4">
                                                                Use o botão abaixo para testar se as configurações estão funcionando corretamente.
                                                            </p>
                                                            <button type="button" class="btn btn-info" id="btn-testar-ia">
                                                                <i class="ki-duotone ki-message-question fs-6 me-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                    <span class="path3"></span>
                                                                </i>
                                                                Testar Conexão
                                                            </button>
                                                            <div id="teste-resultado" class="mt-3" style="display: none;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!--begin::Actions-->
                                        <div class="d-flex justify-content-end pt-6">
                                            <button type="reset" class="btn btn-light me-3">
                                                Resetar
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <span class="indicator-label">Salvar Configurações</span>
                                                <span class="indicator-progress">Por favor aguarde...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                        <!--end::Actions-->
                                        <!--end::Dynamic fields-->
                                    @else
                                        <!--begin::Empty state-->
                                        <div class="d-flex flex-center flex-column py-10">
                                            <i class="ki-duotone ki-information-5 fs-3x text-primary mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <h3 class="text-gray-800 mb-2">Configuração em Desenvolvimento</h3>
                                            <p class="text-gray-600 fs-6 mb-0">
                                                Os campos de configuração para este submódulo ainda não foram implementados.
                                            </p>
                                        </div>
                                        <!--end::Empty state-->
                                    @endif
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                @empty
                    <!--begin::Empty state-->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body d-flex flex-center flex-column py-20">
                                <div class="text-center">
                                    <i class="ki-duotone ki-element-11 fs-4x text-primary mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <h3 class="text-gray-800 mb-2">Nenhum submódulo encontrado</h3>
                                    <p class="text-gray-600 fs-6 mb-6">
                                        Este módulo ainda não possui submódulos configurados.
                                    </p>
                                    <a href="{{ route('parametros.index') }}" class="btn btn-primary">
                                        <i class="ki-duotone ki-arrow-left fs-3"></i>
                                        Voltar aos Parâmetros
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Empty state-->
                @endforelse
            </div>
            <!--end::Row-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission handling
            const forms = document.querySelectorAll('.submodule-form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitButton = this.querySelector('button[type="submit"]');
                    
                    // Show loading state
                    submitButton.querySelector('.indicator-label').style.display = 'none';
                    submitButton.querySelector('.indicator-progress').style.display = 'inline-block';
                    submitButton.disabled = true;
                });
            });

            // Teste de conexão com IA
            const btnTestarIA = document.getElementById('btn-testar-ia');
            if (btnTestarIA) {
                btnTestarIA.addEventListener('click', function() {
                    testarConexaoIA();
                });
            }
        });

        // Modelos disponíveis por provedor
        const modelosPorProvedor = {
            'openai': {
                'gpt-3.5-turbo': 'GPT-3.5 Turbo (Rápido e econômico)',
                'gpt-4': 'GPT-4 (Mais preciso)',
                'gpt-4-turbo': 'GPT-4 Turbo (Balanceado)',
                'gpt-4o': 'GPT-4o (Mais recente)'
            },
            'anthropic': {
                'claude-3-haiku-20240307': 'Claude 3 Haiku (Rápido)',
                'claude-3-sonnet-20240229': 'Claude 3 Sonnet (Balanceado)',
                'claude-3-opus-20240229': 'Claude 3 Opus (Mais preciso)'
            },
            'google': {
                'gemini-1.5-flash': 'Gemini 1.5 Flash (Rápido)',
                'gemini-1.5-pro': 'Gemini 1.5 Pro (Balanceado)',
                'gemini-pro': 'Gemini Pro'
            },
            'local': {
                'llama2': 'Llama 2',
                'codellama': 'CodeLlama',
                'mistral': 'Mistral'
            }
        };

        // Função para atualizar modelos baseado no provedor
        function atualizarModelos() {
            const providerSelect = document.querySelector('select[name="ai_provider"]');
            const modelSelect = document.querySelector('select[name="ai_model"]');
            
            if (!providerSelect || !modelSelect) return;
            
            const providerSelecionado = providerSelect.value;
            const modelosDisponiveis = modelosPorProvedor[providerSelecionado] || {};
            
            // Limpar opções atuais
            modelSelect.innerHTML = '<option value="">Selecione um modelo...</option>';
            
            // Adicionar novos modelos
            Object.entries(modelosDisponiveis).forEach(([value, label]) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                modelSelect.appendChild(option);
            });
            
            // Se só há uma opção, selecionar automaticamente
            if (Object.keys(modelosDisponiveis).length === 1) {
                modelSelect.value = Object.keys(modelosDisponiveis)[0];
            }
        }

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            const providerSelect = document.querySelector('select[name="ai_provider"]');
            
            if (providerSelect) {
                // Atualizar modelos quando o provedor mudar
                providerSelect.addEventListener('change', atualizarModelos);
                
                // Atualizar modelos na inicialização
                atualizarModelos();
            }
        });

        function testarConexaoIA() {
            const btn = document.getElementById('btn-testar-ia');
            const resultado = document.getElementById('teste-resultado');
            
            // Coletar dados do formulário
            const formData = {};
            const form = btn.closest('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                if (input.name && input.name.startsWith('ai_')) {
                    formData[input.name] = input.value;
                }
            });
            
            // Validar campos obrigatórios
            if (!formData.ai_provider || !formData.ai_api_key || !formData.ai_model) {
                resultado.style.display = 'block';
                resultado.innerHTML = `
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Dados incompletos</h6>
                        <p class="mb-0">Preencha pelo menos Provider, API Key e Model antes de testar.</p>
                    </div>
                `;
                return;
            }
            
            // Mostrar loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testando...';
            resultado.style.display = 'none';
            
            // Fazer requisição AJAX
            fetch('/api/ai/testar-conexao', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                // Mostrar resultado
                resultado.style.display = 'block';
                
                if (data.success) {
                    resultado.innerHTML = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check me-2"></i>Conexão bem-sucedida!</h6>
                            <p class="mb-1"><strong>Provedor:</strong> ${data.provider || 'N/A'}</p>
                            <p class="mb-0"><strong>Modelo:</strong> ${data.model || 'N/A'}</p>
                        </div>
                    `;
                } else {
                    resultado.innerHTML = `
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-times me-2"></i>Erro na conexão</h6>
                            <p class="mb-0">${data.message || 'Erro desconhecido'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                resultado.style.display = 'block';
                resultado.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-times me-2"></i>Erro de conexão</h6>
                        <p class="mb-0">Não foi possível conectar com o servidor</p>
                    </div>
                `;
            })
            .finally(() => {
                // Restaurar botão
                btn.disabled = false;
                btn.innerHTML = `
                    <i class="ki-duotone ki-message-question fs-6 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Testar Conexão
                `;
            });
        }
    </script>
@endpush