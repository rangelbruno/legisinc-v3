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
    {{-- Scripts adicionais específicos para o parlamentar --}}
    @push('scripts')
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
    @endpush
</x-onlyoffice-editor>