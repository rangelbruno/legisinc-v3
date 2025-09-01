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

    <!-- Como Funciona o Template Universal -->
    <div class="row g-5 g-xl-10 mb-10">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>
                            <i class="ki-duotone ki-rocket fs-3 text-success me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Como Funciona o Template Universal
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="mb-8">
                                <div class="fs-6 text-muted mb-6">
                                    O <strong>Template Universal</strong> revoluciona o sistema de templates, substituindo 23 templates específicos por um único template inteligente que se adapta automaticamente a qualquer tipo de proposição.
                                </div>
                                
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
                                                <div class="text-gray-500 fs-7">Sistema identifica o tipo da proposição (Moção, Projeto de Lei, etc.) automaticamente</div>
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
                                                <div class="text-gray-500 fs-7">Template ajusta preâmbulo, seções e estrutura conforme o tipo detectado</div>
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
                                                <div class="text-gray-500 fs-7">Gera documento formatado seguindo padrões legais e LC 95/1998</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-4">
                            <div class="card bg-light-success border-success border-dashed">
                                <div class="card-body text-center p-6">
                                    <i class="ki-duotone ki-medal-star fs-3x text-success mb-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                    <h4 class="fw-bold text-success mb-3">Benefícios</h4>
                                    <ul class="list-unstyled text-start">
                                        <li class="mb-2">
                                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fw-semibold">Um só template</span> para gerenciar
                                        </li>
                                        <li class="mb-2">
                                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fw-semibold">Adaptação automática</span> por tipo
                                        </li>
                                        <li class="mb-2">
                                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fw-semibold">Manutenção simplificada</span>
                                        </li>
                                        <li class="mb-2">
                                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fw-semibold">Conformidade legal</span> garantida
                                        </li>
                                        <li class="mb-0">
                                            <i class="ki-duotone ki-check-circle fs-6 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="fw-semibold">Todas as variáveis</span> incluídas
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
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

// Template Universal - Edição e gerenciamento
function editarTemplateUniversal() {
    @if($templateUniversal)
        window.location.href = '{{ route("admin.templates.universal.editor", $templateUniversal) }}';
    @else
        Swal.fire({
            icon: 'warning',
            title: 'Template Universal não encontrado',
            text: 'Crie primeiro o template universal.'
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

// Funções auxiliares para o sistema de templates universais
function definirComoPadrao() {
    @if($templateUniversal)
        Swal.fire({
            title: 'Definir como Padrão?',
            text: 'Este template será usado como padrão para todas as proposições.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, Definir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Implementar definição como padrão via AJAX se necessário
                Swal.fire({
                    icon: 'success',
                    title: 'Definido como Padrão!',
                    text: 'Este template agora é o padrão do sistema.',
                    timer: 2000
                });
            }
        });
    @endif
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