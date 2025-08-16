@extends('components.layouts.app')

@section('title', 'Protocolar Proposição')

@section('content')
<style>
.protocol-card {
    max-width: 600px;
    margin: 0 auto;
}

.signature-status {
    background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%);
    border-left: 4px solid #17C653;
}

.protocol-preview {
    background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%);
    border-left: 4px solid #009ef7;
}

.btn-protocolar {
    background: linear-gradient(135deg, #009ef7 0%, #0077d4 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(0, 158, 247, 0.3);
    transition: all 0.3s ease;
}

.btn-protocolar:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 158, 247, 0.4);
}

.protocol-number {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    color: #009ef7;
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
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <x-alerts.flash />

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!--begin::Protocol Card-->
                    <div class="card protocol-card">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-2 mb-1">{{ $proposicao->tipo }}</span>
                                <span class="text-muted mt-1 fw-semibold fs-6">{{ $proposicao->ementa }}</span>
                            </h3>
                        </div>
                        
                        <div class="card-body pt-0">
                            
                            <!--begin::Autor-->
                            <div class="d-flex align-items-center mb-8">
                                <div class="symbol symbol-45px me-4">
                                    <div class="symbol-label bg-light-primary text-primary fw-bold fs-5">
                                        {{ substr($proposicao->autor->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="fs-5 fw-bold text-gray-800">{{ $proposicao->autor->name }}</div>
                                    <div class="fs-7 text-muted">{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</div>
                                </div>
                            </div>
                            <!--end::Autor-->
                            
                            <!--begin::Status da Assinatura-->
                            @if($proposicao->assinatura_digital && $proposicao->data_assinatura)
                            <div class="signature-status p-4 rounded mb-6">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-shield-tick fs-1 text-success me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div>
                                        <div class="fw-bold fs-5 text-success">Documento Assinado Digitalmente</div>
                                        <div class="fs-7 text-muted">
                                            Assinado em {{ $proposicao->data_assinatura->format('d/m/Y \à\s H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <!--end::Status da Assinatura-->
                            
                            <!--begin::Preview do Protocolo-->
                            <div class="protocol-preview p-4 rounded mb-8">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="ki-duotone ki-files-tablet fs-1 text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div>
                                        <div class="fw-bold fs-5 text-primary">Número de Protocolo</div>
                                        <div class="fs-7 text-muted">Será gerado automaticamente</div>
                                    </div>
                                </div>
                                
                                <div class="separator my-3"></div>
                                
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="fs-7 text-muted mb-1">Data de Protocolo:</div>
                                        <div class="fw-semibold">{{ now()->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="fs-7 text-muted mb-1">Funcionário:</div>
                                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Preview do Protocolo-->
                            
                            <!--begin::Informações Importantes-->
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-8">
                                <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div>
                                        <div class="fs-6 fw-semibold text-gray-700">
                                            <strong>Atenção:</strong> Ao protocolar esta proposição:
                                        </div>
                                        <div class="fs-7 fw-semibold text-gray-600 mt-2">
                                            • Um número de protocolo oficial será atribuído automaticamente<br>
                                            • A proposição será direcionada para as comissões competentes<br>
                                            • O PDF será atualizado com o número de protocolo<br>
                                            • Esta ação não pode ser desfeita
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Informações Importantes-->
                            
                            <!--begin::Action Buttons-->
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('proposicoes.protocolar') }}" class="btn btn-light btn-lg">
                                    <i class="ki-duotone ki-arrow-left fs-4 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Cancelar
                                </a>
                                
                                <button type="button" class="btn btn-protocolar btn-lg px-8" id="btn-protocolar"
                                        {{ !$proposicao->assinatura_digital ? 'disabled title="Proposição deve estar assinada"' : '' }}>
                                    <i class="ki-duotone ki-files-tablet fs-4 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <span class="btn-text">Protocolar Proposição</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                            </div>
                            <!--end::Action Buttons-->
                            
                        </div>
                    </div>
                    <!--end::Protocol Card-->
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnProtocolar = document.getElementById('btn-protocolar');
    const btnText = btnProtocolar.querySelector('.btn-text');
    const spinner = btnProtocolar.querySelector('.spinner-border');
    
    btnProtocolar.addEventListener('click', function() {
        if (btnProtocolar.disabled) return;
        
        // Confirmar protocolação
        Swal.fire({
            title: 'Protocolar Proposição',
            html: `
                <p>Confirma a protocolação desta proposição?</p>
                <div class="alert alert-info mt-3">
                    <strong>Um número de protocolo será gerado automaticamente</strong>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, Protocolar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            confirmButtonColor: '#009ef7',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
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
        
        // Dados automáticos - sistema define tudo
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('tipo_numeracao', 'automatico');
        
        // Enviar requisição para atribuir número automaticamente
        fetch('{{ route("proposicoes.atribuir-numero-protocolo", $proposicao) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Protocolo Realizado!',
                    html: `
                        <div class="text-center">
                            <i class="ki-duotone ki-shield-tick fs-4x text-success mb-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <p class="fs-5 mb-3">Proposição protocolada com sucesso!</p>
                            <div class="protocol-preview p-4 rounded mb-4">
                                <div class="fs-3 fw-bold protocol-number">${data.numero_protocolo}</div>
                                <div class="fs-7 text-muted">Número de Protocolo Oficial</div>
                            </div>
                            <p class="fs-7 text-muted">O PDF foi atualizado com o número de protocolo</p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Ver Lista de Protocolos',
                    confirmButtonColor: '#009ef7'
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
                title: 'Erro ao Protocolar',
                text: error.message || 'Erro ao protocolar proposição. Tente novamente.',
                icon: 'error',
                confirmButtonText: 'Tentar Novamente',
                confirmButtonColor: '#f1416c'
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