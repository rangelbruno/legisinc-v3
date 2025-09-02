@extends('components.layouts.app')

@section('title', 'Tabela: ' . $table)

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-technology-3 fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Tabela: {{ $table }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.database.index') }}" class="text-muted text-hover-primary">Banco de Dados</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $table }}</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('admin.database.index') }}" class="btn btn-sm fw-bold btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>
                    Voltar às Tabelas
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(isset($error))
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-information fs-2hx text-danger me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-dark">Erro ao Acessar Tabela</h4>
                    <span>{{ $error }}</span>
                    <small class="text-muted mt-1">Verifique se o banco de dados está acessível e a tabela existe.</small>
                </div>
            </div>
            @elseif(is_countable($data) && $data->count() > 0)
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-information-2 fs-2 position-absolute ms-4 text-gray-500">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <span class="ms-10 text-gray-700 fw-semibold fs-6">
                                Total de registros: <strong class="text-gray-800">{{ $data->total() }}</strong>
                                | Colunas: <strong class="text-gray-800">{{ count($columns) }}</strong>
                            </span>
                        </div>
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <div class="text-gray-600 fw-semibold fs-6">
                            Mostrando {{ $data->firstItem() }}-{{ $data->lastItem() }} de {{ $data->total() }} registros
                        </div>
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                
                <!--begin::Card body-->
                <div class="card-body py-3">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fw-bold text-muted">
                                    @foreach($columns as $column)
                                    <th class="min-w-150px">{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                            @foreach($data as $row)
                            <tr>
                                @foreach($columns as $column)
                                <td class="text-gray-800 fw-bold fs-6">
                                    @php
                                        $value = $row->$column ?? '';
                                        
                                        // Detectar se é timestamp
                                        if (in_array($column, ['created_at', 'updated_at', 'deleted_at']) && !empty($value)) {
                                            $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y H:i:s');
                                        }
                                        // Detectar se é JSON
                                        elseif (is_string($value) && (str_starts_with($value, '{') || str_starts_with($value, '['))) {
                                            $displayValue = '<code class="bg-gray-100 px-2 py-1 rounded text-xs">' . Str::limit($value, 50) . '</code>';
                                        }
                                        // Detectar se é boolean
                                        elseif (is_bool($value) || in_array($value, [0, 1, '0', '1']) && in_array($column, ['ativo', 'status', 'is_', 'has_', 'can_'])) {
                                            $displayValue = $value ? 
                                                '<span class="badge badge-light-success">Sim</span>' : 
                                                '<span class="badge badge-light-danger">Não</span>';
                                        }
                                        // Detectar se é ID de referência
                                        elseif (str_ends_with($column, '_id') && is_numeric($value) && $value > 0) {
                                            $displayValue = '<span class="text-primary fw-bold">#' . $value . '</span>';
                                        }
                                        // Detectar se é email
                                        elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                            $displayValue = '<a href="mailto:' . $value . '" class="text-primary text-hover-primary">' . $value . '</a>';
                                        }
                                        // Detectar se é URL
                                        elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                                            $displayValue = '<a href="' . $value . '" target="_blank" class="text-primary text-hover-primary">' . Str::limit($value, 30) . '</a>';
                                        }
                                        // Texto longo
                                        elseif (strlen($value) > 100) {
                                            $displayValue = '<span data-bs-toggle="tooltip" title="' . htmlspecialchars($value) . '">' . Str::limit($value, 100) . '</span>';
                                        }
                                        // Valor padrão
                                        else {
                                            $displayValue = $value;
                                        }
                                    @endphp
                                    
                                    @if($value === null || $value === '')
                                        <span class="text-muted fst-italic">null</span>
                                    @else
                                        {!! $displayValue !!}
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table container-->
                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer py-4">
                    {{ $data->links() }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->

            @elseif(!isset($error))
            <!--begin::Empty state-->
            <div class="card">
                <div class="card-body text-center py-20">
                    <i class="ki-duotone ki-database fs-5x text-gray-300 mb-10">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h3 class="text-gray-800 fs-2 fw-bold mb-3">Tabela vazia</h3>
                    <p class="text-gray-600 fs-6 fw-semibold mb-0">A tabela "{{ $table }}" não possui dados.</p>
                </div>
            </div>
            <!--end::Empty state-->
            @endif

            @if(!isset($error))
            <!--begin::Info Card-->
            <div class="card mt-5">
                <div class="card-header">
                    <div class="card-title">
                        <i class="ki-duotone ki-information-2 fs-2 text-primary me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <h3 class="fw-bold">Informações da Tabela</h3>
                    </div>
                </div>
                <div class="card-body pt-5">
                    <div class="row g-5">
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center">
                                <span class="fw-semibold text-gray-800 fs-6 me-2">Nome:</span>
                                <span class="badge badge-light-primary">{{ $table }}</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center">
                                <span class="fw-semibold text-gray-800 fs-6 me-2">Colunas:</span>
                                <span class="badge badge-light-info">{{ count($columns) }}</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center">
                                <span class="fw-semibold text-gray-800 fs-6 me-2">Registros:</span>
                                <span class="badge badge-light-success">{{ is_countable($data) && method_exists($data, 'total') ? $data->total() : 0 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if(count($columns) > 0)
                    <div class="separator my-5"></div>
                    <div>
                        <h4 class="fw-semibold text-gray-800 fs-6 mb-3">Colunas disponíveis:</h4>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($columns as $column)
                            <span class="badge badge-light">{{ $column }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <!--end::Info Card-->
            @endif

        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection