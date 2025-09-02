{{-- Editor de Proposição - Parlamentar --}}
@php
    $titulo = 'Proposição #' . $proposicao->id . ' - ' . $proposicao->tipo_formatado . ' - ' . $proposicao->autor->name;
@endphp

<x-onlyoffice-editor
    :document-key="$config['document']['key']"
    :document-url="$config['document']['url']"
    :document-title="$titulo"
    :document-type="$config['documentType']"
    :file-type="$config['document']['fileType']"
    :callback-url="$config['editorConfig']['callbackUrl']"
    mode="edit"
    user-type="parlamentar"
    :save-route="null"
    :back-route="route('proposicoes.show', $proposicao)"
    :show-variables-panel="false"
    :show-toolbar="true"
    :proposicao-id="$proposicao->id"
    :custom-actions="[]"
>
    {{-- LARAVEL BOOST: Monitor de callbacks OnlyOffice Vue.js --}}
    <div id="onlyoffice-monitor-app" class="mb-4">
        <onlyoffice-monitor
            :proposicao-id="{{ $proposicao->id }}"
            :document-key="'{{ $config['document']['key'] }}'"
            @refresh-content="refreshProposicaoContent"
        ></onlyoffice-monitor>
    </div>

    {{-- Scripts adicionais específicos para o parlamentar --}}
    @push('scripts')
    <script>
        // OTIMIZAÇÃO: Auto-refresh com menos frequência e smart polling
        let lastModified = null;
        let refreshCheckInterval = null;
        let consecutiveErrors = 0;
        let pollInterval = 10000; // Começar com 10 segundos
        
        // Função otimizada para verificar atualizações
        function checkForUpdates() {
            // Usar AbortController para cancelar requisições antigas
            const controller = new AbortController();
            
            fetch('/proposicoes/{{ $proposicao->id }}/onlyoffice/status', {
                signal: controller.signal,
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            })
                .then(response => response.json())
                .then(data => {
                    consecutiveErrors = 0; // Reset error count
                    
                    if (lastModified && data.ultima_modificacao !== lastModified) {
                        console.log('🔄 Documento atualizado, notificando usuário...');
                        showUpdateNotification();
                        
                        // Parar verificação temporariamente
                        if (refreshCheckInterval) {
                            clearInterval(refreshCheckInterval);
                        }
                    } else {
                        // Aumentar intervalo se não há mudanças (performance)
                        pollInterval = Math.min(pollInterval * 1.1, 30000); // Máx 30s
                    }
                    lastModified = data.ultima_modificacao;
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        consecutiveErrors++;
                        console.warn('Erro ao verificar atualizações:', err);
                        
                        // Reduzir frequência se há muitos erros
                        if (consecutiveErrors > 3) {
                            pollInterval = Math.min(pollInterval * 2, 60000); // Máx 1 min
                        }
                    }
                });
        }
        
        // Mostrar notificação de documento atualizado
        function showUpdateNotification() {
            Swal.fire({
                title: '📄 Documento Atualizado',
                text: 'Suas alterações foram salvas! Recarregando documento...',
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                position: 'top-end',
                toast: true,
                showConfirmButton: false
            });
            
            // Marcar como salvo no status
            const statusElement = document.getElementById('statusTexto');
            const statusBadge = document.getElementById('statusSalvamento');
            if (statusElement && statusBadge) {
                statusElement.textContent = 'Salvo';
                statusBadge.className = 'badge badge-success px-3 py-2';
            }
            
            // Forçar reload da página para carregar nova versão do documento
            setTimeout(() => {
                console.log('🔄 Recarregando página para mostrar versão atualizada...');
                window.location.reload();
            }, 2500);
        }
        
        // Iniciar verificação otimizada de atualizações
        document.addEventListener('DOMContentLoaded', function() {
            // Aguardar OnlyOffice carregar
            setTimeout(() => {
                checkForUpdates(); // Verificação inicial
                
                // OTIMIZAÇÃO: Usar intervalo dinâmico baseado em atividade
                function startSmartPolling() {
                    if (refreshCheckInterval) clearInterval(refreshCheckInterval);
                    refreshCheckInterval = setInterval(checkForUpdates, pollInterval);
                }
                
                startSmartPolling();
                
                // Reduzir polling quando janela não está visível
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        pollInterval = 30000; // 30s quando não visível
                    } else {
                        pollInterval = 10000; // 10s quando visível
                    }
                    startSmartPolling();
                });
            }, 2000);
        });
        
    </script>
    <script>
        // Função para enviar para o legislativo
        function enviarParaLegislativo() {
            Swal.fire({
                title: 'Enviar para Legislativo?',
                text: 'Após enviar, a proposição será analisada pelo setor legislativo.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Forçar salvamento antes de enviar
                    onlyofficeEditor.forceSave();
                    
                    // Aguardar um pouco para garantir que salvou
                    setTimeout(() => {
                        fetch("/proposicoes/{{ $proposicao->id }}/enviar-legislativo", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Enviado!',
                                    text: 'A proposição foi enviada para o legislativo.',
                                    icon: 'success'
                                }).then(() => {
                                    window.location.href = "{{ route('proposicoes.show', $proposicao) }}";
                                });
                            } else {
                                Swal.fire('Erro', data.message || 'Erro ao enviar proposição', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            Swal.fire('Erro', 'Erro ao enviar proposição', 'error');
                        });
                    }, 1000);
                }
            });
        }
        
        // Função para salvar como rascunho
        function salvarRascunho() {
            onlyofficeEditor.forceSave();
            onlyofficeEditor.showToast('Rascunho salvo com sucesso!', 'success', 3000);
        }
        
        // Adicionar listener para salvar periodicamente
        setInterval(() => {
            if (window.docEditor && window.docEditor.isModified) {
                onlyofficeEditor.forceSave();
            }
        }, 60000); // Auto-save a cada minuto
    </script>

    {{-- LARAVEL BOOST: Vue.js app para monitor OnlyOffice --}}
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            // Aguardar Vue.js carregar
            if (typeof window.Vue !== 'undefined' && window.OnlyOfficeMonitorComponent) {
                const { createApp } = window.Vue;
                
                const app = createApp({
                    components: {
                        'onlyoffice-monitor': window.OnlyOfficeMonitorComponent
                    },
                    methods: {
                        refreshProposicaoContent() {
                            // Método chamado quando o monitor solicita atualização
                            console.log('🔄 Atualizando conteúdo da proposição via Vue.js monitor...');
                            if (window.docEditor) {
                                window.docEditor.downloadAs();
                            }
                            // Forçar reload se necessário
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    }
                });
                
                app.mount('#onlyoffice-monitor-app');
                console.log('✅ Vue.js OnlyOffice Monitor inicializado');
            } else {
                console.warn('⚠️ Vue.js ou OnlyOfficeMonitor não encontrado');
            }
        });
    </script>
    @endpush
</x-onlyoffice-editor>