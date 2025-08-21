@extends('components.layouts.app')

@section('title', 'Criar Nova Proposição')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Criar Nova Proposição</h1>
            <p class="text-muted">Selecione o tipo de documento legislativo que deseja criar</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Minhas Proposições
            </a>
        </div>
    </div>

    <!-- Filtros e Busca -->
    <div class="card mb-4 filter-card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="input-group input-group-enhanced">
                        <span class="input-group-text">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                        <input type="text" id="buscar-tipo" class="form-control form-control-enhanced" placeholder="Buscar tipo de proposição...">
                        <button class="btn btn-outline-secondary btn-clear d-none" type="button" id="clear-search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="search-hint mt-1" id="search-count"></div>
                </div>
                <div class="col-md-6">
                    <div class="filter-container">
                        <div class="filter-background"></div>
                        <div class="btn-group w-100" role="group" aria-label="Categorias">
                            <button type="button" class="btn btn-filter active" data-categoria="todos">
                                <i class="fas fa-th me-2"></i>
                                <span>Todos</span>
                                <span class="badge-count">0</span>
                            </button>
                            <button type="button" class="btn btn-filter" data-categoria="leis">
                                <i class="fas fa-gavel me-2"></i>
                                <span>Leis</span>
                                <span class="badge-count">0</span>
                            </button>
                            <button type="button" class="btn btn-filter" data-categoria="requerimentos">
                                <i class="fas fa-file-alt me-2"></i>
                                <span>Requerimentos</span>
                                <span class="badge-count">0</span>
                            </button>
                            <button type="button" class="btn btn-filter" data-categoria="outros">
                                <i class="fas fa-folder me-2"></i>
                                <span>Outros</span>
                                <span class="badge-count">0</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Tipos de Proposição -->
    <div class="row" id="tipos-grid">
        <!-- Projetos de Lei -->
        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Projeto de Lei Ordinária">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-file-contract text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Projeto de Lei Ordinária</h5>
                            <span class="badge bg-primary">PL</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Proposição para regular matéria de competência do Poder Legislativo, com sanção do Executivo.</p>
                    <button class="btn btn-primary btn-sm w-100 mt-3" onclick="selecionarTipo('projeto_lei_ordinaria', 'Projeto de Lei Ordinária')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PL
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Projeto de Lei Complementar">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-file-signature text-info fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Projeto de Lei Complementar</h5>
                            <span class="badge bg-info">PLC</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Regulamenta matéria constitucional que exige quórum de maioria absoluta.</p>
                    <button class="btn btn-info btn-sm w-100 mt-3" onclick="selecionarTipo('projeto_lei_complementar', 'Projeto de Lei Complementar')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PLC
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Proposta de Emenda à Constituição">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-shield-alt text-danger fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Proposta de Emenda à Constituição</h5>
                            <span class="badge bg-danger">PEC</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Altera o texto constitucional, exigindo aprovação por 3/5 dos membros.</p>
                    <button class="btn btn-danger btn-sm w-100 mt-3" onclick="selecionarTipo('proposta_emenda_constituicao', 'Proposta de Emenda à Constituição')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PEC
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Proposta de Emenda à Lei Orgânica">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-landmark text-danger fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Proposta de Emenda à Lei Orgânica</h5>
                            <span class="badge bg-danger">PELOM</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Altera a Lei Orgânica Municipal, exigindo quórum de 2/3.</p>
                    <button class="btn btn-danger btn-sm w-100 mt-3" onclick="selecionarTipo('proposta_emenda_lei_organica', 'Proposta de Emenda à Lei Orgânica')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PELOM
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Projeto de Decreto Legislativo">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-stamp text-success fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Projeto de Decreto Legislativo</h5>
                            <span class="badge bg-success">PDL</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Regula matérias de competência exclusiva do Legislativo, dispensando sanção.</p>
                    <button class="btn btn-success btn-sm w-100 mt-3" onclick="selecionarTipo('projeto_decreto_legislativo', 'Projeto de Decreto Legislativo')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PDL
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Projeto de Resolução">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-dark bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-scroll text-dark fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Projeto de Resolução</h5>
                            <span class="badge bg-dark text-white">PR</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Regula matéria de competência privativa da Câmara, de caráter político-administrativo.</p>
                    <button class="btn btn-dark btn-sm w-100 mt-3" onclick="selecionarTipo('projeto_resolucao', 'Projeto de Resolução')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PR
                    </button>
                </div>
            </div>
        </div>

        <!-- Requerimentos e Indicações -->
        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="requerimentos" data-nome="Requerimento">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-question-circle text-info fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Requerimento</h5>
                            <span class="badge bg-info">REQ</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Solicita informações, providências ou manifesta posição sobre determinado assunto.</p>
                    <button class="btn btn-info btn-sm w-100 mt-3" onclick="selecionarTipo('requerimento', 'Requerimento')">
                        <i class="fas fa-arrow-right me-2"></i>Criar REQ
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="requerimentos" data-nome="Indicação">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-lightbulb text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Indicação</h5>
                            <span class="badge bg-primary">IND</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Sugestão de medida de interesse público aos Poderes competentes.</p>
                    <button class="btn btn-primary btn-sm w-100 mt-3" onclick="selecionarTipo('indicacao', 'Indicação')">
                        <i class="fas fa-arrow-right me-2"></i>Criar IND
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="requerimentos" data-nome="Moção">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-comment-alt text-warning fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Moção</h5>
                            <span class="badge bg-warning text-dark">MOC</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Manifestação da Câmara aplaudindo, apoiando, protestando ou repudiando.</p>
                    <button class="btn btn-warning btn-sm w-100 mt-3" onclick="selecionarTipo('mocao', 'Moção')">
                        <i class="fas fa-arrow-right me-2"></i>Criar MOC
                    </button>
                </div>
            </div>
        </div>

        <!-- Emendas e Substitutivos -->
        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Emenda">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-secondary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-edit text-secondary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Emenda</h5>
                            <span class="badge bg-secondary">EME</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Proposição acessória (supressiva, substitutiva, aditiva ou modificativa).</p>
                    <button class="btn btn-secondary btn-sm w-100 mt-3" onclick="selecionarTipo('emenda', 'Emenda')">
                        <i class="fas fa-arrow-right me-2"></i>Criar EME
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Subemenda">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-light rounded-3 p-3 me-3">
                            <i class="fas fa-layer-group text-secondary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Subemenda</h5>
                            <span class="badge bg-light text-dark">SUB</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Emenda apresentada a outra emenda.</p>
                    <button class="btn btn-light btn-sm w-100 mt-3" onclick="selecionarTipo('subemenda', 'Subemenda')">
                        <i class="fas fa-arrow-right me-2"></i>Criar SUB
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Substitutivo">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-exchange-alt text-info fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Substitutivo</h5>
                            <span class="badge bg-info">SUBS</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Proposição que substitui integralmente outra proposição.</p>
                    <button class="btn btn-info btn-sm w-100 mt-3" onclick="selecionarTipo('substitutivo', 'Substitutivo')">
                        <i class="fas fa-arrow-right me-2"></i>Criar SUBS
                    </button>
                </div>
            </div>
        </div>

        <!-- Documentos Especiais -->
        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Parecer de Comissão">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-clipboard-check text-success fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Parecer de Comissão</h5>
                            <span class="badge bg-success">PAR</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Análise técnica sobre constitucionalidade, mérito ou finanças.</p>
                    <button class="btn btn-success btn-sm w-100 mt-3" onclick="selecionarTipo('parecer_comissao', 'Parecer de Comissão')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PAR
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Relatório">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-dark bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-file-alt text-dark fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Relatório</h5>
                            <span class="badge bg-dark text-white">REL</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Documento de CPI, comissão especial ou mista.</p>
                    <button class="btn btn-dark btn-sm w-100 mt-3" onclick="selecionarTipo('relatorio', 'Relatório')">
                        <i class="fas fa-arrow-right me-2"></i>Criar REL
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Ofício">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-envelope text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Ofício</h5>
                            <span class="badge bg-primary">OFI</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Comunicação oficial da Câmara.</p>
                    <button class="btn btn-primary btn-sm w-100 mt-3" onclick="selecionarTipo('oficio', 'Ofício')">
                        <i class="fas fa-arrow-right me-2"></i>Criar OFI
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Mensagem do Executivo">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-comment-dots text-info fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Mensagem do Executivo</h5>
                            <span class="badge bg-info">MSG</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Comunicação do Poder Executivo ao Legislativo.</p>
                    <button class="btn btn-info btn-sm w-100 mt-3" onclick="selecionarTipo('mensagem_executivo', 'Mensagem do Executivo')">
                        <i class="fas fa-arrow-right me-2"></i>Criar MSG
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Recurso">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-undo text-danger fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Recurso</h5>
                            <span class="badge bg-danger">REC</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Contestação contra decisão da Mesa, Comissão ou Presidência.</p>
                    <button class="btn btn-danger btn-sm w-100 mt-3" onclick="selecionarTipo('recurso', 'Recurso')">
                        <i class="fas fa-arrow-right me-2"></i>Criar REC
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Veto">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-ban text-danger fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Veto</h5>
                            <span class="badge bg-danger">VETO</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Rejeição total ou parcial de projeto pelo Executivo.</p>
                    <button class="btn btn-danger btn-sm w-100 mt-3" onclick="selecionarTipo('veto', 'Veto')">
                        <i class="fas fa-arrow-right me-2"></i>Criar VETO
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="outros" data-nome="Destaque">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-filter text-warning fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Destaque</h5>
                            <span class="badge bg-warning text-dark">DEST</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Votação em separado de dispositivo.</p>
                    <button class="btn btn-warning btn-sm w-100 mt-3" onclick="selecionarTipo('destaque', 'Destaque')">
                        <i class="fas fa-arrow-right me-2"></i>Criar DEST
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Medida Provisória">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Medida Provisória</h5>
                            <span class="badge bg-warning text-dark">MP</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Ato do Executivo com força de lei, em caso de urgência.</p>
                    <button class="btn btn-warning btn-sm w-100 mt-3" onclick="selecionarTipo('medida_provisoria', 'Medida Provisória')">
                        <i class="fas fa-arrow-right me-2"></i>Criar MP
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Projeto de Lei Delegada">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-secondary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-hands-helping text-secondary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Projeto de Lei Delegada</h5>
                            <span class="badge bg-secondary">PLD</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Lei elaborada pelo Executivo mediante delegação do Legislativo.</p>
                    <button class="btn btn-secondary btn-sm w-100 mt-3" onclick="selecionarTipo('projeto_lei_delegada', 'Projeto de Lei Delegada')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PLD
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4 tipo-card" data-categoria="leis" data-nome="Projeto de Consolidação das Leis">
            <div class="card h-100 shadow-hover border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-secondary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-book text-secondary fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Projeto de Consolidação das Leis</h5>
                            <span class="badge bg-secondary">PCL</span>
                        </div>
                    </div>
                    <p class="card-text text-muted small">Reunião de diplomas legais sobre a mesma matéria.</p>
                    <button class="btn btn-secondary btn-sm w-100 mt-3" onclick="selecionarTipo('projeto_consolidacao_leis', 'Projeto de Consolidação das Leis')">
                        <i class="fas fa-arrow-right me-2"></i>Criar PCL
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagem quando nenhum tipo for encontrado -->
    <div id="sem-resultados" class="text-center py-5" style="display: none;">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Nenhum tipo de proposição encontrado</h5>
        <p class="text-muted">Tente ajustar sua busca ou filtros</p>
    </div>
