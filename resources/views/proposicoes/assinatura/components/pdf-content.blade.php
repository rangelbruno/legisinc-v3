{{-- Componente de conteúdo PDF integrado otimizado --}}

<style>
/* Estilos específicos para o PDF integrado */
.pdf-container-integrado {
    font-family: 'Times New Roman', Times, serif;
    line-height: 1.6;
    color: #333;
    background: white;
    padding: 40px;
    max-width: 210mm;
    margin: 0 auto;
}

/* Cabeçalho da câmara */
.cabecalho-camara-integrado {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 20px;
}

.cabecalho-camara-integrado .nome-camara {
    font-size: 16pt;
    font-weight: bold;
    color: #1a4b8c;
    margin-bottom: 8px;
}

.cabecalho-camara-integrado .info-camara {
    font-size: 10pt;
    color: #333;
    line-height: 1.4;
}

/* Título do documento */
.titulo-documento-integrado {
    text-align: center;
    font-size: 16pt;
    font-weight: bold;
    margin: 30px 0;
    text-transform: uppercase;
    color: #333;
}

/* Ementa */
.ementa-container-integrado {
    margin: 20px 0;
    padding: 15px;
    background-color: #f8f9fa;
    border-left: 4px solid #1a4b8c;
}

.ementa-titulo-integrado {
    font-weight: bold;
    color: #1a4b8c;
    margin-bottom: 8px;
}

.ementa-texto-integrado {
    font-style: italic;
    text-align: justify;
}

/* Conteúdo principal */
.conteudo-documento-integrado {
    text-align: justify;
    margin: 30px 0;
    font-size: 12pt;
    line-height: 1.8;
}

.conteudo-documento-integrado p {
    margin-bottom: 12pt;
    text-indent: 20pt;
}

/* Data e local */
.data-local-integrado {
    text-align: right;
    margin: 40px 0 20px 0;
    font-size: 12pt;
}

/* Assinatura */
.assinatura-container-integrado {
    margin-top: 50px;
    text-align: center;
}

.linha-assinatura-integrado {
    border-bottom: 1px solid #000;
    width: 300px;
    margin: 30px auto 10px;
}

.nome-assinante-integrado {
    font-weight: bold;
    margin-top: 10px;
}

.cargo-assinante-integrado {
    font-style: italic;
    color: #666;
}

/* Garantir texto selecionável */
.pdf-container-integrado * {
    user-select: text !important;
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    -ms-user-select: text !important;
}
</style>

<div class="pdf-container-integrado">
    
    <!-- Cabeçalho da Câmara (apenas se não for OnlyOffice) -->
    @if(!$usando_onlyoffice)
    <div class="cabecalho-camara-integrado">
        <div class="nome-camara">{{ $dados_camara['nome'] }}</div>
        <div class="info-camara">
            {{ $dados_camara['endereco'] }}<br>
            {{ $dados_camara['telefone'] }} - {{ $dados_camara['website'] }}<br>
            CNPJ: {{ $dados_camara['cnpj'] }}
        </div>
    </div>
    @endif

    <!-- Título do Documento -->
    <div class="titulo-documento-integrado">
        @if($tipo_proposicao)
            {{ strtoupper($tipo_proposicao->tipo) }}
        @else
            {{ strtoupper($proposicao->tipo) }}
        @endif
        @if($proposicao->numero_protocolo)
            Nº {{ $proposicao->numero_protocolo }}
        @else
            Nº [AGUARDANDO PROTOCOLO]
        @endif
    </div>

    <!-- Ementa (apenas se não for OnlyOffice, pois OnlyOffice já tem ementa no documento) -->
    @if(!$usando_onlyoffice && $proposicao->ementa)
    <div class="ementa-container-integrado">
        <div class="ementa-titulo-integrado">EMENTA:</div>
        <div class="ementa-texto-integrado">{{ $proposicao->ementa }}</div>
    </div>
    @endif

    <!-- Conteúdo Principal do Documento -->
    <div class="conteudo-documento-integrado">
        @if($usando_onlyoffice && !empty($conteudo_html))
            <!-- Usar conteúdo limpo do OnlyOffice -->
            {!! $conteudo_html !!}
        @elseif($proposicao->conteudo)
            <!-- Fallback para conteúdo da proposição -->
            @php
                $conteudoFormatado = nl2br(e($proposicao->conteudo));
            @endphp
            {!! $conteudoFormatado !!}
        @else
            <p><em>Conteúdo não disponível. Documento pode não ter sido editado ainda.</em></p>
        @endif
    </div>

    <!-- Data e Local -->
    <div class="data-local-integrado">
        @php
            $meses = [
                1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
                5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
                9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
            ];
            $mesAtual = $meses[now()->month];
        @endphp
        Caraguatatuba, {{ now()->format('d') }} de {{ $mesAtual }} de {{ now()->format('Y') }}.
    </div>

    <!-- Área de Assinatura -->
    <div class="assinatura-container-integrado">
        <div class="linha-assinatura-integrado"></div>
        <div class="nome-assinante-integrado">{{ $autor->name ?? 'Autor não identificado' }}</div>
        <div class="cargo-assinante-integrado">
            @if($autor && $autor->hasRole('PARLAMENTAR'))
                Vereador(a)
            @else
                Parlamentar
            @endif
        </div>
    </div>
</div>