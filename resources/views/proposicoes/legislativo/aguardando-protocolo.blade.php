@extends('components.layouts.app')

@section('title', 'Proposições Aguardando Protocolo')

@use('Illuminate\Support\Str')

@section('content')
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
                        Proposições Aguardando Protocolo
                        <span class="fs-6 text-gray-500 fw-semibold ms-1">Monitoramento de proposições já enviadas ao setor de protocolo</span>
                    </h3>
                    <!--end::Heading-->
                    <!--begin::Actions-->
                    <div class="d-flex flex-wrap my-2">
                        <a href="{{ route('proposicoes.legislativo.index') }}" class="btn btn-light-primary me-3">
                            <i class="ki-duotone ki-arrow-left fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Voltar
                        </a>
                        
                        <button type="button" class="btn btn-light-success" id="btn-exportar">
                            <i class="ki-duotone ki-file-down fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Exportar Lista
                        </button>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Header-->

                <!--begin::Statistics Cards-->
                <div class="row g-5 g-xl-8 mb-5">
                    <!--begin::Card 1-->
                    <div class="col-xl-4 col-md-6">
                        <div class="card bg-light-success">
                            <div class="card-body text-center py-8">
                                <i class="ki-duotone ki-check-circle fs-3x text-success mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h3 class="text-success">{{ $proposicoes->total() }}</h3>
                                <p class="text-muted mb-0">No Protocolo</p>
                                <small class="text-muted">Proposições enviadas</small>
                            </div>
                        </div>
                    </div>
                    <!--end::Card 1-->

                    <!--begin::Card 2-->
                    <div class="col-xl-4 col-md-6">
                        <div class="card bg-light-warning">
                            <div class="card-body text-center py-8">
                                <i class="ki-duotone ki-timer fs-3x text-warning mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <h3 class="text-warning">
                                    @php
                                        $maisAntiga = $proposicoes->where('updated_at', $proposicoes->min('updated_at'))->first();
                                        $diasEspera = $maisAntiga ? $maisAntiga->updated_at->diffInDays(now()) : 0;
                                    @endphp
                                    {{ $diasEspera }}
                                </h3>
                                <p class="text-muted mb-0">Dias de Espera</p>
                                <small class="text-muted">Proposição mais antiga</small>
                            </div>
                        </div>
                    </div>
                    <!--end::Card 2-->

                    <!--begin::Card 3-->
                    <div class="col-xl-4 col-md-6">
                        <div class="card bg-light-info">
                            <div class="card-body text-center py-8">
                                <i class="ki-duotone ki-calendar-8 fs-3x text-info mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                                <h3 class="text-info">{{ $proposicoes->where('updated_at', '>=', now()->startOfMonth())->count() }}</h3>
                                <p class="text-muted mb-0">Este Mês</p>
                                <small class="text-muted">Proposições assinadas</small>
                            </div>
                        </div>
                    </div>
                    <!--end::Card 3-->
                </div>
                <!--end::Statistics Cards-->

                <!--begin::Table Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" id="search-proposicoes" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar proposições...">
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--end::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-proposicoes-table-toolbar="base">
                                <!--begin::Filter-->
                                <div class="w-150px me-3">
                                    <select class="form-select form-select-solid" id="filtro-tipo">
                                        <option value="">Todos os tipos</option>
                                        <option value="PL">Projeto de Lei</option>
                                        <option value="PLP">Projeto de Lei Complementar</option>
                                        <option value="PEC">Proposta de Emenda Constitucional</option>
                                        <option value="PDC">Projeto de Decreto Legislativo</option>
                                        <option value="PRC">Projeto de Resolução</option>
                                        <option value="mocao">Moção</option>
                                    </select>
                                </div>
                                <!--end::Filter-->
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
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="proposicoes-table">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Proposição</th>
                                    <th class="min-w-125px">Autor</th>
                                    <th class="min-w-200px">Ementa</th>
                                    <th class="min-w-100px">Data Assinatura</th>
                                    <th class="min-w-100px">Tempo Espera</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach($proposicoes as $proposicao)
                                <!--begin::Table row-->
                                <tr class="proposicao-row" data-tipo="{{ $proposicao->tipo }}">
                                    <!--begin::Proposicao-->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-light-dark fs-7 fw-bold mb-1">{{ strtoupper($proposicao->tipo) }}</span>
                                            <a href="{{ route('proposicoes.show', $proposicao) }}" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6">
                                                {{ $proposicao->titulo ?? 'Proposição #' . $proposicao->id }}
                                            </a>
                                            @if($proposicao->numero_temporario)
                                            <span class="text-muted fw-semibold d-block fs-7">Nº {{ $proposicao->numero_temporario }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <!--end::Proposicao-->
                                    <!--begin::Autor-->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-900 fw-bold fs-6">{{ $proposicao->autor->name }}</span>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</span>
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
                                    <!--begin::Data Assinatura-->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-900 fw-bold fs-6">{{ $proposicao->updated_at->format('d/m/Y') }}</span>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ $proposicao->updated_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <!--end::Data Assinatura-->
                                    <!--begin::Tempo Espera-->
                                    <td>
                                        @php
                                            $diasEspera = $proposicao->updated_at->diffInDays(now());
                                            $corBadge = $diasEspera > 7 ? 'danger' : ($diasEspera > 3 ? 'warning' : 'success');
                                        @endphp
                                        <div class="badge badge-light-{{ $corBadge }} fw-bold">
                                            {{ $diasEspera }} {{ $diasEspera == 1 ? 'dia' : 'dias' }}
                                        </div>
                                    </td>
                                    <!--end::Tempo Espera-->
                                    <!--begin::Action-->
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            Ações
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="{{ route('proposicoes.show', $proposicao) }}" class="menu-link px-3">
                                                    <i class="ki-duotone ki-eye fs-4 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    Ver Detalhes
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
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
                            <i class="ki-duotone ki-file-deleted fs-5x text-muted mb-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="text-gray-600 fs-5 mb-3">Nenhuma proposição aguardando protocolo</h3>
                            <div class="text-muted fs-7">Todas as proposições foram enviadas para o protocolo ou ainda não foram assinadas.</div>
                        </div>
                        <!--end::No data-->
                        @endif
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Table Card-->
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
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Search functionality
    $('#search-proposicoes').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.proposicao-row').each(function() {
            const text = $(this).text().toLowerCase();
            if (text.indexOf(searchTerm) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Filter by type
    $('#filtro-tipo').on('change', function() {
        const selectedType = $(this).val().toLowerCase();
        $('.proposicao-row').each(function() {
            const rowType = $(this).data('tipo').toLowerCase();
            if (selectedType === '' || rowType === selectedType) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });


    // Export functionality
    $('#btn-exportar').on('click', function() {
        // Create CSV content
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Tipo,Título,Autor,Ementa,Data Assinatura,Dias Espera\n";
        
        $('.proposicao-row:visible').each(function() {
            const cells = $(this).find('td');
            const tipo = $(cells[0]).find('.badge').text().trim();
            const titulo = $(cells[0]).find('a').text().trim();
            const autor = $(cells[1]).find('.text-gray-900').text().trim();
            const ementa = $(cells[2]).text().trim().replace(/"/g, '""');
            const dataAssinatura = $(cells[3]).find('.text-gray-900').text().trim();
            const diasEspera = $(cells[4]).find('.badge').text().trim();
            
            csvContent += `"${tipo}","${titulo}","${autor}","${ementa}","${dataAssinatura}","${diasEspera}"\n`;
        });
        
        // Download CSV
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "proposicoes_aguardando_protocolo_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        Swal.fire('Sucesso!', 'Lista exportada com sucesso!', 'success');
    });
});
</script>
@endpush