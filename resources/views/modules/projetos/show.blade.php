<x-layouts.app title="Projeto de Lei">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ $projeto->titulo }}
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
                        <li class="breadcrumb-item text-muted">{{ $projeto->numero_completo ?? 'Projeto' }}</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('projetos.index') }}" class="btn btn-light-primary btn-sm">
                        <i class="ki-duotone ki-arrow-left fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Voltar
                    </a>
                    
                    @if($projeto->podeEditarConteudo())
                    <a href="{{ route('projetos.editor', $projeto->id) }}" class="btn btn-primary btn-sm" target="_blank">
                        <i class="ki-duotone ki-pencil fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Editor
                    </a>
                    @endif
                    
                    <a href="{{ route('projetos.edit', $projeto->id) }}" class="btn btn-light btn-sm">
                        <i class="ki-duotone ki-pencil fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Editar
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Status e Info Principal -->
                <div class="row g-5 g-xl-8 mb-5">
                    <div class="col-xl-8">
                        <div class="card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Informações do Projeto</h2>
                                </div>
                                <div class="card-toolbar">
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
                            <div class="card-body">
                                <div class="row g-8">
                                    <div class="col-6">
                                        <div class="mb-6">
                                            <label class="fs-6 fw-semibold text-gray-600 mb-2">Número Completo</label>
                                            <div class="fs-4 fw-bold text-gray-900">{{ $projeto->numero_completo ?? 'Não numerado' }}</div>
                                        </div>
                                        
                                        <div class="mb-6">
                                            <label class="fs-6 fw-semibold text-gray-600 mb-2">Tipo</label>
                                            <div class="fs-6 fw-bold text-gray-900">{{ $projeto->tipo_formatado ?? $projeto->tipo }}</div>
                                        </div>

                                        <div class="mb-6">
                                            <label class="fs-6 fw-semibold text-gray-600 mb-2">Autor</label>
                                            <div class="fs-6 fw-bold text-gray-900">{{ $projeto->autor->name ?? 'N/A' }}</div>
                                        </div>

                                        <div class="mb-6">
                                            <label class="fs-6 fw-semibold text-gray-600 mb-2">Comissão</label>
                                            <div class="fs-6 fw-bold text-gray-900">{{ $projeto->comissao->nome ?? 'Não definida' }}</div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-6">
                                            <label class="fs-6 fw-semibold text-gray-600 mb-2">Urgência</label>
                                            <div>
                                                @if($projeto->urgencia === 'urgente')
                                                    <div class="badge badge-light-danger fw-bold">Urgente</div>
                                                @elseif($projeto->urgencia === 'prioritario')
                                                    <div class="badge badge-light-warning fw-bold">Prioritário</div>
                                                @else
                                                    <div class="badge badge-light-secondary fw-bold">Normal</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-6">
                                            <label class="fs-6 fw-semibold text-gray-600 mb-2">Relator</label>
                                            <div class="fs-6 fw-bold text-gray-900">{{ $projeto->relator->name ?? 'Não designado' }}</div>
                                        </div>

                                        <div class="mb-6">
                                            <label class="fs-6 fw-semibold text-gray-600 mb-2">Criado em</label>
                                            <div class="fs-6 fw-bold text-gray-900">{{ $projeto->created_at->format('d/m/Y H:i') }}</div>
                                        </div>

                                        @if($projeto->data_protocolo)
                                        <div class="mb-6">
                                            <label class="fs-6 fw-semibold text-gray-600 mb-2">Protocolado em</label>
                                            <div class="fs-6 fw-bold text-gray-900">{{ $projeto->data_protocolo->format('d/m/Y H:i') }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="separator my-6"></div>

                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Ementa</label>
                                    <div class="fs-6 text-gray-900">{{ $projeto->ementa }}</div>
                                </div>

                                @if($projeto->resumo)
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Resumo</label>
                                    <div class="fs-6 text-gray-900">{{ $projeto->resumo }}</div>
                                </div>
                                @endif

                                @if($projeto->palavras_chave)
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Palavras-chave</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach(explode(',', $projeto->palavras_chave) as $palavra)
                                            <span class="badge badge-light-primary">{{ trim($palavra) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($projeto->observacoes)
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold text-gray-600 mb-2">Observações</label>
                                    <div class="fs-6 text-gray-900">{{ $projeto->observacoes }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <!-- Ações Rápidas -->
                        <div class="card card-flush mb-5">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Ações</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column gap-3">
                                    @if($projeto->status === 'rascunho' && $projeto->hasContent())
                                    <form action="{{ route('projetos.protocolar', $projeto->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Tem certeza que deseja protocolar este projeto?')">
                                            <i class="ki-duotone ki-check-circle fs-3 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Protocolar
                                        </button>
                                    </form>
                                    @endif

                                    @if(in_array($projeto->status, ['protocolado', 'em_tramitacao']))
                                    <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#modal_encaminhar_comissao">
                                        <i class="ki-duotone ki-send fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Encaminhar para Comissão
                                    </button>
                                    @endif

                                    <a href="{{ route('projetos.versoes', $projeto->id) }}" class="btn btn-light-primary w-100">
                                        <i class="ki-duotone ki-time fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Ver Versões ({{ $projeto->version_atual }})
                                    </a>

                                    <a href="{{ route('projetos.tramitacao', $projeto->id) }}" class="btn btn-light-info w-100">
                                        <i class="ki-duotone ki-arrow-right-left fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Ver Tramitação
                                    </a>

                                    <a href="{{ route('projetos.anexos', $projeto->id) }}" class="btn btn-light-warning w-100">
                                        <i class="ki-duotone ki-paper-clip fs-3 me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Anexos ({{ $projeto->anexos->count() }})
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Informações de Versão -->
                        @if($projeto->versionAtual)
                        <div class="card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Versão Atual</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <span class="fs-6 fw-semibold text-gray-600">Versão:</span>
                                    <span class="fs-6 fw-bold text-gray-900 ms-2">{{ $projeto->versionAtual->version_number }}</span>
                                </div>
                                <div class="mb-4">
                                    <span class="fs-6 fw-semibold text-gray-600">Autor:</span>
                                    <span class="fs-6 fw-bold text-gray-900 ms-2">{{ $projeto->versionAtual->author->name ?? 'N/A' }}</span>
                                </div>
                                <div class="mb-4">
                                    <span class="fs-6 fw-semibold text-gray-600">Atualizada em:</span>
                                    <span class="fs-6 fw-bold text-gray-900 ms-2">{{ $projeto->versionAtual->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($projeto->versionAtual->changelog)
                                <div class="mb-4">
                                    <span class="fs-6 fw-semibold text-gray-600">Changelog:</span>
                                    <div class="fs-6 text-gray-900 mt-1">{{ $projeto->versionAtual->changelog }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Conteúdo -->
                @if($projeto->conteudo)
                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Conteúdo</h2>
                        </div>
                        <div class="card-toolbar">
                            @if($projeto->podeEditarConteudo())
                            <a href="{{ route('projetos.editor', $projeto->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                <i class="ki-duotone ki-pencil fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Editar no Editor
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="projeto-conteudo">
                            {!! nl2br(e($projeto->conteudo)) !!}
                        </div>
                    </div>
                </div>
                @else
                <div class="card card-flush">
                    <div class="card-body text-center py-10">
                        <div class="text-gray-500 fs-6 mb-5">
                            Este projeto ainda não possui conteúdo.
                        </div>
                        @if($projeto->podeEditarConteudo())
                        <a href="{{ route('projetos.editor', $projeto->id) }}" class="btn btn-primary" target="_blank">
                            <i class="ki-duotone ki-plus fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Adicionar Conteúdo
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Encaminhar para Comissão -->
    @if(in_array($projeto->status, ['protocolado', 'em_tramitacao']))
    <div class="modal fade" id="modal_encaminhar_comissao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form action="{{ route('projetos.encaminhar-comissao', $projeto->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h2>Encaminhar para Comissão</h2>
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-semibold mb-2">Comissão</label>
                            <select class="form-select form-select-solid" name="comissao_id" required>
                                <option value="">Selecione uma comissão</option>
                                @foreach($opcoes['comissoes'] ?? [] as $comissao)
                                    <option value="{{ $comissao->id }}" {{ $projeto->comissao_id == $comissao->id ? 'selected' : '' }}>{{ $comissao->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2">Relator</label>
                            <select class="form-select form-select-solid" name="relator_id">
                                <option value="">Selecione um relator</option>
                                @foreach($opcoes['autores'] ?? [] as $autor)
                                    <option value="{{ $autor->id }}" {{ $projeto->relator_id == $autor->id ? 'selected' : '' }}>{{ $autor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Encaminhar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @push('styles')
    <style>
        .projeto-conteudo {
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .projeto-conteudo p {
            margin-bottom: 1rem;
        }
    </style>
    @endpush
</x-layouts.app>