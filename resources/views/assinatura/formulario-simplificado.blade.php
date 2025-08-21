@extends('layouts.app')

@section('title', 'Assinatura Digital - Proposição #' . $proposicao->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-signature me-2"></i>
                        Assinatura Digital da Proposição #{{ $proposicao->id }}
                    </h4>
                </div>
                
                <div class="card-body">
                    <!-- Alertas -->
                    @if($errors->any())
                        <div class="alert alert-danger d-none" id="alert-errors">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success d-none" id="alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <!-- Formulário Simplificado -->
                        <div class="col-md-5">
                            <div class="card border-0">
                                <div class="card-body">
                                    <h5 class="mb-4">
                                        <i class="fas fa-lock me-2 text-primary"></i>
                                        Assinatura Digital
                                    </h5>
                                    
                                    <form action="{{ route('proposicoes.assinatura-digital.processar', $proposicao) }}" 
                                          method="POST" 
                                          id="formAssinatura"
                                          enctype="multipart/form-data">
                                        @csrf
                                        
                                        <!-- Tipo de Certificado -->
                                        <div class="mb-4">
                                            <label for="tipo_certificado" class="form-label fw-bold">
                                                Tipo de Certificado
                                            </label>
                                            <select class="form-select form-select-lg" 
                                                    id="tipo_certificado" 
                                                    name="tipo_certificado" 
                                                    required>
                                                <option value="">Selecione...</option>
                                                @foreach($tiposCertificado as $valor => $descricao)
                                                    <option value="{{ $valor }}">{{ $descricao }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Senha (para A1/A3) -->
                                        <div class="mb-4" id="campo_senha" style="display: none;">
                                            <label for="senha" class="form-label fw-bold">
                                                Senha do Certificado
                                            </label>
                                            <input type="password" 
                                                   class="form-control form-control-lg" 
                                                   id="senha" 
                                                   name="senha" 
                                                   placeholder="Digite a senha..."
                                                   autocomplete="new-password">
                                            <div class="form-text">
                                                Digite a senha do seu certificado digital
                                            </div>
                                        </div>

                                        <!-- Upload PFX (para PFX) -->
                                        <div class="mb-4" id="campo_pfx" style="display: none;">
                                            <label for="arquivo_pfx" class="form-label fw-bold">
                                                <i class="fas fa-file-upload me-2"></i>
                                                Arquivo de Certificado
                                            </label>
                                            <input type="file" 
                                                   class="form-control form-control-lg" 
                                                   id="arquivo_pfx" 
                                                   name="arquivo_pfx">
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Selecione um arquivo .pfx, .p12 ou qualquer arquivo para demonstração (máx. 2MB)
                                            </div>
                                        </div>

                                        <!-- Senha PFX (para PFX) -->
                                        <div class="mb-4" id="campo_senha_pfx" style="display: none;">
                                            <label for="senha_pfx" class="form-label fw-bold">
                                                Senha do Arquivo PFX
                                            </label>
                                            <input type="password" 
                                                   class="form-control form-control-lg" 
                                                   id="senha_pfx" 
                                                   name="senha_pfx" 
                                                   placeholder="Digite a senha do arquivo..."
                                                   autocomplete="new-password">
                                            <div class="form-text">
                                                Digite a senha do arquivo de certificado
                                            </div>
                                        </div>

                                        <!-- Informações da Assinatura -->
                                        <div class="alert alert-info mb-4">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Informações da Assinatura
                                            </h6>
                                            <hr>
                                            <p class="mb-1"><strong>Assinante:</strong> {{ Auth::user()->name }}</p>
                                            <p class="mb-1"><strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                                            <p class="mb-0"><strong>IP:</strong> {{ request()->ip() }}</p>
                                        </div>

                                        <!-- Botão de Assinatura -->
                                        <div class="d-grid gap-2">
                                            <button type="submit" 
                                                    class="btn btn-success btn-lg" 
                                                    id="btnAssinar">
                                                <i class="fas fa-signature me-2"></i>
                                                Assinar Documento
                                            </button>
                                            
                                            <a href="{{ route('proposicoes.show', $proposicao) }}" 
                                               class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>
                                                Voltar
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Preview do PDF e Exemplo de Assinatura -->
                        <div class="col-md-7">
                            <div class="card border-0">
                                <div class="card-body">
                                    <h5 class="mb-3">
                                        <i class="fas fa-file-pdf me-2 text-danger"></i>
                                        Documento para Assinatura
                                    </h5>
                                    
                                    <!-- Preview do PDF -->
                                    <div class="pdf-container mb-4" style="height: 400px; overflow: hidden; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                                        <iframe 
                                            src="{{ route('proposicoes.pdf-original', $proposicao) }}" 
                                            style="width: 100%; height: 100%; border: none;"
                                            title="PDF para Assinatura">
                                        </iframe>
                                    </div>
                                    
                                    <!-- Exemplo de Assinatura -->
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-eye me-2"></i>
                                                Exemplo de Assinatura Digital
                                            </h6>
                                            <hr>
                                            <div class="font-monospace small">
                                                <p class="mb-2">Formato compacto da assinatura digital:</p>
                                                <p class="mb-2">Assinado por <strong>{{ Auth::user()->name }}</strong> em {{ now()->format('d/m/Y H:i') }}.</p>
                                                <p class="mb-2">ID: <span class="text-primary"><strong>A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6</strong></span></p>
                                                <p class="mb-2">Checksum: <span class="text-muted">E99128EFEC1336C610367EBB364D6304</span></p>
                                                <p class="mb-0">Conforme art. 4º, II da Lei 14.063/2020.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoCertificado = document.getElementById('tipo_certificado');
    const campoSenha = document.getElementById('campo_senha');
    const campoPfx = document.getElementById('campo_pfx');
    const campoSenhaPfx = document.getElementById('campo_senha_pfx');
    const inputSenha = document.getElementById('senha');
    const inputPfx = document.getElementById('arquivo_pfx');
    const inputSenhaPfx = document.getElementById('senha_pfx');
    const formAssinatura = document.getElementById('formAssinatura');
    const btnAssinar = document.getElementById('btnAssinar');

    // Controlar exibição dos campos baseado no tipo
    tipoCertificado.addEventListener('change', function() {
        const tipo = this.value;
        
        // Ocultar todos os campos específicos
        campoSenha.style.display = 'none';
        campoPfx.style.display = 'none';
        campoSenhaPfx.style.display = 'none';
        
        // Limpar valores e remover required
        inputSenha.value = '';
        inputSenha.required = false;
        inputPfx.value = '';
        inputPfx.required = false;
        inputSenhaPfx.value = '';
        inputSenhaPfx.required = false;
        
        // Mostrar campos específicos baseado no tipo
        if (tipo === 'A1' || tipo === 'A3') {
            campoSenha.style.display = 'block';
            inputSenha.required = true;
            inputSenha.focus();
        } else if (tipo === 'PFX') {
            campoPfx.style.display = 'block';
            campoSenhaPfx.style.display = 'block';
            inputPfx.required = true;
            inputSenhaPfx.required = true;
        }
        
        // Habilitar botão apenas se tipo selecionado
        btnAssinar.disabled = !tipo;
        
        // Mostrar informações sobre o tipo selecionado
        mostrarInfoTipoCertificado(tipo);
    });

    // Mostrar informações sobre o tipo de certificado
    function mostrarInfoTipoCertificado(tipo) {
        let titulo = '';
        let texto = '';
        let icone = 'info';
        
        switch(tipo) {
            case 'A1':
                titulo = 'Certificado A1';
                texto = 'Certificado digital armazenado em arquivo no computador. Requer senha para uso.';
                icone = 'info';
                break;
            case 'A3':
                titulo = 'Certificado A3';
                texto = 'Certificado digital em cartão ou token físico. Requer inserção do dispositivo e senha.';
                icone = 'info';
                break;
            case 'PFX':
                titulo = 'Arquivo PFX/P12';
                texto = 'Certificado digital em arquivo .pfx ou .p12. Faça upload do arquivo e digite a senha.';
                icone = 'info';
                break;
            case 'SIMULADO':
                titulo = 'Assinatura Simulada';
                texto = 'Modo de desenvolvimento que simula uma assinatura digital real.';
                icone = 'warning';
                break;
        }
        
        if (tipo) {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: icone,
                confirmButtonText: 'Entendi',
                confirmButtonColor: '#3085d6',
                timer: tipo === 'SIMULADO' ? 3000 : null,
                timerProgressBar: true
            });
        }
    }

    // Processar envio do formulário com SweetAlert
    formAssinatura.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar campos
        if (!formAssinatura.checkValidity()) {
            Swal.fire({
                title: 'Campos obrigatórios',
                text: 'Por favor, preencha todos os campos obrigatórios.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#f39c12'
            });
            return;
        }
        
        // Confirmação com SweetAlert2
        Swal.fire({
            title: 'Confirmar Assinatura Digital',
            html: `
                <div class="text-start">
                    <p><strong>Documento:</strong> Proposição #{{ $proposicao->id }}</p>
                    <p><strong>Tipo:</strong> ${tipoCertificado.options[tipoCertificado.selectedIndex].text}</p>
                    <p><strong>Assinante:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Data/Hora:</strong> ${new Date().toLocaleString('pt-BR')}</p>
                    <hr>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i><strong>Atenção:</strong></p>
                    <p class="small">Após a assinatura, o documento não poderá ser alterado e terá validade jurídica conforme a Lei 14.063/2020.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-signature me-2"></i>Confirmar Assinatura',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            focusConfirm: false,
            reverseButtons: true,
            width: '650px',
            customClass: {
                popup: 'swal2-large-popup',
                confirmButton: 'btn btn-success btn-lg',
                cancelButton: 'btn btn-danger btn-lg'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar progresso da assinatura
                Swal.fire({
                    title: 'Processando Assinatura...',
                    html: `
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="spinner-border text-primary me-3" role="status"></div>
                            <span>Gerando assinatura digital...</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 100%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Gerando identificador único e checksum SHA-256...
                        </small>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Desabilitar botão e atualizar texto
                btnAssinar.disabled = true;
                btnAssinar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Assinando...';
                
                // Simular delay para mostrar o progresso
                setTimeout(() => {
                    formAssinatura.submit();
                }, 1500);
            }
        });
    });

    // Estado inicial
    btnAssinar.disabled = true;
    
    // Verificar e mostrar mensagens de erro ou sucesso
    @if($errors->any())
        Swal.fire({
            title: 'Erro na Validação',
            html: `
                <div class="text-start">
                    <ul class="list-unstyled">
                        @foreach($errors->all() as $error)
                            <li><i class="fas fa-times-circle text-danger me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            icon: 'error',
            confirmButtonText: 'Corrigir',
            confirmButtonColor: '#dc3545'
        });
    @endif

    @if(session('success'))
        Swal.fire({
            title: 'Assinatura Realizada!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'Visualizar Documento',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route('proposicoes.show', $proposicao) }}';
            }
        });
    @else
        // Mostrar welcome message apenas se não houver mensagens de erro/sucesso
        @if(!$errors->any())
            Swal.fire({
                title: 'Assinatura Digital',
                text: 'Selecione o tipo de certificado para iniciar o processo de assinatura digital.',
                icon: 'info',
                confirmButtonText: 'Iniciar',
                confirmButtonColor: '#3085d6',
                timer: 4000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        @endif
    @endif
});
</script>
@endpush

@push('styles')
<style>
.card {
    border-radius: 0.5rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

.form-select-lg {
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d9ff;
}

.font-monospace {
    font-family: 'Courier New', monospace;
    line-height: 1.6;
}

.pdf-container {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
}

/* Estilos personalizados para SweetAlert maior */
.swal2-large-popup {
    font-size: 1.1rem;
}

.swal2-large-popup .swal2-title {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.swal2-large-popup .swal2-html-container {
    font-size: 1.05rem;
    line-height: 1.6;
    margin: 1rem 0;
}

.swal2-large-popup .swal2-html-container p {
    margin-bottom: 0.75rem;
}

.swal2-large-popup .swal2-html-container hr {
    margin: 1.5rem 0;
    border-color: #e9ecef;
}

.swal2-large-popup .swal2-html-container .text-warning {
    font-size: 1.1rem;
    font-weight: 500;
}

.swal2-large-popup .swal2-html-container .small {
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Botões maiores e mais espaçados */
.swal2-large-popup .swal2-actions {
    margin-top: 2rem;
}

.swal2-large-popup .swal2-confirm,
.swal2-large-popup .swal2-cancel {
    padding: 0.875rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 0.5rem;
    min-width: 160px;
    margin: 0 0.5rem;
}

.swal2-large-popup .swal2-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.swal2-large-popup .swal2-cancel:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}
</style>
@endpush