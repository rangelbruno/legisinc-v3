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
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="buscar-tipo" class="form-control" placeholder="Buscar tipo de proposição...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="btn-group w-100" role="group" aria-label="Categorias">
                        <button type="button" class="btn btn-outline-primary active" data-categoria="todos">
                            <i class="fas fa-th me-1"></i>Todos
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-categoria="leis">
                            <i class="fas fa-gavel me-1"></i>Leis
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-categoria="requerimentos">
                            <i class="fas fa-file-alt me-1"></i>Requerimentos
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-categoria="outros">
                            <i class="fas fa-folder me-1"></i>Outros
                        </button>
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
                            <span class="badge bg-dark">PR</span>
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
                            <span class="badge bg-dark">REL</span>
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
// Função para selecionar tipo e continuar
function selecionarTipo(codigo, nome) {
    document.getElementById('tipo-selecionado').value = codigo;
    document.getElementById('nome-selecionado').value = nome;
    document.getElementById('form-selecionar-tipo').submit();
}

// Filtro por categoria
document.querySelectorAll('[data-categoria]').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remover active de todos
        document.querySelectorAll('[data-categoria]').forEach(b => b.classList.remove('active'));
        // Adicionar active no clicado
        this.classList.add('active');
        
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
        
        // Mostrar mensagem se nenhum resultado
        document.getElementById('sem-resultados').style.display = visiveisCount === 0 ? '' : 'none';
    });
});

// Busca em tempo real
document.getElementById('buscar-tipo').addEventListener('input', function() {
    const busca = this.value.toLowerCase();
    const cards = document.querySelectorAll('.tipo-card');
    let visiveisCount = 0;
    
    cards.forEach(card => {
        const nome = card.dataset.nome.toLowerCase();
        const categoria = document.querySelector('[data-categoria].active').dataset.categoria;
        
        const matchBusca = nome.includes(busca);
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
    
    // Mostrar mensagem se nenhum resultado
    document.getElementById('sem-resultados').style.display = visiveisCount === 0 ? '' : 'none';
});

// Animação de hover nos cards
document.querySelectorAll('.shadow-hover').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});
</script>
@endpush

@push('styles')
<style>
.shadow-hover {
    transition: all 0.3s ease;
    cursor: pointer;
}

.shadow-hover:hover {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-body {
    padding: 1.5rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    font-weight: 600;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-sm:hover {
    transform: translateX(3px);
}

.input-group {
    border-radius: 10px;
    overflow: hidden;
}

.input-group-text {
    border: none;
}

.form-control {
    border: none;
    background: #f8f9fa;
    padding: 0.75rem;
}

.form-control:focus {
    background: white;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.btn-group .btn {
    font-weight: 500;
    padding: 0.6rem 1.2rem;
}

.btn-group .btn.active {
    background: var(--bs-primary);
    color: white;
}

#sem-resultados {
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

/* Responsividade */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
    }
    
    .btn-group .btn {
        flex: 1 1 auto;
        margin: 2px;
    }
    
    .icon-wrapper {
        width: 50px;
        height: 50px;
    }
    
    .card-title {
        font-size: 1rem;
    }
}
</style>
@endpush