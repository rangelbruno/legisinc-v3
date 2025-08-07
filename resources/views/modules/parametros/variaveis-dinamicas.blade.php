@extends('components.layouts.app')

@section('title', 'Variáveis Dinâmicas')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-setting-2 fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        Variáveis Dinâmicas
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Início</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Parâmetros</li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-dark">Variáveis Dinâmicas</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Card principal -->
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-setting-2 fs-3 position-absolute ms-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h3 class="fw-bold ms-15 text-gray-900">
                                    Configuração de Variáveis Dinâmicas
                                </h3>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#modalAdicionarVariavel">
                                    <i class="ki-duotone ki-plus fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Adicionar Variável
                                </button>
                                <button type="button" class="btn btn-sm btn-light-info" onclick="mostrarVariaveisDisponiveis()">
                                    <i class="ki-duotone ki-eye fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Ver Variáveis do Sistema
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body py-4">
                        <!-- Formulário de configuração -->
                        <form id="formVariaveisDinamicas" method="POST">
                            @csrf
                            
                            <!-- Alert de informações -->
                            <div class="alert alert-info d-flex align-items-center p-5 mb-10">
                                <i class="ki-duotone ki-shield-tick fs-2hx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-info">Sobre Variáveis Dinâmicas</h4>
                                    <span>Configure variáveis que podem ser utilizadas em templates, documentos e em todo o sistema. 
                                          As variáveis são substituídas automaticamente pelos seus valores configurados quando utilizadas.</span>
                                </div>
                            </div>

                            <!-- Container das variáveis -->
                            <div id="variaveisContainer">
                                <!-- As variáveis serão adicionadas aqui dinamicamente -->
                            </div>

                            <!-- Botões de ação -->
                            <div class="d-flex justify-content-end mt-10">
                                <div id="alteracoesPendentes" class="alert alert-warning me-3" style="display: none; padding: 8px 15px; margin: 0;">
                                    <i class="ki-duotone ki-information fs-5 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <small>Há alterações não salvas</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-light me-3" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                                    <i class="ki-duotone ki-cross fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-sm btn-primary" id="botaoSalvar">
                                    <i class="ki-duotone ki-check fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card de documentação -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title m-0">
                            <h3 class="fw-bold text-gray-900">Como Usar as Variáveis</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-5">
                            <div class="col-xl-6">
                                <div class="fv-row mb-5">
                                    <label class="fs-6 fw-semibold form-label">Em Templates:</label>
                                    <div class="bg-light-info p-5 rounded">
                                        <code class="text-info">&#123;&#123; $NOME_CAMARA &#125;&#125;</code> - Nome da câmara<br>
                                        <code class="text-info">&#123;&#123; $DATA_ATUAL &#125;&#125;</code> - Data atual<br>
                                        <code class="text-info">&#123;&#123; $ANO_ATUAL &#125;&#125;</code> - Ano atual
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="fv-row mb-5">
                                    <label class="fs-6 fw-semibold form-label">Em Documentos:</label>
                                    <div class="bg-light-success p-5 rounded">
                                        <code class="text-success">{NOME_CAMARA}</code> - Nome da câmara<br>
                                        <code class="text-success">{USUARIO_LOGADO}</code> - Usuário atual<br>
                                        <code class="text-success">{DATA_ATUAL}</code> - Data do sistema
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para variáveis do sistema -->
<div class="modal fade" id="modalVariaveisSistema" tabindex="-1" aria-labelledby="modalVariaveisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Variáveis Disponíveis no Sistema</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <div class="table-responsive">
                    <table class="table table-rounded table-striped border gy-7 gs-7">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th>Variável</th>
                                <th>Valor</th>
                                <th>Tipo</th>
                                <th>Escopo</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaVariaveisSistema">
                            <!-- Preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para adicionar variável -->
