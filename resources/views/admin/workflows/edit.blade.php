@extends('components.layouts.app')

@section('title', 'Editar Workflow: ' . $workflow->nome)

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-pencil fs-2 text-primary me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Editar Workflow
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.workflows.index') }}" class="text-muted text-hover-primary">Workflows</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.workflows.show', $workflow) }}" class="text-muted text-hover-primary">{{ $workflow->nome }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Editar</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.workflows.show', $workflow) }}" class="btn btn-sm fw-bold btn-light-info">
                    <i class="ki-duotone ki-eye fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Visualizar
                </a>
                <a href="{{ route('admin.workflows.index') }}" class="btn btn-sm fw-bold btn-light">
                    <i class="ki-duotone ki-black-left fs-2"></i>
                    Voltar
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!--begin::Status e Informações Rápidas-->
                    <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                        <!--begin::Col-->
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <span class="symbol-label bg-light-{{ $workflow->ativo ? 'success' : 'secondary' }}">
                                            <i class="ki-duotone ki-abstract-25 fs-2 text-{{ $workflow->ativo ? 'success' : 'secondary' }}">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-semibold d-block fs-7">Status</span>
                                        <span class="text-gray-800 fw-bold fs-6">
                                            @if($workflow->ativo)
                                                <span class="badge badge-light-success">Ativo</span>
                                            @else
                                                <span class="badge badge-light-secondary">Inativo</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-element-11 fs-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-semibold d-block fs-7">Etapas</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ $workflow->etapas->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <span class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-arrows-loop fs-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-semibold d-block fs-7">Transições</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ $workflow->transicoes->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-50px me-3">
                                        <span class="symbol-label bg-light-{{ $workflow->is_default ? 'warning' : 'secondary' }}">
                                            <i class="ki-duotone ki-star fs-2 text-{{ $workflow->is_default ? 'warning' : 'secondary' }}">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-semibold d-block fs-7">Padrão</span>
                                        <span class="text-gray-800 fw-bold fs-6">
                                            @if($workflow->is_default)
                                                <span class="badge badge-light-warning">Sim</span>
                                            @else
                                                <span class="text-muted">Não</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Status e Informações Rápidas-->

                    <!--begin::Card Formulário-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Editar Informações</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-gray-900">Erros de validação</h4>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-gray-900">Sucesso!</h4>
                                        <span>{{ session('success') }}</span>
                                    </div>
                                </div>
                            @endif

                    <form method="POST" action="{{ route('admin.workflows.update', $workflow) }}">
                        @csrf
                        @method('PUT')
                        
                            <div class="row">
                                <!--begin::Col Nome-->
                                <div class="col-md-8">
                                    <div class="mb-10">
                                        <label for="nome" class="form-label fw-semibold text-gray-600">Nome do Workflow <span class="text-danger">*</span></label>
                                        <input type="text" name="nome" id="nome" 
                                               class="form-control form-control-solid @error('nome') is-invalid @enderror"
                                               value="{{ old('nome', $workflow->nome) }}" required>
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Nome único e descritivo do workflow</div>
                                    </div>
                                </div>
                                <!--end::Col Nome-->

                                <!--begin::Col Ordem-->
                                <div class="col-md-4">
                                    <div class="mb-10">
                                        <label for="ordem" class="form-label fw-semibold text-gray-600">Ordem de Exibição</label>
                                        <input type="number" name="ordem" id="ordem" 
                                               class="form-control form-control-solid @error('ordem') is-invalid @enderror"
                                               value="{{ old('ordem', $workflow->ordem) }}" min="1" max="999">
                                        @error('ordem')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Prioridade de apresentação</div>
                                    </div>
                                </div>
                                <!--end::Col Ordem-->
                            </div>

                            <!--begin::Tipo de Documento-->
                            <div class="mb-10">
                                <label for="tipo_documento" class="form-label fw-semibold text-gray-600">Tipo de Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" id="tipo_documento" 
                                        class="form-select form-select-solid @error('tipo_documento') is-invalid @enderror" required>
                                    <option value="">Selecione o tipo de documento</option>
                                    @php
                                        $currentValue = old('tipo_documento', $workflow->tipo_documento);
                                        $tipos = [
                                            'proposicao' => 'Proposição',
                                            'requerimento' => 'Requerimento', 
                                            'projeto_lei' => 'Projeto de Lei',
                                            'emenda' => 'Emenda',
                                            'mocao' => 'Moção',
                                            'indicacao' => 'Indicação',
                                            'pedido_informacao' => 'Pedido de Informação',
                                            'outro' => 'Outro'
                                        ];
                                    @endphp
                                    @foreach($tipos as $value => $label)
                                        <option value="{{ $value }}" {{ $currentValue === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($workflow->documentos_em_uso > 0)
                                    <div class="alert alert-warning d-flex align-items-center p-3 mt-3">
                                        <i class="ki-duotone ki-shield-tick fs-2hx text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1">Atenção</h6>
                                            <span class="fs-7">Este workflow está em uso por {{ $workflow->documentos_em_uso }} documento(s). Alterar o tipo pode afetar documentos existentes.</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!--end::Tipo de Documento-->

                            <!--begin::Descrição-->
                            <div class="mb-10">
                                <label for="descricao" class="form-label fw-semibold text-gray-600">Descrição</label>
                                <textarea name="descricao" id="descricao" rows="3"
                                          class="form-control form-control-solid @error('descricao') is-invalid @enderror"
                                          placeholder="Descreva o propósito e funcionamento deste workflow...">{{ old('descricao', $workflow->descricao) }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Descrição-->

                            <!--begin::Configurações-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-10">
                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', $workflow->ativo) ? 'checked' : '' }}>
                                            <span class="form-check-label fw-semibold text-gray-700">
                                                Workflow Ativo
                                            </span>
                                        </label>
                                        <div class="form-text">Workflow pode ser usado por documentos</div>
                                        @if($workflow->documentos_em_uso > 0 && $workflow->ativo)
                                            <div class="alert alert-info d-flex align-items-center p-3 mt-3">
                                                <i class="ki-duotone ki-information-5 fs-2hx text-info me-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-1">Informação</h6>
                                                    <span class="fs-7">Desativar afetará {{ $workflow->documentos_em_uso }} documento(s) em tramitação.</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-10">
                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $workflow->is_default) ? 'checked' : '' }}>
                                            <span class="form-check-label fw-semibold text-gray-700">
                                                Workflow Padrão
                                            </span>
                                        </label>
                                        <div class="form-text">Usado automaticamente para novos documentos deste tipo</div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Configurações-->

                            <!--begin::Configuração JSON-->
                            <div class="mb-10">
                                <label for="configuracao" class="form-label fw-semibold text-gray-600">
                                    Configuração Avançada (JSON)
                                    <i class="ki-duotone ki-information-5 fs-6 text-muted ms-1" title="Configurações avançadas em formato JSON">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </label>
                                <textarea name="configuracao" id="configuracao" rows="4"
                                          class="form-control form-control-solid @error('configuracao') is-invalid @enderror"
                                          placeholder='{"timeout": 86400, "notificacoes": true, "auto_aprovacao": false}'>{{ old('configuracao', $workflow->configuracao ? json_encode($workflow->configuracao, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}') }}</textarea>
                                @error('configuracao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Configurações em JSON. Exemplo: timeout em segundos, habilitação de notificações, etc.
                                </div>
                            </div>
                            <!--end::Configuração JSON-->

                            <!--begin::Metadados do Sistema-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-10">
                                        <label class="form-label fw-semibold text-muted">Criado em</label>
                                        <div class="form-control form-control-plaintext form-control-solid">
                                            {{ $workflow->created_at->format('d/m/Y H:i') }}
                                            <span class="text-muted fs-7 ms-2">({{ $workflow->created_at->diffForHumans() }})</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-10">
                                        <label class="form-label fw-semibold text-muted">Atualizado em</label>
                                        <div class="form-control form-control-plaintext form-control-solid">
                                            {{ $workflow->updated_at->format('d/m/Y H:i') }}
                                            <span class="text-muted fs-7 ms-2">({{ $workflow->updated_at->diffForHumans() }})</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Metadados do Sistema-->

                            <!--begin::Actions-->
                            <div class="d-flex flex-stack">
                                <div>
                                    <a href="{{ route('admin.workflows.show', $workflow) }}" class="btn btn-light">
                                        <i class="ki-duotone ki-cross fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Cancelar
                                    </a>
                                </div>
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ki-duotone ki-check fs-3"></i>
                                        Atualizar Workflow
                                    </button>
                                    <a href="{{ route('admin.workflows.designer.edit', $workflow) }}" class="btn btn-light-success">
                                        <i class="ki-duotone ki-design-1 fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Abrir Designer
                                    </a>
                                </div>
                            </div>
                            <!--end::Actions-->
                        </form>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card Formulário-->

                    <!--begin::Ações Avançadas-->
                    @canany(['duplicate', 'delete'], $workflow)
                        <div class="card">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="fw-bold text-muted">
                                        <i class="ki-duotone ki-setting-2 fs-2 text-muted me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Ações Avançadas
                                    </h3>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body">
                                <div class="row g-9">
                                    @can('duplicate', $workflow)
                                        <div class="col-md-6">
                                            <div class="d-flex flex-column h-100">
                                                <h4 class="fw-bold mb-3">Duplicar Workflow</h4>
                                                <div class="text-gray-600 fs-6 mb-5 flex-grow-1">
                                                    Cria uma cópia completa incluindo etapas e transições.
                                                </div>
                                                <button type="button" class="btn btn-light-info align-self-start" 
                                                        onclick="duplicateWorkflow({{ $workflow->id }}, '{{ $workflow->nome }}')">
                                                    <i class="ki-duotone ki-copy fs-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Duplicar
                                                </button>
                                            </div>
                                        </div>
                                    @endcan
                                    
                                    @can('delete', $workflow)
                                        <div class="col-md-6">
                                            <div class="d-flex flex-column h-100">
                                                <h4 class="fw-bold mb-3 text-danger">Remover Workflow</h4>
                                                <div class="text-gray-600 fs-6 mb-5 flex-grow-1">
                                                    Remove permanentemente o workflow e todo seu histórico.
                                                </div>
                                                @if($workflow->documentos_em_uso > 0)
                                                    <div class="alert alert-warning d-flex align-items-center p-3 mb-5">
                                                        <i class="ki-duotone ki-shield-tick fs-2hx text-warning me-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        <div class="d-flex flex-column">
                                                            <h6 class="mb-1">Bloqueado</h6>
                                                            <span class="fs-7">Não é possível remover: {{ $workflow->documentos_em_uso }} documento(s) em uso.</span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-light-danger align-self-start" disabled>
                                                        <i class="ki-duotone ki-trash fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                            <span class="path5"></span>
                                                        </i>
                                                        Remover
                                                    </button>
                                                @else
                                                    <form method="POST" action="{{ route('admin.workflows.destroy', $workflow) }}" class="align-self-start"
                                                          onsubmit="return confirm('⚠️ ATENÇÃO: Esta ação não pode ser desfeita!\n\nTem certeza que deseja remover permanentemente o workflow \"{{ $workflow->nome }}\"?\n\nTodos os dados relacionados (etapas, transições, histórico) serão perdidos.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-light-danger">
                                                            <i class="ki-duotone ki-trash fs-3">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                            </i>
                                                            Remover
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card Ações Avançadas-->
                    @endcanany
                </div>
            </div>
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!--begin::Modal para duplicar workflow-->
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="duplicateForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">Duplicar Workflow</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-7">
                        <label class="form-label fw-semibold text-gray-600">Nome do Novo Workflow</label>
                        <input type="text" name="nome" class="form-control form-control-solid" required>
                        <div class="form-text">
                            O novo workflow será criado como inativo e não padrão.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ki-duotone ki-copy fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Duplicar Workflow
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal para duplicar workflow-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação JSON em tempo real
    const configTextarea = document.getElementById('configuracao');
    
    configTextarea.addEventListener('blur', function() {
        const value = this.value.trim();
        if (value && value !== '{}') {
            try {
                JSON.parse(value);
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } catch (e) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        } else {
            this.classList.remove('is-invalid', 'is-valid');
        }
    });

    // Verificação de workflow padrão
    const tipoDocumento = document.getElementById('tipo_documento');
    const isDefault = document.getElementById('is_default');
    
    isDefault.addEventListener('change', function() {
        if (this.checked && !tipoDocumento.value) {
            alert('Selecione um tipo de documento antes de definir como padrão.');
            this.checked = false;
        }
    });
});

function duplicateWorkflow(workflowId, currentName) {
    const modal = new bootstrap.Modal(document.getElementById('duplicateModal'));
    const form = document.getElementById('duplicateForm');
    const nameInput = form.querySelector('input[name="nome"]');
    
    // Configurar action do form
    form.action = `/admin/workflows/${workflowId}/duplicate`;
    
    // Sugerir nome baseado no atual
    nameInput.value = `${currentName} (Cópia)`;
    nameInput.select();
    
    modal.show();
}
</script>
@endpush