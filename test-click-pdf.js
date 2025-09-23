// Script de teste para clicar no botão PDF do OnlyOffice
// Execute no console do navegador quando o OnlyOffice estiver carregado

console.log('🔄 Testando clique no botão PDF do OnlyOffice...');

function testarCliqueBotaoPDF() {
    return new Promise((resolve, reject) => {
        try {
            // Encontrar o iframe do OnlyOffice
            const iframe = document.querySelector('[id*="onlyoffice-editor"] iframe');

            if (!iframe) {
                console.error('❌ Iframe do OnlyOffice não encontrado');
                reject(new Error('Iframe não encontrado'));
                return;
            }

            console.log('✅ Iframe encontrado:', iframe);

            // Aguardar um pouco e tentar acessar o conteúdo
            setTimeout(() => {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document;

                    if (!iframeDoc) {
                        console.error('❌ Documento do iframe não acessível (possível CORS)');
                        reject(new Error('Documento do iframe não acessível'));
                        return;
                    }

                    console.log('✅ Documento do iframe acessível');

                    // Listar todos os elementos com format
                    const elementosFormat = iframeDoc.querySelectorAll('[format]');
                    console.log('🔍 Elementos com atributo format encontrados:', elementosFormat.length);

                    Array.from(elementosFormat).forEach((el, index) => {
                        console.log(`  ${index + 1}. format="${el.getAttribute('format')}" class="${el.className}" tag="${el.tagName}"`);
                    });

                    // Procurar pelo botão PDF específico
                    const botaoPDF = iframeDoc.querySelector('.btn-doc-format[format="513"]');

                    if (!botaoPDF) {
                        console.error('❌ Botão PDF com format="513" não encontrado');

                        // Tentar seletores alternativos
                        const seletoresAlternativos = [
                            '[format="513"]',
                            '.format-item [format="513"]',
                            '[data-format="513"]'
                        ];

                        for (const seletor of seletoresAlternativos) {
                            const botaoAlt = iframeDoc.querySelector(seletor);
                            if (botaoAlt) {
                                console.log(`✅ Botão PDF encontrado com seletor alternativo: ${seletor}`, botaoAlt);
                                executarClique(botaoAlt, iframe);
                                resolve(true);
                                return;
                            }
                        }

                        reject(new Error('Botão PDF não encontrado'));
                        return;
                    }

                    console.log('✅ Botão PDF encontrado:', botaoPDF);
                    console.log('📋 Detalhes do botão:', {
                        format: botaoPDF.getAttribute('format'),
                        className: botaoPDF.className,
                        tagName: botaoPDF.tagName,
                        outerHTML: botaoPDF.outerHTML
                    });

                    // Executar clique
                    executarClique(botaoPDF, iframe);
                    resolve(true);

                } catch (iframeError) {
                    console.error('❌ Erro ao acessar conteúdo do iframe:', iframeError);
                    reject(iframeError);
                }
            }, 2000); // Aguardar 2 segundos

        } catch (error) {
            console.error('❌ Erro geral:', error);
            reject(error);
        }
    });
}

function executarClique(botao, iframe) {
    console.log('🖱️ Executando clique no botão PDF...');

    // Método 1: MouseEvent
    const eventoClick = new MouseEvent('click', {
        bubbles: true,
        cancelable: true,
        view: iframe.contentWindow
    });

    const resultado1 = botao.dispatchEvent(eventoClick);
    console.log('🖱️ MouseEvent click resultado:', resultado1);

    // Método 2: Click direto
    setTimeout(() => {
        if (typeof botao.click === 'function') {
            botao.click();
            console.log('🖱️ Click() direto executado');
        }

        // Método 3: MouseDown + MouseUp
        const eventoMouseDown = new MouseEvent('mousedown', {
            bubbles: true,
            cancelable: true,
            view: iframe.contentWindow
        });

        const eventoMouseUp = new MouseEvent('mouseup', {
            bubbles: true,
            cancelable: true,
            view: iframe.contentWindow
        });

        botao.dispatchEvent(eventoMouseDown);
        setTimeout(() => {
            botao.dispatchEvent(eventoMouseUp);
            console.log('🖱️ MouseDown + MouseUp executado');
        }, 50);

    }, 200);

    console.log('✅ Todos os métodos de clique foram executados!');
}

// Executar teste
testarCliqueBotaoPDF()
    .then(() => {
        console.log('✅ Teste concluído com sucesso! Verifique se o download foi iniciado.');
    })
    .catch((error) => {
        console.error('❌ Teste falhou:', error.message);
        console.log('💡 Dica: Certifique-se de que o OnlyOffice está totalmente carregado antes de executar este teste.');
    });

// Função para executar manualmente
window.testarPDF = testarCliqueBotaoPDF;

console.log('📋 Script carregado! Execute window.testarPDF() para testar manualmente.');