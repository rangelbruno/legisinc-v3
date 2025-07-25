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
                        <img src="{{ isset($parlamentar['foto']) && $parlamentar['foto'] ? asset('storage/parlamentares/fotos/' . $parlamentar['foto']) : asset('assets/media/avatars/300-1.jpg') }}" alt="Preview da foto" id="foto-preview" class="symbol-label" />
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
                        <!--begin::Select-->
                        <select name="partido" class="form-select mb-2" required>
                            <option value="">Selecione o partido</option>
                            @foreach($partidos as $sigla => $nome)
                                <option value="{{ $sigla }}" {{ old('partido', isset($parlamentar['partido']) ? $parlamentar['partido'] : '') == $sigla ? 'selected' : '' }}>{{ $sigla }} - {{ $nome }}</option>
                            @endforeach
                        </select>
                        <!--end::Select-->
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
});
</script>
<!--end::Javascript-->