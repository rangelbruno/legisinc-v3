@extends('components.layouts.app')

@section('title', 'Visualização PDF Otimizada - Proposição')

@section('content')

<style>
/* Estilos para PDF limpo e otimizado */
.pdf-container-otimizado {
    background: white;
    padding: 40px;
    margin: 0 auto;
    max-width: 210mm;
    min-height: 297mm;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    font-family: 'Times New Roman', Times, serif;
    position: relative;
    line-height: 1.6;
}

/* Cabeçalho da câmara */
.cabecalho-camara {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 20px;
}

.cabecalho-camara .nome-camara {
    font-size: 16pt;
    font-weight: bold;
    color: #1a4b8c;
    margin-bottom: 8px;
}

.cabecalho-camara .info-camara {
    font-size: 10pt;
    color: #333;
    line-height: 1.4;
}

/* Título do documento */
.titulo-documento {
    text-align: center;
    font-size: 16pt;
    font-weight: bold;
    margin: 30px 0;
    text-transform: uppercase;
    color: #333;
}

/* Ementa */
.ementa-container {
    margin: 20px 0;
    padding: 15px;
    background-color: #f8f9fa;
    border-left: 4px solid #1a4b8c;
}

.ementa-titulo {
    font-weight: bold;
    color: #1a4b8c;
    margin-bottom: 8px;
}

.ementa-texto {
    font-style: italic;
    text-align: justify;
}

/* Conteúdo principal */
.conteudo-documento {
    text-align: justify;
    margin: 30px 0;
    font-size: 12pt;
    line-height: 1.8;
}

.conteudo-documento p {
    margin-bottom: 12pt;
    text-indent: 20pt;
}

/* Data e local */
.data-local {
    text-align: right;
    margin: 40px 0 20px 0;
    font-size: 12pt;
}

/* Assinatura */
.assinatura-container {
    margin-top: 50px;
    text-align: center;
}

.linha-assinatura {
    border-bottom: 1px solid #000;
    width: 300px;
    margin: 30px auto 10px;
}

.nome-assinante {
    font-weight: bold;
    margin-top: 10px;
}

.cargo-assinante {
    font-style: italic;
    color: #666;
}

/* Status de aprovação */
.status-documento {
    position: absolute;
    top: 10px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    font-size: 10pt;
    font-weight: bold;
}

/* Metadados (visíveis apenas em desenvolvimento) */
.metadados-debug {
    position: fixed;
    bottom: 10px;
    right: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px;
    font-size: 10px;
    border-radius: 5px;
    max-width: 300px;
    z-index: 1000;
}

/* Botões de ação */
.acoes-documento {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1000;
}

