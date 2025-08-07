{{-- Editor de Template - Administrador --}}
<x-onlyoffice-editor
    :document-key="$config['document']['key'] ?? $template->document_key ?? 'template_' . $tipo->id . '_' . time()"
    :document-url="$config['document']['url'] ?? route('api.templates.download', $template->id)"
    :document-title="'Template: ' . $tipo->nome"
    document-type="rtf"
    :callback-url="$config['editorConfig']['callbackUrl'] ?? route('api.onlyoffice.callback', $template->document_key ?? 'test')"
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