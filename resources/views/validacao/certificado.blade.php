<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Validação de Assinatura Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .certificate {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border: 3px solid #007bff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .certificate-header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .certificate-seal {
            width: 80px;
            height: 80px;
            border: 3px solid #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .document-info {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
        }
        .validation-info {
            background-color: #e8f5e8;
            border: 1px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer-info {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 0.9em;
            color: #6c757d;
        }
        .qr-section {
            text-align: center;
            margin: 20px 0;
        }
        @media print {
            body { background-color: white; }
            .btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificate">
            <div class="certificate-header">
                <div class="certificate-seal">
                    VÁLIDO
                </div>
                <h2 class="text-primary mb-1">Câmara Municipal de Caraguatatuba</h2>
                <h4>Certificado de Validação de Assinatura Digital</h4>
                <p class="text-muted">Estado de São Paulo - Brasil</p>
            </div>

            <div class="validation-info">
                <h5 class="text-success mb-3">✅ ASSINATURA DIGITAL VERIFICADA E VÁLIDA</h5>
                <p class="mb-0">
                    Este certificado atesta que o documento referenciado foi devidamente assinado digitalmente
                    e sua integridade foi verificada com sucesso em {{ $resultado['verificado_em'] }}.
                </p>
            </div>

            <div class="document-info">
                <h5>Informações do Documento Assinado:</h5>
                <table class="table table-sm">
                    <tr>
                        <td style="width: 25%;"><strong>Tipo do Documento:</strong></td>
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
                        <td><strong>Assinado por:</strong></td>
                        <td>{{ $resultado['proposicao']['autor'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>Data da Assinatura:</strong></td>
                        <td>{{ $resultado['proposicao']['data_assinatura'] }}</td>
                    </tr>
                    @if($resultado['proposicao']['numero_protocolo'])
                    <tr>
                        <td><strong>Número de Protocolo:</strong></td>
                        <td>{{ $resultado['proposicao']['numero_protocolo'] }}</td>
                    </tr>
                    @endif
                    @if($resultado['proposicao']['data_protocolo'])
                    <tr>
                        <td><strong>Data do Protocolo:</strong></td>
                        <td>{{ $resultado['proposicao']['data_protocolo'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <h5>Código de Validação:</h5>
                    <p style="font-family: monospace; font-size: 1.2em; font-weight: bold; color: #007bff;">
                        {{ $codigo }}
                    </p>
                    
                    <h5>URL de Verificação:</h5>
                    <p style="word-break: break-all; font-size: 0.9em;">
                        <a href="https://sistema.camaracaragua.sp.gov.br/conferir_assinatura?codigo={{ $codigo }}" target="_blank">
                            https://sistema.camaracaragua.sp.gov.br/conferir_assinatura?codigo={{ $codigo }}
                        </a>
                    </p>
                </div>
                
                <div class="col-md-4 qr-section">
                    <h6>QR Code para Verificação:</h6>
                    <img src="{{ route('validacao.assinatura.qr', $codigo) }}" 
                         alt="QR Code de Validação" 
                         style="width: 120px; height: 120px; border: 1px solid #dee2e6;">
                </div>
            </div>

            <div class="footer-info">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Câmara Municipal de Caraguatatuba</strong><br>
                        Praça da República, 40, Centro<br>
                        Caraguatatuba - SP<br>
                        CEP: 11660-000</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Contato:</strong><br>
                        Telefone: (12) 3882-5588<br>
                        Site: www.camaracaraguatatuba.sp.gov.br<br>
                        CNPJ: 50.444.108/0001-41</p>
                    </div>
                </div>
                
                <hr>
                
                <p class="text-center mb-0">
                    <small>
                        Este certificado foi gerado automaticamente pelo Sistema de Validação de Assinaturas Digitais.<br>
                        Para verificar sua autenticidade, acesse o link acima ou escaneie o QR Code.<br>
                        Certificado emitido em: {{ now()->format('d/m/Y H:i:s') }}
                    </small>
                </p>
            </div>

            <div class="text-center mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary me-2">🖨️ Imprimir Certificado</button>
                <a href="{{ route('validacao.assinatura.formulario') }}" class="btn btn-secondary">🔍 Nova Validação</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>