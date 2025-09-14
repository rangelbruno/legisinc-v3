@extends('components.layouts.app')

@section('title', 'Criar Nova Proposição')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Criar Nova Proposição</h1>
            <p class="text-muted">Etapa 1: Dados Básicos da Proposição</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-warning" id="btn-limpar-cache" title="Limpar dados salvos do formulário">
                <i class="fas fa-broom me-2"></i>Limpar Cache
            </button>
            <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Minhas Proposições
            </a>
        </div>
    </div>

    <!-- Stepper -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="stepper stepper-pills stepper-column d-flex flex-stack flex-wrap">
                        <div class="stepper-item current" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">1</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Dados Básicos</h3>
                                    <div class="stepper-desc">Tipo, Ementa e Modelo</div>
                                </div>
                            </div>
                        </div>
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">2</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Preenchimento</h3>
                                    <div class="stepper-desc">Campos do Modelo</div>
                                </div>
                            </div>
                        </div>
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">3</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">Edição Final</h3>
                                    <div class="stepper-desc">Revisão e Envio</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Informações da Proposição
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-criar-proposicao">
                        @csrf
                        
                        <!-- Tipo de Proposição -->
                        <div class="mb-4">
                            <label for="tipo" class="form-label required">Tipo de Proposição</label>
                            @if(isset($tipoSelecionado))
                                <!-- Tipo pré-selecionado vindo da tela de seleção -->
                                <input type="hidden" name="tipo" id="tipo" value="{{ $tipoSelecionado }}">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        <strong>Tipo selecionado:</strong> {{ $nomeTipoSelecionado }}
                                        <a href="{{ route('proposicoes.criar') }}" class="btn btn-sm btn-outline-info ms-3">
                                            <i class="fas fa-exchange-alt me-1"></i>Trocar tipo
                                        </a>
                                    </div>
                                </div>
                            @else
                                <!-- Dropdown normal de seleção -->
                                <select name="tipo" id="tipo" class="form-select" data-control="select2" data-placeholder="Selecione o tipo de proposição" required>
                                    <option value="">Selecione o tipo de proposição</option>
                                    @foreach($tipos as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    Escolha o tipo adequado conforme a natureza da proposição.
                                </div>
                            @endif
                        </div>

                        <!-- Ementa -->
                        <div class="mb-4" id="ementa-container" style="display: none;">
                            <label for="ementa" class="form-label required">Ementa da Proposição</label>
                            <textarea name="ementa" id="ementa" class="form-control" rows="3" placeholder="Descreva resumidamente o objetivo da proposição..." required></textarea>
                            <div class="form-text">
                                Descreva de forma clara e objetiva o que a proposição pretende regulamentar ou modificar.
                            </div>
                        </div>

                        <!-- Opções de Preenchimento -->
                        <div class="mb-4" id="opcoes-preenchimento-container" style="display: none;">
                            <label class="form-label required">Como deseja criar o texto da proposição?</label>
                            <div class="row g-3 justify-content-center">
                                <div class="col-md-5">
                                    <div class="card h-100 opcao-card" data-opcao="manual">
                                        <div class="card-body text-center">
                                            <i class="fas fa-edit fa-2x text-success mb-3"></i>
                                            <h6 class="card-title">Texto Personalizado</h6>
                                            <p class="card-text small">Escreva o texto principal e use modelo para formatação</p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="opcao_preenchimento" id="escrever_manual" value="manual">
                                                <label class="form-check-label" for="escrever_manual">
                                                    Texto Personalizado
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="card h-100 opcao-card" data-opcao="ia">
                                        <div class="card-body text-center">
                                            <i class="fas fa-robot fa-2x text-info mb-3"></i>
                                            <h6 class="card-title">Texto com IA</h6>
                                            <p class="card-text small">IA gera o texto e aplica no modelo para formatação</p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="opcao_preenchimento" id="usar_ia_radio" value="ia">
                                                <label class="form-check-label" for="usar_ia_radio">
                                                    Texto com IA
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modelo automático (hidden) -->
                        <input type="hidden" name="modelo" id="modelo" value="">
                        
                        <!-- Informação sobre o template -->
                        <div class="mb-4" id="template-info-container" style="display: none;">
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="fas fa-check-circle me-2"></i>
                                <div>
                                    <strong>Template configurado:</strong> <span id="template-info-nome">Carregando...</span>
                                    <div class="small text-muted mt-1">O documento será formatado automaticamente conforme o padrão oficial</div>
                                </div>
                            </div>
                        </div>

                        <!-- Texto Manual -->
                        <div class="mb-4" id="texto-manual-container" style="display: none;">
                            <label for="texto_principal" class="form-label required">Texto Principal da Proposição</label>
                            <textarea name="texto_principal" id="texto_principal" class="form-control" rows="10" placeholder="Digite aqui o texto principal da sua proposição..."></textarea>
                            <div class="form-text">
                                Escreva o conteúdo completo da proposição. Use linguagem técnica e formal apropriada.
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Dica:</strong> Você pode formatar o texto na etapa seguinte usando o editor avançado.
                                </small>
                            </div>
                        </div>

                        <!-- Upload de Anexos com DropzoneJS -->
                        <div class="mb-4" id="anexos-container">
                            <!-- Card Container -->
                            <div class="card shadow-sm border-0">
                                <!-- Card Header -->
                                <div class="card-header bg-light-info border-0">
                                    <h5 class="card-title mb-0">
                                        <i class="ki-duotone ki-paper-clip fs-2 text-info me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Anexos da Proposição
                                        <span class="badge badge-light-secondary ms-2 fs-8">Opcional</span>
                                    </h5>
                                </div>
                                
                                <!-- Card Body -->
                                <div class="card-body p-6">
                                    <!-- Dropzone Container -->
                                    <div class="dropzone dropzone-queue" id="kt_dropzone_anexos">
                                        <!-- Dropzone Config Panel -->
                                        <div class="dropzone-panel mb-4 p-4 bg-light-primary rounded">
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <a class="dropzone-select btn btn-primary btn-sm me-2">
                                                    <i class="ki-duotone ki-folder-up fs-5 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Selecionar Arquivos
                                                </a>
                                                <div class="ms-auto d-flex flex-column align-items-end">
                                                    <!-- Informações de Limite -->
                                                    <small class="text-muted mb-1">
                                                        <i class="ki-duotone ki-information-2 fs-6 text-info me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Máximo 5 arquivos de 10MB cada
                                                    </small>
                                                    
                                                    <!-- Contador de Arquivos e Tamanho Total -->
                                                    <div id="files-counter" class="d-flex align-items-center gap-3" style="display: none;">
                                                        <small class="text-primary fw-bold">
                                                            <i class="ki-duotone ki-file fs-6 me-1">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <span id="files-count">0</span> arquivo(s)
                                                        </small>
                                                        <small class="text-success fw-bold">
                                                            <i class="ki-duotone ki-security-user fs-6 me-1">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                            Total: <span id="total-size">0 MB</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dropzone Items Container -->
                                        <div class="dropzone-items">
                                            <div class="dropzone-item border border-dashed border-gray-300 rounded p-4 mb-3" style="display:none">
                                                <!-- File Details -->
                                                <div class="dropzone-file d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-40px me-3">
                                                            <div class="symbol-label bg-light-info">
                                                                <i class="ki-duotone ki-file fs-2 text-info">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="dropzone-filename text-gray-900 fw-semibold fs-6" title="arquivo.pdf">
                                                                <span data-dz-name>arquivo.pdf</span>
                                                            </div>
                                                            <div class="text-muted fs-7">
                                                                <span data-dz-size>120kb</span>
                                                            </div>
                                                            <div class="dropzone-error text-danger fs-7 mt-1" data-dz-errormessage></div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                
                                                <!-- Progress Bar Otimizada com Botão -->
                                                <div class="dropzone-progress mt-2">
                                                    <!-- Barra e Botão Alinhados -->
                                                    <div class="d-flex align-items-center gap-3">
                                                        <!-- Container da Barra de Progresso -->
                                                        <div class="flex-grow-1">
                                                            <div class="progress bg-light-primary position-relative overflow-hidden" style="height: 6px; border-radius: 8px;">
                                                                <!-- Fundo com animação shimmer -->
                                                                <div class="progress-shimmer"></div>
                                                                
                                                                <!-- Barra de progresso com loading automático -->
                                                                <div class="progress-bar progress-bar-animated progress-bar-striped loading-animation" 
                                                                     role="progressbar" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100" 
                                                                     aria-valuenow="0" 
                                                                     data-dz-uploadprogress
                                                                     style="background: linear-gradient(45deg, #009ef7 0%, #0066cc 50%, #004488 100%); 
                                                                            transition: width 0.4s ease-in-out, background-color 0.3s ease;
                                                                            border-radius: 8px;
                                                                            box-shadow: 0 1px 6px rgba(0, 158, 247, 0.3);">
                                                                    <!-- Brilho interno -->
                                                                    <span class="progress-glow"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Botão Remover Compacto -->
                                                        <button class="dropzone-remove-all btn btn-light-danger btn-sm px-2 py-1" 
                                                                style="display: none; font-size: 12px;" 
                                                                title="Remover este arquivo">
                                                            <i class="ki-duotone ki-trash fs-6">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                            </i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Indicador de Status Compacto -->
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <small class="text-muted" style="font-size: 11px;">
                                                            <i class="ki-duotone ki-cloud-upload fs-7 me-1 text-primary loading-icon">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            <span class="status-text">Preparando...</span>
                                                        </small>
                                                        <small class="text-primary fw-bold" style="font-size: 11px;">
                                                            <span class="progress-percentage">0%</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Dropzone Hint -->
                                        <div class="dropzone-hint">
                                            <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded bg-light">
                                                <i class="ki-duotone ki-cloud-upload fs-5x text-gray-400 mb-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="text-gray-700 fw-semibold fs-5 mb-3">
                                                    Arraste arquivos aqui ou clique em "Selecionar Arquivos"
                                                </div>
                                                <div class="text-muted fs-7 mb-4">
                                                    Formatos aceitos: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
                                                </div>
                                                <div class="d-flex justify-content-center flex-wrap gap-2">
                                                    <span class="badge badge-light-info">PDF</span>
                                                    <span class="badge badge-light-primary">DOC/DOCX</span>
                                                    <span class="badge badge-light-success">XLS/XLSX</span>
                                                    <span class="badge badge-light-warning">JPG/PNG</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Card Footer -->
                                <div class="card-footer bg-light border-0 py-3">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="ki-duotone ki-shield-tick fs-5 text-success me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <small>
                                            Os anexos serão salvos junto com a proposição e ficam disponíveis durante todo o processo de tramitação.
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden file input for form submission -->
                            <input type="file" 
                                   id="anexos" 
                                   name="anexos[]" 
                                   multiple 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                   style="display: none;">
                        </div>

                        <!-- Geração via IA -->
                        <div class="mb-4" id="ia-container" style="display: none;">
                            <div class="card border-info">
                                <div class="card-header bg-light-info">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-robot text-info me-2"></i>
                                        Geração Automática via IA
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        Use inteligência artificial para gerar automaticamente o texto da proposição baseado na ementa fornecida.
                                    </p>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" class="btn btn-info" id="btn-gerar-ia">
                                            <i class="fas fa-magic me-2"></i>Gerar Texto via IA
                                        </button>
                                    </div>
                                    <div id="ia-status" class="mt-3" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            Gerando texto via IA, aguarde...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" id="btn-salvar-rascunho">
                                <i class="fas fa-save me-2"></i>Salvar Rascunho
                            </button>
                            <button type="submit" class="btn btn-primary" id="btn-continuar" disabled>
                                <i class="fas fa-arrow-right me-2"></i>Continuar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar de Ajuda -->
        <div class="col-lg-4 col-xl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Ajuda
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Tipos de Proposição:</h6>
                        <ul class="list-unstyled small">
                            <li><strong>PL:</strong> Projeto de Lei</li>
                            <li><strong>PLP:</strong> Projeto de Lei Complementar</li>
                            <li><strong>PEC:</strong> Proposta de Emenda Constitucional</li>
                            <li><strong>PDC:</strong> Projeto de Decreto Legislativo</li>
                            <li><strong>PRC:</strong> Projeto de Resolução</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Dicas para a Ementa:</h6>
                        <ul class="list-unstyled small">
                            <li>• Seja claro e objetivo</li>
                            <li>• Use linguagem formal</li>
                            <li>• Evite termos técnicos desnecessários</li>
                            <li>• Indique o objetivo principal</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold">Opções de Texto:</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Personalizado:</strong> Escreva seu próprio texto</li>
                            <li><strong>IA:</strong> Texto gerado automaticamente</li>
                        </ul>
                        <p class="small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Em ambos os casos, o modelo será usado para manter a formatação ABNT.
                        </p>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Dica:</strong> Você pode salvar como rascunho e continuar depois.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let proposicaoId = null;
    let selectedFiles = [];
    
    // Chaves para localStorage
    const STORAGE_KEY = 'proposicao_form_data';
    const AI_TEXT_KEY = 'proposicao_ai_text';
    
    // Função para formatar tamanho de arquivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 MB';
        const MB = bytes / (1024 * 1024);
        return MB.toFixed(2) + ' MB';
    }
    
    // Função para atualizar o contador de arquivos e tamanho total
    function updateFilesCounter() {
        const counter = document.getElementById('files-counter');
        const filesCount = document.getElementById('files-count');
        const totalSizeSpan = document.getElementById('total-size');
        
        if (selectedFiles.length > 0) {
            // Calcular tamanho total
            const totalBytes = selectedFiles.reduce((sum, file) => sum + file.size, 0);
            
            // Atualizar contadores
            filesCount.textContent = selectedFiles.length;
            totalSizeSpan.textContent = formatFileSize(totalBytes);
            
            // Mostrar contador
            counter.style.display = 'flex';
            counter.classList.remove('d-none');
            
            // Verificar se excede limite total (50MB = 5 * 10MB)
            if (totalBytes > 50 * 1024 * 1024) {
                totalSizeSpan.className = 'text-danger fw-bold';
                totalSizeSpan.innerHTML = `⚠️ ${formatFileSize(totalBytes)} <small>(Limite excedido)</small>`;
            } else {
                totalSizeSpan.className = 'text-success fw-bold';
                totalSizeSpan.textContent = formatFileSize(totalBytes);
            }
        } else {
            // Esconder contador quando não há arquivos
            counter.style.display = 'none';
            counter.classList.add('d-none');
        }
    }
    
    // Se já tem tipo selecionado (veio da tela de seleção), processar imediatamente
    @if(isset($tipoSelecionado))
        const tipoPreSelecionado = '{{ $tipoSelecionado }}';
        console.log('Tipo pré-selecionado:', tipoPreSelecionado);
        $('#ementa-container').show();
        $('#opcoes-preenchimento-container').show();
        carregarModeloAutomatico(tipoPreSelecionado);
        
        // Simular que o select foi alterado para ativar os eventos
        setTimeout(function() {
            validarFormulario();
        }, 100);
    @endif
    
    // Função para salvar dados no localStorage
    function salvarDadosFormulario() {
        const dados = {
            tipo: $('#tipo').val(),
            ementa: $('#ementa').val(),
            opcao_preenchimento: $('input[name="opcao_preenchimento"]:checked').val(),
            modelo: $('#modelo').val(),
            texto_manual: $('#texto_principal').val(),
            timestamp: Date.now()
        };
        
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dados));
        
        // Salvar texto da IA separadamente se existir
        if (window.textoGeradoIA) {
            localStorage.setItem(AI_TEXT_KEY, window.textoGeradoIA);
        }
    }
    
    // Função para carregar dados do localStorage
    function carregarDadosFormulario() {
        try {
            const dadosString = localStorage.getItem(STORAGE_KEY);
            if (!dadosString) return false;
            
            const dados = JSON.parse(dadosString);
            console.log('Carregando dados salvos:', dados);
            
            // Verificar se os dados não são muito antigos (1 hora)
            const agora = Date.now();
            const umHora = 60 * 60 * 1000;
            
            if (agora - dados.timestamp > umHora) {
                console.log('Dados expirados, limpando...');
                limparDadosFormulario();
                return false;
            }
            
            // Restaurar campos
            if (dados.tipo) {
                console.log('Restaurando tipo:', dados.tipo);
                $('#tipo').val(dados.tipo).trigger('change');
                
                // Mostrar containers que dependem do tipo
                $('#ementa-container').show();
                $('#opcoes-preenchimento-container').show();
                
                // Carregar modelo automático para o tipo selecionado
                carregarModeloAutomatico(dados.tipo);
            }
            
            if (dados.ementa) {
                console.log('Restaurando ementa:', dados.ementa.substring(0, 50) + '...');
                $('#ementa').val(dados.ementa);
            }
            
            // Restaurar opção de preenchimento
            if (dados.opcao_preenchimento) {
                console.log('Restaurando opção de preenchimento:', dados.opcao_preenchimento);
                $(`input[name="opcao_preenchimento"][value="${dados.opcao_preenchimento}"]`).prop('checked', true).trigger('change');
            }
            
            // Restaurar texto manual se existir
            if (dados.texto_manual) {
                console.log('Restaurando texto manual:', dados.texto_manual.substring(0, 50) + '...');
                $('#texto_principal').val(dados.texto_manual);
            }
            
            // Restaurar texto da IA se existir
            const textoIA = localStorage.getItem(AI_TEXT_KEY);
            if (textoIA) {
                window.textoGeradoIA = textoIA;
                mostrarPreviewTextoIA(textoIA);
            }
            
            // Validar formulário após restaurar dados
            setTimeout(() => {
                validarFormulario();
            }, 600);
            
            return true;
        } catch (e) {
            console.warn('Erro ao carregar dados do formulário:', e);
            limparDadosFormulario();
            return false;
        }
    }
    
    // Função para limpar dados do localStorage
    function limparDadosFormulario() {
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(AI_TEXT_KEY);
        window.textoGeradoIA = null;
        console.log('🧹 Cache do formulário limpo');
    }

    // Função para limpar cache quando usuário sai da página ou inicia nova proposição
    function limparCacheSeNecessario() {
        const urlAtual = window.location.href;

        // Se está na página de criação com tipo específico, significa que é nova proposição
        if (urlAtual.includes('/proposicoes/create?tipo=')) {
            console.log('🔄 Nova proposição detectada, limpando cache...');
            limparDadosFormulario();
        }

        // Se voltou para a lista de proposições, limpar também
        if (urlAtual.includes('/proposicoes') && !urlAtual.includes('create')) {
            console.log('📋 Voltou à lista, limpando cache...');
            limparDadosFormulario();
        }
    }

    // Verificar se deve limpar cache na inicialização
    limparCacheSeNecessario();

    // Carregar dados salvos na inicialização (apenas se não foi limpo acima)
    carregarDadosFormulario();

    // Limpar cache ao sair da página (navegação ou fechamento)
    window.addEventListener('beforeunload', function() {
        // Se está saindo da página de criação, considerar limpar
        const formPreenchido = $('#ementa').val() || $('#tipo').val();
        if (!formPreenchido) {
            console.log('🚪 Saindo sem dados preenchidos, limpando cache...');
            limparDadosFormulario();
        }
    });

    // Configuração do DropzoneJS para Upload de Arquivos
    let myDropzone = null;
    
    function initializeDropzone() {
        // Configurar o container do dropzone
        const id = "#kt_dropzone_anexos";
        const dropzoneElement = document.querySelector(id);
        
        if (!dropzoneElement) {
            console.warn('Dropzone element not found');
            return;
        }

        // Configurar o template de preview
        var previewNode = dropzoneElement.querySelector(".dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        myDropzone = new Dropzone(id, {
            url: "#", // URL temporária, vamos processar manualmente
            parallelUploads: 5,
            maxFilesize: 10, // MB
            maxFiles: 5,
            autoProcessQueue: false,
            uploadMultiple: true,
            previewTemplate: previewTemplate,
            previewsContainer: id + " .dropzone-items",
            clickable: id + " .dropzone-select",
            acceptedFiles: ".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png",
            
            init: function() {
                const dropzone = this;
                
                // Quando arquivo é adicionado (evento básico)
                this.on("addedfile", function(file) {
                    // Configurar botão de start individual se existir
                    const startBtn = file.previewElement.querySelector(".dropzone-start");
                    if (startBtn) {
                        startBtn.onclick = function() {
                            // Adicionar arquivo à lista de selecionados
                            if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                                selectedFiles.push(file);
                            }
                        };
                    }
                    
                    // Mostrar item de preview
                    const dropzoneItems = dropzoneElement.querySelectorAll('.dropzone-item');
                    dropzoneItems.forEach(item => {
                        item.style.display = '';
                    });
                    
                    // Esconder hint quando há arquivos
                    const hint = dropzoneElement.querySelector('.dropzone-hint');
                    if (hint) {
                        hint.style.display = 'none';
                    }
                    
                    // Adicionar à lista global e atualizar contador
                    if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                        selectedFiles.push(file);
                        updateFilesCounter();
                    }
                });
                
                // Quando arquivo é removido
                this.on("removedfile", function(file) {
                    // Remover da lista de selecionados
                    selectedFiles = selectedFiles.filter(f => f.name !== file.name || f.size !== file.size);
                    
                    // Atualizar contador após remoção
                    updateFilesCounter();
                    
                    // Se não há mais arquivos, mostrar hint novamente
                    if (this.files.length === 0) {
                        const hint = dropzoneElement.querySelector('.dropzone-hint');
                        if (hint) {
                            hint.style.display = 'block';
                        }
                    }
                });
                
                // Erro de arquivo
                this.on("error", function(file, errorMessage) {
                    if (typeof errorMessage === 'string') {
                        toastr.error(errorMessage);
                    }
                });
                
                // Validação adicional de arquivo
                this.on("addedfile", function(file) {
                    // Verificar extensão
                    const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
                    const extension = file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedExtensions.includes(extension)) {
                        this.removeFile(file);
                        toastr.warning(`Arquivo "${file.name}" não é permitido.`);
                        return;
                    }
                    
                    // Verificar tamanho individual
                    if (file.size > 10 * 1024 * 1024) {
                        this.removeFile(file);
                        toastr.warning(`Arquivo "${file.name}" excede 10MB.`);
                        return;
                    }
                    
                    // Verificar limite total após adicionar este arquivo
                    const currentTotalSize = selectedFiles.reduce((sum, f) => sum + f.size, 0);
                    if (currentTotalSize + file.size > 50 * 1024 * 1024) {
                        this.removeFile(file);
                        toastr.warning(`Limite total de 50MB seria excedido. Total atual: ${formatFileSize(currentTotalSize)}`);
                        return;
                    }
                });
                
                // Iniciar animação de loading automática quando arquivo é adicionado
                this.on("addedfile", function(file) {
                    // Buscar elementos no preview específico do arquivo
                    const progressBar = file.previewElement ? file.previewElement.querySelector('[data-dz-uploadprogress]') : null;
                    const statusText = file.previewElement ? file.previewElement.querySelector('.status-text') : null;
                    const progressPercentage = file.previewElement ? file.previewElement.querySelector('.progress-percentage') : null;
                    const removeBtn = file.previewElement ? file.previewElement.querySelector('.dropzone-remove-all') : null;
                    
                    // Configurar botão remover se existir
                    if (removeBtn) {
                        removeBtn.style.display = 'flex';
                        removeBtn.onclick = () => {
                            if (myDropzone) {
                                myDropzone.removeFile(file);
                            }
                        };
                    }
                    
                    // Iniciar loading automático
                    if (progressBar && statusText) {
                        progressBar.classList.add('loading-animation');
                        statusText.textContent = 'Carregando...';
                        
                        // Simular progresso automático
                        let progress = 0;
                        const interval = setInterval(() => {
                            progress += Math.random() * 15 + 5; // 5-20% por vez
                            if (progress >= 95) {
                                progress = 95;
                                clearInterval(interval);
                                statusText.textContent = 'Quase pronto...';
                            }
                            
                            if (progressPercentage) {
                                progressPercentage.classList.add('updating');
                                progressPercentage.textContent = Math.round(progress) + '%';
                                setTimeout(() => progressPercentage.classList.remove('updating'), 200);
                            }
                        }, 300);
                        
                        // Finalizar após 3 segundos
                        setTimeout(() => {
                            progress = 100;
                            progressBar.classList.remove('loading-animation');
                            progressBar.classList.add('success');
                            statusText.innerHTML = `
                                <i class="ki-duotone ki-check-circle fs-7 me-1 text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Arquivo pronto!
                            `;
                            if (progressPercentage) {
                                progressPercentage.textContent = '100%';
                            }
                            clearInterval(interval);
                        }, 3200);
                    }
                });
                
                // Evento de progresso real (para uploads verdadeiros)
                this.on("uploadprogress", function(file, progress, bytesSent) {
                    const progressBar = file.previewElement ? file.previewElement.querySelector('[data-dz-uploadprogress]') : null;
                    const progressPercentage = file.previewElement ? file.previewElement.querySelector('.progress-percentage') : null;
                    
                    if (progressBar && progressPercentage) {
                        // Remover animação automática se houver upload real
                        progressBar.classList.remove('loading-animation');
                        
                        // Atualizar com progresso real
                        progressBar.style.width = progress + '%';
                        progressBar.setAttribute('aria-valuenow', Math.round(progress));
                        
                        progressPercentage.classList.add('updating');
                        progressPercentage.textContent = Math.round(progress) + '%';
                        
                        setTimeout(() => progressPercentage.classList.remove('updating'), 300);
                        
                        if (progress >= 95) {
                            progressBar.classList.add('completing');
                        }
                    }
                });
                
                // Evento de sucesso com animação
                this.on("success", function(file, response) {
                    const progressBar = file.previewElement ? file.previewElement.querySelector('[data-dz-uploadprogress]') : null;
                    const progressText = file.previewElement ? file.previewElement.querySelector('.text-muted') : null;
                    
                    if (progressBar) {
                        progressBar.classList.add('success');
                        progressBar.classList.remove('completing');
                    }
                    
                    if (progressText) {
                        progressText.innerHTML = `
                            <i class="ki-duotone ki-check-circle fs-6 me-1 text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Arquivo enviado com sucesso!
                        `;
                    }
                    
                    // Feedback de sucesso
                    setTimeout(() => {
                        toastr.success(`Arquivo "${file.name}" enviado com sucesso!`);
                    }, 500);
                });
                
                // Evento de erro com animação
                this.on("error", function(file, errorMessage) {
                    const progressBar = file.previewElement ? file.previewElement.querySelector('[data-dz-uploadprogress]') : null;
                    const progressText = file.previewElement ? file.previewElement.querySelector('.text-muted') : null;
                    
                    if (progressBar) {
                        progressBar.classList.add('error');
                        progressBar.classList.remove('completing');
                        progressBar.style.width = '100%';
                    }
                    
                    if (progressText) {
                        progressText.innerHTML = `
                            <i class="ki-duotone ki-cross-circle fs-6 me-1 text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Erro no envio do arquivo
                        `;
                    }
                    
                    // Feedback de erro
                    if (typeof errorMessage === 'string') {
                        toastr.error(`Erro: ${errorMessage}`);
                    }
                });
            },
            
            // Mensagens personalizadas
            dictDefaultMessage: "Arraste arquivos aqui ou clique para selecionar",
            dictFallbackMessage: "Seu navegador não suporta drag and drop.",
            dictFileTooBig: "Arquivo muito grande. Máximo: 10MB.",
            dictInvalidFileType: "Tipo de arquivo não permitido.",
            dictResponseError: "Erro ao processar arquivo.",
            dictCancelUpload: "Cancelar",
            dictCancelUploadConfirmation: "Tem certeza que deseja cancelar?",
            dictRemoveFile: "Remover arquivo",
            dictMaxFilesExceeded: "Você não pode adicionar mais arquivos. Máximo: 5 arquivos."
        });

    }
    
    // Inicializar Dropzone após DOM estar pronto
    setTimeout(function() {
        initializeDropzone();
    }, 100);

    // Inicializar Select2 apenas se não é tipo pré-selecionado
    @if(!isset($tipoSelecionado))
        $('#tipo').select2({
            width: '100%',
            placeholder: 'Selecione o tipo de proposição',
            allowClear: false,
            minimumResultsForSearch: 3
        });
    @endif

    // Não inicializar Select2 no modelo pois agora é hidden

    // Carregar modelo automático quando tipo for selecionado
    $('#tipo').on('change', function() {
        const tipo = $(this).val();
        
        if (tipo) {
            $('#ementa-container').show();
            $('#opcoes-preenchimento-container').show();
            carregarModeloAutomatico(tipo); // Carrega modelo automaticamente
        } else {
            $('#ementa-container').hide();
            $('#opcoes-preenchimento-container').hide();
            $('#template-info-container').hide();
            $('#texto-manual-container').hide();
            $('#ia-container').hide();
            $('#ementa').val('');
            $('#modelo').val('');
            $('#texto_principal').val('');
            $('input[name="opcao_preenchimento"]').prop('checked', false);
            $('.opcao-card').removeClass('selected');
            $('#btn-continuar').prop('disabled', true);
        }
        
        // Validar formulário quando tipo for alterado
        validarFormulario();
    });

    // Gerenciar opções de preenchimento
    $('input[name="opcao_preenchimento"]').on('change', function() {
        const opcao = $(this).val();
        console.log('Opção de preenchimento selecionada:', opcao);
        
        // Remove seleção visual de todos os cards
        $('.opcao-card').removeClass('selected');
        
        // Adiciona seleção visual ao card escolhido
        $(this).closest('.opcao-card').addClass('selected');
        
        // Esconde os containers opcionais
        $('#texto-manual-container').hide();
        $('#ia-container').hide();
        
        // Mostra container adicional conforme opção
        switch(opcao) {
            case 'manual':
                console.log('Mostrando container de texto manual');
                $('#texto-manual-container').show();
                break;
            case 'ia':
                console.log('Mostrando container de IA');
                $('#ia-container').show();
                break;
        }
        
        validarFormulario();
    });

    // Também permitir clique no card inteiro para selecionar
    $('.opcao-card').on('click', function() {
        const radio = $(this).find('input[name="opcao_preenchimento"]');
        if (radio.length && !radio.is(':checked')) {
            radio.prop('checked', true).trigger('change');
        }
    });

    // Validação para o texto manual
    $('#texto_principal').on('input keyup', function() {
        validarFormulario();
    });

    // Validar se pode continuar (apenas ementa, modelo é automático)
    $('#ementa').on('change keyup', function() {
        validarFormulario();
    });

    // Funcionalidade de geração via IA
    $('#btn-gerar-ia').on('click', function() {
        gerarTextoViaIA();
    });

    // Botão para limpar cache manualmente
    $('#btn-limpar-cache').on('click', function() {
        if (confirm('Tem certeza que deseja limpar todos os dados salvos do formulário?\n\nIsso irá apagar:\n• Tipo de proposição selecionado\n• Ementa digitada\n• Opções de preenchimento\n• Texto gerado por IA\n\nEsta ação não pode ser desfeita.')) {
            limparDadosFormulario();

            // Resetar formulário visualmente
            $('#tipo').val('').trigger('change');
            $('#ementa').val('');
            $('input[name="opcao_preenchimento"]').prop('checked', false);
            $('#texto_principal').val('');
            $('#ementa-container').hide();
            $('#opcoes-preenchimento-container').hide();
            $('#template-info-container').hide();
            $('#texto-manual-container').hide();
            $('#ia-container').hide();

            toastr.success('Cache limpo com sucesso! Formulário resetado.');
        }
    });

    // Auto-salvar quando opção de preenchimento mudar
    $('input[name="opcao_preenchimento"]').on('change', function() {
        salvarDadosFormulario();
    });

    // Auto-salvar quando campos importantes mudarem
    $('#tipo').on('change', function() {
        salvarDadosFormulario();
    });
    
    $('#ementa').on('input keyup blur', function() {
        // Debounce para evitar muitas chamadas
        clearTimeout(window.ementaTimeout);
        window.ementaTimeout = setTimeout(() => {
            salvarDadosFormulario();
        }, 500);
    });
    
    // Modelo é agora automático, não precisa de evento change

    // Auto-salvar para texto manual
    $('#texto_principal').on('input keyup blur', function() {
        // Debounce para evitar muitas chamadas
        clearTimeout(window.textoManualTimeout);
        window.textoManualTimeout = setTimeout(() => {
            salvarDadosFormulario();
        }, 500);
    });

    // Salvar rascunho
    $('#btn-salvar-rascunho').on('click', function() {
        salvarRascunho();
    });

    // Continuar para próxima etapa
    $('#form-criar-proposicao').on('submit', function(e) {
        e.preventDefault();
        
        const opcaoPreenchimento = $('input[name="opcao_preenchimento"]:checked').val();
        const modeloId = $('#modelo').val();
        const textoManual = $('#texto_principal').val();
        
        console.log('DEBUG: Form submit initiated', {
            opcaoPreenchimento: opcaoPreenchimento,
            modeloId: modeloId,
            textoManual: textoManual ? textoManual.substring(0, 50) + '...' : null,
            temTextoIA: !!window.textoGeradoIA,
            textoIA: window.textoGeradoIA ? window.textoGeradoIA.substring(0, 50) + '...' : 'null',
            proposicaoId: proposicaoId
        });
        
        // Validações específicas por opção - modelo é carregado automaticamente
        if (!modeloId) {
            alert('Aguarde o template ser carregado automaticamente ou recarregue a página.');
            return;
        }
        
        switch(opcaoPreenchimento) {
            case 'manual':
                if (!textoManual || textoManual.trim().length < 10) {
                    alert('Digite o texto principal da proposição (mínimo 10 caracteres).');
                    return;
                }
                break;
            case 'ia':
                if (!window.textoGeradoIA) {
                    alert('Gere o texto via IA antes de continuar.');
                    return;
                }
                break;
            default:
                alert('Selecione como deseja criar o texto da proposição.');
                return;
        }
        
        if (proposicaoId) {
            // Já tem proposição salva, continuar direto
            console.log('Debug: Proposição já existe', {
                proposicaoId: proposicaoId,
                opcaoPreenchimento: opcaoPreenchimento,
                temTextoIA: !!window.textoGeradoIA,
                modeloId: modeloId,
                textoManual: textoManual
            });
            
            // Limpar dados salvos pois vamos continuar
            limparDadosFormulario();
            
            switch(opcaoPreenchimento) {
                case 'manual':
                    console.log('Debug: Processando texto manual e redirecionando para visualização');
                    window.location.href = `/proposicoes/${proposicaoId}/processar-texto-e-redirecionar/${modeloId}?tipo=manual`;
                    break;
                case 'ia':
                    console.log('Debug: Processando texto IA e redirecionando para visualização');
                    window.location.href = `/proposicoes/${proposicaoId}/processar-texto-e-redirecionar/${modeloId}?tipo=ia`;
                    break;
            }
        } else {
            // Salvar rascunho primeiro, depois continuar
            $('#btn-continuar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
            
            const formData = new FormData();
            formData.append('tipo', $('#tipo').val());
            formData.append('ementa', $('#ementa').val() || 'Proposição em elaboração');
            formData.append('opcao_preenchimento', opcaoPreenchimento);
            formData.append('usar_ia', opcaoPreenchimento === 'ia' ? 1 : 0);
            formData.append('texto_ia', opcaoPreenchimento === 'ia' ? window.textoGeradoIA : null);
            formData.append('texto_manual', opcaoPreenchimento === 'manual' ? textoManual : null);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            // Adicionar arquivos anexos do Dropzone
            if (myDropzone && myDropzone.files.length > 0) {
                myDropzone.files.forEach((file, index) => {
                    formData.append(`anexos[${index}]`, file);
                });
            }

            $.ajax({
                url: '/proposicoes/salvar-rascunho',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        proposicaoId = response.proposicao_id;
                        console.log('Debug: Proposição salva com sucesso', {
                            proposicaoId: proposicaoId,
                            opcaoPreenchimento: opcaoPreenchimento,
                            temTextoIA: !!window.textoGeradoIA,
                            modeloId: modeloId,
                            textoManual: textoManual
                        });
                        
                        // Limpar dados salvos pois vamos continuar
                        limparDadosFormulario();
                        
                        switch(opcaoPreenchimento) {
                            case 'manual':
                                console.log('Debug: Processando texto manual após salvar e redirecionando para visualização');
                                window.location.href = `/proposicoes/${proposicaoId}/processar-texto-e-redirecionar/${modeloId}?tipo=manual`;
                                break;
                            case 'ia':
                                console.log('Debug: Processando texto IA após salvar e redirecionando para visualização');
                                window.location.href = `/proposicoes/${proposicaoId}/processar-texto-e-redirecionar/${modeloId}?tipo=ia`;
                                break;
                        }
                    }
                },
                error: function(xhr) {
                    alert('Erro ao salvar rascunho. Tente novamente.');
                    console.error(xhr.responseText);
                },
                complete: function() {
                    $('#btn-continuar').prop('disabled', false).html('<i class="fas fa-arrow-right me-2"></i>Continuar');
                }
            });
        }
    });

    function carregarModeloAutomatico(tipo) {
        console.log('Carregando modelo automático para tipo:', tipo);
        $('#template-info-nome').text('Carregando...');
        $('#template-info-container').show();
        
        $.get(`/proposicoes/modelos/${tipo}`)
            .done(function(modelos) {
                console.log('Modelos disponíveis:', modelos);
                
                if (Array.isArray(modelos) && modelos.length > 0) {
                    // Usar o primeiro modelo (padrão do tipo)
                    const modeloPadrao = modelos[0];
                    $('#modelo').val(modeloPadrao.id);
                    
                    // Sempre usar template universal
                    $('#template-info-nome').html(`
                        <span class="badge bg-primary me-2">Universal</span>
                        ${modeloPadrao.nome}
                    `);
                    $('.alert .small').text('Usando template universal configurado pelo sistema para formatação padrão');
                    
                    console.log('Modelo automático selecionado:', {
                        id: modeloPadrao.id,
                        nome: modeloPadrao.nome,
                        is_universal: modeloPadrao.is_universal
                    });
                    
                    // Atualizar status
                    $('#template-info-container .alert')
                        .removeClass('alert-warning alert-danger')
                        .addClass('alert-success');
                    $('#template-info-container i')
                        .removeClass('fa-exclamation-triangle fa-times-circle')
                        .addClass('fa-check-circle');
                        
                } else {
                    // Nenhum modelo disponível
                    $('#template-info-nome').text('Nenhum template configurado para este tipo');
                    $('#template-info-container .alert')
                        .removeClass('alert-success alert-danger')
                        .addClass('alert-warning');
                    $('#template-info-container i')
                        .removeClass('fa-check-circle fa-times-circle')
                        .addClass('fa-exclamation-triangle');
                    
                    console.warn('Nenhum modelo disponível para tipo:', tipo);
                }
                
                // Validar formulário após carregar modelo
                setTimeout(function() {
                    validarFormulario();
                }, 100);
            })
            .fail(function(xhr, status, error) {
                console.error('Erro ao carregar modelo automático:', xhr, status, error);
                
                // Tentar extrair mensagem de erro do response JSON
                let errorMessage = 'Erro ao carregar template automático';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.log('Não foi possível parsear erro JSON:', e);
                }
                
                $('#template-info-nome').text(errorMessage);
                $('#template-info-container .alert')
                    .removeClass('alert-success alert-warning')
                    .addClass('alert-danger');
                $('#template-info-container i')
                    .removeClass('fa-check-circle fa-exclamation-triangle')
                    .addClass('fa-times-circle');
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                }
                
                // Desabilitar continuação quando há erro crítico
                $('#btn-continuar').prop('disabled', true);
            });
    }

    function salvarRascunho() {
        const formData = new FormData();
        formData.append('tipo', $('#tipo').val());
        formData.append('ementa', $('#ementa').val() || 'Proposição em elaboração');
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Adicionar arquivos anexos do Dropzone
        if (myDropzone && myDropzone.files.length > 0) {
            myDropzone.files.forEach((file, index) => {
                formData.append(`anexos[${index}]`, file);
            });
        }

        if (!$('#tipo').val()) {
            toastr.warning('Selecione o tipo de proposição');
            return;
        }

        $('#btn-salvar-rascunho').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');

        $.ajax({
            url: '/proposicoes/salvar-rascunho',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    proposicaoId = response.proposicao_id;
                    toastr.success('Rascunho salvo com sucesso!');
                    if (response.anexos_salvos > 0) {
                        toastr.info(`${response.anexos_salvos} anexo(s) salvos com sucesso!`);
                    }
                    validarFormulario();
                }
            },
            error: function(xhr) {
                toastr.error('Erro ao salvar rascunho');
                console.error(xhr.responseText);
            },
            complete: function() {
                $('#btn-salvar-rascunho').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Salvar Rascunho');
            }
        });
    }

    function validarFormulario() {
        const tipo = $('#tipo').val();
        const ementa = $('#ementa').val();
        const opcaoPreenchimento = $('input[name="opcao_preenchimento"]:checked').val();
        const modelo = $('#modelo').val();
        const textoManual = $('#texto_principal').val();

        console.log('Validando formulário:', {
            tipo: tipo,
            ementa: ementa,
            ementaLength: ementa ? ementa.length : 0,
            opcaoPreenchimento: opcaoPreenchimento,
            modelo: modelo,
            textoManual: textoManual ? textoManual.substring(0, 50) + '...' : 'vazio',
            textoManualLength: textoManual ? textoManual.length : 0,
            textoIA: window.textoGeradoIA ? 'possui texto IA' : 'sem IA'
        });

        let valido = false;

        // Debug de cada condição (modelo não é mais obrigatório para validação)
        console.log('Verificando condições básicas:', {
            temTipo: !!tipo,
            temEmenta: !!ementa,
            temOpcaoPreenchimento: !!opcaoPreenchimento,
            temModelo: !!modelo // informativo apenas
        });

        if (tipo && ementa && opcaoPreenchimento) {
            console.log('Condições básicas OK, verificando por opção...');
            switch(opcaoPreenchimento) {
                case 'manual':
                    valido = textoManual && textoManual.trim().length > 10; // Mínimo 10 caracteres
                    console.log('Validação manual:', {
                        temTexto: !!textoManual,
                        tamanho: textoManual ? textoManual.trim().length : 0,
                        valido: valido
                    });
                    break;
                case 'ia':
                    valido = !!window.textoGeradoIA; // Deve ter texto gerado
                    console.log('Validação IA:', {
                        temTextoIA: !!window.textoGeradoIA,
                        valido: valido
                    });
                    break;
                default:
                    console.log('Opção de preenchimento não reconhecida:', opcaoPreenchimento);
            }
        } else {
            console.log('Condições básicas não atendidas - falta:', {
                tipo: !tipo ? 'SIM' : 'OK',
                ementa: !ementa ? 'SIM' : 'OK', 
                opcaoPreenchimento: !opcaoPreenchimento ? 'SIM' : 'OK'
            });
        }

        console.log('Validação resultado final:', valido);
        $('#btn-continuar').prop('disabled', !valido);
    }

    // Função para gerar texto via IA
    function gerarTextoViaIA() {
        const tipo = $('#tipo').val();
        const ementa = $('#ementa').val();

        if (!tipo || !ementa) {
            toastr.warning('Selecione o tipo de proposição e preencha a ementa');
            return;
        }

        // Mostrar status de carregamento
        $('#ia-status').show();
        $('#btn-gerar-ia').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Gerando...');

        // Fazer requisição para gerar texto
        $.post('/proposicoes/gerar-texto-ia', {
            tipo: tipo,
            ementa: ementa,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            $('#ia-status').hide();
            
            if (response.success) {
                toastr.success('Texto gerado via IA com sucesso!');
                $('#usar_ia_radio').prop('checked', true).trigger('change');
                
                // Salvar o texto gerado para usar depois
                window.textoGeradoIA = response.texto;
                
                // Salvar dados do formulário incluindo texto da IA
                salvarDadosFormulario();
                
                // Validar formulário novamente
                validarFormulario();
                
                // Mostrar preview se possível
                if (response.texto) {
                    mostrarPreviewTextoIA(response.texto);
                }
            } else {
                toastr.error('Erro ao gerar texto: ' + (response.message || 'Erro desconhecido'));
            }
        })
        .fail(function(xhr) {
            $('#ia-status').hide();
            console.error('Erro na requisição:', xhr);
            toastr.error('Erro ao conectar com o serviço de IA');
        })
        .always(function() {
            $('#btn-gerar-ia').prop('disabled', false).html('<i class="fas fa-magic me-2"></i>Gerar Texto via IA');
        });
    }

    // Função para mostrar preview do texto gerado
    function mostrarPreviewTextoIA(texto) {
        const maxLength = 200;
        const preview = texto.length > maxLength ? texto.substring(0, maxLength) + '...' : texto;
        
        $('#ia-status').html(`
            <div class="alert alert-success">
                <h6><i class="fas fa-check me-2"></i>Texto gerado com sucesso!</h6>
                <p class="mb-2"><strong>Preview:</strong></p>
                <div class="bg-light p-2 rounded small">${preview}</div>
                <small class="text-muted mt-2 d-block">
                    Texto completo será usado na próxima etapa (${texto.length} caracteres)
                </small>
            </div>
        `).show();
    }
});
</script>
@endpush

