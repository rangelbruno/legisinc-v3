@extends('components.layouts.app')

@section('title', 'Designer: ' . $workflow->nome)

@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    <i class="ki-duotone ki-design-1 fs-2 text-success me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                    Designer Visual Profissional
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.workflows.index') }}" class="text-muted text-hover-primary">Workflows</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.workflows.show', $workflow) }}" class="text-muted text-hover-primary">{{ $workflow->nome }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Designer</li>
                </ul>
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button id="validateWorkflow" class="btn btn-sm fw-bold btn-light-info">
                    <i class="ki-duotone ki-check-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Validar
                </button>
                <button id="saveWorkflow" class="btn btn-sm fw-bold btn-success">
                    <i class="ki-duotone ki-check fs-2"></i>
                    Salvar
                </button>
                <a href="{{ route('admin.workflows.show', $workflow) }}" class="btn btn-sm fw-bold btn-light">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>
                    Voltar
                </a>
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Layout-->
            <div class="d-flex flex-column flex-lg-row">
                <!--begin::Sidebar-->
                <div class="flex-column flex-lg-row-auto w-100 w-lg-300px w-xl-400px mb-10 mb-lg-0">
                    <!--begin::Card Ferramentas-->
                    <div class="card mb-5">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">
                                    <i class="ki-duotone ki-questionnaire-tablet fs-2 text-primary me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Ferramentas
                                </h3>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <!-- Arrastar Etapas -->
                            <div class="mb-8">
                                <h6 class="fw-semibold text-gray-800 mb-4">Criar Etapas</h6>
                                <div class="d-grid gap-3">
                                    <div class="etapa-template d-flex align-items-center p-3 bg-light-success rounded cursor-pointer hover-scale-105 transition-all" 
                                         data-tipo="inicio">
                                        <i class="ki-duotone ki-play-circle fs-1 text-success me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-semibold">Início</span>
                                    </div>
                                    <div class="etapa-template d-flex align-items-center p-3 bg-light-primary rounded cursor-pointer hover-scale-105 transition-all" 
                                         data-tipo="processo">
                                        <i class="ki-duotone ki-setting-2 fs-1 text-primary me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-semibold">Processo</span>
                                    </div>
                                    <div class="etapa-template d-flex align-items-center p-3 bg-light-warning rounded cursor-pointer hover-scale-105 transition-all" 
                                         data-tipo="decisao">
                                        <i class="ki-duotone ki-questionnaire-tablet fs-1 text-warning me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-semibold">Decisão</span>
                                    </div>
                                    <div class="etapa-template d-flex align-items-center p-3 bg-light-danger rounded cursor-pointer hover-scale-105 transition-all" 
                                         data-tipo="final">
                                        <i class="ki-duotone ki-check-circle fs-1 text-danger me-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span class="fw-semibold">Final</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Controls -->
                            <div class="mb-8">
                                <h6 class="fw-semibold text-gray-800 mb-4">Controles Avançados</h6>
                                
                                <!-- Zoom Controls -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="fs-7 text-muted fw-semibold">Zoom</label>
                                        <span class="badge badge-light-primary fs-8" id="zoom-indicator">100%</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-light" id="zoomOut" title="Diminuir zoom (Ctrl + -)">
                                            <i class="ki-duotone ki-minus fs-6"></i>
                                        </button>
                                        <input type="range" class="form-range flex-grow-1" min="25" max="300" step="25" value="100" id="zoom-slider">
                                        <button type="button" class="btn btn-sm btn-light" id="zoomIn" title="Aumentar zoom (Ctrl + +)">
                                            <i class="ki-duotone ki-plus fs-6"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <!-- Auto Layout with Options -->
                                    <div class="btn-group w-100" role="group">
                                        <button id="autoArrange" class="btn btn-sm btn-light-primary" title="Organizar automaticamente usando algoritmo ELK.js">
                                            <i class="ki-duotone ki-design-1 fs-6 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Auto Layout
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" title="Escolher algoritmo de layout">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="window.workflowDesigner.autoArrangeNodes('layered')">
                                                <i class="ki-duotone ki-abstract-34 fs-6 me-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Layout em Camadas
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="window.workflowDesigner.autoArrangeNodes('force')">
                                                <i class="ki-duotone ki-abstract-28 fs-6 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Layout por Força
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="window.workflowDesigner.autoArrangeNodes('radial')">
                                                <i class="ki-duotone ki-abstract-41 fs-6 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Layout Radial
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="window.workflowDesigner.autoArrangeNodes('circular')">
                                                <i class="ki-duotone ki-abstract-43 fs-6 me-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Layout Circular
                                            </a></li>
                                        </ul>
                                    </div>
                                    
                                    <!-- Dual View Controls -->
                                    <div class="row g-1">
                                        <div class="col-6">
                                            <button id="fitView" class="btn btn-sm btn-light-secondary w-100" title="Ajustar workflow à tela (Ctrl + 0)">
                                                <i class="ki-duotone ki-zoom-in fs-6 me-1"></i>
                                                Ajustar
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button id="centerView" class="btn btn-sm btn-light-info w-100" title="Centralizar visualização">
                                                <i class="ki-duotone ki-compass fs-6 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Centro
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <button id="snapGrid" class="btn btn-sm btn-light-info" data-active="true" title="Ativar/desativar snap to grid de 8px">
                                        <i class="ki-duotone ki-dots-square fs-6 me-1"></i>
                                        <span class="snap-text">Snap Grid (8px)</span>
                                    </button>
                                    
                                    <div class="separator my-2"></div>
                                    
                                    <button id="saveWorkflow" class="btn btn-sm btn-light-success" title="Salvar alterações do workflow">
                                        <i class="ki-duotone ki-check fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Salvar Alterações
                                    </button>
                                    
                                    <button id="validateWorkflow" class="btn btn-sm btn-light-warning" title="Validar integridade do workflow">
                                        <i class="ki-duotone ki-shield-tick fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Validar Workflow
                                    </button>
                                    
                                    <button id="clearCanvas" class="btn btn-sm btn-light-danger" title="Limpar todo o canvas (irreversível)">
                                        <i class="ki-duotone ki-trash fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                        Limpar Tudo
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Enhanced Statistics & Minimap -->
                            <div>
                                <h6 class="fw-semibold text-gray-800 mb-4">Visão Geral</h6>
                                
                                <!-- Real-time Statistics -->
                                <div class="row g-2 mb-4">
                                    <div class="col-4">
                                        <div class="card card-flush bg-light-primary">
                                            <div class="card-body p-3 text-center">
                                                <i class="ki-duotone ki-abstract-43 fs-2 text-primary mb-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fw-bold fs-4 text-primary" id="node-count">0</div>
                                                <div class="text-muted fs-8">Nós</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card card-flush bg-light-success">
                                            <div class="card-body p-3 text-center">
                                                <i class="ki-duotone ki-arrow-right fs-2 text-success mb-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fw-bold fs-4 text-success" id="edge-count">0</div>
                                                <div class="text-muted fs-8">Conexões</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card card-flush bg-light-info">
                                            <div class="card-body p-3 text-center">
                                                <i class="ki-duotone ki-magnifier fs-2 text-info mb-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fw-bold fs-4 text-info" id="zoom-level">100%</div>
                                                <div class="text-muted fs-8">Zoom</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Interactive Minimap -->
                                <div id="canvas-minimap" class="border rounded shadow-sm position-relative" style="height: 140px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); overflow: hidden; cursor: crosshair;">
                                    <canvas id="minimap-canvas" width="300" height="140" style="width: 100%; height: 100%; position: absolute; top: 0; left: 0;"></canvas>
                                    <div class="position-absolute top-50 start-50 translate-middle text-muted" id="minimap-placeholder">
                                        <div class="text-center">
                                            <i class="ki-duotone ki-element-11 fs-2x mb-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            <div class="fs-7 fw-semibold">Miniatura do Workflow</div>
                                            <div class="fs-8 text-muted">Clique para navegar</div>
                                        </div>
                                    </div>
                                    <!-- Viewport indicator -->
                                    <div id="minimap-viewport" class="position-absolute border border-primary" style="display: none; border-width: 2px !important; background: rgba(0, 158, 247, 0.1);"></div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card Ferramentas-->

                    <!--begin::Card Propriedades-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">
                                    <i class="ki-duotone ki-setting fs-2 text-info me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Propriedades
                                </h3>
                            </div>
                            <!-- Properties Actions -->
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-light-primary" id="duplicateElement" title="Duplicar elemento selecionado" disabled>
                                    <i class="ki-duotone ki-copy fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger" id="deleteElement" title="Excluir elemento selecionado" disabled>
                                    <i class="ki-duotone ki-trash fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                </button>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <div id="properties-panel">
                                <div class="text-center py-8">
                                    <i class="ki-duotone ki-information-5 fs-3x text-muted mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <p class="text-muted">Selecione um elemento para editar suas propriedades</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card Propriedades-->
                </div>
                <!--end::Sidebar-->

                <!--begin::Content-->
                <div class="flex-lg-row-fluid ms-lg-15">
                    <!--begin::Card Canvas-->
                    <div class="card h-xl-100">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">
                                    Canvas: {{ $workflow->nome }}
                                </h3>
                            </div>
                            <div class="card-toolbar d-flex gap-2">
                                <div class="btn-group btn-group-sm">
                                    <button id="zoomIn" class="btn btn-light-secondary" title="Zoom +">
                                        <i class="ki-duotone ki-plus fs-6"></i>
                                    </button>
                                    <button id="zoomOut" class="btn btn-light-secondary" title="Zoom -">
                                        <i class="ki-duotone ki-minus fs-6"></i>
                                    </button>
                                    <button id="resetZoom" class="btn btn-light-secondary" title="Reset Zoom">
                                        <span class="badge badge-light-primary fs-8" id="zoom-level">100%</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body p-0">
                            <!-- Professional Responsive Canvas Container -->
                            <div id="canvas-container" class="position-relative" style="width: 100%; min-height: 70vh; height: 70vh; overflow: hidden; background: #f8f9fa; border: 1px solid #e4e6ef;">
                                <canvas id="workflow-canvas" 
                                        style="border: none; display: block; cursor: grab; position: absolute; top: 0; left: 0;">
                                </canvas>
                                
                                <!-- Grid Pattern Overlay -->
                                <div id="grid-overlay" class="position-absolute w-100 h-100" 
                                     style="pointer-events: none; opacity: 0.3; z-index: 1;
                                            background-image: radial-gradient(circle, #999 1px, transparent 1px);
                                            background-size: 20px 20px;
                                            background-position: 0 0;">
                                </div>
                                
                                <!-- Canvas Controls Overlay -->
                                <div class="position-absolute" style="bottom: 16px; right: 16px; z-index: 100;">
                                    <div class="btn-group-vertical shadow-lg bg-white rounded">
                                        <button class="btn btn-sm btn-light border-0" id="canvas-zoom-in" title="Aumentar Zoom (+)">
                                            <i class="ki-duotone ki-plus fs-6 text-primary"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border-0" id="canvas-zoom-out" title="Diminuir Zoom (-)">
                                            <i class="ki-duotone ki-minus fs-6 text-primary"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border-0" id="canvas-fit-view" title="Ajustar à Tela (Ctrl+0)">
                                            <i class="ki-duotone ki-frame fs-6 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </button>
                                        <button class="btn btn-sm btn-light border-0" id="canvas-center" title="Centralizar Visão">
                                            <i class="ki-duotone ki-compass fs-6 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Pan Indicators -->
                                <div id="pan-indicator" class="position-absolute" style="top: 16px; left: 16px; z-index: 100; display: none;">
                                    <div class="badge badge-primary">
                                        <i class="ki-duotone ki-abstract-26 fs-7 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Arrastar para mover
                                    </div>
                                </div>
                                
                                <!-- Zoom Level Display -->
                                <div id="zoom-display" class="position-absolute" style="top: 16px; right: 16px; z-index: 100;">
                                    <div class="badge badge-light-primary fw-bold fs-7">
                                        <i class="ki-duotone ki-magnifier fs-8 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <span id="zoom-percentage">100%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card body-->
                        <!--begin::Card footer-->
                        <div class="card-footer bg-light-secondary">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-information-5 fs-2 text-primary me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <span class="fw-semibold text-gray-700 fs-7">
                                        <strong>Dicas:</strong> Clique para criar, arraste para mover, conecte automaticamente, duplo clique para editar
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge badge-light-success fs-7 fw-bold">
                                        <span id="node-count">0</span> Etapas
                                    </span>
                                    <span class="badge badge-light-primary fs-7 fw-bold">
                                        <span id="edge-count">0</span> Conexões
                                    </span>
                                    <span class="badge badge-light-info fs-7 fw-bold">
                                        Snap: <span id="snap-status">8px</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!--end::Card footer-->
                    </div>
                    <!--end::Card Canvas-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Layout-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
<!--end::Content wrapper-->
@endsection

@push('styles')
<style>
/* Professional Workflow Designer Styles */
.workflow-canvas-container {
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px solid #e4e6ef;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Enhanced Node Styles */
.workflow-node {
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    border: 2px solid;
    background: white;
    font-family: inherit;
}

.workflow-node:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.workflow-node.selected {
    border-color: #1bc5bd !important;
    box-shadow: 0 0 0 3px rgba(27, 197, 189, 0.3);
}

/* Node Types with Modern Gradients */
.node-inicio {
    border-color: #50cd89;
    background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%);
}

.node-processo {
    border-color: #009ef7;
    background: linear-gradient(135deg, #e1f0ff 0%, #f0f8ff 100%);
}

.node-decisao {
    border-color: #ffc700;
    background: linear-gradient(135deg, #fff8e1 0%, #fffbf0 100%);
}

.node-final {
    border-color: #f1416c;
    background: linear-gradient(135deg, #ffeef2 0%, #fff5f8 100%);
}

/* Modern Edge Styles */
.workflow-edge {
    stroke: #009ef7;
    stroke-width: 3;
    stroke-linecap: round;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.workflow-edge.animated {
    stroke-dasharray: 8, 4;
    animation: edge-flow 2s linear infinite;
}

@keyframes edge-flow {
    0% { stroke-dashoffset: 0; }
    100% { stroke-dashoffset: 12; }
}

/* Enhanced Hover Effects */
.etapa-template:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

/* Smooth Transitions */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Canvas Grid Enhancement */
#workflow-canvas {
    background-image: 
        radial-gradient(circle, #e4e6ef 1px, transparent 1px);
    background-size: 16px 16px;
}

#workflow-canvas.snap-active {
    background-image: 
        radial-gradient(circle, #009ef7 1px, transparent 1px),
        radial-gradient(circle, #e4e6ef 0.5px, transparent 0.5px);
    background-size: 16px 16px, 8px 8px;
}

/* Loading Animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.loading {
    animation: pulse 1.5s ease-in-out infinite;
}
</style>
@endpush

@push('scripts')
<!-- ELK.js for Auto Layout -->
<script src="https://unpkg.com/elkjs@0.8.2/lib/elk.bundled.js"></script>

<script>
"use strict";

// Professional Workflow Designer with Modern Features
class WorkflowDesigner {
    constructor(workflowId, workflowName) {
        this.workflowId = workflowId;
        this.workflowName = workflowName;
        this.canvas = null;
        this.nodes = new Map();
        this.edges = new Map();
        this.selectedNode = null;
        this.selectedEdge = null;
        this.nextNodeId = 1;
        this.nextEdgeId = 1;
        this.snapToGrid = true;
        this.snapSize = 8;
        this.zoom = 1;
        this.panX = 0;
        this.panY = 0;
        this.isDragging = false;
        this.dragStart = { x: 0, y: 0 };
        
        this.init();
    }
    
    async init() {
        console.log('🚀 Inicializando Professional Workflow Designer...');
        
        this.canvas = document.getElementById('workflow-canvas');
        if (!this.canvas) {
            console.error('Canvas não encontrado!');
            return;
        }
        
        this.ctx = this.canvas.getContext('2d');
        
        // Setup responsive canvas
        this.setupResponsiveCanvas();
        this.setupCanvasEvents();
        this.setupControlEvents();
        this.setupTemplateEvents();
        
        // Load workflow data
        await this.loadWorkflowData();
        
        // Auto-arrange on load
        setTimeout(() => {
            this.autoArrangeNodes();
            this.updateStats();
        }, 500);
        
        console.log('✅ Professional Workflow Designer inicializado!');
    }
    
    setupResponsiveCanvas() {
        const container = document.getElementById('canvas-container');
        if (!container) return;
        
        // Make canvas responsive
        const resizeCanvas = () => {
            const rect = container.getBoundingClientRect();
            this.canvas.width = rect.width;
            this.canvas.height = rect.height;
            
            // Update canvas style for crisp rendering
            this.canvas.style.width = rect.width + 'px';
            this.canvas.style.height = rect.height + 'px';
            
            // Scale for high-DPI displays
            const dpr = window.devicePixelRatio || 1;
            const canvasRect = this.canvas.getBoundingClientRect();
            this.canvas.width = canvasRect.width * dpr;
            this.canvas.height = canvasRect.height * dpr;
            this.ctx.scale(dpr, dpr);
            
            this.canvas.style.width = canvasRect.width + 'px';
            this.canvas.style.height = canvasRect.height + 'px';
            
            console.log('📐 Canvas redimensionado:', { 
                width: this.canvas.width, 
                height: this.canvas.height,
                dpr: dpr
            });
            
            this.redraw();
        };
        
        // Initial resize
        resizeCanvas();
        
        // Resize on window resize
        window.addEventListener('resize', resizeCanvas);
        
        // Resize observer for container changes
        if (window.ResizeObserver) {
            const resizeObserver = new ResizeObserver(resizeCanvas);
            resizeObserver.observe(container);
        }
    }
    
    setupCanvasEvents() {
        let isMouseDown = false;
        let isPanning = false;
        let lastMousePos = { x: 0, y: 0 };
        let startPan = { x: 0, y: 0 };
        
        // Mouse down - start interaction
        this.canvas.addEventListener('mousedown', (e) => {
            isMouseDown = true;
            const rect = this.canvas.getBoundingClientRect();
            const canvasX = e.clientX - rect.left;
            const canvasY = e.clientY - rect.top;
            const worldX = canvasX / this.zoom - this.panX;
            const worldY = canvasY / this.zoom - this.panY;
            
            lastMousePos = { x: e.clientX, y: e.clientY };
            startPan = { x: this.panX, y: this.panY };
            
            const clickedNode = this.getNodeAtPosition(worldX, worldY);
            if (clickedNode) {
                // Node dragging mode
                this.selectNode(clickedNode);
                this.isDragging = true;
                this.dragStart = { x: worldX - clickedNode.x, y: worldY - clickedNode.y };
                this.canvas.style.cursor = 'grabbing';
                
                // Enable property actions
                this.enablePropertyActions(true);
                
                console.log('🎯 Nó selecionado:', clickedNode.label);
            } else {
                // Canvas panning mode
                this.selectedNode = null;
                this.enablePropertyActions(false);
                isPanning = true;
                this.canvas.style.cursor = 'grabbing';
                
                // Show pan indicator
                const indicator = document.getElementById('pan-indicator');
                if (indicator) indicator.style.display = 'block';
                
                this.updatePropertiesPanel();
                console.log('🤏 Modo pan ativado');
            }
        });
        
        // Mouse move - handle dragging and panning
        this.canvas.addEventListener('mousemove', (e) => {
            if (!isMouseDown) {
                // Update cursor based on hover
                const rect = this.canvas.getBoundingClientRect();
                const worldX = (e.clientX - rect.left) / this.zoom - this.panX;
                const worldY = (e.clientY - rect.top) / this.zoom - this.panY;
                const hoveredNode = this.getNodeAtPosition(worldX, worldY);
                this.canvas.style.cursor = hoveredNode ? 'grab' : 'default';
                return;
            }
            
            const deltaX = e.clientX - lastMousePos.x;
            const deltaY = e.clientY - lastMousePos.y;
            
            if (this.isDragging && this.selectedNode) {
                // Node dragging
                const rect = this.canvas.getBoundingClientRect();
                const worldX = (e.clientX - rect.left) / this.zoom - this.panX;
                const worldY = (e.clientY - rect.top) / this.zoom - this.panY;
                
                let newX = worldX - this.dragStart.x;
                let newY = worldY - this.dragStart.y;
                
                // Snap to grid
                if (this.snapToGrid) {
                    newX = Math.round(newX / this.snapSize) * this.snapSize;
                    newY = Math.round(newY / this.snapSize) * this.snapSize;
                }
                
                this.selectedNode.x = newX;
                this.selectedNode.y = newY;
                
                console.log('📍 Movendo nó para:', { x: newX, y: newY });
                this.redraw();
                
            } else if (isPanning) {
                // Canvas panning
                this.panX = startPan.x + deltaX / this.zoom;
                this.panY = startPan.y + deltaY / this.zoom;
                
                this.redraw();
            }
            
            lastMousePos = { x: e.clientX, y: e.clientY };
        });
        
        // Mouse up - end interaction
        this.canvas.addEventListener('mouseup', (e) => {
            isMouseDown = false;
            this.isDragging = false;
            isPanning = false;
            this.canvas.style.cursor = 'default';
            
            // Hide pan indicator
            const indicator = document.getElementById('pan-indicator');
            if (indicator) indicator.style.display = 'none';
            
            console.log('✋ Interação finalizada');
        });
        
        // Double click to edit
        this.canvas.addEventListener('dblclick', (e) => {
            const rect = this.canvas.getBoundingClientRect();
            const x = (e.clientX - rect.left) / this.zoom - this.panX;
            const y = (e.clientY - rect.top) / this.zoom - this.panY;
            
            const clickedNode = this.getNodeAtPosition(x, y);
            if (clickedNode) {
                this.editNode(clickedNode);
            }
        });
        
        // Zoom with mouse wheel
        this.canvas.addEventListener('wheel', (e) => {
            e.preventDefault();
            const rect = this.canvas.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;
            
            const zoomFactor = e.deltaY > 0 ? 0.85 : 1.15;
            const oldZoom = this.zoom;
            const newZoom = Math.max(0.25, Math.min(3, this.zoom * zoomFactor));
            
            if (newZoom !== oldZoom) {
                // Zoom towards mouse position
                const scale = newZoom / oldZoom;
                this.panX = mouseX / newZoom - (mouseX / oldZoom - this.panX) * scale;
                this.panY = mouseY / newZoom - (mouseY / oldZoom - this.panY) * scale;
                
                this.zoom = newZoom;
                this.updateZoomDisplay();
                this.redraw();
                
                console.log('🔍 Zoom alterado:', { 
                    zoom: Math.round(newZoom * 100) + '%', 
                    panX: this.panX.toFixed(2), 
                    panY: this.panY.toFixed(2) 
                });
            }
        });
        
        // Prevent context menu
        this.canvas.addEventListener('contextmenu', (e) => {
            e.preventDefault();
        });
    }
    
    setupControlEvents() {
        // Auto arrange
        document.getElementById('autoArrange')?.addEventListener('click', () => {
            this.autoArrangeNodes();
        });
        
        // Clear canvas
        document.getElementById('clearCanvas')?.addEventListener('click', () => {
            if (confirm('Tem certeza que deseja limpar o canvas?')) {
                this.nodes.clear();
                this.edges.clear();
                this.selectedNode = null;
                this.selectedEdge = null;
                this.updatePropertiesPanel();
                this.updateStats();
                this.redraw();
            }
        });
        
        // Snap grid toggle
        document.getElementById('snapGrid')?.addEventListener('click', (e) => {
            this.snapToGrid = !this.snapToGrid;
            e.target.classList.toggle('active', this.snapToGrid);
            document.getElementById('snap-status').textContent = this.snapToGrid ? '8px' : 'Off';
            this.canvas.classList.toggle('snap-active', this.snapToGrid);
        });
        
        // Zoom controls
        document.getElementById('zoomIn')?.addEventListener('click', () => this.zoomIn());
        document.getElementById('zoomOut')?.addEventListener('click', () => this.zoomOut());
        document.getElementById('resetZoom')?.addEventListener('click', () => this.resetZoom());
        document.getElementById('canvas-zoom-in')?.addEventListener('click', () => this.zoomIn());
        document.getElementById('canvas-zoom-out')?.addEventListener('click', () => this.zoomOut());
        document.getElementById('canvas-fit-view')?.addEventListener('click', () => this.fitView());
        document.getElementById('canvas-center')?.addEventListener('click', () => this.centerView());
        document.getElementById('fitView')?.addEventListener('click', () => this.fitView());
    }
    
    setupTemplateEvents() {
        const templates = document.querySelectorAll('.etapa-template');
        templates.forEach(template => {
            template.addEventListener('click', () => {
                const tipo = template.dataset.tipo;
                const nome = template.textContent.trim();
                this.addNode(tipo, nome);
            });
        });
    }
    
    async loadWorkflowData() {
        try {
            console.log('📊 Carregando dados do workflow...');
            const response = await fetch(`/admin/workflows/${this.workflowId}/designer-data`);
            const data = await response.json();
            
            if (data.success) {
                console.log('✅ Dados carregados:', data.data);
                
                // Load nodes (etapas) - ensure consistent ID format
                if (data.data.etapas && data.data.etapas.length > 0) {
                    data.data.etapas.forEach((etapa, index) => {
                        const nodeId = parseInt(etapa.id); // Ensure numeric ID for internal use
                        this.createNode({
                            id: nodeId,
                            x: 100 + (index % 3) * 280,
                            y: 100 + Math.floor(index / 3) * 150,
                            width: 220,
                            height: 80,
                            type: etapa.tipo || 'processo',
                            label: etapa.nome,
                            description: etapa.descricao || ''
                        });
                    });
                    
                    this.nextNodeId = Math.max(...data.data.etapas.map(e => parseInt(e.id))) + 1;
                }
                
                // Load edges (transições) - backend provides from/to keys, convert to IDs
                if (data.data.transicoes && data.data.transicoes.length > 0) {
                    data.data.transicoes.forEach(transicao => {
                        const edgeId = parseInt(transicao.id);
                        
                        // Find source and target nodes by matching etapa keys
                        const sourceNode = Array.from(this.nodes.values()).find(n => 
                            data.data.etapas.find(e => e.id == n.id)?.key === transicao.from
                        );
                        const targetNode = Array.from(this.nodes.values()).find(n => 
                            data.data.etapas.find(e => e.id == n.id)?.key === transicao.to
                        );
                        
                        if (!sourceNode || !targetNode) {
                            console.warn('❌ Skipping transition with missing nodes:', {
                                id: transicao.id,
                                from: transicao.from,
                                to: transicao.to,
                                sourceFound: !!sourceNode,
                                targetFound: !!targetNode
                            });
                            return;
                        }
                        
                        console.log('✅ Creating edge:', {
                            id: edgeId,
                            source: sourceNode.id,
                            target: targetNode.id,
                            from: transicao.from,
                            to: transicao.to
                        });
                        
                        this.createEdge({
                            id: edgeId,
                            source: sourceNode.id,
                            target: targetNode.id,
                            condition: transicao.condicao || '',
                            type: 'sequencial'
                        });
                    });
                    
                    this.nextEdgeId = Math.max(...data.data.transicoes.map(t => parseInt(t.id)).filter(id => !isNaN(id))) + 1;
                }
                
                this.redraw();
                this.updateStats(); // Update all statistics displays
            }
        } catch (error) {
            console.error('❌ Erro ao carregar workflow:', error);
        }
    }
    
    addNode(type, label) {
        const centerX = (this.canvas.width / 2) / this.zoom - this.panX;
        const centerY = (this.canvas.height / 2) / this.zoom - this.panY;
        
        const node = {
            id: this.nextNodeId++,
            x: centerX - 110 + Math.random() * 220,
            y: centerY - 40 + Math.random() * 80,
            width: 220,
            height: 80,
            type: type,
            label: label,
            description: ''
        };
        
        this.createNode(node);
        this.selectNode(node);
        this.redraw();
        this.updateStats();
        
        console.log('➕ Nó criado:', label, type);
    }
    
    createNode(nodeData) {
        const node = {
            id: nodeData.id,
            x: nodeData.x,
            y: nodeData.y,
            width: nodeData.width || 220,
            height: nodeData.height || 80,
            type: nodeData.type,
            label: nodeData.label,
            description: nodeData.description || ''
        };
        
        console.log('📦 Criando nó:', { id: node.id, type: typeof node.id, label: node.label });
        this.nodes.set(node.id, node);
        return node;
    }
    
    createEdge(edgeData) {
        const edge = {
            id: edgeData.id,
            source: edgeData.source,
            target: edgeData.target,
            condition: edgeData.condition || '',
            type: edgeData.type || 'sequential'
        };
        
        console.log('🔗 Criando edge:', { 
            id: edge.id, 
            source: edge.source, 
            target: edge.target,
            sourceType: typeof edge.source,
            targetType: typeof edge.target
        });
        this.edges.set(edge.id, edge);
        return edge;
    }
    
    getNodeAtPosition(x, y) {
        for (let node of this.nodes.values()) {
            if (x >= node.x && x <= node.x + node.width &&
                y >= node.y && y <= node.y + node.height) {
                return node;
            }
        }
        return null;
    }
    
    selectNode(node) {
        this.selectedNode = node;
        this.updatePropertiesPanel();
        this.redraw();
    }
    
    updateConnections(nodeId) {
        // Connections are automatically updated during redraw
        // This method exists for compatibility
    }
    
    async autoArrangeNodes(layoutType = 'layered') {
        if (typeof ELK === 'undefined') {
            console.warn('ELK.js não carregado, usando layout simples');
            this.simpleLayout();
            return;
        }
        
        if (this.nodes.size === 0) {
            console.log('Nenhum nó para organizar');
            return;
        }
        
        console.log(`🎯 Aplicando layout automático com ELK (${layoutType})...`);
        
        // Debug current data state
        console.log('📊 Estado atual do designer:', {
            totalNodes: this.nodes.size,
            totalEdges: this.edges.size,
            nodeIds: Array.from(this.nodes.keys()),
            edgeDetails: Array.from(this.edges.values()).map(e => ({
                id: e.id,
                source: e.source, 
                target: e.target
            }))
        });
        
        // Show loading indicator
        const autoBtn = document.getElementById('autoArrange');
        let originalText = '';
        if (autoBtn) {
            originalText = autoBtn.innerHTML;
            autoBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Organizando...';
            autoBtn.disabled = true;
        }
        
        // Layout configurations based on type
        const layoutConfigs = {
            layered: {
                'elk.algorithm': 'org.eclipse.elk.layered',
                'elk.direction': 'DOWN',
                'elk.layered.spacing.nodeNodeBetweenLayers': '120',
                'elk.spacing.nodeNode': '100',
                'elk.padding': '[50,50,50,50]',
                'elk.layered.crossingMinimization.strategy': 'INTERACTIVE'
            },
            force: {
                'elk.algorithm': 'org.eclipse.elk.force',
                'elk.spacing.nodeNode': '120',
                'elk.padding': '[50,50,50,50]'
            },
            radial: {
                'elk.algorithm': 'org.eclipse.elk.radial',
                'elk.spacing.nodeNode': '150',
                'elk.padding': '[100,100,100,100]'
            },
            circular: {
                'elk.algorithm': 'org.eclipse.elk.circle',
                'elk.spacing.nodeNode': '120',
                'elk.padding': '[80,80,80,80]'
            }
        };
        
        const elk = new ELK({
            defaultLayoutOptions: layoutConfigs[layoutType] || layoutConfigs.layered
        });
        
        // Prepare nodes - handle both string and number IDs
        const nodeArray = Array.from(this.nodes.values());
        const children = nodeArray.map(n => ({
            id: String(n.id),
            width: n.width || 180,
            height: n.height || 80
        }));
        
        // Prepare edges - ensure proper ID mapping and validate references
        const nodeIds = new Set(children.map(n => n.id));
        console.log('📋 Node IDs disponíveis:', Array.from(nodeIds));
        
        const edgeArray = Array.from(this.edges.values());
        console.log('🔗 Edges a processar:', edgeArray.map(e => ({ id: e.id, source: e.source, target: e.target })));
        
        const edges = edgeArray
            .filter(e => {
                // Convert to strings for comparison
                const sourceStr = String(e.source);
                const targetStr = String(e.target);
                
                const sourceExists = nodeIds.has(sourceStr);
                const targetExists = nodeIds.has(targetStr);
                
                if (!sourceExists || !targetExists) {
                    console.warn(`❌ Ignorando edge ${e.id}: source=${e.source}(${sourceExists}), target=${e.target}(${targetExists})`);
                    console.warn('  Nós disponíveis:', Array.from(nodeIds));
                    return false;
                }
                
                console.log(`✅ Edge válido ${e.id}: ${sourceStr} -> ${targetStr}`);
                return true;
            })
            .map(e => ({
                id: String(e.id),
                sources: [String(e.source)],
                targets: [String(e.target)]
            }));
        
        const graph = {
            id: 'root',
            layoutOptions: layoutConfigs[layoutType] || layoutConfigs.layered,
            children: children,
            edges: edges
        };
        
        try {
            console.log('📋 Layout graph:', {
                nodes: children.length,
                edges: edges.length,
                nodeIds: children.map(n => n.id),
                edgeConnections: edges.map(e => `${e.sources[0]} -> ${e.targets[0]}`)
            });
            
            // Validate graph before sending to ELK
            if (children.length === 0) {
                console.warn('Nenhum nó para layout');
                throw new Error('Nenhum nó disponível para layout');
            }
            
            const result = await elk.layout(graph);
            
            // Apply new positions with animation
            const duration = 800; // Animation duration
            const startTime = Date.now();
            const originalPositions = new Map();
            
            // Store original positions
            this.nodes.forEach((node, id) => {
                originalPositions.set(id, { x: node.x, y: node.y });
            });
            
            // Apply new positions from ELK result
            result.children.forEach(layoutNode => {
                console.log(`📍 Aplicando posição para nó ${layoutNode.id}: (${layoutNode.x}, ${layoutNode.y})`);
                
                // Try different ID formats to find the node
                let node = null;
                
                // Try as string first
                node = this.nodes.get(layoutNode.id);
                if (!node) {
                    // Try as number
                    const numericId = parseInt(layoutNode.id);
                    if (!isNaN(numericId)) {
                        node = this.nodes.get(numericId);
                    }
                }
                
                // Try searching by string conversion of existing IDs
                if (!node) {
                    for (let [key, value] of this.nodes.entries()) {
                        if (String(key) === String(layoutNode.id)) {
                            node = value;
                            break;
                        }
                    }
                }
                
                if (node) {
                    // Store target position
                    node.targetX = layoutNode.x;
                    node.targetY = layoutNode.y;
                } else {
                    console.warn(`❌ Nó não encontrado para ID: ${layoutNode.id}`);
                }
            });
            
            // Animate to new positions
            const animate = () => {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easeProgress = 1 - Math.pow(1 - progress, 3); // Ease-out cubic
                
                this.nodes.forEach((node, id) => {
                    const original = originalPositions.get(id);
                    if (original && node.targetX !== undefined && node.targetY !== undefined) {
                        node.x = original.x + (node.targetX - original.x) * easeProgress;
                        node.y = original.y + (node.targetY - original.y) * easeProgress;
                    }
                });
                
                this.redraw();
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    // Clean up target positions
                    this.nodes.forEach(node => {
                        delete node.targetX;
                        delete node.targetY;
                    });
                    
                    // Restore button
                    if (autoBtn) {
                        autoBtn.innerHTML = originalText;
                        autoBtn.disabled = false;
                    }
                    
                    // Fit view after animation
                    setTimeout(() => this.fitView(), 200);
                    
                    console.log('✅ Layout automático aplicado com animação!');
                }
            };
            
            animate();
            
        } catch (error) {
            console.error('❌ Erro no layout automático:', error);
            
            // Restore button
            if (autoBtn) {
                autoBtn.innerHTML = originalText;
                autoBtn.disabled = false;
            }
            
            // Fallback to simple layout
            this.simpleLayout();
        }
    }
    
    simpleLayout() {
        const nodes = Array.from(this.nodes.values());
        const cols = Math.ceil(Math.sqrt(nodes.length));
        
        nodes.forEach((node, index) => {
            const col = index % cols;
            const row = Math.floor(index / cols);
            node.x = 50 + col * 300;
            node.y = 50 + row * 150;
        });
        
        this.redraw();
    }
    
    zoomIn() {
        this.zoom = Math.min(3, this.zoom * 1.15);
        this.updateZoomDisplay();
        this.redraw();
        console.log('🔍 Zoom in:', Math.round(this.zoom * 100) + '%');
    }
    
    zoomOut() {
        this.zoom = Math.max(0.25, this.zoom / 1.15);
        this.updateZoomDisplay();
        this.redraw();
        console.log('🔍 Zoom out:', Math.round(this.zoom * 100) + '%');
    }
    
    resetZoom() {
        this.zoom = 1;
        this.panX = 0;
        this.panY = 0;
        this.updateZoomDisplay();
        this.redraw();
    }
    
    fitView() {
        if (this.nodes.size === 0) return;
        
        const nodes = Array.from(this.nodes.values());
        const minX = Math.min(...nodes.map(n => n.x));
        const minY = Math.min(...nodes.map(n => n.y));
        const maxX = Math.max(...nodes.map(n => n.x + n.width));
        const maxY = Math.max(...nodes.map(n => n.y + n.height));
        
        const contentWidth = maxX - minX;
        const contentHeight = maxY - minY;
        
        const margin = 50;
        const scaleX = (this.canvas.width - margin * 2) / contentWidth;
        const scaleY = (this.canvas.height - margin * 2) / contentHeight;
        
        this.zoom = Math.min(scaleX, scaleY, 1.5);
        this.panX = (this.canvas.width / this.zoom - contentWidth) / 2 - minX;
        this.panY = (this.canvas.height / this.zoom - contentHeight) / 2 - minY;
        
        this.updateZoomDisplay();
        this.redraw();
    }
    
    updateZoomDisplay() {
        const zoomPercentage = Math.round(this.zoom * 100);
        
        // Update all zoom display elements
        const zoomElements = ['zoom-level', 'zoom-indicator', 'zoom-percentage'];
        zoomElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = `${zoomPercentage}%`;
            }
        });
        
        // Update zoom slider if exists
        const zoomSlider = document.getElementById('zoom-slider');
        if (zoomSlider) {
            zoomSlider.value = zoomPercentage;
        }
        
        // Update grid overlay position
        const gridOverlay = document.getElementById('grid-overlay');
        if (gridOverlay) {
            const size = Math.max(10, 20 / this.zoom);
            gridOverlay.style.backgroundSize = `${size}px ${size}px`;
            gridOverlay.style.backgroundPosition = `${this.panX * this.zoom}px ${this.panY * this.zoom}px`;
        }
        
        // Update minimap
        this.updateMinimap();
    }
    
    updateStats() {
        document.getElementById('node-count').textContent = this.nodes.size;
        document.getElementById('edge-count').textContent = this.edges.size;
    }
    
    updatePropertiesPanel() {
        const panel = document.getElementById('properties-panel');
        if (!panel) return;
        
        if (this.selectedNode) {
            const node = this.selectedNode;
            panel.innerHTML = `
                <div class="mb-5">
                    <label class="form-label fw-semibold">Nome da Etapa</label>
                    <input type="text" class="form-control" value="${node.label}" onchange="workflowDesigner.updateNodeProperty('label', this.value)" />
                </div>
                <div class="mb-5">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select class="form-control" onchange="workflowDesigner.updateNodeProperty('type', this.value)">
                        <option value="inicio" ${node.type === 'inicio' ? 'selected' : ''}>Início</option>
                        <option value="processo" ${node.type === 'processo' ? 'selected' : ''}>Processo</option>
                        <option value="decisao" ${node.type === 'decisao' ? 'selected' : ''}>Decisão</option>
                        <option value="final" ${node.type === 'final' ? 'selected' : ''}>Final</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="form-label fw-semibold">Descrição</label>
                    <textarea class="form-control" rows="3" onchange="workflowDesigner.updateNodeProperty('description', this.value)">${node.description}</textarea>
                </div>
                <div class="mb-5">
                    <label class="form-label fw-semibold">Posição</label>
                    <div class="row">
                        <div class="col-6">
                            <input type="number" class="form-control" placeholder="X" value="${Math.round(node.x)}" onchange="workflowDesigner.updateNodeProperty('x', parseInt(this.value))" />
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control" placeholder="Y" value="${Math.round(node.y)}" onchange="workflowDesigner.updateNodeProperty('y', parseInt(this.value))" />
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-sm btn-light-danger" onclick="workflowDesigner.deleteSelectedNode()">
                        <i class="ki-duotone ki-trash fs-7">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                        Excluir Etapa
                    </button>
                </div>
            `;
        } else {
            panel.innerHTML = `
                <div class="text-center py-8">
                    <i class="ki-duotone ki-information-5 fs-3x text-muted mb-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <p class="text-muted">Selecione um elemento para editar suas propriedades</p>
                </div>
            `;
        }
    }
    
    updateNodeProperty(property, value) {
        if (this.selectedNode) {
            this.selectedNode[property] = value;
            this.redraw();
        }
    }
    
    deleteSelectedNode() {
        if (this.selectedNode) {
            const nodeId = this.selectedNode.id;
            
            // Remove related edges
            const edgesToRemove = [];
            this.edges.forEach((edge, id) => {
                if (edge.source === nodeId || edge.target === nodeId) {
                    edgesToRemove.push(id);
                }
            });
            edgesToRemove.forEach(id => this.edges.delete(id));
            
            // Remove node
            this.nodes.delete(nodeId);
            this.selectedNode = null;
            
            this.updatePropertiesPanel();
            this.updateStats();
            this.redraw();
        }
    }
    
    editNode(node) {
        // For now, just select the node to show properties
        this.selectNode(node);
    }
    
    redraw() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.ctx.save();
        
        // Apply zoom and pan
        this.ctx.scale(this.zoom, this.zoom);
        this.ctx.translate(this.panX, this.panY);
        
        // Draw edges first (behind nodes)
        this.drawEdges();
        
        // Draw nodes
        this.drawNodes();
        
        this.ctx.restore();
    }
    
    drawEdges() {
        if (this.edges.size === 0) {
            console.log('🔗 Nenhuma edge para desenhar');
            return;
        }
        
        console.log('🎨 Desenhando', this.edges.size, 'edges');
        
        this.edges.forEach(edge => {
            const sourceNode = this.nodes.get(edge.source);
            const targetNode = this.nodes.get(edge.target);
            
            if (!sourceNode || !targetNode) {
                console.warn('❌ Edge inválida:', edge, 'Source:', !!sourceNode, 'Target:', !!targetNode);
                return;
            }
            
            // Calculate connection points
            const startX = sourceNode.x + sourceNode.width / 2;
            const startY = sourceNode.y + sourceNode.height;
            const endX = targetNode.x + targetNode.width / 2;
            const endY = targetNode.y;
            
            // Draw connection line with smooth curve
            this.ctx.beginPath();
            this.ctx.moveTo(startX, startY);
            
            const controlPointOffset = Math.abs(endY - startY) * 0.4;
            const cp1X = startX;
            const cp1Y = startY + controlPointOffset;
            const cp2X = endX;
            const cp2Y = endY - controlPointOffset;
            
            this.ctx.bezierCurveTo(cp1X, cp1Y, cp2X, cp2Y, endX, endY);
            
            // Style the connection
            this.ctx.strokeStyle = edge === this.selectedEdge ? '#ff6b35' : '#009ef7';
            this.ctx.lineWidth = edge === this.selectedEdge ? 4 : 2;
            this.ctx.lineCap = 'round';
            this.ctx.stroke();
            
            // Calculate arrow angle from control point to end
            const arrowAngle = Math.atan2(endY - cp2Y, endX - cp2X);
            this.drawArrow(endX, endY, arrowAngle);
            
            console.log('✅ Edge desenhada:', edge.id, `${sourceNode.label} -> ${targetNode.label}`);
        });
    }
    
    drawArrow(x, y, angle) {
        const headLength = 10;
        const headWidth = Math.PI / 6;
        
        // Calculate arrow points
        const x1 = x - headLength * Math.cos(angle - headWidth);
        const y1 = y - headLength * Math.sin(angle - headWidth);
        const x2 = x - headLength * Math.cos(angle + headWidth);
        const y2 = y - headLength * Math.sin(angle + headWidth);
        
        // Draw filled arrow head
        this.ctx.fillStyle = this.ctx.strokeStyle; // Use same color as line
        this.ctx.beginPath();
        this.ctx.moveTo(x, y);
        this.ctx.lineTo(x1, y1);
        this.ctx.lineTo(x2, y2);
        this.ctx.closePath();
        this.ctx.fill();
    }
    
    drawNodes() {
        this.nodes.forEach(node => {
            // Node background with gradient
            const gradient = this.ctx.createLinearGradient(node.x, node.y, node.x, node.y + node.height);
            
            switch (node.type) {
                case 'inicio':
                    gradient.addColorStop(0, '#e8f5e8');
                    gradient.addColorStop(1, '#f0f9f0');
                    break;
                case 'processo':
                    gradient.addColorStop(0, '#e1f0ff');
                    gradient.addColorStop(1, '#f0f8ff');
                    break;
                case 'decisao':
                    gradient.addColorStop(0, '#fff8e1');
                    gradient.addColorStop(1, '#fffbf0');
                    break;
                case 'final':
                    gradient.addColorStop(0, '#ffeef2');
                    gradient.addColorStop(1, '#fff5f8');
                    break;
                default:
                    gradient.addColorStop(0, '#f8f9fa');
                    gradient.addColorStop(1, '#ffffff');
            }
            
            // Draw rounded rectangle
            this.drawRoundedRect(node.x, node.y, node.width, node.height, 12, gradient);
            
            // Draw border
            this.ctx.beginPath();
            this.ctx.roundRect(node.x, node.y, node.width, node.height, 12);
            this.ctx.strokeStyle = this.getNodeBorderColor(node.type);
            this.ctx.lineWidth = node === this.selectedNode ? 4 : 2;
            this.ctx.stroke();
            
            // Selection glow
            if (node === this.selectedNode) {
                this.ctx.beginPath();
                this.ctx.roundRect(node.x - 3, node.y - 3, node.width + 6, node.height + 6, 15);
                this.ctx.strokeStyle = 'rgba(27, 197, 189, 0.5)';
                this.ctx.lineWidth = 6;
                this.ctx.stroke();
            }
            
            // Draw text
            this.ctx.fillStyle = '#181c32';
            this.ctx.font = 'bold 14px Inter, sans-serif';
            this.ctx.textAlign = 'center';
            this.ctx.textBaseline = 'middle';
            
            // Title
            const centerX = node.x + node.width / 2;
            const centerY = node.y + node.height / 2 - 8;
            this.ctx.fillText(node.label, centerX, centerY);
            
            // Subtitle
            this.ctx.fillStyle = '#7e8299';
            this.ctx.font = '10px Inter, sans-serif';
            this.ctx.fillText(node.type.toUpperCase(), centerX, centerY + 18);
        });
    }
    
    drawRoundedRect(x, y, width, height, radius, fillStyle) {
        this.ctx.beginPath();
        this.ctx.roundRect(x, y, width, height, radius);
        this.ctx.fillStyle = fillStyle;
        this.ctx.fill();
    }
    
    getNodeBorderColor(type) {
        const colors = {
            'inicio': '#50cd89',
            'processo': '#009ef7',
            'decisao': '#ffc700',
            'final': '#f1416c'
        };
        return colors[type] || '#009ef7';
    }
    
    // Enhanced UX Methods
    centerView() {
        if (this.nodes.size === 0) {
            this.panX = 0;
            this.panY = 0;
        } else {
            const nodes = Array.from(this.nodes.values());
            const centerX = nodes.reduce((sum, n) => sum + n.x, 0) / nodes.length;
            const centerY = nodes.reduce((sum, n) => sum + n.y, 0) / nodes.length;
            
            this.panX = (this.canvas.width / this.zoom) / 2 - centerX;
            this.panY = (this.canvas.height / this.zoom) / 2 - centerY;
        }
        
        this.redraw();
    }
    
    setZoom(zoomValue) {
        this.zoom = Math.max(0.25, Math.min(3, zoomValue));
        this.updateZoomDisplay();
        this.redraw();
    }
    
    toggleSnapToGrid() {
        this.snapToGrid = !this.snapToGrid;
        const snapButton = document.getElementById('snapGrid');
        const snapText = snapButton?.querySelector('.snap-text');
        
        if (this.snapToGrid) {
            snapButton?.classList.add('btn-light-info');
            snapButton?.classList.remove('btn-light');
            snapButton?.setAttribute('data-active', 'true');
            if (snapText) snapText.textContent = 'Snap Grid (8px)';
        } else {
            snapButton?.classList.remove('btn-light-info');
            snapButton?.classList.add('btn-light');
            snapButton?.setAttribute('data-active', 'false');
            if (snapText) snapText.textContent = 'Snap Desativado';
        }
    }
    
    duplicateSelectedElement() {
        if (!this.selectedNode) return;
        
        const original = this.selectedNode;
        const newNode = {
            id: 'node_' + this.nextNodeId++,
            x: original.x + 50,
            y: original.y + 50,
            width: original.width,
            height: original.height,
            label: original.label + ' (Cópia)',
            type: original.type,
            description: original.description || ''
        };
        
        this.nodes.set(newNode.id, newNode);
        this.selectedNode = newNode;
        this.updateStats();
        this.updatePropertiesPanel();
        this.redraw();
    }
    
    deleteSelectedElement() {
        if (!this.selectedNode) return;
        
        // Confirm deletion
        if (!confirm('Tem certeza que deseja excluir este elemento?')) return;
        
        const nodeId = this.selectedNode.id;
        
        // Remove connected edges
        const edgesToRemove = [];
        this.edges.forEach((edge, edgeId) => {
            if (edge.source === nodeId || edge.target === nodeId) {
                edgesToRemove.push(edgeId);
            }
        });
        
        edgesToRemove.forEach(edgeId => this.edges.delete(edgeId));
        
        // Remove node
        this.nodes.delete(nodeId);
        this.selectedNode = null;
        
        // Update UI
        this.enablePropertyActions(false);
        this.updateStats();
        this.updatePropertiesPanel();
        this.redraw();
    }
    
    enablePropertyActions(enabled) {
        const duplicateBtn = document.getElementById('duplicateElement');
        const deleteBtn = document.getElementById('deleteElement');
        
        if (duplicateBtn) duplicateBtn.disabled = !enabled;
        if (deleteBtn) deleteBtn.disabled = !enabled;
    }
    
    saveWorkflow() {
        const workflowData = {
            workflow_id: this.workflowId,
            nodes: Array.from(this.nodes.values()),
            edges: Array.from(this.edges.values())
        };
        
        // Show saving indicator
        const saveBtn = document.getElementById('saveWorkflow');
        if (saveBtn) {
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Salvando...';
            saveBtn.disabled = true;
            
            // Simulate save (replace with actual API call)
            setTimeout(() => {
                console.log('Salvando workflow:', workflowData);
                
                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('Workflow salvo com sucesso!');
                } else {
                    alert('Workflow salvo com sucesso!');
                }
                
                // Restore button
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }, 1000);
        }
    }
    
    validateWorkflow() {
        const errors = [];
        
        // Check for start nodes
        const startNodes = Array.from(this.nodes.values()).filter(n => n.type === 'inicio');
        if (startNodes.length === 0) {
            errors.push('Workflow deve ter pelo menos uma etapa de início');
        } else if (startNodes.length > 1) {
            errors.push('Workflow deve ter apenas uma etapa de início');
        }
        
        // Check for end nodes
        const endNodes = Array.from(this.nodes.values()).filter(n => n.type === 'final');
        if (endNodes.length === 0) {
            errors.push('Workflow deve ter pelo menos uma etapa final');
        }
        
        // Check for orphaned nodes
        const connectedNodes = new Set();
        this.edges.forEach(edge => {
            connectedNodes.add(edge.source);
            connectedNodes.add(edge.target);
        });
        
        const orphanedNodes = Array.from(this.nodes.keys()).filter(id => 
            !connectedNodes.has(id) && this.nodes.size > 1
        );
        
        if (orphanedNodes.length > 0) {
            errors.push(`${orphanedNodes.length} nó(s) não conectado(s)`);
        }
        
        // Show results
        if (errors.length === 0) {
            if (typeof toastr !== 'undefined') {
                toastr.success('Workflow válido! ✓');
            } else {
                alert('Workflow válido! ✓');
            }
        } else {
            const errorMsg = 'Problemas encontrados:\n' + errors.map(e => '• ' + e).join('\n');
            if (typeof toastr !== 'undefined') {
                toastr.warning(errorMsg);
            } else {
                alert(errorMsg);
            }
        }
    }
    
    updateMinimap() {
        const minimapCanvas = document.getElementById('minimap-canvas');
        const placeholder = document.getElementById('minimap-placeholder');
        
        if (!minimapCanvas) return;
        
        const ctx = minimapCanvas.getContext('2d');
        ctx.clearRect(0, 0, minimapCanvas.width, minimapCanvas.height);
        
        if (this.nodes.size === 0) {
            if (placeholder) placeholder.style.display = 'block';
            return;
        }
        
        if (placeholder) placeholder.style.display = 'none';
        
        // Calculate bounds
        const nodes = Array.from(this.nodes.values());
        const bounds = {
            minX: Math.min(...nodes.map(n => n.x)),
            minY: Math.min(...nodes.map(n => n.y)),
            maxX: Math.max(...nodes.map(n => n.x + n.width)),
            maxY: Math.max(...nodes.map(n => n.y + n.height))
        };
        
        const contentWidth = bounds.maxX - bounds.minX;
        const contentHeight = bounds.maxY - bounds.minY;
        
        if (contentWidth === 0 || contentHeight === 0) return;
        
        // Calculate scale
        const padding = 10;
        const scaleX = (minimapCanvas.width - padding * 2) / contentWidth;
        const scaleY = (minimapCanvas.height - padding * 2) / contentHeight;
        const scale = Math.min(scaleX, scaleY, 1);
        
        // Center offset
        const offsetX = (minimapCanvas.width - contentWidth * scale) / 2 - bounds.minX * scale;
        const offsetY = (minimapCanvas.height - contentHeight * scale) / 2 - bounds.minY * scale;
        
        // Draw edges
        this.edges.forEach(edge => {
            const sourceNode = this.nodes.get(edge.source);
            const targetNode = this.nodes.get(edge.target);
            
            if (sourceNode && targetNode) {
                ctx.strokeStyle = '#6c757d';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(
                    sourceNode.x * scale + offsetX + (sourceNode.width * scale) / 2,
                    sourceNode.y * scale + offsetY + (sourceNode.height * scale) / 2
                );
                ctx.lineTo(
                    targetNode.x * scale + offsetX + (targetNode.width * scale) / 2,
                    targetNode.y * scale + offsetY + (targetNode.height * scale) / 2
                );
                ctx.stroke();
            }
        });
        
        // Draw nodes
        nodes.forEach(node => {
            const x = node.x * scale + offsetX;
            const y = node.y * scale + offsetY;
            const width = Math.max(node.width * scale, 4);
            const height = Math.max(node.height * scale, 4);
            
            // Node background
            const colors = {
                'inicio': '#50cd89',
                'processo': '#009ef7', 
                'decisao': '#ffc700',
                'final': '#f1416c'
            };
            
            ctx.fillStyle = colors[node.type] || '#009ef7';
            ctx.fillRect(x, y, width, height);
            
            // Selected node highlight
            if (this.selectedNode && this.selectedNode.id === node.id) {
                ctx.strokeStyle = '#ff6b35';
                ctx.lineWidth = 2;
                ctx.strokeRect(x - 1, y - 1, width + 2, height + 2);
            }
        });
    }
}

// Initialize Enhanced Workflow Designer
document.addEventListener('DOMContentLoaded', function() {
    const workflowId = {{ $workflow->id }};
    const workflowName = "{{ $workflow->nome }}";
    
    console.log('🚀 Inicializando Professional Workflow Designer com UX melhorado...');
    window.workflowDesigner = new WorkflowDesigner(workflowId, workflowName);
    
    // Setup enhanced event listeners
    setupEnhancedEventListeners();
    
    console.log('✅ Professional Workflow Designer inicializado com sucesso!');
});

// Enhanced Event Listeners Setup
function setupEnhancedEventListeners() {
    // Zoom controls
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const zoomSlider = document.getElementById('zoom-slider');
    
    if (zoomInBtn) zoomInBtn.addEventListener('click', () => window.workflowDesigner.zoomIn());
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => window.workflowDesigner.zoomOut());
    if (zoomSlider) {
        zoomSlider.addEventListener('input', (e) => {
            window.workflowDesigner.setZoom(parseInt(e.target.value) / 100);
        });
    }
    
    // View controls
    const centerViewBtn = document.getElementById('centerView');
    const fitViewBtn = document.getElementById('fitView');
    
    if (centerViewBtn) centerViewBtn.addEventListener('click', () => window.workflowDesigner.centerView());
    if (fitViewBtn) fitViewBtn.addEventListener('click', () => window.workflowDesigner.fitView());
    
    // Snap grid toggle
    const snapGridBtn = document.getElementById('snapGrid');
    if (snapGridBtn) snapGridBtn.addEventListener('click', () => window.workflowDesigner.toggleSnapToGrid());
    
    // Property actions
    const duplicateBtn = document.getElementById('duplicateElement');
    const deleteBtn = document.getElementById('deleteElement');
    
    if (duplicateBtn) duplicateBtn.addEventListener('click', () => window.workflowDesigner.duplicateSelectedElement());
    if (deleteBtn) deleteBtn.addEventListener('click', () => window.workflowDesigner.deleteSelectedElement());
    
    // Workflow actions
    const saveBtn = document.getElementById('saveWorkflow');
    const validateBtn = document.getElementById('validateWorkflow');
    
    if (saveBtn) saveBtn.addEventListener('click', () => window.workflowDesigner.saveWorkflow());
    if (validateBtn) validateBtn.addEventListener('click', () => window.workflowDesigner.validateWorkflow());
    
    // Auto-arrange
    const autoArrangeBtn = document.getElementById('autoArrange');
    if (autoArrangeBtn) autoArrangeBtn.addEventListener('click', () => window.workflowDesigner.autoArrangeNodes());
    
    // Clear canvas
    const clearBtn = document.getElementById('clearCanvas');
    if (clearBtn) clearBtn.addEventListener('click', () => window.workflowDesigner.clearCanvas());
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case '+':
                case '=':
                    e.preventDefault();
                    window.workflowDesigner.zoomIn();
                    break;
                case '-':
                    e.preventDefault();
                    window.workflowDesigner.zoomOut();
                    break;
                case '0':
                    e.preventDefault();
                    window.workflowDesigner.fitView();
                    break;
                case 's':
                    e.preventDefault();
                    window.workflowDesigner.saveWorkflow();
                    break;
                case 'd':
                    e.preventDefault();
                    if (window.workflowDesigner.selectedNode) {
                        window.workflowDesigner.duplicateSelectedElement();
                    }
                    break;
            }
        }
        
        // Delete selected element
        if (e.key === 'Delete' && window.workflowDesigner.selectedNode) {
            e.preventDefault();
            window.workflowDesigner.deleteSelectedElement();
        }
        
        // ESC to deselect
        if (e.key === 'Escape') {
            window.workflowDesigner.selectedNode = null;
            window.workflowDesigner.enablePropertyActions(false);
            window.workflowDesigner.updatePropertiesPanel();
            window.workflowDesigner.redraw();
        }
    });
    
    // Minimap click navigation
    const minimapCanvas = document.getElementById('minimap-canvas');
    if (minimapCanvas) {
        minimapCanvas.addEventListener('click', (e) => {
            const rect = e.target.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Convert minimap coordinates to canvas coordinates (simplified)
            const canvasX = (x / rect.width) * window.workflowDesigner.canvas.width;
            const canvasY = (y / rect.height) * window.workflowDesigner.canvas.height;
            
            // Center view on clicked position
            window.workflowDesigner.panX = -(canvasX / window.workflowDesigner.zoom - window.workflowDesigner.canvas.width / 2 / window.workflowDesigner.zoom);
            window.workflowDesigner.panY = -(canvasY / window.workflowDesigner.zoom - window.workflowDesigner.canvas.height / 2 / window.workflowDesigner.zoom);
            window.workflowDesigner.redraw();
        });
        
        // Minimap hover cursor
        minimapCanvas.style.cursor = 'crosshair';
    }
    
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            if (!tooltipTriggerEl.getAttribute('data-bs-toggle')) {
                tooltipTriggerEl.setAttribute('data-bs-toggle', 'tooltip');
            }
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    console.log('🎮 Enhanced event listeners configurados');
}
</script>
@endpush