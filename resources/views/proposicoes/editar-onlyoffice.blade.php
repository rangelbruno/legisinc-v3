{{-- Editor de Proposição - Parlamentar --}}
@php
    $documentUrl = 'http://host.docker.internal:8001/onlyoffice/file/proposicao/' . ($proposicao->id ?? 1) . '/' . $arquivoProposicao;
    $callbackUrl = 'http://host.docker.internal:8001/api/onlyoffice/callback/proposicao/' . ($proposicao->id ?? 1);
    $titulo = 'Proposição ' . ($proposicao->id ?? '') . ' - ' . ($template ? ($template->tipoProposicao->nome ?? $template->nome) : 'Template em Branco');
@endphp

<x-onlyoffice-editor
    :document-key="$documentKey"
    :document-url="$documentUrl"
    :document-title="$titulo"
    document-type="docx"
    :callback-url="$callbackUrl"
    mode="edit"
    user-type="parlamentar"
    :save-route="null"
    :back-route="route('proposicoes.minhas-proposicoes')"
    :show-variables-panel="false"
    :show-toolbar="true"
    :proposicao-id="$proposicao->id ?? null"
    :template-id="$template->id ?? null"
    :custom-actions="Auth::user()->isLegislativo() ? [
        [
            'label' => 'Voltar para Parlamentar',
            'onclick' => 'voltarParaParlamentar()',
            'class' => 'btn-success btn-sm',
            'icon' => 'ki-duotone ki-arrow-left fs-2'
        ]
    ] : []"
>
    {{-- Scripts adicionais específicos para parlamentar --}}
    @push('scripts')
    <script>
        @if(Auth::user()->isLegislativo())
        function voltarParaParlamentar() {
            Swal.fire({
                title: 'Voltar para Parlamentar?',
                text: 'Esta ação converterá o documento para PDF e o enviará de volta ao Parlamentar para assinatura.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirmar e Voltar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Processando...',
                        text: 'Convertendo documento e enviando para o Parlamentar...',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Fazer a requisição
                    fetch("{{ route('proposicoes.voltar-parlamentar', $proposicao->id ?? 1) }}", {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = data.redirect || "{{ route('proposicoes.legislativo.index') }}";
                            });
                        } else {
                            Swal.fire('Erro', data.message || 'Erro ao processar solicitação', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        Swal.fire('Erro', 'Erro de conectividade. Tente novamente.', 'error');
                    });
                }
            });
        }
        @endif
        
        // Log para debug
        console.log('Editor OnlyOffice carregado para Parlamentar');
        console.log('Proposição ID:', {{ $proposicao->id ?? 'null' }});
        console.log('Template ID:', {{ $template->id ?? 'null' }});
    </script>
    @endpush
</x-onlyoffice-editor>