@push('styles')
<style>
.stepper .stepper-item.current .stepper-wrapper .stepper-icon {
    background-color: var(--bs-primary);
    color: white;
}

/* === ANIMAÇÕES DA BARRA DE PROGRESSO === */

/* Animação shimmer de fundo */
.progress-shimmer {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
    animation: shimmer 2s infinite ease-in-out;
    border-radius: 10px;
}

@keyframes shimmer {
    0% { left: -100%; }
    50% { left: 100%; }
    100% { left: 100%; }
}

/* Brilho interno da barra de progresso */
.progress-glow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 50%;
    background: linear-gradient(180deg, rgba(255,255,255,0.3) 0%, transparent 100%);
    border-radius: 10px 10px 0 0;
}

/* Animação pulsante quando completando */
.progress-bar.completing {
    animation: progressPulse 1s ease-in-out;
}

@keyframes progressPulse {
    0%, 100% { 
        transform: scaleY(1);
        box-shadow: 0 2px 8px rgba(0, 158, 247, 0.3);
    }
    50% { 
        transform: scaleY(1.1);
        box-shadow: 0 4px 16px rgba(0, 158, 247, 0.5);
    }
}

/* Efeito de sucesso */
.progress-bar.success {
    background: linear-gradient(45deg, #50cd89 0%, #3d9970 50%, #2d7a5a 100%) !important;
    box-shadow: 0 2px 8px rgba(80, 205, 137, 0.4) !important;
    animation: successGlow 0.6s ease-in-out;
}

@keyframes successGlow {
    0% { box-shadow: 0 2px 8px rgba(0, 158, 247, 0.3); }
    50% { box-shadow: 0 4px 20px rgba(80, 205, 137, 0.6); }
    100% { box-shadow: 0 2px 8px rgba(80, 205, 137, 0.4); }
}

/* Efeito de erro */
.progress-bar.error {
    background: linear-gradient(45deg, #f1416c 0%, #d63384 50%, #b02a5b 100%) !important;
    box-shadow: 0 2px 8px rgba(241, 65, 108, 0.4) !important;
    animation: errorShake 0.5s ease-in-out;
}

@keyframes errorShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-2px); }
    75% { transform: translateX(2px); }
}

