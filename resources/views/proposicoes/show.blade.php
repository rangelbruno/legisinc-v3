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

                </div>
            </div>

            <!-- Histórico/Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ki-duotone ki-time fs-3 text-primary me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Histórico da Proposição
                    </h6>
                </div>
                <div class="card-body">
                    <!--begin::Timeline-->
                    <div class="timeline-label">
                        <!--begin::Timeline item-->
                        <div class="timeline-item">
                            <!--begin::Timeline line-->
                            <div class="timeline-line w-40px"></div>
                            <!--end::Timeline line-->

                            <!--begin::Timeline icon-->
                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                <div class="symbol-label bg-light-success">
                                    <i class="ki-duotone ki-plus fs-2 text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </div>
                            </div>
                            <!--end::Timeline icon-->

                            <!--begin::Timeline content-->
                            <div class="timeline-content mb-10 mt-n2">
                                <!--begin::Timeline heading-->
                                <div class="overflow-auto pe-3">
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-semibold mb-2">Proposição Criada</div>
                                    <!--end::Title-->

                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center mt-1 fs-6">
                                        <!--begin::Info-->
                                        <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->created_at ?? now())) }}</div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <!--end::Timeline heading-->

                                <!--begin::Timeline details-->
                                <div class="overflow-auto pb-5">
                                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <div class="fs-6 text-gray-700">
                                                    <span class="text-primary">{{ $proposicao->autor->name ?? 'Sistema' }}</span> 
                                                    criou esta proposição do tipo <strong>{{ strtoupper($proposicao->tipo) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Timeline details-->
                            </div>
                            <!--end::Timeline content-->
                        </div>
                        <!--end::Timeline item-->

                        @if($proposicao->status !== 'rascunho')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-send fs-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Enviada para Análise</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        Proposição enviada para análise legislativa
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif

                        @if($proposicao->status === 'aprovada')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-check fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Proposição Aprovada</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        <i class="ki-duotone ki-check-circle fs-4 text-success me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Proposição aprovada pelo legislativo
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif

                        @if($proposicao->status === 'rejeitada')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-danger">
                                        <i class="ki-duotone ki-cross fs-2 text-danger">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Proposição Rejeitada</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        <i class="ki-duotone ki-information fs-4 text-danger me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Proposição rejeitada pelo legislativo
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif
                    </div>
                    <!--end::Timeline-->
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

/* Timeline styles following Keen UI template patterns */
.timeline-label {
    position: relative;
}

.timeline-line {
    position: absolute;
    left: 20px;
    top: 40px;
    bottom: -20px;
    border-left: 1px dashed #E1E3EA;
}

.timeline-item:last-child .timeline-line {
    display: none;
}

.timeline-icon {
    position: relative;
    z-index: 1;
}

.timeline-content {
    margin-left: 60px;
}

.notice {
    border-radius: 0.475rem;
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