</div>

<!-- Form oculto para envio -->
<form id="form-selecionar-tipo" action="{{ route('proposicoes.create') }}" method="GET" style="display: none;">
    <input type="hidden" name="tipo" id="tipo-selecionado">
    <input type="hidden" name="nome" id="nome-selecionado">
</form>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    // Função para selecionar tipo e continuar
    window.selecionarTipo = function(codigo, nome) {
        try {
            const tipoInput = document.getElementById('tipo-selecionado');
            const nomeInput = document.getElementById('nome-selecionado');
            const form = document.getElementById('form-selecionar-tipo');
            
            if (tipoInput && nomeInput && form) {
                tipoInput.value = codigo;
                nomeInput.value = nome;
                form.submit();
            }
        } catch (error) {
            console.error('Erro ao selecionar tipo:', error);
        }
    }

    // Filtro por categoria com contagem dinâmica
    document.querySelectorAll('[data-categoria]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            try {
                e.preventDefault();
                
                // Remover active de todos
                document.querySelectorAll('[data-categoria]').forEach(b => {
                    b.classList.remove('active');
                });
                // Adicionar active no clicado
                this.classList.add('active');
                
                // Forçar atualização visual
                setTimeout(() => {
                    if (!this.classList.contains('active')) {
                        this.classList.add('active');
                    }
                }, 10);
                
                const categoria = this.dataset.categoria;
                const cards = document.querySelectorAll('.tipo-card');
                let visiveisCount = 0;
                
                cards.forEach(card => {
                    if (categoria === 'todos' || card.dataset.categoria === categoria) {
                        card.style.display = '';
                        if (!card.classList.contains('d-none')) {
                            visiveisCount++;
                        }
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Atualizar contadores dinamicamente
                if (typeof updateFilterCounts === 'function') {
                    updateFilterCounts();
                }
                
                // Mostrar mensagem se nenhum resultado
                const semResultados = document.getElementById('sem-resultados');
                if (semResultados) {
                    semResultados.style.display = visiveisCount === 0 ? '' : 'none';
                }
            } catch (error) {
                console.error('Erro no filtro:', error);
            }
        });
    });

    // Função para atualizar contadores dos filtros
    window.updateFilterCounts = function() {
        try {
            const categorias = ['todos', 'leis', 'requerimentos', 'outros'];
            
            categorias.forEach(cat => {
                const btn = document.querySelector(`[data-categoria="${cat}"]`);
                if (btn) {
                    const cards = cat === 'todos' 
                        ? document.querySelectorAll('.tipo-card:not(.d-none)')
                        : document.querySelectorAll(`.tipo-card[data-categoria="${cat}"]:not(.d-none)`);
                    
                    const count = btn.querySelector('.badge-count');
                    if (count) {
                        count.textContent = cards.length;
                        // Adicionar animação de mudança
                        count.classList.add('count-update');
                        setTimeout(() => {
                            if (count) {
                                count.classList.remove('count-update');
                            }
                        }, 300);
                    }
                }
            });
        } catch (error) {
            console.error('Erro ao atualizar contadores:', error);
        }
    }

    // Busca em tempo real com melhorias
    const searchInput = document.getElementById('buscar-tipo');
    const searchClear = document.getElementById('clear-search');
    const searchCount = document.getElementById('search-count');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const busca = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.tipo-card');
            let visiveisCount = 0;
            let totalCards = cards.length;
            
            // Mostrar/esconder botão de limpar
            if (busca.length > 0) {
                searchClear.classList.remove('d-none');
            } else {
                searchClear.classList.add('d-none');
            }
            
            cards.forEach(card => {
                const nome = card.dataset.nome.toLowerCase();
                const categoria = document.querySelector('[data-categoria].active').dataset.categoria;
                
                const matchBusca = busca === '' || nome.includes(busca);
                const matchCategoria = categoria === 'todos' || card.dataset.categoria === categoria;
                
                if (matchBusca && matchCategoria) {
                    card.classList.remove('d-none');
                    card.style.display = '';
                    visiveisCount++;
                } else {
                    card.classList.add('d-none');
                    card.style.display = 'none';
                }
            });
            
            // Atualizar contador de resultados
            if (busca.length > 0) {
                searchCount.innerHTML = `<i class="fas fa-info-circle"></i>${visiveisCount} de ${totalCards} tipos encontrados`;
                searchCount.classList.add('show');
            } else {
                searchCount.classList.remove('show');
            }
            
            // Atualizar contadores após busca
            if (typeof updateFilterCounts === 'function') {
                updateFilterCounts();
            }
            
            // Mostrar mensagem se nenhum resultado
            const semResultados = document.getElementById('sem-resultados');
            if (semResultados) {
                semResultados.style.display = visiveisCount === 0 ? '' : 'none';
            }
        });

        // Botão de limpar busca
        if (searchClear) {
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }
    }

    // Animação de hover nos cards
    document.querySelectorAll('.shadow-hover').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Inicializar ao carregar a página
    window.addEventListener('load', function() {
        setTimeout(function() {
            try {
                // Garantir que todos os cards estejam visíveis inicialmente
                const allCards = document.querySelectorAll('.tipo-card');
                if (allCards.length > 0) {
                    allCards.forEach(card => {
                        card.style.display = '';
                        card.classList.remove('d-none');
                    });
                }
                
                // Atualizar contadores com os valores corretos
                if (typeof updateFilterCounts === 'function') {
                    updateFilterCounts();
                }
                
                // Garantir que o botão "Todos" esteja ativo
                const filterButtons = document.querySelectorAll('[data-categoria]');
                if (filterButtons.length > 0) {
                    filterButtons.forEach(btn => {
                        btn.classList.remove('active');
                    });
                    const todosBtn = document.querySelector('[data-categoria="todos"]');
                    if (todosBtn) {
                        todosBtn.classList.add('active');
                    }
                }
            } catch (error) {
                console.error('Erro na inicialização:', error);
            }
        }, 100);
    });
    
})(); // Fechar IIFE
</script>
@endpush