/* Hover effect no container da progress bar */
.dropzone-progress:hover .progress-bar {
    box-shadow: 0 4px 12px rgba(0, 158, 247, 0.4);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Animação do texto de porcentagem */
.progress-percentage {
    display: inline-block;
    transition: all 0.3s ease;
}

.progress-percentage.updating {
    transform: scale(1.1);
    color: var(--bs-primary);
}

/* Animação de loading automática */
.loading-animation {
    animation: autoProgress 3s ease-out forwards;
}

@keyframes autoProgress {
    0% { width: 0%; }
    20% { width: 15%; }
    40% { width: 35%; }
    60% { width: 55%; }
    80% { width: 75%; }
    95% { width: 90%; }
    100% { width: 95%; }
}

/* Animação do ícone de loading */
.loading-icon {
    animation: spin 2s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Botão remover compacto */
.dropzone-remove-all {
    min-width: 32px;
    height: 32px;
    border-radius: 6px;
    transition: all 0.2s ease;
    display: flex !important;
    align-items: center;
    justify-content: center;
}

.dropzone-remove-all:hover {
    background-color: #f1416c !important;
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(241, 65, 108, 0.3);
}

/* Shimmer otimizado para barra menor */
.progress-shimmer {
    border-radius: 8px;
    animation: shimmerFast 1.5s infinite ease-in-out;
}

@keyframes shimmerFast {
    0% { left: -100%; }
    40% { left: 100%; }
    100% { left: 100%; }
}

/* Responsividade otimizada */
@media (max-width: 768px) {
    .progress {
        height: 4px !important;
    }
    
    .progress-shimmer,
    .progress-glow {
        border-radius: 4px;
    }
    
    .dropzone-remove-all {
        min-width: 28px;
        height: 28px;
    }
    
    .d-flex.gap-3 {
        gap: 1rem !important;
    }
}

.stepper .stepper-item.completed .stepper-wrapper .stepper-icon {
    background-color: var(--bs-success);
    color: white;
}

.required:after {
    content: " *";
    color: red;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

/* Estilos para os cards de opções */
.opcao-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e1e5e9;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.opcao-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,123,255,0.15);
    border-color: #007bff;
}

.opcao-card.selected {
    border-color: #007bff;
    background: linear-gradient(145deg, rgba(0,123,255,0.05) 0%, rgba(0,123,255,0.02) 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,123,255,0.2);
}

.opcao-card .form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.opcao-card .card-body {
    padding: 1.5rem;
}

.opcao-card .card-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.75rem;
}

