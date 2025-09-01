@extends('layouts.app')

@section('title', 'Template Universal')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-design-1 fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Template Universal
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Administração</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Template Universal</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="row g-5 g-xl-10">
        <!-- Template Universal Card -->
        <div class="col-xl-8">
            <div class="card card-flush h-xl-100">
                <div class="card-header">
                    <div class="card-title">
                        <h2>{{ $template->nome }}</h2>
                    </div>
                    <div class="card-toolbar">
                        @if($template->is_default)
                            <span class="badge badge-light-success fw-bold fs-7 px-3 py-2">
                                <i class="ki-duotone ki-crown fs-8 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Template Padrão
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-8">
                        <div class="flex-grow-1">
                            <div class="mb-5">
                                <div class="fs-6 fw-semibold text-gray-700 mb-2">Descrição</div>
                                <div class="text-gray-500">
                                    {{ $template->descricao ?: 'Template universal que se adapta a qualquer tipo de proposição legislativa.' }}
                                </div>
                            </div>
                            
                            <div class="mb-5">
                                <div class="fs-6 fw-semibold text-gray-700 mb-2">Informações</div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ki-duotone ki-time fs-6 text-muted me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fs-7 text-muted">Última atualização:</span>
                                            <span class="fs-7 fw-bold ms-1">{{ $template->updated_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ki-duotone ki-user fs-6 text-muted me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fs-7 text-muted">Por:</span>
                                            <span class="fs-7 fw-bold ms-1">{{ $template->updatedBy->name ?? 'Sistema' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ki-duotone ki-document fs-6 text-muted me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fs-7 text-muted">Formato:</span>
                                            <span class="fs-7 fw-bold ms-1">{{ strtoupper($template->formato) }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ki-duotone ki-toggle-on fs-6 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fs-7 text-muted">Status:</span>
                                            <span class="fs-7 fw-bold text-success ms-1">Ativo</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="separator my-6"></div>

                    <div class="d-flex justify-content-center">
                        <a href="{{ route('admin.templates.universal.editor', $template) }}" 
                           class="btn btn-primary btn-lg me-3">
                            <i class="ki-duotone ki-pencil fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Editar Template Universal
                        </a>
                        <button type="button" class="btn btn-light-info btn-lg me-3" 
                                onclick="previewTemplate({{ $template->id }})">
                            <i class="ki-duotone ki-eye fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Preview
                        </button>
                        <a href="{{ route('api.templates.universal.download', $template) }}?v={{ $template->updated_at->timestamp }}" 
                           class="btn btn-light-success btn-lg">
                            <i class="ki-duotone ki-exit-down fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Download
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Painel de Aplicação por Tipo -->
        <div class="col-xl-4">
            <div class="card card-flush h-xl-100">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Aplicar por Tipo</h3>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div class="mb-5">
                        <div class="fs-7 text-muted mb-3">
                            Selecione um tipo de proposição para visualizar como o template universal se adapta:
                        </div>
                        
                        <div class="input-group mb-3">
                            <select class="form-select" id="tipo_proposicao_select">
                                <option value="">Selecione um tipo...</option>
                                @foreach($tiposProposicao as $tipo)
                                    <option value="{{ $tipo->id }}" data-nome="{{ $tipo->nome }}">{{ $tipo->nome }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary" onclick="aplicarTemplate()">
                                <i class="ki-duotone ki-rocket fs-6 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Aplicar
                            </button>
                        </div>
                    </div>

                    <div class="separator my-4"></div>

                    <div class="mb-5">
                        <h4 class="fs-6 fw-bold mb-3">Vantagens do Template Universal:</h4>
                        <div class="d-flex align-items-start mb-3">
                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2 mt-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div>
                                <div class="fs-7 fw-semibold">Um Único Template</div>
                                <div class="fs-8 text-muted">Gerenciar apenas um template para todos os tipos</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2 mt-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div>
                                <div class="fs-7 fw-semibold">Adaptação Automática</div>
                                <div class="fs-8 text-muted">Se ajusta ao tipo de proposição selecionado</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2 mt-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div>
                                <div class="fs-7 fw-semibold">Manutenção Simplificada</div>
                                <div class="fs-8 text-muted">Atualizações em um só local</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2 mt-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div>
                                <div class="fs-7 fw-semibold">Variáveis Dinâmicas</div>
                                <div class="fs-8 text-muted">Todas as variáveis do sistema disponíveis</div>
                            </div>
                        </div>
                    </div>

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-6">
                        <i class="ki-duotone ki-information-5 fs-2 text-info me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-700">Template Inteligente</div>
                                <div class="fs-7 text-muted">O template se adapta automaticamente com base no tipo de proposição escolhido na criação do documento</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal - Preview Template-->
<div class="modal fade" id="kt_modal_preview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Preview do Template Universal</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div id="preview_loading" class="text-center py-10">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Carregando...</span>
                    </div>
                    <div class="mt-3">Gerando preview do template...</div>
                </div>
                
                <div id="preview_content" style="display: none;">
                    <div class="card card-bordered">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ki-duotone ki-document fs-3 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Template Universal
                            </h3>
                        </div>
                        <div class="card-body">
                            <pre id="preview_text" class="text-gray-800 fs-6" style="white-space: pre-wrap; font-family: 'Times New Roman', serif; line-height: 1.6;"></pre>
                        </div>
                    </div>
                </div>
                
                <div id="preview_error" style="display: none;">
                    <div class="alert alert-danger">
                        <div class="alert-text" id="preview_error_message"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <a href="{{ route('admin.templates.universal.editor', $template) }}" class="btn btn-primary">
                    <i class="ki-duotone ki-pencil fs-6 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Editar Template
                </a>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Preview Template-->

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.prod.js"></script>
<script>
const { createApp } = Vue;

// Aplicação Vue para preview do template
const previewApp = createApp({
    data() {
        return {
            isLoading: false,
            hasError: false,
            errorMessage: '',
            templateData: null,
            modal: null
        }
    },
    methods: {
        async loadTemplate(templateId) {
            this.isLoading = true;
            this.hasError = false;
            this.templateData = null;
            
            // Exibir modal
            if (!this.modal) {
                this.modal = new bootstrap.Modal(document.getElementById('kt_modal_preview'));
            }
            this.modal.show();
            
            try {
                const response = await fetch(`/api/templates/universal/${templateId}/preview`);
                const result = await response.json();
                
                if (result.success) {
                    this.templateData = result.data;
                } else {
                    throw new Error(result.message || 'Erro ao carregar template');
                }
            } catch (error) {
                this.hasError = true;
                this.errorMessage = error.message || 'Erro ao carregar o preview do template';
                console.error('Erro ao carregar template:', error);
            } finally {
                this.isLoading = false;
            }
        },
        formatContent(content) {
            if (!content) return 'Sem conteúdo disponível';
            
            // Destacar variáveis em uma cor diferente
            return content.replace(/\$\{([^}]+)\}/g, '<span class="text-primary fw-bold">${$1}</span>');
        },
        formatVariables(variables) {
            if (!variables || variables.length === 0) {
                return 'Nenhuma variável disponível';
            }
            return variables.map(v => `<span class="badge badge-light-primary me-1">\${${v}}</span>`).join(' ');
        }
    },
    mounted() {
        // Expor método para ser chamado externamente
        window.previewTemplate = (templateId) => {
            this.loadTemplate(templateId);
        };
    },
    template: `
        <div>
            <div v-if="isLoading" id="preview_loading" class="text-center py-10">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Carregando...</span>
                </div>
                <div class="mt-3">Carregando preview do template...</div>
            </div>
            
            <div v-else-if="hasError" id="preview_error">
                <div class="alert alert-danger">
                    <div class="alert-text">@{{ errorMessage }}</div>
                </div>
            </div>
            
            <div v-else-if="templateData" id="preview_content">
                <div class="card card-bordered">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ki-duotone ki-document fs-3 text-primary me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            @{{ templateData.nome }}
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-info">@{{ (templateData.formato || 'rtf').toUpperCase() }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="fw-bold text-muted mb-2">Variáveis Disponíveis:</h6>
                            <div v-html="formatVariables(templateData.variaveis)"></div>
                        </div>
                        
                        <div class="separator my-4"></div>
                        
                        <h6 class="fw-bold text-muted mb-3">Conteúdo do Template:</h6>
                        <div class="bg-light rounded p-4">
                            <pre class="text-gray-800 fs-6 mb-0" 
                                 style="white-space: pre-wrap; font-family: 'Courier New', monospace; line-height: 1.8;"
                                 v-html="formatContent(templateData.conteudo)"></pre>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="ki-duotone ki-time fs-7 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Última atualização: @{{ templateData.updated_at }} por @{{ templateData.updated_by }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
});

// Montar a aplicação no modal
document.addEventListener('DOMContentLoaded', () => {
    const modalBody = document.querySelector('#kt_modal_preview .modal-body');
    if (modalBody) {
        // Limpar conteúdo estático
        modalBody.innerHTML = '<div id="preview-app"></div>';
        // Montar Vue app
        previewApp.mount('#preview-app');
    }
});

// Manter a função original para compatibilidade
function previewTemplate(templateId) {
    // Será substituída pelo Vue quando montar
    console.log('Carregando preview do template:', templateId);
}

// Aplicar template a um tipo específico
function aplicarTemplate() {
    const selectTipo = document.getElementById('tipo_proposicao_select');
    const tipoId = selectTipo.value;
    const tipoNome = selectTipo.options[selectTipo.selectedIndex].dataset.nome;
    
    if (!tipoId) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecione um Tipo',
            text: 'Por favor, selecione um tipo de proposição.'
        });
        return;
    }
    
    Swal.fire({
        title: 'Template Aplicado!',
        html: `
            <div class="text-center mb-4">
                <i class="ki-duotone ki-rocket fs-3x text-success mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <p class="fw-bold fs-5">Template universal adaptado para:</p>
                <p class="fs-4 text-primary">${tipoNome}</p>
            </div>
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-information-5 fs-2 text-info me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div>
                        <div class="fw-semibold">Como funciona:</div>
                        <div class="fs-7 text-muted">Quando um usuário criar uma proposição do tipo "${tipoNome}", o sistema aplicará automaticamente este template universal com as adaptações necessárias.</div>
                    </div>
                </div>
            </div>
        `,
        icon: 'success',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#17c653'
    });
}
</script>
@endpush