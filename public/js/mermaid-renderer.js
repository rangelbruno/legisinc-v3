/**
 * Mermaid Renderer - Robusta com Validação e Error Handling
 * Implementa renderização segura com parse prévia e fallbacks
 */

class MermaidRenderer {
    constructor() {
        this.initialized = false;
        this.renderQueue = [];
        this.renderAttempts = new Map();
        this.maxRetries = 2;
    }

    async initialize() {
        if (this.initialized) return;

        try {
            // Inicializar Mermaid com configuração otimizada
            mermaid.initialize({
                startOnLoad: false,
                theme: 'default',
                securityLevel: 'loose',
                logLevel: 'error',
                htmlLabels: true,
                maxTextSize: 90000,
                fontFamily: '"Inter", sans-serif',
                fontSize: 14,

                // Configurações específicas por tipo de diagrama
                flowchart: {
                    useMaxWidth: true,
                    htmlLabels: true,
                    curve: 'basis',
                    padding: 15,
                    nodeSpacing: 40,
                    rankSpacing: 40,
                    diagramPadding: 15
                },

                sequence: {
                    diagramMarginX: 30,
                    diagramMarginY: 10,
                    boxTextMargin: 5,
                    noteMargin: 10,
                    messageMargin: 25,
                    mirrorActors: true,
                    useMaxWidth: true,
                    rightAngles: false,
                    showSequenceNumbers: false
                },

                gantt: {
                    titleTopMargin: 25,
                    barHeight: 20,
                    fontFamily: '"Inter", sans-serif',
                    fontSize: 11,
                    gridLineStartPadding: 35,
                    bottomPadding: 50,
                    rightPadding: 75
                },

                // Tema customizado para melhor legibilidade
                themeVariables: {
                    primaryColor: '#f1416c',
                    primaryTextColor: '#2e3440',
                    primaryBorderColor: '#7E8299',
                    lineColor: '#5CB85C',
                    secondaryColor: '#006100',
                    tertiaryColor: '#fff',
                    background: '#ffffff',
                    mainBkg: '#ffffff',
                    secondBkg: '#f8f9fa',
                    tertiaryBkg: '#e9ecef'
                }
            });

            this.initialized = true;
            console.log('✅ MermaidRenderer initialized successfully');

        } catch (error) {
            console.error('❌ Failed to initialize MermaidRenderer:', error);
            throw error;
        }
    }

    /**
     * Valida a sintaxe do diagrama antes de renderizar
     */
    async validateDiagram(diagramText) {
        try {
            await mermaid.parse(diagramText);
            return { valid: true };
        } catch (error) {
            return {
                valid: false,
                error: error.message,
                line: error.hash?.line || null,
                column: error.hash?.column || null
            };
        }
    }

    /**
     * Limpa e normaliza o texto do diagrama
     */
    cleanDiagramText(text) {
        return text
            .replace(/[\u200B-\u200D\uFEFF]/g, '') // Remove zero-width characters
            .replace(/\r\n/g, '\n')
            .replace(/\r/g, '\n')
            .replace(/^\s*```mermaid\s*\n?/i, '') // Remove markdown wrapper
            .replace(/\n?\s*```\s*$/i, '') // Remove markdown wrapper
            .trim();
    }