.opcao-card.selected .card-title {
    color: #007bff;
}

.opcao-card .card-text {
    color: #6c757d;
    line-height: 1.4;
    margin-bottom: 1rem;
}

.opcao-card .form-check {
    margin-bottom: 0;
}

.opcao-card .form-check-label {
    font-weight: 500;
    color: #495057;
}

.opcao-card.selected .form-check-label {
    color: #007bff;
}

/* Estilos Select2 Moderno - Tema Padrão */
.select2-container {
    width: 100% !important;
    font-family: inherit !important;
}

/* Container principal do select */
.select2-selection--single {
    height: 58px !important;
    border: 2px solid #e1e5e9 !important;
    border-radius: 12px !important;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative !important;
    overflow: hidden !important;
}

.select2-selection--single:hover {
    border-color: #007bff !important;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2) !important;
    transform: translateY(-1px) !important;
}

.select2-container--focus .select2-selection--single,
.select2-container--open .select2-selection--single {
    border-color: #007bff !important;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1), 0 4px 15px rgba(0, 123, 255, 0.2) !important;
    transform: translateY(-1px) !important;
}

/* Texto renderizado */
.select2-selection__rendered {
    padding: 0 20px !important;
    line-height: 54px !important;
    color: #2c3e50 !important;
    font-weight: 500 !important;
    font-size: 1rem !important;
}

