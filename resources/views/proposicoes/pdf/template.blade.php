<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $proposicao->tipo }} - {{ $proposicao->ementa }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #003366;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #003366;
            font-size: 18px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }
        
        .header h2 {
            color: #666;
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #495057;
        }
        
        .info-value {
            flex: 1;
            color: #212529;
        }
        
        .content {
            margin: 30px 0;
            text-align: justify;
            line-height: 1.8;
        }
        
        .content h3 {
            color: #003366;
            font-size: 14px;
            margin: 20px 0 10px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
        
        .signature-area {
            margin-top: 50px;
            position: relative;
            height: 200px;
        }
        
        .signature-vertical {
            position: absolute;
            right: 20px;
            top: 0;
            width: 150px;
            height: 180px;
            border: 2px solid #003366;
            border-radius: 8px;
            padding: 10px;
            font-size: 9px;
            background-color: #f8f9fa;
        }
        
        .signature-vertical h4 {
            margin: 0 0 10px 0;
            font-size: 10px;
            color: #003366;
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .signature-info {
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin: 10px 0 5px 0;
        }
        
        .qr-code-area {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 80px;
            height: 80px;
            border: 1px solid #ccc;
            padding: 5px;
            background-color: white;
            text-align: center;
        }
        
        .qr-placeholder {
            width: 100%;
            height: 60px;
            background: linear-gradient(45deg, #000 25%, transparent 25%), 
                        linear-gradient(-45deg, #000 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #000 75%), 
                        linear-gradient(-45deg, transparent 75%, #000 75%);
            background-size: 4px 4px;
            background-position: 0 0, 0 2px, 2px -2px, -2px 0px;
            margin-bottom: 2px;
        }
        
        .qr-text {
            font-size: 6px;
            color: #666;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 48px;
            color: rgba(0, 51, 102, 0.1);
            z-index: -1;
            font-weight: bold;
        }
        
        @page {
            margin: 2cm;
        }
    </style>
</head>
<body>
    <!-- Marca d'água dinâmica -->
    <div class="watermark">
        @if($proposicao->status == 'protocolado')
            DOCUMENTO OFICIAL
        @elseif($proposicao->assinatura_digital)
            DOCUMENTO ASSINADO
        @else
            PARA ASSINATURA
        @endif
    </div>
    
    <!-- Cabeçalho -->
    <div class="header">
        <h1>{{ strtoupper($proposicao->tipo ?? 'Proposição') }}
            @if($proposicao->numero_protocolo)
                Nº {{ $proposicao->numero_protocolo }}
            @endif
        </h1>
        <h2>Sistema Legislativo Municipal</h2>
        @if($proposicao->status == 'protocolado' && $proposicao->data_protocolo)
            <p style="margin: 5px 0; font-size: 12px; color: #003366;">
                <strong>Protocolado em:</strong> {{ \Carbon\Carbon::parse($proposicao->data_protocolo)->format('d/m/Y H:i') }}
            </p>
        @endif
    </div>
    
    <!-- Informações da Proposição -->
    <div class="info-box">
        <div class="info-row">
            <div class="info-label">Proposição:</div>
            <div class="info-value">
                @if($proposicao->numero_protocolo)
                    {{ $proposicao->numero_protocolo }}
                @else
                    #{{ $proposicao->id }} (Aguardando Protocolo)
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Tipo:</div>
            <div class="info-value">{{ $proposicao->tipo }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Autor:</div>
            <div class="info-value">{{ $proposicao->autor?->name ?? 'Não informado' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Data:</div>
            <div class="info-value">{{ $proposicao->created_at?->format('d/m/Y H:i') ?? 'Não informada' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $proposicao->status)) }}</div>
        </div>
    </div>
    
    <!-- Ementa -->
    @if($proposicao->ementa)
    <div>
        <h3>Ementa</h3>
        <p>{{ $proposicao->ementa }}</p>
    </div>
    @endif
    
    <!-- Conteúdo -->
    <div class="content">
        <h3>Conteúdo da Proposição</h3>
        @if($conteudo)
            <div>{!! nl2br(e($conteudo)) !!}</div>
        @else
            <p><em>Conteúdo não disponível</em></p>
        @endif
    </div>
    
    <!-- Área de Assinatura -->
    <div class="signature-area">
        <!-- Assinatura Vertical na Lateral -->
        <div class="signature-vertical">
            <h4>ASSINATURA DIGITAL</h4>
            
            <div class="signature-info">
                <strong>Autor:</strong><br>
                {{ $proposicao->autor?->name ?? 'Autor da Proposição' }}
            </div>
            
            <div class="signature-info">
                <strong>Cargo:</strong><br>
                {{ $proposicao->autor?->cargo ?? 'Parlamentar' }}
            </div>
            
            @if($proposicao->data_assinatura)
            <div class="signature-info">
                <strong>Assinado em:</strong><br>
                {{ \Carbon\Carbon::parse($proposicao->data_assinatura)->format('d/m/Y H:i') }}
            </div>
            @else
            <div class="signature-info">
                <strong>Status:</strong><br>
                Aguardando Assinatura
            </div>
            @endif
            
            @if($proposicao->assinatura_digital)
            <div class="signature-info">
                <strong>Hash:</strong><br>
                <small style="font-size: 7px; word-break: break-all;">{{ substr($proposicao->assinatura_digital, 0, 20) }}...</small>
            </div>
            @endif
            
            <div class="signature-line"></div>
            <div style="text-align: center; font-size: 8px; color: #666;">
                Documento Válido
            </div>
        </div>
        
        <!-- QR Code para Consulta -->
        <div class="qr-code-area">
            @php
                $qrService = app(\App\Services\QRCodeService::class);
                $qrUrl = $qrService->gerarQRCodeProposicao($proposicao->id, 70);
            @endphp
            <img src="{{ $qrUrl }}" alt="QR Code" style="width: 70px; height: 70px; margin-bottom: 2px;">
            <div class="qr-text">Consulta: ID {{ $proposicao->id }}</div>
        </div>
    </div>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>Este documento foi gerado automaticamente pelo Sistema Legislativo Municipal</p>
        <p>Proposição ID: {{ $proposicao->id }} | Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>