.btn-acao {
    display: inline-block;
    padding: 10px 20px;
    margin: 0 5px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-acao:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-acao.btn-success {
    background: #28a745;
}

.btn-acao.btn-success:hover {
    background: #1e7e34;
}

.btn-acao.btn-warning {
    background: #ffc107;
    color: #333;
}

.btn-acao.btn-warning:hover {
    background: #e0a800;
}

/* Responsividade */
@media screen and (max-width: 768px) {
    .pdf-container-otimizado {
        padding: 20px;
        margin: 10px;
        max-width: 100%;
    }
    
    .acoes-documento {
        position: relative;
        top: auto;
        left: auto;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .btn-acao {
        display: block;
        margin: 10px auto;
        max-width: 200px;
    }
}

/* Impressão */
@media print {
    .acoes-documento,
    .metadados-debug {
        display: none !important;
    }
    
    .pdf-container-otimizado {
        box-shadow: none;
        margin: 0;
        padding: 20mm;
        max-width: none;
        width: 100%;
    }
}
</style>

<!-- Botões de Ação -->
<div class="acoes-documento">
    <a href="{{ url()->previous() }}" class="btn-acao">
        ← Voltar
    </a>
    
    @if($usando_onlyoffice)
        <span class="btn-acao btn-success">
            ✓ OnlyOffice
        </span>
    @else
        <span class="btn-acao btn-warning">
            ⚠ Fallback
        </span>
    @endif
    
    <button onclick="window.print()" class="btn-acao">
        🖨️ Imprimir
    </button>
</div>

<!-- Container Principal do PDF -->
<div class="pdf-container-otimizado" id="documento-pdf">
    
    <!-- Status do Documento -->
    <div class="status-documento">
        {{ strtoupper($proposicao->status) }}
    </div>

    <!-- Cabeçalho da Câmara (apenas se não for OnlyOffice) -->
    @if(!$usando_onlyoffice)
    <div class="cabecalho-camara">
        <div class="nome-camara">{{ $dados_camara['nome'] }}</div>
        <div class="info-camara">
            {{ $dados_camara['endereco'] }}<br>
            {{ $dados_camara['telefone'] }} - {{ $dados_camara['website'] }}<br>
            CNPJ: {{ $dados_camara['cnpj'] }}
        </div>
    </div>
    @endif

    <!-- Título do Documento -->
    <div class="titulo-documento">
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
    <div class="ementa-container">
        <div class="ementa-titulo">EMENTA:</div>
        <div class="ementa-texto">{{ $proposicao->ementa }}</div>
    </div>
    @endif

    <!-- Conteúdo Principal do Documento -->
    <div class="conteudo-documento">
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
    <div class="data-local">
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
    <div class="assinatura-container">
        <div class="linha-assinatura"></div>
        <div class="nome-assinante">{{ $autor->name ?? 'Autor não identificado' }}</div>
        <div class="cargo-assinante">
            @if($autor && $autor->hasRole('PARLAMENTAR'))
                Vereador(a)
            @else
                Parlamentar
            @endif
        </div>
    </div>
</div>

<!-- Metadados de Debug (apenas em ambiente de desenvolvimento) -->
@if(config('app.debug'))
<div class="metadados-debug" id="debug-info">
    <strong>🔍 Informações Técnicas:</strong><br>
    <strong>Método:</strong> {{ $metadados['metodo_extracao'] }}<br>
    <strong>OnlyOffice:</strong> {{ $usando_onlyoffice ? 'Sim' : 'Não' }}<br>
    <strong>Geração:</strong> {{ $metadados['data_geracao']->format('d/m/Y H:i:s') }}<br>
    @if(isset($metadados['hash_integridade']))
        <strong>Hash:</strong> {{ substr($metadados['hash_integridade'], 0, 8) }}...<br>
    @endif
    <small><em>Clique para ocultar</em></small>
</div>
@endif

<script>
// Funcionalidades JavaScript para melhor experiência
document.addEventListener('DOMContentLoaded', function() {
    
    // Ocultar debug info ao clicar
    const debugInfo = document.getElementById('debug-info');
    if (debugInfo) {
        debugInfo.addEventListener('click', function() {
            this.style.display = 'none';
        });
    }
    
    // Melhorar contraste para impressão
    const beforePrint = function() {
        document.body.style.background = 'white';
    };
    
    const afterPrint = function() {
        document.body.style.background = '';
    };
    
    if (window.matchMedia) {
        const mediaQueryList = window.matchMedia('print');
        mediaQueryList.addEventListener('change', function(mql) {
            if (mql.matches) {
                beforePrint();
            } else {
                afterPrint();
            }
        });
    }
    
    // Feedback visual de carregamento
    console.log('📄 Documento carregado:', {
        proposicao_id: {{ $proposicao->id }},
        usando_onlyoffice: {{ $usando_onlyoffice ? 'true' : 'false' }},
        metodo_extracao: '{{ $metadados['metodo_extracao'] }}',
        timestamp: '{{ $metadados['data_geracao'] }}'
    });
    
    // Tornar texto selecionável e copiável
    document.querySelectorAll('.conteudo-documento *').forEach(element => {
        element.style.userSelect = 'text';
        element.style.webkitUserSelect = 'text';
        element.style.mozUserSelect = 'text';
        element.style.msUserSelect = 'text';
    });
});

// Função para copiar conteúdo selecionado
document.addEventListener('copy', function(e) {
    console.log('📋 Texto copiado do documento');
});
</script>

@endsection