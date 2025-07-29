@extends('layouts.app')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!-- Cards de Resumo -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-md-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-100" style="background-color: #50cd89;background-image:url('/assets/media/patterns/vector-1.png')">
                    <div class="card-header pt-5">
                        <h3 class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $proposicoes->total() }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Assinadas</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <span class="text-white opacity-75 fs-7">Proposições com assinatura digital</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-100" style="background-color: #7239ea;background-image:url('/assets/media/patterns/vector-1.png')">
                    <div class="card-header pt-5">
                        <h3 class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $proposicoes->where('status', 'assinado')->count() }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Aguardando Envio</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <span class="text-white opacity-75 fs-7">Prontas para protocolo</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-100" style="background-color: #009ef7;background-image:url('/assets/media/patterns/vector-1.png')">
                    <div class="card-header pt-5">
                        <h3 class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $proposicoes->where('status', 'enviado_protocolo')->count() }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Enviadas</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <span class="text-white opacity-75 fs-7">Aguardando protocolo</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-100" style="background-color: #f1416c;background-image:url('/assets/media/patterns/vector-1.png')">
                    <div class="card-header pt-5">
                        <h3 class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $proposicoes->where('status', 'protocolado')->count() }}</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Protocoladas</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <span class="text-white opacity-75 fs-7">Finalizadas no sistema</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-6">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-docs-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Buscar proposição" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                        <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-sm btn-light-primary">
                            <i class="ki-duotone ki-arrow-left fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_docs_datatable">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_docs_datatable .form-check-input" value="1" />
                                    </div>
                                </th>
                                <th class="min-w-125px">Proposição</th>
                                <th class="min-w-125px">Tipo</th>
                                <th class="min-w-125px">Data Assinatura</th>
                                <th class="min-w-125px">Status</th>
                                <th class="min-w-125px">Certificado</th>
                                <th class="text-end min-w-100px">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($proposicoes as $proposicao)
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="{{ $proposicao->id }}" />
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('proposicoes.show', $proposicao) }}" class="text-gray-800 text-hover-primary mb-1">
                                            #{{ $proposicao->id }}
                                        </a>
                                        <span class="text-gray-500 text-truncate" style="max-width: 300px;">
                                            {{ Str::limit($proposicao->ementa, 50) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary">
                                        {{ ucfirst(str_replace('_', ' ', $proposicao->tipo)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 mb-1">
                                            {{ $proposicao->data_assinatura->format('d/m/Y') }}
                                        </span>
                                        <span class="text-gray-500 fs-7">
                                            {{ $proposicao->data_assinatura->format('H:i') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @switch($proposicao->status)
                                        @case('assinado')
                                            <span class="badge badge-light-success">
                                                <i class="ki-duotone ki-check-circle fs-7 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Assinado
                                            </span>
                                            @break
                                        @case('enviado_protocolo')
                                            <span class="badge badge-light-info">
                                                <i class="ki-duotone ki-send fs-7 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Enviado Protocolo
                                            </span>
                                            @break
                                        @case('protocolado')
                                            <span class="badge badge-light-primary">
                                                <i class="ki-duotone ki-document fs-7 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Protocolado
                                            </span>
                                            @break
                                        @default
                                            <span class="badge badge-light">
                                                {{ ucfirst($proposicao->status) }}
                                            </span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($proposicao->certificado_digital)
                                        <span class="badge badge-light-success">
                                            <i class="ki-duotone ki-shield-tick fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Certificado Digital
                                        </span>
                                    @else
                                        <span class="badge badge-light-warning">
                                            <i class="ki-duotone ki-shield-cross fs-7 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            Sem Certificado
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Ações
                                        <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <a href="{{ route('proposicoes.show', $proposicao) }}" class="menu-link px-3">
                                                <i class="ki-duotone ki-eye fs-5 me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                Visualizar
                                            </a>
                                        </div>
                                        @if($proposicao->status === 'assinado')
                                        <div class="menu-item px-3">
                                            <a href="#" onclick="enviarProtocolo({{ $proposicao->id }})" class="menu-link px-3">
                                                <i class="ki-duotone ki-send fs-5 me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Enviar para Protocolo
                                            </a>
                                        </div>
                                        @endif
                                        <div class="menu-item px-3">
                                            <a href="#" onclick="verDetalhesAssinatura({{ $proposicao->id }})" class="menu-link px-3">
                                                <i class="ki-duotone ki-information fs-5 me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                Detalhes da Assinatura
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ki-duotone ki-document fs-3x text-gray-300 mb-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="text-gray-500">Nenhuma proposição assinada encontrada.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($proposicoes->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $proposicoes->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes da Assinatura -->
<div class="modal fade" id="modalDetalhesAssinatura" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detalhes da Assinatura Digital</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body" id="detalhesAssinaturaContent">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// DataTable
$(document).ready(function() {
    const table = $('#kt_docs_datatable').DataTable({
        "info": false,
        "order": [[3, "desc"]],
        "language": {
            "search": "Pesquisar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "paginate": {
                "first": "Primeiro",
                "last": "Último",
                "next": "Próximo",
                "previous": "Anterior"
            },
            "emptyTable": "Nenhuma proposição assinada encontrada",
            "zeroRecords": "Nenhum registro encontrado"
        },
        "pageLength": 15,
        "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "Todos"]]
    });

    // Search
    const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
    filterSearch.addEventListener('keyup', function (e) {
        table.search(e.target.value).draw();
    });
});

function enviarProtocolo(proposicaoId) {
    Swal.fire({
        title: 'Enviar para Protocolo?',
        text: 'Deseja enviar esta proposição para o protocolo?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, Enviar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#50cd89',
        cancelButtonColor: '#7239ea'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/proposicoes/${proposicaoId}/enviar-protocolo`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Enviado!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#50cd89'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Erro!',
                        text: xhr.responseJSON?.message || 'Erro ao enviar para protocolo',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function verDetalhesAssinatura(proposicaoId) {
    // Buscar detalhes da assinatura via AJAX
    $.ajax({
        url: `/proposicoes/${proposicaoId}`,
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(proposicao) {
            let html = `
                <div class="d-flex flex-column gap-5">
                    <div>
                        <label class="form-label fw-bold">Data e Hora da Assinatura:</label>
                        <p class="text-gray-800">${proposicao.data_assinatura ? new Date(proposicao.data_assinatura).toLocaleString('pt-BR') : 'Não disponível'}</p>
                    </div>
                    <div>
                        <label class="form-label fw-bold">IP da Assinatura:</label>
                        <p class="text-gray-800">${proposicao.ip_assinatura || 'Não disponível'}</p>
                    </div>
                    <div>
                        <label class="form-label fw-bold">Certificado Digital:</label>
                        <p class="text-gray-800">${proposicao.certificado_digital ? 'Sim' : 'Não'}</p>
                    </div>
                    <div>
                        <label class="form-label fw-bold">Hash da Assinatura:</label>
                        <p class="text-gray-800 text-break" style="font-family: monospace; font-size: 12px;">
                            ${proposicao.assinatura_digital ? proposicao.assinatura_digital.substring(0, 64) + '...' : 'Não disponível'}
                        </p>
                    </div>
                </div>
            `;
            
            $('#detalhesAssinaturaContent').html(html);
            $('#modalDetalhesAssinatura').modal('show');
        },
        error: function() {
            toastr.error('Erro ao carregar detalhes da assinatura');
        }
    });
}
</script>
@endpush