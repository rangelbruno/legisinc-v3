{{-- Editor de Proposição - Legislativo --}}
@php
    $titulo = 'Proposição #' . $proposicao->id . ' - ' . $proposicao->tipo_formatado . ' - ' . $proposicao->autor->name;
@endphp

<x-onlyoffice-editor
    :document-key="$config['document']['key']"
    :document-url="$config['document']['url']"
    :document-title="$titulo"
    document-type="word"
    :callback-url="$config['editorConfig']['callbackUrl']"
    mode="edit"
    user-type="legislativo"
    :save-route="null"
    :back-route="route('proposicoes.show', $proposicao)"
    :show-variables-panel="false"
    :show-toolbar="true"
    :proposicao-id="$proposicao->id"
    :custom-actions="[
        [
            'label' => 'Converter para PDF',
            'onclick' => 'converterParaPDF()',
            'class' => 'btn-warning btn-sm',
            'icon' => 'ki-duotone ki-file-pdf fs-2'
        ],
        [
            'label' => 'Aprovar',
            'onclick' => 'aprovarProposicao()',
            'class' => 'btn-success btn-sm',
            'icon' => 'ki-duotone ki-check-circle fs-2'
        ]
    ]"
>
    {{-- Scripts adicionais específicos para o legislativo --}}
    @push('scripts')
    <script>
        function converterParaPDF() {
            Swal.fire({
                title: 'Converter para PDF?',
                text: 'Esta ação irá converter o documento atual para PDF.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Converter',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implementar conversão para PDF
                    onlyofficeEditor.showToast('Convertendo para PDF...', 'info', 3000);
                    
                    fetch("/api/proposicoes/{{ $proposicao->id }}/converter-pdf", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            onlyofficeEditor.showToast('PDF gerado com sucesso!', 'success', 3000);
                            // Opcionalmente, abrir o PDF em nova aba
                            if (data.pdf_url) {
                                window.open(data.pdf_url, '_blank');
                            }
                        } else {
                            onlyofficeEditor.showToast('Erro ao converter para PDF', 'error', 5000);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        onlyofficeEditor.showToast('Erro na conversão', 'error', 5000);
                    });
                }
            });
        }
        
        function aprovarProposicao() {
            Swal.fire({
                title: 'Aprovar Proposição?',
                text: 'Esta ação marcará a proposição como aprovada pelo Legislativo.',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Aprovar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Salvar documento antes de aprovar
                    onlyofficeEditor.forceSave();
                    
                    // Aguardar um pouco e então aprovar
                    setTimeout(() => {
                        fetch("/api/proposicoes/{{ $proposicao->id }}/aprovar", {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Aprovado!',
                                    text: 'A proposição foi aprovada com sucesso.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href = "{{ route('proposicoes.legislativo.index') }}";
                                });
                            } else {
                                Swal.fire('Erro', data.message || 'Erro ao aprovar proposição', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            Swal.fire('Erro', 'Erro ao processar aprovação', 'error');
                        });
                    }, 2000);
                }
            });
        }
        
        // Log para debug
        console.log('Editor OnlyOffice carregado para Legislativo');
        console.log('Proposição:', {
            id: {{ $proposicao->id }},
            tipo: '{{ $proposicao->tipo_formatado }}',
            autor: '{{ $proposicao->autor->name }}'
        });
    </script>
    @endpush
</x-onlyoffice-editor>