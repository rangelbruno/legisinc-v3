@extends('components.layouts.app')

@section('title', 'Diagnóstico do Banco de Dados')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-abstract-26 fs-2 me-3 text-primary">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Diagrama do Banco de Dados
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.system-diagnostic.index') }}" class="text-muted text-hover-primary">Diagnóstico</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Banco de Dados</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Export button-->
                <button class="btn btn-sm btn-flex btn-light btn-active-primary" id="exportDiagram">
                    <i class="ki-duotone ki-exit-down fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Exportar
                </button>
                <!--end::Export button-->
                <!--begin::Secondary button-->
                <a href="{{ route('admin.system-diagnostic.index') }}" class="btn btn-sm btn-flex btn-secondary">
                    <i class="ki-duotone ki-arrow-left fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Voltar
                </a>
                <!--end::Secondary button-->
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::Alert-->
            <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="d-flex flex-column">
                    <h5 class="mb-1">Diagrama Interativo</h5>
                    <span>Explore a estrutura do banco de dados com visualização interativa. Arraste tabelas, use zoom e clique para ver detalhes.</span>
                </div>
            </div>
            <!--end::Alert-->
            
            <!-- Informações do Banco -->
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-12">
                    <div class="card card-flush">
                        <div class="card-header pt-8">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-800 fs-2">
                                    <i class="ki-duotone ki-database fs-1 text-primary me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Informações da Conexão
                                </h3>
                            </div>
                            <div class="card-toolbar">
                                <span class="badge badge-light-success fs-7 fw-bold px-3 py-2">
                                    <i class="ki-duotone ki-check-circle fs-5 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Conectado
                                </span>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-6 g-xl-9">
                                <div class="col-sm-6 col-xl-4">
                                    <!--begin::Mixed Widget 2-->
                                    <div class="card card-xl-stretch mb-xl-8" style="background: linear-gradient(112.14deg, #00D4AA 0%, #00A3FF 100%)">
                                        <div class="card-body p-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px w-40px me-5">
                                                    <span class="symbol-label bg-white bg-opacity-15">
                                                        <i class="ki-duotone ki-gear fs-1 text-white">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-white fw-semibold fs-6">Driver</div>
                                                    <div class="text-white fw-bold fs-4">{{ strtoupper($databaseInfo['driver']) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Mixed Widget 2-->
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <!--begin::Mixed Widget 2-->
                                    <div class="card card-xl-stretch mb-xl-8" style="background: linear-gradient(112.14deg, #7239EA 0%, #F1416C 100%)">
                                        <div class="card-body p-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px w-40px me-5">
                                                    <span class="symbol-label bg-white bg-opacity-15">
                                                        <i class="ki-duotone ki-host fs-1 text-white">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-white fw-semibold fs-6">Host</div>
                                                    <div class="text-white fw-bold fs-4">{{ $databaseInfo['host'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Mixed Widget 2-->
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <!--begin::Mixed Widget 2-->
                                    <div class="card card-xl-stretch mb-xl-8" style="background: linear-gradient(112.14deg, #FFC700 0%, #FF5722 100%)">
                                        <div class="card-body p-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px w-40px me-5">
                                                    <span class="symbol-label bg-white bg-opacity-15">
                                                        <i class="ki-duotone ki-category fs-1 text-white">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-white fw-semibold fs-6">Banco</div>
                                                    <div class="text-white fw-bold fs-4">{{ $databaseInfo['database'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Mixed Widget 2-->
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <!--begin::Stats Widget 1-->
                                    <div class="card card-flush h-xl-100">
                                        <div class="card-body p-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px w-40px me-5">
                                                    <span class="symbol-label bg-light-info">
                                                        <i class="ki-duotone ki-port fs-1 text-info">
                                                            <span class="path1"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-gray-400 fw-semibold fs-6">Porta</div>
                                                    <div class="text-gray-800 fw-bold fs-4">{{ $databaseInfo['port'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Stats Widget 1-->
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <!--begin::Stats Widget 1-->
                                    <div class="card card-flush h-xl-100">
                                        <div class="card-body p-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px w-40px me-5">
                                                    <span class="symbol-label bg-light-warning">
                                                        <i class="ki-duotone ki-text fs-1 text-warning">
                                                            <span class="path1"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-gray-400 fw-semibold fs-6">Charset</div>
                                                    <div class="text-gray-800 fw-bold fs-4">{{ $databaseInfo['charset'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Stats Widget 1-->
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <!--begin::Stats Widget 1-->
                                    <div class="card card-flush h-xl-100">
                                        <div class="card-body p-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px w-40px me-5">
                                                    <span class="symbol-label bg-light-success">
                                                        <i class="ki-duotone ki-row-horizontal fs-1 text-success">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-gray-400 fw-semibold fs-6">Tabelas</div>
                                                    <div class="text-gray-800 fw-bold fs-4">{{ count($tables) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Stats Widget 1-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagrama Interativo das Tabelas -->
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                <div class="col-12">
                    <div class="card card-flush">
                        <div class="card-header pt-8">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-800 fs-2">
                                    <i class="ki-duotone ki-abstract-26 fs-1 text-success me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Diagrama Interativo
                                </h3>
                                <p class="text-gray-600 fs-6 mt-2 mb-0">{{ count($relationships) }} relacionamentos encontrados entre {{ count($tables) }} tabelas</p>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex flex-wrap gap-2">
                                    <!--begin::Toolbar buttons-->
                                    <div class="btn-group" role="group">
                                        <button id="zoomIn" class="btn btn-sm btn-light-primary" title="Zoom In">
                                            <i class="ki-duotone ki-plus fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                        <button id="resetZoom" class="btn btn-sm btn-light-primary" title="Reset Zoom">
                                            <i class="ki-duotone ki-zoom-out fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                        <button id="zoomOut" class="btn btn-sm btn-light-primary" title="Zoom Out">
                                            <i class="ki-duotone ki-minus fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                    </div>
                                    
                                    <div class="btn-group" role="group">
                                        <button id="centerDiagram" class="btn btn-sm btn-light-warning" title="Centralizar">
                                            <i class="ki-duotone ki-design fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                        <button id="fitScreen" class="btn btn-sm btn-light-warning" title="Ajustar à Tela">
                                            <i class="ki-duotone ki-switch fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                    </div>
                                    
                                    <button id="toggleTable" class="btn btn-sm btn-light-info">
                                        <i class="ki-duotone ki-eye fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Lista de Tabelas
                                    </button>
                                    <!--end::Toolbar buttons-->
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="database-diagram" style="width: 100%; height: 700px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); position: relative; overflow: hidden;">
                                <div id="diagram-loading" class="d-flex justify-content-center align-items-center h-100">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-lg text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                        <div class="mt-4">
                                            <h5 class="text-gray-700 fw-bold">Carregando Diagrama</h5>
                                            <p class="text-gray-500 fs-7">Preparando visualização das tabelas...</p>
                                        </div>
                                    </div>
                                </div>
                                <svg id="diagram-svg" style="display: none; width: 100%; height: 100%;"></svg>
                                
                                <!-- Controles flutuantes -->
                                <div id="diagram-controls" style="position: absolute; top: 20px; right: 20px; z-index: 100; display: none;">
                                    <div class="card shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="text-gray-600 fs-8 fw-bold mb-2">Controles</div>
                                            <div class="d-flex flex-column gap-1">
                                                <small class="text-gray-500 fs-9">🖱️ Arraste para mover</small>
                                                <small class="text-gray-500 fs-9">🖱️ Scroll para zoom</small>
                                                <small class="text-gray-500 fs-9">👆 Clique para detalhes</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Tabelas (oculta inicialmente) -->
            <div class="row g-5 g-xl-8" id="tables-list" style="display: none;">
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

    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Script iniciando...');
            
            // Verificar se D3 está carregado
            if (typeof d3 === 'undefined') {
                console.error('❌ D3.js não foi carregado!');
                document.getElementById('diagram-loading').innerHTML = '<div class="text-center"><h5 class="text-danger">Erro: D3.js não carregado</h5><p>Verifique sua conexão com a internet</p></div>';
                return;
            }
            
            console.log('✅ D3 carregado, versão:', d3.version);
            
            // Dados das tabelas e relacionamentos do PHP
            const tables = @json($tables);
            const relationships = @json($relationships);
            
            console.log('📊 Tables:', tables.length);
            console.log('🔗 Relationships:', relationships.length);
            
            // Teste simples primeiro
            const diagramElement = document.getElementById('database-diagram');
            if (!diagramElement) {
                console.error('❌ Elemento database-diagram não encontrado!');
                return;
            }
            
            console.log('✅ Elemento diagrama encontrado');
            
            // Mostrar informações básicas no loading
            document.getElementById('diagram-loading').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border spinner-border-lg text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div class="mt-4">
                        <h5 class="text-gray-700 fw-bold">Carregando ${tables.length} Tabelas</h5>
                        <p class="text-gray-500 fs-7">${relationships.length} relacionamentos encontrados...</p>
                    </div>
                </div>
            `;
            
            const width = diagramElement.clientWidth || 800;
            const height = 700;
            
            console.log('📐 Dimensões:', width, 'x', height);
            
            try {
                console.log('🎨 Criando dados para o diagrama...');
                
                // Teste básico - criar SVG simples
                const svg = d3.select('#diagram-svg')
                    .attr('width', width)
                    .attr('height', height);
                
                console.log('✅ SVG criado');
                
                // Adicionar um círculo de teste
                svg.append('circle')
                    .attr('cx', width / 2)
                    .attr('cy', height / 2)
                    .attr('r', 50)
                    .attr('fill', '#007bff')
                    .attr('stroke', '#fff')
                    .attr('stroke-width', 3);
                
                // Adicionar texto de teste
                svg.append('text')
                    .attr('x', width / 2)
                    .attr('y', height / 2)
                    .attr('text-anchor', 'middle')
                    .attr('dy', '0.35em')
                    .attr('fill', 'white')
                    .attr('font-size', '14px')
                    .attr('font-weight', 'bold')
                    .text(`${tables.length} Tabelas`);
                
                console.log('✅ Elementos de teste adicionados');
                
                // Ocultar loading e mostrar diagrama
                setTimeout(() => {
                    console.log('🎯 Finalizando...');
                    document.getElementById('diagram-loading').style.display = 'none';
                    document.getElementById('diagram-svg').style.display = 'block';
                    console.log('✅ Diagrama mostrado!');
                }, 1000);
                
            } catch (error) {
                console.error('❌ Erro ao criar diagrama:', error);
                document.getElementById('diagram-loading').innerHTML = `
                    <div class="text-center">
                        <h5 class="text-danger">Erro ao carregar diagrama</h5>
                        <p class="text-muted">${error.message}</p>
                    </div>
                `;
            }
            
            // Configurar zoom
            const zoom = d3.zoom()
                .scaleExtent([0.1, 3])
                .on('zoom', function(event) {
                    container.attr('transform', event.transform);
                });
            
            svg.call(zoom);
            
            const container = svg.append('g');
            
            // Criar gradientes para as setas
            const defs = svg.append('defs');
            
            const arrowMarker = defs.append('marker')
                .attr('id', 'arrowhead')
                .attr('viewBox', '0 -5 10 10')
                .attr('refX', 8)
                .attr('refY', 0)
                .attr('markerWidth', 6)
                .attr('markerHeight', 6)
                .attr('orient', 'auto');
                
            arrowMarker.append('path')
                .attr('d', 'M0,-5L10,0L0,5')
                .attr('fill', '#6c757d');
            
            // Criar simulação de força
            const simulation = d3.forceSimulation(nodes)
                .force('link', d3.forceLink(links).id(d => d.id).distance(150))
                .force('charge', d3.forceManyBody().strength(-500))
                .force('center', d3.forceCenter(width / 2, height / 2))
                .force('collision', d3.forceCollide().radius(70));
            
            // Criar links (conexões)
            const link = container.append('g')
                .selectAll('line')
                .data(links)
                .enter().append('line')
                .attr('stroke', '#6c757d')
                .attr('stroke-width', 2)
                .attr('marker-end', 'url(#arrowhead)')
                .attr('opacity', 0.7);
            
            // Criar rótulos dos links
            const linkLabels = container.append('g')
                .selectAll('text')
                .data(links)
                .enter().append('text')
                .attr('class', 'link-label')
                .attr('font-size', '10px')
                .attr('fill', '#6c757d')
                .attr('text-anchor', 'middle')
                .text(d => d.label)
                .style('pointer-events', 'none');
            
            // Criar grupos para as tabelas
            const node = container.append('g')
                .selectAll('g')
                .data(nodes)
                .enter().append('g')
                .attr('class', 'table-node')
                .call(d3.drag()
                    .on('start', dragStarted)
                    .on('drag', dragged)
                    .on('end', dragEnded));
            
            // Adicionar sombra para as tabelas
            const filter = defs.append('filter')
                .attr('id', 'table-shadow')
                .attr('x', '-50%')
                .attr('y', '-50%')
                .attr('width', '200%')
                .attr('height', '200%');
            
            filter.append('feDropShadow')
                .attr('dx', 2)
                .attr('dy', 2)
                .attr('stdDeviation', 3)
                .attr('flood-color', 'rgba(0,0,0,0.2)');
            
            // Adicionar retângulos para as tabelas com gradientes
            node.append('rect')
                .attr('width', 160)
                .attr('height', 80)
                .attr('rx', 12)
                .attr('ry', 12)
                .attr('fill', function(d) {
                    // Cor baseada no número de registros
                    const rows = typeof d.rows === 'number' ? d.rows : 0;
                    if (rows > 1000) return 'url(#gradient-high)';
                    if (rows > 100) return 'url(#gradient-medium)';
                    return 'url(#gradient-low)';
                })
                .attr('stroke', '#e1e5e9')
                .attr('stroke-width', 2)
                .attr('filter', 'url(#table-shadow)')
                .style('cursor', 'move')
                .on('mouseover', function(event, d) {
                    d3.select(this)
                        .attr('stroke', '#0066cc')
                        .attr('stroke-width', 3)
                        .transition()
                        .duration(200)
                        .attr('rx', 16)
                        .attr('ry', 16);
                })
                .on('mouseout', function(event, d) {
                    d3.select(this)
                        .attr('stroke', '#e1e5e9')
                        .attr('stroke-width', 2)
                        .transition()
                        .duration(200)
                        .attr('rx', 12)
                        .attr('ry', 12);
                })
                .on('click', function(event, d) {
                    // Animação de click
                    d3.select(this)
                        .transition()
                        .duration(100)
                        .attr('transform', 'scale(0.95)')
                        .transition()
                        .duration(100)
                        .attr('transform', 'scale(1)');
                    
                    window.open(`{{ url('admin/system-diagnostic/database/table') }}/${d.id}`, '_blank');
                });
            
            // Criar gradientes para as tabelas
            const gradientHigh = defs.append('linearGradient')
                .attr('id', 'gradient-high')
                .attr('x1', '0%').attr('y1', '0%')
                .attr('x2', '0%').attr('y2', '100%');
            gradientHigh.append('stop').attr('offset', '0%').attr('stop-color', '#ff6b6b').attr('stop-opacity', 0.1);
            gradientHigh.append('stop').attr('offset', '100%').attr('stop-color', '#ee5a52').attr('stop-opacity', 0.2);
            
            const gradientMedium = defs.append('linearGradient')
                .attr('id', 'gradient-medium')
                .attr('x1', '0%').attr('y1', '0%')
                .attr('x2', '0%').attr('y2', '100%');
            gradientMedium.append('stop').attr('offset', '0%').attr('stop-color', '#ffd93d').attr('stop-opacity', 0.1);
            gradientMedium.append('stop').attr('offset', '100%').attr('stop-color', '#ffb74d').attr('stop-opacity', 0.2);
            
            const gradientLow = defs.append('linearGradient')
                .attr('id', 'gradient-low')
                .attr('x1', '0%').attr('y1', '0%')
                .attr('x2', '0%').attr('y2', '100%');
            gradientLow.append('stop').attr('offset', '0%').attr('stop-color', '#4fc3f7').attr('stop-opacity', 0.1);
            gradientLow.append('stop').attr('offset', '100%').attr('stop-color', '#29b6f6').attr('stop-opacity', 0.2);
            
            // Cabeçalho da tabela
            node.append('rect')
                .attr('width', 160)
                .attr('height', 25)
                .attr('rx', 12)
                .attr('ry', 12)
                .attr('fill', function(d) {
                    const rows = typeof d.rows === 'number' ? d.rows : 0;
                    if (rows > 1000) return '#ff5722';
                    if (rows > 100) return '#ff9800';
                    return '#2196f3';
                })
                .attr('opacity', 0.9);
            
            // Adicionar ícones das tabelas
            node.append('circle')
                .attr('cx', 20)
                .attr('cy', 12.5)
                .attr('r', 8)
                .attr('fill', '#ffffff')
                .attr('stroke', 'none');
            
            node.append('text')
                .attr('x', 20)
                .attr('y', 16)
                .attr('text-anchor', 'middle')
                .attr('font-size', '10px')
                .attr('fill', function(d) {
                    const rows = typeof d.rows === 'number' ? d.rows : 0;
                    if (rows > 1000) return '#ff5722';
                    if (rows > 100) return '#ff9800';
                    return '#2196f3';
                })
                .attr('font-weight', 'bold')
                .text('📊');
            
            // Adicionar nome da tabela (no cabeçalho)
            node.append('text')
                .attr('x', 80)
                .attr('y', 16)
                .attr('text-anchor', 'middle')
                .attr('font-size', '12px')
                .attr('font-weight', 'bold')
                .attr('fill', '#ffffff')
                .text(d => d.name.length > 18 ? d.name.substring(0, 18) + '...' : d.name)
                .append('title')
                .text(d => d.name);
            
            // Adicionar informações adicionais
            node.append('text')
                .attr('x', 80)
                .attr('y', 45)
                .attr('text-anchor', 'middle')
                .attr('font-size', '11px')
                .attr('font-weight', '600')
                .attr('fill', '#2c3e50')
                .text(d => {
                    if (typeof d.rows === 'number') {
                        return `${d.rows.toLocaleString()} registros`;
                    } else {
                        return d.rows;
                    }
                });
            
            node.append('text')
                .attr('x', 80)
                .attr('y', 60)
                .attr('text-anchor', 'middle')
                .attr('font-size', '9px')
                .attr('fill', '#7f8c8d')
                .text(d => d.size);
            
            // Badge de status baseado no número de registros
            node.append('rect')
                .attr('x', 125)
                .attr('y', 5)
                .attr('width', 30)
                .attr('height', 15)
                .attr('rx', 8)
                .attr('ry', 8)
                .attr('fill', function(d) {
                    const rows = typeof d.rows === 'number' ? d.rows : 0;
                    if (rows > 1000) return '#e74c3c';
                    if (rows > 100) return '#f39c12';
                    return '#3498db';
                })
                .attr('opacity', 0.8);
            
            node.append('text')
                .attr('x', 140)
                .attr('y', 14)
                .attr('text-anchor', 'middle')
                .attr('font-size', '8px')
                .attr('font-weight', 'bold')
                .attr('fill', '#ffffff')
                .text(function(d) {
                    const rows = typeof d.rows === 'number' ? d.rows : 0;
                    if (rows > 1000) return 'HIGH';
                    if (rows > 100) return 'MED';
                    return 'LOW';
                });
            
            // Configurar simulação
            simulation.on('tick', () => {
                // Atualizar posições dos links
                link
                    .attr('x1', d => d.source.x + 80)
                    .attr('y1', d => d.source.y + 40)
                    .attr('x2', d => d.target.x + 80)
                    .attr('y2', d => d.target.y + 40);
                
                // Atualizar posições dos rótulos dos links
                linkLabels
                    .attr('x', d => (d.source.x + d.target.x + 160) / 2)
                    .attr('y', d => (d.source.y + d.target.y + 80) / 2);
                
                // Atualizar posições dos nós
                node.attr('transform', d => `translate(${d.x},${d.y})`);
            });
            
            // Funções de drag
            function dragStarted(event, d) {
                if (!event.active) simulation.alphaTarget(0.3).restart();
                d.fx = d.x;
                d.fy = d.y;
            }
            
            function dragged(event, d) {
                d.fx = event.x;
                d.fy = event.y;
            }
            
            function dragEnded(event, d) {
                if (!event.active) simulation.alphaTarget(0);
                d.fx = null;
                d.fy = null;
            }
            
            // Ocultar loading e mostrar diagrama
            console.log('Finalizando configuração do diagrama...');
            
            setTimeout(() => {
                console.log('Ocultando loading e mostrando diagrama...');
                const loadingElement = document.getElementById('diagram-loading');
                const svgElement = document.getElementById('diagram-svg');
                const controlsElement = document.getElementById('diagram-controls');
                
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                    console.log('Loading ocultado');
                }
                
                if (svgElement) {
                    svgElement.style.display = 'block';
                    console.log('SVG mostrado');
                }
                
                if (controlsElement) {
                    controlsElement.style.display = 'block';
                    console.log('Controles mostrados');
                }
            }, 1500);
            
            // Event listeners para botões
            document.getElementById('resetZoom').addEventListener('click', function() {
                svg.transition().duration(750).call(
                    zoom.transform,
                    d3.zoomIdentity
                );
            });
            
            document.getElementById('zoomIn').addEventListener('click', function() {
                svg.transition().duration(300).call(
                    zoom.scaleBy,
                    1.5
                );
            });
            
            document.getElementById('zoomOut').addEventListener('click', function() {
                svg.transition().duration(300).call(
                    zoom.scaleBy,
                    1 / 1.5
                );
            });
            
            document.getElementById('centerDiagram').addEventListener('click', function() {
                svg.transition().duration(750).call(
                    zoom.transform,
                    d3.zoomIdentity.translate(width / 2, height / 2)
                );
            });
            
            document.getElementById('fitScreen').addEventListener('click', function() {
                const bounds = container.node().getBBox();
                const fullWidth = bounds.width;
                const fullHeight = bounds.height;
                const widthRatio = width / fullWidth;
                const heightRatio = height / fullHeight;
                const scale = 0.8 * Math.min(widthRatio, heightRatio);
                const translate = [(width - scale * (bounds.x + bounds.x + fullWidth)) / 2, (height - scale * (bounds.y + bounds.y + fullHeight)) / 2];
                
                svg.transition().duration(750).call(
                    zoom.transform,
                    d3.zoomIdentity.translate(translate[0], translate[1]).scale(scale)
                );
            });
            
            // Botão de exportar
            document.getElementById('exportDiagram').addEventListener('click', function() {
                // Criar uma cópia do SVG para exportar
                const svgClone = document.getElementById('diagram-svg').cloneNode(true);
                const serializer = new XMLSerializer();
                const svgString = serializer.serializeToString(svgClone);
                const blob = new Blob([svgString], {type: 'image/svg+xml'});
                const url = URL.createObjectURL(blob);
                
                const link = document.createElement('a');
                link.href = url;
                link.download = 'diagrama-banco-dados.svg';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            });
            
            let tableVisible = false;
            document.getElementById('toggleTable').addEventListener('click', function() {
                const tablesList = document.getElementById('tables-list');
                const btn = this;
                
                if (tableVisible) {
                    tablesList.style.display = 'none';
                    btn.innerHTML = '<i class="ki-duotone ki-eye fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Ver Tabela';
                    tableVisible = false;
                } else {
                    tablesList.style.display = 'block';
                    btn.innerHTML = '<i class="ki-duotone ki-eye-slash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Ocultar Tabela';
                    tableVisible = true;
                    
                    // Inicializar DataTable se não foi inicializado ainda
                    @if(count($tables) > 0)
                    if (typeof $ !== 'undefined' && $.fn.DataTable && !$.fn.DataTable.isDataTable('#tables-table')) {
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
                }
            });
            
            // Adicionar tooltip personalizado para informações detalhadas
            node.on('mouseover', function(event, d) {
                const rows = typeof d.rows === 'number' ? d.rows : 0;
                const statusColor = rows > 1000 ? '#e74c3c' : rows > 100 ? '#f39c12' : '#3498db';
                const statusText = rows > 1000 ? 'HIGH VOLUME' : rows > 100 ? 'MEDIUM VOLUME' : 'LOW VOLUME';
                
                const tooltip = d3.select('body').append('div')
                    .attr('class', 'database-tooltip')
                    .style('position', 'absolute')
                    .style('padding', '0')
                    .style('background', '#ffffff')
                    .style('border', '1px solid #dee2e6')
                    .style('border-radius', '12px')
                    .style('box-shadow', '0 10px 30px rgba(0,0,0,0.15)')
                    .style('pointer-events', 'none')
                    .style('font-family', '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif')
                    .style('z-index', '1000')
                    .style('max-width', '280px')
                    .style('opacity', '0')
                    .html(`
                        <div style="background: ${statusColor}; color: white; padding: 12px; border-radius: 12px 12px 0 0;">
                            <div style="font-weight: bold; font-size: 14px; margin-bottom: 4px;">📊 ${d.name}</div>
                            <div style="font-size: 11px; opacity: 0.9;">${statusText}</div>
                        </div>
                        <div style="padding: 16px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #6c757d; font-size: 12px;">Registros:</span>
                                <span style="font-weight: 600; color: #2c3e50; font-size: 12px;">${typeof d.rows === 'number' ? d.rows.toLocaleString() : d.rows}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #6c757d; font-size: 12px;">Tamanho:</span>
                                <span style="font-weight: 600; color: #2c3e50; font-size: 12px;">${d.size}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #6c757d; font-size: 12px;">Engine:</span>
                                <span style="font-weight: 600; color: #2c3e50; font-size: 12px;">${d.engine}</span>
                            </div>
                            <div style="text-align: center; padding: 8px; background: #f8f9fa; border-radius: 6px; font-size: 11px; color: #6c757d;">
                                👆 Clique para ver detalhes da tabela
                            </div>
                        </div>
                    `);
                    
                const [mouseX, mouseY] = d3.pointer(event, document.body);
                tooltip
                    .style('left', (mouseX + 15) + 'px')
                    .style('top', (mouseY - 10) + 'px')
                    .transition()
                    .duration(200)
                    .style('opacity', '1');
            })
            .on('mouseout', function() {
                d3.selectAll('.database-tooltip')
                    .transition()
                    .duration(150)
                    .style('opacity', '0')
                    .remove();
            });
        });
    </script>
    
    <style>
        .table-node {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .table-node:hover {
            filter: drop-shadow(0 8px 25px rgba(0,0,0,0.15));
        }
        
        .link-label {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-weight: 500;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
        }
        
        .database-tooltip {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        #database-diagram {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }
        
        /* Botões com efeitos hover melhorados */
        .btn-group .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Animação para o loading */
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        .spinner-border-lg {
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        /* Scrollbar personalizada para tooltips */
        .database-tooltip::-webkit-scrollbar {
            width: 4px;
        }
        
        .database-tooltip::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }
        
        .database-tooltip::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        
        /* Efeito de hover para os cards de informação */
        .card-xl-stretch:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        /* Melhorias nos badges */
        .badge {
            letter-spacing: 0.5px;
        }
        
        /* Responsividade para mobile */
        @media (max-width: 768px) {
            #database-diagram {
                height: 500px !important;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn-group .btn {
                border-radius: 0.375rem !important;
                margin-bottom: 2px;
            }
        }
    </style>
@endsection