<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Análise do Processo Legislativo Completo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/vis-network@latest/dist/vis-network.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/vis-network@latest/dist/vis-network.min.css" rel="stylesheet">
    <style>
        .vis-network { height: 600px; }
        .step-card { transition: all 0.3s ease; }
        .step-card:hover { transform: translateY(-2px); }
        .status-success { color: #10b981; }
        .status-error { color: #ef4444; }
        .status-pending { color: #f59e0b; }
        .database-save { 
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            color: white;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">
                Análise do Processo Legislativo Completo
            </h1>
            
            <!-- Controles -->
            <div class="flex justify-center gap-4 mb-6">
                <button id="iniciarTeste" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                    🚀 Iniciar Teste Completo
                </button>
                <button id="resetarTeste" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                    🔄 Resetar
                </button>
                <button id="gerarRelatorio" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                    📊 Gerar Relatório
                </button>
            </div>

            <!-- Status Geral -->
            <div class="bg-gray-100 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-medium">Status do Teste:</span>
                    <span id="statusGeral" class="text-lg font-bold">Aguardando Início</span>
                </div>
                <div class="w-full bg-gray-300 rounded-full h-3 mt-2">
                    <div id="progressBar" class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>

            <!-- Rede de Fluxo -->
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">Fluxo de Processo e Salvamentos</h2>
                <div id="networkContainer" class="border rounded-lg"></div>
            </div>

            <!-- Lista Detalhada de Etapas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Etapa 1: Administrador -->
                <div class="step-card bg-white border rounded-lg p-4" data-step="admin">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-blue-600">👨‍💼 1. Administrador</h3>
                        <span class="step-status text-sm" data-status="pending">⏳ Pendente</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>• Criação de templates</span>
                            <span class="database-save">BD: tipo_proposicao_templates</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Configuração de parâmetros</span>
                            <span class="database-save">BD: parametros</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Processamento de imagens</span>
                            <span class="database-save">BD: template.conteudo</span>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
                        <strong>Verificação:</strong> Template Moção criado com variáveis ${numero_proposicao}, ${ementa}, etc.
                    </div>
                </div>

                <!-- Etapa 2: Parlamentar Criação -->
                <div class="step-card bg-white border rounded-lg p-4" data-step="parlamentar-create">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-green-600">🏛️ 2. Parlamentar (Criação)</h3>
                        <span class="step-status text-sm" data-status="pending">⏳ Pendente</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>• Nova proposição</span>
                            <span class="database-save">BD: proposicoes.status = 'rascunho'</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Aplicação do template</span>
                            <span class="database-save">BD: proposicoes.template_id</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Salvamento no OnlyOffice</span>
                            <span class="database-save">BD: proposicoes.arquivo_path</span>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
                        <strong>Verificação:</strong> Proposição criada com ${numero_proposicao} = "[AGUARDANDO PROTOCOLO]"
                    </div>
                </div>

                <!-- Etapa 3: Parlamentar Edição -->
                <div class="step-card bg-white border rounded-lg p-4" data-step="parlamentar-edit">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-green-600">✏️ 3. Parlamentar (Edição)</h3>
                        <span class="step-status text-sm" data-status="pending">⏳ Pendente</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>• Editor OnlyOffice</span>
                            <span class="database-save">BD: proposicoes.status = 'em_edicao'</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Callback de salvamento</span>
                            <span class="database-save">BD: proposicoes.conteudo</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Arquivo atualizado</span>
                            <span class="database-save">storage/app/proposicoes/</span>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
                        <strong>Verificação:</strong> Template aplicado com todas as variáveis substituídas
                    </div>
                </div>

                <!-- Etapa 4: Envio Legislativo -->
                <div class="step-card bg-white border rounded-lg p-4" data-step="envio-legislativo">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-purple-600">📤 4. Envio p/ Legislativo</h3>
                        <span class="step-status text-sm" data-status="pending">⏳ Pendente</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>• Alteração de status</span>
                            <span class="database-save">BD: proposicoes.status = 'enviado_legislativo'</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Data de envio</span>
                            <span class="database-save">BD: proposicoes.updated_at</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Log de tramitação</span>
                            <span class="database-save">BD: tramitacao_logs</span>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
                        <strong>Verificação:</strong> Status alterado e proposição visível para Legislativo
                    </div>
                </div>

                <!-- Etapa 5: Legislativo -->
                <div class="step-card bg-white border rounded-lg p-4" data-step="legislativo">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-indigo-600">⚖️ 5. Legislativo</h3>
                        <span class="step-status text-sm" data-status="pending">⏳ Pendente</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>• Carregamento do arquivo</span>
                            <span class="database-save">storage/app/proposicoes/</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Edição no OnlyOffice</span>
                            <span class="database-save">BD: proposicoes.observacoes_legislativo</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Salvamento das alterações</span>
                            <span class="database-save">BD: proposicoes.conteudo</span>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
                        <strong>Verificação:</strong> Legislativo consegue carregar, editar e salvar o documento
                    </div>
                </div>

                <!-- Etapa 6: Retorno Parlamentar -->
                <div class="step-card bg-white border rounded-lg p-4" data-step="retorno-parlamentar">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-orange-600">↩️ 6. Retorno p/ Parlamentar</h3>
                        <span class="step-status text-sm" data-status="pending">⏳ Pendente</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>• Status de retorno</span>
                            <span class="database-save">BD: proposicoes.status = 'retornado_legislativo'</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Data de retorno</span>
                            <span class="database-save">BD: proposicoes.data_retorno_legislativo</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Geração de PDF</span>
                            <span class="database-save">BD: proposicoes.arquivo_pdf_path</span>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
                        <strong>Verificação:</strong> PDF gerado com alterações do Legislativo
                    </div>
                </div>

                <!-- Etapa 7: Assinatura -->
                <div class="step-card bg-white border rounded-lg p-4" data-step="assinatura">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-red-600">✍️ 7. Assinatura Digital</h3>
                        <span class="step-status text-sm" data-status="pending">⏳ Pendente</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>• Confirmação de leitura</span>
                            <span class="database-save">BD: proposicoes.confirmacao_leitura</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Assinatura digital</span>
                            <span class="database-save">BD: proposicoes.assinatura_digital</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• PDF assinado</span>
                            <span class="database-save">BD: proposicoes.pdf_assinado_path</span>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
                        <strong>Verificação:</strong> PDF assinado com QR Code e dados de autenticação
                    </div>
                </div>

                <!-- Etapa 8: Protocolo -->
                <div class="step-card bg-white border rounded-lg p-4" data-step="protocolo">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-teal-600">📋 8. Protocolo</h3>
                        <span class="step-status text-sm" data-status="pending">⏳ Pendente</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>• Número de protocolo</span>
                            <span class="database-save">BD: proposicoes.numero_protocolo</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• Status protocolado</span>
                            <span class="database-save">BD: proposicoes.status = 'protocolado'</span>
                        </div>
                        <div class="flex justify-between">
                            <span>• PDF final atualizado</span>
                            <span class="database-save">BD: proposicoes.arquivo_pdf_path</span>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
                        <strong>Verificação:</strong> Número oficial inserido e documento finalizado
                    </div>
                </div>
            </div>

            <!-- Log de Atividades -->
            <div class="mt-8">
                <h2 class="text-xl font-bold mb-4">Log de Atividades</h2>
                <div id="logContainer" class="bg-gray-900 text-green-400 p-4 rounded-lg h-64 overflow-y-auto font-mono text-sm">
                    <div class="text-gray-400">Sistema pronto para iniciar teste...</div>
                </div>
            </div>

            <!-- Relatório de Erros -->
            <div id="errorReport" class="mt-8 hidden">
                <h2 class="text-xl font-bold mb-4 text-red-600">Relatório de Erros</h2>
                <div id="errorList" class="space-y-2"></div>
            </div>
        </div>
    </div>

    <script>
        class ProcessoCompleto {
            constructor() {
                this.etapas = [
                    'admin', 'parlamentar-create', 'parlamentar-edit', 
                    'envio-legislativo', 'legislativo', 'retorno-parlamentar', 
                    'assinatura', 'protocolo'
                ];
                this.currentStep = 0;
                this.errors = [];
                this.proposicaoId = null;
                this.networkData = null;
                
                this.initNetwork();
                this.bindEvents();
            }

            initNetwork() {
                const nodes = new vis.DataSet([
                    {id: 1, label: 'Admin\nTemplates', color: '#3b82f6', font: {size: 12}},
                    {id: 2, label: 'Parlamentar\nCria', color: '#10b981', font: {size: 12}},
                    {id: 3, label: 'Parlamentar\nEdita', color: '#10b981', font: {size: 12}},
                    {id: 4, label: 'Envia\nLegislativo', color: '#8b5cf6', font: {size: 12}},
                    {id: 5, label: 'Legislativo\nRevisa', color: '#6366f1', font: {size: 12}},
                    {id: 6, label: 'Retorna\nParlamentar', color: '#f97316', font: {size: 12}},
                    {id: 7, label: 'Assinatura\nDigital', color: '#ef4444', font: {size: 12}},
                    {id: 8, label: 'Protocolo\nFinal', color: '#06b6d4', font: {size: 12}}
                ]);

                const edges = new vis.DataSet([
                    {from: 1, to: 2, label: 'Template', arrows: 'to'},
                    {from: 2, to: 3, label: 'Edição', arrows: 'to'},
                    {from: 3, to: 4, label: 'Envio', arrows: 'to'},
                    {from: 4, to: 5, label: 'Revisão', arrows: 'to'},
                    {from: 5, to: 6, label: 'Retorno', arrows: 'to'},
                    {from: 6, to: 7, label: 'PDF', arrows: 'to'},
                    {from: 7, to: 8, label: 'Assinado', arrows: 'to'}
                ]);

                this.networkData = { nodes, edges };
                
                const container = document.getElementById('networkContainer');
                const options = {
                    layout: { randomSeed: 2 },
                    nodes: {
                        shape: 'box',
                        margin: 10,
                        font: { multi: 'markdown' }
                    },
                    edges: {
                        font: { size: 10 },
                        smooth: true
                    }
                };
                
                this.network = new vis.Network(container, this.networkData, options);
            }

            bindEvents() {
                document.getElementById('iniciarTeste').addEventListener('click', () => this.iniciarTeste());
                document.getElementById('resetarTeste').addEventListener('click', () => this.resetarTeste());
                document.getElementById('gerarRelatorio').addEventListener('click', () => this.gerarRelatorio());
            }

            async iniciarTeste() {
                this.log("🚀 Iniciando teste completo do processo legislativo...");
                this.updateStatus("Em Execução", "bg-yellow-500");
                
                for (let i = 0; i < this.etapas.length; i++) {
                    this.currentStep = i;
                    this.updateProgress((i / this.etapas.length) * 100);
                    
                    try {
                        await this.executarEtapa(this.etapas[i]);
                        this.updateStepStatus(this.etapas[i], 'success');
                        this.updateNetworkNode(i + 1, '#10b981');
                    } catch (error) {
                        this.updateStepStatus(this.etapas[i], 'error');
                        this.updateNetworkNode(i + 1, '#ef4444');
                        this.errors.push({
                            etapa: this.etapas[i],
                            erro: error.message,
                            timestamp: new Date().toISOString()
                        });
                        this.log(`❌ ERRO na etapa ${this.etapas[i]}: ${error.message}`);
                        break;
                    }
                }

                this.updateProgress(100);
                if (this.errors.length === 0) {
                    this.updateStatus("✅ Concluído com Sucesso", "bg-green-500");
                    this.log("🎉 Teste completo executado com sucesso!");
                } else {
                    this.updateStatus("❌ Concluído com Erros", "bg-red-500");
                    this.showErrorReport();
                }
            }

            async executarEtapa(etapa) {
                this.log(`🔄 Executando etapa: ${etapa}`);
                
                switch(etapa) {
                    case 'admin':
                        await this.testarAdmin();
                        break;
                    case 'parlamentar-create':
                        await this.testarParlamentarCreate();
                        break;
                    case 'parlamentar-edit':
                        await this.testarParlamentarEdit();
                        break;
                    case 'envio-legislativo':
                        await this.testarEnvioLegislativo();
                        break;
                    case 'legislativo':
                        await this.testarLegislativo();
                        break;
                    case 'retorno-parlamentar':
                        await this.testarRetornoParlamentar();
                        break;
                    case 'assinatura':
                        await this.testarAssinatura();
                        break;
                    case 'protocolo':
                        await this.testarProtocolo();
                        break;
                }
                
                await this.sleep(1000); // Pausa para visualização
            }

            async testarAdmin() {
                // Verificar se templates existem
                const response = await this.apiCall('/api/templates/check');
                if (!response.template_mocao_exists) {
                    throw new Error('Template de Moção não encontrado no banco de dados');
                }
                this.log("✅ Templates criados e configurados no BD");
            }

            async testarParlamentarCreate() {
                // Criar nova proposição
                const response = await this.apiCall('/api/proposicoes/create-test', {
                    tipo: 'Moção',
                    ementa: 'Teste de proposição para análise do processo',
                    template_id: 6
                });
                
                if (!response.proposicao_id) {
                    throw new Error('Falha ao criar proposição');
                }
                
                this.proposicaoId = response.proposicao_id;
                this.log(`✅ Proposição criada (ID: ${this.proposicaoId}) - Status: rascunho`);
            }

            async testarParlamentarEdit() {
                if (!this.proposicaoId) throw new Error('Proposição não encontrada');
                
                // Simular edição no OnlyOffice
                const response = await this.apiCall(`/api/proposicoes/${this.proposicaoId}/simulate-edit`);
                
                if (!response.arquivo_salvo) {
                    throw new Error('Falha ao salvar arquivo no OnlyOffice');
                }
                
                this.log("✅ Documento editado e salvo via OnlyOffice callback");
            }

            async testarEnvioLegislativo() {
                if (!this.proposicaoId) throw new Error('Proposição não encontrada');
                
                const response = await this.apiCall(`/api/proposicoes/${this.proposicaoId}/enviar-legislativo`);
                
                if (response.status !== 'enviado_legislativo') {
                    throw new Error('Falha ao enviar para o Legislativo');
                }
                
                this.log("✅ Proposição enviada para o Legislativo");
            }

            async testarLegislativo() {
                if (!this.proposicaoId) throw new Error('Proposição não encontrada');
                
                const response = await this.apiCall(`/api/proposicoes/${this.proposicaoId}/simulate-legislativo-edit`);
                
                if (!response.edicao_salva) {
                    throw new Error('Legislativo não conseguiu salvar alterações');
                }
                
                this.log("✅ Legislativo editou e salvou o documento");
            }

            async testarRetornoParlamentar() {
                if (!this.proposicaoId) throw new Error('Proposição não encontrada');
                
                const response = await this.apiCall(`/api/proposicoes/${this.proposicaoId}/retornar-parlamentar`);
                
                if (!response.pdf_gerado) {
                    throw new Error('Falha ao gerar PDF para o Parlamentar');
                }
                
                this.log("✅ PDF gerado e retornado para o Parlamentar");
            }

            async testarAssinatura() {
                if (!this.proposicaoId) throw new Error('Proposição não encontrada');
                
                const response = await this.apiCall(`/api/proposicoes/${this.proposicaoId}/simulate-assinatura`);
                
                if (!response.assinatura_valida) {
                    throw new Error('Falha na assinatura digital');
                }
                
                this.log("✅ Documento assinado digitalmente com QR Code");
            }

            async testarProtocolo() {
                if (!this.proposicaoId) throw new Error('Proposição não encontrada');
                
                const response = await this.apiCall(`/api/proposicoes/${this.proposicaoId}/simulate-protocolo`);
                
                if (!response.numero_protocolo) {
                    throw new Error('Falha ao protocolar documento');
                }
                
                this.log(`✅ Documento protocolado - Número: ${response.numero_protocolo}`);
            }

            async apiCall(url, data = null) {
                try {
                    const options = {
                        method: data ? 'POST' : 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    };

                    if (data) {
                        options.body = JSON.stringify(data);
                    }

                    const response = await fetch(url, options);
                    
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || errorData.error || `HTTP ${response.status}`);
                    }

                    return await response.json();
                } catch (error) {
                    this.log(`🚨 Erro na API ${url}: ${error.message}`);
                    throw error;
                }
            }

            updateStepStatus(step, status) {
                const card = document.querySelector(`[data-step="${step}"]`);
                const statusElement = card.querySelector('.step-status');
                
                const statusMap = {
                    'pending': { text: '⏳ Pendente', class: 'status-pending' },
                    'success': { text: '✅ Sucesso', class: 'status-success' },
                    'error': { text: '❌ Erro', class: 'status-error' }
                };
                
                const statusInfo = statusMap[status];
                statusElement.textContent = statusInfo.text;
                statusElement.className = `step-status text-sm ${statusInfo.class}`;
            }

            updateNetworkNode(nodeId, color) {
                this.networkData.nodes.update({id: nodeId, color: color});
            }

            updateStatus(text, bgClass) {
                const statusElement = document.getElementById('statusGeral');
                statusElement.textContent = text;
                statusElement.className = `text-lg font-bold text-white px-3 py-1 rounded ${bgClass}`;
            }

            updateProgress(percent) {
                const progressBar = document.getElementById('progressBar');
                progressBar.style.width = `${percent}%`;
            }

            log(message) {
                const logContainer = document.getElementById('logContainer');
                const timestamp = new Date().toLocaleTimeString();
                const logEntry = document.createElement('div');
                logEntry.textContent = `[${timestamp}] ${message}`;
                logContainer.appendChild(logEntry);
                logContainer.scrollTop = logContainer.scrollHeight;
            }

            resetarTeste() {
                this.currentStep = 0;
                this.errors = [];
                this.proposicaoId = null;
                
                // Resetar status das etapas
                this.etapas.forEach(etapa => {
                    this.updateStepStatus(etapa, 'pending');
                });
                
                // Resetar rede
                for (let i = 1; i <= 8; i++) {
                    this.updateNetworkNode(i, '#94a3b8');
                }
                
                this.updateStatus("Aguardando Início", "bg-gray-500");
                this.updateProgress(0);
                
                const logContainer = document.getElementById('logContainer');
                logContainer.innerHTML = '<div class="text-gray-400">Sistema resetado e pronto para novo teste...</div>';
                
                document.getElementById('errorReport').classList.add('hidden');
                
                this.log("🔄 Sistema resetado com sucesso");
            }

            showErrorReport() {
                const errorReport = document.getElementById('errorReport');
                const errorList = document.getElementById('errorList');
                
                errorList.innerHTML = '';
                
                this.errors.forEach(error => {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-50 border-l-4 border-red-400 p-4';
                    errorDiv.innerHTML = `
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Erro na etapa: ${error.etapa}
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>${error.erro}</p>
                                </div>
                                <div class="mt-1 text-xs text-red-600">
                                    ${new Date(error.timestamp).toLocaleString()}
                                </div>
                            </div>
                        </div>
                    `;
                    errorList.appendChild(errorDiv);
                });
                
                errorReport.classList.remove('hidden');
            }

            gerarRelatorio() {
                const report = {
                    timestamp: new Date().toISOString(),
                    etapas_completadas: this.currentStep,
                    total_etapas: this.etapas.length,
                    sucesso: this.errors.length === 0,
                    erros: this.errors,
                    proposicao_id: this.proposicaoId
                };
                
                const dataStr = JSON.stringify(report, null, 2);
                const dataBlob = new Blob([dataStr], {type: 'application/json'});
                const url = URL.createObjectURL(dataBlob);
                
                const link = document.createElement('a');
                link.href = url;
                link.download = `relatorio-processo-${Date.now()}.json`;
                link.click();
                
                this.log("📊 Relatório gerado e baixado");
            }

            sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }
        }

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', () => {
            new ProcessoCompleto();
        });
    </script>
</body>
</html>