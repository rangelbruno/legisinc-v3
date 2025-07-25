<x-layouts.app title="Criar Usuário">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Criar Usuário
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('usuarios.index') }}" class="text-muted text-hover-primary">Usuários</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Criar</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                @if(session('error'))
                    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                        <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-danger">Erro ao criar usuário</h4>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                        <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-success">Sucesso!</h4>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('usuarios.store') }}" class="form d-flex flex-column flex-lg-row">
                    @csrf
                    
                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Avatar</h2>
                                </div>
                            </div>
                            <div class="card-body text-center pt-0">
                                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                                    <div class="image-input-wrapper w-150px h-150px"></div>
                                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Alterar avatar">
                                        <i class="ki-duotone ki-pencil fs-7">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                    </label>
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancelar avatar">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remover avatar">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="text-muted fs-7">Permitido apenas arquivos: *.png, *.jpg, *.jpeg</div>
                            </div>
                        </div>

                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Status</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="ativo" value="1" checked id="kt_user_status">
                                    <label class="form-check-label" for="kt_user_status">
                                        Usuário Ativo
                                    </label>
                                </div>
                                <div class="text-muted fs-7">O usuário terá acesso ao sistema</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Informações Gerais</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row mb-5">
                                    <div class="col-md-6 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Nome Completo</label>
                                        <input type="text" class="form-control form-control-solid @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Email</label>
                                        <input type="email" class="form-control form-control-solid @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required />
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-5">
                                    <div class="col-md-6 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Senha</label>
                                        <input type="password" class="form-control form-control-solid @error('password') is-invalid @enderror" name="password" required />
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Confirmar Senha</label>
                                        <input type="password" class="form-control form-control-solid" name="password_confirmation" required />
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">Documento (CPF)</label>
                                        <input type="text" class="form-control form-control-solid @error('documento') is-invalid @enderror" name="documento" value="{{ old('documento') }}" placeholder="000.000.000-00" />
                                        @error('documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">Telefone</label>
                                        <input type="text" class="form-control form-control-solid @error('telefone') is-invalid @enderror" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 00000-0000" />
                                        @error('telefone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">Data de Nascimento</label>
                                        <input type="date" class="form-control form-control-solid @error('data_nascimento') is-invalid @enderror" name="data_nascimento" value="{{ old('data_nascimento') }}" />
                                        @error('data_nascimento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">Profissão</label>
                                        <input type="text" class="form-control form-control-solid @error('profissao') is-invalid @enderror" name="profissao" value="{{ old('profissao') }}" />
                                        @error('profissao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6 fv-row">
                                        <label class="fs-6 fw-semibold mb-2">Cargo Atual</label>
                                        <input type="text" class="form-control form-control-solid @error('cargo_atual') is-invalid @enderror" name="cargo_atual" value="{{ old('cargo_atual') }}" />
                                        @error('cargo_atual')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 fv-row" id="partido-field" style="display: none;">
                                        <label class="fs-6 fw-semibold mb-2">Partido</label>
                                        <select class="form-select form-select-solid @error('partido') is-invalid @enderror" name="partido">
                                            <option value="">Selecione o partido</option>
                                            @if(isset($partidos))
                                                @foreach($partidos as $sigla => $nome)
                                                    <option value="{{ $sigla }}" {{ old('partido') == $sigla ? 'selected' : '' }}>{{ $sigla }} - {{ $nome }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('partido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-12 fv-row">
                                        <label class="required fs-6 fw-semibold mb-2">Perfil</label>
                                        <select class="form-select form-select-solid @error('perfil') is-invalid @enderror" name="perfil" id="perfil-select" required>
                                            <option value="">Selecione o perfil</option>
                                            @foreach($perfis as $key => $nome)
                                                <option value="{{ $key }}" {{ old('perfil') == $key ? 'selected' : '' }}>{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('perfil')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Campos específicos para Parlamentar -->
                                <div id="parlamentar-fields" style="display: none;">
                                    <div class="card card-flush py-4 mb-5">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Dados Parlamentares</h2>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="row mb-5">
                                                <div class="col-md-6 fv-row">
                                                    <label class="fs-6 fw-semibold mb-2">Nome Político</label>
                                                    <input type="text" class="form-control form-control-solid @error('nome_politico') is-invalid @enderror" name="nome_politico" value="{{ old('nome_politico') }}" placeholder="Nome pelo qual é conhecido politicamente" />
                                                    @error('nome_politico')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 fv-row">
                                                    <label class="fs-6 fw-semibold mb-2">Cargo Parlamentar</label>
                                                    <select class="form-select form-select-solid @error('cargo_parlamentar') is-invalid @enderror" name="cargo_parlamentar">
                                                        <option value="">Selecione o cargo</option>
                                                        <option value="Vereador" {{ old('cargo_parlamentar') == 'Vereador' ? 'selected' : '' }}>Vereador</option>
                                                        <option value="Vereadora" {{ old('cargo_parlamentar') == 'Vereadora' ? 'selected' : '' }}>Vereadora</option>
                                                        <option value="Presidente da Câmara" {{ old('cargo_parlamentar') == 'Presidente da Câmara' ? 'selected' : '' }}>Presidente da Câmara</option>
                                                        <option value="Vice-Presidente" {{ old('cargo_parlamentar') == 'Vice-Presidente' ? 'selected' : '' }}>Vice-Presidente</option>
                                                        <option value="1º Secretário" {{ old('cargo_parlamentar') == '1º Secretário' ? 'selected' : '' }}>1º Secretário</option>
                                                        <option value="2º Secretário" {{ old('cargo_parlamentar') == '2º Secretário' ? 'selected' : '' }}>2º Secretário</option>
                                                    </select>
                                                    @error('cargo_parlamentar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row mb-5">
                                                <div class="col-md-12 fv-row">
                                                    <label class="fs-6 fw-semibold mb-2">Comissões</label>
                                                    <input type="text" class="form-control form-control-solid @error('comissoes') is-invalid @enderror" name="comissoes" value="{{ old('comissoes') }}" placeholder="Ex: Educação, Saúde, Finanças (separadas por vírgula)" />
                                                    <div class="form-text">Digite as comissões separadas por vírgula</div>
                                                    @error('comissoes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-light me-5">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Salvar</span>
                                <span class="indicator-progress">Salvando...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para CPF
            const documentoInput = document.querySelector('input[name="documento"]');
            if (documentoInput) {
                documentoInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length <= 11) {
                        value = value.replace(/(\d{3})(\d)/, '$1.$2');
                        value = value.replace(/(\d{3})(\d)/, '$1.$2');
                        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    }
                    e.target.value = value;
                });
            }

            // Máscara para telefone
            const telefoneInput = document.querySelector('input[name="telefone"]');
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length <= 11) {
                        if (value.length > 6) {
                            value = value.replace(/(\d{2})(\d{5})(\d)/, '($1) $2-$3');
                        } else if (value.length > 2) {
                            value = value.replace(/(\d{2})(\d)/, '($1) $2');
                        }
                    }
                    e.target.value = value;
                });
            }

            // Controle de visibilidade dos campos baseado no perfil
            const perfilSelect = document.getElementById('perfil-select');
            const partidoField = document.getElementById('partido-field');
            const parlamentarFields = document.getElementById('parlamentar-fields');

            function toggleFields() {
                const selectedPerfil = perfilSelect.value;
                
                if (selectedPerfil === 'PARLAMENTAR' || selectedPerfil === 'RELATOR') {
                    partidoField.style.display = 'block';
                    parlamentarFields.style.display = 'block';
                } else {
                    partidoField.style.display = 'none';
                    parlamentarFields.style.display = 'none';
                }
            }

            if (perfilSelect) {
                perfilSelect.addEventListener('change', toggleFields);
                // Executar na inicialização para manter estado se houver old values
                toggleFields();
            }
        });
    </script>
    @endpush
</x-layouts.app>