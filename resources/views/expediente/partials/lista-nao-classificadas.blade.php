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
                                <span class="symbol-label bg-light-warning text-warning fw-bold">
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
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light btn-active-primary" type="button" data-bs-toggle="dropdown">
                                Classificar
                                <i class="ki-duotone ki-down fs-5"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="classificarProposicao({{ $proposicao->id }}, 'EXPEDIENTE')">
                                        <i class="ki-duotone ki-information fs-5 text-info me-2"></i>
                                        Expediente
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="classificarProposicao({{ $proposicao->id }}, 'ORDEM_DO_DIA')">
                                        <i class="ki-duotone ki-scales fs-5 text-primary me-2"></i>
                                        Ordem do Dia
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('expediente.show', $proposicao) }}">
                                        <i class="ki-duotone ki-eye fs-5 text-muted me-2"></i>
                                        Ver Detalhes
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-10">
        <i class="ki-duotone ki-check-circle fs-4x text-success mb-5"></i>
        <div class="fs-3 fw-bold text-gray-700 mb-2">Todas Classificadas!</div>
        <div class="fs-6 text-muted">Todas as proposições já foram classificadas quanto ao momento da sessão.</div>
    </div>
@endif