@extends('components.layouts.app')

@section('title', 'Diagnóstico do Banco de Dados')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-6">
        <div class="app-container container-xxl d-flex align-items-start">
            <div class="d-flex flex-column flex-row-fluid">
                <div class="d-flex align-items-center pt-1">
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 fs-lg-6">
                        <li class="breadcrumb-item text-gray-700 fw-bold lh-1">
                            <a href="{{ route('dashboard') }}" class="text-gray-700 text-hover-primary">
                                <i class="ki-duotone ki-home text-gray-700 fs-6 fs-lg-5"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
                        </li>
                        <li class="breadcrumb-item text-gray-700 fw-bold lh-1 d-none d-sm-block">
                            <a href="{{ route('admin.system-diagnostic.index') }}" class="text-gray-700 text-hover-primary">
                                Diagnóstico
                            </a>
                        </li>
                        <li class="breadcrumb-item d-none d-sm-block">
                            <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
                        </li>
                        <li class="breadcrumb-item text-gray-800 fw-bold lh-1">
                            <span class="d-inline d-sm-none">Banco de Dados</span>
                            <span class="d-none d-sm-inline">Banco de Dados</span>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center py-2">
                    <h1 class="text-gray-900 fw-bold fs-3 fs-lg-2 fs-xl-1 my-0">
                        <span class="d-inline d-lg-none">Banco de Dados</span>
                        <span class="d-none d-lg-inline">Diagnóstico do Banco de Dados</span>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-xxl">
            
            <!-- Informações do Banco -->
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-800 fs-3 fs-lg-2">
                                    <i class="ki-duotone ki-database fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Informações do Banco de Dados
                                </h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ route('admin.system-diagnostic.index') }}" class="btn btn-sm btn-light">
                                    <i class="ki-duotone ki-arrow-left fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Voltar
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-6 p-lg-9">
                            <div class="row g-5">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex flex-column bg-light-primary p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Driver</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ strtoupper($databaseInfo['driver']) }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex flex-column bg-light-info p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Host</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ $databaseInfo['host'] }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex flex-column bg-light-success p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Banco</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ $databaseInfo['database'] }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex flex-column bg-light-warning p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Porta</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ $databaseInfo['port'] }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex flex-column bg-light-secondary p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Charset</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ $databaseInfo['charset'] }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex flex-column bg-light-danger p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Total de Tabelas</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ count($tables) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Tabelas -->
            <div class="row g-5 g-xl-8">
                <div class="col-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-800 fs-3 fs-lg-2">
                                    <i class="ki-duotone ki-row-horizontal fs-2 text-success me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Tabelas do Banco de Dados
                                </h3>
                            </div>
                        </div>
                        <div class="card-body p-6 p-lg-9">
                            @if(count($tables) > 0)
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300 gy-5" id="tables-table">
                                        <thead>
                                            <tr class="fw-semibold fs-7 fs-lg-6 text-gray-800">
                                                <th class="min-w-200px">
                                                    <i class="ki-duotone ki-category fs-2 text-primary me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                    </i>
                                                    Nome da Tabela
                                                </th>
                                                <th class="min-w-100px text-center">
                                                    <i class="ki-duotone ki-chart fs-2 text-info me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Registros
                                                </th>
                                                <th class="min-w-100px text-center d-none d-lg-table-cell">
                                                    <i class="ki-duotone ki-size fs-2 text-warning me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Tamanho
                                                </th>
                                                <th class="min-w-100px text-center d-none d-xl-table-cell">
                                                    <i class="ki-duotone ki-gear fs-2 text-success me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Engine
                                                </th>
                                                <th class="min-w-120px text-center d-none d-xl-table-cell">
                                                    <i class="ki-duotone ki-calendar fs-2 text-danger me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    Criada em
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tables as $table)
                                                <tr>
                                                    <td class="fs-7 fs-lg-6">
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-30px me-3">
                                                                <div class="symbol-label bg-light-primary">
                                                                    <i class="ki-duotone ki-row-horizontal fs-3 text-primary">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                </div>
                                                            </div>
                                                            <a href="{{ route('admin.system-diagnostic.table', $table['name']) }}" 
                                                               class="fw-bold text-gray-800 text-hover-primary text-decoration-none">
                                                                {{ $table['name'] }}
                                                                <i class="ki-duotone ki-arrow-right fs-5 text-gray-400 ms-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td class="text-center fs-7 fs-lg-6">
                                                        @if(in_array($table['rows'], ['N/A', 'Erro', 'Sem acesso', 'Sem permissão']))
                                                            @if(in_array($table['rows'], ['Erro', 'Sem acesso', 'Sem permissão']))
                                                                <span class="badge badge-light-warning fs-8">{{ $table['rows'] }}</span>
                                                            @else
                                                                <span class="badge badge-light-secondary fs-8">N/A</span>
                                                            @endif
                                                        @else
                                                            <span class="fw-bold text-gray-800">{{ number_format($table['rows']) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fs-7 fs-lg-6 d-none d-lg-table-cell">
                                                        @if(in_array($table['size'], ['N/A', 'Não disponível']))
                                                            @if($table['size'] === 'Não disponível')
                                                                <span class="badge badge-light-info fs-8">{{ $table['size'] }}</span>
                                                            @else
                                                                <span class="badge badge-light-secondary fs-8">N/A</span>
                                                            @endif
                                                        @else
                                                            <span class="fw-bold text-gray-800">{{ $table['size'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fs-7 fs-lg-6 d-none d-xl-table-cell">
                                                        @if($table['engine'] === 'N/A')
                                                            <span class="badge badge-light-secondary fs-8">N/A</span>
                                                        @else
                                                            <span class="badge badge-light-success fs-8">{{ $table['engine'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fs-7 fs-lg-6 d-none d-xl-table-cell">
                                                        @if($table['created'] === 'N/A')
                                                            <span class="badge badge-light-secondary fs-8">N/A</span>
                                                        @else
                                                            <span class="text-gray-600">{{ $table['created'] }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="mb-5">
                                        <i class="ki-duotone ki-information fs-5x text-muted">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                    <h3 class="text-gray-600 fw-semibold mb-2">Nenhuma tabela encontrada</h3>
                                    <p class="text-gray-400">Não foi possível encontrar tabelas no banco de dados.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable se existir tabelas
            @if(count($tables) > 0)
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                $('#tables-table').DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[0, 'asc']],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
                    }
                });
            }
            @endif
        });
    </script>
@endsection