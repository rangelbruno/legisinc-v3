@extends('layouts.admin')

@section('title', 'Verificação de Assinatura Digital - Proposição ' . $proposicao->id)

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <i class="ki-duotone ki-security-check fs-3x text-primary mb-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <h1 class="h3 text-gray-900 mb-2">Verificação de Assinatura Digital</h1>
                    <p class="text-muted">Validação de autenticidade e integridade do documento</p>
                </div>
            </div>

            <!-- Proposição Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <div class="card-title mb-0">
                        <i class="ki-duotone ki-document fs-5 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Informações do Documento
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-gray-600">ID da Proposição:</label>
                            <div class="text-gray-900">#{{ $proposicao->id }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-gray-600">Tipo:</label>
                            <div class="text-gray-900">{{ $proposicao->tipo }}</div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold text-gray-600">Ementa:</label>
                            <div class="text-gray-900">{{ Str::limit($proposicao->ementa, 200) }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-gray-600">Status:</label>
                            <span class="badge badge-{{ $proposicao->status === 'assinado' ? 'success' : 'primary' }}">
                                {{ ucfirst($proposicao->status) }}
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-gray-600">Autor:</label>
                            <div class="text-gray-900">{{ $proposicao->autor->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Results -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="card-title mb-0">
                        @if($verificacao['status'] === 'signed')
                            <i class="ki-duotone ki-check-circle fs-5 me-2 text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="text-success">Assinatura Válida</span>
                        @elseif($verificacao['status'] === 'not_signed')
                            <i class="ki-duotone ki-cross-circle fs-5 me-2 text-warning">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="text-warning">Documento Não Assinado</span>
                        @else
                            <i class="ki-duotone ki-information fs-5 me-2 text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <span class="text-danger">Erro na Verificação</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($verificacao['status'] === 'signed')
                        <!-- Valid Signature -->
                        <div class="alert alert-success d-flex align-items-center mb-4">
                            <i class="ki-duotone ki-shield-tick fs-2x me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div>
                                <h4 class="alert-heading mb-1">{{ $verificacao['message'] }}</h4>
                                <p class="mb-0">A assinatura digital foi verificada com sucesso e o documento é autêntico.</p>
                            </div>
                        </div>

                        <!-- Signature Details -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="bg-light-success p-4 rounded">
                                    <i class="ki-duotone ki-user-tick fs-3x text-success mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <h5 class="text-success mb-2">Signatário</h5>
                                    <div class="mb-2">
                                        <span class="fw-semibold">Nome:</span><br>
                                        {{ $verificacao['signer_name'] }}
                                    </div>
                                    @if(!empty($verificacao['signer_cn']))
                                    <div class="mb-2">
                                        <span class="fw-semibold">Certificado CN:</span><br>
                                        <small class="text-muted">{{ $verificacao['signer_cn'] }}</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="bg-light-primary p-4 rounded">
                                    <i class="ki-duotone ki-time fs-3x text-primary mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <h5 class="text-primary mb-2">Data da Assinatura</h5>
                                    <div class="mb-2">
                                        <span class="fw-semibold">Assinado em:</span><br>
                                        {{ $verificacao['signature_date'] }}
                                    </div>
                                    <div class="mb-2">
                                        <span class="fw-semibold">Tipo:</span><br>
                                        <span class="badge badge-primary">{{ $verificacao['signature_type'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!empty($verificacao['document_hash']))
                        <!-- Document Hash -->
                        <div class="mb-4">
                            <div class="bg-light-info p-4 rounded">
                                <i class="ki-duotone ki-code fs-3x text-info mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <h5 class="text-info mb-2">Hash do Documento</h5>
                                <div class="mb-2">
                                    <span class="fw-semibold">SHA-256:</span><br>
                                    <code class="bg-light px-2 py-1 rounded">{{ $verificacao['document_hash'] }}</code>
                                </div>
                                <small class="text-muted">Este hash garante que o documento não foi alterado após a assinatura.</small>
                            </div>
                        </div>
                        @endif

                        @if(!empty($verificacao['pdf_available']) && $verificacao['pdf_available'])
                        <!-- PDF Actions -->
                        <div class="bg-light-secondary p-4 rounded">
                            <i class="ki-duotone ki-document fs-3x text-secondary mb-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h5 class="text-secondary mb-2">PDF Assinado</h5>
                            <p class="mb-3">O documento assinado está disponível para visualização e download.</p>
                            <div class="mb-3">
                                <span class="fw-semibold">Tamanho:</span> {{ $verificacao['pdf_size'] }}
                            </div>
                            <div class="d-flex gap-3">
                                <a href="{{ $verificacao['signed_pdf_url'] }}" target="_blank"
                                   class="btn btn-primary">
                                    <i class="ki-duotone ki-eye fs-5 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Visualizar PDF
                                </a>
                                <a href="{{ $verificacao['signed_pdf_url'] }}" download
                                   class="btn btn-secondary">
                                    <i class="ki-duotone ki-download fs-5 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Download PDF
                                </a>
                            </div>
                        </div>
                        @endif

                        @if(!empty($verificacao['verification_details']))
                        <!-- Additional Details -->
                        <div class="mt-4">
                            <h6 class="text-gray-700 mb-3">Detalhes Técnicos</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    @foreach($verificacao['verification_details'] as $key => $value)
                                        @if(!empty($value))
                                        <tr>
                                            <td class="fw-semibold text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</td>
                                            <td class="text-gray-900">{{ $value }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        @endif

                    @elseif($verificacao['status'] === 'not_signed')
                        <!-- Not Signed -->
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="ki-duotone ki-information fs-2x me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div>
                                <h4 class="alert-heading mb-1">Documento Não Assinado</h4>
                                <p class="mb-0">{{ $verificacao['message'] }}</p>
                            </div>
                        </div>

                    @else
                        <!-- Error -->
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="ki-duotone ki-cross-circle fs-2x me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div>
                                <h4 class="alert-heading mb-1">Erro na Verificação</h4>
                                <p class="mb-0">{{ $verificacao['message'] }}</p>
                                @if(!empty($verificacao['details']))
                                <small class="text-muted d-block mt-1">{{ $verificacao['details'] }}</small>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Footer Info -->
                    <hr class="my-4">
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="ki-duotone ki-information-5 fs-6 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Verificação realizada em {{ now()->format('d/m/Y H:i:s') }} (UTC-3)
                            @if(!empty($uuid))
                            | ID da Verificação: {{ $uuid }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh temporary URLs if needed
@if(isset($verificacao['signed_pdf_url']) && !empty($verificacao['signed_pdf_url']))
setTimeout(() => {
    console.log('⚠️ URLs temporárias podem expirar. Atualize a página se necessário.');
}, 1800000); // 30 minutes
@endif
</script>
@endpush