<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Pública - Proposição {{ $informacoesPublicas['id'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #003366, #0066cc);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        .info-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        .info-value {
            color: #212529;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 3rem;
        }
        .status-protocolado { background-color: #28a745; }
        .status-assinado { background-color: #17a2b8; }
        .status-analise { background-color: #ffc107; color: #212529; }
        .status-aguardando { background-color: #fd7e14; }
        .status-elaboracao { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="text-center">
                <h1 class="mb-1">Consulta Pública de Proposição</h1>
                <p class="mb-0">Sistema Legislativo Municipal - Transparência</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Informações Principais -->
                <div class="info-card">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h2 class="text-primary mb-0">
                                {{ $informacoesPublicas['tipo'] }}
                                @if($informacoesPublicas['numero_protocolo'])
                                    Nº {{ $informacoesPublicas['numero_protocolo'] }}
                                @else
                                    #{{ $informacoesPublicas['id'] }}
                                @endif
                            </h2>
                        </div>
                        <div>
                            @php
                                $statusClass = 'status-elaboracao';
                                if(str_contains($informacoesPublicas['status'], 'Protocolado')) $statusClass = 'status-protocolado';
                                elseif(str_contains($informacoesPublicas['status'], 'Assinado')) $statusClass = 'status-assinado';
                                elseif(str_contains($informacoesPublicas['status'], 'Análise')) $statusClass = 'status-analise';
                                elseif(str_contains($informacoesPublicas['status'], 'Aguardando')) $statusClass = 'status-aguardando';
                            @endphp
                            <span class="badge {{ $statusClass }} status-badge">
                                {{ $informacoesPublicas['status'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Ementa -->
                    <div class="info-label">Ementa:</div>
                    <div class="info-value">
                        <strong>{{ $informacoesPublicas['ementa'] ?: 'Ementa não disponível' }}</strong>
                    </div>

                    <!-- Autor -->
                    @if($informacoesPublicas['autor_nome'])
                        <div class="info-label">Autor:</div>
                        <div class="info-value">{{ $informacoesPublicas['autor_nome'] }}</div>
                    @endif

                    <!-- Data de Criação -->
                    <div class="info-label">Data de Criação:</div>
                    <div class="info-value">{{ $informacoesPublicas['data_criacao'] }}</div>

                    <!-- Assinatura Digital -->
                    @if($informacoesPublicas['assinado'])
                        <div class="info-label">Assinatura Digital:</div>
                        <div class="info-value">
                            <i class="text-success">✓</i> Documento assinado digitalmente
                            @if($informacoesPublicas['data_assinatura'])
                                em {{ $informacoesPublicas['data_assinatura'] }}
                            @endif
                        </div>
                    @endif

                    <!-- Data do Protocolo -->
                    @if($informacoesPublicas['data_protocolo'])
                        <div class="info-label">Data do Protocolo:</div>
                        <div class="info-value">{{ $informacoesPublicas['data_protocolo'] }}</div>
                    @endif
                </div>

                <!-- Download do Documento -->
                @if($informacoesPublicas['tem_pdf'])
                <div class="info-card">
                    <h4 class="text-primary mb-3">
                        <i>📄</i> Documento Oficial
                    </h4>
                    <p class="mb-3">O documento oficial com assinatura digital e protocolo está disponível para visualização:</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ $informacoesPublicas['pdf_url'] }}" 
                           target="_blank" 
                           class="btn btn-primary btn-lg">
                            <i>👁️</i> Visualizar PDF
                        </a>
                        <a href="{{ $informacoesPublicas['pdf_url'] }}?download=1" 
                           class="btn btn-outline-primary btn-lg">
                            <i>⬇️</i> Baixar PDF
                        </a>
                    </div>
                    <small class="text-muted d-block mt-2">
                        O documento contém assinatura digital e QR Code para verificação de autenticidade.
                    </small>
                </div>
                @endif

                <!-- Informações sobre Transparência -->
                <div class="info-card">
                    <h4 class="text-success mb-3">
                        <i>🔒</i> Informações Verificadas
                    </h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="text-success">✓</i> Proposição autêntica do sistema legislativo</li>
                        <li class="mb-2"><i class="text-success">✓</i> Dados atualizados em tempo real</li>
                        @if($informacoesPublicas['assinado'])
                            <li class="mb-2"><i class="text-success">✓</i> Assinatura digital verificada</li>
                        @endif
                        @if($informacoesPublicas['numero_protocolo'])
                            <li class="mb-2"><i class="text-success">✓</i> Documento protocolado oficialmente</li>
                        @endif
                    </ul>
                </div>

                <!-- Instruções -->
                <div class="info-card">
                    <h5 class="text-primary mb-3">Como utilizar esta consulta</h5>
                    <p>Esta página permite verificar a autenticidade e o status atual da proposição através do QR Code presente no documento oficial.</p>
                    <p class="mb-0"><strong>ID da Consulta:</strong> {{ $informacoesPublicas['id'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p class="mb-0">Sistema Legislativo Municipal - Consulta gerada em {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>