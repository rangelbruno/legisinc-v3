@extends('components.layouts.app')

@section('title', 'Registros da Tabela: ' . $table)

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
                        <li class="breadcrumb-item text-gray-700 fw-bold lh-1">
                            <a href="{{ route('admin.system-diagnostic.index') }}" class="text-gray-700 text-hover-primary">
                                Diagnóstico
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
                        </li>
                        <li class="breadcrumb-item text-gray-700 fw-bold lh-1">
                            <a href="{{ route('admin.system-diagnostic.database') }}" class="text-gray-700 text-hover-primary">
                                Banco de Dados
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
                        </li>
                        <li class="breadcrumb-item text-gray-800 fw-bold lh-1">
                            {{ $table }}
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center py-2">
                    <h1 class="text-gray-900 fw-bold fs-3 fs-lg-2 fs-xl-1 my-0">
                        <span class="d-inline d-lg-none">{{ $table }}</span>
                        <span class="d-none d-lg-inline">Tabela: {{ $table }}</span>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-xxl">
            
            <!-- Informações da Tabela -->
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-800 fs-3 fs-lg-2">
                                    <i class="ki-duotone ki-row-horizontal fs-2 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Informações da Tabela
                                </h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ route('admin.system-diagnostic.database') }}" class="btn btn-sm btn-light me-2">
                                    <i class="ki-duotone ki-arrow-left fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Voltar
                                </a>
                                <button onclick="location.reload()" class="btn btn-sm btn-primary">
                                    <i class="ki-duotone ki-arrows-circle fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Atualizar
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-6 p-lg-9">
                            <div class="row g-5">
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="d-flex flex-column bg-light-primary p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Nome da Tabela</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ $tableInfo['name'] }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="d-flex flex-column bg-light-success p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Total de Registros</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ number_format($tableInfo['rows']) }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="d-flex flex-column bg-light-info p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Tamanho</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ $tableInfo['size'] }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div class="d-flex flex-column bg-light-warning p-4 rounded">
                                        <span class="text-gray-600 fw-semibold fs-7 mb-1">Colunas</span>
                                        <span class="text-gray-800 fw-bold fs-6">{{ count($columns) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estrutura da Tabela -->
            @if(count($columns) > 0)
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-800 fs-4 fs-lg-3">
                                    <i class="ki-duotone ki-setting-4 fs-3 text-info me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Estrutura da Tabela
                                </h3>
                            </div>
                        </div>
                        <div class="card-body p-6 p-lg-9">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 gy-3">
                                    <thead>
                                        <tr class="fw-semibold fs-7 fs-lg-6 text-gray-800">
                                            <th>Coluna</th>
                                            <th>Tipo</th>
                                            <th class="text-center">Nulo</th>
                                            <th class="d-none d-lg-table-cell">Padrão</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($columns as $column)
                                            <tr>
                                                <td class="fs-7 fs-lg-6">
                                                    <span class="fw-bold text-gray-800">{{ $column['name'] }}</span>
                                                    @if(isset($column['key']) && $column['key'] === 'PRI')
                                                        <span class="badge badge-light-primary fs-8 ms-2">PK</span>
                                                    @elseif(isset($column['pk']) && $column['pk'])
                                                        <span class="badge badge-light-primary fs-8 ms-2">PK</span>
                                                    @endif
                                                </td>
                                                <td class="fs-7 fs-lg-6">
                                                    <code class="text-gray-700">{{ $column['type'] }}</code>
                                                </td>
                                                <td class="text-center fs-7 fs-lg-6">
                                                    @if($column['nullable'])
                                                        <span class="badge badge-light-success fs-8">Sim</span>
                                                    @else
                                                        <span class="badge badge-light-danger fs-8">Não</span>
                                                    @endif
                                                </td>
                                                <td class="fs-7 fs-lg-6 d-none d-lg-table-cell">
                                                    @if($column['default'])
                                                        <code class="text-gray-600">{{ $column['default'] }}</code>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Registros da Tabela -->
            <div class="row g-5 g-xl-8">
                <div class="col-12">
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-800 fs-4 fs-lg-3">
                                    <i class="ki-duotone ki-chart fs-3 text-success me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Dados da Tabela
                                </h3>
                                <span class="badge badge-light-primary fs-7 ms-3">
                                    Página {{ $page }} de {{ $totalPages }} 
                                    ({{ number_format($totalRecords) }} registros)
                                </span>
                            </div>
                            <div class="card-toolbar">
                                <!-- Paginação -->
                                <div class="d-flex align-items-center">
                                    @if($hasPrev)
                                        <a href="?page={{ $page - 1 }}" class="btn btn-sm btn-light me-2">
                                            <i class="ki-duotone ki-arrow-left fs-3"></i>
                                            Anterior
                                        </a>
                                    @endif
                                    
                                    @if($hasNext)
                                        <a href="?page={{ $page + 1 }}" class="btn btn-sm btn-light">
                                            Próxima
                                            <i class="ki-duotone ki-arrow-right fs-3"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-6 p-lg-9">
                            @if(count($records) > 0)
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300 gy-3" id="records-table">
                                        <thead>
                                            <tr class="fw-semibold fs-7 fs-lg-6 text-gray-800">
                                                @foreach($columns as $column)
                                                    <th class="min-w-100px">{{ $column['name'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($records as $record)
                                                <tr>
                                                    @foreach($columns as $column)
                                                        <td class="fs-7 fs-lg-6">
                                                            @php
                                                                $value = $record->{$column['name']} ?? '';
                                                                $displayValue = $value;
                                                                
                                                                // Formatação especial para diferentes tipos de dados
                                                                if (is_null($value)) {
                                                                    $displayValue = '<span class="text-muted fst-italic">NULL</span>';
                                                                } elseif (is_bool($value)) {
                                                                    $displayValue = $value ? '<span class="badge badge-light-success fs-8">true</span>' : '<span class="badge badge-light-danger fs-8">false</span>';
                                                                } elseif (is_numeric($value)) {
                                                                    $displayValue = '<span class="text-primary fw-bold">' . $value . '</span>';
                                                                } elseif (strlen($value) > 50) {
                                                                    $displayValue = '<span class="text-gray-700">' . substr(htmlspecialchars($value), 0, 50) . '...</span>';
                                                                } else {
                                                                    $displayValue = '<span class="text-gray-700">' . htmlspecialchars($value) . '</span>';
                                                                }
                                                            @endphp
                                                            {!! $displayValue !!}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Paginação inferior -->
                                @if($totalPages > 1)
                                <div class="d-flex align-items-center justify-content-between mt-5">
                                    <div class="text-gray-600 fs-7">
                                        Mostrando {{ ($page - 1) * $perPage + 1 }} a {{ min($page * $perPage, $totalRecords) }} 
                                        de {{ number_format($totalRecords) }} registros
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        @if($hasPrev)
                                            <a href="?page=1" class="btn btn-sm btn-light me-2">Primeira</a>
                                            <a href="?page={{ $page - 1 }}" class="btn btn-sm btn-primary me-2">Anterior</a>
                                        @endif
                                        
                                        <span class="text-gray-600 mx-3">Página {{ $page }} de {{ $totalPages }}</span>
                                        
                                        @if($hasNext)
                                            <a href="?page={{ $page + 1 }}" class="btn btn-sm btn-primary me-2">Próxima</a>
                                            <a href="?page={{ $totalPages }}" class="btn btn-sm btn-light">Última</a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @else
                                <div class="text-center py-10">
                                    <div class="mb-5">
                                        <i class="ki-duotone ki-information fs-5x text-muted">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                    <h3 class="text-gray-600 fw-semibold mb-2">Nenhum registro encontrado</h3>
                                    <p class="text-gray-400">Esta tabela não possui registros ou você não tem permissão para visualizá-los.</p>
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
            // Melhorar a responsividade da tabela em mobile
            const recordsTable = document.getElementById('records-table');
            if (recordsTable && window.innerWidth < 768) {
                recordsTable.style.fontSize = '12px';
            }
            
            // Auto-refresh opcional (comentado por padrão)
            // setInterval(() => {
            //     location.reload();
            // }, 30000); // 30 segundos
        });
    </script>
@endsection