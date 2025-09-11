@extends('components.layouts.app')

@section('title', 'Certificado Digital')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="5" y="11" width="14" height="10" rx="2"/>
                        <circle cx="12" cy="16" r="1"/>
                        <path d="m8 11v-4a4 4 0 0 1 8 0v4"/>
                    </svg>
                    Certificado Digital
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10"/>
                        </svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <circle cx="12" cy="12" r="9"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row g-4">
            <!-- Status do Certificado -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Status do Certificado Digital</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div>
                                        @php
                                            $status = $user->getStatusCertificadoDigital();
                                            $badgeClass = match($status) {
                                                'Ativo' => 'bg-green',
                                                'Expirado' => 'bg-red',
                                                'Inativo' => 'bg-yellow',
                                                'Não cadastrado' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </div>
                                </div>

                                @if($user->certificado_digital_nome)
                                    <div class="mb-3">
                                        <label class="form-label">Arquivo</label>
                                        <div class="text-muted">{{ $user->certificado_digital_nome }}</div>
                                    </div>
                                @endif

                                @if($user->certificado_digital_upload_em)
                                    <div class="mb-3">
                                        <label class="form-label">Upload em</label>
                                        <div class="text-muted">{{ $user->certificado_digital_upload_em->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                @if($user->certificado_digital_cn)
                                    <div class="mb-3">
                                        <label class="form-label">Nome no Certificado</label>
                                        <div class="text-muted">{{ $user->certificado_digital_cn }}</div>
                                    </div>
                                @endif

                                @if($user->certificado_digital_validade)
                                    <div class="mb-3">
                                        <label class="form-label">Validade</label>
                                        <div class="text-muted">
                                            {{ $user->certificado_digital_validade->format('d/m/Y') }}
                                            @if($user->certificadoProximoVencimento())
                                                <span class="badge bg-yellow ms-2">Vence em {{ $user->getDiasParaExpiracaoCertificado() }} dias</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if($user->certificado_digital_path)
                                    <div class="mb-3">
                                        <label class="form-label">Ações</label>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="testarCertificado()">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="12" cy="12" r="9"/>
                                                    <path d="m9 12l2 2l4 -4"/>
                                                </svg>
                                                Testar
                                            </button>
                                            
                                            @if($user->certificado_digital_ativo)
                                                <form method="POST" action="{{ route('certificado-digital.toggle') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="ativo" value="0">
                                                    <button type="submit" class="btn btn-outline-warning">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <circle cx="12" cy="12" r="9"/>
                                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                                        </svg>
                                                        Desativar
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('certificado-digital.toggle') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="ativo" value="1">
                                                    <button type="submit" class="btn btn-outline-success">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <circle cx="12" cy="12" r="9"/>
                                                            <path d="m9 12l2 2l4 -4"/>
                                                        </svg>
                                                        Ativar
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button" class="btn btn-outline-danger" onclick="removerCertificado()">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <line x1="4" y1="7" x2="20" y2="7"/>
                                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                                    <path d="m5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                    <path d="m9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                </svg>
                                                Remover
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload de Certificado -->
            @if(!$user->certificado_digital_path || !$user->certificado_digital_ativo)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if($user->certificado_digital_path)
                                Substituir Certificado Digital
                            @else
                                Cadastrar Certificado Digital
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('certificado-digital.upload') }}" enctype="multipart/form-data" id="form-certificado">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Arquivo do Certificado (.pfx ou .p12)</label>
                                <input type="file" class="form-control" name="certificado" accept=".pfx,.p12" required>
                                <div class="form-hint">
                                    Selecione seu certificado digital no formato .pfx ou .p12. O arquivo deve ter no máximo 5MB.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Senha do Certificado</label>
                                <input type="password" class="form-control" name="senha_teste" required placeholder="Digite a senha do certificado">
                                <div class="form-hint">
                                    A senha será usada para validar o certificado.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="salvar_senha_substituicao" value="1" checked>
                                    <span class="form-check-label">
                                        <strong>Salvar senha do certificado (criptografada)</strong>
                                    </span>
                                </label>
                                <div class="form-hint">
                                    Ao marcar esta opção, a senha será salva de forma segura e criptografada. 
                                    Isso permitirá assinar documentos sem precisar digitar a senha novamente.
                                </div>
                            </div>

                            <div class="btn-list">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                        <circle cx="12" cy="13" r="2"/>
                                        <path d="m9.5 12.5l5 5"/>
                                        <path d="m10.5 17.5l4 -4"/>
                                    </svg>
                                    @if($user->certificado_digital_path) Substituir @else Cadastrar @endif Certificado
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informações Adicionais -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informações sobre Certificado Digital</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>O que é?</h4>
                                <p class="text-muted">
                                    O certificado digital é um documento eletrônico que funciona como uma identidade virtual, 
                                    garantindo autenticidade e validade jurídica aos documentos digitais que você assina.
                                </p>
                                
                                <h4>Como usar?</h4>
                                <p class="text-muted">
                                    Após cadastrar seu certificado, ele será usado automaticamente para assinar digitalmente 
                                    as proposições. Você precisará apenas informar a senha do certificado no momento da assinatura.
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h4>Segurança</h4>
                                <p class="text-muted">
                                    Seu certificado é armazenado de forma segura no servidor e a senha não é salva. 
                                    Apenas você tem acesso ao seu certificado através do seu login no sistema.
                                </p>
                                
                                <h4>Formatos aceitos</h4>
                                <p class="text-muted">
                                    <strong>Extensões:</strong> .pfx, .p12<br>
                                    <strong>Tamanho máximo:</strong> 5MB<br>
                                    <strong>Validade:</strong> Verificada automaticamente
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Teste do Certificado -->
<div class="modal modal-blur fade" id="modal-teste-certificado" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Testar Certificado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-teste-certificado">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Senha do Certificado</label>
                        <input type="password" class="form-control" name="senha_teste" required placeholder="Digite a senha">
                    </div>
                    <div id="resultado-teste" class="d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Testar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function testarCertificado() {
    const modal = new bootstrap.Modal(document.getElementById('modal-teste-certificado'));
    modal.show();
    
    document.getElementById('form-teste-certificado').onsubmit = function(e) {
        e.preventDefault();
        
        const senha = this.senha_teste.value;
        const resultadoDiv = document.getElementById('resultado-teste');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Testando...';
        
        fetch('{{ route("certificado-digital.testar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ senha_teste: senha })
        })
        .then(response => response.json())
        .then(data => {
            resultadoDiv.className = 'd-block alert ' + (data.success ? 'alert-success' : 'alert-danger');
            resultadoDiv.innerHTML = data.message;
            
            if (data.success && data.dados) {
                resultadoDiv.innerHTML += '<br><small><strong>Nome:</strong> ' + data.dados.cn + 
                                         '<br><strong>Validade:</strong> ' + data.dados.validade + '</small>';
            }
            
            submitBtn.disabled = false;
            submitBtn.textContent = 'Testar';
        })
        .catch(error => {
            resultadoDiv.className = 'd-block alert alert-danger';
            resultadoDiv.textContent = 'Erro ao testar certificado.';
            
            submitBtn.disabled = false;
            submitBtn.textContent = 'Testar';
        });
    };
}

function removerCertificado() {
    if (confirm('Tem certeza que deseja remover o certificado digital? Esta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("certificado-digital.remover") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        
        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}

// Loading no upload
document.getElementById('form-certificado').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1" role="status"></div>Processando...';
});
</script>
@endpush