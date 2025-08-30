<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento Protocolado - {{ $proposicao->tipo ?? 'Proposição' }}</title>
    <style>
        @page {
            size: A4;
            margin: 36pt 42pt 18pt 85pt;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .cabecalho {
            text-align: center;
            margin-bottom: 30pt;
            border-bottom: 2pt solid #333;
            padding-bottom: 20pt;
        }
        
        .brasao {
            width: 80pt;
            height: 80pt;
            margin: 0 auto 15pt;
            display: block;
        }
        
        .titulo-instituicao {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5pt;
            color: #1a365d;
        }
        
        .subtitulo-instituicao {
            font-size: 14pt;
            margin-bottom: 3pt;
            color: #2d3748;
        }
        
        .documento-titulo {
            font-size: 16pt;
            font-weight: bold;
            margin: 25pt 0 15pt;
            text-align: center;
            color: #1a365d;
        }
        
        .documento-ementa {
            font-size: 14pt;
            font-style: italic;
            text-align: center;
            margin-bottom: 30pt;
            color: #4a5568;
            border: 1pt solid #e2e8f0;
            padding: 15pt;
            background-color: #f7fafc;
        }
        
        .conteudo {
            text-align: justify;
            margin-bottom: 40pt;
            line-height: 1.6;
        }
        
        .conteudo p {
            margin-bottom: 12pt;
            text-indent: 20pt;
        }
        
        .assinatura-digital {
            text-align: center;
            margin-top: 50pt;
            padding-top: 20pt;
            border-top: 1pt solid #e2e8f0;
        }
        
        .linha-assinatura {
            width: 200pt;
            height: 1pt;
            background-color: #000;
            margin: 0 auto 15pt;
        }
        
        .nome-assinante {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5pt;
            color: #1a365d;
        }
        
        .cargo-assinante {
            font-size: 12pt;
            margin-bottom: 8pt;
            color: #4a5568;
        }
        
        .data-assinatura {
            font-size: 11pt;
            margin-bottom: 5pt;
            color: #718096;
        }
        
        .protocolo-info {
            font-size: 11pt;
            font-weight: bold;
            color: #2b6cb0;
            background-color: #ebf8ff;
            padding: 8pt;
            border-radius: 4pt;
            display: inline-block;
        }
        
        .qrcode-container {
            text-align: center;
            margin-top: 30pt;
            padding: 20pt;
            border: 1pt solid #e2e8f0;
            background-color: #f7fafc;
        }
        
        .qrcode-info {
            font-size: 10pt;
            color: #4a5568;
        }
        
        .qrcode-info strong {
            color: #1a365d;
        }
        
        .qrcode-info small {
            color: #718096;
        }
        
        .rodape {
            position: fixed;
            bottom: 20pt;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #718096;
            border-top: 1pt solid #e2e8f0;
            padding-top: 10pt;
        }
        
        .numero-pagina {
            position: fixed;
            bottom: 15pt;
            right: 20pt;
            font-size: 9pt;
            color: #718096;
        }
        
        .marca-dagua {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 48pt;
            color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
            z-index: -1;
        }
        
        /* Otimizações específicas para PDF */
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        /* Cores otimizadas para impressão */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Marca d'água -->
    <div class="marca-dagua">PROTOCOLADO</div>
    
    <!-- Cabeçalho -->
    <div class="cabecalho">
        <div class="titulo-instituicao">CÂMARA MUNICIPAL</div>
        <div class="subtitulo-instituicao">Câmara Municipal de Caraguatatuba</div>
        <div class="subtitulo-instituicao">Estância Balneária</div>
        <div class="subtitulo-instituicao">Estado de São Paulo</div>
    </div>
    
    <!-- Título do Documento -->
    <div class="documento-titulo">
        {{ strtoupper($proposicao->tipo ?? 'PROPOSIÇÃO') }} Nº {{ $numeroProtocolo }}
    </div>
    
    <!-- Ementa -->
    <div class="documento-ementa">
        <strong>EMENTA:</strong> {{ $proposicao->ementa ?? 'Ementa não disponível' }}
    </div>
    
    <!-- Conteúdo -->
    <div class="conteudo no-break">
        {!! nl2br(e($conteudo)) !!}
    </div>
    
    <!-- Assinatura Digital -->
    <div class="assinatura-digital no-break">
        {!! $assinaturaDigital !!}
    </div>
    
    <!-- QR Code -->
    <div class="qrcode-container no-break">
        {!! $qrcode !!}
    </div>
    
    <!-- Rodapé -->
    <div class="rodape">
        <div>Documento gerado automaticamente pelo Sistema LegisInc</div>
        <div>Data de geração: {{ now()->format('d/m/Y H:i:s') }}</div>
        <div>Versão do sistema: {{ config('app.version', '1.0.0') }}</div>
    </div>
    
    <!-- Número da Página -->
    <div class="numero-pagina">
        Página <span class="pagenum"></span>
    </div>
</body>
</html>
