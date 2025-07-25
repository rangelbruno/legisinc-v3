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
            

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <form method="GET" action="{{ route('documentos.modelos.index') }}" class="d-flex align-items-center gap-3">
                            <!-- Busca por texto -->
                            <div class="position-relative">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5 mt-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="form-control form-control-solid w-250px ps-13" 
                                       placeholder="Buscar modelos..." />
                            </div>
                            
                            <!-- Filtro por categoria -->
                            <select name="categoria" class="form-select form-select-solid w-200px">
                                <option value="">Todas as categorias</option>
                                @foreach($categorias as $key => $label)
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Filtro por tipo de proposição -->
                            <select name="tipo_proposicao_id" class="form-select form-select-solid w-200px">
                                <option value="">Todos os tipos</option>
                                @foreach($tiposProposicao as $tipo)
                                    <option value="{{ $tipo->id }}" {{ request('tipo_proposicao_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Filtro template/personalizado -->
                            <select name="is_template" class="form-select form-select-solid w-150px">
                                <option value="">Todos</option>
                                <option value="true" {{ request('is_template') === 'true' ? 'selected' : '' }}>Templates</option>
                                <option value="false" {{ request('is_template') === 'false' ? 'selected' : '' }}>Personalizados</option>
                            </select>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-duotone ki-filter fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Filtrar
                            </button>
                            
                            @if(request()->hasAny(['search', 'categoria', 'tipo_proposicao_id', 'is_template']))
                                <a href="{{ route('documentos.modelos.index') }}" class="btn btn-light">
                                    <i class="ki-duotone ki-cross-circle fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Limpar
                                </a>
                            @endif
                        </form>
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
                                    <th class="min-w-250px">Nome</th>
                                    <th class="min-w-120px">Categoria</th>
                                    <th class="min-w-150px">Tipo Proposição</th>
                                    <th class="min-w-100px">Tipo</th>
                                    <th class="min-w-80px">Status</th>
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
                                                    <span class="symbol-label {{ $modelo->is_template ? 'bg-light-warning text-warning' : 'bg-light-primary text-primary' }} fw-bold">
                                                        <i class="ki-duotone {{ $modelo->icon ?? 'ki-document' }} fs-2">
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
                                                    @if($modelo->template_id)
                                                        <span class="badge badge-light-info mt-1">{{ $modelo->template_id }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($modelo->categoria)
                                                @php
                                                    $categoriaCores = [
                                                        'legislativo' => 'primary',
                                                        'administrativo' => 'info',
                                                        'juridico' => 'warning',
                                                        'financeiro' => 'success',
                                                        'geral' => 'secondary'
                                                    ];
                                                    $cor = $categoriaCores[$modelo->categoria] ?? 'secondary';
                                                @endphp
                                                <span class="badge badge-light-{{ $cor }}">
                                                    {{ $categorias[$modelo->categoria] ?? $modelo->categoria }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($modelo->tipoProposicao)
                                                <span class="badge badge-primary">{{ $modelo->tipoProposicao->nome }}</span>
                                            @else
                                                <span class="text-muted">Geral</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($modelo->is_template)
                                                <span class="badge badge-warning">Template</span>
                                            @else
                                                <span class="badge badge-info">Personalizado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($modelo->ativo)
                                                <span class="badge badge-success">Ativo</span>
                                            @else
                                                <span class="badge badge-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>{{ $modelo->created_at->format('d/m/Y') }}</td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" 
                                               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                Ações
                                                <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" 
                                                 data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.show', $modelo) }}" class="menu-link px-3 d-flex align-items-center">
                                                        <i class="ki-duotone ki-eye fs-5 me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Visualizar
                                                    </a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.download', $modelo) }}?v={{ $modelo->updated_at->timestamp }}" class="menu-link px-3 d-flex align-items-center">
                                                        <i class="ki-duotone ki-down fs-5 me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Download
                                                    </a>
                                                </div>
                                                @if($modelo->document_key)
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('onlyoffice.standalone.editor.modelo', $modelo) }}" 
                                                       class="menu-link px-3 d-flex align-items-center" 
                                                       target="_blank">
                                                       <i class="ki-duotone ki-document-edit fs-5 me-2">
                                                           <span class="path1"></span>
                                                           <span class="path2"></span>
                                                       </i>
                                                       Editar documento
                                                    </a>
                                                </div>
                                                @endif
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('documentos.modelos.edit', $modelo) }}" class="menu-link px-3 d-flex align-items-center">
                                                        <i class="ki-duotone ki-pencil fs-5 me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Editar
                                                    </a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger delete-modelo d-flex align-items-center" 
                                                       data-modelo-id="{{ $modelo->id }}" 
                                                       data-modelo-nome="{{ $modelo->nome }}"
                                                       data-delete-url="{{ route('documentos.modelos.destroy', $modelo) }}">
                                                       <i class="ki-duotone ki-trash fs-5 me-2">
                                                           <span class="path1"></span>
                                                           <span class="path2"></span>
                                                           <span class="path3"></span>
                                                           <span class="path4"></span>
                                                           <span class="path5"></span>
                                                       </i>
                                                       Excluir
                                                    </a>
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

@endsection

@push('styles')
<style>
/* Estilos customizados para SweetAlert2 */
.swal2-styled-popup {
    border-radius: 0.475rem !important;
    box-shadow: 0 0 50px 0 rgba(82, 63, 105, 0.15) !important;
}

.swal2-styled-popup .swal2-title {
    font-size: 1.5rem !important;
    font-weight: 600 !important;
    color: #181C32 !important;
    margin-bottom: 1rem !important;
}

.swal2-styled-popup .swal2-html-container {
    font-size: 1rem !important;
    color: #5E6278 !important;
}

.swal2-styled-popup .swal2-actions {
    gap: 0.5rem !important;
}

.swal2-styled-popup .btn {
    padding: 0.75rem 1.5rem !important;
    font-weight: 500 !important;
}

/* Animação de loading */
.swal2-loader {
    border-color: #009EF7 !important;
    border-right-color: transparent !important;
}

/* Estilos para toasts */
.swal2-toast-styled {
    border-radius: 0.475rem !important;
    box-shadow: 0 0 20px 0 rgba(82, 63, 105, 0.15) !important;
}

.swal2-toast-styled .swal2-icon {
    margin: 0 !important;
}

.swal2-toast-styled .swal2-title {
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    margin: 0 0 0.25rem 0 !important;
}

.swal2-toast-styled .swal2-html-container {
    font-size: 0.85rem !important;
    margin: 0 !important;
}
</style>
@endpush

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar exclusão de modelos com SweetAlert2
    document.querySelectorAll('.delete-modelo').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const modeloId = this.dataset.modeloId;
            const modeloNome = this.dataset.modeloNome;
            const deleteUrl = this.dataset.deleteUrl;
            
            console.log('Dados do modelo:', { modeloId, modeloNome, deleteUrl });
            
            
            Swal.fire({
                title: 'Confirmar Exclusão',
                html: `
                    <div class="text-center">
                        <i class="ki-duotone ki-trash fs-5x text-danger mb-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        <p class="text-gray-700 fs-5 fw-semibold mb-4">
                            Tem certeza que deseja excluir o modelo?
                        </p>
                        <p class="text-gray-900 fs-6 fw-bold mb-2">
                            "${modeloNome}"
                        </p>
                        <div class="alert alert-danger d-flex align-items-center p-3 mt-5">
                            <i class="ki-duotone ki-shield-cross fs-2x text-danger me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold">Atenção!</span>
                                <span class="fs-7">Esta ação não pode ser desfeita.</span>
                            </div>
                        </div>
                    </div>
                `,
                icon: null,
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: '<i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> Sim, excluir',
                cancelButtonText: '<i class="ki-duotone ki-cross-circle fs-2"><span class="path1"></span><span class="path2"></span></i> Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger fw-bold',
                    cancelButton: 'btn btn-light-primary fw-bold me-3',
                    popup: 'swal2-styled-popup'
                },
                reverseButtons: true,
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    if (!deleteUrl) {
                        Swal.showValidationMessage('URL de exclusão não encontrada');
                        return false;
                    }
                    
                    return fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('Delete response status:', response.status);
                        if (!response.ok) {
                            if (response.status === 405) {
                                throw new Error('Método não permitido. Verifique se a rota DELETE está configurada corretamente.');
                            }
                            return response.text().then(text => {
                                try {
                                    const data = JSON.parse(text);
                                    throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
                                } catch {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                }
                            });
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        Swal.showValidationMessage(`Erro: ${error.message || 'Falha na comunicação com o servidor'}`);
                        return false;
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && result.value !== false) {
                    // Remover a linha da tabela com animação imediatamente
                    const row = document.querySelector(`tr[data-modelo-id="${modeloId}"]`);
                    if (row) {
                        row.style.transition = 'opacity 0.3s ease-out';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            
                            // Se não houver mais modelos, recarregar a página
                            const remainingRows = document.querySelectorAll('tbody tr[data-modelo-id]');
                            if (remainingRows.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }
                    
                    // Mostrar toast de sucesso ao invés de modal
                    showToast('success', 'Excluído!', 'O modelo foi excluído com sucesso.');
                }
            });
        });
    });

    // Função para mostrar toast de notificação
    function showToast(type, title, message) {
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'swal2-toast-styled'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        toast.fire({
            icon: type,
            title: title,
            text: message
        });
    }

    // Mostrar mensagens de sessão com toast
    @if(session('success'))
        showToast('success', 'Sucesso!', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showToast('error', 'Erro!', '{{ session('error') }}');
    @endif

    @if($errors->any())
        showToast('error', 'Erro!', '{{ $errors->first() }}');
    @endif

    // Auto-submit do formulário ao mudar os selects
    document.querySelectorAll('select[name="categoria"], select[name="tipo_proposicao_id"], select[name="is_template"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

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