@push('styles')
<style>
/* Card Principal - Design Moderno */
.card {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.06);
    background: #ffffff;
    position: relative;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--bs-primary), var(--bs-info));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.card:hover::before {
    opacity: 1;
}

/* Sombras e Elevação */
.shadow-hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    cursor: pointer;
}

.shadow-hover:hover {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-8px);
}

/* Corpo do Card */
.card-body {
    padding: 1.75rem;
    position: relative;
    z-index: 1;
}

/* Ícones - Design Glassmorphism */
.icon-wrapper {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    position: relative;
    overflow: hidden;
}

.icon-wrapper::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
    transform: translate(-50%, -50%);
}

.icon-wrapper i {
    position: relative;
    z-index: 1;
}

/* Títulos e Textos */
.card-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 0.25rem !important;
    line-height: 1.3;
}

.card-text {
    font-size: 0.875rem;
    line-height: 1.5;
    color: #6c757d;
    margin-top: 0.75rem;
}

/* Badges Modernos */
.badge {
    font-size: 0.7rem;
    padding: 0.35rem 0.65rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    border-radius: 6px;
    text-transform: uppercase;
    position: relative;
    overflow: hidden;
}

.badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.card:hover .badge::before {
    left: 100%;
}

/* Botões Aprimorados */
.btn-sm {
    padding: 0.625rem 1.25rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    border: none;
    position: relative;
    overflow: hidden;
}

