@extends('components.layouts.app')

@section('title', 'Visualizar Proposição')

@section('content')
<div class="container-fluid">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0 fw-bold">
                                <i class="fas fa-file-alt me-2"></i>
                                {{ strtoupper($proposicao->tipo ?? 'Proposição') }}
                                <span class="badge bg-white text-primary ms-2">#{{ $proposicao->id }}</span>
                            </h2>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            @php
                                $statusClasses = [
                                    'rascunho' => 'warning',
                                    'em_edicao' => 'warning',
                                    'enviado_legislativo' => 'secondary',
                                    'em_revisao' => 'primary',
                                    'aguardando_aprovacao_autor' => 'primary',
                                    'devolvido_edicao' => 'warning',
                                    'retornado_legislativo' => 'info',
                                    'aprovado' => 'success',
                                    'reprovado' => 'danger'
                                ];
                                $statusClass = $statusClasses[$proposicao->status] ?? 'secondary';
                                
                                $statusTexts = [
                                    'rascunho' => 'Rascunho',
                                    'em_edicao' => 'Em Edição',
                                    'enviado_legislativo' => 'Enviado ao Legislativo',
                                    'em_revisao' => 'Em Revisão',
                                    'aguardando_aprovacao_autor' => 'Aguardando Aprovação do Autor',
                                    'devolvido_edicao' => 'Devolvido para Edição',
                                    'retornado_legislativo' => 'Retornado do Legislativo',
                                    'aprovado' => 'Aprovado',
                                    'reprovado' => 'Reprovado'
                                ];
                                $statusText = $statusTexts[$proposicao->status] ?? 'Status Desconhecido';
                                
                                $statusIcons = [
                                    'rascunho' => 'fas fa-edit',
                                    'em_edicao' => 'fas fa-pencil-alt',
                                    'enviado_legislativo' => 'fas fa-paper-plane',
                                    'em_revisao' => 'fas fa-search',
                                    'aguardando_aprovacao_autor' => 'fas fa-user-clock',
                                    'devolvido_edicao' => 'fas fa-undo',
                                    'retornado_legislativo' => 'fas fa-reply',
                                    'aprovado' => 'fas fa-check-circle',
                                    'reprovado' => 'fas fa-times-circle'
                                ];
                                $statusIcon = $statusIcons[$proposicao->status] ?? 'fas fa-question-circle';
                            @endphp
                            <span class="badge fs-6 px-3 py-2 bg-{{ $statusClass }}">
                                <i class="{{ $statusIcon }} me-1"></i>
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Informações Básicas -->
                <div class="card-body border-bottom">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Autor</small>
                                    <strong>{{ $proposicao->autor->name ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-calendar text-info"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Criado em</small>
                                    <strong>{{ $proposicao->created_at ? $proposicao->created_at->format('d/m/Y') : 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-hashtag text-success"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Protocolo</small>
                                    <strong>{{ $proposicao->numero_protocolo ?? '[Aguardando]' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ementa -->
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-quote-left me-2"></i>
                        Ementa
                    </h5>
                    <p class="mb-0 fs-5 text-dark">
                        {{ $proposicao->ementa ?? 'Ementa não definida' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content and Actions Row -->
    <div class="row">
        <!-- Content Card -->
        <div class="col-lg-8">
            <div class="card card-hover h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-document me-2"></i>Conteúdo da Proposição</h5>
                    @if($proposicao->conteudo && strlen($proposicao->conteudo) > 500)
                        <button 
                            onclick="toggleContent()" 
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-expand" id="toggleIcon"></i>
                            <span id="toggleText">Mostrar Mais</span>
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="content-area border p-3 rounded bg-light" id="contentArea">
                        @if($proposicao->conteudo)
                            @if(strlen($proposicao->conteudo) > 500)
                                <div id="shortContent">{{ substr($proposicao->conteudo, 0, 500) }}...</div>
                                <div id="fullContent" style="display: none;">{{ $proposicao->conteudo }}</div>
                            @else
                                {{ $proposicao->conteudo }}
                            @endif
                        @else
                            Nenhum conteúdo disponível
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white text-center">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Ações Disponíveis</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        
                        @php
                            $user = auth()->user();
                            $userRole = $user ? $user->getRoleNames()->first() : 'guest';
                            $isOwner = $proposicao->autor_id === $user->id;
                        @endphp
                        
                        <!-- Botões para PARLAMENTAR ou dono da proposição -->
                        @if($isOwner || $userRole === 'PARLAMENTAR')
                            
                            <!-- OnlyOffice Editor (rascunho ou em edição) -->
                            @if(in_array($proposicao->status, ['rascunho', 'em_edicao']))
                                <a 
                                    href="{{ route('proposicoes.onlyoffice.editor-parlamentar', $proposicao->id) }}"
                                    class="btn btn-lg btn-primary btn-onlyoffice">
                                    <i class="fas fa-file-word me-2"></i>
                                    {{ $proposicao->conteudo ? 'Continuar Editando' : 'Adicionar Conteúdo' }}
                                </a>
                            @endif
                            
                            <!-- Enviar para Legislativo -->
                            @if(in_array($proposicao->status, ['rascunho', 'em_edicao', 'devolvido_edicao']) 
                                && $proposicao->ementa && $proposicao->conteudo)
                                <form method="POST" action="{{ route('proposicoes.enviar-legislativo', $proposicao->id) }}" 
                                      onsubmit="return confirm('Deseja enviar esta proposição para análise do Legislativo?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-lg btn-success w-100">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Enviar para o Legislativo
                                    </button>
                                </form>
                            @endif
                            
                            <!-- OnlyOffice Editor (devolvido) -->
                            @if($proposicao->status === 'devolvido_edicao')
                                <a 
                                    href="{{ route('proposicoes.onlyoffice.editor-parlamentar', $proposicao->id) }}"
                                    class="btn btn-lg btn-warning btn-onlyoffice">
                                    <i class="fas fa-edit me-2"></i>
                                    Revisar Proposição
                                </a>
                            @endif
                            
                        @endif
                        
                        <!-- Botões para LEGISLATIVO -->
                        @if($userRole === 'LEGISLATIVO')
                            
                            <!-- OnlyOffice Editor para Legislativo -->
                            @if(in_array($proposicao->status, ['enviado_legislativo', 'em_revisao']))
                                <a 
                                    href="{{ route('proposicoes.onlyoffice.editor', $proposicao->id) }}"
                                    class="btn btn-lg btn-info btn-onlyoffice">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Revisar no Editor
                                </a>
                            @endif
                            
                            <!-- Ações de Status -->
                            @if($proposicao->status === 'em_revisao')
                                <div class="mt-2">
                                    <form method="POST" action="{{ route('proposicoes.update-status', $proposicao->id) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="aprovado">
                                        <button type="submit" class="btn btn-success w-100 mb-2">
                                            <i class="fas fa-check me-2"></i>
                                            Aprovar Proposição
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('proposicoes.update-status', $proposicao->id) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="devolvido_edicao">
                                        <button type="submit" class="btn btn-warning w-100 mb-2">
                                            <i class="fas fa-undo me-2"></i>
                                            Devolver para Edição
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('proposicoes.update-status', $proposicao->id) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="reprovado">
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-times me-2"></i>
                                            Reprovar Proposição
                                        </button>
                                    </form>
                                </div>
                            @endif
                            
                        @endif
                        
                        <!-- Assinatura (disponível quando aprovado) -->
                        @if($proposicao->status === 'aprovado' && ($isOwner || $userRole === 'PARLAMENTAR'))
                            <a 
                                href="{{ route('proposicoes.assinar', $proposicao->id) }}"
                                class="btn btn-lg btn-success btn-assinatura"
                                target="_blank">
                                <i class="fas fa-signature me-2"></i>
                                Assinar Documento
                            </a>
                        @endif
                        
                        <!-- Visualizar PDF (quando tem arquivo) -->
                        @if($proposicao->arquivo_pdf_path)
                            <a 
                                href="{{ route('proposicoes.pdf', $proposicao->id) }}"
                                class="btn btn-outline-info"
                                target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>
                                Visualizar PDF
                            </a>
                        @endif
                        
                        <!-- Refresh Button -->
                        <hr class="my-3">
                        <a 
                            href="{{ route('proposicoes.show', $proposicao->id) }}" 
                            class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-sync me-1"></i>
                            Atualizar Página
                        </a>
                        
                    </div>
                </div>
            </div>
            
            <!-- Histórico de Tramitação -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light text-center">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Histórico de Tramitação
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="timeline-container p-3">
                        @php
                            $eventos = [];
                            
                            // Evento de criação
                            $eventos[] = [
                                'title' => 'Proposição Criada',
                                'description' => 'Por ' . ($proposicao->autor->name ?? 'N/A'),
                                'date' => $proposicao->created_at ? $proposicao->created_at->format('d/m/Y H:i') : '',
                                'icon' => 'fas fa-plus',
                                'color' => 'primary'
                            ];
                            
                            // Eventos baseados no status atual
                            $status = $proposicao->status;
                            
                            if (in_array($status, ['em_edicao', 'enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado', 'devolvido_edicao'])) {
                                $eventos[] = [
                                    'title' => 'Em Edição',
                                    'description' => 'Conteúdo sendo elaborado',
                                    'date' => $proposicao->updated_at ? $proposicao->updated_at->format('d/m/Y H:i') : '',
                                    'icon' => 'fas fa-pencil-alt',
                                    'color' => 'warning'
                                ];
                            }
                            
                            if (in_array($status, ['enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado'])) {
                                $eventos[] = [
                                    'title' => 'Enviado ao Legislativo',
                                    'description' => 'Para análise técnica',
                                    'date' => $proposicao->updated_at ? $proposicao->updated_at->format('d/m/Y H:i') : '',
                                    'icon' => 'fas fa-paper-plane',
                                    'color' => 'info'
                                ];
                            }
                            
                            if (in_array($status, ['em_revisao', 'aprovado', 'reprovado'])) {
                                $eventos[] = [
                                    'title' => 'Em Revisão',
                                    'description' => 'Análise do Legislativo',
                                    'date' => $proposicao->updated_at ? $proposicao->updated_at->format('d/m/Y H:i') : '',
                                    'icon' => 'fas fa-search',
                                    'color' => 'primary'
                                ];
                            }
                            
                            if ($status === 'devolvido_edicao') {
                                $eventos[] = [
                                    'title' => 'Devolvido para Edição',
                                    'description' => 'Necessita ajustes',
                                    'date' => $proposicao->updated_at ? $proposicao->updated_at->format('d/m/Y H:i') : '',
                                    'icon' => 'fas fa-undo',
                                    'color' => 'warning'
                                ];
                            }
                            
                            if ($status === 'aprovado') {
                                $eventos[] = [
                                    'title' => 'Aprovado',
                                    'description' => 'Proposição aprovada',
                                    'date' => $proposicao->updated_at ? $proposicao->updated_at->format('d/m/Y H:i') : '',
                                    'icon' => 'fas fa-check-circle',
                                    'color' => 'success'
                                ];
                                
                                if ($proposicao->numero_protocolo) {
                                    $eventos[] = [
                                        'title' => 'Protocolado',
                                        'description' => 'Nº ' . $proposicao->numero_protocolo,
                                        'date' => $proposicao->updated_at ? $proposicao->updated_at->format('d/m/Y H:i') : '',
                                        'icon' => 'fas fa-hashtag',
                                        'color' => 'success'
                                    ];
                                }
                            }
                            
                            if ($status === 'reprovado') {
                                $eventos[] = [
                                    'title' => 'Reprovado',
                                    'description' => 'Proposição não aprovada',
                                    'date' => $proposicao->updated_at ? $proposicao->updated_at->format('d/m/Y H:i') : '',
                                    'icon' => 'fas fa-times-circle',
                                    'color' => 'danger'
                                ];
                            }
                        @endphp
                        
                        @foreach($eventos as $index => $evento)
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-icon">
                                        <div class="badge rounded-circle p-2 bg-{{ $evento['color'] }}">
                                            <i class="{{ $evento['icon'] }} text-white"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content ms-3 flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="d-block">{{ $evento['title'] }}</strong>
                                                <small class="text-muted">{{ $evento['description'] }}</small>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $evento['date'] }}
                                        </small>
                                    </div>
                                </div>
                                @if($index < count($eventos) - 1)
                                    <div class="timeline-line"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos mantidos da versão anterior */
.btn-onlyoffice, .btn-assinatura {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    font-weight: 600;
    padding: 12px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-onlyoffice:hover, .btn-assinatura:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-onlyoffice {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-assinatura {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    border: none;
}

.card-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.content-area {
    min-height: 200px; 
    white-space: pre-wrap;
    line-height: 1.6;
    font-size: 1rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.card {
    border: none;
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
    padding: 1.25rem;
}

.timeline-container {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-line {
    position: absolute;
    left: 19px;
    top: 40px;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-icon .badge {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.text-dark {
    color: #212529!important;
}
</style>

<script>
// JavaScript simples para expandir/contrair conteúdo
function toggleContent() {
    const shortContent = document.getElementById('shortContent');
    const fullContent = document.getElementById('fullContent');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleText = document.getElementById('toggleText');
    
    if (fullContent.style.display === 'none') {
        shortContent.style.display = 'none';
        fullContent.style.display = 'block';
        toggleIcon.className = 'fas fa-compress';
        toggleText.textContent = 'Mostrar Menos';
    } else {
        shortContent.style.display = 'block';
        fullContent.style.display = 'none';
        toggleIcon.className = 'fas fa-expand';
        toggleText.textContent = 'Mostrar Mais';
    }
}
</script>
@endsection