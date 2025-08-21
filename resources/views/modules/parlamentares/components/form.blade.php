<!--begin::Form-->
<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="form d-flex flex-column flex-lg-row" id="kt_parlamentar_form">
    @csrf
    @if($method ?? null)
        @method($method)
    @endif
    
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <!--begin::Photo upload-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Foto do Parlamentar</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="d-flex align-items-start">
                    <!--begin::Preview-->
                    <div class="symbol symbol-100px symbol-circle me-5" id="foto-preview-container">
                        <img src="{{ isset($parlamentar['foto']) && $parlamentar['foto'] ? asset('storage/parlamentares/fotos/' . $parlamentar['foto']) : asset('assets/media/avatars/blank.png') }}" alt="Preview da foto" id="foto-preview" class="symbol-label" />
                    </div>
                    <!--end::Preview-->
                    <!--begin::Upload input-->
                    <div class="flex-grow-1">
                        <input type="file" name="foto" id="foto-input" class="form-control mb-2" accept="image/*" />
                        <div class="text-muted fs-7">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                        @if(isset($parlamentar['foto']) && $parlamentar['foto'])
                            <div class="text-muted fs-8 mt-2">Foto atual: {{ $parlamentar['foto'] }}</div>
                        @endif
                        @error('foto')
                            <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Upload input-->
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Photo upload-->
        
        <!--begin::General options-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Dados Pessoais</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Input group-->
                <div class="row mb-10">
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="required form-label">Nome Completo</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="nome" class="form-control mb-2" placeholder="Nome completo do parlamentar" value="{{ old('nome', isset($parlamentar['nome']) ? $parlamentar['nome'] : '') }}" required />
                        <!--end::Input-->
                        @error('nome')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="form-label">Nome Político</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="nome_politico" class="form-control mb-2" placeholder="Nome pelo qual é conhecido politicamente" value="{{ old('nome_politico', isset($parlamentar['nome_politico']) ? $parlamentar['nome_politico'] : '') }}" />
                        <!--end::Input-->
                        <!--begin::Hint-->
                        <div class="text-muted fs-7">Ex: Lula, Bolsonaro, etc. (opcional)</div>
                        <!--end::Hint-->
                        @error('nome_politico')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Input group-->
                
                <!--begin::Input group-->
                <div class="row mb-10">
                    <div class="col-md-4">
                        <!--begin::Label-->
                        <label class="form-label">Email</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="email" name="email" class="form-control mb-2" placeholder="email@camara.gov.br" value="{{ old('email', isset($parlamentar['email']) ? $parlamentar['email'] : '') }}" />
                        <!--end::Input-->
                        @error('email')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <!--begin::Label-->
                        <label class="form-label">CPF</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="cpf" id="cpf-input" class="form-control mb-2" placeholder="000.000.000-00" value="{{ old('cpf', isset($parlamentar['cpf']) ? $parlamentar['cpf'] : '') }}" maxlength="14" />
                        <!--end::Input-->
                        @error('cpf')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <!--begin::Label-->
                        <label class="form-label">Telefone</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="telefone" id="telefone-input" class="form-control mb-2" placeholder="(11) 99999-9999" value="{{ old('telefone', isset($parlamentar['telefone']) ? $parlamentar['telefone'] : '') }}" maxlength="15" />
                        <!--end::Input-->
                        @error('telefone')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Input group-->
                
                <!--begin::Input group-->
                <div class="row mb-10">
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="form-label">Data de Nascimento</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="date" name="data_nascimento" class="form-control mb-2" 
                            value="{{ old('data_nascimento', isset($parlamentar['data_nascimento']) && !empty($parlamentar['data_nascimento']) ? 
                                (function() use ($parlamentar) {
                                    try {
                                        $date = $parlamentar['data_nascimento'];
                                        if (strpos($date, '/') !== false) {
                                            // Data no formato brasileiro, converter para Y-m-d
                                            return \Carbon\Carbon::parse(str_replace(' ', '', $date))->format('Y-m-d');
                                        }
                                        return $date;
                                    } catch (\Exception $e) {
                                        return '';
                                    }
                                })() : '') }}" />
                        <!--end::Input-->
                        @error('data_nascimento')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <!--begin::Label-->
                        <label class="form-label">Profissão</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" name="profissao" class="form-control mb-2" placeholder="Ex: Advogado, Professor, Médico" value="{{ old('profissao', isset($parlamentar['profissao']) ? $parlamentar['profissao'] : '') }}" />
                        <!--end::Input-->
                        @error('profissao')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Input group-->
                
                <!--begin::Input group-->
                <div class="mb-10 fv-row">
                    <!--begin::Label-->
                    <label class="form-label">Escolaridade</label>
                    <!--end::Label-->
                    <!--begin::Select-->
                    <select name="escolaridade" class="form-select mb-2">
                        <option value="">Selecione a escolaridade</option>
                        @foreach($escolaridadeOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('escolaridade', isset($parlamentar['escolaridade']) ? $parlamentar['escolaridade'] : '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <!--end::Select-->
                    @error('escolaridade')
                        <div class="text-danger fs-7">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::General options-->
        
        <!--begin::Parliamentary info-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Informações Parlamentares</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Input group-->
                <div class="row mb-10">
                    <div class="col-md-4">
                        <!--begin::Label-->
                        <label class="required form-label">Partido</label>
                        <!--end::Label-->
                        <!--begin::Input with autocomplete-->
                        <div class="position-relative">
                            <input type="text" 
                                   id="partido_input"
                                   name="partido" 
                                   class="form-control mb-2" 
                                   placeholder="Digite a sigla do partido..."
                                   value="{{ old('partido', isset($parlamentar['partido']) ? $parlamentar['partido'] : '') }}"
                                   autocomplete="off"
                                   required />
                            
                            <!-- Dropdown de sugestões -->
                            <div id="partido_suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm" 
                                 style="display: none; z-index: 1000; max-height: 300px; overflow-y: auto; top: 100%;">
                            </div>
                        </div>
                        <!--end::Input with autocomplete-->
                        @error('partido')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <!--begin::Label-->
                        <label class="required form-label">Cargo</label>
                        <!--end::Label-->
                        <!--begin::Select-->
                        <select name="cargo" class="form-select mb-2" required>
                            <option value="">Selecione o cargo</option>
                            @foreach($cargos as $value => $label)
                                @php
                                    $currentCargo = old('cargo', isset($parlamentar['cargo']) ? $parlamentar['cargo'] : '');
                                    $isSelected = strtolower($currentCargo) == strtolower($value) || $currentCargo == $value;
                                @endphp
                                <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <!--end::Select-->
                        @error('cargo')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                    @if(isset($parlamentar) && !empty($parlamentar))
                    <div class="col-md-4">
                        <!--begin::Label-->
                        <label class="required form-label">Status</label>
                        <!--end::Label-->
                        <!--begin::Select-->
                        <select name="status" class="form-select mb-2" required>
                            @foreach($statusOptions ?? ['ativo' => 'Ativo', 'licenciado' => 'Licenciado', 'inativo' => 'Inativo'] as $value => $label)
                                <option value="{{ $value }}" {{ old('status', isset($parlamentar['status']) ? strtolower($parlamentar['status']) : 'ativo') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <!--end::Select-->
                        @error('status')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>
                <!--end::Input group-->
                
                <!--begin::Input group-->
                <div class="mb-10 fv-row">
                    <!--begin::Label-->
                    <label class="form-label">Comissões</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" name="comissoes" class="form-control mb-2" placeholder="Ex: Educação, Saúde, Finanças (separadas por vírgula)" value="{{ old('comissoes', isset($parlamentar['comissoes']) ? (is_array($parlamentar['comissoes']) ? implode(', ', $parlamentar['comissoes']) : $parlamentar['comissoes']) : '') }}" />
                    <!--end::Input-->
                    <!--begin::Hint-->
                    <div class="text-muted fs-7">Digite as comissões separadas por vírgula</div>
                    <!--end::Hint-->
                    @error('comissoes')
                        <div class="text-danger fs-7">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Parliamentary info-->
        
        <!--begin::User integration-->
        @if(!isset($parlamentar) || empty($parlamentar))
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Integração com Usuário</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!-- Opção: Vincular a usuário existente -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Vinculação</label>
                    <div class="col-lg-8">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="user_option" value="existing" id="existing_user" />
                            <label class="form-check-label" for="existing_user">
                                Vincular a usuário já cadastrado
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_option" value="create" id="create_user" />
                            <label class="form-check-label" for="create_user">
                                Criar usuário de acesso para este parlamentar
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Select de usuário existente -->
                <div class="row mb-6" id="select_user" style="display: none;">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Usuário</label>
                    <div class="col-lg-8">
                        <select name="user_id" class="form-control form-control-lg form-control-solid">
                            <option value="">Selecione um usuário</option>
                            @if(isset($usuariosSemParlamentar) && $usuariosSemParlamentar->count() > 0)
                                @foreach($usuariosSemParlamentar as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('user_id') == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->name }} ({{ $usuario->email }})
                                        @if($usuario->partido)
                                            - {{ $usuario->partido }}
                                        @endif
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Nenhum usuário parlamentar disponível</option>
                            @endif
                        </select>
                        <div class="form-text">Usuários com perfil parlamentar que não possuem cadastro de parlamentar</div>
                    </div>
                </div>

                <!-- Campos para criar usuário -->
                <div id="create_user_fields" style="display: none;">
                    <div class="alert alert-info">
                        <i class="ki-duotone ki-information fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Será criado automaticamente um usuário com perfil PARLAMENTAR usando os dados informados acima.
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Senha do Usuário</label>
                        <div class="col-lg-8">
                            <input type="password" name="usuario_password" class="form-control form-control-lg form-control-solid" 
                                   placeholder="Digite uma senha segura" />
                            @error('usuario_password')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Confirmar Senha</label>
                        <div class="col-lg-8">
                            <input type="password" name="usuario_password_confirmation" class="form-control form-control-lg form-control-solid" 
                                   placeholder="Confirme a senha" />
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::User integration-->
        @endif
        
        <div class="d-flex justify-content-end">
            <!--begin::Button-->
            <a href="{{ $cancelUrl }}" class="btn btn-light me-5">Cancelar</a>
            <!--end::Button-->
            <!--begin::Button-->
            <button type="submit" class="btn btn-primary">
                <span class="indicator-label">{{ $submitText }}</span>
                <span class="indicator-progress">
                    Salvando... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
            <!--end::Button-->
        </div>
    </div>
    <!--end::Main column-->
</form>
<!--end::Form-->

<!--begin::Javascript-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('kt_parlamentar_form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('[type="submit"]');
            const buttonText = submitButton.querySelector('.indicator-label');
            const buttonProgress = submitButton.querySelector('.indicator-progress');
            
            // Show loading state
            submitButton.disabled = true;
            buttonText.classList.add('d-none');
            buttonProgress.classList.remove('d-none');
        });
    }
    
    // Photo preview functionality
    const fotoInput = document.getElementById('foto-input');
    const fotoPreview = document.getElementById('foto-preview');
    const originalSrc = fotoPreview.src;
    
    if (fotoInput && fotoPreview) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Verificar se é uma imagem
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        fotoPreview.src = e.target.result;
                    };
                    
                    reader.readAsDataURL(file);
                } else {
                    alert('Por favor, selecione apenas arquivos de imagem.');
                    fotoInput.value = '';
                }
            } else {
                // Voltar para a imagem original se nenhum arquivo for selecionado
                fotoPreview.src = originalSrc;
            }
        });
    }
    
    // CPF mask
    const cpfInput = document.getElementById('cpf-input');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });
    }
    
    // Phone mask
    const telefoneInput = document.getElementById('telefone-input');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 10) {
                // Telefone fixo: (11) 1234-5678
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                // Celular: (11) 99999-9999
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            
            e.target.value = value;
        });
    }
    
    // Controle de integração com usuário
    const existingUserRadio = document.getElementById('existing_user');
    const createUserRadio = document.getElementById('create_user');
    const selectUser = document.getElementById('select_user');
    const createUserFields = document.getElementById('create_user_fields');
    
    function toggleUserOptions() {
        if (existingUserRadio && existingUserRadio.checked) {
            selectUser.style.display = 'block';
            createUserFields.style.display = 'none';
        } else if (createUserRadio && createUserRadio.checked) {
            selectUser.style.display = 'none';
            createUserFields.style.display = 'block';
        } else {
            selectUser.style.display = 'none';
            createUserFields.style.display = 'none';
        }
    }

    if (existingUserRadio) {
        existingUserRadio.addEventListener('change', toggleUserOptions);
    }
    if (createUserRadio) {
        createUserRadio.addEventListener('change', toggleUserOptions);
    }

    // Inicializar
    toggleUserOptions();
    
    // Inicializar sistema de autocomplete de partidos
    setTimeout(() => {
        initPartidoAutocomplete();
    }, 500);
});

