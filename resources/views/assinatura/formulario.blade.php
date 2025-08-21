@extends('layouts.app')

@section('title', 'Assinatura Digital - Proposição #' . $proposicao->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-signature me-2"></i>
                        Assinatura Digital da Proposição
                    </h4>
                    <p class="card-subtitle text-muted">
                        Proposição #{{ $proposicao->id }} - {{ $proposicao->titulo }}
                    </p>
                </div>
                
                <div class="card-body">
                    <!-- Alertas -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <!-- Formulário de Assinatura -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-certificate me-2"></i>
                                        Configurações da Assinatura
                                    </h5>
                                </div>
                                
                                <div class="card-body">
                                    <form action="{{ route('proposicoes.assinatura-digital.processar', $proposicao) }}" method="POST" enctype="multipart/form-data" id="formAssinatura">
                                        @csrf
                                        
                                        <!-- Tipo de Certificado -->
                                        <div class="mb-3">
                                            <label for="tipo_certificado" class="form-label">
                                                <i class="fas fa-key me-1"></i>
                                                Tipo de Certificado <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('tipo_certificado') is-invalid @enderror" 
                                                    id="tipo_certificado" 
                                                    name="tipo_certificado" 
                                                    required>
                                                <option value="">Selecione o tipo de certificado</option>
                                                @foreach($tiposCertificado as $valor => $descricao)
                                                    <option value="{{ $valor }}" {{ old('tipo_certificado') == $valor ? 'selected' : '' }}>
                                                        {{ $descricao }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tipo_certificado')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Nome do Assinante -->
                                        <div class="mb-3">
                                            <label for="nome_assinante" class="form-label">
                                                <i class="fas fa-user me-1"></i>
                                                Nome do Assinante <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('nome_assinante') is-invalid @enderror" 
                                                   id="nome_assinante" 
                                                   name="nome_assinante" 
                                                   value="{{ old('nome_assinante', Auth::user()->name) }}" 
                                                   required>
                                            @error('nome_assinante')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- PIN (para A1/A3) -->
                                        <div class="mb-3" id="campo_pin" style="display: none;">
                                            <label for="pin" class="form-label">
                                                <i class="fas fa-lock me-1"></i>
                                                PIN do Certificado <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" 
                                                   class="form-control @error('pin') is-invalid @enderror" 
                                                   id="pin" 
                                                   name="pin" 
                                                   minlength="4"
                                                   autocomplete="new-password">
                                            <div class="form-text">
                                                Digite o PIN do seu cartão/token de certificado digital
                                            </div>
                                            @error('pin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Arquivo PFX (para PFX) -->
                                        <div class="mb-3" id="campo_pfx" style="display: none;">
                                            <label for="arquivo_pfx" class="form-label">
                                                <i class="fas fa-file-upload me-1"></i>
                                                Arquivo de Certificado (.pfx/.p12) <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" 
                                                   class="form-control @error('arquivo_pfx') is-invalid @enderror" 
                                                   id="arquivo_pfx" 
                                                   name="arquivo_pfx" 
                                                   accept=".pfx,.p12">
                                            <div class="form-text">
                                                Selecione seu arquivo de certificado digital (.pfx ou .p12)
                                            </div>
                                            @error('arquivo_pfx')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Senha PFX (para PFX) -->
                                        <div class="mb-3" id="campo_senha_pfx" style="display: none;">
                                            <label for="senha_pfx" class="form-label">
                                                <i class="fas fa-key me-1"></i>
                                                Senha do Certificado <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" 
                                                   class="form-control @error('senha_pfx') is-invalid @enderror" 
                                                   id="senha_pfx" 
                                                   name="senha_pfx"
                                                   autocomplete="new-password">
                                            <div class="form-text">
                                                Digite a senha do arquivo de certificado
                                            </div>
                                            @error('senha_pfx')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Protocolo -->
                                        <div class="mb-3">
                                            <label for="protocolo" class="form-label">
                                                <i class="fas fa-hashtag me-1"></i>
                                                Número do Protocolo
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('protocolo') is-invalid @enderror" 
                                                   id="protocolo" 
                                                   name="protocolo" 
                                                   value="{{ old('protocolo') }}" 
                                                   placeholder="Ex: 2024/001234">
                                            <div class="form-text">
                                                Número do protocolo para identificação da proposição
                                            </div>
                                            @error('protocolo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Observações -->
                                        <div class="mb-3">
                                            <label for="observacoes" class="form-label">
                                                <i class="fas fa-comment me-1"></i>
                                                Observações
                                            </label>
                                            <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                                      id="observacoes" 
                                                      name="observacoes" 
                                                      rows="3" 
                                                      placeholder="Observações adicionais sobre a assinatura...">{{ old('observacoes') }}</textarea>
                                            @error('observacoes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Botões -->
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-success" id="btnAssinar">
                                                <i class="fas fa-signature me-2"></i>
                                                Assinar Documento
                                            </button>
                                            
                                            <a href="{{ route('proposicoes.show', $proposicao) }}" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>
                                                Voltar
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Preview do PDF -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Documento para Assinatura
                                    </h5>
                                    <div class="card-tools">
                                        <a href="{{ route('proposicoes.pdf-original', $proposicao) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            Abrir em Nova Aba
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="card-body p-0">
                                    <div class="pdf-container" style="height: 500px; overflow: hidden;">
                                        <iframe 
                                            src="{{ route('proposicoes.pdf-original', $proposicao) }}" 
                                            style="width: 100%; height: 100%; border: none;"
                                            title="PDF para Assinatura">
                                        </iframe>
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

<!-- Modal de Confirmação -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirmar Assinatura
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja assinar digitalmente esta proposição?</p>
                <p class="text-muted small">
                    <strong>Atenção:</strong> Após a assinatura, o documento será enviado para o protocolo 
                    e não poderá ser alterado.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmarAssinatura">
                    <i class="fas fa-check me-2"></i>
                    Confirmar Assinatura
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoCertificado = document.getElementById('tipo_certificado');
    const campoPin = document.getElementById('campo_pin');
    const campoPfx = document.getElementById('campo_pfx');
    const campoSenhaPfx = document.getElementById('campo_senha_pfx');
    const formAssinatura = document.getElementById('formAssinatura');
    const btnAssinar = document.getElementById('btnAssinar');
    const modalConfirmacao = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
    const btnConfirmarAssinatura = document.getElementById('btnConfirmarAssinatura');

    // Controlar exibição dos campos baseado no tipo de certificado
    tipoCertificado.addEventListener('change', function() {
        const tipo = this.value;
        
        // Ocultar todos os campos específicos
        campoPin.style.display = 'none';
        campoPfx.style.display = 'none';
        campoSenhaPfx.style.display = 'none';
        
        // Limpar e remover required dos campos ocultos
        const inputPin = campoPin.querySelector('input');
        const inputPfx = campoPfx.querySelector('input');
        const inputSenhaPfx = campoSenhaPfx.querySelector('input');
        
        // Limpar valores dos campos que serão ocultados
        inputPin.value = '';
        inputPin.required = false;
        inputPfx.value = '';
        inputPfx.required = false;
        inputSenhaPfx.value = '';
        inputSenhaPfx.required = false;
        
        // Mostrar campos específicos baseado no tipo
        if (tipo === 'A1' || tipo === 'A3') {
            campoPin.style.display = 'block';
            inputPin.required = true;
        } else if (tipo === 'PFX') {
            campoPfx.style.display = 'block';
            campoSenhaPfx.style.display = 'block';
            inputPfx.required = true;
            inputSenhaPfx.required = true;
        }
    });

    // Interceptar envio do formulário para mostrar confirmação
    formAssinatura.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar se todos os campos obrigatórios estão preenchidos
        if (!formAssinatura.checkValidity()) {
            formAssinatura.reportValidity();
            return;
        }
        
        // Mostrar modal de confirmação
        modalConfirmacao.show();
    });

    // Confirmar assinatura
    btnConfirmarAssinatura.addEventListener('click', function() {
        // Desabilitar botão para evitar duplo clique
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';
        
        // Fechar modal
        modalConfirmacao.hide();
        
        // Enviar formulário
        formAssinatura.submit();
    });

    // Mostrar campos baseado no valor atual (se houver erro de validação)
    if (tipoCertificado.value) {
        tipoCertificado.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush

@push('styles')
<style>
.pdf-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.card-header .card-tools {
    float: right;
}

.form-label {
    font-weight: 500;
}

.alert ul {
    padding-left: 1.2rem;
}

.btn {
    font-weight: 500;
}

.modal-header .modal-title {
    color: #495057;
}
</style>
@endpush