<div class="modal fade" id="modalAdicionarVariavel" tabindex="-1" aria-labelledby="modalAdicionarVariavelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Adicionar Nova Variável Dinâmica</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="formNovaVariavel">
                    <div class="row g-5">
                        <div class="col-md-6">
                            <div class="fv-row">
                                <label class="required fs-6 fw-semibold form-label">Nome da Variável</label>
                                <input type="text" id="novaVariavelNome" name="nome" 
                                       class="form-control" 
                                       placeholder="Ex: NOME_MUNICIPIO"
                                       maxlength="100"
                                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '')">
                                <div class="form-text">Use apenas letras, números e underscore. Será convertido para maiúsculas.</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row">
                                <label class="required fs-6 fw-semibold form-label">Tipo</label>
                                <select id="novaVariavelTipo" name="tipo" class="form-select" onchange="alterarTipoNovaVariavel()">
                                    <option value="texto">Texto</option>
                                    <option value="numero">Número</option>
                                    <option value="data">Data</option>
                                    <option value="boolean">Verdadeiro/Falso</option>
                                    <option value="url">URL</option>
                                    <option value="email">Email</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="fv-row">
                                <label class="required fs-6 fw-semibold form-label">Valor</label>
                                <input type="text" id="novaVariavelValor" name="valor" 
                                       class="form-control" 
                                       placeholder="Digite o valor da variável">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="fv-row">
                                <label class="fs-6 fw-semibold form-label">Descrição</label>
                                <input type="text" id="novaVariavelDescricao" name="descricao" 
                                       class="form-control" 
                                       placeholder="Descrição opcional da variável"
                                       maxlength="255">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row">
                                <label class="required fs-6 fw-semibold form-label">Escopo</label>
                                <select id="novaVariavelEscopo" name="escopo" class="form-select">
                                    <option value="global">Global (Todo o sistema)</option>
                                    <option value="documentos">Documentos</option>
                                    <option value="templates">Templates</option>
                                    <option value="sistema">Sistema</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row">
                                <label class="fs-6 fw-semibold form-label">Formato</label>
                                <input type="text" id="novaVariavelFormato" name="formato" 
                                       class="form-control" 
                                       placeholder="Ex: d/m/Y para datas"
                                       maxlength="100">
                                <div class="form-text">Formato específico para o tipo de dado (ex: d/m/Y para datas)</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row">
                                <label class="fs-6 fw-semibold form-label">Validação</label>
                                <input type="text" id="novaVariavelValidacao" name="validacao" 
                                       class="form-control" 
                                       placeholder="Ex: required|string|max:255"
                                       maxlength="200">
                                <div class="form-text">Regras de validação Laravel (opcional)</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarNovaVariavel()">
                    <i class="ki-duotone ki-check fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Adicionar Variável
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Botão flutuante para voltar ao topo -->
<div id="kt_scrolltop" class="scrolltop" style="display: none;">
    <i class="ki-duotone ki-arrow-up">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
</div>

@endsection

@push('styles')
<style>
/* Estilo para o botão de scroll to top */
.scrolltop {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #1e1e2d;
    color: white;
    border: none;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    transition: all 0.3s ease;
}

.scrolltop:hover {
    background-color: #009ef7;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 158, 247, 0.3);
}

.scrolltop i {
    font-size: 1.2rem;
}

/* Animação de highlight para variáveis recém-adicionadas */
.border-primary {
    border: 2px solid #009ef7 !important;
    transition: border-color 0.3s ease;
}

/* Melhorar espaçamento dos cards de variáveis */
.variavel-item {
    transition: all 0.3s ease;
}

.variavel-item:hover {
    box-shadow: 0 0 20px rgba(0, 158, 247, 0.1);
}

/* Estilo para campos com erro no modal */
.modal .is-invalid {
    border-color: #f1416c;
}

.modal .invalid-feedback {
    color: #f1416c;
    font-size: 0.875rem;
}
</style>
@endpush

@push('scripts')
<script>
// Configurações e dados do PHP
let configuracoes = @json($configuracoes);
let tiposDisponiveis = configuracoes.tipos_disponiveis;
let escoposDisponiveis = configuracoes.escopos_disponiveis;
let variaveisPadrao = configuracoes.variaveis_padrao;
let variaveisAtuais = configuracoes.variaveis || [];
let contadorVariaveis = 0;

