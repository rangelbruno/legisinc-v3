<x-layouts.app title="Tramitação do Projeto">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Tramitação
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('projetos.index') }}" class="text-muted text-hover-primary">Projetos</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('projetos.show', $projeto->id) }}" class="text-muted text-hover-primary">{{ $projeto->numero_completo ?? 'Projeto' }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Tramitação</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('projetos.show', $projeto->id) }}" class="btn btn-light-primary btn-sm">
                        <i class="ki-duotone ki-arrow-left fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Voltar ao Projeto
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <div class="row g-5 g-xl-8">
                    <!-- Informações do Projeto -->
                    <div class="col-xl-4">
                        <div class="card card-flush mb-5">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Informações do Projeto</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Título</label>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $projeto->titulo }}</div>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Número</label>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $projeto->numero_completo ?? 'Não numerado' }}</div>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Status Atual</label>
                                    <div>
                                        @php
                                            $statusColor = match($projeto->status) {
                                                'rascunho' => 'secondary',
                                                'protocolado' => 'primary',
                                                'em_tramitacao' => 'warning',
                                                'na_comissao' => 'info',
                                                'aprovado' => 'success',
                                                'rejeitado' => 'danger',
                                                'arquivado' => 'dark',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <div class="badge badge-light-{{ $statusColor }} fw-bold fs-6 px-4 py-3">
                                            {{ $projeto->status_formatado ?? ucfirst($projeto->status) }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Autor</label>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $projeto->autor->name ?? 'N/A' }}</div>
                                </div>
                                
                                @if($projeto->comissao)
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Comissão Atual</label>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $projeto->comissao->nome }}</div>
                                </div>
                                @endif
                                
                                @if($projeto->relator)
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Relator</label>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $projeto->relator->name }}</div>
                                </div>
                                @endif
                                
                                @if($projeto->data_limite_tramitacao)
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Data Limite</label>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $projeto->data_limite_tramitacao->format('d/m/Y') }}</div>
                                    @php
                                        $diasRestantes = now()->diffInDays($projeto->data_limite_tramitacao, false);
                                    @endphp
                                    @if($diasRestantes < 0)
                                        <div class="text-danger fs-7 mt-1">Atrasado há {{ abs($diasRestantes) }} dias</div>
                                    @elseif($diasRestantes <= 7)
                                        <div class="text-warning fs-7 mt-1">{{ $diasRestantes }} dias restantes</div>
                                    @else
                                        <div class="text-success fs-7 mt-1">{{ $diasRestantes }} dias restantes</div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Estatísticas de Tramitação -->
                        <div class="card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Estatísticas</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Total de Etapas</span>
                                        <span class="fs-6 fw-bold text-gray-900">{{ $projeto->tramitacao->count() }}</span>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Concluídas</span>
                                        <span class="fs-6 fw-bold text-success">{{ $projeto->tramitacao->where('status', 'concluido')->count() }}</span>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Em Andamento</span>
                                        <span class="fs-6 fw-bold text-warning">{{ $projeto->tramitacao->where('status', 'em_andamento')->count() }}</span>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Pendentes</span>
                                        <span class="fs-6 fw-bold text-info">{{ $projeto->tramitacao->where('status', 'pendente')->count() }}</span>
                                    </div>
                                </div>
                                
                                @php
                                    $diasTramitacao = $projeto->tramitacao->sum('dias_tramitacao') ?: 
                                                    ($projeto->created_at ? now()->diffInDays($projeto->created_at) : 0);
                                @endphp
                                <div class="separator my-4"></div>
                                <div class="mb-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-6 fw-semibold text-gray-600">Dias em Tramitação</span>
                                        <span class="fs-6 fw-bold text-primary">{{ $diasTramitacao }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline de Tramitação -->
                    <div class="col-xl-8">
                        <div class="card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Histórico de Tramitação</h3>
                                </div>
                                <div class="card-toolbar">
                                    @if(in_array($projeto->status, ['protocolado', 'em_tramitacao']))
                                    <button type="button" class="btn btn-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modal_nova_tramitacao">
                                        <i class="ki-duotone ki-plus fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Nova Tramitação
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                @forelse($projeto->tramitacao->sortByDesc('created_at') as $tramitacao)
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-line w-40px"></div>
                                        
                                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                                            @switch($tramitacao->status)
                                                @case('concluido')
                                                    <div class="symbol-label bg-success">
                                                        <i class="ki-duotone ki-check fs-2 text-white">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                    @break
                                                @case('em_andamento')
                                                    <div class="symbol-label bg-warning">
                                                        <i class="ki-duotone ki-time fs-2 text-white">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                    @break
                                                @case('cancelado')
                                                    <div class="symbol-label bg-danger">
                                                        <i class="ki-duotone ki-cross fs-2 text-white">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                    @break
                                                @default
                                                    <div class="symbol-label bg-light-primary text-primary fw-bold">
                                                        {{ $tramitacao->ordem ?? substr($tramitacao->etapa, 0, 1) }}
                                                    </div>
                                            @endswitch
                                        </div>
                                        
                                        <div class="timeline-content mb-10 mt-n1">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="pe-3">
                                                    <div class="fs-5 fw-semibold mb-2">
                                                        {{ ucfirst($tramitacao->etapa) }} - {{ ucfirst($tramitacao->acao) }}
                                                        <div class="badge badge-light-{{ 
                                                            match($tramitacao->status) {
                                                                'concluido' => 'success',
                                                                'em_andamento' => 'warning',
                                                                'cancelado' => 'danger',
                                                                default => 'primary'
                                                            }
                                                        }} ms-2 fs-8">
                                                            {{ ucfirst(str_replace('_', ' ', $tramitacao->status)) }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-center mt-1 fs-6">
                                                        <div class="text-muted me-2 fs-7">
                                                            @if($tramitacao->data_inicio)
                                                                Iniciado: {{ $tramitacao->data_inicio->format('d/m/Y H:i') }}
                                                            @endif
                                                        </div>
                                                        
                                                        @if($tramitacao->data_fim)
                                                            <div class="text-muted me-2 fs-7">•</div>
                                                            <div class="text-muted me-2 fs-7">
                                                                Concluído: {{ $tramitacao->data_fim->format('d/m/Y H:i') }}
                                                            </div>
                                                        @endif
                                                        
                                                        @if($tramitacao->responsavel)
                                                            <div class="text-muted me-2 fs-7">•</div>
                                                            <div class="text-muted me-2 fs-7">
                                                                {{ $tramitacao->responsavel->name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                @if($tramitacao->status === 'em_andamento')
                                                <div class="ms-auto">
                                                    <button type="button" class="btn btn-light-success btn-sm"
                                                            onclick="concluirTramitacao({{ $tramitacao->id }})">
                                                        <i class="ki-duotone ki-check fs-3">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        Concluir
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            @if($tramitacao->comissao)
                                            <div class="text-gray-800 fw-normal fs-6 mb-2">
                                                <strong>Comissão:</strong> {{ $tramitacao->comissao->nome }}
                                            </div>
                                            @endif
                                            
                                            @if($tramitacao->orgao_destino)
                                            <div class="text-gray-800 fw-normal fs-6 mb-2">
                                                <strong>Órgão Destino:</strong> {{ $tramitacao->orgao_destino }}
                                            </div>
                                            @endif
                                            
                                            @if($tramitacao->observacoes)
                                            <div class="text-gray-800 fw-normal fs-6 mb-3">
                                                {{ $tramitacao->observacoes }}
                                            </div>
                                            @endif
                                            
                                            @if($tramitacao->despacho)
                                            <div class="bg-light-primary p-3 rounded mb-3">
                                                <div class="fs-7 fw-bold text-primary mb-1">Despacho:</div>
                                                <div class="fs-6 text-gray-800">{{ $tramitacao->despacho }}</div>
                                            </div>
                                            @endif
                                            
                                            <!-- Informações de Prazo -->
                                            <div class="d-flex align-items-center text-gray-600 fs-7">
                                                @if($tramitacao->prazo)
                                                <div class="me-5">
                                                    <i class="ki-duotone ki-calendar fs-6 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Prazo: {{ $tramitacao->prazo->format('d/m/Y') }}
                                                    @php
                                                        $diasRestantesPrazo = now()->diffInDays($tramitacao->prazo, false);
                                                    @endphp
                                                    @if($diasRestantesPrazo < 0 && $tramitacao->status !== 'concluido')
                                                        <span class="text-danger ms-1">(Atrasado)</span>
                                                    @elseif($diasRestantesPrazo <= 3 && $tramitacao->status !== 'concluido')
                                                        <span class="text-warning ms-1">(Urgente)</span>
                                                    @endif
                                                </div>
                                                @endif
                                                
                                                @if($tramitacao->dias_tramitacao)
                                                <div class="me-5">
                                                    <i class="ki-duotone ki-time fs-6 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    {{ $tramitacao->dias_tramitacao }} dias
                                                </div>
                                                @endif
                                                
                                                @if($tramitacao->urgente)
                                                <div class="me-5">
                                                    <span class="badge badge-light-danger fs-8">Urgente</span>
                                                </div>
                                                @endif
                                                
                                                <div class="me-5">
                                                    <i class="ki-duotone ki-time fs-6 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    {{ $tramitacao->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-10">
                                    <div class="text-gray-500 fs-6">
                                        Nenhuma tramitação registrada para este projeto.
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Tramitação -->
    <div class="modal fade" id="modal_nova_tramitacao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-750px">
            <div class="modal-content">
                <form id="form_nova_tramitacao">
                    @csrf
                    <div class="modal-header">
                        <h2>Nova Tramitação</h2>
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row g-5">
                            <div class="col-6">
                                <div class="fv-row mb-7">
                                    <label class="required fs-6 fw-semibold mb-2">Etapa</label>
                                    <select class="form-select form-select-solid" name="etapa" required>
                                        <option value="">Selecione a etapa</option>
                                        <option value="protocolo">Protocolo</option>
                                        <option value="distribuicao">Distribuição</option>
                                        <option value="relatoria">Relatoria</option>
                                        <option value="analise">Análise</option>
                                        <option value="votacao">Votação</option>
                                        <option value="aprovacao">Aprovação</option>
                                        <option value="rejeicao">Rejeição</option>
                                        <option value="arquivo">Arquivo</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-6">
                                <div class="fv-row mb-7">
                                    <label class="required fs-6 fw-semibold mb-2">Ação</label>
                                    <select class="form-select form-select-solid" name="acao" required>
                                        <option value="">Selecione a ação</option>
                                        <option value="criado">Criado</option>
                                        <option value="enviado">Enviado</option>
                                        <option value="recebido">Recebido</option>
                                        <option value="analisado">Analisado</option>
                                        <option value="aprovado">Aprovado</option>
                                        <option value="rejeitado">Rejeitado</option>
                                        <option value="arquivado">Arquivado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Responsável</label>
                            <select class="form-select form-select-solid" name="responsavel_id">
                                <option value="">Selecione o responsável</option>
                                <!-- Opcoes serão carregadas via JS ou backend -->
                            </select>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Comissão</label>
                            <select class="form-select form-select-solid" name="comissao_id">
                                <option value="">Selecione a comissão</option>
                                <!-- Opcoes serão carregadas via JS ou backend -->
                            </select>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Órgão Destino</label>
                            <input type="text" class="form-control form-control-solid" name="orgao_destino" placeholder="Nome do órgão de destino" />
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Observações</label>
                            <textarea class="form-control form-control-solid" name="observacoes" rows="3" placeholder="Observações sobre esta tramitação"></textarea>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Despacho</label>
                            <textarea class="form-control form-control-solid" name="despacho" rows="3" placeholder="Despacho oficial"></textarea>
                        </div>
                        
                        <div class="row g-5">
                            <div class="col-6">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold mb-2">Prazo</label>
                                    <input type="date" class="form-control form-control-solid" name="prazo" min="{{ date('Y-m-d') }}" />
                                </div>
                            </div>
                            
                            <div class="col-6">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold mb-2"></label>
                                    <div class="form-check form-check-custom form-check-solid mt-6">
                                        <input class="form-check-input" type="checkbox" name="urgente" value="1" id="tramitacao_urgente" />
                                        <label class="form-check-label fw-semibold" for="tramitacao_urgente">
                                            Tramitação urgente
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Tramitação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .timeline-item:last-child .timeline-line {
            display: none;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Submissão do formulário de nova tramitação
            document.getElementById('form_nova_tramitacao').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                
                fetch(`/projetos/{{ $projeto->id }}/tramitacao`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tramitação adicionada com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro ao adicionar tramitação: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    alert('Erro na conexão. Tente novamente.');
                    console.error('Erro:', error);
                });
            });
            
            // Carregar opções de responsáveis e comissões
            loadSelectOptions();
        });
        
        function concluirTramitacao(tramitacaoId) {
            if (!confirm('Tem certeza que deseja concluir esta tramitação?')) {
                return;
            }
            
            fetch(`/tramitacao/${tramitacaoId}/concluir`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Tramitação concluída com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao concluir tramitação: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                alert('Erro na conexão. Tente novamente.');
                console.error('Erro:', error);
            });
        }
        
        function loadSelectOptions() {
            // Carregar responsáveis
            fetch('/api/users/parlamentares')
                .then(response => response.json())
                .then(data => {
                    const select = document.querySelector('select[name="responsavel_id"]');
                    if (select && data.users) {
                        data.users.forEach(user => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = user.name;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.log('Erro ao carregar responsáveis:', error));
            
            // Carregar comissões
            fetch('/api/comissoes/ativas')
                .then(response => response.json())
                .then(data => {
                    const select = document.querySelector('select[name="comissao_id"]');
                    if (select && data.comissoes) {
                        data.comissoes.forEach(comissao => {
                            const option = document.createElement('option');
                            option.value = comissao.id;
                            option.textContent = comissao.nome;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.log('Erro ao carregar comissões:', error));
        }
    </script>
    @endpush
</x-layouts.app>