.select2-selection__placeholder {
    color: #8b9cb5 !important;
    font-weight: 400 !important;
    font-style: italic !important;
}

/* Seta do dropdown */
.select2-selection__arrow {
    height: 58px !important;
    width: 20px !important;
    top: 0 !important;
    right: 15px !important;
}

.select2-selection__arrow b {
    border-color: #007bff transparent transparent transparent !important;
    border-style: solid !important;
    border-width: 7px 7px 0 7px !important;
    height: 0 !important;
    left: 50% !important;
    margin-left: -7px !important;
    margin-top: -3px !important;
    position: absolute !important;
    top: 50% !important;
    width: 0 !important;
    transition: transform 0.3s ease !important;
}

.select2-container--open .select2-selection__arrow b {
    transform: rotate(180deg) !important;
}

/* Dropdown */
.select2-dropdown {
    border: 2px solid #007bff !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(0, 123, 255, 0.2) !important;
    background: white !important;
    margin-top: 5px !important;
    overflow: hidden !important;
    animation: fadeInDown 0.3s ease !important;
}

/* Campo de busca */
.select2-search--dropdown {
    padding: 16px !important;
    background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-bottom: 1px solid #e1e5e9 !important;
}

.select2-search__field {
    border: 2px solid #e1e5e9 !important;
    border-radius: 8px !important;
    padding: 12px 16px !important;
    font-size: 1rem !important;
    width: 100% !important;
    transition: all 0.3s ease !important;
    background: white !important;
}

