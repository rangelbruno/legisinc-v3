<x-layouts.app title="Projetos de Lei">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Projetos de Lei
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Projetos</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                @if(isset($error))
                    <div class="alert alert-danger d-flex align-items-center mb-10">
                        <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-danger">Erro</h4>
                            <span>{{ $error }}</span>
                        </div>
                    </div>
                @endif
                
                <!-- Estatísticas -->
                <div class="row g-5 g-xl-8 mb-5">
                    <div class="col-xl-3">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $estatisticas['total'] ?? 0 }}</span>
                                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Total de Projetos</span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <div class="d-flex justify-content-between fw-bold fs-6 text-gray-400 w-100 mt-auto mb-2">
                                        <span>Este Ano</span>
                                        <span>{{ $estatisticas['este_ano'] ?? 0 }}</span>
                                    </div>
                                    <div class="h-8px mx-3 w-100 bg-light-success rounded">
                                        <div class="bg-success rounded h-8px" style="width: {{ ($estatisticas['total'] ?? 0) > 0 ? (($estatisticas['este_ano'] ?? 0) / $estatisticas['total']) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $estatisticas['em_tramitacao'] ?? 0 }}</span>
                                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Em Tramitação</span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end pe-0">
                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <div class="d-flex justify-content-between fw-bold fs-6 text-gray-400 w-100 mt-auto mb-2">
                                        <span>Urgentes</span>
                                        <span>{{ $estatisticas['urgentes'] ?? 0 }}</span>
                                    </div>
                                    <div class="h-8px mx-3 w-100 bg-light-warning rounded">
                                        <div class="bg-warning rounded h-8px" style="width: {{ ($estatisticas['em_tramitacao'] ?? 0) > 0 ? (($estatisticas['urgentes'] ?? 0) / $estatisticas['em_tramitacao']) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-50 mb-xl-10">
                            <div class="card-header pt-5">
                                <h3 class="card-title text-gray-800 fw-bold">Por Status</h3>
                            </div>
                            <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                                @forelse($estatisticas['por_status'] ?? [] as $status => $total)
                                    <div class="d-flex flex-column me-7 me-lg-16 pt-sm-3 pt-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bullet w-8px h-6px rounded-2 bg-primary me-3"></div>
                                            <div class="fs-6 fw-semibold text-gray-600">{{ ucfirst(str_replace('_', ' ', $status)) }}</div>
                                        </div>
                                        <div class="fw-bold fs-6 text-gray-800">{{ $total }}</div>
                                    </div>
                                @empty
                                    <div class="text-gray-500">Nenhum dado disponível</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-50 mb-xl-10">
                            <div class="card-header pt-5">
                                <h3 class="card-title text-gray-800 fw-bold">Por Tipo</h3>
                            </div>
                            <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                                @forelse($estatisticas['por_tipo'] ?? [] as $tipo => $total)
                                    <div class="d-flex flex-column me-7 me-lg-16 pt-sm-3 pt-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bullet w-8px h-6px rounded-2 bg-success me-3"></div>
                                            <div class="fs-6 fw-semibold text-gray-600">{{ ucfirst($tipo) }}</div>
                                        </div>
                                        <div class="fw-bold fs-6 text-gray-800">{{ $total }}</div>
                                    </div>
                                @empty
                                    <div class="text-gray-500">Nenhum dado disponível</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" data-kt-projeto-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Buscar projeto..." />
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-projeto-table-toolbar="base">
                                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="ki-duotone ki-filter fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Filtros
                                </button>
                                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filtros Avançados</div>
                                    </div>
                                    <div class="separator border-gray-200"></div>
                                    <form method="GET" action="{{ route('projetos.index') }}">
                                        <div class="px-7 py-5">
                                            <div class="mb-5">
                                                <label class="form-label fw-semibold">Tipo:</label>
                                                <select class="form-select form-select-solid" name="tipo">
                                                    <option value="">Todos</option>
                                                    @foreach($opcoes['tipos'] ?? [] as $key => $nome)
                                                        <option value="{{ $key }}" {{ (request('tipo') == $key) ? 'selected' : '' }}>{{ $nome }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-5">
                                                <label class="form-label fw-semibold">Status:</label>
                                                <select class="form-select form-select-solid" name="status">
                                                    <option value="">Todos</option>
                                                    @foreach($opcoes['status'] ?? [] as $key => $nome)
                                                        <option value="{{ $key }}" {{ (request('status') == $key) ? 'selected' : '' }}>{{ $nome }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-5">
                                                <label class="form-label fw-semibold">Urgência:</label>
                                                <select class="form-select form-select-solid" name="urgencia">
                                                    <option value="">Todas</option>
                                                    @foreach($opcoes['urgencias'] ?? [] as $key => $nome)
                                                        <option value="{{ $key }}" {{ (request('urgencia') == $key) ? 'selected' : '' }}>{{ $nome }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-5">
                                                <label class="form-label fw-semibold">Ano:</label>
                                                <input type="number" class="form-control form-control-solid" name="ano" value="{{ request('ano') }}" min="2020" max="{{ date('Y') + 5 }}" placeholder="Ex: {{ date('Y') }}" />
                                            </div>
                                            <div class="form-check mb-5">
                                                <input class="form-check-input" type="checkbox" name="urgentes" value="1" {{ request('urgentes') ? 'checked' : '' }} id="filtro_urgentes">
                                                <label class="form-check-label fw-semibold" for="filtro_urgentes">
                                                    Apenas urgentes
                                                </label>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2">Limpar</button>
                                                <button type="submit" class="btn btn-sm btn-primary">Aplicar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <a href="{{ route('projetos.create') }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    Novo Projeto
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table table-rounded table-striped border gy-7 gs-7" id="kt_table_projetos">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                        <th class="min-w-200px">Projeto</th>
                                        <th class="min-w-100px">Número</th>
                                        <th class="min-w-125px">Tipo</th>
                                        <th class="min-w-125px">Status</th>
                                        <th class="min-w-125px">Autor</th>
                                        <th class="min-w-125px">Comissão</th>
                                        <th class="min-w-100px">Urgência</th>
                                        <th class="min-w-125px">Criado em</th>
                                        <th class="text-end min-w-100px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($projetos as $projeto)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('projetos.show', $projeto->id) }}" class="text-gray-800 text-hover-primary mb-1 fs-6 fw-bold">{{ $projeto->titulo }}</a>
                                                <span class="text-muted fw-semibold d-block fs-7">{{ Str::limit($projeto->ementa, 80) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $projeto->numero_completo ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="badge badge-light-primary fw-bold">{{ $projeto->tipo_formatado ?? $projeto->tipo }}</div>
                                        </td>
                                        <td>
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
                                            <div class="badge badge-light-{{ $statusColor }} fw-bold">{{ $projeto->status_formatado ?? ucfirst($projeto->status) }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $projeto->autor->name ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $projeto->comissao->nome ?? '-' }}</div>
                                        </td>
                                        <td>
                                            @if($projeto->urgencia === 'urgente')
                                                <div class="badge badge-light-danger fw-bold">Urgente</div>
                                            @elseif($projeto->urgencia === 'prioritario')
                                                <div class="badge badge-light-warning fw-bold">Prioritário</div>
                                            @else
                                                <div class="badge badge-light-secondary fw-bold">Normal</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $projeto->created_at->format('d/m/Y') }}</div>
                                            <div class="text-muted fs-7">{{ $projeto->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                Ações
                                                <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('projetos.show', $projeto->id) }}" class="menu-link px-3">Ver</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('projetos.edit', $projeto->id) }}" class="menu-link px-3">Editar</a>
                                                </div>
                                                @if($projeto->podeEditarConteudo())
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('projetos.editor', $projeto->id) }}" class="menu-link px-3" target="_blank">Editor</a>
                                                </div>
                                                @endif
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('projetos.versoes', $projeto->id) }}" class="menu-link px-3">Versões</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('projetos.tramitacao', $projeto->id) }}" class="menu-link px-3">Tramitação</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('projetos.anexos', $projeto->id) }}" class="menu-link px-3">Anexos</a>
                                                </div>
                                                @if($projeto->status === 'rascunho')
                                                <div class="separator my-2"></div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-projetos-table-action="protocolar" data-projeto-id="{{ $projeto->id }}">
                                                        Protocolar
                                                    </a>
                                                </div>
                                                @endif
                                                @if(in_array($projeto->status, ['protocolado', 'em_tramitacao']))
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-projetos-table-action="encaminhar-comissao" data-projeto-id="{{ $projeto->id }}">
                                                        Encaminhar
                                                    </a>
                                                </div>
                                                @endif
                                                @if($projeto->isRascunho())
                                                <div class="separator my-2"></div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger" data-kt-projetos-table-action="delete" data-projeto-id="{{ $projeto->id }}">
                                                        Excluir
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-10">
                                            <div class="text-gray-500 fs-6">
                                                Nenhum projeto encontrado.
                                                <br>
                                                <a href="{{ route('projetos.create') }}" class="btn btn-sm btn-primary mt-3">
                                                    <i class="ki-duotone ki-plus fs-3"></i>
                                                    Criar primeiro projeto
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        @if($projetos->hasPages())
                        <div class="d-flex flex-stack flex-wrap pt-10">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Exibindo {{ $projetos->firstItem() }} a {{ $projetos->lastItem() }} de {{ $projetos->total() }} projetos
                            </div>
                            <ul class="pagination">
                                {{ $projetos->links() }}
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Protocolar projeto
            document.querySelectorAll('[data-kt-projetos-table-action="protocolar"]').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const projetoId = this.getAttribute('data-projeto-id');
                    
                    if (confirm('Tem certeza que deseja protocolar este projeto? Após protocolado, não poderá ser editado.')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/projetos/${projetoId}/protocolar`;
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        form.appendChild(csrfInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            // Excluir projeto
            document.querySelectorAll('[data-kt-projetos-table-action="delete"]').forEach(function(element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    const projetoId = this.getAttribute('data-projeto-id');
                    
                    if (confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita.')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/projetos/${projetoId}`;
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        form.appendChild(methodInput);
                        form.appendChild(csrfInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            // Busca em tempo real
            const searchInput = document.querySelector('[data-kt-projeto-table-filter="search"]');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value;
                        if (searchTerm.length >= 3 || searchTerm.length === 0) {
                            // Fazer busca via AJAX
                            fetch(`/projetos/buscar?termo=${encodeURIComponent(searchTerm)}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Atualizar tabela com resultados
                                        console.log('Resultados da busca:', data.projetos);
                                    }
                                });
                        }
                    }, 500);
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>