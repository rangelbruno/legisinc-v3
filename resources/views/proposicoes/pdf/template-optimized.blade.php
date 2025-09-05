<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proposição {{ $proposicao->id }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c5282;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #2c5282;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 10pt;
            color: #666;
        }
        
        .documento-info {
            margin: 20px 0;
            background-color: #f7fafc;
            padding: 15px;
            border-left: 4px solid #4299e1;
        }
        
        .documento-info h2 {
            font-size: 14pt;
            margin: 0 0 10px 0;
            color: #2c5282;
        }
        
        .documento-info p {
            margin: 5px 0;
            font-size: 11pt;
        }
        
        .content {
            margin: 20px 0;
            text-align: justify;
            line-height: 1.6;
        }
        
        .content h3 {
            font-size: 13pt;
            color: #2c5282;
            margin: 20px 0 10px 0;
        }
        
        .content p {
            margin: 10px 0;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            font-size: 10pt;
            color: #666;
        }
        
        .signature-area {
            margin-top: 50px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 300px;
            margin: 0 auto 5px auto;
        }
        
        .metadata {
            font-size: 9pt;
            color: #666;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        
        /* Otimizações para impressão */
        @media print {
            body { 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .header { page-break-inside: avoid; }
            .documento-info { page-break-inside: avoid; }
            .signature-area { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CÂMARA MUNICIPAL DE CARAGUATATUBA</h1>
        <p>Praça da República, 40, Centro - Caraguatatuba/SP</p>
        <p>Telefone: (12) 3882-5588 | www.camaracaraguatatuba.sp.gov.br</p>
    </div>

    <div class="documento-info">
        <h2>{{ strtoupper($proposicao->tipo) }} @if($proposicao->numero_protocolo) Nº {{ $proposicao->numero_protocolo }} @else [AGUARDANDO PROTOCOLO] @endif</h2>
        
        @if($proposicao->ementa)
        <p><strong>EMENTA:</strong> {{ $proposicao->ementa }}</p>
        @endif
        
        @if($proposicao->autor)
        <p><strong>AUTOR:</strong> {{ $proposicao->autor->name }}
        @if($proposicao->autor->cargo_atual) - {{ $proposicao->autor->cargo_atual }} @endif</p>
        @endif
        
        @if($proposicao->data_protocolo)
        <p><strong>DATA PROTOCOLO:</strong> {{ $proposicao->data_protocolo->format('d/m/Y') }}</p>
        @endif
        
        <p><strong>STATUS:</strong> {{ ucfirst(str_replace('_', ' ', $proposicao->status)) }}</p>
    </div>

    <div class="content">
        @if($proposicao->tipo === 'mocao')
        <p><strong>A Câmara Municipal manifesta:</strong></p>
        @endif
        
        {!! nl2br(e($conteudo)) !!}
        
        @if($proposicao->tipo === 'mocao')
        <p style="margin-top: 30px;"><strong>Resolve dirigir a presente Moção.</strong></p>
        @endif
    </div>

    @if($proposicao->data_assinatura && $proposicao->codigo_validacao)
    <div class="signature-area digital-signature">
        <div style="border: 1px solid #ccc; padding: 15px; margin-top: 30px;">
            @php
                $validacaoService = app(\App\Services\AssinaturaValidacaoService::class);
                $textoAssinatura = $validacaoService->gerarTextoAssinaturaPadrao($proposicao);
            @endphp
            
            <div style="display: table; width: 100%;">
                <div style="display: table-cell; width: 70%; vertical-align: top;">
                    <p style="font-size: 9pt; line-height: 1.4; margin: 0;">
                        {!! nl2br(e($textoAssinatura)) !!}
                    </p>
                </div>
                
                @if($proposicao->qr_code_validacao)
                <div style="display: table-cell; width: 30%; text-align: right; vertical-align: top;">
                    <img src="data:image/png;base64,{{ $proposicao->qr_code_validacao }}" 
                         style="width: 80px; height: 80px;" 
                         alt="QR Code para validação">
                    <p style="font-size: 7pt; text-align: center; margin-top: 5px;">
                        Validação Digital
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @elseif($proposicao->data_assinatura)
    <div class="signature-area">
        <p><strong>DOCUMENTO ASSINADO DIGITALMENTE</strong></p>
        <p>Data da Assinatura: {{ $proposicao->data_assinatura->format('d/m/Y H:i:s') }}</p>
        @if($proposicao->certificado_digital)
        <p style="font-size: 8pt; color: #666;">Certificado: {{ substr($proposicao->certificado_digital, 0, 50) }}...</p>
        @endif
    </div>
    @else
    <div class="signature-area">
        <p>Caraguatatuba, {{ now()->format('d') }} de {{ now()->locale('pt_BR')->translatedFormat('F') }} de {{ now()->format('Y') }}.</p>
        <br><br>
        <div class="signature-line"></div>
        <p>{{ $proposicao->autor->name ?? 'Autor' }}</p>
        <p>{{ $proposicao->autor->cargo_atual ?? 'Parlamentar' }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
        @if($proposicao->id)
        <p>ID da Proposição: {{ $proposicao->id }}</p>
        @endif
    </div>

    <div class="metadata">
        <p><strong>METADADOS DO DOCUMENTO:</strong></p>
        <p>Gerado em: {{ now()->toISOString() }}</p>
        <p>Sistema: Legisinc v1.0</p>
        @if($proposicao->arquivo_path)
        <p>Baseado no arquivo: {{ basename($proposicao->arquivo_path) }}</p>
        @endif
    </div>
</body>
</html>

@php
function getMesExtenso($mes) {
    $meses = [
        1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
        5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
        9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
    ];
    return $meses[$mes] ?? 'indefinido';
}
@endphp