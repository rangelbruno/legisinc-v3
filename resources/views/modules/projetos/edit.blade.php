<x-layouts.app title="Editar Projeto de Lei">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Editar Projeto de Lei
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
                        <li class="breadcrumb-item text-muted">Editar</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('projetos.show', $projeto->id) }}" class="btn btn-light-primary btn-sm">
                        <i class="ki-duotone ki-arrow-left fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center mb-10">
                        <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-danger">Erro na validação</h4>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Alerta de Status -->
                @if(!in_array($projeto->status, ['rascunho', 'em_revisao']))
                    <div class="alert alert-warning d-flex align-items-center mb-10">
                        <i class="ki-duotone ki-information fs-2hx text-warning me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-warning">Atenção</h4>
                            <span>Este projeto está com status "{{ $projeto->status_formatado }}". Algumas alterações podem ter restrições.</span>
                        </div>
                    </div>
                @endif

                <form action="{{ route('projetos.update', $projeto->id) }}" method="POST" id="kt_projeto_form">
                    @csrf
                    @method('PUT')
                    
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações Básicas</h2>
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
                                <div class="col-xl-8">
                                    <div class="fv-row mb-8">
                                        <label class="required fs-6 fw-semibold mb-2">Título</label>
                                        <input type="text" class="form-control form-control-solid" name="titulo" value="{{ old('titulo', $projeto->titulo) }}" placeholder="Digite o título do projeto" required />
                                        @error('titulo')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="fv-row mb-8">
                                        <label class="required fs-6 fw-semibold mb-2">Ementa</label>
                                        <textarea class="form-control form-control-solid" name="ementa" rows="4" placeholder="Digite a ementa do projeto" required>{{ old('ementa', $projeto->ementa) }}</textarea>
                                        @error('ementa')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="fv-row mb-8">
                                        <label class="fs-6 fw-semibold mb-2">Resumo</label>
                                        <textarea class="form-control form-control-solid" name="resumo" rows="3" placeholder="Resumo executivo do projeto (opcional)">{{ old('resumo', $projeto->resumo) }}</textarea>
                                        @error('resumo')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="fv-row mb-8">
                                        <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                                        <select class="form-select form-select-solid" name="tipo" required {{ $projeto->status !== 'rascunho' ? 'disabled' : '' }}>
                                            <option value="">Selecione o tipo</option>
                                            @foreach($opcoes['tipos'] ?? [] as $key => $nome)
                                                <option value="{{ $key }}" {{ old('tipo', $projeto->tipo) == $key ? 'selected' : '' }}>{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @if($projeto->status !== 'rascunho')
                                            <input type="hidden" name="tipo" value="{{ $projeto->tipo }}" />
                                            <div class="form-text">Tipo não pode ser alterado após protocolo</div>
                                        @endif
                                        @error('tipo')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row g-3 mb-8">
                                        <div class="col-6">
                                            <label class="fs-6 fw-semibold mb-2">Número</label>
                                            <input type="text" class="form-control form-control-solid" name="numero" value="{{ old('numero', $projeto->numero) }}" placeholder="Auto" {{ $projeto->status !== 'rascunho' ? 'readonly' : '' }} />
                                            @if($projeto->status !== 'rascunho')
                                                <div class="form-text">Número não pode ser alterado após protocolo</div>
                                            @else
                                                <div class="form-text">Deixe em branco para gerar automaticamente</div>
                                            @endif
                                            @error('numero')
                                                <div class="fv-plugins-message-container">
                                                    <div class="fv-help-block">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="fs-6 fw-semibold mb-2">Ano</label>
                                            <input type="number" class="form-control form-control-solid" name="ano" value="{{ old('ano', $projeto->ano) }}" min="2020" max="{{ date('Y') + 5 }}" {{ $projeto->status !== 'rascunho' ? 'readonly' : '' }} />
                                            @if($projeto->status !== 'rascunho')
                                                <div class="form-text">Ano não pode ser alterado após protocolo</div>
                                            @endif
                                            @error('ano')
                                                <div class="fv-plugins-message-container">
                                                    <div class="fv-help-block">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="fv-row mb-8">
                                        <label class="required fs-6 fw-semibold mb-2">Urgência</label>
                                        <select class="form-select form-select-solid" name="urgencia" required>
                                            @foreach($opcoes['urgencias'] ?? [] as $key => $nome)
                                                <option value="{{ $key }}" {{ old('urgencia', $projeto->urgencia) == $key ? 'selected' : '' }}>{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('urgencia')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="fv-row mb-8">
                                        <label class="fs-6 fw-semibold mb-2">Comissão</label>
                                        <select class="form-select form-select-solid" name="comissao_id">
                                            <option value="">Selecione uma comissão</option>
                                            @foreach($opcoes['comissoes'] ?? [] as $comissao)
                                                <option value="{{ $comissao->id }}" {{ old('comissao_id', $projeto->comissao_id) == $comissao->id ? 'selected' : '' }}>{{ $comissao->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('comissao_id')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="fv-row mb-8">
                                        <label class="fs-6 fw-semibold mb-2">Relator</label>
                                        <select class="form-select form-select-solid" name="relator_id">
                                            <option value="">Selecione um relator</option>
                                            @foreach($opcoes['autores'] ?? [] as $autor)
                                                <option value="{{ $autor->id }}" {{ old('relator_id', $projeto->relator_id) == $autor->id ? 'selected' : '' }}>{{ $autor->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('relator_id')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="fv-row mb-8">
                                        <label class="fs-6 fw-semibold mb-2">Data Limite Tramitação</label>
                                        <input type="date" class="form-control form-control-solid" name="data_limite_tramitacao" value="{{ old('data_limite_tramitacao', $projeto->data_limite_tramitacao?->format('Y-m-d')) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                                        @error('data_limite_tramitacao')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-flush mt-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Detalhes Adicionais</h2>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="fv-row mb-8">
                                <label class="fs-6 fw-semibold mb-2">Palavras-chave</label>
                                <input type="text" class="form-control form-control-solid" name="palavras_chave" value="{{ old('palavras_chave', $projeto->palavras_chave) }}" placeholder="Separadas por vírgula" />
                                <div class="form-text">Digite palavras-chave separadas por vírgula para facilitar a busca</div>
                                @error('palavras_chave')
                                    <div class="fv-plugins-message-container">
                                        <div class="fv-help-block">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>

                            <div class="fv-row mb-8">
                                <label class="fs-6 fw-semibold mb-2">Observações</label>
                                <textarea class="form-control form-control-solid" name="observacoes" rows="4" placeholder="Observações internas sobre o projeto">{{ old('observacoes', $projeto->observacoes) }}</textarea>
                                @error('observacoes')
                                    <div class="fv-plugins-message-container">
                                        <div class="fv-help-block">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>

                            @if($projeto->podeEditarConteudo())
                            <div class="fv-row mb-8">
                                <label class="fs-6 fw-semibold mb-2">Conteúdo</label>
                                <textarea class="form-control form-control-solid" name="conteudo" rows="10" placeholder="Digite o conteúdo do projeto">{{ old('conteudo', $projeto->conteudo) }}</textarea>
                                <div class="form-text">
                                    Para edição avançada com formatação rica, use o 
                                    <a href="{{ route('projetos.editor', $projeto->id) }}" target="_blank" class="fw-bold text-primary">Editor Avançado</a>
                                </div>
                                @error('conteudo')
                                    <div class="fv-plugins-message-container">
                                        <div class="fv-help-block">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                            @else
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="ki-duotone ki-information-2 fs-2hx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-info">Conteúdo não editável</h4>
                                    <span>O conteúdo não pode ser editado através deste formulário devido ao status atual do projeto. Use o histórico de versões para acompanhar as alterações.</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informações de Auditoria -->
                    <div class="card card-flush mt-5">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações do Sistema</h2>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-8">
                                <div class="col-6">
                                    <div class="mb-6">
                                        <label class="fs-6 fw-semibold text-gray-600 mb-2">Criado por</label>
                                        <div class="fs-6 fw-bold text-gray-900">{{ $projeto->autor->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="mb-6">
                                        <label class="fs-6 fw-semibold text-gray-600 mb-2">Criado em</label>
                                        <div class="fs-6 fw-bold text-gray-900">{{ $projeto->created_at->format('d/m/Y H:i:s') }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-6">
                                        <label class="fs-6 fw-semibold text-gray-600 mb-2">Última atualização</label>
                                        <div class="fs-6 fw-bold text-gray-900">{{ $projeto->updated_at->format('d/m/Y H:i:s') }}</div>
                                    </div>
                                    <div class="mb-6">
                                        <label class="fs-6 fw-semibold text-gray-600 mb-2">Versão atual</label>
                                        <div class="fs-6 fw-bold text-gray-900">{{ $projeto->version_atual }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-10">
                        <a href="{{ route('projetos.show', $projeto->id) }}" class="btn btn-light me-5">Cancelar</a>
                        <button type="submit" class="btn btn-primary" id="kt_projeto_submit">
                            <span class="indicator-label">Salvar Alterações</span>
                            <span class="indicator-progress">
                                Processando... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('kt_projeto_form');
            const submitButton = document.getElementById('kt_projeto_submit');

            form.addEventListener('submit', function(e) {
                // Mostrar loading
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;
            });

            // Filtrar relatores baseado na comissão
            const comissaoSelect = document.querySelector('select[name="comissao_id"]');
            const relatorSelect = document.querySelector('select[name="relator_id"]');
            
            if (comissaoSelect && relatorSelect) {
                comissaoSelect.addEventListener('change', function() {
                    const comissaoId = this.value;
                    
                    if (comissaoId) {
                        // Fazer busca de relatores da comissão
                        fetch(`/comissoes/${comissaoId}/membros`)
                            .then(response => response.json())
                            .then(data => {
                                const relatorAtual = relatorSelect.value;
                                relatorSelect.innerHTML = '<option value="">Selecione um relator</option>';
                                
                                data.membros.forEach(membro => {
                                    const option = document.createElement('option');
                                    option.value = membro.id;
                                    option.textContent = membro.name;
                                    if (membro.id == relatorAtual) {
                                        option.selected = true;
                                    }
                                    relatorSelect.appendChild(option);
                                });
                            })
                            .catch(() => {
                                console.log('Erro ao carregar membros da comissão');
                            });
                    }
                });
            }

            // Aviso sobre alterações críticas
            const tipoSelect = document.querySelector('select[name="tipo"]');
            const numeroInput = document.querySelector('input[name="numero"]');
            const anoInput = document.querySelector('input[name="ano"]');
            
            [tipoSelect, numeroInput, anoInput].forEach(element => {
                if (element && !element.disabled && !element.readOnly) {
                    element.addEventListener('change', function() {
                        if (confirm('Esta alteração pode afetar a numeração e identificação do projeto. Continuar?')) {
                            // Permitir alteração
                        } else {
                            // Reverter valor
                            this.value = this.defaultValue;
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>