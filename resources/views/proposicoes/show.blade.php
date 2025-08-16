@extends('components.layouts.app')

@section('title', 'Visualizar Proposição')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-5">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Proposição #{{ $proposicao->id }}</h1>
            <p class="text-muted">Visualização detalhada da proposição</p>
        </div>
        <div>
            <a href="{{ route('proposicoes.minhas-proposicoes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
            @if($proposicao->status === 'rascunho')
                @if($proposicao->template_id)
                    <a href="{{ route('proposicoes.editar-onlyoffice', ['proposicao' => $proposicao->id, 'template' => $proposicao->template_id]) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-file-word me-2"></i>Editar
                    </a>
                @else
                    <a href="{{ route('proposicoes.onlyoffice.editor-parlamentar', $proposicao->id) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-file-word me-2"></i>Editar no OnlyOffice
                    </a>
                @endif
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Conteúdo Principal -->
        <div class="col-lg-8">
            <!-- Informações Básicas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informações Básicas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo:</label>
                                <div>
                                    <span class="badge badge-secondary fs-6">{{ strtoupper($proposicao->tipo) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <div>
                                    @switch($proposicao->status)
                                        @case('rascunho')
                                            <span class="badge badge-warning fs-6">Rascunho</span>
                                            @break
                                        @case('analise')
                                            <span class="badge badge-info fs-6">Em Análise</span>
                                            @break
                                        @case('aprovada')
                                            <span class="badge badge-success fs-6">Aprovada</span>
                                            @break
                                        @case('rejeitada')
                                            <span class="badge badge-danger fs-6">Rejeitada</span>
                                            @break
                                        @case('aguardando_aprovacao_autor')
                                            <span class="badge badge-primary fs-6">Aguardando Aprovação</span>
                                            @break
                                        @case('devolvido_edicao')
                                            <span class="badge badge-warning fs-6">Devolvido para Edição</span>
                                            @break
                                        @case('devolvido_correcao')
                                            <span class="badge badge-danger fs-6">Devolvido p/ Correção</span>
                                            @break
                                        @case('editado_legislativo')
                                            <span class="badge badge-info fs-6">Editado pelo Legislativo</span>
                                            @break
                                        @case('enviado_legislativo')
                                            <span class="badge badge-secondary fs-6">Enviado para Legislativo</span>
                                            @break
                                        @case('em_revisao')
                                            <span class="badge badge-primary fs-6">Em Revisão</span>
                                            @break
                                        @case('em_edicao')
                                            <span class="badge badge-warning fs-6">Em Edição</span>
                                            @break
                                        @case('assinado')
                                            <span class="badge badge-success fs-6">Assinado</span>
                                            @break
                                        @case('enviado_protocolo')
                                            <span class="badge badge-info fs-6">Enviado para Protocolo</span>
                                            @break
                                        @case('aprovado_assinatura')
                                            <span class="badge badge-warning fs-6">Pronto para Assinatura</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary fs-6">{{ ucfirst(str_replace('_', ' ', $proposicao->status)) }}</span>
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ementa:</label>
                        <div id="ementa-container" class="p-3 bg-light rounded">
                            @if($proposicao->ementa)
                                @if(str_contains($proposicao->ementa, 'a ser definid') || str_contains($proposicao->ementa, 'em elaboração') || str_contains($proposicao->ementa, 'serem definidos') || str_contains($proposicao->ementa, 'definidos'))
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-warning me-2"></i>
                                        <span class="text-warning">{{ $proposicao->ementa }}</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Esta ementa foi gerada automaticamente. Complete o preenchimento dos campos do template para definir uma ementa específica.
                                    </small>
                                @else
                                    {{ $proposicao->ementa }}
                                @endif
                            @elseif(isset($templateVariables['ementa']))
                                {{ $templateVariables['ementa'] }}
                            @elseif(isset($templateVariables['finalidade']))
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-lightbulb text-info me-2"></i>
                                    {{ $templateVariables['finalidade'] }}
                                </div>
                                <small class="text-muted d-block mt-1">Baseado na finalidade definida no template</small>
                            @elseif(isset($templateVariables['texto']))
                                {{ Str::limit($templateVariables['texto'], 200) }}
                                <small class="text-muted d-block mt-1">Extraído do conteúdo do template</small>
                            @else
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle text-muted me-2"></i>
                                    <span class="text-muted">Ementa não informada</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Complete o preenchimento do template para gerar a ementa automaticamente.
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Autor:</label>
                                <div>{{ $proposicao->autor->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Data de Criação:</label>
                                <div>{{ date('d/m/Y H:i', strtotime($proposicao->created_at ?? now())) }}</div>
                            </div>
                        </div>
                    </div>

                    @if(in_array($proposicao->status, ['aguardando_aprovacao_autor', 'devolvido_edicao']) && $proposicao->observacoes_retorno)
                    <div class="alert alert-warning mt-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-comment-dots me-2"></i>
                            Observações do Legislativo
                        </h6>
                        <p class="mb-0">{{ $proposicao->observacoes_retorno }}</p>
                        @if($proposicao->data_retorno_legislativo)
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            {{ $proposicao->data_retorno_legislativo->format('d/m/Y H:i') }}
                        </small>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Conteúdo da Proposição -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-text text-primary me-2"></i>
                        Conteúdo da Proposição
                    </h5>
                </div>
                <div class="card-body">
                    <div id="conteudo-container">
                    @if(!empty($proposicao->conteudo))
                        <div class="documento-content">
                            {!! $proposicao->conteudo !!}
                        </div>
                    @elseif(!empty($conteudoProcessado))
                        <div class="documento-content">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Conteúdo gerado a partir do template:</strong>
                            </div>
                            <div class="p-3 border rounded bg-light">
                                {!! nl2br(e($conteudoProcessado)) !!}
                            </div>
                        </div>
                    @elseif(!empty($templateVariables))
                        <div class="documento-content">
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Variáveis do template preenchidas:</strong>
                            </div>
                            @foreach($templateVariables as $key => $value)
                                @if(!empty($value) && !in_array($key, ['data_atual', 'autor_nome', 'nome_camara', 'imagem_cabecalho']))
                                    <div class="mb-3">
                                        <label class="fw-bold text-capitalize">{{ str_replace('_', ' ', $key) }}:</label>
                                        <div class="p-2 border rounded bg-light">
                                            {{ $value }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <h5>Conteúdo não disponível</h5>
                            <p>O conteúdo desta proposição ainda não foi definido.</p>
                            @if($proposicao->status === 'rascunho')
                                @if($proposicao->template_id)
                                    <a href="{{ route('proposicoes.editar-onlyoffice', ['proposicao' => $proposicao->id, 'template' => $proposicao->template_id]) }}" class="btn btn-primary">
                                        <i class="fas fa-file-word me-2"></i>Adicionar Conteúdo
                                    </a>
                                @else
                                    <a href="{{ route('proposicoes.onlyoffice.editor-parlamentar', $proposicao->id) }}" class="btn btn-primary">
                                        <i class="fas fa-file-word me-2"></i>Adicionar Conteúdo no OnlyOffice
                                    </a>
                                @endif
                            @endif
                        </div>
                    @endif
                    </div> <!-- end conteudo-container -->
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cog text-primary me-2"></i>
                        Ações
                    </h6>
                </div>
                <div class="card-body">
                    @if($proposicao->status === 'rascunho')
                        <div class="d-grid gap-2">
                            @if($proposicao->template_id)
                                <a href="{{ route('proposicoes.editar-onlyoffice', ['proposicao' => $proposicao->id, 'template' => $proposicao->template_id]) }}" class="btn btn-primary">
                                    <i class="fas fa-file-word me-2"></i>Editar Proposição
                                </a>
                                <!-- Botão para preencher/repreencher template -->
                                @if(str_contains($proposicao->ementa ?? '', 'a ser definid') || str_contains($proposicao->ementa ?? '', 'em elaboração') || str_contains($proposicao->ementa ?? '', 'serem definidos') || str_contains($proposicao->ementa ?? '', 'definidos') || empty($proposicao->ementa))
                                    <a href="{{ route('proposicoes.preencher-modelo', ['proposicao' => $proposicao->id, 'modeloId' => $proposicao->template_id]) }}" class="btn btn-outline-info">
                                        <i class="fas fa-form me-2"></i>Preencher Campos do Template
                                    </a>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Complete os campos para gerar uma ementa específica
                                    </small>
                                @endif
                            @else
                                <a href="{{ route('proposicoes.onlyoffice.editor-parlamentar', $proposicao->id) }}" class="btn btn-primary">
                                    <i class="fas fa-file-word me-2"></i>Editar Proposição no OnlyOffice
                                </a>
                            @endif
                            @if($podeEnviarLegislativo)
                                <button class="btn btn-success" onclick="enviarParaLegislativo()">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar para o Legislativo
                                </button>
                            @else
                                <button class="btn btn-success" disabled title="Proposição precisa ter ementa e conteúdo para ser enviada">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar para o Legislativo
                                </button>
                            @endif
                            <button class="btn btn-outline-danger" onclick="excluirProposicao()">
                                <i class="fas fa-trash me-2"></i>Excluir Rascunho
                            </button>
                        </div>
                    @elseif($proposicao->status === 'salvando')
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-save me-2"></i>
                            <strong>Salvando:</strong> Proposição está sendo preparada.
                        </div>
                        <div class="d-grid gap-2">
                            @if($podeEnviarLegislativo)
                                <button class="btn btn-success" onclick="enviarLegislativo()">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar para Legislativo
                                </button>
                            @else
                                <button class="btn btn-success" disabled title="Proposição precisa ter ementa e conteúdo para ser enviada">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar para Legislativo
                                </button>
                            @endif
                            <a href="{{ route('proposicoes.onlyoffice.editor-parlamentar', $proposicao->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-file-word me-2"></i>Continuar Editando no OnlyOffice
                            </a>
                            <button type="button" class="btn btn-outline-danger" onclick="excluirProposicao()">
                                <i class="fas fa-trash me-2"></i>Excluir Proposição
                            </button>
                        </div>
                    @elseif($proposicao->status === 'enviado_legislativo')
                        @if(Auth::user()->isLegislativo())
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fs-2 text-warning me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Aguardando Revisão</h6>
                                        <p class="mb-0 small">Esta proposição está aguardando revisão técnica do Legislativo.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('proposicoes.onlyoffice.editor', $proposicao->id) }}" class="btn btn-primary">
                                    <i class="fas fa-file-word me-2"></i>Revisar no Editor
                                </a>
                                @if(Auth::user()->isAssessorJuridico())
                                <a href="{{ route('proposicoes.revisar.show', $proposicao->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-clipboard-check me-2"></i>Análise Técnica
                                </a>
                                @endif
                                <button onclick="devolverParaParlamentar({{ $proposicao->id }})" class="btn btn-success">
                                    <i class="fas fa-arrow-left me-2"></i>Devolver para Parlamentar
                                </button>
                            </div>
                        @else
                            <div class="alert alert-info mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fs-2 text-info me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Em Análise Legislativa</h6>
                                        <p class="mb-0 small">Sua proposição está sendo analisada pelo Legislativo. Você será notificado quando houver atualizações.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-info btn-sm" onclick="consultarStatus()">
                                    <i class="fas fa-search me-2"></i>Consultar Status
                                </button>
                            </div>
                        @endif
                    @elseif(in_array($proposicao->status, ['aguardando_aprovacao_autor', 'devolvido_edicao']))
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="aprovarEdicoes()">
                                <i class="fas fa-check me-2"></i>
                                @if($proposicao->status === 'aguardando_aprovacao_autor')
                                    Aprovar Edições
                                @else
                                    Aceitar Edições
                                @endif
                            </button>
                            <a href="{{ route('proposicoes.onlyoffice.editor-parlamentar', $proposicao->id) }}" class="btn btn-outline-warning">
                                <i class="fas fa-file-word me-2"></i>Fazer Novas Edições no OnlyOffice
                            </a>
                            <button class="btn btn-outline-info btn-sm" onclick="verHistoricoEdicoes()">
                                <i class="fas fa-history me-2"></i>Ver Histórico
                            </button>
                        </div>
                    @elseif($proposicao->status === 'em_edicao')
                        <div class="d-grid gap-2">
                            <!-- Sempre usar OnlyOffice para edição -->
                            <a href="{{ route('proposicoes.onlyoffice.editor-parlamentar', $proposicao->id) }}" class="btn btn-primary">
                                <i class="fas fa-file-word me-2"></i>Continuar Edição no OnlyOffice
                            </a>

                            <!-- Botão para preencher template apenas se houver template_id -->
                            @if($proposicao->template_id && (str_contains($proposicao->ementa ?? '', 'a ser definid') || str_contains($proposicao->ementa ?? '', 'em elaboração') || str_contains($proposicao->ementa ?? '', 'serem definidos') || str_contains($proposicao->ementa ?? '', 'definidos') || empty($proposicao->ementa)))
                                <a href="{{ route('proposicoes.preencher-modelo', ['proposicao' => $proposicao->id, 'modeloId' => $proposicao->template_id]) }}" class="btn btn-outline-info">
                                    <i class="fas fa-form me-2"></i>Preencher Campos do Template
                                </a>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Complete os campos para gerar uma ementa específica
                                </small>
                            @endif
                            @if($podeEnviarLegislativo)
                                <button class="btn btn-success" onclick="enviarParaLegislativo()">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar para Legislativo
                                </button>
                            @else
                                <button class="btn btn-success" disabled title="Proposição precisa ter ementa e conteúdo para ser enviada">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar para Legislativo
                                </button>
                            @endif
                            <button class="btn btn-outline-danger btn-sm" onclick="excluirProposicao()">
                                <i class="fas fa-trash me-2"></i>Descartar Proposição
                            </button>
                        </div>
                    @elseif(in_array($proposicao->status, ['analise', 'em_revisao']))
                        @if(Auth::user()->isLegislativo())
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-edit fs-2 text-warning me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Em Revisão Técnica</h6>
                                        <p class="mb-0 small">Esta proposição está em processo de revisão pelo Legislativo.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('proposicoes.onlyoffice.editor', $proposicao->id) }}" class="btn btn-primary">
                                    <i class="fas fa-file-word me-2"></i>Continuar Revisão no Editor
                                </a>
                                @if(Auth::user()->isAssessorJuridico())
                                <a href="{{ route('proposicoes.revisar.show', $proposicao->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-clipboard-check me-2"></i>Análise Técnica
                                </a>
                                @endif
                                <button onclick="devolverParaParlamentar({{ $proposicao->id }})" class="btn btn-success">
                                    <i class="fas fa-arrow-left me-2"></i>Devolver para Parlamentar
                                </button>
                            </div>
                        @else
                            <div class="alert alert-primary mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-search-list fs-2 text-primary me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">
                                            @if($proposicao->status === 'em_revisao')
                                                Em Revisão Técnica
                                            @else
                                                Em Análise Legislativa
                                            @endif
                                        </h6>
                                        <p class="mb-0 small">O Legislativo está fazendo a análise técnica da sua proposição. Aguarde o retorno.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="consultarStatus()">
                                    <i class="fas fa-info-circle me-2"></i>Ver Detalhes
                                </button>
                            </div>
                        @endif
                    @elseif($proposicao->status === 'retornado')
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-undo me-2"></i>
                            <strong>Retornado:</strong> Proposição retornada para ajustes.
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="consultarStatus()">
                                <i class="fas fa-info-circle me-2"></i>Ver Detalhes
                            </button>
                        </div>
                    @elseif($proposicao->status === 'retornado_legislativo')
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-arrow-left-right me-2"></i>
                            <strong>Retornado do Legislativo:</strong> Proposição aprovada pelo Legislativo e pronta para assinatura digital.
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('proposicoes.assinar', $proposicao->id) }}" class="btn btn-success">
                                <i class="fas fa-signature me-2"></i>Assinar Documento
                            </a>
                            <button class="btn btn-outline-primary btn-sm" onclick="consultarStatus()">
                                <i class="fas fa-info-circle me-2"></i>Ver Detalhes
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="excluirProposicao()">
                                <i class="fas fa-trash me-2"></i>Excluir Documento
                            </button>
                        </div>
                    @elseif($proposicao->status === 'aprovado')
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Aprovado:</strong> Proposição aprovada pelo Legislativo.
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" disabled>
                                <i class="fas fa-thumbs-up me-2"></i>Aprovado
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="baixarDocumento()">
                                <i class="fas fa-download me-2"></i>Baixar Documento
                            </button>
                        </div>
                    @elseif($proposicao->status === 'assinado')
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-signature me-2"></i>
                            <strong>{{ $proposicao->numero_protocolo ? 'Assinado e Protocolado' : 'Assinado' }}:</strong> 
                            {{ $proposicao->numero_protocolo ? 'Protocolo: ' . $proposicao->numero_protocolo : 'Documento assinado digitalmente.' }}
                        </div>
                        <div class="d-grid gap-2">
                            @if(Auth::user()->isProtocolo() && !$proposicao->numero_protocolo)
                                <button class="btn btn-success" onclick="atribuirNumeroProtocolo()">
                                    <i class="fas fa-hashtag me-2"></i>Atribuir Número de Protocolo
                                </button>
                                <hr class="my-2">
                            @elseif(!Auth::user()->isProtocolo() && !$proposicao->numero_protocolo)
                                <button class="btn btn-primary" onclick="enviarParaProtocolo()">
                                    <i class="fas fa-file-signature me-2"></i>Enviar para Protocolo
                                </button>
                            @endif
                            <button class="btn btn-outline-primary btn-sm" onclick="baixarDocumento()">
                                <i class="fas fa-download me-2"></i>Baixar Documento Assinado
                            </button>
                        </div>
                    @elseif($proposicao->status === 'enviado_protocolo')
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-file-signature me-2"></i>
                            <strong>{{ $proposicao->numero_protocolo ? 'Protocolado' : 'Aguardando Protocolo' }}:</strong> 
                            {{ $proposicao->numero_protocolo ? 'Protocolo: ' . $proposicao->numero_protocolo : 'Documento enviado para protocolo oficial.' }}
                        </div>
                        <div class="d-grid gap-2">
                            @if(Auth::user()->isProtocolo())
                                @if(!$proposicao->numero_protocolo)
                                    <a href="{{ route('proposicoes.protocolar.show', $proposicao) }}" class="btn btn-primary">
                                        <i class="fas fa-file-signature me-2"></i>Protocolar
                                    </a>
                                    <button class="btn btn-outline-success" onclick="atribuirNumeroProtocolo()">
                                        <i class="fas fa-hashtag me-2"></i>Atribuir Número de Protocolo
                                    </button>
                                    <hr class="my-2">
                                @endif
                            @endif
                            <button class="btn btn-outline-info btn-sm" onclick="consultarProtocolo()">
                                <i class="fas fa-search me-2"></i>Consultar Protocolo
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="baixarDocumento()">
                                <i class="fas fa-download me-2"></i>Baixar Documento Final
                            </button>
                        </div>
                    @elseif($proposicao->status === 'devolvido_correcao')
                        @if(Auth::user()->isLegislativo())
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle fs-2 text-warning me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Devolvido para Correção</h6>
                                        <p class="mb-0 small">Este documento foi devolvido pelo parlamentar e requer correções.</p>
                                    </div>
                                </div>
                            </div>
                            @if($proposicao->observacoes_retorno)
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading">
                                    <i class="fas fa-comment-dots me-2"></i>
                                    Observações do Parlamentar
                                </h6>
                                <p class="mb-0">{{ $proposicao->observacoes_retorno }}</p>
                                @if($proposicao->data_retorno_legislativo)
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $proposicao->data_retorno_legislativo->format('d/m/Y H:i') }}
                                </small>
                                @endif
                            </div>
                            @endif
                            <div class="d-grid gap-2">
                                <a href="{{ route('proposicoes.onlyoffice.editor', $proposicao->id) }}" class="btn btn-primary">
                                    <i class="fas fa-file-word me-2"></i>Fazer Correções no Editor
                                </a>
                                <button onclick="retornarParaParlamentar({{ $proposicao->id }})" class="btn btn-success">
                                    <i class="fas fa-arrow-right me-2"></i>Retornar para Parlamentar
                                </button>
                                @if(Auth::user()->isAssessorJuridico())
                                <a href="{{ route('proposicoes.revisar.show', $proposicao->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-clipboard-check me-2"></i>Análise Técnica
                                </a>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-undo fs-2 text-warning me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Devolvido para Correção</h6>
                                        <p class="mb-0 small">Você devolveu este documento ao Legislativo solicitando correções.</p>
                                    </div>
                                </div>
                            </div>
                            @if($proposicao->observacoes_retorno)
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading">
                                    <i class="fas fa-comment-dots me-2"></i>
                                    Suas Observações
                                </h6>
                                <p class="mb-0">{{ $proposicao->observacoes_retorno }}</p>
                                @if($proposicao->data_retorno_legislativo)
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $proposicao->data_retorno_legislativo->format('d/m/Y H:i') }}
                                </small>
                                @endif
                            </div>
                            @endif
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-info btn-sm" onclick="consultarStatus()">
                                    <i class="fas fa-search me-2"></i>Acompanhar Status
                                </button>
                            </div>
                        @endif
                    @elseif($proposicao->status === 'protocolado')
                        <div class="alert alert-success mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-signature fs-2 text-success me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Proposição Protocolada</h6>
                                    <p class="mb-0 small">
                                        @if($proposicao->numero_protocolo)
                                            Protocolo: {{ $proposicao->numero_protocolo }}
                                        @else
                                            Proposição protocolada com sucesso.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            @if(Auth::user()->isParlamentar())
                                <a href="{{ route('proposicoes.serve-pdf', $proposicao->id) }}" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-file-pdf me-2"></i>Baixar PDF
                                </a>
                            @endif
                            <button class="btn btn-outline-info btn-sm" onclick="consultarProtocolo()">
                                <i class="fas fa-search me-2"></i>Consultar Protocolo
                            </button>
                        </div>
                    @elseif($proposicao->status === 'aprovado_assinatura')
                        <div class="alert alert-warning mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-signature fs-2 text-warning me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Pronto para Assinatura</h6>
                                    <p class="mb-0 small">Sua proposição foi aprovada pelo Legislativo e está pronta para assinatura digital.</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('proposicoes.assinar', $proposicao->id) }}" class="btn btn-success">
                                <i class="fas fa-signature me-2"></i>Assinar Documento
                            </a>
                            @if($proposicao->arquivo_pdf_path)
                            <a href="{{ route('proposicoes.serve-pdf', $proposicao) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-file-pdf me-2"></i>Visualizar PDF
                            </a>
                            @endif
                            <button class="btn btn-outline-info btn-sm" onclick="consultarStatus()">
                                <i class="fas fa-info-circle me-2"></i>Ver Detalhes
                            </button>
                            <button class="btn btn-outline-warning btn-sm" onclick="devolverParaLegislativo()">
                                <i class="fas fa-arrow-left me-2"></i>Devolver para Legislativo
                            </button>
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <i class="fas fa-question-circle me-2"></i>
                            Status: {{ ucfirst($proposicao->status) }}
                        </div>
                    @endif

                    <hr>

                </div>
            </div>

            <!-- Histórico/Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ki-duotone ki-time fs-3 text-primary me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Histórico da Proposição
                    </h6>
                </div>
                <div class="card-body">
                    <!--begin::Timeline-->
                    <div class="timeline-label">
                        <!--begin::Timeline item-->
                        <div class="timeline-item">
                            <!--begin::Timeline line-->
                            <div class="timeline-line w-40px"></div>
                            <!--end::Timeline line-->

                            <!--begin::Timeline icon-->
                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                <div class="symbol-label bg-light-success">
                                    <i class="ki-duotone ki-plus fs-2 text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </div>
                            </div>
                            <!--end::Timeline icon-->

                            <!--begin::Timeline content-->
                            <div class="timeline-content mb-10 mt-n2">
                                <!--begin::Timeline heading-->
                                <div class="overflow-auto pe-3">
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-semibold mb-2">Proposição Criada</div>
                                    <!--end::Title-->

                                    <!--begin::Description-->
                                    <div class="d-flex align-items-center mt-1 fs-6">
                                        <!--begin::Info-->
                                        <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->created_at ?? now())) }}</div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <!--end::Timeline heading-->

                                <!--begin::Timeline details-->
                                <div class="overflow-auto pb-5">
                                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <div class="fs-6 text-gray-700">
                                                    <span class="text-primary">{{ $proposicao->autor->name ?? 'Sistema' }}</span> 
                                                    criou esta proposição do tipo <strong>{{ strtoupper($proposicao->tipo) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Timeline details-->
                            </div>
                            <!--end::Timeline content-->
                        </div>
                        <!--end::Timeline item-->

                        @if($proposicao->status === 'em_edicao')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-pencil fs-2 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Em Edição</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        Proposição está sendo editada e ainda não foi enviada para análise
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @elseif(in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'analise', 'aprovado_assinatura']))
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-send fs-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Enviada para Análise</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        Proposição enviada para análise legislativa
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif

                        @if(in_array($proposicao->status, ['aguardando_aprovacao_autor', 'devolvido_edicao']))
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-arrow-left fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">
                                            @if($proposicao->status === 'aguardando_aprovacao_autor')
                                                Aguardando Aprovação do Autor
                                            @else
                                                Devolvido para Edição
                                            @endif
                                        </div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->data_retorno_legislativo ?? $proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        @if($proposicao->status === 'aguardando_aprovacao_autor')
                                                            Proposição editada pelo Legislativo e aguarda aprovação do autor
                                                        @else
                                                            Proposição devolvida pelo Legislativo para correções
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif

                        @if($proposicao->status === 'aprovada')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-check fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Proposição Aprovada</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        <i class="ki-duotone ki-check-circle fs-4 text-success me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Proposição aprovada pelo legislativo
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif

                        @if($proposicao->status === 'rejeitada')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-danger">
                                        <i class="ki-duotone ki-cross fs-2 text-danger">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Proposição Rejeitada</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        <i class="ki-duotone ki-information fs-4 text-danger me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Proposição rejeitada pelo legislativo
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif

                        @if($proposicao->status === 'aprovado_assinatura')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-document fs-2 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Aprovado para Assinatura</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ $proposicao->revisado_em ? date('d/m/Y H:i', strtotime($proposicao->revisado_em)) : date('d/m/Y H:i', strtotime($proposicao->updated_at)) }}</div>
                                            <!--end::Info-->

                                            <!--begin::User-->
                                            @if($proposicao->revisor)
                                            <div class="text-muted me-2 fs-7">
                                                <i class="ki-duotone ki-profile-circle fs-6 text-muted me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>{{ $proposicao->revisor->name }}
                                            </div>
                                            @endif
                                            <!--end::User-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        Proposição aprovada pelo Legislativo e liberada para assinatura digital pelo parlamentar
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif

                        @if($proposicao->status === 'assinado')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-success">
                                        <i class="fas fa-signature fs-2 text-success"></i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Documento Assinado</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        <i class="fas fa-check-circle fs-4 text-success me-2"></i>
                                                        Documento assinado digitalmente e pronto para protocolo
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif

                        @if($proposicao->status === 'enviado_protocolo')
                            <!--begin::Timeline item-->
                            <div class="timeline-item">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-40px"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                    <div class="symbol-label bg-light-info">
                                        <i class="fas fa-paper-plane fs-2 text-info"></i>
                                    </div>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content mb-10 mt-n2">
                                    <!--begin::Timeline heading-->
                                    <div class="overflow-auto pe-3">
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-semibold mb-2">Enviado para Protocolo</div>
                                        <!--end::Title-->

                                        <!--begin::Description-->
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <!--begin::Info-->
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->updated_at ?? now())) }}</div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Timeline heading-->

                                    <!--begin::Timeline details-->
                                    <div class="overflow-auto pb-5">
                                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">
                                                        <i class="fas fa-check-circle fs-4 text-info me-2"></i>
                                                        Documento enviado para protocolo oficial e tramitação
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Timeline details-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->
                        @endif
                    </div>
                    <!--end::Timeline-->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal - Consultar Status -->
