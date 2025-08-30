@extends('layouts.app')

@section('title', 'Fluxo de Proposições - Documentação')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Fluxo de Proposições - Sistema Legisinc</h5>
                            <p class="text-sm mb-0">Documentação completa dos processos e diagramas do sistema</p>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-gradient-success">
                                <i class="fas fa-file-code me-1"></i>
                                Documentação Técnica
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="markdown-content" id="documentation-content">
                        {!! \League\CommonMark\GithubFlavoredMarkdownConverter::class ? 
                            (new \League\CommonMark\GithubFlavoredMarkdownConverter())->convert($content) : 
                            nl2br(e($content)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para o conteúdo Markdown */
.markdown-content {
    line-height: 1.6;
    font-size: 14px;
}

.markdown-content h1 {
    color: #2c3e50;
    border-bottom: 2px solid #e74c3c;
    padding-bottom: 10px;
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: bold;
}

.markdown-content h2 {
    color: #34495e;
    border-bottom: 1px solid #bdc3c7;
    padding-bottom: 8px;
    margin-top: 30px;
    margin-bottom: 15px;
    font-size: 20px;
    font-weight: 600;
}

.markdown-content h3 {
    color: #5d6d7e;
    margin-top: 25px;
    margin-bottom: 12px;
    font-size: 16px;
    font-weight: 600;
}

.markdown-content pre {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    overflow-x: auto;
    margin: 15px 0;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 13px;
}

.markdown-content code {
    background-color: #f1f2f6;
    color: #e74c3c;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 12px;
}

.markdown-content pre code {
    background: none;
    color: #2c3e50;
    padding: 0;
}

.markdown-content ul, .markdown-content ol {
    margin: 10px 0;
    padding-left: 25px;
}

.markdown-content li {
    margin-bottom: 5px;
}

.markdown-content blockquote {
    border-left: 4px solid #3498db;
    margin: 15px 0;
    padding-left: 15px;
    color: #7f8c8d;
    font-style: italic;
}

.markdown-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.markdown-content th, .markdown-content td {
    border: 1px solid #ddd;
    padding: 8px 12px;
    text-align: left;
}

.markdown-content th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.markdown-content hr {
    border: none;
    border-top: 2px solid #ecf0f1;
    margin: 30px 0;
}

.markdown-content p {
    margin-bottom: 12px;
}

.markdown-content strong {
    color: #2c3e50;
    font-weight: 600;
}

.markdown-content em {
    color: #7f8c8d;
}

/* Melhorias para diagramas Mermaid */
.markdown-content .mermaid,
.markdown-content .mermaid-diagram {
    text-align: center;
    margin: 30px 0;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
}

.markdown-content .mermaid-diagram svg {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

/* Estilo para pré-visualização dos blocos de código antes da renderização */
.markdown-content pre code {
    display: block;
    max-height: 400px;
    overflow-y: auto;
}

/* Melhorias para responsividade dos diagramas */
@media (max-width: 768px) {
    .markdown-content .mermaid,
    .markdown-content .mermaid-diagram {
        padding: 15px 10px;
        margin: 20px 0;
    }
    
    .markdown-content .mermaid-diagram svg {
        transform: scale(0.85);
        transform-origin: center top;
    }
}

/* Continuação da responsividade */
@media (max-width: 768px) {
    .markdown-content {
        font-size: 13px;
    }
    
    .markdown-content pre {
        font-size: 11px;
        padding: 10px;
    }
    
    .markdown-content h1 {
        font-size: 20px;
    }
    
    .markdown-content h2 {
        font-size: 18px;
    }
}
</style>

<!-- Mermaid.js para renderizar diagramas -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@10.9.1/dist/mermaid.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração do Mermaid
    mermaid.initialize({ 
        startOnLoad: false,
        theme: 'default',
        themeVariables: {
            primaryColor: '#007bff',
            primaryTextColor: '#2c3e50',
            primaryBorderColor: '#e74c3c',
            lineColor: '#5d6d7e',
            secondaryColor: '#f8f9fa',
            tertiaryColor: '#e9ecef',
            background: '#ffffff',
            mainBkg: '#ffffff',
            secondBkg: '#f1f3f4',
            tertiaryBkg: '#e8f4f8'
        },
        flowchart: {
            useMaxWidth: true,
            htmlLabels: true,
            curve: 'basis'
        },
        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        fontSize: '14px'
    });

    // Renderizar diagramas
    const renderDiagrams = async () => {
        try {
            // Encontrar todos os blocos de código mermaid
            const mermaidElements = document.querySelectorAll('pre code.language-mermaid, code.language-mermaid');
            
            for (let i = 0; i < mermaidElements.length; i++) {
                const element = mermaidElements[i];
                const graphDefinition = element.textContent;
                
                // Criar container para o diagrama
                const diagramContainer = document.createElement('div');
                diagramContainer.className = 'mermaid-diagram';
                diagramContainer.id = 'mermaid-' + i;
                
                // Substituir o elemento de código pelo container
                const parent = element.closest('pre') || element;
                parent.parentNode.insertBefore(diagramContainer, parent);
                parent.remove();
                
                // Renderizar o diagrama
                const { svg } = await mermaid.render('diagram-' + i, graphDefinition);
                diagramContainer.innerHTML = svg;
            }
            
            console.log('✅ Diagramas Mermaid renderizados com sucesso!');
        } catch (error) {
            console.error('❌ Erro ao renderizar diagramas Mermaid:', error);
            
            // Fallback: tentar renderização automática
            mermaid.init(undefined, document.querySelectorAll('.mermaid'));
        }
    };

    // Aguardar um pouco para garantir que o DOM está completamente carregado
    setTimeout(renderDiagrams, 500);
});
</script>
@endsection