.select2-search__field:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1) !important;
    outline: none !important;
}

/* Opções do dropdown */
.select2-results__options {
    max-height: 280px !important;
    padding: 8px 0 !important;
}

.select2-results__option {
    padding: 14px 20px !important;
    font-size: 1rem !important;
    color: #2c3e50 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    border-left: 4px solid transparent !important;
    position: relative !important;
}

.select2-results__option--highlighted {
    background: linear-gradient(90deg, rgba(0, 123, 255, 0.12) 0%, rgba(0, 123, 255, 0.06) 100%) !important;
    color: #007bff !important;
    border-left-color: #007bff !important;
}

.select2-results__option[aria-selected="true"] {
    background: linear-gradient(90deg, rgba(40, 167, 69, 0.12) 0%, rgba(40, 167, 69, 0.06) 100%) !important;
    color: #28a745 !important;
    border-left-color: #28a745 !important;
    font-weight: 600 !important;
}

.select2-results__option[aria-selected="true"]::after {
    content: '✓' !important;
    position: absolute !important;
    right: 20px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #28a745 !important;
    font-weight: bold !important;
    font-size: 1.1em !important;
}

/* Mensagens do dropdown */
.select2-results__message {
    padding: 16px 20px !important;
    color: #8b9cb5 !important;
    text-align: center !important;
    font-style: italic !important;
    font-size: 0.95rem !important;
}