    /**
     * Gera ID único para SVG
     */
    generateUniqueId(prefix = 'mermaid') {
        return `${prefix}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    }

    /**
     * Renderiza um diagrama individual com error handling completo
     */
    async renderDiagram(element, options = {}) {
        const elementId = element.id || this.generateUniqueId('diagram');
        const attemptKey = `${elementId}-${options.attempt || 0}`;

        try {
            // Verificar se já foi processado
            if (element.getAttribute('data-processed') === 'true') {
                return { success: true, cached: true };
            }

            // Obter e limpar texto do diagrama
            const rawText = element.textContent || element.innerText;
            const cleanText = this.cleanDiagramText(rawText);

            if (!cleanText) {
                throw new Error('Diagrama vazio ou inválido');
            }

            // Validar sintaxe antes de renderizar
            const validation = await this.validateDiagram(cleanText);
            if (!validation.valid) {
                throw new Error(`Sintaxe inválida: ${validation.error}${validation.line ? ` (linha ${validation.line})` : ''}`);
            }

            // Gerar ID único para este render
            const svgId = this.generateUniqueId(`svg-${elementId}`);

            // Renderizar o diagrama
            const startTime = performance.now();
            const { svg } = await mermaid.render(svgId, cleanText);
            const renderTime = Math.round(performance.now() - startTime);

            // Inserir SVG no elemento
            element.innerHTML = svg;
            element.setAttribute('data-processed', 'true');
            element.setAttribute('data-render-time', renderTime);

            // Aplicar estilos responsivos
            this.applyResponsiveStyles(element);

            console.log(`✅ Diagram rendered successfully: ${elementId} (${renderTime}ms)`);

            return {
                success: true,
                renderTime,
                svgId,
                textLength: cleanText.length
            };

        } catch (error) {
            console.error(`❌ Error rendering diagram ${elementId}:`, error);

            // Incrementar tentativas
            const attempts = (this.renderAttempts.get(elementId) || 0) + 1;
            this.renderAttempts.set(elementId, attempts);

            // Tentar novamente se não excedeu o limite
            if (attempts < this.maxRetries) {
                console.log(`🔄 Retrying diagram ${elementId} (attempt ${attempts + 1}/${this.maxRetries})`);
                await new Promise(resolve => setTimeout(resolve, 1000 * attempts)); // Delay exponencial
                return this.renderDiagram(element, { ...options, attempt: attempts });
            }

            // Mostrar erro amigável
            this.showError(element, error, elementId);

            return {
                success: false,
                error: error.message,
                attempts
            };
        }
    }

    /**
     * Aplica estilos responsivos ao SVG renderizado
     */
    applyResponsiveStyles(element) {
        const svgElement = element.querySelector('svg');
        if (!svgElement) return;

        // Estilos responsivos
        svgElement.style.maxWidth = '100%';
        svgElement.style.height = 'auto';
        svgElement.style.width = '100%';

        // Remover dimensões fixas
        svgElement.removeAttribute('width');
        svgElement.removeAttribute('height');

        // Configurar viewBox se necessário
        this.ensureViewBox(svgElement);
    }

    /**
     * Garante que o SVG tenha um viewBox apropriado
     */
    ensureViewBox(svgElement) {
        if (svgElement.getAttribute('viewBox')) return;

        try {
            // Aguardar um frame para garantir que o SVG está renderizado
            requestAnimationFrame(() => {
                try {
                    const bbox = svgElement.getBBox();
                    if (bbox.width > 0 && bbox.height > 0) {
                        svgElement.setAttribute('viewBox', `0 0 ${bbox.width} ${bbox.height}`);
                    }
                } catch (e) {
                    // Fallback para dimensões padrão
                    svgElement.setAttribute('viewBox', '0 0 800 600');
                }
            });
        } catch (e) {
            console.warn('Could not set viewBox for SVG:', e.message);
        }
    }

    /**
     * Mostra erro amigável para o usuário
     */
    showError(element, error, diagramId) {
        const errorHtml = `
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="ki-duotone ki-cross-circle fs-2x text-danger me-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <div class="flex-grow-1">
                    <h6 class="mb-1">❌ Erro na renderização do diagrama</h6>
                    <small class="text-muted">
                        <strong>ID:</strong> ${diagramId}<br/>
                        <strong>Erro:</strong> ${error.message}<br/>
                        <strong>Sugestão:</strong> Verifique a sintaxe do diagrama Mermaid.
                    </small>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-light-primary" onclick="window.mermaidRenderer.retryDiagram('${diagramId}')">
                            <i class="ki-duotone ki-refresh fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Tentar Novamente
                        </button>
                    </div>
                </div>
            </div>
        `;

        element.innerHTML = errorHtml;
        element.setAttribute('data-processed', 'error');
        element.setAttribute('data-error', error.message);
    }

    /**
     * Renderiza todos os diagramas na página
     */
    async renderAllDiagrams() {
        if (!this.initialized) {
            await this.initialize();
        }

        const diagramElements = document.querySelectorAll('.mermaid:not([data-processed="true"])');
        console.log(`🎯 Found ${diagramElements.length} diagrams to render`);

        if (diagramElements.length === 0) {
            console.log('No diagrams found to render');
            return;
        }

        const results = [];
        let successCount = 0;
        let errorCount = 0;

        // Renderizar diagramas em paralelo (mas limitado)
        const chunks = this.chunkArray([...diagramElements], 3); // Máximo 3 por vez

        for (const chunk of chunks) {
            const chunkPromises = chunk.map(element => this.renderDiagram(element));
            const chunkResults = await Promise.allSettled(chunkPromises);

            chunkResults.forEach((result, index) => {
                if (result.status === 'fulfilled' && result.value.success) {
                    successCount++;
                } else {
                    errorCount++;
                }
                results.push(result);
            });

            // Pequena pausa entre chunks para não sobrecarregar
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        console.log(`📊 Rendering complete: ${successCount} success, ${errorCount} errors`);

        // Disparar evento customizado
        window.dispatchEvent(new CustomEvent('mermaid-rendering-complete', {
            detail: { successCount, errorCount, results }
        }));

        return { successCount, errorCount, results };
    }

    /**
     * Utilitário para dividir array em chunks
     */
    chunkArray(array, size) {
        const chunks = [];
        for (let i = 0; i < array.length; i += size) {
            chunks.push(array.slice(i, i + size));
        }
        return chunks;
    }

    /**
     * Tenta renderizar um diagrama específico novamente
     */
    async retryDiagram(diagramId) {
        const element = document.getElementById(diagramId);
        if (!element) {
            console.error(`Diagram element not found: ${diagramId}`);
            return;
        }

        // Reset estado
        element.removeAttribute('data-processed');
        element.removeAttribute('data-error');
        this.renderAttempts.delete(diagramId);

        // Mostrar loading
        element.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Renderizando...</span></div>';

        // Tentar renderizar novamente
        return this.renderDiagram(element);
    }
}

// Instância global
window.mermaidRenderer = new MermaidRenderer();

// Auto-inicializar quando DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.mermaidRenderer.renderAllDiagrams();
    });
} else {
    // DOM já está pronto
    window.mermaidRenderer.renderAllDiagrams();
}

// Renderizar novamente quando houver mudanças de aba
document.addEventListener('shown.bs.tab', function() {
    setTimeout(() => {
        window.mermaidRenderer.renderAllDiagrams();
    }, 200);
});

console.log('🎨 MermaidRenderer loaded and ready');