<div class="modal fade" id="modalConsultarStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">
                    <i class="fas fa-search me-2 text-primary"></i>
                    Status da Proposição #{{ $proposicao->id }}
                </h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17" style="max-height: 70vh; overflow-y: auto;">
                <!-- Status Atual -->
                <div class="card mb-6">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-50px me-4">
                                <div class="symbol-label bg-light-primary">
                                    <i class="fas fa-info-circle fs-2 text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="text-gray-900 fw-bold mb-1">Status Atual</h4>
                                <span class="badge badge-light-{{ $proposicao->status === 'enviado_legislativo' ? 'info' : ($proposicao->status === 'aprovado' ? 'success' : 'warning') }} fs-6">
                                    {{ ucfirst(str_replace('_', ' ', $proposicao->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-gray-700">
                            @switch($proposicao->status)
                                @case('rascunho')
                                    A proposição está em elaboração e ainda não foi enviada.
                                    @break
                                @case('salvando')
                                    A proposição está sendo preparada para envio.
                                    @break
                                @case('enviado_legislativo')
                                    A proposição foi enviada para o Legislativo e está aguardando análise inicial.
                                    @break
                                @case('analise')
                                    O Legislativo está analisando a proposição e verificando sua conformidade.
                                    @break
                                @case('retornado')
                                    A proposição foi retornada para ajustes pelo autor.
                                    @break
                                @case('aprovado')
                                    A proposição foi aprovada e está pronta para tramitação.
                                    @break
                                @default
                                    Status personalizado: {{ $proposicao->status }}
                            @endswitch
                        </div>
                    </div>
                </div>

                <!-- Timeline de Status -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-history me-2"></i>
                            Fluxo de Tramitação
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline timeline-border-dashed">
                            <!-- Rascunho -->
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                    <div class="symbol-label bg-light-{{ in_array($proposicao->status, ['rascunho', 'salvando', 'enviado_legislativo', 'analise', 'retornado', 'aprovado']) ? 'success' : 'secondary' }}">
                                        <i class="fas fa-{{ in_array($proposicao->status, ['rascunho', 'salvando', 'enviado_legislativo', 'analise', 'retornado', 'aprovado']) ? 'check' : 'circle' }} fs-2 text-{{ in_array($proposicao->status, ['rascunho', 'salvando', 'enviado_legislativo', 'analise', 'retornado', 'aprovado']) ? 'success' : 'secondary' }}"></i>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="pe-3 mb-5">
                                        <div class="fs-5 fw-semibold mb-2">1. Criação da Proposição</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <div class="text-muted me-2 fs-7">{{ date('d/m/Y H:i', strtotime($proposicao->created_at ?? now())) }}</div>
                                        </div>
                                        <div class="text-gray-700 fw-normal">Proposição criada como rascunho pelo autor.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enviado ao Legislativo -->
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                    <div class="symbol-label bg-light-{{ in_array($proposicao->status, ['enviado_legislativo', 'analise', 'retornado', 'aprovado']) ? 'info' : 'secondary' }}">
                                        <i class="fas fa-{{ in_array($proposicao->status, ['enviado_legislativo', 'analise', 'retornado', 'aprovado']) ? 'paper-plane' : 'circle' }} fs-2 text-{{ in_array($proposicao->status, ['enviado_legislativo', 'analise', 'retornado', 'aprovado']) ? 'info' : 'secondary' }}"></i>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="pe-3 mb-5">
                                        <div class="fs-5 fw-semibold mb-2">2. Envio ao Legislativo</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <div class="text-muted me-2 fs-7">
                                                @if(in_array($proposicao->status, ['enviado_legislativo', 'analise', 'retornado', 'aprovado']))
                                                    {{ date('d/m/Y H:i') }}
                                                @else
                                                    Pendente
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-gray-700 fw-normal">
                                            @if(in_array($proposicao->status, ['enviado_legislativo', 'analise', 'retornado', 'aprovado']))
                                                Proposição enviada para análise do Legislativo.
                                            @else
                                                Aguardando envio da proposição.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Em Análise -->
                            <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                    <div class="symbol-label bg-light-{{ in_array($proposicao->status, ['analise', 'retornado', 'aprovado']) ? 'primary' : 'secondary' }}">
                                        <i class="fas fa-{{ in_array($proposicao->status, ['analise', 'retornado', 'aprovado']) ? 'search' : 'circle' }} fs-2 text-{{ in_array($proposicao->status, ['analise', 'retornado', 'aprovado']) ? 'primary' : 'secondary' }}"></i>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="pe-3 mb-5">
                                        <div class="fs-5 fw-semibold mb-2">3. Análise Técnica</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <div class="text-muted me-2 fs-7">
                                                @if(in_array($proposicao->status, ['analise', 'retornado', 'aprovado']))
                                                    {{ date('d/m/Y H:i') }}
                                                @else
                                                    Pendente
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-gray-700 fw-normal">
                                            @if($proposicao->status === 'analise')
                                                Proposição está sendo analisada pela equipe técnica.
                                            @elseif(in_array($proposicao->status, ['retornado', 'aprovado']))
                                                Análise técnica concluída.
                                            @else
                                                Aguardando início da análise técnica.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resultado -->
                            <div class="timeline-item">
                                <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                                    <div class="symbol-label bg-light-{{ $proposicao->status === 'aprovado' ? 'success' : ($proposicao->status === 'retornado' ? 'warning' : 'secondary') }}">
                                        <i class="fas fa-{{ $proposicao->status === 'aprovado' ? 'check-circle' : ($proposicao->status === 'retornado' ? 'undo' : 'circle') }} fs-2 text-{{ $proposicao->status === 'aprovado' ? 'success' : ($proposicao->status === 'retornado' ? 'warning' : 'secondary') }}"></i>
                                    </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                    <div class="pe-3 mb-5">
                                        <div class="fs-5 fw-semibold mb-2">4. Resultado da Análise</div>
                                        <div class="d-flex align-items-center mt-1 fs-6">
                                            <div class="text-muted me-2 fs-7">
                                                @if(in_array($proposicao->status, ['aprovado', 'retornado']))
                                                    {{ date('d/m/Y H:i') }}
                                                @else
                                                    Pendente
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-gray-700 fw-normal">
                                            @if($proposicao->status === 'aprovado')
                                                Proposição aprovada e pronta para tramitação.
                                            @elseif($proposicao->status === 'retornado')
                                                Proposição retornada para ajustes.
                                            @else
                                                Aguardando conclusão da análise.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="atualizarStatus()">
                    <i class="fas fa-sync-alt me-2"></i>Atualizar Status
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function excluirProposicao() {
    Swal.fire({
        title: 'Confirmar Exclusão',
        html: `Tem certeza que deseja excluir este rascunho?<br><strong>Esta ação não pode ser desfeita.</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, Excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f1416c',
        cancelButtonColor: '#7e8299',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-light'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Excluindo...',
                text: 'Aguarde enquanto a proposição é removida.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Fazer requisição DELETE
            fetch(`/proposicoes/{{ $proposicao->id }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        // Redirecionar para a lista de proposições
                        window.location.href = '{{ route("proposicoes.minhas-proposicoes") }}';
                    });
                } else {
                    Swal.fire({
                        title: 'Erro',
                        text: data.message || 'Erro ao excluir proposição.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                Swal.fire({
                    title: 'Erro',
                    text: 'Erro de conexão. Tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });
        }
    });
}

function imprimirProposicao() {
    window.print();
}

function exportarPDF() {
    // TODO: Implementar exportação PDF
    toastr.info('Funcionalidade de exportação em desenvolvimento...');
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>
@endpush

@push('styles')
<style>
.documento-content {
    font-family: 'Times New Roman', serif;
    font-size: 14px;
    line-height: 1.6;
    text-align: justify;
    background: white;
    padding: 30px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

/* Timeline styles following Keen UI template patterns */
.timeline-label {
    position: relative;
}

.timeline-line {
    position: absolute;
    left: 20px;
    top: 40px;
    bottom: -20px;
    border-left: 1px dashed #E1E3EA;
}

.timeline-item:last-child .timeline-line {
    display: none;
}

.timeline-icon {
    position: relative;
    z-index: 1;
}

.timeline-content {
    margin-left: 60px;
}

.notice {
    border-radius: 0.475rem;
}

.badge {
    font-size: 0.8rem;
    padding: 0.375rem 0.75rem;
}

/* Print styles */
@media print {
    .sidebar, .btn, .card-header, .timeline {
        display: none !important;
    }
    
    .documento-content {
        border: none;
        padding: 0;
        box-shadow: none;
    }
    
    body.printing .container-fluid {
        margin: 0;
        padding: 0;
    }
    
    body.printing .card {
        border: none;
        box-shadow: none;
    }
}
</style>
@endpush

@push('scripts')
<script>
function enviarLegislativo() {
    if (confirm('Tem certeza que deseja enviar esta proposição para o Legislativo?')) {
        // Simular envio - aqui você implementaria a lógica real
        toastr.success('Proposição enviada para o Legislativo!');
        setTimeout(() => {
            location.reload();
        }, 1500);
    }
}

function enviarParaLegislativo() {
    Swal.fire({
        title: 'Enviar para o Legislativo?',
        html: `<div class="text-center">
                <i class="fas fa-paper-plane text-primary fa-4x mb-3"></i>
                <p class="mb-3">Sua proposição será enviada para análise técnica do Legislativo.</p>
                <div class="text-start small">
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> O Legislativo revisará o conteúdo</p>
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> Você será notificado sobre alterações</p>
                    <p class="mb-0"><i class="fas fa-check-circle text-success me-1"></i> Após aprovação, poderá assinar</p>
                </div>
               </div>`,
        width: '450px',
        icon: null,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i>Enviar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-secondary'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Enviando Proposição...',
                html: '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2 mb-0">Aguarde enquanto sua proposição é enviada para o Legislativo...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `{{ route('proposicoes.enviar-legislativo', $proposicao) }}`,
                method: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Enviado com Sucesso!',
                            html: `<div class="text-center">
                                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                                    <p>Sua proposição foi enviada para análise do Legislativo.</p>
                                    <p class="text-muted small">Você será notificado sobre o andamento.</p>
                                   </div>`,
                            icon: null,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.href = '{{ route("proposicoes.minhas-proposicoes") }}';
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message || 'Erro ao enviar proposição',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Erro:', xhr);
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    
                    let message = 'Erro ao enviar proposição. Tente novamente.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        message = 'Você não tem permissão para enviar esta proposição.';
                    } else if (xhr.status === 400) {
                        message = 'Dados inválidos. Verifique se a proposição tem ementa e conteúdo.';
                    } else if (xhr.status === 404) {
                        message = 'Proposição não encontrada.';
                    }
                    
                    Swal.fire({
                        title: 'Erro!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function aprovarEdicoes() {
    Swal.fire({
        title: 'Aprovar Edições do Legislativo?',
        text: 'Tem certeza que deseja aprovar as edições feitas pelo Legislativo? A proposição ficará pronta para assinatura.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, Aprovar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#50cd89',
        cancelButtonColor: '#7239ea',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ route('proposicoes.aprovar-edicoes-legislativo', $proposicao) }}`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('button[onclick="aprovarEdicoes()"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Aprovando...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Aprovado!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#50cd89'
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message || 'Erro ao aprovar edições',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                        $('button[onclick="aprovarEdicoes()"]').prop('disabled', false).html('<i class="fas fa-check me-2"></i>Aprovar Edições');
                    }
                },
                error: function(xhr) {
                    console.error('Erro:', xhr);
                    let message = 'Erro ao aprovar edições';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Erro!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                    $('button[onclick="aprovarEdicoes()"]').prop('disabled', false).html('<i class="fas fa-check me-2"></i>Aprovar Edições');
                }
            });
        }
    });
}

function verHistoricoEdicoes() {
    toastr.info('Funcionalidade de histórico em desenvolvimento');
}

function consultarStatus() {
    // Abrir modal com status detalhado
    $('#modalConsultarStatus').modal('show');
}

function atualizarStatus() {
    const proposicaoId = {{ $proposicao->id }};
    
    // Desabilitar botão e mostrar loading
    const btn = document.querySelector('#modalConsultarStatus .btn-primary');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Atualizando...';
    
    // Fazer requisição AJAX para obter dados atualizados
    fetch(`/proposicoes/${proposicaoId}/status`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Atualizar status atual no modal
            const statusBadge = document.querySelector('#modalConsultarStatus .badge');
            if (statusBadge) {
                statusBadge.textContent = data.status_formatado;
                statusBadge.className = `badge badge-light-${data.status_class} fs-6`;
            }
            
            // Atualizar descrição do status
            const statusDescription = document.querySelector('#modalConsultarStatus .text-gray-700');
            if (statusDescription) {
                statusDescription.textContent = data.status_descricao;
            }
            
            // Atualizar timeline se houver mudanças
            if (data.timeline_updated) {
                atualizarTimeline(data.timeline);
            }
            
            // Atualizar também o status na página principal
            const statusBadgeMain = document.querySelector('.proposicao-status .badge');
            if (statusBadgeMain) {
                statusBadgeMain.textContent = data.status_formatado;
                statusBadgeMain.className = `badge badge-${data.status_class}`;
            }
            
            toastr.success('Status atualizado com sucesso!');
        } else {
            toastr.error(data.message || 'Erro ao atualizar status');
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        toastr.error('Erro de conexão. Tente novamente.');
    })
    .finally(() => {
        // Reabilitar botão
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function atualizarTimeline(timelineData) {
    // Função para atualizar a timeline com novos dados
    const timeline = document.querySelector('#modalConsultarStatus .timeline');
    if (timeline && timelineData) {
        // Aqui você pode implementar a lógica para atualizar
        // os ícones e estados dos itens da timeline
        // baseado nos dados retornados do servidor
        console.log('Timeline atualizada:', timelineData);
    }
}

function verComentarios() {
    // Implementar visualização de comentários
    toastr.info('Carregando comentários do Legislativo...');
    // Aqui você abriria um modal ou redirecionaria para página de comentários
}

function baixarDocumento() {
    // Mostrar loading
    Swal.fire({
        title: 'Preparando Download...',
        html: '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2 mb-0">Preparando o documento para download...</p></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Fazer requisição para download
    fetch(`{{ route('proposicoes.serve-pdf', $proposicao) }}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erro ${response.status}: ${response.statusText}`);
        }
        
        // Verificar se é realmente um arquivo PDF
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/pdf')) {
            throw new Error('Formato de arquivo não suportado. Esperado PDF.');
        }
        
        return response.blob();
    })
    .then(blob => {
        if (blob.size === 0) {
            throw new Error('Arquivo vazio recebido');
        }
        
        // Nome do arquivo PDF assinado
        const filename = `proposicao_{{ $proposicao->id }}_assinada.pdf`;
        
        // Criar URL do blob e iniciar download
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        Swal.close();
        toastr.success('Download iniciado com sucesso!');
    })
    .catch(error => {
        console.error('Erro no download:', error);
        let errorMessage = 'Não foi possível baixar o documento. Tente novamente.';
        
        if (error.message.includes('404')) {
            errorMessage = 'Documento não encontrado. Pode não ter sido gerado ainda.';
        } else if (error.message.includes('403')) {
            errorMessage = 'Você não tem permissão para baixar este documento.';
        } else if (error.message.includes('500')) {
            errorMessage = 'Erro interno do servidor. Contate o administrador.';
        }
        
        Swal.fire({
            title: 'Erro no Download',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    });
}

function verHistorico() {
    // Implementar visualização completa do histórico
    toastr.info('Carregando histórico completo...');
    // Aqui você redirecionaria para página de histórico detalhado
}

function compartilhar() {
    // Implementar compartilhamento
    if (navigator.share) {
        navigator.share({
            title: 'Proposição #{{ $proposicao->id }}',
            text: '{{ $proposicao->ementa }}',
            url: window.location.href
        });
    } else {
        // Fallback para navegadores que não suportam Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
            toastr.success('Link copiado para a área de transferência!');
        });
    }
}

function excluirProposicao() {
    Swal.fire({
        title: 'Descartar Proposição?',
        html: `<div class="text-center">
                <i class="fas fa-exclamation-triangle text-warning fa-4x mb-3"></i>
                <p class="text-muted">Esta ação <strong class="text-danger">não pode ser desfeita!</strong></p>
                <div class="text-start small text-muted mt-3">
                    <p class="mb-1"><i class="fas fa-times-circle text-danger me-1"></i> Todo conteúdo será perdido</p>
                    <p class="mb-1"><i class="fas fa-times-circle text-danger me-1"></i> Arquivos serão removidos</p>
                    <p class="mb-0"><i class="fas fa-times-circle text-danger me-1"></i> Histórico será excluído</p>
                </div>
               </div>`,
        width: '400px',
        icon: null,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash me-1"></i>Descartar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        customClass: {
            popup: 'swal2-sm',
            confirmButton: 'btn btn-danger btn-sm',
            cancelButton: 'btn btn-secondary btn-sm'
        },
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Excluindo Proposição...',
                html: '<div class="text-center"><div class="spinner-border text-danger" role="status"><span class="visually-hidden">Carregando...</span></div></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fazer a requisição AJAX para excluir
            $.ajax({
                url: `{{ route('proposicoes.destroy', $proposicao) }}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Proposição Descartada!',
                        text: 'A proposição foi excluída com sucesso.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // Redirecionar para a lista de proposições
                        window.location.href = '{{ route("proposicoes.minhas-proposicoes") }}';
                    });
                },
                error: function(xhr) {
                    console.error('Erro ao excluir:', xhr);
                    let message = 'Erro ao excluir proposição. Tente novamente.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: 'Erro!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function voltarParaParlamentar() {
    Swal.fire({
        title: 'Confirmar Devolução',
        html: `Tem certeza que deseja devolver esta proposição para o Parlamentar?<br><br><strong>Após esta ação:</strong><ul style="text-align: left;"><li>O Legislativo não terá mais acesso à proposição</li><li>O Parlamentar poderá assinar o documento</li><li>O documento seguirá para o Protocolo</li></ul>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, Devolver',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-secondary'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Devolvendo...',
                text: 'Aguarde enquanto a proposição é devolvida para o Parlamentar.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Fazer requisição PUT
            fetch(`/proposicoes/{{ $proposicao->id }}/voltar-parlamentar`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        // Redirecionar para a página indicada ou padrão
                        window.location.href = data.redirect || '{{ route("proposicoes.legislativo.index") }}';
                    });
                } else {
                    Swal.fire({
                        title: 'Erro',
                        text: data.message || 'Erro ao devolver proposição.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                Swal.fire({
                    title: 'Erro',
                    text: 'Erro de conexão. Tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });
        }
    });
}

function enviarParaProtocolo() {
    Swal.fire({
        title: 'Enviar para Protocolo?',
        html: `<div class="text-center">
                <i class="fas fa-file-signature text-primary fa-4x mb-3"></i>
                <p class="mb-3">O documento assinado será enviado para protocolo oficial.</p>
                <div class="text-start small">
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> Documento será protocolado oficialmente</p>
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> Número de protocolo será gerado</p>
                    <p class="mb-0"><i class="fas fa-check-circle text-success me-1"></i> Tramitação oficial será iniciada</p>
                </div>
               </div>`,
        width: '450px',
        icon: null,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i>Enviar para Protocolo',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Enviando para Protocolo...',
                html: '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2 mb-0">Aguarde enquanto o documento é enviado para protocolo...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `{{ route('proposicoes.enviar-protocolo', $proposicao) }}`,
                method: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Enviado com Sucesso!',
                            html: `<div class="text-center">
                                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                                    <p>Documento enviado para protocolo com sucesso!</p>
                                    ${response.protocolo ? `<p class="text-muted">Protocolo: <strong>${response.protocolo}</strong></p>` : ''}
                                   </div>`,
                            icon: null,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message || 'Erro ao enviar para protocolo',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Erro:', xhr);
                    let message = 'Erro ao enviar para protocolo. Tente novamente.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        message = 'Você não tem permissão para enviar esta proposição para protocolo.';
                    } else if (xhr.status === 404) {
                        message = 'Proposição não encontrada.';
                    }
                    
                    Swal.fire({
                        title: 'Erro!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function consultarProtocolo() {
    Swal.fire({
        title: 'Consultar Protocolo',
        html: `<div class="text-start">
                <p class="mb-3">Esta proposição foi enviada para protocolo oficial.</p>
                <div class="alert alert-info">
                    <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Status do Protocolo</h6>
                    <p class="mb-1"><strong>Proposição:</strong> {{ $proposicao->tipo }} #{{ $proposicao->id }}</p>
                    <p class="mb-1"><strong>Autor:</strong> {{ $proposicao->autor->name }}</p>
                    <p class="mb-1"><strong>Data de Envio:</strong> {{ $proposicao->updated_at ? $proposicao->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                    <p class="mb-0"><strong>Status:</strong> {{ $proposicao->numero_protocolo ? 'Protocolado: ' . $proposicao->numero_protocolo : 'Aguardando processamento no protocolo' }}</p>
                </div>
                <p class="text-muted small">O número de protocolo será gerado pelo sistema de protocolo oficial da Câmara.</p>
               </div>`,
        width: '500px',
        icon: null,
        confirmButtonText: 'Fechar',
        confirmButtonColor: '#6c757d'
    });
}

function atribuirNumeroProtocolo() {
    Swal.fire({
        title: 'Atribuir Número de Protocolo',
        html: `<div class="text-start">
                <p class="mb-3">Escolha como atribuir o número de protocolo para esta proposição:</p>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="tipoNumero" id="numeroAutomatico" value="automatico" checked>
                    <label class="form-check-label" for="numeroAutomatico">
                        <strong>Número Automático</strong><br>
                        <small class="text-muted">Sistema gerará o próximo número disponível (Recomendado)</small>
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="tipoNumero" id="numeroManual" value="manual">
                    <label class="form-check-label" for="numeroManual">
                        <strong>Número Manual</strong><br>
                        <small class="text-muted">Inserir número específico</small>
                    </label>
                </div>
                <div id="campoNumeroManual" class="d-none">
                    <label for="numeroProtocoloManual" class="form-label">Número do Protocolo:</label>
                    <input type="text" class="form-control" id="numeroProtocoloManual" placeholder="Ex: {{ $proposicao->tipo }}/{{ date('Y') }}/0001">
                    <small class="text-muted">Formato: TIPO/ANO/SEQUENCIAL (ex: PL/{{ date('Y') }}/0001)</small>
                </div>
               </div>`,
        width: '500px',
        icon: null,
        showCancelButton: true,
        confirmButtonText: 'Atribuir Número',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-secondary'
        },
        didOpen: () => {
            // Mostrar/ocultar campo manual
            document.querySelectorAll('input[name="tipoNumero"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const campoManual = document.getElementById('campoNumeroManual');
                    if (this.value === 'manual') {
                        campoManual.classList.remove('d-none');
                    } else {
                        campoManual.classList.add('d-none');
                    }
                });
            });
        },
        preConfirm: () => {
            const tipoSelecionado = document.querySelector('input[name="tipoNumero"]:checked').value;
            let numeroManual = '';
            
            if (tipoSelecionado === 'manual') {
                numeroManual = document.getElementById('numeroProtocoloManual').value.trim();
                if (!numeroManual) {
                    Swal.showValidationMessage('Digite o número do protocolo');
                    return false;
                }
                // Validar formato
                const formatoValido = /^[A-Z]{2,3}\/\d{4}\/\d{4}$/.test(numeroManual);
                if (!formatoValido) {
                    Swal.showValidationMessage('Formato inválido. Use: TIPO/ANO/SEQUENCIAL (ex: PL/2025/0001)');
                    return false;
                }
            }
            
            return {
                tipo: tipoSelecionado,
                numero: numeroManual
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const dados = result.value;
            
            // Mostrar loading
            Swal.fire({
                title: 'Atribuindo Número...',
                html: '<div class="text-center"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2 mb-0">Processando atribuição do número de protocolo...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `{{ route('proposicoes.atribuir-numero-protocolo', $proposicao) }}`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    tipo_numeracao: dados.tipo,
                    numero_protocolo: dados.numero
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Número Atribuído com Sucesso!',
                            html: `<div class="text-center">
                                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                                    <p>Número de protocolo: <strong class="text-primary">${response.numero_protocolo}</strong></p>
                                    <p class="text-muted">Protocolo atribuído em ${response.data_protocolo}</p>
                                   </div>`,
                            icon: null,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message || 'Erro ao atribuir número de protocolo',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Erro:', xhr);
                    let message = 'Erro ao atribuir número de protocolo. Tente novamente.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Erros de validação
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        message = errors.join(', ');
                    } else if (xhr.status === 403) {
                        message = 'Você não tem permissão para atribuir números de protocolo.';
                    } else if (xhr.status === 404) {
                        message = 'Proposição não encontrada.';
                    }
                    
                    Swal.fire({
                        title: 'Erro!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function devolverParaParlamentar(proposicaoId) {
    Swal.fire({
        title: 'Devolver para o Parlamentar?',
        html: `<div class="text-center">
                <i class="fas fa-arrow-left text-success fa-4x mb-3"></i>
                <p class="mb-3">Esta ação converterá o documento para PDF e o enviará de volta ao Parlamentar para assinatura.</p>
                <div class="text-start small">
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> Documento será convertido para PDF</p>
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> Parlamentar poderá assinar</p>
                    <p class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-1"></i> O Legislativo não terá mais acesso</p>
                </div>
               </div>`,
        width: '450px',
        icon: null,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-arrow-left me-1"></i>Devolver',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-secondary'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Processando...',
                html: '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2 mb-0">Convertendo documento e enviando para o Parlamentar...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            // Fazer a requisição
            $.ajax({
                url: `/proposicoes/${proposicaoId}/voltar-parlamentar`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            html: `<div class="text-center">
                                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                                    <p>${response.message}</p>
                                   </div>`,
                            icon: null,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message || 'Erro ao devolver proposição',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Erro:', xhr);
                    let message = 'Erro ao devolver proposição. Tente novamente.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        message = 'Você não tem permissão para devolver esta proposição.';
                    } else if (xhr.status === 404) {
                        message = 'Proposição não encontrada.';
                    } else if (xhr.status === 400) {
                        message = xhr.responseJSON?.message || 'Esta proposição não pode ser devolvida no status atual.';
                    }
                    
                    Swal.fire({
                        title: 'Erro!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function retornarParaParlamentar(proposicaoId) {
    Swal.fire({
        title: 'Retornar para Parlamentar?',
        html: `<div class="text-center">
                <i class="fas fa-arrow-right text-success fa-4x mb-3"></i>
                <p class="mb-3">Esta ação finalizará as correções e enviará o documento de volta ao Parlamentar para assinatura.</p>
                <div class="text-start small">
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> Correções foram finalizadas</p>
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> Documento será convertido para PDF</p>
                    <p class="mb-1"><i class="fas fa-check-circle text-success me-1"></i> Parlamentar poderá assinar</p>
                    <p class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-1"></i> O Legislativo não terá mais acesso</p>
                </div>
               </div>`,
        width: '450px',
        icon: null,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-arrow-right me-1"></i>Retornar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-secondary'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Processando...',
                html: '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2 mb-0">Finalizando correções e enviando para o Parlamentar...</p></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            // Fazer a requisição
            $.ajax({
                url: `/proposicoes/${proposicaoId}/voltar-parlamentar`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            html: `<div class="text-center">
                                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                    <p class="mb-2">${response.message}</p>
                                    <p class="small text-muted">O documento foi enviado para o Parlamentar e está pronto para assinatura.</p>
                                   </div>`,
                            icon: null,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Redirecionar
                            window.location.href = response.redirect || '{{ route("proposicoes.legislativo.index") }}';
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: response.message || 'Erro ao retornar proposição.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Erro:', xhr);
                    let message = 'Erro ao retornar proposição. Tente novamente.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        message = 'Você não tem permissão para esta ação.';
                    } else if (xhr.status === 404) {
                        message = 'Proposição não encontrada.';
                    } else if (xhr.status === 400) {
                        message = xhr.responseJSON?.message || 'Esta proposição não pode ser retornada no status atual.';
                    }
                    
                    Swal.fire({
                        title: 'Erro!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

// =========================================================================
// ATUALIZAÇÃO AUTOMÁTICA AO RETORNAR DO EDITOR ONLYOFFICE
// =========================================================================

// Função para atualizar dinamicamente a ementa e conteúdo
function atualizarDadosProposicao(proposicaoId) {
    console.log('🔄 Buscando dados atualizados da proposição...');
    
    // Mostrar indicador de carregamento
    const ementaElement = document.querySelector('.card-body p:contains("Ementa:")');
    const conteudoCard = document.querySelector('.documento-content');
    
    // Fazer requisição AJAX para buscar dados atualizados
    $.ajax({
        url: `/api/proposicoes/${proposicaoId}/dados-atualizados`,
        method: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                console.log('✅ Dados atualizados recebidos:', response.data);
                
                // Atualizar Ementa usando o ID específico
                const ementaContainer = document.getElementById('ementa-container');
                if (ementaContainer) {
                    let ementaHtml = '';
                    if (response.data.ementa && response.data.ementa !== 'Ementa a ser definida') {
                        ementaHtml = response.data.ementa;
                    } else {
                        ementaHtml = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-muted me-2"></i>
                                <span class="text-muted">Ementa não informada</span>
                            </div>
                        `;
                    }
                    ementaContainer.innerHTML = ementaHtml;
                }
                
                // Atualizar Conteúdo usando o ID específico
                const conteudoContainer = document.getElementById('conteudo-container');
                if (conteudoContainer) {
                    let novoConteudo = '';
                    
                    if (response.data.conteudo_processado) {
                        // Se houver conteúdo processado do arquivo
                        novoConteudo = `
                            <div class="documento-content">
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Documento atualizado do OnlyOffice</strong>
                                </div>
                                <div class="p-3 border rounded bg-light">
                                    ${response.data.conteudo_processado}
                                </div>
                            </div>
                        `;
                    } else if (response.data.conteudo) {
                        // Se houver conteúdo normal
                        novoConteudo = `
                            <div class="documento-content">
                                ${response.data.conteudo}
                            </div>
                        `;
                    } else {
                        novoConteudo = `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Conteúdo em elaboração
                            </div>
                        `;
                    }
                    
                    conteudoContainer.innerHTML = novoConteudo;
                }
                
                // Atualizar última modificação se existir elemento
                const ultimaModElement = document.querySelector('.text-muted small:contains("Última modificação:")');
                if (ultimaModElement && response.data.ultima_modificacao) {
                    ultimaModElement.textContent = `Última modificação: ${response.data.ultima_modificacao}`;
                }
                
                // Mostrar toast de sucesso
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: 'Documento atualizado',
                        text: 'A ementa e conteúdo foram atualizados com sucesso'
                    });
                }
            }
        },
        error: function(xhr) {
            console.error('❌ Erro ao buscar dados atualizados:', xhr);
            
            // Em caso de erro, fazer reload completo como fallback
            setTimeout(() => {
                window.location.reload(true);
            }, 1000);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Verificar se voltamos do editor OnlyOffice
    const editorFechado = localStorage.getItem('onlyoffice_editor_fechado');
    const destinoEsperado = localStorage.getItem('onlyoffice_destino');
    const urlAtual = window.location.href.split('?')[0]; // Remove query parameters
    
    if (editorFechado === 'true' && destinoEsperado) {
        // Verificar se estamos na página de destino correta
        const destinoLimpo = destinoEsperado.split('?')[0]; // Remove query parameters
        
        if (urlAtual === destinoLimpo) {
            console.log('🔄 Retornando do editor OnlyOffice - atualizando dados...');
            
            // Limpar flags do localStorage
            localStorage.removeItem('onlyoffice_editor_fechado');
            localStorage.removeItem('onlyoffice_destino');
            
            // Extrair ID da proposição da URL
            const urlParts = urlAtual.split('/');
            const proposicaoId = urlParts[urlParts.length - 1];
            
            // Aguardar um pouco para garantir que o callback do OnlyOffice foi processado
            setTimeout(() => {
                atualizarDadosProposicao(proposicaoId);
            }, 2000);
        }
    }
    
    // Limpar parâmetros de refresh da URL sem recarregar a página
    if (window.location.href.includes('_refresh=')) {
        const urlLimpa = window.location.href.replace(/[?&]_refresh=\d+/, '');
        window.history.replaceState({}, document.title, urlLimpa);
    }
});

function devolverParaLegislativo() {
    console.log('Abrindo modal de devolução para o Legislativo');
    
    // Abordagem super simples: prompt nativo do browser
    const observacoes = prompt(
        "DEVOLVER PARA O LEGISLATIVO\n\n" +
        "Você está prestes a devolver esta proposição para o Legislativo com solicitação de alterações.\n\n" +
        "Observações (obrigatório):\n" +
        "Descreva as alterações ou correções necessárias..."
    );
    
    // Se cancelou ou não escreveu nada
    if (!observacoes || observacoes.trim() === '') {
        console.log('Devolução cancelada pelo usuário');
        return;
    }
    
    // Confirmação final
    const confirmacao = confirm(
        "CONFIRMAR DEVOLUÇÃO?\n\n" +
        "A proposição será devolvida para o Legislativo com suas observações.\n\n" +
        "Observações: " + observacoes.trim() + "\n\n" +
        "Confirma a devolução?"
    );
    
    if (!confirmacao) {
        console.log('Devolução cancelada na confirmação');
        return;
    }
    
    // Enviar para o servidor
    console.log('Enviando devolução para o servidor...');
    
    // Mostrar loading
    const loadingAlert = document.createElement('div');
    loadingAlert.style.cssText = `
        position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
        background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        z-index: 10000; text-align: center; min-width: 200px;
    `;
    loadingAlert.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    document.body.appendChild(loadingAlert);
    
    fetch(`/proposicoes/{{ $proposicao->id }}/devolver-legislativo`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            observacoes: observacoes.trim()
        })
    })
    .then(response => response.json())
    .then(data => {
        document.body.removeChild(loadingAlert);
        
        if (data.success) {
            alert('✅ Proposição devolvida com sucesso!\n\nO Legislativo foi notificado sobre suas solicitações.');
            window.location.reload();
        } else {
            alert('❌ Erro: ' + (data.message || 'Não foi possível devolver a proposição.'));
        }
    })
    .catch(error => {
        document.body.removeChild(loadingAlert);
        console.error('Erro na devolução:', error);
        alert('❌ Erro de comunicação. Tente novamente.');
    });
}
</script>
@endpush