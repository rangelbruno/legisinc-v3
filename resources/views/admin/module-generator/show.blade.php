@extends('components.layouts.app')

@section('title', 'Visualizar Módulo - Gerador')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ $generatedModule->name }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.module-generator.index') }}" class="text-muted text-hover-primary">Gerador de Módulos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $generatedModule->name }}</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if(!$generatedModule->isGenerated())
                    <a href="{{ route('admin.module-generator.edit', $generatedModule) }}" class="btn btn-sm fw-bold btn-light-primary">
                        <i class="ki-duotone ki-pencil fs-2"></i>Editar
                    </a>
                    <form action="{{ route('admin.module-generator.generate', $generatedModule) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm fw-bold btn-success" 
                                onclick="return confirm('Deseja gerar este módulo? Esta ação criará todos os arquivos e não pode ser desfeita.')">
                            <i class="ki-duotone ki-rocket fs-2"></i>Gerar Código
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Sucesso!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Erro!</h4>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <div class="row g-5 g-xl-8">
                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Card-->
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Informações Gerais</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            <div class="d-flex align-items-sm-center mb-7">
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label">
                                        <i class="ki-duotone {{ $generatedModule->icon }} fs-2 text-{{ $generatedModule->color }}">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <span class="text-dark text-hover-primary fs-6 fw-bold">{{ $generatedModule->name }}</span>
                                    <span class="text-muted fw-semibold">{{ $generatedModule->description ?: 'Sem descrição' }}</span>
                                </div>
                                @if($generatedModule->status === 'draft')
                                    <span class="badge badge-light-warning">Rascunho</span>
                                @elseif($generatedModule->status === 'generated')
                                    <span class="badge badge-light-success">Gerado</span>
                                @elseif($generatedModule->status === 'error')
                                    <span class="badge badge-light-danger">Erro</span>
                                @endif
                            </div>
                            
                            <div class="separator separator-dashed my-5"></div>
                            
                            <div class="mb-5">
                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-muted fw-semibold d-block">Tabela</span>
                                        <span class="text-gray-800 fw-bold">{{ $generatedModule->table_name }}</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted fw-semibold d-block">Slug</span>
                                        <span class="text-gray-800 fw-bold">{{ $generatedModule->slug }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-5">
                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-muted fw-semibold d-block">CRUD</span>
                                        <span class="badge badge-light-{{ $generatedModule->has_crud ? 'success' : 'secondary' }}">
                                            {{ $generatedModule->has_crud ? 'Sim' : 'Não' }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted fw-semibold d-block">Permissões</span>
                                        <span class="badge badge-light-{{ $generatedModule->has_permissions ? 'success' : 'secondary' }}">
                                            {{ $generatedModule->has_permissions ? 'Sim' : 'Não' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-5">
                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-muted fw-semibold d-block">Criado por</span>
                                        <span class="text-gray-800 fw-bold">{{ $generatedModule->creator->name }}</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted fw-semibold d-block">Criado em</span>
                                        <span class="text-gray-800 fw-bold">{{ $generatedModule->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($generatedModule->generated_at)
                            <div class="mb-5">
                                <span class="text-muted fw-semibold d-block">Código gerado em</span>
                                <span class="text-gray-800 fw-bold">{{ $generatedModule->generated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
                
                <!--begin::Col-->
                <div class="col-xl-6">
                    <!--begin::Card-->
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Campos da Tabela</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">{{ count($generatedModule->fields_config) }} campos configurados</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            @foreach($generatedModule->fields_config as $field)
                            <div class="d-flex align-items-center mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-primary text-primary fw-semibold fs-7">
                                        @switch($field['type'])
                                            @case('string')
                                                📝
                                                @break
                                            @case('text')
                                                📄
                                                @break
                                            @case('integer')
                                                🔢
                                                @break
                                            @case('boolean')
                                                ☑️
                                                @break
                                            @case('date')
                                                📅
                                                @break
                                            @case('datetime')
                                                ⏰
                                                @break
                                            @case('json')
                                                🗂️
                                                @break
                                            @case('decimal')
                                                💰
                                                @break
                                            @default
                                                📋
                                        @endswitch
                                    </span>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <span class="text-dark fw-bold fs-6">{{ $field['name'] }}</span>
                                    <span class="text-muted fw-semibold">
                                        {{ ucfirst($field['type']) }}
                                        @if($field['nullable'] ?? false)
                                            <span class="badge badge-light-warning badge-sm ms-2">Opcional</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col-->
            </div>
            
            @if($generatedModule->relationships)
            <!--begin::Relationships-->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Relacionamentos</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">{{ count($generatedModule->relationships) }} relacionamentos</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="min-w-150px">Tipo</th>
                                    <th class="min-w-150px">Tabela</th>
                                    <th class="min-w-150px">Método</th>
                                    <th class="min-w-150px">Chave</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($generatedModule->relationships as $rel)
                                <tr>
                                    <td>
                                        <span class="badge badge-light-{{ $rel['type'] === 'belongsTo' ? 'primary' : 'success' }}">
                                            {{ $rel['type'] === 'belongsTo' ? 'Pertence a' : 'Tem muitos' }}
                                        </span>
                                    </td>
                                    <td class="text-dark fw-bold">{{ $rel['table'] }}</td>
                                    <td class="text-muted fw-semibold">{{ $rel['method_name'] }}</td>
                                    <td class="text-muted fw-semibold">{{ $rel['foreign_key'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end::Relationships-->
            @endif

            @if($generatedModule->business_logic)
            <!--begin::Business Logic-->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Regras de Negócio</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <pre class="bg-light-primary p-5 rounded"><code class="language-php">{{ $generatedModule->business_logic }}</code></pre>
                </div>
            </div>
            <!--end::Business Logic-->
            @endif

            @if($generatedModule->isGenerated() && $generatedModule->generated_files)
            <!--begin::Generated Files-->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Arquivos Gerados</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">{{ count($generatedModule->generated_files) }} arquivos criados</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    @foreach($generatedModule->generated_files as $file)
                        <div class="d-flex align-items-center mb-3">
                            <i class="ki-duotone ki-document fs-2 text-primary me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="text-gray-800 fw-semibold">{{ str_replace(base_path(), '', $file) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <!--end::Generated Files-->
            @endif

            @if($generatedModule->hasError() && $generatedModule->generation_log)
            <!--begin::Error Log-->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-danger">Log de Erro</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="alert alert-danger">
                        <pre>{{ $generatedModule->generation_log }}</pre>
                    </div>
                </div>
            </div>
            <!--end::Error Log-->
            @endif
        </div>
    </div>
</div>
@endsection