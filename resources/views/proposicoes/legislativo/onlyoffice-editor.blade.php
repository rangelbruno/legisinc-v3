{{-- Editor de Proposição - Legislativo --}}
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
    user-type="legislativo"
    :save-route="null"
    :back-route="route('proposicoes.show', $proposicao)"
    :show-variables-panel="false"
    :show-toolbar="true"
    :proposicao-id="$proposicao->id"
    :custom-actions="[]"
>
    {{-- Scripts adicionais específicos para o legislativo --}}
    @push('scripts')
    <script>
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