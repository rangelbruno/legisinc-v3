<!--begin::Summary-->
<div class="row mb-5">
    <div class="col-12">
        <div class="alert alert-primary">
            <div class="d-flex align-items-center">
                <i class="ki-duotone ki-information-5 fs-2x me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div>
                    <h4 class="mb-1">Relatório de Produtividade</h4>
                    <p class="mb-0">
                        <strong>Período:</strong> {{ $dados['data_inicio'] }} até {{ $dados['data_fim'] }} 
                        <span class="text-muted">| Total de proposições: {{ $dados['total_geral'] }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Summary-->

<!--begin::Statistics Cards-->
<div class="row g-5 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-primary">
            <div class="card-body text-center">
                <i class="ki-duotone ki-check-circle fs-3x text-primary mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h3 class="text-primary">{{ array_sum(array_column($dados['dados_por_usuario'], 'aprovadas')) }}</h3>
                <p class="text-muted mb-0">Proposições Aprovadas</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-warning">
            <div class="card-body text-center">
                <i class="ki-duotone ki-arrow-left fs-3x text-warning mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h3 class="text-warning">{{ array_sum(array_column($dados['dados_por_usuario'], 'devolvidas')) }}</h3>
                <p class="text-muted mb-0">Proposições Devolvidas</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-info">
            <div class="card-body text-center">
                <i class="ki-duotone ki-arrow-circle-left fs-3x text-info mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h3 class="text-info">{{ array_sum(array_column($dados['dados_por_usuario'], 'retornadas')) }}</h3>
                <p class="text-muted mb-0">Proposições Retornadas</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-success">
            <div class="card-body text-center">
                <i class="ki-duotone ki-chart-line-up fs-3x text-success mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <h3 class="text-success">{{ $dados['total_geral'] }}</h3>
                <p class="text-muted mb-0">Total de Proposições</p>
            </div>
        </div>
    </div>
</div>
<!--end::Statistics Cards-->

<!--begin::User Performance Table-->
@if(count($dados['dados_por_usuario']) > 0)
<div class="table-responsive">
    <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
        <thead>
            <tr class="fw-bold text-muted">
                <th class="min-w-150px">Usuário</th>
                <th class="min-w-100px text-center">Aprovadas</th>
                <th class="min-w-100px text-center">Devolvidas</th>
                <th class="min-w-100px text-center">Retornadas</th>
                <th class="min-w-100px text-center">Total</th>
                <th class="min-w-100px text-center">Taxa de Aprovação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dados['dados_por_usuario'] as $usuarioId => $dadosUsuario)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-45px me-5">
                            <div class="symbol-label bg-light-primary text-primary fw-bold">
                                {{ strtoupper(substr($dadosUsuario['nome'], 0, 2)) }}
                            </div>
                        </div>
                        <div class="d-flex justify-content-start flex-column">
                            <span class="text-dark fw-bold text-hover-primary fs-6">{{ $dadosUsuario['nome'] }}</span>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge badge-light-success fs-7 fw-bold">{{ $dadosUsuario['aprovadas'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-light-warning fs-7 fw-bold">{{ $dadosUsuario['devolvidas'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-light-info fs-7 fw-bold">{{ $dadosUsuario['retornadas'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-light-primary fs-7 fw-bold">{{ $dadosUsuario['total'] }}</span>
                </td>
                <td class="text-center">
                    @php
                        $taxaAprovacao = $dadosUsuario['total'] > 0 ? round(($dadosUsuario['aprovadas'] / $dadosUsuario['total']) * 100, 1) : 0;
                        $corTaxa = $taxaAprovacao >= 80 ? 'success' : ($taxaAprovacao >= 60 ? 'warning' : 'danger');
                    @endphp
                    <span class="badge badge-light-{{ $corTaxa }} fs-7 fw-bold">{{ $taxaAprovacao }}%</span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="fw-bold bg-light-primary">
            <tr>
                <td><strong>TOTAL GERAL</strong></td>
                <td class="text-center">
                    <span class="badge badge-success">{{ array_sum(array_column($dados['dados_por_usuario'], 'aprovadas')) }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-warning">{{ array_sum(array_column($dados['dados_por_usuario'], 'devolvidas')) }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-info">{{ array_sum(array_column($dados['dados_por_usuario'], 'retornadas')) }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-primary">{{ $dados['total_geral'] }}</span>
                </td>
                <td class="text-center">
                    @php
                        $totalAprovadas = array_sum(array_column($dados['dados_por_usuario'], 'aprovadas'));
                        $taxaGeralAprovacao = $dados['total_geral'] > 0 ? round(($totalAprovadas / $dados['total_geral']) * 100, 1) : 0;
                        $corTaxaGeral = $taxaGeralAprovacao >= 80 ? 'success' : ($taxaGeralAprovacao >= 60 ? 'warning' : 'danger');
                    @endphp
                    <span class="badge badge-{{ $corTaxaGeral }}">{{ $taxaGeralAprovacao }}%</span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@else
<div class="text-center py-10">
    <i class="ki-duotone ki-file-deleted fs-5x text-muted mb-5">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
    <h3 class="text-gray-600 fs-5 mb-3">Nenhum dado encontrado</h3>
    <div class="text-muted fs-7">Não há proposições revisadas no período selecionado.</div>
</div>
@endif
<!--end::User Performance Table-->

<!--begin::Detailed List-->
@if($dados['proposicoes']->count() > 0)
<div class="mt-10">
    <h4 class="mb-5">Detalhamento das Proposições</h4>
    <div class="table-responsive">
        <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
            <thead>
                <tr class="fw-bold text-muted">
                    <th class="min-w-150px">Proposição</th>
                    <th class="min-w-150px">Autor</th>
                    <th class="min-w-150px">Revisor</th>
                    <th class="min-w-100px">Status</th>
                    <th class="min-w-100px">Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dados['proposicoes'] as $proposicao)
                <tr>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="badge badge-light-dark fs-7 fw-bold mb-1">{{ strtoupper($proposicao->tipo) }}</span>
                            <span class="text-dark fw-bold">{{ $proposicao->titulo ?? 'Proposição #' . $proposicao->id }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="text-gray-900 fw-semibold">{{ $proposicao->autor->name }}</span>
                    </td>
                    <td>
                        <span class="text-gray-900 fw-semibold">Sistema Legislativo</span>
                    </td>
                    <td>
                        @if($proposicao->status === 'aprovado_assinatura')
                            <span class="badge badge-light-success">Aprovada</span>
                        @elseif($proposicao->status === 'devolvido_correcao')
                            <span class="badge badge-light-warning">Devolvida</span>
                        @elseif($proposicao->status === 'retornado_legislativo')
                            <span class="badge badge-light-info">Retornada</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-gray-900 fw-semibold">{{ $proposicao->updated_at->format('d/m/Y H:i') }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
<!--end::Detailed List-->