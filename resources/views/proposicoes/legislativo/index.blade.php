@extends('components.layouts.app')

@section('title', 'Proposições para Revisão')

@use('Illuminate\Support\Str')

@section('content')
<style>
.dashboard-card-primary {
    background: linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #7239EA 0%, #5a2bc4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-info {
    background: linear-gradient(135deg, #009EF7 0%, #0077d4 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #009EF7 0%, #0077d4 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-success {
    background: linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #17C653 0%, #13a342 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-warning {
    background: linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #FFC700 0%, #e6b300 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
.dashboard-card-danger {
    background: linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-image: url("{{ asset('assets/media/patterns/vector-1.png') }}"), linear-gradient(135deg, #F1416C 0%, #e02454 100%) !important;
    background-repeat: no-repeat !important;
    background-size: contain, cover !important;
    background-position: right center, center !important;
}
</style>

<!--begin::Post-->
<div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-xxl">
        <!--begin::Row-->
        <div class="row g-5 g-xl-8">
            <!--begin::Col-->
            <div class="col-xl-12">
                <!--begin::Header-->
                <div class="d-flex flex-wrap flex-stack mb-6">
                    <!--begin::Heading-->
                    <h3 class="fw-bold my-2">
                        Revisão Legislativa
                        <span class="fs-6 text-gray-500 fw-semibold ms-1">Proposições aguardando análise técnica</span>
                    </h3>
                    <!--end::Heading-->
                    <!--begin::Actions-->
                    <div class="d-flex flex-wrap my-2">
                        <a href="{{ route('proposicoes.relatorio-legislativo') }}" class="btn btn-light-primary me-3">
                            <i class="ki-duotone ki-chart-line-star fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Relatório
                        </a>
                        <a href="{{ route('proposicoes.aguardando-protocolo') }}" class="btn btn-light-success">
                            <i class="ki-duotone ki-clipboard-check fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Aguardando Protocolo
                        </a>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Header-->

                <!--begin::Statistics-->
                <div class="row g-5 g-xl-8 mb-5">
                    <!--begin::Card 1-->
                    <x-dashboard.card
                        icon="ki-timer"
                        iconClass="text-white"
                        title="Aguardando Revisão"
                        :value="$allProposicoes->where('status', 'enviado_legislativo')->count()"
                        cardType="primary"
                        :progress="($allProposicoes->count() > 0) ? ($allProposicoes->where('status', 'enviado_legislativo')->count() / $allProposicoes->count() * 100) : 0"
                        colSize="col-xl-3"
                    />
                    <!--end::Card 1-->

                    <!--begin::Card 2-->
                    <x-dashboard.card
                        icon="ki-document"
                        iconClass="text-white"
                        title="Em Revisão"
                        :value="$allProposicoes->where('status', 'em_revisao')->count()"
                        cardType="warning"
                        :progress="($allProposicoes->count() > 0) ? ($allProposicoes->where('status', 'em_revisao')->count() / $allProposicoes->count() * 100) : 0"
                        colSize="col-xl-3"
                    />
                    <!--end::Card 2-->

                    <!--begin::Card 3-->
                    <x-dashboard.card
                        icon="ki-check-circle"
                        iconClass="text-white"
                        title="Total do Mês"
                        :value="isset($estatisticas['total_mes']) ? $estatisticas['total_mes'] : 0"
                        cardType="success"
                        :progress="100"
                        colSize="col-xl-3"
                    />
                    <!--end::Card 3-->

                    <!--begin::Card 4-->
                    <x-dashboard.card
                        icon="ki-arrow-left"
                        iconClass="text-white"
                        title="Devolvidas p/ Correção"
                        :value="$allProposicoes->where('status', 'devolvido_correcao')->count()"
                        cardType="danger"
                        :progress="($allProposicoes->count() > 0) ? ($allProposicoes->where('status', 'devolvido_correcao')->count() / $allProposicoes->count() * 100) : 0"
                        colSize="col-xl-3"
                    />
                    <!--end::Card 4-->
                </div>
                <!--end::Statistics-->

                <!--begin::Filters-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Compact form-->
                        <div class="d-flex align-items-center">
                            <!--begin::Input group-->
                            <div class="position-relative w-md-400px me-md-2">
                                <i class="ki-duotone ki-magnifier fs-3 text-gray-500 position-absolute top-50 translate-middle ms-6">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" class="form-control form-control-solid ps-10" name="search" id="search-proposicoes" placeholder="Buscar proposições...">
                            </div>
                            <!--end::Input group-->
                            <!--begin:Action-->
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-primary me-5" id="btn-buscar">Buscar</button>
                                <a id="kt_horizontal_search_advanced_link" class="btn btn-link" data-bs-toggle="collapse" href="#kt_advanced_search_form">Busca Avançada</a>
                            </div>
                            <!--end:Action-->
                        </div>
                        <!--end::Compact form-->
                        <!--begin::Advance form-->
                        <div class="collapse" id="kt_advanced_search_form">
                            <!--begin::Separator-->
                            <div class="separator separator-dashed mt-9 mb-6"></div>
                            <!--end::Separator-->
                            <!--begin::Row-->
                            <div class="row g-8">
                                <!--begin::Col-->
                                <div class="col-xxl-3 col-lg-4 col-md-6">
                                    <label class="fs-6 form-label fw-bold text-gray-900">Status</label>
                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione o status" id="filtro-status">
                                        <option value="">Todos os status</option>
                                        <option value="enviado_legislativo">Aguardando Revisão</option>
                                        <option value="em_revisao">Em Revisão</option>
                                    </select>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-xxl-3 col-lg-4 col-md-6">
                                    <label class="fs-6 form-label fw-bold text-gray-900">Tipo</label>
                                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione o tipo" id="filtro-tipo">
                                        <option value="">Todos os tipos</option>
                                        <option value="PL">Projeto de Lei</option>
                                        <option value="PLP">Projeto de Lei Complementar</option>
                                        <option value="PEC">Proposta de Emenda Constitucional</option>
                                        <option value="PDC">Projeto de Decreto Legislativo</option>
                                        <option value="PRC">Projeto de Resolução</option>
                                    </select>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-xxl-3 col-lg-4 col-md-6">
                                    <label class="fs-6 form-label fw-bold text-gray-900">Autor</label>
                                    <input type="text" class="form-control form-control-solid" id="filtro-autor" placeholder="Nome do autor">
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <div class="row g-8 mt-2">
                                <!--begin::Col-->
                                <div class="col-xxl-12">
                                    <div class="text-end">
                                        <button type="button" class="btn btn-light me-3" id="btn-limpar">Limpar</button>
                                        <button type="button" class="btn btn-primary" id="btn-filtrar">
                                            <span class="indicator-label">Filtrar</span>
                                            <span class="indicator-progress">Aguarde...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Advance form-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Filters-->

                @if($proposicoes->where('status', 'devolvido_correcao')->count() > 0)
                <!--begin::Urgent Section-->
                <div class="card mb-5 mb-xl-8">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Title-->
                            <h2 class="fw-bold text-danger">
                                <i class="ki-duotone ki-arrow-left fs-2 text-danger me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Ação Requerida: Correções Solicitadas
                            </h2>
                            <!--end::Title-->
                        </div>
                        <!--end::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <div class="badge badge-light-danger fs-7 fw-bold px-3 py-2">
                                {{ $proposicoes->where('status', 'devolvido_correcao')->count() }} 
                                {{ $proposicoes->where('status', 'devolvido_correcao')->count() === 1 ? 'documento' : 'documentos' }}
                            </div>
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Notice-->
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-6">
                            <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Proposições Devolvidas</h4>
                                    <div class="fs-6 text-gray-700">Os parlamentares solicitaram correções nos documentos abaixo. Revise as observações e faça as alterações necessárias.</div>
                                </div>
                            </div>
                        </div>
                        <!--end::Notice-->
                        
                        @foreach($proposicoes->where('status', 'devolvido_correcao') as $proposicao)
                        <!--begin::Item-->
                        <div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                            <!--begin::Details-->
                            <div class="d-flex align-items-center">
                                <!--begin::Avatar-->
                                <div class="symbol symbol-45px me-5">
                                    <div class="symbol-label fs-3 bg-light-danger text-danger fw-bold">
                                        {{ substr($proposicao->autor?->name ?? 'A', 0, 1) }}
                                    </div>
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Details-->
                                <div class="d-flex flex-column">
                                    <!--begin::Title-->
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge badge-light-dark fs-8 fw-bold me-3">{{ $proposicao->tipo }}</span>
                                        <a href="{{ route('proposicoes.show', $proposicao) }}" class="text-gray-900 fw-bold text-hover-primary fs-6">
                                            {{ $proposicao->autor?->name ?? 'Autor não informado' }}
                                        </a>
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Description-->
                                    <span class="text-muted fw-semibold fs-7 d-block text-start pe-10">
                                        {{ Str::limit($proposicao->ementa, 120) }}
                                    </span>
                                    <!--end::Description-->
                                    @if($proposicao->observacoes_retorno)
                                    <!--begin::Observations-->
                                    <div class="bg-light-danger rounded p-3 mt-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="ki-duotone ki-message-text-2 fs-6 text-danger me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <span class="fs-7 fw-bold text-danger">Observações:</span>
                                        </div>
                                        <span class="fs-7 text-gray-700">{{ $proposicao->observacoes_retorno }}</span>
                                    </div>
                                    <!--end::Observations-->
                                    @endif
                                </div>
                                <!--end::Details-->
                            </div>
                            <!--end::Details-->
                            <!--begin::Stats-->
                            <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">
                                <div class="d-flex justify-content-end w-100">
                                    <a href="/proposicoes/{{ $proposicao->id }}/onlyoffice/editor" class="btn btn-bg-light btn-color-danger btn-active-color-primary btn-sm px-4 me-2">Corrigir</a>
                                    <a href="{{ route('proposicoes.show', $proposicao) }}" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4">Visualizar</a>
                                </div>
                                <div class="d-flex justify-content-end w-100 mt-2">
                                    <span class="text-muted fs-7 fw-semibold">
                                        {{ $proposicao->data_retorno_legislativo ? \Carbon\Carbon::parse($proposicao->data_retorno_legislativo)->format('d/m/Y H:i') : 'Data não informada' }}
                                    </span>
                                </div>
                            </div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Item-->
                        @endforeach
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Urgent Section-->
                @endif

                <!--begin::Table-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Title-->
                            <h2 class="fw-bold">Proposições para Revisão</h2>
                            <!--end::Title-->
                        </div>
                        <!--end::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-proposicoes-table-toolbar="base">
                                <!--begin::Info-->
                                <span class="text-muted fs-7 fw-semibold me-4">Total: {{ $proposicoes->total() }} proposições</span>
                                <!--end::Info-->
                            </div>
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        @if($proposicoes->count() > 0)
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Proposição</th>
                                    <th class="min-w-125px">Autor</th>
                                    <th class="min-w-200px">Ementa</th>
                                    <th class="min-w-200px">Observações</th>
                                    <th class="min-w-100px">Data Envio</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach($proposicoes as $proposicao)
                                <!--begin::Table row-->
                                <tr>
                                    <!--begin::Proposicao-->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-light-dark fs-7 fw-bold mb-1">{{ $proposicao->tipo }}</span>
                                            @if($proposicao->numero_temporario)
                                            <span class="text-muted fw-semibold d-block fs-7">Nº {{ $proposicao->numero_temporario }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <!--end::Proposicao-->
                                    <!--begin::Autor-->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-900 fw-bold fs-6">{{ $proposicao->autor?->name ?? 'Autor não informado' }}</span>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ $proposicao->autor?->cargo_atual ?? 'Parlamentar' }}</span>
                                        </div>
                                    </td>
                                    <!--end::Autor-->
                                    <!--begin::Ementa-->
                                    <td>
                                        <div class="text-gray-900 fw-semibold" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $proposicao->ementa }}">
                                            {{ Str::limit($proposicao->ementa, 100) }}
                                        </div>
                                    </td>
                                    <!--end::Ementa-->
                                    <!--begin::Observacoes-->
                                    <td>
                                        @if($proposicao->status === 'devolvido_correcao' && $proposicao->observacoes_retorno)
                                            <div class="text-danger fw-semibold" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $proposicao->observacoes_retorno }}">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ Str::limit($proposicao->observacoes_retorno, 80) }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <!--end::Observacoes-->
                                    <!--begin::Data-->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-900 fw-bold fs-6">{{ $proposicao->created_at->format('d/m/Y') }}</span>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ $proposicao->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <!--end::Data-->
                                    <!--begin::Status-->
                                    <td>
                                        @if($proposicao->status === 'enviado_legislativo')
                                            <div class="badge badge-light-primary fw-bold">Aguardando Revisão</div>
                                        @elseif($proposicao->status === 'em_revisao')
                                            <div class="badge badge-light-warning fw-bold">Em Revisão</div>
                                        @elseif($proposicao->status === 'devolvido_correcao')
                                            <div class="badge badge-light-danger fw-bold">
                                                <i class="fas fa-arrow-left me-1"></i>Devolvido p/ Correção
                                            </div>
                                        @elseif($proposicao->status === 'aprovado')
                                            <div class="badge badge-light-success fw-bold">
                                                <i class="fas fa-check me-1"></i>Aprovado
                                            </div>
                                        @elseif($proposicao->status === 'aprovado_assinatura')
                                            <div class="badge badge-light-info fw-bold">
                                                <i class="fas fa-clock me-1"></i>Aguardando Assinatura
                                            </div>
                                        @elseif($proposicao->status === 'assinado')
                                            <div class="badge badge-light-success fw-bold">
                                                <i class="fas fa-signature me-1"></i>Assinado
                                            </div>
                                        @elseif($proposicao->status === 'protocolado')
                                            <div class="badge badge-success fw-bold">
                                                <i class="fas fa-stamp me-1"></i>Protocolado
                                            </div>
                                        @endif
                                    </td>
                                    <!--end::Status-->
                                    <!--begin::Action-->
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Ações
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="{{ route('proposicoes.show', $proposicao) }}" class="menu-link px-3">
                                                    <i class="ki-duotone ki-eye fs-4 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    Visualizar
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                            @if(auth()->user()->isLegislativo() && in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'devolvido_correcao']))
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="/proposicoes/{{ $proposicao->id }}/onlyoffice/editor" class="menu-link px-3">
                                                    <i class="ki-duotone ki-pencil fs-4 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    {{ $proposicao->status === 'devolvido_correcao' ? 'Corrigir' : 'Editar' }}
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                            @endif
                                            @if(auth()->user()->isAssessorJuridico() && in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'devolvido_correcao']))
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="{{ route('proposicoes.revisar.show', $proposicao) }}" class="menu-link px-3">
                                                    <i class="ki-duotone ki-check-square fs-4 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    {{ $proposicao->status === 'devolvido_correcao' ? 'Finalizar Correção' : 'Revisar' }}
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                            @endif
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                    <!--end::Action-->
                                </tr>
                                <!--end::Table row-->
                                @endforeach
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                        <!--begin::Pagination-->
                        <div class="d-flex flex-stack flex-wrap pt-10">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Mostrando {{ $proposicoes->firstItem() }} a {{ $proposicoes->lastItem() }} de {{ $proposicoes->total() }} registros
                            </div>
                            {{ $proposicoes->links() }}
                        </div>
                        <!--end::Pagination-->
                        @else
                        <!--begin::No data-->
                        <div class="text-center py-15">
                            <i class="ki-duotone ki-document fs-5x text-muted mb-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="text-gray-600 fs-5 mb-3">Nenhuma proposição para revisão</h3>
                            <div class="text-muted fs-7">Não há proposições aguardando análise técnica no momento.</div>
                        </div>
                        <!--end::No data-->
                        @endif
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Table-->
                
                <!--begin::Additional Stats-->
                <div class="row g-5 g-xl-8 mt-5">
                    <div class="col-xl-6">
                        <!--begin::Chart Card-->
                        <div class="card">
                            <div class="card-header border-0">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 mb-1">Proposições por Tipo</span>
                                    <span class="text-muted fw-semibold fs-7">Distribuição das proposições em análise</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                @if(isset($estatisticas['por_tipo']) && count($estatisticas['por_tipo']) > 0)
                                    @foreach($estatisticas['por_tipo'] as $tipo => $total)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bullet bg-primary me-3" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                                            <div class="flex-grow-1">
                                                <span class="text-gray-800 fw-bold">{{ strtoupper($tipo) }}</span>
                                                <span class="text-muted ms-2">({{ $total }} {{ $total == 1 ? 'proposição' : 'proposições' }})</span>
                                            </div>
                                            <span class="badge badge-light-primary">{{ $total }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-muted py-5">
                                        <i class="ki-duotone ki-chart-pie-simple fs-3x mb-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div>Nenhuma proposição em análise no momento</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!--end::Chart Card-->
                    </div>
                    
                    <div class="col-xl-6">
                        <!--begin::Stats Card-->
                        <div class="card">
                            <div class="card-header border-0">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 mb-1">Métricas de Desempenho</span>
                                    <span class="text-muted fw-semibold fs-7">Indicadores de eficiência</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <!--begin::Item-->
                                <div class="d-flex align-items-center bg-light-primary rounded p-5 mb-3">
                                    <span class="svg-icon svg-icon-primary me-5">
                                        <i class="ki-duotone ki-time fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <div class="flex-grow-1 me-2">
                                        <span class="fw-bold text-gray-800 fs-6">Tempo Médio de Revisão</span>
                                        <span class="text-muted fw-semibold d-block fs-7">Média de horas para análise</span>
                                    </div>
                                    <span class="fw-bold text-primary py-1 px-3">
                                        {{ isset($estatisticas['tempo_medio_revisao']) ? $estatisticas['tempo_medio_revisao'] . 'h' : 'N/A' }}
                                    </span>
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="d-flex align-items-center bg-light-warning rounded p-5 mb-3">
                                    <span class="svg-icon svg-icon-warning me-5">
                                        <i class="ki-duotone ki-flag fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <div class="flex-grow-1 me-2">
                                        <span class="fw-bold text-gray-800 fs-6">Proposições Urgentes</span>
                                        <span class="text-muted fw-semibold d-block fs-7">Requerem atenção prioritária</span>
                                    </div>
                                    <span class="fw-bold text-warning py-1 px-3">
                                        {{ isset($estatisticas['urgentes']) ? $estatisticas['urgentes'] : '0' }}
                                    </span>
                                </div>
                                <!--end::Item-->
                                
                                <!--begin::Item-->
                                <div class="d-flex align-items-center bg-light-success rounded p-5">
                                    <span class="svg-icon svg-icon-success me-5">
                                        <i class="ki-duotone ki-chart-line-up fs-2x">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <div class="flex-grow-1 me-2">
                                        <span class="fw-bold text-gray-800 fs-6">Taxa de Conclusão</span>
                                        <span class="text-muted fw-semibold d-block fs-7">Proposições finalizadas este mês</span>
                                    </div>
                                    <span class="fw-bold text-success py-1 px-3">
                                        @php
                                            $finalizadas = $allProposicoes->whereIn('status', ['aprovado_assinatura', 'devolvido_correcao', 'retornado_legislativo'])
                                                ->filter(function($p) { return $p->updated_at->isCurrentMonth(); })
                                                ->count();
                                            $taxa = isset($estatisticas['total_mes']) && $estatisticas['total_mes'] > 0 
                                                ? round(($finalizadas / $estatisticas['total_mes']) * 100) 
                                                : 0;
                                        @endphp
                                        {{ $taxa }}%
                                    </span>
                                </div>
                                <!--end::Item-->
                            </div>
                        </div>
                        <!--end::Stats Card-->
                    </div>
                </div>
                <!--end::Additional Stats-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<!--end::Post-->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('[data-control="select2"]').select2({
        minimumResultsForSearch: -1
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Search functionality
    $('#btn-buscar').on('click', function() {
        const search = $('#search-proposicoes').val();
        if (search) {
            const params = new URLSearchParams(window.location.search);
            params.set('search', search);
            window.location.search = params.toString();
        }
    });

    $('#search-proposicoes').on('keypress', function(e) {
        if (e.which === 13) {
            $('#btn-buscar').click();
        }
    });

    // Advanced filters
    $('#btn-filtrar').on('click', function() {
        aplicarFiltros();
    });

    $('#btn-limpar').on('click', function() {
        $('#filtro-status').val('').trigger('change');
        $('#filtro-tipo').val('').trigger('change');
        $('#filtro-autor').val('');
        $('#search-proposicoes').val('');
        aplicarFiltros();
    });

    function aplicarFiltros() {
        const params = new URLSearchParams(window.location.search);
        
        const search = $('#search-proposicoes').val();
        const status = $('#filtro-status').val();
        const tipo = $('#filtro-tipo').val();
        const autor = $('#filtro-autor').val();

        if (search) params.set('search', search);
        else params.delete('search');

        if (status) params.set('status', status);
        else params.delete('status');

        if (tipo) params.set('tipo', tipo);
        else params.delete('tipo');

        if (autor) params.set('autor', autor);
        else params.delete('autor');

        params.delete('page'); // Reset pagination

        window.location.search = params.toString();
    }

    // Apply filters from URL
    const urlParams = new URLSearchParams(window.location.search);
    $('#search-proposicoes').val(urlParams.get('search') || '');
    $('#filtro-status').val(urlParams.get('status') || '').trigger('change');
    $('#filtro-tipo').val(urlParams.get('tipo') || '').trigger('change');
    $('#filtro-autor').val(urlParams.get('autor') || '');

    // Auto-refresh every 60 seconds for real-time status
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 60000);
});
</script>
@endpush