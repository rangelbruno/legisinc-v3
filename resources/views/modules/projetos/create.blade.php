<x-layouts.app title="Novo Projeto de Lei">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Novo Projeto de Lei
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
                        <li class="breadcrumb-item text-muted">Novo</li>
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

                <form action="{{ route('projetos.store') }}" method="POST" id="kt_projeto_form">
                    @csrf
                    
                    <div class="card card-flush">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informações Básicas</h2>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-8">
                                <div class="col-xl-8">
                                    <div class="fv-row mb-8">
                                        <label class="required fs-6 fw-semibold mb-2">Título</label>
                                        <input type="text" class="form-control form-control-solid" name="titulo" value="{{ old('titulo') }}" placeholder="Digite o título do projeto" required />
                                        @error('titulo')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="fv-row mb-8">
                                        <label class="required fs-6 fw-semibold mb-2">Ementa</label>
                                        <textarea class="form-control form-control-solid" name="ementa" rows="4" placeholder="Digite a ementa do projeto" required>{{ old('ementa') }}</textarea>
                                        @error('ementa')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="fv-row mb-8">
                                        <label class="fs-6 fw-semibold mb-2">Resumo</label>
                                        <textarea class="form-control form-control-solid" name="resumo" rows="3" placeholder="Resumo executivo do projeto (opcional)">{{ old('resumo') }}</textarea>
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
                                        <select class="form-select form-select-solid" name="tipo" id="tipoSelect" required>
                                            <option value="">Selecione o tipo</option>
                                            @foreach($opcoes['tipos'] ?? [] as $key => $nome)
                                                <option value="{{ $key }}" {{ old('tipo') == $key ? 'selected' : '' }}>{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('tipo')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                        @if(empty($opcoes['tipos'] ?? []))
                                            <div class="alert alert-warning mt-2">
                                                <small>Nenhum tipo de projeto disponível. Verifique a configuração do sistema.</small>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="fv-row mb-8" id="modeloContainer" style="display: none;">
                                        <label class="fs-6 fw-semibold mb-2">Modelo</label>
                                        <select class="form-select form-select-solid" name="modelo_id" id="modeloSelect">
                                            <option value="">Selecione um modelo</option>
                                        </select>
                                        <div class="form-text">Selecione um modelo para pré-popular o conteúdo</div>
                                        @error('modelo_id')
                                            <div class="fv-plugins-message-container">
                                                <div class="fv-help-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row g-3 mb-8">
                                        <div class="col-6">
                                            <label class="fs-6 fw-semibold mb-2">Número</label>
                                            <input type="text" class="form-control form-control-solid" name="numero" value="{{ old('numero') }}" placeholder="Auto" />
                                            <div class="form-text">Deixe em branco para gerar automaticamente</div>
                                            @error('numero')
                                                <div class="fv-plugins-message-container">
                                                    <div class="fv-help-block">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label class="fs-6 fw-semibold mb-2">Ano</label>
                                            <input type="number" class="form-control form-control-solid" name="ano" value="{{ old('ano', date('Y')) }}" min="2020" max="{{ date('Y') + 5 }}" />
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
                                                <option value="{{ $key }}" {{ old('urgencia', 'normal') == $key ? 'selected' : '' }}>{{ $nome }}</option>
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
                                                <option value="{{ $comissao->id }}" {{ old('comissao_id') == $comissao->id ? 'selected' : '' }}>{{ $comissao->nome }}</option>
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
                                                <option value="{{ $autor->id }}" {{ old('relator_id') == $autor->id ? 'selected' : '' }}>{{ $autor->name }}</option>
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
                                        <input type="date" class="form-control form-control-solid" name="data_limite_tramitacao" value="{{ old('data_limite_tramitacao') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
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
                                <input type="text" class="form-control form-control-solid" name="palavras_chave" value="{{ old('palavras_chave') }}" placeholder="Separadas por vírgula" />
                                <div class="form-text">Digite palavras-chave separadas por vírgula para facilitar a busca</div>
                                @error('palavras_chave')
                                    <div class="fv-plugins-message-container">
                                        <div class="fv-help-block">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>

                            <div class="fv-row mb-8">
                                <label class="fs-6 fw-semibold mb-2">Observações</label>
                                <textarea class="form-control form-control-solid" name="observacoes" rows="4" placeholder="Observações internas sobre o projeto">{{ old('observacoes') }}</textarea>
                                @error('observacoes')
                                    <div class="fv-plugins-message-container">
                                        <div class="fv-help-block">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>

                            <div class="fv-row mb-8">
                                <label class="fs-6 fw-semibold mb-2">Conteúdo Inicial</label>
                                <textarea class="form-control form-control-solid" name="conteudo" rows="10" placeholder="Digite o conteúdo inicial do projeto (opcional)">{{ old('conteudo') }}</textarea>
                                <div class="form-text">Você pode adicionar o conteúdo agora ou usar o editor avançado após criar o projeto</div>
                                @error('conteudo')
                                    <div class="fv-plugins-message-container">
                                        <div class="fv-help-block">{{ $message }}</div>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-10">
                        <a href="{{ route('projetos.index') }}" class="btn btn-light me-5">Cancelar</a>
                        <button type="submit" class="btn btn-primary" id="kt_projeto_submit">
                            <span class="indicator-label">Salvar Projeto</span>
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

            // Auto-completar ano baseado no tipo e carregar modelos
            const tipoSelect = document.querySelector('#tipoSelect');
            const anoInput = document.querySelector('input[name="ano"]');
            const modeloContainer = document.getElementById('modeloContainer');
            const modeloSelect = document.getElementById('modeloSelect');
            const conteudoTextarea = document.querySelector('textarea[name="conteudo"]');
            
            if (tipoSelect && anoInput) {
                tipoSelect.addEventListener('change', function() {
                    if (!anoInput.value) {
                        anoInput.value = new Date().getFullYear();
                    }
                    
                    // Carregar modelos para o tipo selecionado
                    const tipoSelecionado = this.value;
                    
                    if (tipoSelecionado) {
                        fetch(`/admin/modelos/por-tipo?tipo=${tipoSelecionado}`)
                            .then(response => response.json())
                            .then(data => {
                                modeloSelect.innerHTML = '<option value="">Selecione um modelo</option>';
                                
                                if (data.success && data.modelos.length > 0) {
                                    data.modelos.forEach(modelo => {
                                        const option = document.createElement('option');
                                        option.value = modelo.id;
                                        option.textContent = modelo.nome;
                                        if (modelo.descricao) {
                                            option.title = modelo.descricao;
                                        }
                                        modeloSelect.appendChild(option);
                                    });
                                    modeloContainer.style.display = 'block';
                                } else {
                                    modeloContainer.style.display = 'none';
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao carregar modelos:', error);
                                modeloContainer.style.display = 'none';
                            });
                    } else {
                        modeloContainer.style.display = 'none';
                    }
                });
            }
            
            // Carregar conteúdo do modelo selecionado
            if (modeloSelect && conteudoTextarea) {
                modeloSelect.addEventListener('change', function() {
                    const modeloId = this.value;
                    
                    if (modeloId) {
                        fetch(`/admin/modelos/${modeloId}/conteudo`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    conteudoTextarea.value = data.conteudo;
                                } else {
                                    alert('Erro ao carregar modelo: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao carregar conteúdo do modelo:', error);
                                alert('Erro ao carregar conteúdo do modelo');
                            });
                    } else {
                        conteudoTextarea.value = '';
                    }
                });
            }

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
                                relatorSelect.innerHTML = '<option value="">Selecione um relator</option>';
                                data.membros.forEach(membro => {
                                    const option = document.createElement('option');
                                    option.value = membro.id;
                                    option.textContent = membro.name;
                                    relatorSelect.appendChild(option);
                                });
                            })
                            .catch(() => {
                                // Se der erro, manter opções originais
                                console.log('Erro ao carregar membros da comissão');
                            });
                    } else {
                        // Resetar para lista completa de autores
                        location.reload();
                    }
                });
            }

            // Preview de palavras-chave
            const palavrasChaveInput = document.querySelector('input[name="palavras_chave"]');
            
            if (palavrasChaveInput) {
                palavrasChaveInput.addEventListener('input', function() {
                    const palavras = this.value.split(',').map(p => p.trim()).filter(p => p);
                    console.log('Palavras-chave:', palavras);
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>