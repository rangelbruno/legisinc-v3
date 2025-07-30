@extends('components.layouts.app')

@section('title', 'Protocolar Proposição')

@section('content')
<style>
/* Estilos para verificações */
.verification-item {
    transition: all 0.3s ease;
}

.verification-item.approved {
    border-left: 4px solid #17C653;
    background-color: #f8fff9;
}

.verification-item.pending {
    border-left: 4px solid #FFC700;
    background-color: #fffdf8;
}

.verification-item.failed {
    border-left: 4px solid #F1416C;
    background-color: #fff8f9;
}

/* Botões de ação */
.action-buttons {
    position: sticky;
    bottom: 0;
    background: white;
    border-top: 1px solid #e1e5e9;
    padding: 1rem 0;
    margin-top: 2rem;
    z-index: 10;
}

/* Responsividade */
@media (max-width: 768px) {
    .card-header .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .action-buttons .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Protocolar Proposição
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('proposicoes.protocolar') }}" class="text-muted text-hover-primary">Protocolo</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Protocolar</li>
                </ul>
            </div>
            <!--end::Page title-->
            
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('proposicoes.protocolar') }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <a href="{{ route('proposicoes.show', $proposicao) }}" class="btn btn-sm btn-primary">
                    <i class="ki-duotone ki-eye fs-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Ver Proposição
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <x-alerts.flash />

            <div class="row g-5 g-xl-10">
                <!--begin::Left Column-->
                <div class="col-xl-8 col-lg-7">
                    <!--begin::Dados da Proposição-->
                    <div class="card mb-5 mb-xl-10">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Dados da Proposição</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Informações para protocolação</span>
                            </h3>
                            <div class="card-toolbar">
                                <span class="badge badge-light-{{ $proposicao->urgencia === 'urgentissima' ? 'danger' : ($proposicao->urgencia === 'urgente' ? 'warning' : 'secondary') }} fs-7 fw-bold">
                                    {{ ucfirst($proposicao->urgencia) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body py-3">
                            <div class="row g-5">
                                <div class="col-lg-6">
                                    <div class="d-flex flex-column mb-5">
                                        <label class="fs-6 fw-semibold mb-2">Tipo</label>
                                        <span class="badge badge-light-primary fs-7 fw-bold w-fit">{{ $proposicao->tipo }}</span>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="d-flex flex-column mb-5">
                                        <label class="fs-6 fw-semibold mb-2">Número de Protocolo</label>
                                        <span class="fs-6 fw-bold text-gray-800">
                                            @if($proposicao->numero_protocolo)
                                                {{ $proposicao->numero_protocolo }}
                                            @else
                                                <span class="text-muted">Será gerado automaticamente</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="d-flex flex-column mb-5">
                                        <label class="fs-6 fw-semibold mb-2">Título</label>
                                        <p class="fs-6 text-gray-800">{{ $proposicao->titulo ?: 'Sem título informado' }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="d-flex flex-column mb-5">
                                        <label class="fs-6 fw-semibold mb-2">Ementa</label>
                                        <p class="fs-6 text-gray-600">{{ $proposicao->ementa }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="d-flex flex-column mb-5">
                                        <label class="fs-6 fw-semibold mb-2">Autor</label>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-35px me-3">
                                                <div class="symbol-label bg-light-primary text-primary fw-bold">
                                                    {{ substr($proposicao->autor->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fs-6 fw-bold text-gray-800">{{ $proposicao->autor->name }}</div>
                                                <div class="fs-7 text-muted">{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <div class="d-flex flex-column mb-5">
                                        <label class="fs-6 fw-semibold mb-2">Data de Assinatura</label>
                                        <div class="fs-6 text-gray-800">
                                            @if($proposicao->data_assinatura)
                                                {{ $proposicao->data_assinatura->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">Não assinada</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Dados da Proposição-->
                    
                    <!--begin::Formulário de Protocolo-->
                    <div class="card">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Dados do Protocolo</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Configurações para tramitação</span>
                            </h3>
                        </div>
                        
                        <div class="card-body py-3">
                            <form id="form-protocolacao" method="POST" action="{{ route('proposicoes.efetivar-protocolo', $proposicao) }}">
                                @csrf
                                
                                <!--begin::Comissões de Destino-->
                                <div class="mb-8">
                                    <label class="required fs-6 fw-semibold form-label mb-2">Comissões de Destino</label>
                                    <p class="text-muted fs-7 mb-4">Selecione as comissões que irão analisar esta proposição</p>
                                    
                                    <div class="row g-3">
                                        @foreach($comissoes as $comissao => $obrigatoria)
                                        <div class="col-md-6">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" name="comissoes_destino[]" 
                                                       value="{{ $comissao }}" id="comissao_{{ $loop->index }}"
                                                       {{ $obrigatoria ? 'checked disabled' : '' }} />
                                                <label class="form-check-label fw-semibold" for="comissao_{{ $loop->index }}">
                                                    {{ $comissao }}
                                                    @if($obrigatoria)
                                                        <span class="badge badge-light-warning ms-2 fs-8">Obrigatória</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Campos hidden para comissões obrigatórias -->
                                    @foreach($comissoes as $comissao => $obrigatoria)
                                        @if($obrigatoria)
                                            <input type="hidden" name="comissoes_destino[]" value="{{ $comissao }}" />
                                        @endif
                                    @endforeach
                                </div>
                                <!--end::Comissões de Destino-->
                                
                                <!--begin::Observações-->
                                <div class="mb-8">
                                    <label class="fs-6 fw-semibold form-label mb-2">Observações do Protocolo</label>
                                    <p class="text-muted fs-7 mb-4">Informações adicionais sobre a tramitação (opcional)</p>
                                    <textarea class="form-control form-control-solid" name="observacoes_protocolo" 
                                              rows="4" placeholder="Digite observações relevantes para a tramitação..."></textarea>
                                </div>
                                <!--end::Observações-->
                            </form>
                        </div>
                    </div>
                    <!--end::Formulário de Protocolo-->
                </div>
                <!--end::Left Column-->
                
                <!--begin::Right Column-->
                <div class="col-xl-4 col-lg-5">
                    <!--begin::Verificações Automáticas-->
                    <div class="card mb-5 mb-xl-10">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Verificações</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Status das validações</span>
                            </h3>
                        </div>
                        
                        <div class="card-body py-3">
                            @foreach([
                                'documento_assinado' => 'Documento Assinado',
                                'texto_completo' => 'Texto Completo',
                                'formato_adequado' => 'Formato Adequado',
                                'metadados_completos' => 'Metadados Completos',
                                'revisao_aprovada' => 'Revisão Aprovada'
                            ] as $key => $label)
                            <div class="verification-item {{ $verificacoes[$key] ? 'approved' : 'failed' }} p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    @if($verificacoes[$key])
                                        <i class="ki-duotone ki-check-circle fs-2 text-success me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    @else
                                        <i class="ki-duotone ki-cross-circle fs-2 text-danger me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    @endif
                                    <div>
                                        <div class="fw-bold fs-6">{{ $label }}</div>
                                        <div class="fs-7 text-muted">
                                            {{ $verificacoes[$key] ? 'Verificação aprovada' : 'Verificação pendente' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            <!-- Status Geral -->
                            <div class="separator my-4"></div>
                            
                            <div class="d-flex align-items-center p-3 rounded {{ $verificacoes['todas_aprovadas'] ? 'bg-light-success' : 'bg-light-warning' }}">
                                @if($verificacoes['todas_aprovadas'])
                                    <i class="ki-duotone ki-shield-tick fs-1 text-success me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div>
                                        <div class="fw-bold fs-6 text-success">Pronto para Protocolo</div>
                                        <div class="fs-7 text-muted">Todas as verificações foram aprovadas</div>
                                    </div>
                                @else
                                    <i class="ki-duotone ki-shield-cross fs-1 text-warning me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div>
                                        <div class="fw-bold fs-6 text-warning">Verificações Pendentes</div>
                                        <div class="fs-7 text-muted">Algumas verificações não passaram</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--end::Verificações Automáticas-->
                    
                    <!--begin::Informações Adicionais-->
                    <div class="card">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Informações</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Dados complementares</span>
                            </h3>
                        </div>
                        
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center mb-5">
                                <i class="ki-duotone ki-calendar-8 fs-2 text-primary me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                                <div>
                                    <div class="fw-bold fs-6">Data de Protocolo</div>
                                    <div class="fs-7 text-muted">{{ now()->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-5">
                                <i class="ki-duotone ki-profile-circle fs-2 text-primary me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div>
                                    <div class="fw-bold fs-6">Funcionário</div>
                                    <div class="fs-7 text-muted">{{ Auth::user()->name }}</div>
                                </div>
                            </div>
                            
                            @if($proposicao->urgencia !== 'normal')
                            <div class="d-flex align-items-center">
                                <i class="ki-duotone ki-warning-2 fs-2 text-warning me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div>
                                    <div class="fw-bold fs-6">Urgência</div>
                                    <div class="fs-7 text-muted">
                                        Tramitação {{ $proposicao->urgencia === 'urgentissima' ? 'urgentíssima' : 'urgente' }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!--end::Informações Adicionais-->
                </div>
                <!--end::Right Column-->
            </div>
            
            <!--begin::Action Buttons-->
            <div class="action-buttons">
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('proposicoes.protocolar') }}" class="btn btn-light">
                        <i class="ki-duotone ki-arrow-left fs-4 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Cancelar
                    </a>
                    
                    <button type="button" class="btn btn-primary" id="btn-protocolar" 
                            {{ !$verificacoes['todas_aprovadas'] ? 'disabled' : '' }}>
                        <i class="ki-duotone ki-files-tablet fs-4 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <span class="btn-text">Efetivar Protocolo</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </div>
            <!--end::Action Buttons-->
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-protocolacao');
    const btnProtocolar = document.getElementById('btn-protocolar');
    const btnText = btnProtocolar.querySelector('.btn-text');
    const spinner = btnProtocolar.querySelector('.spinner-border');
    
    btnProtocolar.addEventListener('click', function() {
        if (btnProtocolar.disabled) return;
        
        // Validar comissões selecionadas
        const comissoesSelecionadas = form.querySelectorAll('input[name="comissoes_destino[]"]:checked');
        if (comissoesSelecionadas.length === 0) {
            Swal.fire({
                title: 'Comissões Obrigatórias',
                text: 'Selecione pelo menos uma comissão de destino.',
                icon: 'warning',
                confirmButtonText: 'Entendi'
            });
            return;
        }
        
        // Confirmar protocolação
        Swal.fire({
            title: 'Confirmar Protocolação',
            text: 'Deseja efetivar o protocolo desta proposição? Esta ação não pode ser desfeita.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, Protocolar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                protocolaProposicao();
            }
        });
    });
    
    function protocolaProposicao() {
        // Mostrar loading
        btnProtocolar.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');
        
        // Preparar dados
        const formData = new FormData(form);
        
        // Enviar requisição
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Protocolo Efetuado!',
                    html: `
                        <p>Proposição protocolada com sucesso!</p>
                        <p><strong>Número de Protocolo:</strong> ${data.numero_protocolo}</p>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Continuar'
                }).then(() => {
                    window.location.href = '{{ route("proposicoes.protocolar") }}';
                });
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Swal.fire({
                title: 'Erro',
                text: error.message || 'Erro ao protocolar proposição. Tente novamente.',
                icon: 'error',
                confirmButtonText: 'Tentar Novamente'
            });
        })
        .finally(() => {
            // Esconder loading
            btnProtocolar.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
        });
    }
});
</script>

@endsection