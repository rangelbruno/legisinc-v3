<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Editor Tiptap Mínimo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <h1>Editor Tiptap - Teste Mínimo</h1>
    
    <div>
        <label>Nome: <input type="text" id="modelo-nome" value="{{ optional($modelo)->nome }}"></label>
    </div>
    
    <div>
        <label>Tipo: 
            <select id="modelo-tipo">
                @foreach($tipos as $key => $tipo)
                    <option value="{{ $key }}" {{ $tipoSelecionado == $key ? 'selected' : '' }}>
                        {{ $tipo }}
                    </option>
                @endforeach
            </select>
        </label>
    </div>
    
    <div id="legal-editor" style="border: 1px solid #ccc; min-height: 300px; padding: 10px;"></div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página carregada');
            console.log('Tipos:', @json($tipos));
            console.log('Tipo selecionado:', @json($tipoSelecionado));
            console.log('Modelo:', {!! json_encode($modelo) !!});
            
            if (window.LegalEditor) {
                const container = document.getElementById('legal-editor');
                const editor = window.LegalEditor.init(container, {
                    content: '<p>Editor funcionando!</p>'
                });
                console.log('Editor inicializado:', editor);
            } else {
                console.error('LegalEditor não encontrado');
            }
        });
    </script>
</body>
</html>