@extends('components.layouts.app')

@section('title', 'Relatório de Produtividade')

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
                        Relatório de Produtividade
                        <span class="fs-6 text-gray-500 fw-semibold ms-1">Proposições revisadas por período</span>
                    </h3>
                    <!--end::Heading-->
                    <!--begin::Actions-->
                    <div class="d-flex flex-wrap my-2">
                        <a href="{{ route('proposicoes.legislativo.index') }}" class="btn btn-light-primary">
                            <i class="ki-duotone ki-arrow-left fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Voltar
                        </a>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Header-->

                <!--begin::Filters Card-->
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Filtros para Relatório</h3>
                    </div>
                    <div class="card-body">
                        <form id="relatorio-form">
                            <div class="row g-5">
                                <!--begin::Período-->
                                <div class="col-md-3">
                                    <label class="fs-6 fw-semibold form-label">Período</label>
                                    <select class="form-select" id="periodo" name="periodo">
                                        <option value="hoje">Hoje</option>
                                        <option value="mes_atual" selected>Mês Atual</option>
                                        <option value="ano_atual">Ano Atual</option>
                                        <option value="personalizado">Período Personalizado</option>
                                    </select>
                                </div>
                                <!--end::Período-->

                                <!--begin::Data Início-->
                                <div class="col-md-3" id="data-inicio-group" style="display: none;">
                                    <label class="fs-6 fw-semibold form-label">Data Início</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio">
                                </div>
                                <!--end::Data Início-->

                                <!--begin::Data Fim-->
                                <div class="col-md-3" id="data-fim-group" style="display: none;">
                                    <label class="fs-6 fw-semibold form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim">
                                </div>
                                <!--end::Data Fim-->

                                <!--begin::Usuário-->
                                <div class="col-md-3">
                                    <label class="fs-6 fw-semibold form-label">Usuário</label>
                                    <select class="form-select" id="usuario" name="usuario">
                                        <option value="">Todos os usuários</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Usuário-->
                            </div>
                        </form>
                    </div>
                </div>
                <!--end::Filters Card-->

                <!--begin::Actions Card-->
                <div class="card mb-5">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-primary" id="gerar-pdf">
                                <i class="ki-duotone ki-file-down fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Gerar PDF
                            </button>
                            
                            <button type="button" class="btn btn-success" id="gerar-excel">
                                <i class="ki-duotone ki-file-down fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Gerar Excel
                            </button>
                            
                            <button type="button" class="btn btn-info" id="visualizar-dados">
                                <i class="ki-duotone ki-eye fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Visualizar Dados
                            </button>
                        </div>
                    </div>
                </div>
                <!--end::Actions Card-->

                <!--begin::Preview Card-->
                <div class="card" id="preview-card" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title">Preview dos Dados</h3>
                    </div>
                    <div class="card-body" id="preview-content">
                        <!-- Conteúdo será carregado via AJAX -->
                    </div>
                </div>
                <!--end::Preview Card-->
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
    // Toggle date fields for custom period
    $('#periodo').on('change', function() {
        const periodo = $(this).val();
        if (periodo === 'personalizado') {
            $('#data-inicio-group, #data-fim-group').show();
        } else {
            $('#data-inicio-group, #data-fim-group').hide();
        }
    });

    // Visualizar dados
    $('#visualizar-dados').on('click', function() {
        const formData = $('#relatorio-form').serialize();
        
        $.ajax({
            url: '{{ route("proposicoes.relatorio-legislativo.dados") }}',
            type: 'GET',
            data: formData,
            success: function(response) {
                $('#preview-content').html(response);
                $('#preview-card').show();
            },
            error: function() {
                Swal.fire('Erro', 'Erro ao carregar dados do relatório', 'error');
            }
        });
    });

    // Gerar PDF
    $('#gerar-pdf').on('click', function() {
        const formData = $('#relatorio-form').serialize();
        window.open('{{ route("proposicoes.relatorio-legislativo.pdf") }}?' + formData, '_blank');
    });

    // Gerar Excel
    $('#gerar-excel').on('click', function() {
        const formData = $('#relatorio-form').serialize();
        window.location.href = '{{ route("proposicoes.relatorio-legislativo.excel") }}?' + formData;
    });

    // Set default dates for custom period
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    $('#data_inicio').val(firstDay.toISOString().split('T')[0]);
    $('#data_fim').val(today.toISOString().split('T')[0]);
});
</script>
@endpush