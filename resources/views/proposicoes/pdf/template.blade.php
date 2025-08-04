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
            margin-top: 80px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 300px;
            margin: 50px auto 10px auto;
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
    <!-- Marca d'água -->
    <div class="watermark">PARA ASSINATURA</div>
    
    <!-- Cabeçalho -->
    <div class="header">
        <h1>{{ strtoupper($proposicao->tipo ?? 'Proposição') }}</h1>
        <h2>Sistema Legislativo Municipal</h2>
    </div>
    
    <!-- Informações da Proposição -->
    <div class="info-box">
        <div class="info-row">
            <div class="info-label">Proposição:</div>
            <div class="info-value">#{{ $proposicao->id }}</div>
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
        <div class="signature-line"></div>
        <p><strong>{{ $proposicao->autor?->name ?? 'Autor da Proposição' }}</strong></p>
        <p>Assinatura Digital</p>
        <p><small>Data: _____ / _____ / _____</small></p>
    </div>
    
    <!-- Rodapé -->
    <div class="footer">
        <p>Este documento foi gerado automaticamente pelo Sistema Legislativo Municipal</p>
        <p>Proposição ID: {{ $proposicao->id }} | Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>