.btn-sm::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
}

.btn-sm:hover::before {
    width: 300px;
    height: 300px;
}

.btn-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.btn-sm i {
    transition: transform 0.3s ease;
}

.btn-sm:hover i {
    transform: translateX(4px);
}

/* Cores dos Botões com Gradientes */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

.btn-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.btn-warning {
    background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
}

.btn-secondary {
    background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
}

.btn-dark {
    background: linear-gradient(135deg, #232526 0%, #414345 100%);
}

.btn-light {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    color: #2c3e50 !important;
}

/* Melhorias do Input Group */
.input-group-enhanced {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.input-group-enhanced:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    transform: translateY(-1px);
}

.input-group-enhanced:focus-within {
    box-shadow: 0 6px 16px rgba(var(--bs-primary-rgb), 0.2);
    transform: translateY(-2px);
}

.input-group-enhanced .input-group-text {
    border: none;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-right: 1px solid rgba(var(--bs-primary-rgb), 0.15);
}

.input-group-enhanced .input-group-text i {
    transition: all 0.3s ease;
}

.input-group-enhanced:focus-within .input-group-text {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
}

.input-group-enhanced:focus-within .input-group-text i {
    animation: searchPulse 2s infinite;
}

@keyframes searchPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.form-control-enhanced {
    border: none;
    background: #f8f9fa;
    padding: 0.875rem;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.form-control-enhanced:focus {
    background: white;
    box-shadow: none;
    border-color: transparent;
}

.form-control-enhanced::placeholder {
    color: #8e9aaf;
    font-weight: 400;
}

/* Botão de Limpar */
.btn-clear {
    border-left: none;
    background: transparent;
    color: #6c757d;
    padding: 0.375rem 0.75rem;
    transition: all 0.3s ease;
}

.btn-clear:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border-color: rgba(220, 53, 69, 0.25);
    transform: scale(1.05);
}

.btn-clear:focus {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

/* Dica de Busca */
.search-hint {
    font-size: 0.8rem;
    color: #6c757d;
    opacity: 0;
    transform: translateY(-5px);
    transition: all 0.3s ease;
}

.search-hint.show {
    opacity: 1;
    transform: translateY(0);
}

.search-hint i {
    color: var(--bs-primary);
    margin-right: 0.25rem;
}

/* Card de Filtros Principal */
.filter-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: visible;
}

.filter-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    opacity: 0.8;
}