// Inicializar página
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Iniciando página de Variáveis Dinâmicas');
    
    // Carregar variáveis existentes
    if (variaveisAtuais && variaveisAtuais.length > 0) {
        variaveisAtuais.forEach(variavel => {
            adicionarVariavel(variavel);
        });
    } else if (variaveisPadrao && variaveisPadrao.length > 0) {
        // Se não há variáveis salvas, carregar as padrão do sistema
        variaveisPadrao.forEach(variavel => {
            adicionarVariavel(variavel);
        });
    } else {
        // Adicionar uma variável em branco para começar
        adicionarVariavel();
    }
    
    // Configurar formulário
    configurarFormulario();
    
    // Inicializar botão de scroll to top
    inicializarScrollTop();
    
    console.log('✅ Página inicializada');
});

// Adicionar nova variável
function adicionarVariavel(dadosVariavel = null) {
    contadorVariaveis++;
    
    const dados = dadosVariavel || {
        nome: '',
        valor: '',
        descricao: '',
        tipo: 'texto',
        escopo: 'global',
        formato: '',
        validacao: '',
        sistema: false
    };
    
    const container = document.getElementById('variaveisContainer');
    
    // Construir options do select de tipo
    let tipoOptions = '';
    Object.entries(tiposDisponiveis).forEach(([key, value]) => {
        tipoOptions += `<option value="${key}" ${dados.tipo === key ? 'selected' : ''}>${value}</option>`;
    });
    
    // Construir options do select de escopo
    let escopoOptions = '';
    Object.entries(escoposDisponiveis).forEach(([key, value]) => {
        escopoOptions += `<option value="${key}" ${dados.escopo === key ? 'selected' : ''}>${value}</option>`;
    });
    
    const variavelHtml = `
        <div class="variavel-item card card-bordered mb-5" data-index="${contadorVariaveis}">
            <div class="card-header collapsible cursor-pointer rotate" data-bs-toggle="collapse" data-bs-target="#variavel_${contadorVariaveis}">
                <h3 class="card-title">
                    <span class="card-label fw-bold text-gray-800">
                        Variável ${contadorVariaveis}: <span class="variavel-nome-display">${dados.nome || 'Nova Variável'}</span>
                    </span>
                </h3>
                <div class="card-toolbar">
                    ${dados.sistema ? '<span class="badge badge-light-info">Sistema</span>' : ''}
                    <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" onclick="removerVariavel(${contadorVariaveis})" ${dados.sistema ? 'disabled' : ''}>
                        <i class="ki-duotone ki-trash fs-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </button>
                </div>
            </div>
            <div id="variavel_${contadorVariaveis}" class="collapse show">
                <div class="card-body">
                    <div class="row g-5">
                        <div class="col-md-6">
                            <div class="fv-row">
                                <label class="required fs-6 fw-semibold form-label">Nome da Variável</label>
                                <input type="text" name="variaveis[${contadorVariaveis}][nome]" 
                                       class="form-control variavel-nome" 
                                       value="${dados.nome}" 
                                       placeholder="Ex: NOME_MUNICIPIO" 
                                       ${dados.sistema ? 'readonly' : ''}
                                       onchange="atualizarNomeDisplay(${contadorVariaveis})">
                                <div class="form-text">Use apenas letras, números e underscore. Será convertido para maiúsculas.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row">
                                <label class="required fs-6 fw-semibold form-label">Tipo</label>
                                <select name="variaveis[${contadorVariaveis}][tipo]" 
                                        class="form-select" 
                                        ${dados.sistema ? 'disabled' : ''}
                                        onchange="alterarTipoVariavel(${contadorVariaveis})">
                                    ${tipoOptions}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="fv-row">
                                <label class="required fs-6 fw-semibold form-label">Valor</label>
                                <input type="text" name="variaveis[${contadorVariaveis}][valor]" 
                                       class="form-control variavel-valor" 
                                       value="${dados.valor}" 
                                       placeholder="Digite o valor da variável"
                                       ${dados.sistema ? 'readonly' : ''}>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="fv-row">
                                <label class="fs-6 fw-semibold form-label">Descrição</label>
                                <input type="text" name="variaveis[${contadorVariaveis}][descricao]" 
                                       class="form-control" 
                                       value="${dados.descricao || ''}" 
                                       placeholder="Descrição opcional da variável"
                                       ${dados.sistema ? 'readonly' : ''}>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row">
                                <label class="required fs-6 fw-semibold form-label">Escopo</label>
                                <select name="variaveis[${contadorVariaveis}][escopo]" 
                                        class="form-select" 
                                        ${dados.sistema ? 'disabled' : ''}>
                                    ${escopoOptions}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row">
                                <label class="fs-6 fw-semibold form-label">Formato</label>
                                <input type="text" name="variaveis[${contadorVariaveis}][formato]" 
                                       class="form-control" 
                                       value="${dados.formato || ''}" 
                                       placeholder="Ex: d/m/Y para datas"
                                       ${dados.sistema ? 'readonly' : ''}>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fv-row">
                                <label class="fs-6 fw-semibold form-label">Validação</label>
                                <input type="text" name="variaveis[${contadorVariaveis}][validacao]" 
                                       class="form-control" 
                                       value="${dados.validacao || ''}" 
                                       placeholder="Ex: required|string|max:255"
                                       ${dados.sistema ? 'readonly' : ''}>
                            </div>
                        </div>
                        ${dados.sistema ? `<input type="hidden" name="variaveis[${contadorVariaveis}][sistema]" value="true">` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', variavelHtml);
    console.log(`✅ Variável ${contadorVariaveis} adicionada`);
}

// Remover variável
function removerVariavel(index) {
    const elemento = document.querySelector(`.variavel-item[data-index="${index}"]`);
    if (elemento) {
        elemento.remove();
        marcarAlteracoesPendentes();
        console.log(`🗑️ Variável ${index} removida`);
    }
}

// Atualizar display do nome
function atualizarNomeDisplay(index) {
    const input = document.querySelector(`input[name="variaveis[${index}][nome]"]`);
    const display = document.querySelector(`.variavel-item[data-index="${index}"] .variavel-nome-display`);
    
    if (input && display) {
        const nomeFormatado = input.value.toUpperCase();
        input.value = nomeFormatado;
        display.textContent = nomeFormatado || 'Nova Variável';
        marcarAlteracoesPendentes();
    }
}

// Alterar tipo de variável
function alterarTipoVariavel(index) {
    const select = document.querySelector(`select[name="variaveis[${index}][tipo]"]`);
    const inputValor = document.querySelector(`input[name="variaveis[${index}][valor]"]`);
    
    if (!select || !inputValor) return;
    
    const tipo = select.value;
    
    // Ajustar placeholder e tipo de input baseado no tipo selecionado
    switch(tipo) {
        case 'numero':
            inputValor.type = 'number';
            inputValor.placeholder = 'Digite um número';
            break;
        case 'data':
            inputValor.type = 'date';
            inputValor.placeholder = 'Selecione uma data';
            break;
        case 'email':
            inputValor.type = 'email';
            inputValor.placeholder = 'Digite um email válido';
            break;
        case 'url':
            inputValor.type = 'url';
            inputValor.placeholder = 'Digite uma URL válida';
            break;
        case 'boolean':
            inputValor.type = 'text';
            inputValor.placeholder = 'true/false, sim/não, 1/0';
            break;
        default:
            inputValor.type = 'text';
            inputValor.placeholder = 'Digite o valor da variável';
    }
    
    marcarAlteracoesPendentes();
}

// Mostrar variáveis disponíveis do sistema
function mostrarVariaveisDisponiveis() {
    const modalEl = document.getElementById('modalVariaveisSistema');
    const tbody = document.getElementById('tabelaVariaveisSistema');
    
    // Limpar tabela
    tbody.innerHTML = '';
    
    // Adicionar variáveis do sistema
    variaveisPadrao.forEach(variavel => {
        const row = `
            <tr>
                <td><code class="text-primary">${variavel.nome}</code></td>
                <td><span class="text-gray-600">${variavel.valor}</span></td>
                <td><span class="badge badge-light-${getTipoCor(variavel.tipo)}">${tiposDisponiveis[variavel.tipo]}</span></td>
                <td><span class="badge badge-light">${escoposDisponiveis[variavel.escopo]}</span></td>
                <td><span class="text-gray-700">${variavel.descricao || '-'}</span></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
    
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// Obter cor do badge baseada no tipo
function getTipoCor(tipo) {
    const cores = {
        texto: 'primary',
        numero: 'success',
        data: 'info',
        boolean: 'warning',
        email: 'danger',
        url: 'dark'
    };
    return cores[tipo] || 'primary';
}

// Alterar tipo de variável no modal
function alterarTipoNovaVariavel() {
    const select = document.getElementById('novaVariavelTipo');
    const inputValor = document.getElementById('novaVariavelValor');
    const inputFormato = document.getElementById('novaVariavelFormato');
    
    if (!select || !inputValor) return;
    
    const tipo = select.value;
    
    // Ajustar placeholder e tipo de input baseado no tipo selecionado
    switch(tipo) {
        case 'numero':
            inputValor.type = 'number';
            inputValor.placeholder = 'Digite um número';
            inputFormato.placeholder = 'Ex: 2 (casas decimais)';
            break;
        case 'data':
            inputValor.type = 'date';
            inputValor.placeholder = 'Selecione uma data';
            inputFormato.placeholder = 'd/m/Y';
            inputFormato.value = 'd/m/Y';
            break;
        case 'email':
            inputValor.type = 'email';
            inputValor.placeholder = 'Digite um email válido';
            inputFormato.placeholder = 'Formato do email';
            break;
        case 'url':
            inputValor.type = 'url';
            inputValor.placeholder = 'Digite uma URL válida';
            inputFormato.placeholder = 'Formato da URL';
            break;
        case 'boolean':
            inputValor.type = 'text';
            inputValor.placeholder = 'true/false, sim/não, 1/0';
            inputFormato.placeholder = 'true/false';
            break;
        default:
            inputValor.type = 'text';
            inputValor.placeholder = 'Digite o valor da variável';
            inputFormato.placeholder = 'Ex: maiúscula, minúscula';
    }
}

// Salvar nova variável do modal
async function salvarNovaVariavel() {
    const form = document.getElementById('formNovaVariavel');
    const formData = new FormData(form);
    
    // Limpar erros anteriores
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Validar campos obrigatórios
    const nome = formData.get('nome')?.trim();
    const valor = formData.get('valor')?.trim();
    const tipo = formData.get('tipo');
    const escopo = formData.get('escopo');
    
    let hasErrors = false;
    
    if (!nome) {
        mostrarErroInput('novaVariavelNome', 'Nome da variável é obrigatório');
        hasErrors = true;
    } else if (!/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(nome)) {
        mostrarErroInput('novaVariavelNome', 'Nome deve começar com letra ou underscore e conter apenas letras, números e underscores');
        hasErrors = true;
    }
    
    if (!valor) {
        mostrarErroInput('novaVariavelValor', 'Valor da variável é obrigatório');
        hasErrors = true;
    }
    
    // Validações específicas por tipo
    if (tipo === 'numero' && valor && !isNumeric(valor)) {
        mostrarErroInput('novaVariavelValor', 'Valor deve ser um número válido');
        hasErrors = true;
    }
    
    if (tipo === 'email' && valor && !isValidEmail(valor)) {
        mostrarErroInput('novaVariavelValor', 'Valor deve ser um email válido');
        hasErrors = true;
    }
    
    if (tipo === 'url' && valor && !isValidURL(valor)) {
        mostrarErroInput('novaVariavelValor', 'Valor deve ser uma URL válida');
        hasErrors = true;
    }
    
    if (hasErrors) {
        return;
    }
    
    // Verificar se variável já existe
    if (verificarVariavelExistente(nome.toUpperCase())) {
        mostrarErroInput('novaVariavelNome', 'Uma variável com este nome já existe');
        return;
    }
    
    // Adicionar variável à lista
    const dadosVariavel = {
        nome: nome.toUpperCase(),
        valor: valor,
        descricao: formData.get('descricao') || '',
        tipo: tipo,
        escopo: escopo,
        formato: formData.get('formato') || '',
        validacao: formData.get('validacao') || '',
        sistema: false
    };
    
    adicionarVariavel(dadosVariavel);
    
    // Marcar como alterações pendentes
    marcarAlteracoesPendentes();
    
    // Fechar modal e limpar formulário
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalAdicionarVariavel'));
    modal.hide();
    form.reset();
    
    // Scroll para a nova variável
    setTimeout(() => {
        const novaVariavel = document.querySelector('.variavel-item:last-child');
        if (novaVariavel) {
            novaVariavel.scrollIntoView({ behavior: 'smooth', block: 'center' });
            novaVariavel.classList.add('border-primary');
            setTimeout(() => novaVariavel.classList.remove('border-primary'), 3000);
        }
    }, 100);
    
    Swal.fire({
        icon: 'success',
        title: 'Variável Adicionada!',
        text: `A variável ${dadosVariavel.nome} foi adicionada com sucesso. Lembre-se de clicar em "Salvar Configurações" para persistir no banco de dados.`,
        timer: 4000,
        timerProgressBar: true
    });
}

// Funções auxiliares de validação
function isNumeric(value) {
    return !isNaN(parseFloat(value)) && isFinite(value);
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidURL(url) {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
}

function mostrarErroInput(inputId, mensagem) {
    const input = document.getElementById(inputId);
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    
    input.classList.add('is-invalid');
    feedback.textContent = mensagem;
}

function verificarVariavelExistente(nome) {
    const variaveisExistentes = document.querySelectorAll('.variavel-nome');
    for (let input of variaveisExistentes) {
        if (input.value.toUpperCase() === nome) {
            return true;
        }
    }
    
    // Verificar também nas variáveis padrão
    return variaveisPadrao.some(v => v.nome === nome);
}

// Inicializar botão de scroll to top
function inicializarScrollTop() {
    const scrollTop = document.getElementById('kt_scrolltop');
    
    if (scrollTop) {
        // Mostrar/ocultar botão baseado no scroll
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollTop.style.display = 'flex';
            } else {
                scrollTop.style.display = 'none';
            }
        });
        
        // Click handler para voltar ao topo
        scrollTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Marcar como alterações pendentes
function marcarAlteracoesPendentes() {
    const indicador = document.getElementById('alteracoesPendentes');
    const botaoSalvar = document.getElementById('botaoSalvar');
    
    if (indicador) {
        indicador.style.display = 'flex';
    }
    
    if (botaoSalvar) {
        botaoSalvar.classList.add('btn-success');
        botaoSalvar.classList.remove('btn-primary');
    }
}

// Esconder indicador de alterações pendentes
function esconderAlteracoesPendentes() {
    const indicador = document.getElementById('alteracoesPendentes');
    const botaoSalvar = document.getElementById('botaoSalvar');
    
    if (indicador) {
        indicador.style.display = 'none';
    }
    
    if (botaoSalvar) {
        botaoSalvar.classList.remove('btn-success');
        botaoSalvar.classList.add('btn-primary');
    }
}

// Configurar formulário
function configurarFormulario() {
    const form = document.getElementById('formVariaveisDinamicas');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        console.log('📤 Enviando formulário de variáveis dinâmicas');
        
        // Mostrar loading
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';
        
        try {
            // Verificar se há variáveis para salvar
            const variaveisCards = document.querySelectorAll('.variavel-item');
            if (variaveisCards.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhuma Variável',
                    text: 'Adicione pelo menos uma variável antes de salvar.'
                });
                return;
            }
            
            // Coletar dados do formulário
            const formData = new FormData(form);
            
            // Enviar via fetch
            const response = await fetch('/parametros-variaveis-dinamicas', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                console.log('✅ Variáveis salvas com sucesso');
                
                // Esconder indicador de alterações pendentes
                esconderAlteracoesPendentes();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: result.message,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                });
            } else {
                console.log('❌ Erro ao salvar:', result.message);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: result.message
                });
                
                // Mostrar erros de validação se existirem
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            // Adicionar texto de erro se não existir
                            let errorDiv = input.parentNode.querySelector('.invalid-feedback');
                            if (!errorDiv) {
                                errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                input.parentNode.appendChild(errorDiv);
                            }
                            errorDiv.textContent = result.errors[field][0];
                        }
                    });
                }
            }
            
        } catch (error) {
            console.error('❌ Erro de rede:', error);
            
            Swal.fire({
                icon: 'error',
                title: 'Erro de Conexão',
                text: 'Não foi possível conectar ao servidor. Tente novamente.'
            });
        } finally {
            // Restaurar botão
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// Limpar erros de validação quando o usuário digitar e marcar alterações pendentes
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('is-invalid')) {
        e.target.classList.remove('is-invalid');
        const errorDiv = e.target.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    // Se o input está dentro do formulário de variáveis, marcar como alteração pendente
    if (e.target.closest('#formVariaveisDinamicas')) {
        marcarAlteracoesPendentes();
    }
});
</script>
@endpush