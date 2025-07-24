@extends('layouts.app')

@section('title', 'Minhas Proposições')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-document fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Minhas Proposições
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Minhas Proposições</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Stats-->
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #F1416C;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start" data-stat="total">{{ $proposicoes->total() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total de Proposições</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Criadas</span>
                            <span class="fw-bold fs-6 text-white" data-stat="total">{{ $proposicoes->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #F1BC00;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start" data-stat="rascunho">{{ $proposicoes->where('status', 'rascunho')->count() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Rascunhos</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Pendentes</span>
                            <span class="fw-bold fs-6 text-white" data-stat="rascunho">{{ $proposicoes->where('status', 'rascunho')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #7239EA;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ $proposicoes->where('status', 'analise')->count() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Em Análise</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Tramitando</span>
                            <span class="fw-bold fs-6 text-white">{{ $proposicoes->where('status', 'analise')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10" style="background-color: #17C653;background-image:url('{{ asset('assets/media/patterns/vector-1.png') }}')">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-semibold text-white me-1 align-self-start">{{ $proposicoes->where('status', 'aprovada')->count() }}</span>
                        </div>
                        <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Aprovadas</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-white opacity-75">Finalizadas</span>
                            <span class="fw-bold fs-6 text-white">{{ $proposicoes->where('status', 'aprovada')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Stats-->

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" data-kt-proposicoes-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar proposições..." />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-proposicoes-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_help_proposicoes">
                        <i class="ki-duotone ki-information-5 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Como Criar
                    </button>
                    <a href="{{ route('proposicoes.criar') }}" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Nova Proposição
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($proposicoes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle">
                        <thead>
                            <tr class="fw-bold fs-6 text-gray-800">
                                <th>Proposição</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposicoes as $proposicao)
                                <tr data-proposicao-id="{{ $proposicao->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ki-duotone ki-file-text fs-3 text-primary me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <div class="fw-bold">#{{ $proposicao->id }}</div>
                                                <div class="text-muted fs-7" style="max-width: 300px;">
                                                    {{ Str::limit($proposicao->ementa ?? 'Sem ementa', 50) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-secondary fw-bold">{{ strtoupper($proposicao->tipo) }}</span>
                                    </td>
                                    <td>
                                        @switch($proposicao->status)
                                            @case('rascunho')
                                                <span class="badge badge-warning">
                                                    <i class="ki-duotone ki-pencil fs-7 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Rascunho
                                                </span>
                                                @break
                                            @case('analise')
                                                <span class="badge badge-info">
                                                    <i class="ki-duotone ki-time fs-7 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Em Análise
                                                </span>
                                                @break
                                            @case('aprovada')
                                                <span class="badge badge-success">
                                                    <i class="ki-duotone ki-check fs-7 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Aprovada
                                                </span>
                                                @break
                                            @case('rejeitada')
                                                <span class="badge badge-danger">
                                                    <i class="ki-duotone ki-cross fs-7 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Rejeitada
                                                </span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($proposicao->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div>{{ date('d/m/Y', strtotime($proposicao->created_at ?? now())) }}</div>
                                        <div class="text-muted fs-7">{{ date('H:i', strtotime($proposicao->created_at ?? now())) }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('proposicoes.show', $proposicao->id) }}" 
                                           class="btn btn-sm btn-light-primary">
                                            <i class="ki-duotone ki-eye fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Visualizar
                                        </a>
                                        
                                        @if($proposicao->status === 'rascunho')
                                            <a href="{{ route('proposicoes.editar-texto', $proposicao->id) }}" 
                                               class="btn btn-sm btn-primary ms-2">
                                                <i class="ki-duotone ki-pencil fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Editar
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-light-danger ms-2"
                                                    onclick="confirmarExclusaoProposicao({{ $proposicao->id }})">
                                                <i class="ki-duotone ki-trash fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                </i>
                                                Excluir
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                @if($proposicoes->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $proposicoes->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-10">
                    <i class="ki-duotone ki-file-added fs-3x text-gray-300 mb-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <h3 class="text-gray-800 fw-bold mb-3">Nenhuma proposição encontrada</h3>
                    <p class="text-gray-500 mb-5">Você ainda não criou nenhuma proposição legislativa.</p>
                    <a href="{{ route('proposicoes.criar') }}" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Criar Primeira Proposição
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!--begin::Modal - Como Criar-->
<div class="modal fade" id="kt_modal_help_proposicoes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Como Criar uma Proposição</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-rocket fs-2 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Passo a Passo
                            </h3>
                            <div class="timeline timeline-border-dashed">
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">1</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Dados Básicos</div>
                                            <div class="text-gray-500">Escolha o tipo de proposição, escreva a ementa e selecione um modelo</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">2</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Preencher Modelo</div>
                                            <div class="text-gray-500">Complete os campos do modelo escolhido com os dados da proposição</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">3</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Edição Final</div>
                                            <div class="text-gray-500">Revise e faça ajustes no texto gerado automaticamente</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label bg-light">
                                            <span class="text-gray-500 fw-bold fs-8">4</span>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-10 mt-n1">
                                        <div class="mb-5 pe-3">
                                            <div class="fs-6 fw-semibold text-gray-700 mb-2">Enviar</div>
                                            <div class="text-gray-500">Envie a proposição finalizada para análise do legislativo</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-10">
                            <h3 class="text-gray-900 fw-bold fs-4 mb-5">
                                <i class="ki-duotone ki-information fs-2 text-info me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Tipos de Proposição
                            </h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3"><strong>PL:</strong> Projeto de Lei Ordinária</div>
                                    <div class="mb-3"><strong>PLP:</strong> Projeto de Lei Complementar</div>
                                    <div class="mb-3"><strong>PEC:</strong> Proposta de Emenda Constitucional</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3"><strong>PDC:</strong> Projeto de Decreto Legislativo</div>
                                    <div class="mb-3"><strong>PRC:</strong> Projeto de Resolução</div>
                                    <div class="mb-3"><strong>IND:</strong> Indicação</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Como Criar-->

@endsection

@push('scripts')
<script>
// Filtro de busca na tabela
document.querySelector('[data-kt-proposicoes-table-filter="search"]').addEventListener('input', function(e) {
    const searchValue = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        const proposicaoText = row.querySelector('td:first-child').textContent.toLowerCase();
        const tipoText = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        if (proposicaoText.includes(searchValue) || tipoText.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Função para confirmar exclusão de proposição
function confirmarExclusaoProposicao(proposicaoId) {
    Swal.fire({
        title: 'Confirmar Exclusão',
        html: `Tem certeza que deseja excluir esta proposição?<br><strong>Esta ação não pode ser desfeita.</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, Excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f1416c',
        cancelButtonColor: '#7e8299',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-light'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Excluindo...',
                text: 'Aguarde enquanto a proposição é removida.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Fazer requisição DELETE
            fetch(`/proposicoes/${proposicaoId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        // Remover a linha da tabela
                        document.querySelector(`tr[data-proposicao-id="${proposicaoId}"]`)?.remove();
                        
                        // Se não há mais proposições, recarregar a página para mostrar empty state
                        if (document.querySelectorAll('tbody tr').length === 0) {
                            window.location.reload();
                        } else {
                            // Atualizar contadores nos cards
                            atualizarContadores();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Erro',
                        text: data.message || 'Erro ao excluir proposição.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                Swal.fire({
                    title: 'Erro',
                    text: 'Erro de conexão. Tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });
        }
    });
}

// Função para atualizar contadores após exclusão
function atualizarContadores() {
    const totalRows = document.querySelectorAll('tbody tr').length;
    const rascunhoRows = document.querySelectorAll('tbody tr').length; // Simplificado - assumindo que só rascunhos podem ser excluídos
    
    // Atualizar cards de estatísticas
    document.querySelectorAll('[data-stat="total"]').forEach(el => {
        el.textContent = totalRows;
    });
    
    document.querySelectorAll('[data-stat="rascunho"]').forEach(el => {
        el.textContent = rascunhoRows;
    });
}
</script>
@endpush