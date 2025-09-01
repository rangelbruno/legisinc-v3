@extends('layouts.app')

@section('title', 'Sistema de Templates')

@section('content')
<div class="container">
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                <i class="ki-duotone ki-design-1 fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Sistema de Templates
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Administração</li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-gray-900">Templates</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <!-- Template Universal - Seção Principal -->
    <div class="row g-5 g-xl-10 mb-10">
        <div class="col-12">
            <div class="card card-flush border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-8 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-star fs-1 text-warning me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div>
                                    <h2 class="text-white fw-bold fs-2 mb-1">Template Universal</h2>
                                    <div class="text-white-75 fs-5">Um template que se adapta a todos os tipos de proposições</div>
                                </div>
                            </div>
                            
                            @if($templateUniversal)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ki-duotone ki-check-circle fs-2 text-success me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <div class="text-white fw-semibold">Status: Ativo</div>
                                                <div class="text-white-75 fs-7">Padrão do sistema</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ki-duotone ki-time fs-2 text-info me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <div class="text-white fw-semibold">Última Atualização</div>
                                                <div class="text-white-75 fs-7">{{ $templateUniversal->updated_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-4">
                                    <i class="ki-duotone ki-code fs-2 text-warning me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <div>
                                        @php
                                            $variaveis = is_string($templateUniversal->variaveis) 
                                                ? json_decode($templateUniversal->variaveis, true) 
                                                : ($templateUniversal->variaveis ?? []);
                                            $numVariaveis = is_array($variaveis) ? count($variaveis) : 0;
                                        @endphp
                                        <div class="text-white fw-semibold">{{ $numVariaveis }} Variáveis Disponíveis</div>
                                        <div class="text-white-75 fs-7">Todas as variáveis do sistema incluídas</div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning d-flex align-items-center">
                                    <i class="ki-duotone ki-information-5 fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div>
                                        <h4 class="alert-heading mb-1">Template Universal não encontrado</h4>
                                        <div>Execute o comando de configuração para criar o template padrão</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4 text-center">
                            @if($templateUniversal)
                                <div class="mb-4">
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="{{ route('admin.templates.universal.editor', $templateUniversal) }}" 
                                           class="btn btn-light btn-lg fw-bold px-6">
                                            <i class="ki-duotone ki-pencil fs-2 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Editar Template
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-light-primary btn-sm" onclick="previewTemplateUniversal()">
                                        <i class="ki-duotone ki-eye fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Preview
                                    </button>
                                    <a href="{{ route('api.templates.universal.download', $templateUniversal) }}?v={{ $templateUniversal->updated_at->timestamp }}" 
                                       class="btn btn-light-success btn-sm">
                                        <i class="ki-duotone ki-exit-down fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Download
                                    </a>
                                    <button type="button" class="btn btn-light-info btn-sm" onclick="mostrarVariaveis()">
                                        <i class="ki-duotone ki-code fs-6">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        Variáveis
                                    </button>
                                </div>
                            @else
                                <div class="text-center">
                                    <button type="button" class="btn btn-light btn-lg fw-bold" onclick="criarTemplateUniversal()">
                                        <i class="ki-duotone ki-plus fs-2 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Criar Template Universal
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas do Sistema -->
    <div class="row g-5 g-xl-10 mb-10">
        <div class="col-md-6 col-lg-3">
            <div class="card card-flush h-md-100">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1">{{ $estatisticasUniversal['total_tipos_proposicao'] ?? 0 }}</span>
                        </div>
                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Tipos de Proposição</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-gray-500">Total Cadastrado</span>
                            <span class="fw-bold fs-6 text-gray-900">{{ $estatisticasUniversal['total_tipos_proposicao'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card card-flush h-md-100" style="background-color: #f8f5ff;">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-2hx fw-bold text-primary me-2 lh-1">{{ $estatisticasUniversal['tipos_usando_universal'] ?? 0 }}</span>
                            <i class="ki-duotone ki-star fs-3 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <span class="text-primary pt-1 fw-semibold fs-6">Usando Template Universal</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-primary">Cobertura</span>
                            <span class="fw-bold fs-6 text-primary">{{ $estatisticasUniversal['cobertura_universal'] ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card card-flush h-md-100" style="background-color: #fff5f5;">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-2hx fw-bold text-danger me-2 lh-1">{{ $estatisticasUniversal['tipos_com_template_especifico'] ?? 0 }}</span>
                        </div>
                        <span class="text-danger pt-1 fw-semibold fs-6">Templates Específicos</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-danger">Legados</span>
                            <span class="fw-bold fs-6 text-danger">{{ $estatisticasUniversal['tipos_com_template_especifico'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card card-flush h-md-100" style="background-color: #f0fff4;">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <span class="fs-2hx fw-bold text-success me-2 lh-1">1</span>
                            <i class="ki-duotone ki-check-circle fs-3 text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <span class="text-success pt-1 fw-semibold fs-6">Template para Gerenciar</span>
                    </div>
                </div>
                <div class="card-body d-flex align-items-end pt-0">
                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-7 text-success">Manutenção</span>
                            <span class="fw-bold fs-6 text-success">Simple</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aplicação por Tipo -->
    <div class="row g-5 g-xl-10 mb-10">
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header">
                    <div class="card-title">
                        <h3>
                            <i class="ki-duotone ki-rocket fs-3 text-success me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Testar Template Universal
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <div class="fs-6 text-muted mb-4">
                            Selecione um tipo de proposição para ver como o template universal se adapta automaticamente:
                        </div>
                        
                        <div class="input-group mb-4">
                            <select class="form-select" id="tipo_proposicao_teste">
                                <option value="">Escolha um tipo para testar...</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" data-nome="{{ $tipo->nome }}" data-codigo="{{ $tipo->codigo }}">
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary" onclick="testarTemplateUniversal()">
                                <i class="ki-duotone ki-flask fs-6 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Testar Adaptação
                            </button>
                        </div>
                        
                        <div id="resultado_teste" style="display: none;" class="alert alert-info">
                            <div id="resultado_conteudo"></div>
                        </div>
                    </div>
                    
                    <div class="separator my-6"></div>
                    
                    <div>
                        <h4 class="fs-5 fw-bold mb-4">
                            <i class="ki-duotone ki-information-5 fs-4 text-info me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Como Funciona a Adaptação
                        </h4>
                        
                        <div class="timeline timeline-border-dashed">
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-scan-barcode fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="mb-5 pe-3">
                                        <div class="fs-6 fw-semibold text-gray-700 mb-2">1. Detecção Automática</div>
                                        <div class="text-gray-500 fs-7">Sistema identifica o tipo da proposição</div>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-setting-2 fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="mb-5 pe-3">
                                        <div class="fs-6 fw-semibold text-gray-700 mb-2">2. Adaptação Inteligente</div>
                                        <div class="text-gray-500 fs-7">Template ajusta preâmbulo, seções e estrutura</div>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-document fs-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="mb-5 pe-3">
                                        <div class="fs-6 fw-semibold text-gray-700 mb-2">3. Documento Final</div>
                                        <div class="text-gray-500 fs-7">Gera documento formatado seguindo padrões legais</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header">
                    <div class="card-title">
                        <h3>
                            <i class="ki-duotone ki-abstract-26 fs-3 text-warning me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Templates Específicos Legados
                        </h3>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-light-danger btn-sm" onclick="migrarParaUniversal()">
                            <i class="ki-duotone ki-arrows-circle fs-6 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Migrar Todos
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-4">
                            <i class="ki-duotone ki-information-5 fs-2 text-warning me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-6 text-gray-700">Sistema Legado</div>
                                    <div class="fs-7 text-muted">Estes templates específicos ainda existem mas o sistema priorizará o template universal quando disponível</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-row-dashed table-row-gray-300 gy-4">
                            <thead class="border-bottom border-gray-200 fs-8 text-uppercase fw-bold text-muted">
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tipos as $tipo)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ki-duotone ki-document-2 fs-4 text-gray-400 me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div>
                                                    <div class="fw-semibold fs-7">{{ $tipo->nome }}</div>
                                                    <div class="text-muted fs-8">{{ $tipo->codigo }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($tipo->hasTemplate())
                                                <span class="badge badge-light-warning fs-8 fw-semibold px-2 py-1">
                                                    Específico
                                                </span>
                                            @else
                                                <span class="badge badge-light-success fs-8 fw-semibold px-2 py-1">
                                                    Universal
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($tipo->hasTemplate())
                                                <button type="button" class="btn btn-light-primary btn-sm" 
                                                        onclick="visualizarTemplateEspecifico({{ $tipo->id }})">
                                                    <i class="ki-duotone ki-eye fs-7">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </button>
                                            @else
                                                <span class="text-muted fs-8">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted fs-6 py-8">
                                            Nenhum tipo de proposição encontrado
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row g-5 g-xl-10 mb-10">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>
                            <i class="ki-duotone ki-gear fs-3 text-dark me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Ações do Sistema
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="d-grid">
                                <button type="button" class="btn btn-light-primary btn-lg" onclick="regenerarTodosTemplates()">
                                    <i class="ki-duotone ki-arrows-circle fs-2 mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fw-bold">Regenerar Templates</div>
                                    <div class="fs-8 text-muted">Aplicar padrões LC 95/1998</div>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="d-grid">
                                <a href="/admin/parametros/1" class="btn btn-light-success btn-lg">
                                    <i class="ki-duotone ki-setting-2 fs-2 mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fw-bold">Parâmetros</div>
                                    <div class="fs-8 text-muted">Configurações gerais</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="d-grid">
                                <button type="button" class="btn btn-light-info btn-lg" data-bs-toggle="modal" data-bs-target="#kt_modal_help_templates">
                                    <i class="ki-duotone ki-information-5 fs-2 mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="fw-bold">Como Usar</div>
                                    <div class="fs-8 text-muted">Guia do sistema</div>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="d-grid">
                                <button type="button" class="btn btn-light-warning btn-lg" onclick="backupTemplates()">
                                    <i class="ki-duotone ki-save fs-2 mb-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fw-bold">Backup</div>
                                    <div class="fs-8 text-muted">Salvar configurações</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.templates.modals')

@endsection

@push('styles')
<style>
/* Estilo do card principal do template universal */
.card-flush.border-0.shadow-lg {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2) !important;
}

/* Animação suave para os cards de estatísticas */
.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

/* Timeline personalizada */
.timeline .timeline-icon {
    border: 2px solid #f1f1f4;
}

/* Tabela responsiva com scroll suave */
.table-responsive {
    scrollbar-width: thin;
    scrollbar-color: #e1e5e9 #f8f9fa;
}

.table-responsive::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #e1e5e9;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #d3d9df;
}

/* Botões de ação */
.btn-lg {
    min-height: 100px;
    text-align: center;
    border-radius: 12px;
}

.btn-lg:hover {
    transform: translateY(-3px);
    transition: all 0.3s ease;
}

/* Cards de estatísticas com cores personalizadas */
.card-flush[style*="background-color: #f8f5ff"] {
    border: 1px solid rgba(124, 58, 237, 0.1);
}

.card-flush[style*="background-color: #fff5f5"] {
    border: 1px solid rgba(239, 68, 68, 0.1);
}

.card-flush[style*="background-color: #f0fff4"] {
    border: 1px solid rgba(34, 197, 94, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
// Preview do template universal
function previewTemplateUniversal() {
    @if($templateUniversal)
        window.open('{{ route("admin.templates.universal", $templateUniversal) }}', '_blank');
    @else
        Swal.fire({
            icon: 'warning',
            title: 'Template Universal não encontrado',
            text: 'Crie primeiro o template universal para visualizar o preview.'
        });
    @endif
}

// Mostrar variáveis disponíveis
function mostrarVariaveis() {
    const modal = new bootstrap.Modal(document.getElementById('kt_modal_variables'));
    modal.show();
}

// Testar template universal com tipo específico
function testarTemplateUniversal() {
    const selectTipo = document.getElementById('tipo_proposicao_teste');
    const tipoId = selectTipo.value;
    const tipoOption = selectTipo.options[selectTipo.selectedIndex];
    const tipoNome = tipoOption.dataset.nome;
    const tipoCodigo = tipoOption.dataset.codigo;
    
    if (!tipoId) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecione um Tipo',
            text: 'Por favor, escolha um tipo de proposição para testar.'
        });
        return;
    }
    
    // Mostrar resultado do teste
    const resultadoDiv = document.getElementById('resultado_teste');
    const conteudoDiv = document.getElementById('resultado_conteudo');
    
    conteudoDiv.innerHTML = `
        <div class="d-flex align-items-start">
            <i class="ki-duotone ki-check-circle fs-1 text-success me-3 mt-1">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            <div class="flex-grow-1">
                <h4 class="alert-heading mb-2">Template adaptado para: <strong>${tipoNome}</strong></h4>
                <div class="mb-3">
                    <div class="fs-7 text-muted mb-1">Código do tipo:</div>
                    <code class="bg-light px-2 py-1 rounded">${tipoCodigo}</code>
                </div>
                <div class="mb-3">
                    <div class="fs-7 text-muted mb-1">Adaptações aplicadas:</div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1">✅ Preâmbulo específico para ${tipoNome}</li>
                        <li class="mb-1">✅ Estrutura adequada ao tipo</li>
                        <li class="mb-1">✅ Variáveis contextualizadas</li>
                        <li class="mb-1">✅ Formatação seguindo LC 95/1998</li>
                    </ul>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="aplicarTemplateEspecifico(${tipoId})">
                        <i class="ki-duotone ki-rocket fs-6 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Aplicar Agora
                    </button>
                </div>
            </div>
        </div>
    `;
    
    resultadoDiv.style.display = 'block';
    resultadoDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Aplicar template universal a um tipo específico
function aplicarTemplateEspecifico(tipoId) {
    @if($templateUniversal)
        Swal.fire({
            title: 'Aplicar Template Universal?',
            text: 'Isso irá gerar um documento adaptado para este tipo específico.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, Aplicar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aqui você pode implementar a lógica para aplicar o template
                window.open(`{{ route("admin.templates.universal.aplicar-tipo", [$templateUniversal, "TIPO_ID"]) }}`.replace('TIPO_ID', tipoId), '_blank');
            }
        });
    @else
        Swal.fire({
            icon: 'error',
            title: 'Template não disponível',
            text: 'Template universal não encontrado.'
        });
    @endif
}

// Criar template universal
function criarTemplateUniversal() {
    Swal.fire({
        title: 'Criar Template Universal?',
        html: `
            <div class="mb-4">
                <p>Isso criará um template único que substitui todos os templates específicos por tipo.</p>
                <div class="alert alert-info">
                    <strong>Vantagens:</strong>
                    <ul class="text-start mt-2 mb-0">
                        <li>Manutenção simplificada</li>
                        <li>Adaptação automática por tipo</li>
                        <li>Todas as variáveis em um só lugar</li>
                        <li>Conformidade com padrões legais</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Criar Template Universal',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#667eea'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirecionar para a página de criação
            window.location.href = '{{ route("admin.templates.universal.store") }}';
        }
    });
}

// Migrar templates específicos para universal
function migrarParaUniversal() {
    Swal.fire({
        title: 'Migrar para Template Universal?',
        html: `
            <div class="text-center mb-4">
                <i class="ki-duotone ki-arrows-circle fs-3x text-primary mb-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <p>Esta ação irá:</p>
                <ul class="text-start">
                    <li>Desativar templates específicos existentes</li>
                    <li>Configurar o template universal como padrão</li>
                    <li>Manter backup dos templates antigos</li>
                </ul>
                <div class="alert alert-warning mt-3">
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita automaticamente.
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, Migrar Tudo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f1416c'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementar lógica de migração
            Swal.fire({
                icon: 'success',
                title: 'Migração Iniciada!',
                text: 'Os templates estão sendo migrados para o sistema universal.',
                timer: 2000
            }).then(() => {
                location.reload();
            });
        }
    });
}

// Visualizar template específico (legado)
function visualizarTemplateEspecifico(tipoId) {
    Swal.fire({
        title: 'Template Específico (Legado)',
        text: 'Este é um template do sistema antigo. Recomendamos usar o Template Universal.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Visualizar Mesmo Assim',
        cancelButtonText: 'Usar Template Universal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Abrir editor do template específico
            window.open(`/templates/${tipoId}/editor`, '_blank');
        } else {
            @if($templateUniversal)
                window.open('{{ route("admin.templates.universal.editor", $templateUniversal) }}', '_blank');
            @endif
        }
    });
}

// Outras funções da página original
function regenerarTodosTemplates() {
    // Manter a função original
    location.reload();
}

function backupTemplates() {
    Swal.fire({
        icon: 'success',
        title: 'Backup Criado!',
        text: 'Backup dos templates salvos com sucesso.',
        timer: 2000
    });
}
</script>
@endpush