/* Container dos Filtros com Fundo Especial */
.filter-container {
    position: relative;
    padding: 1rem;
    background: linear-gradient(145deg, #dde3ed 0%, #c8d1e0 100%) !important;
    border-radius: 16px;
    box-shadow: 
        inset 0 4px 8px rgba(0, 0, 0, 0.1),
        inset 0 -2px 4px rgba(255, 255, 255, 0.7),
        0 2px 8px rgba(0, 0, 0, 0.05) !important;
    border: 1px solid rgba(102, 126, 234, 0.15);
    overflow: hidden;
}

.filter-container::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.08) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
    pointer-events: none;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Fundo Animado dos Filtros */
.filter-background {
    position: absolute;
    inset: 0;
    border-radius: 16px;
    background: 
        linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(240, 147, 251, 0.03) 100%),
        radial-gradient(circle at 30% 30%, rgba(102, 126, 234, 0.06) 0%, transparent 40%),
        radial-gradient(circle at 70% 70%, rgba(240, 147, 251, 0.06) 0%, transparent 40%);
    opacity: 0.6;
    pointer-events: none;
}


/* Grupo de Botões de Filtro - Design Aprimorado */
.btn-group {
    display: flex;
    gap: 0.4rem;
    padding: 0;
    background: transparent;
    border-radius: 12px;
    position: relative;
    z-index: 1;
}

