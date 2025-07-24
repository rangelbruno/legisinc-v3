@extends('components.layouts.app')

@section('title', 'Visualizar Proposição')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Proposição #{{ $proposicao->id }}</h1>
            <p class="text-muted">Visualização detalhada da proposição</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
            @if($proposicao->status === 'rascunho')
                <a href="{{ route('proposicoes.editar-texto', $proposicao->id) }}" class="btn btn-primary ms-2">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Conteúdo Principal -->
        <div class="col-lg-8">
            <!-- Informações Básicas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informações Básicas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo:</label>
                                <div>
                                    <span class="badge badge-secondary fs-6">{{ strtoupper($proposicao->tipo) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <div>
                                    @switch($proposicao->status)
                                        @case('rascunho')
                                            <span class="badge badge-warning fs-6">Rascunho</span>
                                            @break
                                        @case('analise')
                                            <span class="badge badge-info fs-6">Em Análise</span>
                                            @break
                                        @case('aprovada')
                                            <span class="badge badge-success fs-6">Aprovada</span>
                                            @break
                                        @case('rejeitada')
                                            <span class="badge badge-danger fs-6">Rejeitada</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary fs-6">{{ ucfirst($proposicao->status) }}</span>
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ementa:</label>
                        <div class="p-3 bg-light rounded">
                            {{ $proposicao->ementa ?? 'Ementa não informada' }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Autor:</label>
                                <div>{{ $proposicao->autor->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Data de Criação:</label>
                                <div>{{ date('d/m/Y H:i', strtotime($proposicao->created_at ?? now())) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo da Proposição -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-text text-primary me-2"></i>
                        Conteúdo da Proposição
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($proposicao->conteudo))
                        <div class="documento-content">
                            {!! nl2br(e($proposicao->conteudo)) !!}
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <h5>Conteúdo não disponível</h5>
                            <p>O conteúdo desta proposição ainda não foi definido.</p>
                            @if($proposicao->status === 'rascunho')
                                <a href="{{ route('proposicoes.editar-texto', $proposicao->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Adicionar Conteúdo
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog text-primary me-2"></i>
                        Ações
                    </h6>
                </div>
                <div class="card-body">
                    @if($proposicao->status === 'rascunho')
                        <div class="d-grid gap-2">
                            <a href="{{ route('proposicoes.editar-texto', $proposicao->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Editar Proposição
                            </a>
                            <button class="btn btn-outline-danger" onclick="excluirProposicao()">
                                <i class="fas fa-trash me-2"></i>Excluir Rascunho
                            </button>
                        </div>
                    @elseif($proposicao->status === 'analise')
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Em análise:</strong> Aguardando análise do legislativo.
                        </div>
                    @endif

                    <hr>

                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="imprimirProposicao()">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                        <button class="btn btn-outline-success" onclick="exportarPDF()">
                            <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- Histórico/Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history text-info me-2"></i>
                        Histórico
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Proposição Criada</h6>
                                <p class="mb-1 text-muted small">
                                    {{ date('d/m/Y H:i', strtotime($proposicao->created_at ?? now())) }}
                                </p>
                                <small class="text-muted">
                                    Proposição criada por {{ $proposicao->autor->name ?? 'Sistema' }}
                                </small>
                            </div>
                        </div>

                        @if($proposicao->status !== 'rascunho')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Enviada para Análise</h6>
                                    <p class="mb-1 text-muted small">
                                        {{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}
                                    </p>
                                    <small class="text-muted">
                                        Proposição enviada para análise legislativa
                                    </small>
                                </div>
                            </div>
                        @endif

                        @if($proposicao->status === 'aprovada')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Aprovada</h6>
                                    <p class="mb-1 text-muted small">
                                        {{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}
                                    </p>
                                    <small class="text-muted">
                                        Proposição aprovada pelo legislativo
                                    </small>
                                </div>
                            </div>
                        @endif

                        @if($proposicao->status === 'rejeitada')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Rejeitada</h6>
                                    <p class="mb-1 text-muted small">
                                        {{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}
                                    </p>
                                    <small class="text-muted">
                                        Proposição rejeitada pelo legislativo
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function excluirProposicao() {
    Swal.fire({
        title: 'Confirmar Exclusão',
        html: `Tem certeza que deseja excluir este rascunho?<br><strong>Esta ação não pode ser desfeita.</strong>`,
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
            fetch(`/proposicoes/{{ $proposicao->id }}`, {
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
                        // Redirecionar para a lista de proposições
                        window.location.href = '{{ route("proposicoes.minhas-proposicoes") }}';
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

function imprimirProposicao() {
    window.print();
}

function exportarPDF() {
    // TODO: Implementar exportação PDF
    toastr.info('Funcionalidade de exportação em desenvolvimento...');
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>
@endpush

@push('styles')
<style>
.documento-content {
    font-family: 'Times New Roman', serif;
    font-size: 14px;
    line-height: 1.6;
    text-align: justify;
    background: white;
    padding: 30px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content h6 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.badge {
    font-size: 0.8rem;
    padding: 0.375rem 0.75rem;
}

/* Print styles */
@media print {
    .sidebar, .btn, .card-header, .timeline {
        display: none !important;
    }
    
    .documento-content {
        border: none;
        padding: 0;
        box-shadow: none;
    }
    
    body.printing .container-fluid {
        margin: 0;
        padding: 0;
    }
    
    body.printing .card {
        border: none;
        box-shadow: none;
    }
}
</style>
@endpush