{{-- Editor de Template - Administrador --}}
<x-onlyoffice-editor
    :document-key="$config['document']['key']"
    :document-url="$config['document']['url']"
    :document-title="$config['document']['title']"
    :document-type="$config['documentType']"
    :file-type="$config['document']['fileType']"
    :callback-url="$config['editorConfig']['callbackUrl']"
    mode="edit"
    user-type="admin"
    :save-route="route('templates.salvar', $tipo)"
    :back-route="route('templates.index')"
    :show-variables-panel="true"
    :show-toolbar="true"
    :template-id="$template->id ?? null"
>
    {{-- Slot para scripts adicionais se necessário --}}
    @push('scripts')
    <script>
        // Customizações específicas para o admin
        console.log('Editor de template carregado para administrador');
    </script>
    @endpush
</x-onlyoffice-editor>