/* Reset dos estilos Bootstrap para os botões */
.btn-filter.btn {
    box-shadow: none !important;
    text-decoration: none !important;
    outline: none !important;
    border: 2px solid #e9ecef !important;
}

.btn-filter.btn:focus,
.btn-filter.btn:active {
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25) !important;
    outline: none !important;
}

.btn-filter.btn:not(.active):focus,
.btn-filter.btn:not(.active):active {
    background: linear-gradient(135deg, #f0f4ff 0%, #e6ecff 100%) !important;
    border-color: #667eea !important;
    color: #667eea !important;
}

.btn-filter {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: 2px solid #e9ecef !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    color: #495057 !important;
    border-radius: 10px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.btn-filter::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
    transition: left 0.5s ease;
    z-index: 0;
}

.btn-filter::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.3s ease;
    z-index: 1;
}

.btn-filter > * {
    position: relative;
    z-index: 2;
}

.btn-filter:hover::before {
    left: 100%;
}

.btn-filter:hover {
    transform: translateY(-2px) !important;
    background: linear-gradient(135deg, #f0f4ff 0%, #e6ecff 100%) !important;
    border-color: #667eea !important;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25) !important;
    color: #667eea !important;
}

.btn-filter:hover::after {
    transform: scaleX(1);
}

/* Estado Ativo com Múltiplos Indicadores Visuais - ALTA PRIORIDADE */
.filter-container .btn-group .btn-filter.btn.active,
.btn-group .btn-filter.btn.active,
.btn-filter.btn.active,
button.btn-filter.active,
.btn.btn-filter.active {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    color: white !important;
    border-color: #007bff !important;
    transform: translateY(-3px) !important;
    box-shadow: 
        0 12px 30px rgba(0, 123, 255, 0.5),
        inset 0 3px 6px rgba(255, 255, 255, 0.3),
        inset 0 -3px 6px rgba(0, 0, 0, 0.2),
        0 0 0 3px rgba(0, 123, 255, 0.2) !important;
    position: relative !important;
    z-index: 3 !important;
    font-weight: 700 !important;
}

/* Fallback adicional para garantir que funcione */
[data-categoria].active {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    color: white !important;
    border-color: #007bff !important;
    transform: translateY(-3px) !important;
    box-shadow: 
        0 12px 30px rgba(0, 123, 255, 0.5),
        inset 0 3px 6px rgba(255, 255, 255, 0.3),
        inset 0 -3px 6px rgba(0, 0, 0, 0.2),
        0 0 0 3px rgba(0, 123, 255, 0.2) !important;
    font-weight: 700 !important;
}

.btn-group .btn-filter.btn.active::before,
.btn-filter.btn.active::before {
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.1)) !important;
    left: 0 !important;
    animation: activeShimmer 2s ease-in-out infinite !important;
}