/* Animações */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsividade */
@media (max-width: 768px) {
    .select2-selection--single {
        height: 52px !important;
    }
    
    .select2-selection__rendered {
        line-height: 48px !important;
        padding: 0 16px !important;
    }
    
    .select2-selection__arrow {
        height: 52px !important;
        right: 12px !important;
    }
}

/* DropzoneJS Card Customizado */
#anexos-container .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

#anexos-container .card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.dropzone {
    border: none !important;
    background: transparent !important;
    min-height: auto !important;
}

.dropzone.dz-drag-hover .dropzone-hint {
    border-color: #28a745 !important;
    background: linear-gradient(145deg, rgba(40,167,69,0.05) 0%, rgba(40,167,69,0.02) 100%) !important;
    box-shadow: 0 0 20px rgba(40,167,69,0.2) !important;
    transform: scale(1.02);
}

.dropzone-panel {
    background: rgba(67, 97, 238, 0.1);
    border: 1px solid rgba(67, 97, 238, 0.2);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.dropzone-item {
    background: #ffffff;
    border: 2px dashed #e1e5e9 !important;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.dropzone-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.dropzone-item:hover {
    border-color: #007bff !important;
    background: rgba(0, 123, 255, 0.02);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.1);
    transform: translateY(-2px);
}

