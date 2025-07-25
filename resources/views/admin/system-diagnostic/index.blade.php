@extends('components.layouts.app')

@section('title', 'Diagnóstico do Sistema')

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
                        <li class="breadcrumb-item text-gray-700 fw-bold lh-1 d-none d-sm-block">Administração</li>
                        <li class="breadcrumb-item d-none d-sm-block">
                            <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
                        </li>
                        <li class="breadcrumb-item text-gray-800 fw-bold lh-1">
                            <span class="d-inline d-sm-none">Diagnóstico</span>
                            <span class="d-none d-sm-inline">Diagnóstico do Sistema</span>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center py-2">
                    <h1 class="text-gray-900 fw-bold fs-3 fs-lg-2 fs-xl-1 my-0">
                        <span class="d-inline d-lg-none">Diagnóstico</span>
                        <span class="d-none d-lg-inline">Diagnóstico do Sistema</span>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-xxl">
                <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                    @foreach($diagnostics as $name => $diagnostic)
                        <div class="col-12 col-lg-6 col-xxl-4">
                            <div class="card card-flush h-100">
                                <div class="card-body p-6 p-lg-9">
                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4 mb-lg-5">
                                        @if($name === 'database' && $diagnostic['status'] === 'success')
                                            <a href="{{ route('admin.system-diagnostic.database') }}" class="text-decoration-none">
                                                <h3 class="card-title fw-bold text-gray-800 fs-3 fs-lg-2 mb-2 mb-sm-0 text-hover-primary">
                                                    {{ ucfirst(str_replace('_', ' ', $name)) }}
                                                    <i class="ki-duotone ki-arrow-right fs-3 ms-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </h3>
                                            </a>
                                        @else
                                            <h3 class="card-title fw-bold text-gray-800 fs-3 fs-lg-2 mb-2 mb-sm-0">
                                                {{ ucfirst(str_replace('_', ' ', $name)) }}
                                            </h3>
                                        @endif
                                        <span class="badge fs-8 fs-lg-7 fw-bold
                                            @if($diagnostic['status'] === 'success') badge-light-success
                                            @elseif($diagnostic['status'] === 'error') badge-light-danger
                                            @elseif($diagnostic['status'] === 'warning') badge-light-warning
                                            @else badge-light-secondary
                                            @endif">
                                            {{ ucfirst($diagnostic['status']) }}
                                        </span>
                                    </div>

                                    <p class="text-gray-700 fs-6 fs-lg-5 mb-4 mb-lg-5">
                                        {{ $diagnostic['message'] }}
                                    </p>

                                    @if(isset($diagnostic['error']))
                                        <div class="alert alert-danger p-3 p-lg-4 mb-4 mb-lg-5">
                                            <div class="alert-text fs-7 fs-lg-6 fw-bold text-break">
                                                {{ $diagnostic['error'] }}
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($diagnostic['details']) && !empty($diagnostic['details']))
                                        <div class="mb-4 mb-lg-5">
                                            <h4 class="fw-semibold text-gray-800 fs-6 fs-lg-5 mb-3">Detalhes:</h4>
                                    
                                            @if($name === 'storage')
                                                <div class="table-responsive">
                                                    <table class="table table-row-dashed table-row-gray-300 gy-5">
                                                        <thead>
                                                            <tr class="fw-semibold fs-7 fs-lg-6 text-gray-800">
                                                                <th class="min-w-150px">Diretório</th>
                                                                <th class="min-w-80px text-center">Existe</th>
                                                                <th class="min-w-80px text-center">Gravável</th>
                                                                <th class="min-w-100px text-center d-none d-lg-table-cell">Permissões</th>
                                                                <th class="min-w-120px d-none d-xl-table-cell">Proprietário</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($diagnostic['details'] as $dir => $info)
                                                                <tr>
                                                                    <td class="fs-8 fs-lg-7">
                                                                        <code class="text-break">{{ $dir }}</code>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge fs-9 fs-lg-8 fw-bold {{ $info['exists'] ? 'badge-light-success' : 'badge-light-danger' }}">
                                                                            {{ $info['exists'] ? 'Sim' : 'Não' }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge fs-9 fs-lg-8 fw-bold {{ $info['writable'] ? 'badge-light-success' : 'badge-light-danger' }}">
                                                                            {{ $info['writable'] ? 'Sim' : 'Não' }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="fs-8 fs-lg-7 text-center d-none d-lg-table-cell">
                                                                        <code>{{ $info['permissions'] }}</code>
                                                                    </td>
                                                                    <td class="fs-8 fs-lg-7 d-none d-xl-table-cell">
                                                                        <code class="text-break">{{ $info['owner'] }}</code>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="row g-3">
                                                    @foreach($diagnostic['details'] as $key => $value)
                                                        <div class="col-12 col-sm-6 col-lg-12 col-xxl-6">
                                                            <div class="d-flex flex-column flex-sm-row justify-content-between bg-light p-3 rounded">
                                                                <span class="fs-8 fs-lg-7 text-gray-600 fw-semibold mb-1 mb-sm-0">
                                                                    {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                                </span>
                                                                <span class="fs-8 fs-lg-7 text-gray-800">
                                                                    <code class="text-break">{{ is_bool($value) ? ($value ? 'Sim' : 'Não') : $value }}</code>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row g-5 g-xl-8">
                    <div class="col-12">
                        <div class="card card-flush">
                            <div class="card-header">
                                <h3 class="card-title fw-bold text-gray-800 fs-3 fs-lg-2">
                                    Ações de Correção
                                </h3>
                            </div>
                            <div class="card-body p-6 p-lg-9">
                                <div class="alert alert-warning d-flex flex-column flex-sm-row align-items-start align-items-sm-center p-4 p-lg-5 mb-5">
                                    <i class="ki-duotone ki-shield-tick fs-2hx fs-lg-2x text-warning me-0 me-sm-4 mb-3 mb-sm-0">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-2 text-warning fs-5 fs-lg-4">Atenção!</h4>
                                        <span class="fs-6 fs-lg-5">As ações abaixo tentarão corrigir problemas de permissão automaticamente. Execute apenas se você tem certeza do que está fazendo.</span>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row gap-3">
                                    <button onclick="fixPermissions()" class="btn btn-danger btn-sm flex-shrink-0">
                                        <i class="ki-duotone ki-setting-4 fs-3 fs-lg-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="d-none d-sm-inline ms-2">Tentar Corrigir Permissões</span>
                                        <span class="d-inline d-sm-none">Corrigir</span>
                                    </button>
                                </div>

                                <div id="fix-results" class="d-none mt-4 mt-lg-5 p-4 p-lg-5 bg-light rounded">
                                    <h4 class="fw-semibold text-gray-800 mb-3 fs-6 fs-lg-5">Resultados:</h4>
                                    <div id="fix-output" class="fs-7 fs-lg-6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <!--end::Content-->

    <script>
        function fixPermissions() {
            const button = event.target;
            const resultsDiv = document.getElementById('fix-results');
            const outputDiv = document.getElementById('fix-output');
            
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Executando...';
            
            fetch('{{ route('admin.system-diagnostic.fix-permissions') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                resultsDiv.classList.remove('d-none');
                outputDiv.innerHTML = '';
                
                if (data.results) {
                    data.results.forEach(result => {
                        const div = document.createElement('div');
                        div.className = `mb-3 p-3 rounded ${result.success ? 'bg-light-success text-success' : 'bg-light-danger text-danger'}`;
                        div.innerHTML = `
                            <strong>${result.command}</strong><br>
                            <small>${result.output || 'Executado com sucesso'}</small>
                        `;
                        outputDiv.appendChild(div);
                    });
                } else {
                    outputDiv.innerHTML = `<div class="alert alert-danger">${data.message || 'Erro desconhecido'}</div>`;
                }
                
                button.disabled = false;
                button.innerHTML = '<i class="ki-duotone ki-setting-4 fs-2"><span class="path1"></span><span class="path2"></span></i> Tentar Corrigir Permissões';
                
                // Recarregar a página após 3 segundos
                setTimeout(() => location.reload(), 3000);
            })
            .catch(error => {
                resultsDiv.classList.remove('d-none');
                outputDiv.innerHTML = `<div class="alert alert-danger">Erro: ${error.message}</div>`;
                button.disabled = false;
                button.innerHTML = '<i class="ki-duotone ki-setting-4 fs-2"><span class="path1"></span><span class="path2"></span></i> Tentar Corrigir Permissões';
            });
        }
    </script>
@endsection