.btn-group .btn-filter.btn.active::after,
.btn-filter.btn.active::after {
    content: '' !important;
    position: absolute !important;
    inset: -3px !important;
    background: linear-gradient(135deg, #007bff, #0056b3, #004085) !important;
    border-radius: 13px !important;
    z-index: -1 !important;
    opacity: 0.4 !important;
    filter: blur(10px) !important;
}

@keyframes activeShimmer {
    0%, 100% { left: -100%; }
    50% { left: 100%; }
}

/* Hover no botão ativo */
.btn-group .btn-filter.btn.active:hover,
.btn-filter.btn.active:hover {
    background: linear-gradient(135deg, #0069d9 0%, #004085 100%) !important;
    transform: translateY(-4px) !important;
    box-shadow: 
        0 15px 40px rgba(0, 123, 255, 0.6),
        inset 0 4px 8px rgba(255, 255, 255, 0.4),
        inset 0 -4px 8px rgba(0, 0, 0, 0.25),
        0 0 0 4px rgba(0, 123, 255, 0.3) !important;
}

.btn-group .btn-filter.btn.active:hover::after,
.btn-filter.btn.active:hover::after {
    opacity: 0.6 !important;
    filter: blur(15px) !important;
}

/* Indicador de Ativo - Pulsação Suave */
.btn-group .btn-filter.btn.active,
.btn-filter.btn.active {
    animation: activePulse 4s ease-in-out infinite !important;
}

@keyframes activePulse {
    0%, 100% {
        box-shadow: 0 12px 30px rgba(0, 123, 255, 0.5),
                    inset 0 3px 6px rgba(255, 255, 255, 0.3),
                    inset 0 -3px 6px rgba(0, 0, 0, 0.2),
                    0 0 0 3px rgba(0, 123, 255, 0.2);
    }
    50% {
        box-shadow: 0 15px 35px rgba(0, 123, 255, 0.6),
                    inset 0 4px 8px rgba(255, 255, 255, 0.4),
                    inset 0 -4px 8px rgba(0, 0, 0, 0.25),
                    0 0 0 4px rgba(0, 123, 255, 0.3);
    }
}

.btn-filter i {
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.btn-filter:hover i {
    transform: scale(1.1) rotate(5deg);
}

.btn-group .btn-filter.btn.active i,
.btn-filter.btn.active i {
    color: white !important;
    animation: iconPulse 2s infinite !important;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2)) !important;
}

/* Badge de Contagem */
.badge-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.15rem 0.35rem;
    margin-left: 0.5rem;
    border-radius: 10px;
    min-width: 22px;
    height: 18px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(238, 90, 36, 0.3);
}