.dropzone-item:hover::before {
    opacity: 1;
}

.dropzone-hint {
    min-height: 180px !important;
    transition: all 0.3s ease;
    position: relative;
}

.dropzone-hint .border-dashed {
    transition: all 0.3s ease;
}

.dropzone-hint:hover .border-dashed {
    border-color: #007bff !important;
    background: rgba(0, 123, 255, 0.02) !important;
}









.symbol.symbol-40px {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
}

.symbol-label.bg-light-info {
    background: rgba(105, 147, 255, 0.1) !important;
    border: 1px solid rgba(105, 147, 255, 0.2);
}

.progress.h-6px {
    height: 6px !important;
    border-radius: 3px;
    overflow: hidden;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
}

/* Card header customizado */
.card-header.bg-light-info {
    background: linear-gradient(135deg, rgba(105, 147, 255, 0.1) 0%, rgba(105, 147, 255, 0.05) 100%) !important;
    border-bottom: 1px solid rgba(105, 147, 255, 0.2);
}

/* Card footer customizado */
.card-footer.bg-light {
    background: #f8f9fa !important;
    border-top: 1px solid #e9ecef;
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropzone-item {
    animation: fadeInUp 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dropzone-panel .d-flex {
        flex-direction: column;
        align-items: stretch !important;
    }
    
    .dropzone-panel .ms-auto {
        margin-left: 0 !important;
        margin-top: 1rem;
        text-align: center;
    }
    
    .dropzone-file .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
}
</style>
@endpush