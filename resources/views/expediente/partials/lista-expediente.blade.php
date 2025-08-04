<!-- Informações sobre o Expediente -->
<div class="notice d-flex bg-light-info rounded border-info border border-dashed mb-5 p-6">
    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
        <div class="mb-3 mb-md-0 fw-semibold">
            <h4 class="text-gray-900 fw-bold mb-1">📋 Expediente</h4>
            <div class="fs-6 text-gray-700 pe-7">
                <strong>Tipos permitidos:</strong>
                <ul class="mt-2 mb-0">
                    @foreach($regras['tipos_permitidos'] as $tipo)
                    <li>{{ $tipo }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="badge badge-light-info fs-8 fw-bold">
                Votação: {{ $regras['pode_votar'] ? 'Permitida' : 'Não permitida' }}
            </div>
        </div>
    </div>
</div>

@if($proposicoes->count() > 0)
    <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
            <thead>
                <tr class="fw-bold text-muted">
                    <th class="min-w-150px">Proposição</th>
                    <th class="min-w-140px">Autor</th>
                    <th class="min-w-120px">Protocolo</th>
                    <th class="min-w-100px text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proposicoes as $proposicao)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-5">
                                <span class="symbol-label bg-light-info text-info fw-bold">
                                    {{ strtoupper(substr($proposicao->tipo, 0, 2)) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-start flex-column">
                                <a href="{{ route('expediente.show', $proposicao) }}" class="text-dark fw-bold text-hover-primary fs-6">
                                    {{ $proposicao->tipo_formatado }}
                                </a>
                                <span class="text-muted fw-semibold text-muted d-block fs-7">
                                    {{ Str::limit($proposicao->ementa, 50) }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-dark fw-bold d-block fs-6">{{ $proposicao->autor->name }}</span>
                        <span class="text-muted fw-semibold fs-7">{{ $proposicao->autor->roles->first()->name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="text-dark fw-bold d-block fs-6">{{ $proposicao->numero_protocolo }}</span>
                        <span class="text-muted fw-semibold fs-7">{{ $proposicao->data_protocolo->format('d/m/Y') }}</span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end flex-shrink-0">
                            <a href="{{ route('expediente.show', $proposicao) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                <i class="ki-duotone ki-eye fs-5"></i>
                            </a>
                            @if($regras['pode_votar'])
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm" 
                                    onclick="enviarParaVotacao({{ $proposicao->id }})"
                                    title="Enviar para Votação">
                                <i class="ki-duotone ki-check-square fs-5"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-10">
        <i class="ki-duotone ki-document fs-4x text-muted mb-5"></i>
        <div class="fs-5 fw-bold text-gray-700 mb-2">Nenhuma proposição no Expediente</div>
        <div class="fs-6 text-muted">Não há proposições classificadas para a fase do Expediente.</div>
    </div>
@endif