.btn-filter:hover .badge-count {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: scale(1.15);
    box-shadow: 0 3px 6px rgba(102, 126, 234, 0.4);
}

.btn-group .btn-filter.btn.active .badge-count,
.btn-filter.btn.active .badge-count {
    background: rgba(255, 255, 255, 0.95) !important;
    color: #007bff !important;
    font-weight: 800 !important;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3) !important;
    border: 2px solid rgba(255, 255, 255, 0.8) !important;
}

.btn-filter:hover .badge-count {
    transform: scale(1.15);
}

.badge-count.count-update {
    animation: countBounce 0.3s ease;
}

@keyframes countBounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.25); }
}

@keyframes iconPulse {
    0%, 100% { 
        transform: scale(1);
        opacity: 1;
    }
    50% { 
        transform: scale(1.05);
        opacity: 0.8;
    }
}

/* Responsividade dos Filtros */
@media (max-width: 768px) {
    .filter-container {
        padding: 0.5rem;
    }
    
    .btn-group {
        flex-wrap: wrap;
        gap: 0.4rem;
    }
    
    .btn-filter {
        flex: 1 1 calc(50% - 0.2rem);
        min-width: 130px;
        padding: 0.6rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .btn-filter i {
        font-size: 0.85rem;
    }
    
    .badge-count {
        font-size: 0.65rem;
        padding: 0.1rem 0.3rem;
        margin-left: 0.4rem;
    }
}

@media (max-width: 480px) {
    .btn-group {
        gap: 0.3rem;
        padding: 0.3rem;
    }
    
    .btn-filter {
        padding: 0.5rem 0.6rem;
        font-size: 0.75rem;
        border-radius: 8px;
    }
    
    .btn-filter i {
        font-size: 0.8rem;
        margin-right: 0.25rem !important;
    }
    
    .badge-count {
        font-size: 0.6rem;
        min-width: 18px;
        height: 16px;
    }
}

/* Card de Filtros */
.card.mb-4 {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

/* Animação de Entrada */
.tipo-card {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

.tipo-card:nth-child(1) { animation-delay: 0.05s; }
.tipo-card:nth-child(2) { animation-delay: 0.1s; }
.tipo-card:nth-child(3) { animation-delay: 0.15s; }
.tipo-card:nth-child(4) { animation-delay: 0.2s; }
.tipo-card:nth-child(5) { animation-delay: 0.25s; }
.tipo-card:nth-child(6) { animation-delay: 0.3s; }

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

/* Sem Resultados */
#sem-resultados {
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 16px;
    margin: 2rem 0;
}

#sem-resultados i {
    color: #a8b2c1;
    margin-bottom: 1rem;
}

/* Responsividade Aprimorada */
@media (max-width: 768px) {
    .btn-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .btn-group .btn {
        margin: 0;
        padding: 0.6rem;
        font-size: 0.85rem;
    }
    
    .icon-wrapper {
        width: 56px;
        height: 56px;
    }
    
    .card-title {
        font-size: 1rem;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
    }
}

@media (max-width: 576px) {
    .d-flex.align-items-center.mb-3 {
        flex-direction: column;
        text-align: center;
    }
    
    .icon-wrapper {
        margin-bottom: 1rem;
        margin-right: 0 !important;
    }
    
    .card-title {
        text-align: center;
    }
    
    .badge {
        display: inline-block;
        margin-top: 0.5rem;
    }
}

/* Melhorias de Acessibilidade */
.btn:focus,
.form-control:focus {
    outline: 2px solid var(--bs-primary);
    outline-offset: 2px;
}

.card:focus-within {
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

/* Dark Mode Support (opcional) */
@media (prefers-color-scheme: dark) {
    .card {
        background: #1a1a1a;
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    .card-title {
        color: #e9ecef;
    }
    
    .card-text {
        color: #adb5bd;
    }
    
    .form-control {
        background: #2c2c2c;
        color: #e9ecef;
    }
    
    .btn-group .btn {
        background: #2c2c2c;
        border-color: #495057;
        color: #e9ecef;
    }
}
</style>
@endpush