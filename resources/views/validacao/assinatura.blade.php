<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Assinatura Digital - Câmara Municipal de Caraguatatuba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .signature-validation {
            max-width: 800px;
            margin: 50px auto;
        }
        .validation-card {
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .result-valid {
            border-color: #28a745;
            background-color: #f8fff8;
        }
        .result-invalid {
            border-color: #dc3545;
            background-color: #fff8f8;
        }
        .qr-code img {
            max-width: 150px;
            height: auto;
        }
        .logo-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .validation-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container signature-validation">
        <div class="logo-header">
            <h2 class="text-primary">Câmara Municipal de Caraguatatuba</h2>
            <h4>Validação de Assinatura Digital</h4>
        </div>

        <div class="validation-card">
            <form method="POST" action="{{ route('validacao.assinatura.validar') }}">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <label for="codigo" class="form-label"><strong>Código de Validação:</strong></label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="codigo" 
                               name="codigo" 
                               placeholder="XXXX-XXXX-XXXX-XXXX" 
                               value="{{ old('codigo', $codigo) }}"
                               pattern="[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}"
                               style="font-family: monospace; font-size: 1.2em;"
                               required>
                        <div class="form-text">
                            Digite o código de 16 caracteres encontrado no documento assinado.
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            🔍 Validar Assinatura
                        </button>
                    </div>
                </div>
            </form>

            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($resultado)
            @if($resultado['valida'])
                <div class="validation-card result-valid">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="text-success">✅ Assinatura Digital Válida</h3>
                            
                            <div class="validation-details">
                                <h5>Detalhes do Documento:</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Tipo:</strong></td>
                                        <td>{{ $resultado['proposicao']['tipo'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Número:</strong></td>
                                        <td>{{ $resultado['proposicao']['numero'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ementa:</strong></td>
                                        <td>{{ $resultado['proposicao']['ementa'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Autor:</strong></td>
                                        <td>{{ $resultado['proposicao']['autor'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Data da Assinatura:</strong></td>
                                        <td>{{ $resultado['proposicao']['data_assinatura'] }}</td>
                                    </tr>
                                    @if($resultado['proposicao']['numero_protocolo'])
                                    <tr>
                                        <td><strong>Protocolo:</strong></td>
                                        <td>{{ $resultado['proposicao']['numero_protocolo'] }} 
                                            @if($resultado['proposicao']['data_protocolo'])
                                                ({{ $resultado['proposicao']['data_protocolo'] }})
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                                
                                <p class="text-muted mt-3">
                                    <small>Verificação realizada em: {{ $resultado['verificado_em'] }}</small>
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center qr-code">
                            <p><strong>QR Code de Validação:</strong></p>
                            <img src="{{ route('validacao.assinatura.qr', $codigo) }}" 
                                 alt="QR Code de Validação" 
                                 class="img-fluid border">
                            <div class="mt-2">
                                <a href="{{ route('validacao.assinatura.certificado', $codigo) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   target="_blank">
                                    📄 Certificado de Validação
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="validation-card result-invalid">
                    <h3 class="text-danger">❌ Código de Validação Inválido</h3>
                    <p>O código informado não foi encontrado ou não corresponde a uma assinatura digital válida.</p>
                    <p class="text-muted">Verifique se o código foi digitado corretamente e tente novamente.</p>
                </div>
            @endif
        @endif

        <div class="text-center mt-4">
            <p class="text-muted">
                <small>
                    Este sistema permite validar a autenticidade de documentos assinados digitalmente pela Câmara Municipal de Caraguatatuba.<br>
                    Para dúvidas, entre em contato: (12) 3882-5588 | www.camaracaraguatatuba.sp.gov.br
                </small>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-format code input
        document.getElementById('codigo').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^A-Z0-9]/g, '').toUpperCase();
            let formattedValue = '';
            
            for (let i = 0; i < value.length && i < 16; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += '-';
                }
                formattedValue += value[i];
            }
            
            e.target.value = formattedValue;
        });
    </script>
</body>
</html>