// Sistema de Autocomplete de Partidos
function initPartidoAutocomplete() {
    const partidoInput = document.getElementById('partido_input');
    const partidoSuggestions = document.getElementById('partido_suggestions');
    
    if (!partidoInput || !partidoSuggestions) {
        console.error('❌ Elementos do autocomplete de partido não encontrados');
        return;
    }
    
    console.log('🏦 Sistema de autocomplete de partido inicializado');
    
    let searchTimeout;
    let currentPartidos = [];
    let isSearching = false;
    
    // Carregar partidos iniciais
    const partidosIniciais = @json($partidos ?? []);
    
    // Debounced search function
    function debouncedSearchPartidos(query) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (query.length >= 1) {
                searchPartidos(query);
            } else {
                hidePartidoSuggestions();
            }
        }, 200);
    }
    
    // Buscar partidos via API ou dados locais
    async function searchPartidos(query) {
        if (isSearching) {
            console.log('⏸️ Busca de partidos já em andamento, pulando...');
            return;
        }
        
        try {
            isSearching = true;
            
            // Primeiro, buscar nos dados locais
            const partidosLocal = searchPartidosLocal(query, partidosIniciais);
            
            if (partidosLocal.length > 0) {
                console.log(`🏦 Encontrados ${partidosLocal.length} partidos localmente`);
                showPartidoSuggestions(partidosLocal, query);
                return;
            }
            
            // Se não encontrou localmente, buscar na API
            const url = `/api/partidos/buscar-sigla?sigla=${encodeURIComponent(query)}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                cache: 'no-store'
            });
            
            if (!response.ok) {
                throw new Error(`Erro HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.partidos && data.partidos.length > 0) {
                console.log(`🏦 Encontrados ${data.partidos.length} partidos na API`);
                showPartidoSuggestions(data.partidos, query, true);
            } else {
                showNoPartidoResults(query);
            }
            
        } catch (error) {
            console.error('💥 Erro na busca de partidos:', error);
            // Em caso de erro na API, mostrar opção de criar novo partido
            showNoPartidoResults(query);
        } finally {
            isSearching = false;
        }
    }
    
    // Buscar partidos nos dados locais
    function searchPartidosLocal(query, partidos) {
        const queryLower = query.toLowerCase().trim();
        const results = [];
        
        for (const [sigla, nome] of Object.entries(partidos)) {
            if (sigla.toLowerCase().includes(queryLower) || 
                nome.toLowerCase().includes(queryLower)) {
                results.push({
                    sigla: sigla,
                    nome: nome,
                    nome_completo: nome,
                    local: true
                });
            }
        }
        
        return results.slice(0, 10); // Limitar a 10 resultados
    }
    
    // Mostrar sugestões de partidos
    function showPartidoSuggestions(partidos, query, fromApi = false) {
        currentPartidos = partidos;
        let html = '';
        
        partidos.forEach((partido, index) => {
            // Destacar texto que coincide com a busca
            const highlightedSigla = highlightMatch(partido.sigla, query);
            const highlightedNome = highlightMatch(partido.nome_completo || partido.nome, query);
            
            // Definir badge da fonte
            const sourceBadge = fromApi ? '<span class="badge badge-light-info fs-8 ms-2">API Externa</span>' : 
                                          '<span class="badge badge-light-success fs-8 ms-2">Cadastrado</span>';
            
            html += `
                <div class="suggestion-item p-3 border-bottom cursor-pointer hover-bg-light partido-item" 
                     data-index="${index}" 
                     style="cursor: pointer; transition: all 0.2s;">
                    <div class="d-flex align-items-center">
                        <i class="ki-duotone ki-flag fs-2 text-primary me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-0 text-gray-800">${highlightedSigla}</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="text-muted fs-7 me-2">${highlightedNome}</span>
                                        ${sourceBadge}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <i class="ki-duotone ki-arrow-right fs-6 text-muted">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        partidoSuggestions.innerHTML = html;
        partidoSuggestions.style.display = 'block';
        
        // Adicionar event listeners para clique
        partidoSuggestions.querySelectorAll('.partido-item').forEach(item => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                selectPartido(partidos[index]);
            });
        });
    }
    
    // Mostrar quando nenhum partido é encontrado
    function showNoPartidoResults(query) {
        partidoSuggestions.innerHTML = `
            <div class="p-4 text-center">
                <i class="ki-duotone ki-information-2 fs-2x text-muted mb-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <p class="text-muted mb-2">Nenhum partido encontrado para "${query}"</p>
                <div class="mt-3">
                    <a href="/partidos/create" class="btn btn-sm btn-light-primary" target="_blank">
                        <i class="ki-duotone ki-plus fs-6 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Cadastrar Novo Partido
                    </a>
                </div>
                <small class="text-muted d-block mt-2">Você pode continuar digitando ou cadastrar um novo partido</small>
            </div>
        `;
        partidoSuggestions.style.display = 'block';
    }
    
    // Esconder sugestões
    function hidePartidoSuggestions() {
        partidoSuggestions.style.display = 'none';
        currentPartidos = [];
    }
    
    // Selecionar partido
    function selectPartido(partido) {
        partidoInput.value = partido.sigla;
        hidePartidoSuggestions();
        
        // Mostrar feedback visual
        partidoInput.style.borderColor = '#50cd89';
        
        setTimeout(() => {
            partidoInput.style.borderColor = '';
        }, 2000);
        
        console.log('✅ Partido selecionado:', partido.sigla, '-', partido.nome);
    }
    
    // Função para destacar texto que coincide com a busca
    function highlightMatch(text, query) {
        if (!query.trim()) return text;
        
        const regex = new RegExp(`(${query.trim()})`, 'gi');
        return text.replace(regex, '<mark class="bg-warning text-dark">$1</mark>');
    }
    
    // Event listeners
    partidoInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        
        if (query.length >= 1) {
            debouncedSearchPartidos(query);
        } else {
            hidePartidoSuggestions();
        }
    });
    
    partidoInput.addEventListener('focus', (e) => {
        const query = e.target.value.trim();
        if (query.length >= 1) {
            debouncedSearchPartidos(query);
        }
    });
    
    // Esconder sugestões quando clicar fora
    document.addEventListener('click', (e) => {
        if (!partidoInput.contains(e.target) && !partidoSuggestions.contains(e.target)) {
            hidePartidoSuggestions();
        }
    });
    
    // Navegação por teclado
    partidoInput.addEventListener('keydown', (e) => {
        const items = partidoSuggestions.querySelectorAll('.partido-item');
        const selected = partidoSuggestions.querySelector('.partido-item.active');
        let newIndex = -1;
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (selected) {
                const currentIndex = Array.from(items).indexOf(selected);
                newIndex = Math.min(currentIndex + 1, items.length - 1);
            } else {
                newIndex = 0;
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (selected) {
                const currentIndex = Array.from(items).indexOf(selected);
                newIndex = Math.max(currentIndex - 1, 0);
            } else {
                newIndex = items.length - 1;
            }
        } else if (e.key === 'Enter' && selected) {
            e.preventDefault();
            const index = Array.from(items).indexOf(selected);
            selectPartido(currentPartidos[index]);
        } else if (e.key === 'Escape') {
            hidePartidoSuggestions();
        }
        
        if (newIndex >= 0 && items[newIndex]) {
            items.forEach(item => item.classList.remove('active', 'bg-light'));
            items[newIndex].classList.add('active', 'bg-light');
        }
    });
    
    console.log('🏦 Autocomplete de partido configurado com sucesso');
}
</script>
<!--end::Javascript-->

<!--begin::Styles-->
<style>
/* Autocomplete de Partido */
.suggestion-item:hover {
    background-color: #f8f9fa !important;
}
.suggestion-item.active {
    background-color: #e9ecef !important;
}
.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

/* Partido specific styles */
.partido-item {
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.partido-item:hover {
    background-color: #f0f8ff !important;
    border-left-color: #007bff;
    transform: translateX(2px);
}

/* Highlight matches */
mark {
    padding: 1px 2px;
    border-radius: 2px;
    font-weight: 600;
}

/* Input feedback */
input[style*="border-color: rgb(80, 205, 137)"] {
    box-shadow: 0 0 0 2px rgba(80, 205, 137, 0.25);
    transition: all 0.2s ease;
}

/* Loading spinner in dropdown */
#partido_suggestions .spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Badge styling */
.badge.fs-8 {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Dropdown z-index fix */
#partido_suggestions {
    z-index: 1050 !important;
}
</style>
<!--end::Styles-->