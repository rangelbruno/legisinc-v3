@extends('components.layouts.app')

@section('title', 'Modelos de Documentos')

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Modelos de Documentos
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Documentos</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Modelos</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('documentos.modelos.create') }}" class="btn btn-sm fw-bold btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Novo Modelo (Upload)
                </a>
                <a href="{{ route('documentos.modelos.create-onlyoffice') }}" class="btn btn-sm fw-bold btn-success">
                    <i class="ki-duotone ki-document-edit fs-2"></i>
                    Criar documento
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Sucesso!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif


            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Erro!</h4>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-docs-table-filter="search" 
                                   class="form-control form-control-solid w-250px ps-13" 
                                   placeholder="Buscar modelos..." />
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" 
                                    data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-filter fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Filtros
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-gray-900 fw-bold">Opções de Filtro</div>
                                </div>
                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5" data-kt-docs-table-filter="form">
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold">Status:</label>
                                        <select class="form-select form-select-solid fw-bold" data-kt-select2="true" 
                                                data-placeholder="Selecione" data-allow-clear="true" 
                                                data-kt-docs-table-filter="status">
                                            <option></option>
                                            <option value="ativo">Ativo</option>
                                            <option value="inativo">Inativo</option>
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" 
                                                data-kt-menu-dismiss="true" data-kt-docs-table-filter="reset">Limpar</button>
                                        <button type="submit" class="btn btn-primary fw-semibold px-6" 
                                                data-kt-menu-dismiss="true" data-kt-docs-table-filter="filter">Aplicar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body py-4">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_docs_table">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-200px">Nome</th>
                                    <th class="min-w-150px">Tipo Proposição</th>
                                    <th class="min-w-100px">Versão</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Criado em</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse($modelos as $modelo)
                                    <tr data-modelo-id="{{ $modelo->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                        <i class="ki-duotone ki-document fs-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="{{ route('documentos.modelos.show', $modelo) }}" 
                                                       class="text-gray-900 fw-bold text-hover-primary fs-6">{{ $modelo->nome }}</a>
                                                    @if($modelo->descricao)
                                                        <span class="text-muted fw-semibold text-muted d-block fs-7">{{ Str::limit($modelo->descricao, 50) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($modelo->tipoProposicao)
                                                <span class="badge badge-primary">{{ $modelo->tipoProposicao->nome }}</span>
                                            @else
                                                <span class="text-muted">Geral</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bold">{{ $modelo->versao }}</span>
                                        </td>
                                        <td>
                                            @if($modelo->ativo)
                                                <span class="badge badge-success">Ativo</span>
                                            @else
                                                <span class="badge badge-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>{{ $modelo->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" 
                                               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                Ações
                                                <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" 
                                                 data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.show', $modelo) }}" class="menu-link px-3">Visualizar</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.download', $modelo) }}?v={{ $modelo->updated_at->timestamp }}" class="menu-link px-3">Download</a>
                                                </div>
                                                @if($modelo->document_key)
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('onlyoffice.standalone.editor.modelo', $modelo) }}" 
                                                       class="menu-link px-3" 
                                                       target="_blank">
                                                       <i class="fas fa-external-link-alt me-2"></i>Editar documento
                                                    </a>
                                                </div>
                                                @endif
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.edit', $modelo) }}" class="menu-link px-3">Editar</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger delete-modelo" 
                                                       data-modelo-id="{{ $modelo->id }}" 
                                                       data-modelo-nome="{{ $modelo->nome }}">Excluir</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ki-duotone ki-file-deleted fs-3x text-muted mb-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="text-muted fw-bold fs-6">Nenhum modelo encontrado</div>
                                                <div class="text-muted fs-7">Clique em "Novo Modelo" para começar</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->

                    @if($modelos->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-10">
                            <div class="text-muted">
                                Mostrando {{ $modelos->firstItem() }} até {{ $modelos->lastItem() }} de {{ $modelos->total() }} resultados
                            </div>
                            {{ $modelos->links() }}
                        </div>
                    @endif
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modal_confirmar_exclusao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="modal_confirmar_exclusao_header">
                <h2 class="fw-bold">Confirmar Exclusão</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="fw-semibold fs-6 text-gray-600 mb-5">
                    Tem certeza que deseja excluir o modelo "<span id="nome_modelo_exclusao"></span>"?
                </div>
                <div class="text-danger fs-7">
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <form id="form_exclusao" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar exclusão de modelos
    document.querySelectorAll('.delete-modelo').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const modeloId = this.dataset.modeloId;
            const modeloNome = this.dataset.modeloNome;
            
            document.getElementById('nome_modelo_exclusao').textContent = modeloNome;
            document.getElementById('form_exclusao').action = `/admin/documentos/modelos/${modeloId}`;
            
            const modal = new bootstrap.Modal(document.getElementById('modal_confirmar_exclusao'));
            modal.show();
        });
    });

    // Filtro de busca simples
    const searchInput = document.querySelector('[data-kt-docs-table-filter="search"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#kt_docs_table tbody tr');
            
            tableRows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Criar indicador visual para debug
    const debugIndicator = document.createElement('div');
    debugIndicator.id = 'onlyoffice-debug';
    debugIndicator.style.cssText = 'position: fixed; top: 10px; left: 10px; background: #007bff; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; z-index: 9999; display: none;';
    document.body.appendChild(debugIndicator);
    
    function showDebugMessage(message) {
        debugIndicator.textContent = message;
        debugIndicator.style.display = 'block';
        setTimeout(() => {
            debugIndicator.style.display = 'none';
        }, 3000);
    }

    // Listener para detectar eventos do editor OnlyOffice
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type && !event.data.source && !event.data.type.includes('content-script')) {
            console.log('📨 Evento OnlyOffice:', event.data.type);
            showDebugMessage('Evento: ' + event.data.type);
            
            switch(event.data.type) {
                case 'onlyoffice_editor_closed':
                    console.log('Editor OnlyOffice foi fechado, atualizando página...');
                    showDebugMessage('Editor fechado - Atualizando...');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                    break;
                case 'onlyoffice_version_changed':
                    console.log('Versão do documento alterada, preparando para atualizar...');
                    showDebugMessage('Versão alterada - Aguardando...');
                    // Aguardar mais tempo para mudanças de versão
                    setTimeout(function() {
                        location.reload();
                    }, 5000); // Aumentado para 5 segundos
                    break;
                case 'onlyoffice_document_saved':
                    console.log('Documento salvo no OnlyOffice, atualizando página imediatamente...');
                    showDebugMessage('Documento salvo - Atualizando!');
                    // Documento foi realmente salvo, atualizar imediatamente
                    setTimeout(function() {
                        location.reload();
                    }, 1000); // Aumentado para 1 segundo
                    break;
                case 'onlyoffice_document_ready':
                    console.log('Editor OnlyOffice está pronto para edição...');
                    showDebugMessage('Editor pronto!');
                    break;
                case 'onlyoffice_document_updated':
                    console.log('Documento atualizado no OnlyOffice...');
                    showDebugMessage('Documento atualizado');
                    // Atualização menor, aguardar menos tempo
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                    break;
            }
        }
    });

    // Sistema de detecção automática de mudanças no modelo via polling (simplificado)
    let lastKnownData = new Map();
    let isCheckingUpdates = false;
    
    // Função para verificar se algum modelo foi atualizado
    function checkModelUpdates() {
        if (isCheckingUpdates) return;
        isCheckingUpdates = true;
        
        const modelRows = document.querySelectorAll('[data-modelo-id]');
        if (modelRows.length === 0) {
            isCheckingUpdates = false;
            return;
        }
        
        // Verificar apenas o primeiro modelo
        const modelId = modelRows[0].getAttribute('data-modelo-id');
        const baseUrl = window.location.origin + '/admin/documentos/modelos';
        const url = `${baseUrl}/${modelId}/last-update`;
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject('HTTP ' + response.status))
        .then(data => {
            const currentData = {
                timestamp: data.timestamp,
                size: data.arquivo_size,
                name: data.arquivo_nome
            };
            const lastData = lastKnownData.get(modelId);
            
            if (!lastData) {
                lastKnownData.set(modelId, currentData);
            } else {
                // Check for changes
                const changed = currentData.timestamp !== lastData.timestamp || 
                               currentData.size !== lastData.size ||
                               currentData.name !== lastData.name;
                
                if (changed) {
                    console.log('🚀 Model', modelId, 'changed! Reloading page...');
                    showDebugMessage('Model ' + modelId + ' changed!');
                    clearInterval(modelCheckInterval);
                    setTimeout(() => location.reload(), 1000);
                    return;
                }
            }
            isCheckingUpdates = false;
        })
        .catch(error => {
            console.log('Error checking model', modelId + ':', error);
            isCheckingUpdates = false;
        });
    }

    // Check for model updates every 3 seconds
    let modelCheckInterval = setInterval(checkModelUpdates, 3000);
    
    // Stop checking when leaving the page
    window.addEventListener('beforeunload', () => {
        if (modelCheckInterval) clearInterval(modelCheckInterval);
    });

    // Fallback: verificar localStorage periodicamente
    let lastChecks = {
        closed: localStorage.getItem('onlyoffice_editor_closed'),
        versionChanged: localStorage.getItem('onlyoffice_version_changed'),
        updated: localStorage.getItem('onlyoffice_document_updated'),
        saved: localStorage.getItem('onlyoffice_document_saved')
    };
    
    setInterval(function() {
        // Verificar fechamento do editor
        const currentClosed = localStorage.getItem('onlyoffice_editor_closed');
        if (currentClosed && currentClosed !== lastChecks.closed) {
            const timestamp = parseInt(currentClosed);
            if (Date.now() - timestamp < 5000) {
                console.log('Editor OnlyOffice foi fechado (localStorage), atualizando página...');
                lastChecks.closed = currentClosed;
                setTimeout(() => location.reload(), 1000);
                return;
            }
        }
        
        // Verificar mudança de versão
        const currentVersion = localStorage.getItem('onlyoffice_version_changed');
        if (currentVersion && currentVersion !== lastChecks.versionChanged) {
            const timestamp = parseInt(currentVersion);
            if (Date.now() - timestamp < 10000) { // 10 segundos para versão
                console.log('Versão alterada (localStorage), atualizando página...');
                lastChecks.versionChanged = currentVersion;
                setTimeout(() => location.reload(), 2000);
                return;
            }
        }
        
        // Verificar salvamento de documento (prioridade alta)
        const currentSaved = localStorage.getItem('onlyoffice_document_saved');
        if (currentSaved && currentSaved !== lastChecks.saved) {
            const timestamp = parseInt(currentSaved);
            if (Date.now() - timestamp < 5000) { // 5 segundos para save
                console.log('Documento salvo (localStorage), atualizando página imediatamente...');
                lastChecks.saved = currentSaved;
                setTimeout(() => location.reload(), 500);
                return;
            }
        }
        
        // Verificar atualização de documento
        const currentUpdate = localStorage.getItem('onlyoffice_document_updated');
        if (currentUpdate && currentUpdate !== lastChecks.updated) {
            const timestamp = parseInt(currentUpdate);
            if (Date.now() - timestamp < 8000) { // 8 segundos para update
                console.log('Documento atualizado (localStorage), atualizando página...');
                lastChecks.updated = currentUpdate;
                setTimeout(() => location.reload(), 1500);
                return;
            }
        }
    }, 1000);
});
</script>
@endpush